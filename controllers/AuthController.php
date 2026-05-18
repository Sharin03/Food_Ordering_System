<?php

class AuthController {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // =========================================================
    // REGISTER
    // =========================================================
    public function register() {

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name    = trim($_POST['name'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $phone   = trim($_POST['phone'] ?? '');
            $vehicle = trim($_POST['vehicle_type'] ?? '');
            $pass    = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            // VALIDATION
            if (!$name || !$email || !$phone || !$vehicle || !$pass) {

                $error = 'All fields are required.';
            }

            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $error = 'Invalid email address.';
            }

            elseif (strlen($pass) < 6) {

                $error = 'Password must be at least 6 characters.';
            }

            elseif ($pass !== $confirm) {

                $error = 'Passwords do not match.';
            }

            else {

                $hash = password_hash($pass, PASSWORD_BCRYPT);

                // INSERT USER
                $stmt = $this->conn->prepare(
                    "INSERT INTO users
                    (name,email,phone,password_hash,role)
                    VALUES (?,?,?,?,'agent')"
                );

                $stmt->bind_param(
                    'ssss',
                    $name,
                    $email,
                    $phone,
                    $hash
                );

                if ($stmt->execute()) {

                    $user_id = $this->conn->insert_id;

                    /*
                     AUTO APPROVAL ENABLED
                     is_approved = 1
                    */
                    $stmt2 = $this->conn->prepare(
                        "INSERT INTO delivery_agents
                        (user_id,vehicle_type,is_approved)
                        VALUES (?,?,1)"
                    );

                    $stmt2->bind_param(
                        'is',
                        $user_id,
                        $vehicle
                    );

                    $stmt2->execute();

                    $success = 'Registration successful! You can now log in.';
                }

                else {

                    $error = 'Email already registered.';
                }
            }
        }

        include __DIR__ . '/../views/register.php';
    }

    // =========================================================
    // LOGIN
    // =========================================================
    public function login() {

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';

            $stmt = $this->conn->prepare(
                "SELECT
                    u.id,
                    u.name,
                    u.password_hash,
                    u.is_active,
                    da.id AS agent_id
                 FROM users u
                 JOIN delivery_agents da
                 ON da.user_id = u.id
                 WHERE u.email=? AND u.role='agent'"
            );

            $stmt->bind_param('s', $email);

            $stmt->execute();

            $row = $stmt->get_result()->fetch_assoc();

            // LOGIN VALIDATION
            if (!$row || !password_verify($pass, $row['password_hash'])) {

                $error = 'Incorrect email or password.';
            }

            elseif (!$row['is_active']) {

                $error = 'Your account has been deactivated.';
            }

            else {

                $_SESSION['user_id']  = $row['id'];
                $_SESSION['agent_id'] = $row['agent_id'];
                $_SESSION['name']     = $row['name'];
                $_SESSION['role']     = 'agent';

                header('Location: index.php?page=dashboard');
                exit;
            }
        }

        include __DIR__ . '/../views/login.php';
    }
}