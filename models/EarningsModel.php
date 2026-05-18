<?php
class EarningsModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getSummary($agent_id) {
        $stmt = $this->conn->prepare(
            "SELECT
               SUM(CASE WHEN DATE(da.delivered_at) = CURDATE()
                   THEN o.delivery_fee ELSE 0 END) AS today,
               SUM(CASE WHEN YEARWEEK(da.delivered_at,1) = YEARWEEK(NOW(),1)
                   THEN o.delivery_fee ELSE 0 END) AS this_week,
               SUM(CASE WHEN MONTH(da.delivered_at)=MONTH(NOW()) AND YEAR(da.delivered_at)=YEAR(NOW())
                   THEN o.delivery_fee ELSE 0 END) AS this_month,
               SUM(o.delivery_fee) AS all_time
             FROM delivery_assignments da
             JOIN orders o ON o.id = da.order_id
             WHERE da.agent_id = ? AND da.status = 'delivered'"
        );
        $stmt->bind_param("i", $agent_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getEarningsHistory($agent_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.id, o.delivery_fee, da.delivered_at, r.name AS restaurant_name
             FROM delivery_assignments da
             JOIN orders o ON o.id = da.order_id
             JOIN restaurants r ON r.id = o.restaurant_id
             WHERE da.agent_id = ? AND da.status = 'delivered'
             ORDER BY da.delivered_at DESC
             LIMIT 50"
        );
        $stmt->bind_param("i", $agent_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}