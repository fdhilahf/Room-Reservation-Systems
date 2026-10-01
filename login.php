<?php
require_once 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check empty fields
    if (empty($email) || empty($password)) {

        $error = "All fields are required.";

    } else {

        // Search user by email
        $stmt = $conn->prepare(
            "SELECT id, username, password, role 
             FROM users 
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Get result
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            // Get user data
            $user = $result->fetch_assoc();

            $id = $user['id'];
            $username = $user['username'];
            $hashed_password = $user['password'];
            $role = $user['role'];

            // Check password
            if (password_verify($password, $hashed_password)) {

                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                // Admin
                if ($role === 'admin') {

                    header("Location: admin/dashboard.php");
                    exit();

                } else {

                    // Normal user (Tukar index.php ke dashboard.php)
                    header("Location: dashboard.php");
                    exit();
                }

            } else {

                $error = "Invalid password.";
            }

        } else {

            $error = "No user found with that email.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Room Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .top-banner {
            backdrop-filter: blur(15px);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        .top-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shine 3s infinite;
        }
        @keyframes shine {
            0% { left: -100%; }
            20% { left: 100%; }
            100% { left: 100%; }
        }
        .card-main {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.95);
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: opacity 0.3s ease;
        }
        .btn-custom:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    
    <div class="top-banner text-center py-4 text-white mb-5">
        <h2 class="fw-bold mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Room Booking System</h2>
        <p class="mb-0 text-white-50">Welcome to the Official Portal</p>
    </div>

    <div class="container d-flex justify-content-center pb-5" style="max-width: 440px;">
        <div class="card card-main shadow-lg border-0 rounded-4 p-4 w-100">
            <div class="card-body">
                <div class="text-center mb-3">
                    <h5 class="fw-bold text-dark">Member Login</h5>
                </div>

                <?php if(isset($error) && $error): ?>
                    <div class="alert alert-danger py-2 small rounded-3"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-lg fs-6 rounded-3" placeholder="name@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg fs-6 rounded-3" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-custom w-100 py-2 fw-semibold rounded-3 shadow-sm">Login</button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">Don't have an account? <a href="register.php" class="text-decoration-none fw-semibold text-primary">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>