<?php

class AuthController {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register() {

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name      = trim($_POST['name'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $phone     = trim($_POST['phone'] ?? '');
            $pass      = $_POST['password'] ?? '';
            $confirm   = $_POST['confirm_password'] ?? '';
            $rest_name = trim($_POST['restaurant_name'] ?? '');
            $cuisine   = trim($_POST['cuisine_type'] ?? '');
            $address   = trim($_POST['address'] ?? '');
            $city      = trim($_POST['city'] ?? '');

            // Validation
            if (
                !$name || !$email || !$phone || !$pass ||
                !$rest_name || !$cuisine || !$address || !$city
            ) {

                $error = 'All fields are required.';
            }

            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $error = 'Invalid email address.';
            }

            elseif ($pass !== $confirm) {

                $error = 'Passwords do not match.';
            }

            elseif (strlen($pass) < 6) {

                $error = 'Password must be at least 6 characters.';
            }

            else {

                // CHECK IF EMAIL ALREADY EXISTS
                $check = $this->conn->prepare(
                    "SELECT id FROM users WHERE email=?"
                );

                $check->bind_param('s', $email);
                $check->execute();
                $result = $check->get_result();

                if ($result->num_rows > 0) {

                    $error = 'Email already registered.';
                }

                else {

                    $hash = password_hash($pass, PASSWORD_BCRYPT);

                    $stmt = $this->conn->prepare(
                        "INSERT INTO users
                        (name,email,phone,password_hash,role)
                        VALUES (?,?,?,?, 'manager')"
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

                        // AUTO APPROVAL ENABLED
                        $stmt2 = $this->conn->prepare(
                            "INSERT INTO restaurants
                            (manager_id,name,cuisine_type,address,city,is_approved)
                            VALUES (?,?,?,?,?,1)"
                        );

                        $stmt2->bind_param(
                            'issss',
                            $user_id,
                            $rest_name,
                            $cuisine,
                            $address,
                            $city
                        );

                        $stmt2->execute();

                        $success = 'Registration successful! You can now log in.';
                    }

                    else {

                        $error = 'Registration failed.';
                    }
                }
            }
        }

        include __DIR__ . '/../views/register.php';
    }

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
                    r.id AS restaurant_id,
                    r.is_approved
                 FROM users u
                 JOIN restaurants r ON r.manager_id = u.id
                 WHERE u.email = ?
                 AND u.role = 'manager'"
            );

            $stmt->bind_param('s', $email);
            $stmt->execute();

            $row = $stmt->get_result()->fetch_assoc();

            if (!$row || !password_verify($pass, $row['password_hash'])) {

                $error = 'Incorrect email or password.';
            }

            elseif (!$row['is_active']) {

                $error = 'Your account has been deactivated.';
            }

            else {

                $_SESSION['user_id']       = $row['id'];
                $_SESSION['name']          = $row['name'];
                $_SESSION['role']          = 'manager';
                $_SESSION['restaurant_id'] = $row['restaurant_id'];

                header('Location: index.php?page=dashboard');
                exit;
            }
        }

        include __DIR__ . '/../views/login.php';
    }
}