<?php
$page_title = "My Profile - RoomReserve";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Tarik data pengguna sedia ada
$user = [];
try {
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
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

$display_name = $user['username'] ?? $_SESSION['username'] ?? 'User';
$display_email = $user['email'] ?? 'No email provided';
$display_role = ucfirst($user['role'] ?? 'Student');
$initials = strtoupper(substr($display_name, 0, 2));
?>

<!-- PAGE TITLE -->
<div class="mb-4 text-white">
    <h1 class="fw-bold mb-1 fs-2">My Profile 👤</h1>
    <p class="text-white-50 mb-0">Manage your personal account settings and security options.</p>
</div>

<!-- PROFILE MAIN CARD -->
<div class="card glass-card shadow-lg rounded-4 overflow-hidden border-0 style="max-width: 800px; margin: 0 auto;"">
    
    <!-- HEADER AVATAR BANNER -->
    <div class="p-4 text-white d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <div class="profile-avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3 shadow-sm" style="width: 64px; height: 64px; border: 2px solid rgba(255,255,255,0.3);">
            <?= htmlspecialchars($initials); ?>
        </div>
        <div>
            <h4 class="fw-bold mb-1 text-white"><?= htmlspecialchars($display_name); ?></h4>
            <div class="d-flex align-items-center gap-2 text-white-50 small">
                <span class="badge bg-primary bg-opacity-20 text-info border border-info border-opacity-20 px-2 py-1 rounded-2"><?= htmlspecialchars($display_role); ?></span>
                <span>&bull;</span>
                <span><?= htmlspecialchars($display_email); ?></span>
            </div>
        </div>
    </div>

    <div class="p-4 p-md-5">

        <!-- PERSONAL INFORMATION SECTION -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-dark mb-0">Personal Information</h5>
            <a href="edit_profile.php" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold">
                <i class="bi bi-pencil-square me-1"></i> Edit Profile
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-muted fw-bold small">USERNAME / FULL NAME</label>
                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($display_name); ?>" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted fw-bold small">EMAIL ADDRESS</label>
                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($display_email); ?>" readonly>
            </div>
        </div>

        <hr class="my-4" style="border-color: #e2e8f0;">

        <!-- SECURITY SECTION -->
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark mb-1">Password & Security</h6>
                <p class="text-muted small mb-0">Manage and update your account password.</p>
            </div>
            <a href="change_password.php" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-semibold">
                <i class="bi bi-shield-lock me-1"></i> Change Password
            </a>
        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>