<?php
class ReviewModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function submit($order_id, $customer_id, $restaurant_id, $rating, $comment) {
        
        $stmt = $this->conn->prepare(
            "SELECT id FROM reviews WHERE order_id=? AND customer_id=?"
        );
        $stmt->bind_param("ii", $order_id, $customer_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return false;

        $stmt2 = $this->conn->prepare(
            "INSERT INTO reviews (order_id,customer_id,restaurant_id,rating,comment)
             VALUES (?,?,?,?,?)"
        );
        $stmt2->bind_param("iiiis", $order_id, $customer_id, $restaurant_id, $rating, $comment);
        return $stmt2->execute();
    }

    public function getByCustomer($customer_id) {
        $stmt = $this->conn->prepare(
            "SELECT rv.*, r.name AS restaurant_name FROM reviews rv
             JOIN restaurants r ON r.id=rv.restaurant_id
             WHERE rv.customer_id=? ORDER BY rv.created_at DESC"
        );
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByRestaurant($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT rv.*, u.name AS customer_name FROM reviews rv
             JOIN users u ON u.id=rv.customer_id
             WHERE rv.restaurant_id=? ORDER BY rv.created_at DESC LIMIT 10"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}