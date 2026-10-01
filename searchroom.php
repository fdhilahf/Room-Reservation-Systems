<?php
$page_title = "Find a Room - RoomReserve";
include 'includes/header.php';

/*AUTO-CREATE & FORCE RE-SEED 'ROOMS' TABLE*/

$createTableSQL = "
    CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_name VARCHAR(100) NOT NULL,
        capacity INT NOT NULL,
        location VARCHAR(150) NOT NULL,
        amenities VARCHAR(255) DEFAULT NULL,
        status VARCHAR(50) DEFAULT 'available',
        image VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$conn->query($createTableSQL);

// Force reset table to load all 6 default rooms
$conn->query("TRUNCATE TABLE rooms");

// Insert 6 rooms: 4 Available, 2 Occupied
$conn->query("
    INSERT INTO rooms (room_name, capacity, location, amenities, status, image) VALUES
    ('Boardroom Alpha', 20, 'Level 1, Block A', 'Wi-Fi, Projector, Whiteboard, Video Conference', 'available', 'images/images1.jfif'),
    ('Conference Suite B', 30, 'Level 1, Block C', 'Wi-Fi, Projector, Whiteboard, Video Conference', 'occupied', 'images/images2.jfif'),
    ('Innovation Hub', 12, 'Level 2, Block B', 'Wi-Fi, Whiteboard, Video Conference, Smart TV', 'available', 'images/images3.jfif'),
    ('Creative Lab', 8, 'Level 2, Block A', 'Wi-Fi, Whiteboard, Smart TV', 'occupied', 'images/images4.jfif'),
    ('Focus Room 1', 6, 'Level 3, Block A', 'Wi-Fi, Whiteboard, Smart TV', 'available', 'images/images5.jfif'),
    ('Executive Suite', 20, 'Level 3, Block C', 'Wi-Fi, Projector, Video Conference, Whiteboard', 'available', 'images/images6.jfif')
");

// Explicitly set 4 Available and 2 Occupied
$conn->query("UPDATE rooms SET status = 'available'");
$conn->query("UPDATE rooms SET status = 'occupied' WHERE room_name IN ('Conference Suite B', 'Creative Lab')");

/*HELPER FUNCTION FOR AMENITY ICONS*/
function getAmenityIcon($amenity) {
    $clean = strtolower(trim($amenity));
    if (str_contains($clean, 'wifi') || str_contains($clean, 'wi-fi')) {
        return 'bi-wifi';
    } elseif (str_contains($clean, 'projector')) {
        return 'bi-projector';
    } elseif (str_contains($clean, 'tv') || str_contains($clean, 'smart tv')) {
        return 'bi-tv';
    } elseif (str_contains($clean, 'whiteboard')) {
        return 'bi-easel';
    } elseif (str_contains($clean, 'video') || str_contains($clean, 'conference')) {
        return 'bi-camera-video';
    }
    return 'bi-check-circle';
}

/*SEARCH PARAMETERS*/

$search_date  = $_POST['search_date'] ?? date('Y-m-d');
$start_time   = $_POST['start_time'] ?? '09:00';
$end_time     = $_POST['end_time'] ?? '11:00';
$attendees    = $_POST['attendees'] ?? '';
$min_capacity = $_POST['min_capacity'] ?? '';
$floor        = $_POST['floor'] ?? '';
$equipment    = $_POST['equipment'] ?? '';
$status       = $_POST['status'] ?? '';

$rooms = [];

/*SEARCH ROOMS QUERY*/

$sql = "SELECT * FROM rooms WHERE 1=1";
$params = [];
$types  = "";

if (!empty($attendees)) {
    $sql .= " AND capacity >= ? ";
    $params[] = (int)$attendees;
    $types .= "i";
}

if (!empty($min_capacity)) {
    $sql .= " AND capacity >= ? ";
    $params[] = (int)$min_capacity;
    $types .= "i";
}

if (!empty($floor)) {
    $sql .= " AND (location LIKE ? OR location LIKE ?) ";
    $params[] = "%Level " . $floor . "%";
    $params[] = "%Floor " . $floor . "%";
    $types .= "ss";
}

if (!empty($equipment)) {
    $sql .= " AND LOWER(amenities) LIKE ? ";
    $params[] = "%" . strtolower($equipment) . "%";
    $types .= "s";
}

if (!empty($status)) {
    $sql .= " AND LOWER(status) = ? ";
    $params[] = strtolower($status);
    $types .= "s";
}

$sql .= " ORDER BY id ASC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    $stmt->close();
}
?>

