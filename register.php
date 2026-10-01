<?php
require_once 'config.php';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = 'user'; // Default role

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $stmt->close();
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>Login</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Room Booking System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    
    <!-- BANNER ATAS -->
    <div class="top-banner text-center py-4 text-white mb-5">
        <h2 class="fw-bold mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Room Booking System</h2>
        <p class="mb-0 text-white-50">Welcome to the Official Portal</p>
    </div>

    <!-- FORM REGISTER -->
    <div class="container d-flex justify-content-center pb-5" style="max-width: 440px;">
        <div class="card card-main shadow-lg border-0 rounded-4 p-4 w-100">
            <div class="card-body">
                <div class="text-center mb-3">
                    <h5 class="fw-bold text-dark">Create Account</h5>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger py-2 small rounded-3"><?= $error ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert alert-success py-2 small rounded-3"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Username</label>
                        <input type="text" name="username" class="form-control form-control-lg fs-6 rounded-3" placeholder="Your username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-lg fs-6 rounded-3" placeholder="name@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg fs-6 rounded-3" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-custom w-100 py-2 fw-semibold rounded-3 shadow-sm">Sign Up</button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">Already have an account? <a href="login.php" class="text-decoration-none fw-semibold text-primary">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>