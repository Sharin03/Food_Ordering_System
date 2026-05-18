<?php
class OrderController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    private function resolveCart() {
        $cart = $_SESSION['cart'] ?? [];
        $items = []; $subtotal = 0; $restaurant_id = 0;
        foreach ($cart as $item_id => $qty) {
            $stmt = $this->conn->prepare(
                "SELECT mi.*,mc.restaurant_id,
                        CASE WHEN d.is_active=1 AND CURDATE() BETWEEN d.valid_from AND d.valid_until
                             THEN ROUND(mi.price*(1-d.discount_pct/100),2) ELSE mi.price END AS final_price
                 FROM menu_items mi
                 JOIN menu_categories mc ON mc.id=mi.category_id
                 LEFT JOIN discounts d ON d.menu_item_id=mi.id WHERE mi.id=?"
            );
            $stmt->bind_param('i', $item_id); $stmt->execute();
            $item = $stmt->get_result()->fetch_assoc();
            if ($item) {
                $item['quantity'] = $qty;
                $item['subtotal'] = $item['final_price'] * $qty;
                $subtotal        += $item['subtotal'];
                $restaurant_id    = $item['restaurant_id'];
                $items[]          = $item;
            }
        }
        return [$items, $subtotal, $restaurant_id];
    }

    public function checkout() {
        if (empty($_SESSION['cart'])) { header('Location: index.php?page=cart'); exit; }
        $user_id = $_SESSION['user_id'];
        $stmt = $this->conn->prepare(
            "SELECT * FROM delivery_addresses WHERE customer_id=? ORDER BY is_default DESC"
        );
        $stmt->bind_param('i', $user_id); $stmt->execute();
        $addresses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        [$items, $subtotal, $restaurant_id] = $this->resolveCart();
        $delivery_fee = 30; $total = $subtotal + $delivery_fee;
        include __DIR__ . '/../views/checkout.php';
    }

    public function confirm() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?page=checkout'); exit; }
        $cart             = $_SESSION['cart'] ?? [];
        $user_id          = $_SESSION['user_id'];
        $delivery_address = trim($_POST['delivery_address'] ?? '');
        $payment_method   = in_array($_POST['payment_method'] ?? '', ['cash','card']) ? $_POST['payment_method'] : 'cash';
        if (empty($cart) || !$delivery_address) { header('Location: index.php?page=checkout'); exit; }

        [$items_raw, $subtotal, $restaurant_id] = $this->resolveCart();
        $delivery_fee = 30; $total = $subtotal + $delivery_fee;

        $stmt2 = $this->conn->prepare(
            "INSERT INTO orders (customer_id,restaurant_id,delivery_address,payment_method,subtotal,delivery_fee,total_amount,status,estimated_delivery_minutes)
             VALUES (?,?,?,?,?,?,?,'pending',45)"
        );
        $stmt2->bind_param('iissddd', $user_id, $restaurant_id, $delivery_address, $payment_method, $subtotal, $delivery_fee, $total);
        $stmt2->execute();
        $order_id = $this->conn->insert_id;

        foreach ($items_raw as $i) {
            $stmt3 = $this->conn->prepare(
                "INSERT INTO order_items (order_id,menu_item_id,quantity,unit_price) VALUES (?,?,?,?)"
            );
            $stmt3->bind_param('iiid', $order_id, $i['id'], $i['quantity'], $i['final_price']);
            $stmt3->execute();
        }
        $_SESSION['cart'] = [];
        $order = ['id' => $order_id, 'total_amount' => $total, 'estimated_delivery_minutes' => 45];
        include __DIR__ . '/../views/order_confirm.php';
    }

    public function history() {
        $user_id = $_SESSION['user_id'];
        $stmt = $this->conn->prepare(
            "SELECT o.*,r.name AS restaurant_name FROM orders o
             JOIN restaurants r ON r.id=o.restaurant_id
             WHERE o.customer_id=? ORDER BY o.created_at DESC"
        );
        $stmt->bind_param('i', $user_id); $stmt->execute();
        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/order_history.php';
    }

    public function track() {
        $order_id = (int)($_GET['id'] ?? 0);
        $user_id  = $_SESSION['user_id'];
        $stmt = $this->conn->prepare(
            "SELECT o.*,r.name AS restaurant_name FROM orders o
             JOIN restaurants r ON r.id=o.restaurant_id WHERE o.id=? AND o.customer_id=?"
        );
        $stmt->bind_param('ii', $order_id, $user_id); $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        if (!$order) { header('Location: index.php?page=order_history'); exit; }
        include __DIR__ . '/../views/track_order.php';
    }

    public function cancel() {
        $order_id = (int)($_POST['order_id'] ?? 0);
        $user_id  = $_SESSION['user_id'];
        $stmt = $this->conn->prepare(
            "UPDATE orders SET status='cancelled' WHERE id=? AND customer_id=? AND status='pending'"
        );
        $stmt->bind_param('ii', $order_id, $user_id); $stmt->execute();
        header('Location: index.php?page=order_history'); exit;
    }

    public function reorder() {
        $order_id = (int)($_POST['order_id'] ?? 0);
        $user_id  = $_SESSION['user_id'];
        $stmt = $this->conn->prepare(
            "SELECT oi.menu_item_id,oi.quantity FROM order_items oi
             JOIN orders o ON o.id=oi.order_id WHERE oi.order_id=? AND o.customer_id=?"
        );
        $stmt->bind_param('ii', $order_id, $user_id); $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $_SESSION['cart'] = [];
        foreach ($items as $i) $_SESSION['cart'][$i['menu_item_id']] = $i['quantity'];
        header('Location: index.php?page=cart'); exit;
    }

    public function reviews() {
        $user_id = $_SESSION['user_id'];
        $stmt = $this->conn->prepare(
            "SELECT rv.*,r.name AS restaurant_name FROM reviews rv
             JOIN restaurants r ON r.id=rv.restaurant_id
             WHERE rv.customer_id=? ORDER BY rv.created_at DESC"
        );
        $stmt->bind_param('i', $user_id); $stmt->execute();
        $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/reviews.php';
    }

    public function submitReview() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?page=order_history'); exit; }
        $user_id       = $_SESSION['user_id'];
        $order_id      = (int)($_POST['order_id'] ?? 0);
        $restaurant_id = (int)($_POST['restaurant_id'] ?? 0);
        $rating        = (int)($_POST['rating'] ?? 0);
        $comment       = trim($_POST['comment'] ?? '');
        if ($rating < 1 || $rating > 5 || !$comment) { header('Location: index.php?page=order_history'); exit; }

        // Prevent duplicate review
        $chk = $this->conn->prepare("SELECT id FROM reviews WHERE order_id=? AND customer_id=?");
        $chk->bind_param('ii', $order_id, $user_id); $chk->execute();
        if ($chk->get_result()->num_rows === 0) {
            $stmt = $this->conn->prepare(
                "INSERT INTO reviews (order_id,customer_id,restaurant_id,rating,comment) VALUES (?,?,?,?,?)"
            );
            $stmt->bind_param('iiiis', $order_id, $user_id, $restaurant_id, $rating, $comment);
            $stmt->execute();
        }
        header('Location: index.php?page=reviews'); exit;
    }
}
