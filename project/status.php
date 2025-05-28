<?php
session_start();
require_once('API/connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// จำนวนผู้ใช้ (ยกเว้นแอดมิน)
$res = $conn->query("SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'");
$total_users = $res->fetch_assoc()['total_users'] ?? 0;

// จำนวนการจอง
$res = $conn->query("SELECT COUNT(*) AS total_bookings FROM bookings");
$total_bookings = $res->fetch_assoc()['total_bookings'] ?? 0;

// รายได้รวมโดยคิดจาก seat_type
$res = $conn->query("SELECT seat_type, COUNT(*) AS count FROM bookings GROUP BY seat_type");

$total_revenue = 0;
$seat_prices = [
    'ปกติ' => 120,
    'VIP' => 180,
    'Deluxe' => 250
];

while ($row = $res->fetch_assoc()) {
    $price = $seat_prices[$row['seat_type']] ?? 0;
    $total_revenue += $row['count'] * $price;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานสถิติ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #eee; }
        .navbar { background-color: #1e1e1e; margin-bottom: 30px; }
        .navbar-nav .nav-link { color: #ffffff !important; font-weight: bold; font-size: 1.05rem; margin: 0 15px; }
        .navbar-nav .nav-link.active { text-decoration: underline; }
        .navbar-brand { color: #ffffff !important; margin-right: auto; font-weight: bold; } /* ปรับ margin-right เป็น auto เพื่อผลักไปทางซ้าย */
        .navbar-collapse {
            justify-content: center; /* จัดตรงกลาง navbar-collapse */
        }
        .navbar-nav {
            margin-left: auto; /* ผลักรายการเมนูไปทางขวา */
            margin-right: auto; /* ผลักรายการเมนูไปทางซ้าย */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-film"></i> HOME</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="manage_movies.php">จัดการหนัง</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_bookings.php">จัดการการจอง</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php">จัดการผู้ใช้</a></li>
                <li class="nav-item"><a class="nav-link active" href="status.php">สถิติ</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">ออกจากระบบ</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container text-center">
    <h2 class="mb-4"><i class="bi bi-bar-chart-line-fill"></i> รายงานสถิติ</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">ผู้ใช้ทั้งหมด</h5>
                    <h3><?= $total_users ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">ยอดการจอง</h5>
                    <h3><?= $total_bookings ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">รายได้รวม</h5>
                    <h3><?= number_format($total_revenue, 2) ?> บาท</h3>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>