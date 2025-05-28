<?php
session_start();
require_once('API/connect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$search = $_GET['search'] ?? '';
$stmt = $conn->prepare("SELECT id, title, poster, show_date, show_time FROM movies WHERE title LIKE ? ORDER BY show_date ASC");
$likeSearch = "%$search%";
$stmt->bind_param("s", $likeSearch);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>หน้าแรก - MovieBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .navbar-custom {
            background-color: #000000;
        }
        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand,
        .navbar-custom .navbar-text,
        .navbar-custom .btn-outline-light {
            color: #ffffff;
        }
        .navbar-custom .nav-link:hover {
            color: #1db954;
        }
        .profile-icon {
            font-size: 1.8rem;
            color: #1db954;
            margin-right: 8px;
        }
        .movie-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        .movie-card:hover {
            transform: translateY(-5px);
        }
        .movie-poster {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body class="bg-dark text-white">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php">DarkFrame</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="history.php"><i class="bi bi-clock-history"></i> ประวัติการจองของฉัน</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i> แผงควบคุมแอดมิน</a></li>
                <?php endif; ?>
            </ul>

            <span class="navbar-text d-flex align-items-center me-3">
                <i class="bi bi-person-circle profile-icon"></i> <?=htmlspecialchars($_SESSION['username'])?>
            </span>
            <a href="logout.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
        </div>
    </div>
</nav>

<!-- Search -->
<div class="container mt-4">
    <form method="get" class="d-flex mb-4" style="max-width: 400px;">
        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="ค้นหาชื่อหนัง..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-success btn-sm" type="submit"><i class="bi bi-search"></i></button>
    </form>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($movie = $result->fetch_assoc()): ?>
                <?php
                    $movie_id = $movie['id'];
                    $total_seats = 50; // กำหนดจำนวนที่นั่งรวมตรงนี้ หรือใช้จาก DB ถ้ามี column
                    $booked_sql = "SELECT COUNT(*) AS total_booked FROM bookings WHERE movie_id = ?";
                    $booked_stmt = $conn->prepare($booked_sql);
                    $booked_stmt->bind_param("i", $movie_id);
                    $booked_stmt->execute();
                    $booked_result = $booked_stmt->get_result()->fetch_assoc();
                    $booked = $booked_result['total_booked'] ?? 0;
                    $available = $total_seats - $booked;
                ?>
                <div class="col-md-4">
                    <div class="card movie-card bg-secondary text-white">
                        <img src="uploads/<?= htmlspecialchars($movie['poster']) ?>" alt="โปสเตอร์" class="movie-poster">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($movie['title']) ?></h5>
                            <p class="card-text">
                                📅 <?= htmlspecialchars($movie['show_date']) ?> <br>
                                ⏰ <?= htmlspecialchars($movie['show_time']) ?> <br>
                                🎟 คงเหลือ: <?= $available ?> / <?= $total_seats ?> ที่นั่ง
                            </p>
                            <a href="book.php?movie_id=<?= $movie['id'] ?>" class="btn btn-success w-100" <?= $available <= 0 ? 'disabled' : '' ?>>
                                <?= $available <= 0 ? 'เต็มแล้ว' : 'จองตั๋ว' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">ไม่พบหนังที่ค้นหา</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
