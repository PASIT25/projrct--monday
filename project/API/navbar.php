<?php
if (!isset($_SESSION)) session_start();
?>

<nav class="navbar navbar-expand-lg navbar-custom bg-secondary">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/logo.png" alt="Logo" width="40" height="40" class="me-2">
            <span class="text-white fw-bold">MovieBooking</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link text-white" href="booking.php"><i class="bi bi-ticket-perforated-fill"></i> จองตั๋วหนัง</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="history.php"><i class="bi bi-clock-history"></i> ประวัติการจองของฉัน</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link text-white" href="admin.php"><i class="bi bi-speedometer2"></i> แผงควบคุมแอดมิน</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text d-flex align-items-center me-3 text-white">
                <div class="profile-icon d-flex justify-content-center align-items-center me-2">
                    <i class="bi bi-person-fill"></i>
                </div>
                <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
        </div>
    </div>
</nav>
