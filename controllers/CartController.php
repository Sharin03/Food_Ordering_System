<?php
class CartController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    private function fetchItem($item_id) {
        $stmt = $this->conn->prepare(
            "SELECT mi.*,mc.restaurant_id,
                    CASE WHEN d.is_active=1 AND CURDATE() BETWEEN d.valid_from AND d.valid_until
                         THEN ROUND(mi.price*(1-d.discount_pct/100),2) ELSE mi.price END AS final_price
             FROM menu_items mi
             JOIN menu_categories mc ON mc.id=mi.category_id
             LEFT JOIN discounts d ON d.menu_item_id=mi.id
             WHERE mi.id=?"
        );
        $stmt->bind_param('i', $item_id); $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        $items = []; $total = 0;
        foreach ($cart as $item_id => $qty) {
            $item = $this->fetchItem($item_id);
            if ($item) {
                $item['quantity'] = $qty;
                $item['subtotal'] = $item['final_price'] * $qty;
                $total += $item['subtotal'];
                $items[] = $item;
            }
        }
        include __DIR__ . '/../views/cart.php';
    }

    public function add() {
        $item_id = (int)($_POST['item_id'] ?? 0);
        $qty     = max(1, (int)($_POST['quantity'] ?? 1));
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (!empty($_SESSION['cart'])) {
            $new_item = $this->fetchItem($item_id);
            $new_rest = $new_item['restaurant_id'] ?? 0;
            $old_item = $this->fetchItem(array_key_first($_SESSION['cart']));
            $old_rest = $old_item['restaurant_id'] ?? 0;
            if ($new_rest !== $old_rest) $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][$item_id] = ($_SESSION['cart'][$item_id] ?? 0) + $qty;
        header('Location: index.php?page=cart'); exit;
    }

    public function remove() {
        $item_id = (int)($_POST['item_id'] ?? 0);
        unset($_SESSION['cart'][$item_id]);
        header('Location: index.php?page=cart'); exit;
    }
}
