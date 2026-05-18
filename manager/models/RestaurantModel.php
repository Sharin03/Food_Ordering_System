<?php
class RestaurantModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getById($restaurant_id) {
        $stmt = $this->conn->prepare("SELECT * FROM restaurants WHERE id=?");
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($restaurant_id, $name, $desc, $cuisine,
                           $address, $city, $hours, $logo, $is_open) {
        $stmt = $this->conn->prepare(
            "UPDATE restaurants SET name=?,description=?,cuisine_type=?,
             address=?,city=?,opening_hours=?,logo_path=?,is_open=? WHERE id=?"
        );
        $stmt->bind_param("sssssssii",
            $name,$desc,$cuisine,$address,$city,$hours,$logo,$is_open,$restaurant_id);
        $stmt->execute();
    }
}