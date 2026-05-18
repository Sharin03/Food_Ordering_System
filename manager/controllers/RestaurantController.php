<?php
class RestaurantController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function profile() {
        $error = $success = '';
        $restaurant_id = $_SESSION['restaurant_id'];
        $stmt = $this->conn->prepare("SELECT * FROM restaurants WHERE id=?");
        $stmt->bind_param('i', $restaurant_id); $stmt->execute();
        $restaurant = $stmt->get_result()->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = trim($_POST['name'] ?? '');
            $desc    = trim($_POST['description'] ?? '');
            $cuisine = trim($_POST['cuisine_type'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $city    = trim($_POST['city'] ?? '');
            $hours   = trim($_POST['opening_hours'] ?? '');
            $is_open = isset($_POST['is_open']) ? 1 : 0;
            $logo    = $restaurant['logo_path'];
            if (!empty($_FILES['logo']['name'])) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                    $dir = __DIR__ . '/../../uploads/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $fn = 'logo_' . $restaurant_id . '_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['logo']['tmp_name'], $dir . $fn);
                    $logo = 'uploads/' . $fn;
                }
            }
            $s = $this->conn->prepare(
                "UPDATE restaurants SET name=?,description=?,cuisine_type=?,address=?,city=?,opening_hours=?,logo_path=?,is_open=? WHERE id=?"
            );
            $s->bind_param('sssssssii', $name,$desc,$cuisine,$address,$city,$hours,$logo,$is_open,$restaurant_id);
            $s->execute();
            $success = 'Profile updated.';
            $restaurant = array_merge($restaurant, compact('name','desc','cuisine','address','city','hours','is_open'));
            $restaurant['logo_path'] = $logo;
        }
        include __DIR__ . '/../views/profile.php';
    }
}
