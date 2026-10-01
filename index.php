<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Room Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
    
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content-wrap {
            flex: 1;
        }
        /* Navbar & Footer Transparent Shine Effect */
        .shining-bar {
            backdrop-filter: blur(15px);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.05) 100%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }
        .shining-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shine 3.5s infinite;
        }
        @keyframes shine {
            0% { left: -100%; }
            20% { left: 100%; }
            100% { left: 100%; }
        }
        .glass-card {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.95);
            border: none;
        }
        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.15)!important;
        }
        .banner-box {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border-radius: 1rem;
        }
    </style>
</head>
<body>
    
    <!-- NAVBAR TRANSPARENT SHINE -->
    <nav class="navbar navbar-expand-lg navbar-dark shining-bar py-3">
        <div class="container px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <i class="bi bi-calendar-event fs-4"></i> RoomReserve
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link active bg-primary px-3 rounded-pill text-white shadow-sm" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="find_room.php">Find a Room</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">My Reservations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Calendar</a>
                    </li>

                    <!-- PROFIL LINK (Terus ke profile.php bila tekan nama/avatar mimi) -->
                    <li class="nav-item">
                        <a href="profile.php" class="btn btn-light btn-sm text-dark d-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-semibold text-decoration-none shadow-sm">
                            <span class="badge bg-dark text-white rounded-circle p-1">
                                <?= strtoupper(substr($_SESSION['username'], 0, 2)); ?>
                            </span> 
                            <?= htmlspecialchars($_SESSION['username']); ?>
                        </a>
                    </li>

                    <!-- LOGOUT BUTTON -->
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm px-3 rounded-pill text-white" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENT WRAPPER -->
    <div class="content-wrap container py-5" style="max-width: 1200px;">
        
        <!-- WELCOME SECTION -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="fw-bold text-white mb-1">Welcome back, <?= htmlspecialchars($_SESSION['username']); ?>! 👋</h1>
                <p class="text-white-50 mb-0"><?= date('l, d F Y'); ?> &middot; Here's your overview for today.</p>
            </div>
            <a href="#" class="btn btn-light fw-bold px-4 py-2 rounded-3 shadow-sm text-dark">
                <i class="bi bi-plus-lg me-1"></i> New Reservation
            </a>
        </div>

        <!-- STATS CARDS GRID -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card glass-card shadow-sm rounded-4 p-3 hover-card h-100">
                    <div class="card-body">
                        <div class="text-primary mb-2"><i class="bi bi-calendar-check fs-3"></i></div>
                        <h3 class="fw-bold text-dark mb-0">18</h3>
                        <p class="text-muted small mb-1">Available Rooms</p>
                        <span class="text-secondary" style="font-size: 0.75rem;">out of 24 total</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card glass-card shadow-sm rounded-4 p-3 hover-card h-100">
                    <div class="card-body">
                        <div class="text-info mb-2"><i class="bi bi-journal-text fs-3"></i></div>
                        <h3 class="fw-bold text-dark mb-0">4</h3>
                        <p class="text-muted small mb-1">My Reservations</p>
                        <span class="text-secondary" style="font-size: 0.75rem;">total bookings</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card glass-card shadow-sm rounded-4 p-3 hover-card h-100">
                    <div class="card-body">
                        <div class="text-warning mb-2"><i class="bi bi-clock-history fs-3"></i></div>
                        <h3 class="fw-bold text-dark mb-0">2</h3>
                        <p class="text-muted small mb-1">Upcoming Meetings</p>
                        <span class="text-secondary" style="font-size: 0.75rem;">this week</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card glass-card shadow-sm rounded-4 p-3 hover-card h-100">
                    <div class="card-body">
                        <div class="text-success mb-2"><i class="bi bi-check-circle fs-3"></i></div>
                        <h3 class="fw-bold text-dark mb-0">12</h3>
                        <p class="text-muted small mb-1">Available Today</p>
                        <span class="text-success fw-semibold" style="font-size: 0.75rem;">rooms free now</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- BANNER CARDS: FIND A MEETING ROOM -->
        <div class="banner-box p-4 p-md-5 mb-4 shadow-lg d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-search me-2"></i>Find a Meeting Room</h3>
                <p class="text-white-50 mb-0">Search and reserve rooms by date, time, capacity, and equipment.</p>
            </div>
            <a href="#" class="btn btn-primary fw-semibold px-4 py-2 rounded-3 shadow-sm">
                Search Available Rooms &rarr;
            </a>
        </div>

        <!-- LOWER SECTION: UPCOMING & QUICK ACTIONS -->
        <div class="row g-4">
            <!-- UPCOMING RESERVATIONS -->
            <div class="col-lg-8">
                <div class="card glass-card shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">Upcoming Reservations</h5>
                        <a href="#" class="text-decoration-none small fw-semibold text-primary">View all &rarr;</a>
                    </div>
                    <hr class="text-muted opacity-25">
                    
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-white shadow-sm mb-3 border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Q4 Strategy Review</h6>
                                <p class="text-muted small mb-0">Boardroom Alpha &middot; Level 5, Block A</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block fw-semibold small text-dark">22 Sept</span>
                            <span class="text-muted" style="font-size: 0.75rem;">09:00 – 11:00</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 mt-1 d-block">Confirmed</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-white shadow-sm border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Product Design Sprint</h6>
                                <p class="text-muted small mb-0">Innovation Hub &middot; Level 3, Block B</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block fw-semibold small text-dark">23 Sept</span>
                            <span class="text-muted" style="font-size: 0.75rem;">14:00 – 17:00</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 mt-1 d-block">Confirmed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="col-lg-4">
                <div class="card glass-card shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3">Quick Actions</h5>
                    <hr class="text-muted opacity-25">
                    
                    <div class="d-flex flex-column gap-3">
                        <a href="#" class="p-3 rounded-3 text-decoration-none text-white shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                            <div class="fw-bold mb-1"><i class="bi bi-search me-2"></i>Find a Room</div>
                            <div class="text-white-50 small">Search available rooms</div>
                        </a>
                        <a href="#" class="p-3 rounded-3 text-decoration-none text-dark bg-white shadow-sm border hover-card">
                            <div class="fw-bold mb-1 text-dark"><i class="bi bi-journal-check me-2 text-primary"></i>My Reservations</div>
                            <div class="text-muted small">Manage your bookings</div>
                        </a>
                        <a href="#" class="p-3 rounded-3 text-decoration-none text-dark bg-white shadow-sm border hover-card">
                            <div class="fw-bold mb-1 text-dark"><i class="bi bi-calendar3 me-2 text-primary"></i>View Calendar</div>
                            <div class="text-muted small">Room availability overview</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER TRANSPARENT SHINE -->
    <footer class="shining-bar text-center py-3 text-white mt-auto">
        <p class="small mb-0" style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">&copy; 2026 Room Booking System. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>