<?php
$page_title = "Edit Profile - RoomReserve";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$errors = [];

// 1. Tarik data pengguna sedia ada (hanya username & email)
$user = [];
try {
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
    $stmt->close();
} catch (Exception $e) {
    // Fallback
}

// 2. Proses simpan perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_input = trim($_POST['username'] ?? '');
    $email_input    = trim($_POST['email'] ?? '');

    if (empty($username_input)) $errors[] = "Username / Full Name is required.";
    if (empty($email_input))    $errors[] = "Email Address is required.";

    if (empty($errors)) {
        try {
            // Kemaskini jadual 'users' untuk kolum 'username' dan 'email' sahaja
            $updateStmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $updateStmt->bind_param("ssi", $username_input, $email_input, $user_id);

            if ($updateStmt->execute()) {
                $_SESSION['username'] = $username_input;
                echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
                exit();
            } else {
                $errors[] = "Failed to update profile. Please try again.";
            }
            $updateStmt->close();
        } catch (Exception $e) {
            $errors[] = "System error: " . $e->getMessage();
        }
    }
}
?>

<div class="mb-4 text-white">
    <h1 class="fw-bold mb-1 fs-2">Edit Profile ✏️</h1>
    <p class="text-white-50 mb-0">Update your account details below.</p>
</div>

<div class="card glass-card shadow-lg rounded-4" style="max-width: 600px; margin: 0 auto;">
    <div class="p-4 text-white d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 1rem 1rem 0 0;">
        <div class="bg-primary bg-opacity-20 rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-person-gear text-white"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0 text-white">Account Info</h4>
            <p class="mb-0 text-white-50 small">Update your main account credentials.</p>
        </div>
    </div>

    <div class="p-4 p-md-5">
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

        <form method="POST" action="edit_profile.php">
            <div class="mb-3">
                <label class="form-label text-secondary fw-bold small">USERNAME / FULL NAME *</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? $username); ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-secondary fw-bold small">EMAIL ADDRESS *</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>

            <hr class="my-4" style="border-color: #e2e8f0;">

            <div class="d-flex justify-content-end gap-2">
                <a href="profile.php" class="btn btn-outline-secondary rounded-3 px-4 fw-semibold">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
                    <i class="bi bi-check-circle me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>