<?php
class OrderModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getActive($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.*, u.name AS customer_name,
                    GROUP_CONCAT(CONCAT(mi.name,' x',oi.quantity) SEPARATOR ', ') AS items
             FROM orders o
             JOIN users u ON u.id=o.customer_id
             JOIN order_items oi ON oi.order_id=o.id
             JOIN menu_items mi ON mi.id=oi.menu_item_id
             WHERE o.restaurant_id=? AND o.status NOT IN ('delivered','cancelled')
             GROUP BY o.id ORDER BY o.created_at DESC"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getHistory($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.*, u.name AS customer_name,
                    GROUP_CONCAT(CONCAT(mi.name,' x',oi.quantity) SEPARATOR ', ') AS items
             FROM orders o
             JOIN users u ON u.id=o.customer_id
             JOIN order_items oi ON oi.order_id=o.id
             JOIN menu_items mi ON mi.id=oi.menu_item_id
             WHERE o.restaurant_id=?
             GROUP BY o.id ORDER BY o.created_at DESC"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus($order_id, $restaurant_id, $status) {
        $stmt = $this->conn->prepare(
            "UPDATE orders SET status=? WHERE id=? AND restaurant_id=?"
        );
        $stmt->bind_param("sii", $status, $order_id, $restaurant_id);
        $stmt->execute();
    }

    public function getPendingCount($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS cnt FROM orders WHERE restaurant_id=? AND status='pending'"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['cnt'];
    }

    public function getTodayStats($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total, COALESCE(SUM(total_amount),0) AS revenue
             FROM orders WHERE restaurant_id=? AND status='delivered'
             AND DATE(created_at)=CURDATE()"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}