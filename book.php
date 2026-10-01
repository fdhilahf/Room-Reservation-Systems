<?php
$page_title = "Make a Reservation - RoomReserve";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Semak sama ada EDIT tempahan sedia ada atau TEMPAPAN BAHARU
$edit_id = $_GET['edit_id'] ?? null;
$room_id = $_GET['room_id'] ?? 1;

$room_name   = "Focus Room 1";
$search_date = date('Y-m-d');
$start_time  = '09:00';
$end_time    = '11:00';

// Jika MODE EDIT, tarik data asal dari database
if ($edit_id) {
    $stmt_edit = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt_edit->bind_param("ii", $edit_id, $user_id);
    $stmt_edit->execute();
    $res_edit = $stmt_edit->get_result();
    if ($row_edit = $res_edit->fetch_assoc()) {
        $room_name   = $row_edit['room_name'];
        $search_date = $row_edit['booking_date'];
        $times = explode(' - ', $row_edit['time_slot']);
        $start_time  = $times[0] ?? '09:00';
        $end_time    = $times[1] ?? '11:00';
    }
    $stmt_edit->close();
}

$step = $_POST['step'] ?? 'form';

/* PROSES SIMPAN / UPDATE (CRUD: Create & Update) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_reservation'])) {
    $p_room_name   = $_POST['room_name'];
    $p_search_date = $_POST['search_date'];
    $p_start_time  = $_POST['start_time'];
    $p_end_time    = $_POST['end_time'];
    $time_slot     = $p_start_time . ' - ' . $p_end_time;
    
    if (!empty($_POST['edit_id'])) {
        // JIKA EDIT -> JALANKAN UPDATE QUERY
        $edit_target_id = (int)$_POST['edit_id'];
        $update_stmt = $conn->prepare("UPDATE bookings SET room_name = ?, booking_date = ?, time_slot = ? WHERE id = ? AND user_id = ?");
        $update_stmt->bind_param("sssii", $p_room_name, $p_search_date, $time_slot, $edit_target_id, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // JIKA BAHARU -> JALANKAN INSERT QUERY
        $insert_stmt = $conn->prepare("INSERT INTO bookings (user_id, room_name, booking_date, time_slot, status) VALUES (?, ?, ?, ?, 'Upcoming')");
        $insert_stmt->bind_param("isss", $user_id, $p_room_name, $p_search_date, $time_slot);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $step = 'confirmed';
}
?>

<?php if ($step === 'confirmed'): ?>
    <div class="d-flex justify-content-center py-4">
        <div class="card glass-card shadow-lg rounded-4 p-4 text-center" style="max-width: 480px; width: 100%;">
            <div class="bg-success text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.8rem;">
                <i class="bi bi-check-lg"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?= $edit_id ? 'Reservation Updated!' : 'Reservation Confirmed!'; ?></h4>
            <p class="text-muted small mb-4">Your room booking record has been successfully processed.</p>
            <a href="reservations.php" class="btn btn-dark w-100 py-2 rounded-3 fw-semibold">View My Reservations</a>
        </div>
    </div>
<?php else: ?>
    <div class="card glass-card shadow-sm rounded-4 p-4 style="max-width: 600px; margin: 0 auto;"">
        <h5 class="fw-bold text-dark mb-4"><?= $edit_id ? 'Edit Reservation' : 'Make a Reservation'; ?></h5>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($edit_id ?? ''); ?>">
            
            <div class="mb-3">
                <label class="form-label text-muted fw-bold small">ROOM NAME</label>
                <input type="text" name="room_name" class="form-control" value="<?= htmlspecialchars($room_name); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted fw-bold small">DATE</label>
                <input type="date" name="search_date" class="form-control" value="<?= htmlspecialchars($search_date); ?>" required>
            </div>
            <div class="row g-2 mb-4">
                <div class="col-6">
                    <label class="form-label text-muted fw-bold small">START TIME</label>
                    <input type="time" name="start_time" class="form-control" value="<?= htmlspecialchars($start_time); ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label text-muted fw-bold small">END TIME</label>
                    <input type="time" name="end_time" class="form-control" value="<?= htmlspecialchars($end_time); ?>" required>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="reservations.php" class="btn btn-outline-secondary btn-sm px-3 rounded-3">Cancel</a>
                <button type="submit" name="confirm_reservation" class="btn btn-success btn-sm px-4 rounded-3 fw-semibold">Save Reservation</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>