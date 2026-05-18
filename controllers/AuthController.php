<?php
class AuthController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function register() {
        $error = $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = trim($_POST['name'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $phone   = trim($_POST['phone'] ?? '');
            $pass    = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (!$name || !$email || !$phone || !$pass)
                $error = 'All fields are required.';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
                $error = 'Invalid email address.';
            elseif (strlen($pass) < 6)
                $error = 'Password must be at least 6 characters.';
            elseif ($pass !== $confirm)
                $error = 'Passwords do not match.';
            else {
                $hash = password_hash($pass, PASSWORD_BCRYPT);
                $stmt = $this->conn->prepare(
                    "INSERT INTO users (name,email,phone,password_hash,role) VALUES (?,?,?,?,'customer')"
                );
                $stmt->bind_param('ssss', $name, $email, $phone, $hash);
                if ($stmt->execute())
                    $success = 'Registration successful! You can now log in.';
                else
                    $error = 'Email already registered.';
            }
        }
        include __DIR__ . '/../views/register.php';
    }

    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';
            $stmt  = $this->conn->prepare(
                "SELECT id,name,password_hash,is_active FROM users WHERE email=? AND role='customer'"
            );
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();

            if (!$row || !password_verify($pass, $row['password_hash']))
                $error = 'Incorrect email or password.';
            elseif (!$row['is_active'])
                $error = 'Your account has been deactivated.';
            else {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['name']    = $row['name'];
                $_SESSION['role']    = 'customer';
                header('Location: index.php?page=dashboard'); exit;
            }
        }
        include __DIR__ . '/../views/login.php';
    }
}
