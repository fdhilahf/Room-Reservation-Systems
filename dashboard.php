<?php
$page_title = "Dashboard - RoomReserve";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Kira jumlah bilik dalam database rooms
$q_available = mysqli_query($conn, "SELECT COUNT(*) as total FROM rooms WHERE status = 'available'");
$available_rooms = mysqli_fetch_assoc($q_available)['total'] ?? 0;

$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM rooms");
$total_rooms = mysqli_fetch_assoc($q_total)['total'] ?? 0;

// Kira jumlah tempahan user dalam database bookings
$q_my = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE user_id = '$user_id'");
$my_reservations = mysqli_fetch_assoc($q_my)['total'] ?? 0;

// Ambil tempahan terkini dari database
$query_upcoming = "SELECT * FROM bookings WHERE user_id = '$user_id' AND status = 'Upcoming' ORDER BY booking_date ASC LIMIT 3";
$result_upcoming = mysqli_query($conn, $query_upcoming);
?>

<!-- WELCOME HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="welcome-title mb-1">Welcome back, <?= htmlspecialchars($username); ?>! 👋</h1>
        <p class="welcome-subtitle mb-0"><?= date('l, d F Y'); ?> &middot; Here's your overview from live database.</p>
    </div>
    <a href="find_room.php" class="btn btn-light fw-bold px-4 py-2 rounded-3 shadow-sm text-dark"><i class="bi bi-plus-lg me-1"></i>New Reservation</a>
</div>

<!-- STATS CARDS (DATA REAL-TIME MYSQL) -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card glass-card shadow-sm rounded-4 p-3 hover-card h-100">
            <div class="card-body">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-3"><i class="bi bi-door-open fs-4"></i></div>
                <h3 class="fw-bold text-dark stat-number mb-0"><?= $available_rooms; ?></h3>
                <p class="text-muted small mb-1">Available Rooms</p>
                <span class="text-secondary" style="font-size: 0.75rem;">out of <?= $total_rooms; ?> total rooms in DB</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card glass-card shadow-sm rounded-4 p-3 hover-card h-100">
            <div class="card-body">
                <div class="stat-icon bg-info bg-opacity-10 text-info mb-3"><i class="bi bi-journal-text fs-4"></i></div>
                <h3 class="fw-bold text-dark stat-number mb-0"><?= $my_reservations; ?></h3>
                <p class="text-muted small mb-1">My Total Reservations</p>
                <span class="text-secondary" style="font-size: 0.75rem;">recorded in system</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card glass-card shadow-sm rounded-4 p-3 hover-card h-100">
            <div class="card-body">
                <div class="stat-icon bg-success bg-opacity-10 text-success mb-3"><i class="bi bi-check-circle fs-4"></i></div>
                <h3 class="fw-bold text-dark stat-number mb-0"><?= $available_rooms; ?></h3>
                <p class="text-muted small mb-1">Active Rooms</p>
                <span class="text-success fw-semibold" style="font-size: 0.75rem;">ready for booking</span>
            </div>
        </div>
    </div>
</div>

<!-- LOWER SECTION -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card glass-card shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">Upcoming Reservations</h5>
                <a href="reservations.php" class="text-decoration-none small fw-semibold text-primary">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <hr class="text-muted opacity-25">

            <?php if ($result_upcoming && mysqli_num_rows($result_upcoming) > 0): ?>
                <?php while ($res = mysqli_fetch_assoc($result_upcoming)): ?>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-white shadow-sm mb-3 border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary fs-4"><i class="bi bi-calendar-event"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($res['room_name']); ?></h6>
                                <p class="text-muted small mb-0">Slot: <?= htmlspecialchars($res['time_slot']); ?></p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block fw-semibold small text-dark"><?= htmlspecialchars($res['booking_date']); ?></span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 mt-1 d-block"><?= htmlspecialchars($res['status']); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <p class="mb-0">No upcoming reservations found in database.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card glass-card shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">Quick Actions</h5>
            <hr class="text-muted opacity-25">
            <div class="d-flex flex-column gap-3">
                <a href="find_room.php" class="p-3 rounded-3 text-decoration-none text-white shadow-sm quick-action-dark">
                    <div class="fw-bold mb-1"><i class="bi bi-search me-2"></i>Find a Room</div>
                    <div class="text-white-50 small">Search available rooms</div>
                </a>
                <a href="reservations.php" class="p-3 rounded-3 text-decoration-none text-dark bg-white shadow-sm border">
                    <div class="fw-bold mb-1 text-dark"><i class="bi bi-journal-check me-2 text-primary"></i>My Reservations</div>
                    <div class="text-muted small">Manage your bookings</div>
                </a>
                <a href="profile.php" class="p-3 rounded-3 text-decoration-none text-dark bg-white shadow-sm border">
                    <div class="fw-bold mb-1 text-dark"><i class="bi bi-person-circle me-2 text-primary"></i>My Profile</div>
                    <div class="text-muted small">View and edit your profile</div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>