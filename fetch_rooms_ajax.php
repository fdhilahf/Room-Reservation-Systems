<?php
require_once 'config.php';

$keyword = $_GET['keyword'] ?? '';

$sql = "SELECT * FROM rooms WHERE room_name LIKE ? OR location LIKE ? OR amenities LIKE ?";
$searchTerm = "%" . $keyword . "%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<div class="row g-4">';
    while ($room = $result->fetch_assoc()) {
        $isAvailable = strtolower(trim($room['status'])) === 'available';
        $image = !empty($room['image']) ? $room['image'] : 'https://placehold.co/800x500?text=Room+Image';
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="card glass-card hover-card shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column">
                <div class="position-relative" style="height: 190px;">
                    <img src="<?= htmlspecialchars($image); ?>" class="w-100 h-100" style="object-fit: cover;" alt="Room Image">
                    <?php if ($isAvailable): ?>
                        <span class="position-absolute top-0 end-0 m-3 badge bg-success px-3 py-2 rounded-pill fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Available</span>
                    <?php else: ?>
                        <span class="position-absolute top-0 end-0 m-3 badge bg-danger px-3 py-2 rounded-pill fw-semibold"><i class="bi bi-x-circle-fill me-1"></i>Occupied</span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($room['room_name']); ?></h5>
                        <span class="text-muted small fw-semibold"><i class="bi bi-people me-1"></i><?= htmlspecialchars($room['capacity']); ?></span>
                    </div>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($room['location']); ?>
                    </div>
                    <p class="text-secondary small mb-4"><?= htmlspecialchars($room['amenities']); ?></p>
                    <div class="mt-auto row g-2">
                        <div class="col-12">
                            <?php if ($isAvailable): ?>
                                <a href="book.php?room_id=<?= $room['id']; ?>" class="btn btn-success w-100 fw-bold rounded-3 btn-sm py-2">Reserve Room</a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 fw-bold rounded-3 btn-sm py-2" disabled>Occupied</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    echo '</div>';
} else {
    echo '<div class="card glass-card shadow-sm rounded-4 p-5 text-center my-4"><h5 class="fw-bold text-dark">No matching rooms found</h5></div>';
}
$stmt->close();
?>