<?php
class CustomerModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getById($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateProfile($user_id, $name, $phone, $pic = null) {
        if ($pic) {
            $stmt = $this->conn->prepare(
                "UPDATE users SET name=?, phone=?, profile_pic=? WHERE id=?"
            );
            $stmt->bind_param("sssi", $name, $phone, $pic, $user_id);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE users SET name=?, phone=? WHERE id=?"
            );
            $stmt->bind_param("ssi", $name, $phone, $user_id);
        }
        $stmt->execute();
    }

    public function changePassword($user_id, $new_password) {
        $hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $stmt->bind_param("si", $hash, $user_id);
        $stmt->execute();
    }
}