<!-- PAGE TITLE -->
<div class="mb-4">
    <h1 class="text-white fw-bold mb-1">Search Results</h1>
    <p class="text-white-50 mb-0">Found matching rooms based on your filter selection.</p>
</div>

<!-- SEARCH FILTER CARD -->
<div class="card glass-card shadow-sm rounded-4 p-4 mb-4">
    <form action="searchroom.php" method="POST">
        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label text-dark fw-bold small">Date</label>
                <input type="date" name="search_date" class="form-control" value="<?= htmlspecialchars($search_date); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label text-dark fw-bold small">Start Time</label>
                <input type="time" name="start_time" class="form-control" value="<?= htmlspecialchars($start_time); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label text-dark fw-bold small">End Time</label>
                <input type="time" name="end_time" class="form-control" value="<?= htmlspecialchars($end_time); ?>" required>
            </div>

            <div class="col-md-3">
                <label class="form-label text-dark fw-bold small">Number of Attendees</label>
                <input type="number" name="attendees" class="form-control" placeholder="e.g. 5" value="<?= htmlspecialchars($attendees); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label text-dark fw-bold small">Floor</label>
                <select name="floor" class="form-select">
                    <option value="">Any Floor</option>
                    <option value="1" <?= $floor == '1' ? 'selected' : ''; ?>>Level 1</option>
                    <option value="2" <?= $floor == '2' ? 'selected' : ''; ?>>Level 2</option>
                    <option value="3" <?= $floor == '3' ? 'selected' : ''; ?>>Level 3</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label text-dark fw-bold small">Minimum Capacity</label>
                <select name="min_capacity" class="form-select">
                    <option value="">Any Capacity</option>
                    <option value="4" <?= $min_capacity == '4' ? 'selected' : ''; ?>>4 People</option>
                    <option value="8" <?= $min_capacity == '8' ? 'selected' : ''; ?>>8 People</option>
                    <option value="12" <?= $min_capacity == '12' ? 'selected' : ''; ?>>12 People</option>
                    <option value="20" <?= $min_capacity == '20' ? 'selected' : ''; ?>>20 People</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label text-dark fw-bold small">Equipment</label>
                <select name="equipment" class="form-select">
                    <option value="">Any Equipment</option>
                    <option value="Wi-Fi" <?= $equipment == 'Wi-Fi' ? 'selected' : ''; ?>>Wi-Fi</option>
                    <option value="Projector" <?= $equipment == 'Projector' ? 'selected' : ''; ?>>Projector</option>
                    <option value="Smart TV" <?= $equipment == 'Smart TV' ? 'selected' : ''; ?>>Smart TV</option>
                    <option value="Whiteboard" <?= $equipment == 'Whiteboard' ? 'selected' : ''; ?>>Whiteboard</option>
                    <option value="Video Conference" <?= $equipment == 'Video Conference' ? 'selected' : ''; ?>>Video Conference</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label text-dark fw-bold small">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="available" <?= $status == 'available' ? 'selected' : ''; ?>>Available Only</option>
                    <option value="occupied" <?= $status == 'occupied' ? 'selected' : ''; ?>>Occupied Only</option>
                </select>
            </div>

            <div class="col-12 mt-3 text-end">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="bi bi-search me-1"></i> Update Search
                </button>
            </div>

        </div>
    </form>
</div>

