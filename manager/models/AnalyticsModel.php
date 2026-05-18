<?php
class AnalyticsModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getRevenue($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT
               SUM(CASE WHEN DATE(created_at)=CURDATE()
                   THEN total_amount ELSE 0 END) AS today,
               SUM(CASE WHEN YEARWEEK(created_at,1)=YEARWEEK(NOW(),1)
                   THEN total_amount ELSE 0 END) AS this_week,
               SUM(CASE WHEN MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())
                   THEN total_amount ELSE 0 END) AS this_month,
               COUNT(*) AS total_orders
             FROM orders WHERE restaurant_id=? AND status='delivered'"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getTopItems($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT mi.name, SUM(oi.quantity) AS total_qty
             FROM order_items oi
             JOIN menu_items mi ON mi.id=oi.menu_item_id
             JOIN orders o ON o.id=oi.order_id
             WHERE o.restaurant_id=? AND o.status='delivered'
             GROUP BY oi.menu_item_id ORDER BY total_qty DESC LIMIT 5"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getComplaints($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT DISTINCT c.*, u.name AS submitter_name
             FROM complaints c
             JOIN users u ON u.id=c.submitter_id
             JOIN orders o ON o.customer_id=c.submitter_id
             WHERE o.restaurant_id=?
             ORDER BY c.created_at DESC"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}