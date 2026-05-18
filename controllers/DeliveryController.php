<?php
require_once __DIR__ . '/../models/AgentModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

class DeliveryController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function dashboard() {
        $model    = new AgentModel($this->conn);
        $omodel   = new OrderModel($this->conn);
        $agent_id = $_SESSION['agent_id'];
        $profile  = $model->getProfile($agent_id);
        $active   = $omodel->getActiveOrder($agent_id);
        $stats    = $model->getStats($agent_id);
        include __DIR__ . '/../views/dashboard.php';
    }

    public function profile() {
        $model    = new AgentModel($this->conn);
        $agent_id = $_SESSION['agent_id'];
        $error = $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = trim($_POST['name'] ?? '');
            $phone   = trim($_POST['phone'] ?? '');
            $vehicle = trim($_POST['vehicle_type'] ?? '');
            if (!$name||!$phone||!$vehicle) {
                $error = 'All fields are required.';
            } else {
                $pic = null;
                if (!empty($_FILES['profile_pic']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                        $dir = __DIR__ . '/../../uploads/';
                        if (!is_dir($dir)) mkdir($dir, 0755, true);
                        $fn = 'agent_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dir . $fn);
                        $pic = 'uploads/' . $fn;
                    } else {
                        $error = 'Only JPG, PNG, GIF allowed.';
                    }
                }
                if (!$error) {
                    $model->updateProfile($agent_id, $_SESSION['user_id'], $name, $phone, $vehicle, $pic);
                    $_SESSION['name'] = $name;
                    $success = 'Profile updated successfully.';
                }
            }
        }
        $profile = $model->getProfile($agent_id);
        include __DIR__ . '/../views/profile.php';
    }

    public function available() {
        $model    = new OrderModel($this->conn);
        $agent_id = $_SESSION['agent_id'];
        $stmt = $this->conn->prepare("SELECT is_online FROM delivery_agents WHERE id=?");
        $stmt->bind_param('i', $agent_id); $stmt->execute();
        $is_online = $stmt->get_result()->fetch_assoc()['is_online'] ?? 0;
        $orders = $model->getAvailableOrders();
        include __DIR__ . '/../views/available.php';
    }

    public function accept() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?page=available'); exit; }
        $order_id = (int)($_POST['order_id'] ?? 0);
        $agent_id = (int)$_SESSION['agent_id'];
        $model    = new OrderModel($this->conn);
        $model->acceptOrder($order_id, $agent_id);
        header('Location: index.php?page=active'); exit;
    }

    public function decline() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?page=available'); exit; }
        $order_id = (int)($_POST['order_id'] ?? 0);
        $model    = new OrderModel($this->conn);
        $model->declineOrder($order_id);
        header('Location: index.php?page=available'); exit;
    }

    public function active() {
        $model    = new OrderModel($this->conn);
        $agent_id = $_SESSION['agent_id'];
        $active   = $model->getActiveOrder($agent_id);
        include __DIR__ . '/../views/active.php';
    }

    public function history() {
        $model    = new OrderModel($this->conn);
        $agent_id = $_SESSION['agent_id'];
        $history  = $model->getDeliveryHistory($agent_id);
        include __DIR__ . '/../views/history.php';
    }
}