<!-- RESULTS COUNT -->
<div class="d-flex justify-content-between align-items-center mb-3 text-white">
    <h5 class="fw-bold mb-0"><?= count($rooms); ?> rooms found</h5>
    <span class="text-white-50 small font-monospace">
        <?= htmlspecialchars($search_date); ?> &nbsp; <?= htmlspecialchars($start_time); ?> - <?= htmlspecialchars($end_time); ?>
    </span>
</div>

<!-- ROOM RESULTS GRID -->
<?php if (count($rooms) > 0): ?>

    <div class="row g-4">
        <?php foreach ($rooms as $room): ?>
            <?php
            $roomId      = $room['id'] ?? '';
            $roomName    = $room['room_name'] ?? 'Meeting Room';
            $capacity    = $room['capacity'] ?? 0;
            $location    = $room['location'] ?? 'Location not specified';
            $rawStatus   = strtolower(trim($room['status'] ?? 'available'));
            $isAvailable = ($rawStatus === 'available');
            $rawTags     = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];
            $image       = !empty($room['image']) ? $room['image'] : 'https://placehold.co/800x500?text=Room+Image';
            ?>

            <div class="col-lg-4 col-md-6">
                <div class="card glass-card hover-card shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column">

                    <!-- IMAGE & STATUS -->
                    <div class="position-relative" style="height: 190px;">
                        <img src="<?= htmlspecialchars($image); ?>"
                             alt="<?= htmlspecialchars($roomName); ?>"
                             class="w-100 h-100" style="object-fit: cover;"
                             onerror="this.src='https://placehold.co/800x500?text=Room+Image';">
                        <?php if ($isAvailable): ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-success px-3 py-2 rounded-pill fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Available</span>
                        <?php else: ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-danger px-3 py-2 rounded-pill fw-semibold"><i class="bi bi-x-circle-fill me-1"></i>Occupied</span>
                        <?php endif; ?>
                    </div>

                    <!-- BODY -->
                    <div class="card-body p-4 d-flex flex-column">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($roomName); ?></h5>
                            <span class="text-muted small fw-semibold"><i class="bi bi-people me-1"></i><?= htmlspecialchars($capacity); ?></span>
                        </div>

                        <div class="text-muted small mb-3">
                            <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($location); ?>
                        </div>

                        <div class="d-flex flex-wrap gap-1 mb-4">
                            <?php foreach ($rawTags as $tag): ?>
                                <span class="badge bg-light text-secondary border px-2 py-1 rounded-2 small fw-normal">
                                    <i class="bi <?= getAmenityIcon($tag); ?> me-1"></i> 
                                    <?= htmlspecialchars(trim($tag)); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-auto row g-2">
                            <div class="col-6">
                                <a href="room_details.php?id=<?= urlencode($roomId); ?>" class="btn btn-outline-secondary w-100 fw-semibold text-dark rounded-3 btn-sm py-2">
                                    View Details
                                </a>
                            </div>
                            <div class="col-6">
                                <?php if ($isAvailable): ?>
                                    <a href="book.php?id=<?= urlencode($roomId); ?>&date=<?= urlencode($search_date); ?>&start=<?= urlencode($start_time); ?>&end=<?= urlencode($end_time); ?>" class="btn btn-success w-100 fw-bold rounded-3 btn-sm py-2">
                                        Reserve
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 fw-bold rounded-3 btn-sm py-2" disabled>
                                        Occupied
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

<?php else: ?>

    <div class="card glass-card shadow-sm rounded-4 p-5 text-center my-4">
        <div class="bg-danger bg-opacity-10 text-danger mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 50%;">
            <i class="bi bi-building-x fs-2"></i>
        </div>
        <h4 class="fw-bold text-dark mb-2">No Rooms Found</h4>
        <p class="text-muted mb-0" style="max-width: 500px; margin: 0 auto;">
            We couldn't find any rooms matching your search parameters. Try adjusting your capacity, floor, status, or equipment filters above.
        </p>
    </div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>