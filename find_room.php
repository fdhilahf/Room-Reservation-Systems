<?php
$page_title = "Find a Room - RoomReserve";
include 'includes/header.php';
?>

<div class="mb-4">
    <h1 class="text-white fw-bold mb-1">Find a Room</h1>
    <p class="text-white-50 mb-0">Search and reserve meeting rooms dynamically.</p>
</div>

<!-- AJAX LIVE SEARCH BAR -->
<div class="card glass-card shadow-sm rounded-4 p-4 mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-md-12">
            <label class="form-label text-dark fw-bold small">LIVE SEARCH (AJAX)</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" id="liveSearchInput" class="form-control py-2" placeholder="Type room name, equipment, or location to filter instantly...">
            </div>
        </div>
    </div>
</div>

<!-- CONTAINER DATA AJAX -->
<div id="roomResultsContainer">
    <!-- Results loaded automatically via AJAX -->
</div>

<script>
function loadRooms(keyword = '') {
    fetch('fetch_rooms_ajax.php?keyword=' + encodeURIComponent(keyword))
        .then(response => response.text())
        .then(html => {
            document.getElementById('roomResultsContainer').innerHTML = html;
        });
}

// Muat data kali pertama
document.addEventListener('DOMContentLoaded', () => loadRooms());

// Carian masa nyata (Live Search AJAX)
document.getElementById('liveSearchInput').addEventListener('keyup', function() {
    loadRooms(this.value);
});
</script>

<?php include 'includes/footer.php'; ?>