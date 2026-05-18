<?php
class AgentModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getProfile($agent_id) {
        $stmt = $this->conn->prepare(
            "SELECT u.name, u.email, u.phone, u.profile_pic,
                    da.vehicle_type, da.is_online, da.total_earnings
             FROM delivery_agents da
             JOIN users u ON u.id = da.user_id
             WHERE da.id = ?"
        );
        $stmt->bind_param("i", $agent_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateProfile($agent_id, $user_id, $name, $phone, $vehicle, $pic = null) {
        $stmt = $this->conn->prepare("UPDATE users SET name=?, phone=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $phone, $user_id);
        $stmt->execute();

        $stmt2 = $this->conn->prepare("UPDATE delivery_agents SET vehicle_type=? WHERE id=?");
        $stmt2->bind_param("si", $vehicle, $agent_id);
        $stmt2->execute();

        if ($pic) {
            $stmt3 = $this->conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
            $stmt3->bind_param("si", $pic, $user_id);
            $stmt3->execute();
        }
    }

    public function getStats($agent_id) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total_deliveries,
                    AVG(TIMESTAMPDIFF(MINUTE, assigned_at, delivered_at)) AS avg_time
             FROM delivery_assignments WHERE agent_id=? AND status='delivered'"
        );
        $stmt->bind_param("i", $agent_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $row['avg_time'] = round($row['avg_time'] ?? 0, 1);
        return $row;
    }
}