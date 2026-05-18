<?php
class AnalyticsController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function index() {
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare(
            "SELECT
               COALESCE(SUM(CASE WHEN DATE(created_at)=CURDATE() THEN total_amount ELSE 0 END),0) AS today,
               COALESCE(SUM(CASE WHEN YEARWEEK(created_at,1)=YEARWEEK(NOW(),1) THEN total_amount ELSE 0 END),0) AS this_week,
               COALESCE(SUM(CASE WHEN MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) THEN total_amount ELSE 0 END),0) AS this_month,
               COUNT(*) AS total_orders
             FROM orders WHERE restaurant_id=? AND status='delivered'"
        );
        $stmt->bind_param('i', $restaurant_id); $stmt->execute();
        $revenue = $stmt->get_result()->fetch_assoc();

        $stmt2 = $this->conn->prepare(
            "SELECT mi.name, SUM(oi.quantity) AS total_qty
             FROM order_items oi
             JOIN menu_items mi ON mi.id=oi.menu_item_id
             JOIN orders o ON o.id=oi.order_id
             WHERE o.restaurant_id=? AND o.status='delivered'
             GROUP BY oi.menu_item_id ORDER BY total_qty DESC LIMIT 5"
        );
        $stmt2->bind_param('i', $restaurant_id); $stmt2->execute();
        $top_items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/analytics.php';
    }

    public function complaints() {
        $restaurant_id = $_SESSION['restaurant_id'];
        // Fixed: subquery instead of problematic GROUP BY JOIN
        $stmt = $this->conn->prepare(
            "SELECT c.*,u.name AS submitter_name FROM complaints c
             JOIN users u ON u.id=c.submitter_id
             WHERE c.submitter_id IN (
                 SELECT DISTINCT customer_id FROM orders WHERE restaurant_id=?
             )
             ORDER BY c.created_at DESC"
        );
        $stmt->bind_param('i', $restaurant_id); $stmt->execute();
        $complaints = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/complaints.php';
    }
}
