<?php
class OrderController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function dashboard() {
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare(
            "SELECT o.*,u.name AS customer_name FROM orders o
             JOIN users u ON u.id=o.customer_id
             WHERE o.restaurant_id=? AND o.status NOT IN ('delivered','cancelled')
             ORDER BY o.created_at DESC"
        );
        $stmt->bind_param('i', $restaurant_id); $stmt->execute();
        $active_orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt2 = $this->conn->prepare(
            "SELECT COUNT(*) AS total, COALESCE(SUM(total_amount),0) AS revenue
             FROM orders WHERE restaurant_id=? AND status='delivered' AND DATE(created_at)=CURDATE()"
        );
        $stmt2->bind_param('i', $restaurant_id); $stmt2->execute();
        $today = $stmt2->get_result()->fetch_assoc();
        include __DIR__ . '/../views/dashboard.php';
    }

    public function active() {
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare(
            "SELECT o.*,u.name AS customer_name,
                    GROUP_CONCAT(CONCAT(mi.name,' x',oi.quantity) SEPARATOR ', ') AS items
             FROM orders o
             JOIN users u ON u.id=o.customer_id
             JOIN order_items oi ON oi.order_id=o.id
             JOIN menu_items mi ON mi.id=oi.menu_item_id
             WHERE o.restaurant_id=? AND o.status NOT IN ('delivered','cancelled')
             GROUP BY o.id ORDER BY o.created_at DESC"
        );
        $stmt->bind_param('i', $restaurant_id); $stmt->execute();
        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/orders.php';
    }

    public function accept() {
        $order_id      = (int)($_POST['order_id'] ?? 0);
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare("UPDATE orders SET status='accepted' WHERE id=? AND restaurant_id=? AND status='pending'");
        $stmt->bind_param('ii', $order_id, $restaurant_id); $stmt->execute();
        header('Location: index.php?page=orders'); exit;
    }

    public function reject() {
        $order_id      = (int)($_POST['order_id'] ?? 0);
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare("UPDATE orders SET status='cancelled' WHERE id=? AND restaurant_id=?");
        $stmt->bind_param('ii', $order_id, $restaurant_id); $stmt->execute();
        header('Location: index.php?page=orders'); exit;
    }

    public function updateStatus() {
        $order_id      = (int)($_POST['order_id'] ?? 0);
        $new_status    = $_POST['status'] ?? '';
        $restaurant_id = $_SESSION['restaurant_id'];
        if (!in_array($new_status, ['preparing','ready'])) { header('Location: index.php?page=orders'); exit; }
        $stmt = $this->conn->prepare("UPDATE orders SET status=? WHERE id=? AND restaurant_id=?");
        $stmt->bind_param('sii', $new_status, $order_id, $restaurant_id); $stmt->execute();
        header('Location: index.php?page=orders'); exit;
    }

    public function history() {
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare(
            "SELECT o.*,u.name AS customer_name,
                    GROUP_CONCAT(CONCAT(mi.name,' x',oi.quantity) SEPARATOR ', ') AS items
             FROM orders o
             JOIN users u ON u.id=o.customer_id
             JOIN order_items oi ON oi.order_id=o.id
             JOIN menu_items mi ON mi.id=oi.menu_item_id
             WHERE o.restaurant_id=? GROUP BY o.id ORDER BY o.created_at DESC"
        );
        $stmt->bind_param('i', $restaurant_id); $stmt->execute();
        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/order_history.php';
    }

    public function reviews() {
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare(
            "SELECT rv.*,u.name AS customer_name FROM reviews rv
             JOIN users u ON u.id=rv.customer_id WHERE rv.restaurant_id=? ORDER BY rv.created_at DESC"
        );
        $stmt->bind_param('i', $restaurant_id); $stmt->execute();
        $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/reviews.php';
    }

    public function replyReview() {
        $review_id     = (int)($_POST['review_id'] ?? 0);
        $reply         = trim($_POST['reply'] ?? '');
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare("UPDATE reviews SET manager_reply=? WHERE id=? AND restaurant_id=?");
        $stmt->bind_param('sii', $reply, $review_id, $restaurant_id); $stmt->execute();
        header('Location: index.php?page=reviews'); exit;
    }
}
