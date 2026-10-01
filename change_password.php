<?php
$page_title = "Change Password - RoomReserve";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password)) $errors[] = "Sila masukkan kata laluan semasa.";
    if (empty($new_password))     $errors[] = "Sila masukkan kata laluan baharu.";
    if (strlen($new_password) < 6) $errors[] = "Kata laluan baharu mestilah sekurang-kurangnya 6 aksara.";
    if ($new_password !== $confirm_password) $errors[] = "Pengesahan kata laluan tidak sepadan.";

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $row = $result->fetch_assoc()) {
                $db_password = $row['password'];
                if (password_verify($current_password, $db_password) || ($current_password === $db_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $hashed_password, $user_id);

                    if ($updateStmt->execute()) {
                        echo "<script>alert('Kata laluan berjaya ditukar!'); window.location.href='profile.php';</script>";
                        exit();
                    } else {
                        $errors[] = "Gagal mengemaskini kata laluan.";
                    }
                    $updateStmt->close();
                } else {
                    $errors[] = "Kata laluan semasa adalah salah.";
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = "Ralat sistem: " . $e->getMessage();
        }
    }
}
?>

<div class="mb-4 text-white">
    <h1 class="fw-bold mb-1 fs-2">Change Password 🔒</h1>
    <p class="text-white-50 mb-0">Update your account password to maintain security.</p>
</div>

<div class="card glass-card shadow-lg rounded-4 style="max-width: 650px; margin: 0 auto;"">
    <div class="p-4 text-white d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 1rem 1rem 0 0;">
        <div class="bg-primary bg-opacity-20 rounded-3 p-3 fs-3"><i class="bi bi-shield-lock-fill"></i></div>
        <div>
            <h4 class="fw-bold mb-0 text-white">Password & Security</h4>
            <p class="mb-0 text-white-50 small">Ensure your new password is strong and secure.</p>
        </div>
    </div>

    <div class="p-4">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-secondary fw-bold small">CURRENT PASSWORD</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary fw-bold small">NEW PASSWORD</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary fw-bold small">CONFIRM NEW PASSWORD</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="profile.php" class="btn btn-outline-secondary rounded-3 px-4 fw-semibold">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold"><i class="bi bi-check-circle me-1"></i> Update Password</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>