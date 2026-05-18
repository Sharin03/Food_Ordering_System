<?php
class ProfileController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function index() {
        $error = $success = '';
        $user_id = $_SESSION['user_id'];
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bind_param('i', $user_id); $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name  = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $pass  = $_POST['new_password'] ?? '';
            if (!$name || !$phone) {
                $error = 'Name and phone are required.';
            } else {
                $pic = $user['profile_pic'];
                if (!empty($_FILES['profile_pic']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                        $dir = __DIR__ . '/../../uploads/';
                        if (!is_dir($dir)) mkdir($dir, 0755, true);
                        $filename = 'customer_' . $user_id . '_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dir . $filename);
                        $pic = 'uploads/' . $filename;
                    }
                }
                if ($pass && strlen($pass) >= 6) {
                    $hash = password_hash($pass, PASSWORD_BCRYPT);
                    $s = $this->conn->prepare("UPDATE users SET name=?,phone=?,profile_pic=?,password_hash=? WHERE id=?");
                    $s->bind_param('ssssi', $name, $phone, $pic, $hash, $user_id);
                } else {
                    $s = $this->conn->prepare("UPDATE users SET name=?,phone=?,profile_pic=? WHERE id=?");
                    $s->bind_param('sssi', $name, $phone, $pic, $user_id);
                }
                $s->execute();
                $_SESSION['name'] = $name;
                $success = 'Profile updated.';
                $user = array_merge($user, ['name'=>$name,'phone'=>$phone,'profile_pic'=>$pic]);
            }
        }
        include __DIR__ . '/../views/profile.php';
    }

    public function addresses() {
        $error = $success = '';
        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'add') {
                $label   = trim($_POST['label'] ?? '');
                $address = trim($_POST['address_line'] ?? '');
                $city    = trim($_POST['city'] ?? '');
                if (!$label || !$address || !$city) {
                    $error = 'All fields are required.';
                } else {
                    $stmt = $this->conn->prepare(
                        "INSERT INTO delivery_addresses (customer_id,label,address_line,city) VALUES (?,?,?,?)"
                    );
                    $stmt->bind_param('isss', $user_id, $label, $address, $city);
                    $stmt->execute();
                    $success = 'Address added.';
                }
            } elseif ($action === 'delete') {
                $addr_id = (int)$_POST['addr_id'];
                $stmt = $this->conn->prepare("DELETE FROM delivery_addresses WHERE id=? AND customer_id=?");
                $stmt->bind_param('ii', $addr_id, $user_id); $stmt->execute();
                $success = 'Address deleted.';
            } elseif ($action === 'set_default') {
                // BUG FIX: Original code had two prepare() calls without executing the first one.
                // Now properly: clear all defaults first, then set chosen one.
                $addr_id = (int)$_POST['addr_id'];
                $clear = $this->conn->prepare("UPDATE delivery_addresses SET is_default=0 WHERE customer_id=?");
                $clear->bind_param('i', $user_id);
                $clear->execute();
                $set = $this->conn->prepare("UPDATE delivery_addresses SET is_default=1 WHERE id=? AND customer_id=?");
                $set->bind_param('ii', $addr_id, $user_id);
                $set->execute();
                $success = 'Default address updated.';
            }
        }
        $stmt = $this->conn->prepare("SELECT * FROM delivery_addresses WHERE customer_id=? ORDER BY is_default DESC");
        $stmt->bind_param('i', $user_id); $stmt->execute();
        $addresses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/addresses.php';
    }

    public function complaints() {
        $error = $success = '';
        $user_id = $_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject = trim($_POST['subject'] ?? '');
            $desc    = trim($_POST['description'] ?? '');
            if (!$subject || !$desc) {
                $error = 'All fields are required.';
            } else {
                $stmt = $this->conn->prepare("INSERT INTO complaints (submitter_id,subject,description) VALUES (?,?,?)");
                $stmt->bind_param('iss', $user_id, $subject, $desc); $stmt->execute();
                $success = 'Complaint submitted successfully.';
            }
        }
        $stmt = $this->conn->prepare("SELECT * FROM complaints WHERE submitter_id=? ORDER BY created_at DESC");
        $stmt->bind_param('i', $user_id); $stmt->execute();
        $complaints = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/complaints.php';
    }
}
