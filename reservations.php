<?php
$page_title = "My Reservations - RoomReserve";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$status_filter = $_GET['status'] ?? 'Upcoming';

/* PROSES CANCEL BOOKING (DELETE / UPDATE STATUS) */
if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['id'])) {
    $booking_id_to_cancel = (int)$_GET['id'];
    
    $stmt_cancel = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
    $stmt_cancel->bind_param("ii", $booking_id_to_cancel, $user_id);
    
    if ($stmt_cancel->execute()) {
        echo "<script>alert('Reservation has been cancelled successfully.'); window.location.href='reservations.php?status=Cancelled';</script>";
        exit();
    }
    $stmt_cancel->close();
}

/* KIRAAN REKOD UNTUK TAB */
$count_upcoming  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings WHERE user_id = '$user_id' AND status = 'Upcoming'"));
$count_past      = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings WHERE user_id = '$user_id' AND status = 'Past'"));
$count_cancelled = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings WHERE user_id = '$user_id' AND status = 'Cancelled'"));

/* TARIK DATA TEMPAPAN */
$stmt_get = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? AND status = ? ORDER BY booking_date DESC");
$stmt_get->bind_param("is", $user_id, $status_filter);
$stmt_get->execute();
$result = $stmt_get->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="text-white fw-bold mb-1" style="font-size: 2rem;">My Reservations</h1>
        <p class="text-white-50 mb-0">Manage and view all your room bookings.</p>
    </div>
    <a href="find_room.php" class="btn btn-dark fw-bold px-4 py-2 rounded-3 shadow-sm" style="background-color: #1e293b; border: none;">
        <i class="bi bi-plus-lg me-1"></i> New Booking
    </a>
</div>

<!-- TAB FILTER -->
<div class="d-flex gap-2 mb-4 p-1 rounded-3" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(10px); width: fit-content;">
    <a href="reservations.php?status=Upcoming" class="btn btn-sm <?= ($status_filter == 'Upcoming') ? 'btn-dark px-3 fw-bold' : 'text-secondary px-3' ?> rounded-2">
        Upcoming <span class="badge bg-secondary bg-opacity-20 text-dark ms-1"><?= $count_upcoming ?></span>
    </a>
    <a href="reservations.php?status=Past" class="btn btn-sm <?= ($status_filter == 'Past') ? 'btn-dark px-3 fw-bold' : 'text-secondary px-3' ?> rounded-2">
        Past <span class="badge bg-secondary bg-opacity-20 text-dark ms-1"><?= $count_past ?></span>
    </a>
    <a href="reservations.php?status=Cancelled" class="btn btn-sm <?= ($status_filter == 'Cancelled') ? 'btn-dark px-3 fw-bold' : 'text-secondary px-3' ?> rounded-2">
        Cancelled <span class="badge bg-secondary bg-opacity-20 text-dark ms-1"><?= $count_cancelled ?></span>
    </a>
</div>

<!-- KAD TEMPAPAN -->
<div class="d-flex flex-column gap-3 mb-5">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card glass-card shadow-sm rounded-4 border-0 p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-light rounded-4 text-primary fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($row['room_name']); ?></h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 rounded-pill" style="font-size: 0.7rem;"><?= htmlspecialchars($row['status']); ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-3 text-muted small flex-wrap">
                                <span><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($row['booking_date'])); ?></span>
                                <span><i class="bi bi-clock me-1"></i><?= htmlspecialchars($row['time_slot']); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($row['status'] === 'Upcoming'): ?>
                        <div class="d-flex align-items-center gap-2 ms-auto ms-md-0">
                            <a href="book.php?edit_id=<?= $row['id']; ?>" class="btn btn-outline-primary btn-sm px-3 rounded-2 fw-semibold">Edit</a>
                            <a href="reservations.php?action=cancel&id=<?= $row['id']; ?>" class="btn btn-outline-danger btn-sm px-3 rounded-2 fw-semibold" onclick="return confirm('Are you sure you want to cancel this reservation?');">Cancel</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card glass-card shadow-sm rounded-4 p-5 text-center">
            <h5 class="fw-bold text-dark mb-1">No Reservations Found</h5>
            <p class="text-secondary mb-0 small">You don't have any <?= strtolower($status_filter); ?> room reservations recorded.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>