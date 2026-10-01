<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? 'User';
$initials = strtoupper(substr($username, 0, 2));

// Ambil nama fail semasa untuk set active link automatik
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'RoomReserve'; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- Pindahkan semua CSS ke fail external -->
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shining-bar py-3">
    <div class="container px-4">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
            <i class="bi bi-calendar-event fs-4"></i><span>RoomReserve</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link text-white <?= ($current_page == 'dashboard.php') ? 'active-nav px-3 rounded-pill' : '' ?>" href="dashboard.php">
                        <i class="bi bi-house-door me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= ($current_page == 'find_room.php') ? 'active-nav px-3 rounded-pill' : '' ?>" href="find_room.php">
                        <i class="bi bi-search me-1"></i> Find a Room
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= ($current_page == 'reservations.php') ? 'active-nav px-3 rounded-pill' : '' ?>" href="reservations.php">
                        <i class="bi bi-journal-check me-1"></i> My Reservations
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="btn btn-light btn-sm text-dark d-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-semibold text-decoration-none shadow-sm">
                        <span class="profile-avatar"><?= htmlspecialchars($initials); ?></span><?= htmlspecialchars($username); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm px-3 rounded-pill text-white" href="logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="content-wrap">