<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

require_once('API/connect.php');
$admin_username = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แผงควบคุมแอดมิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #eee;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #1e1e1e;
            justify-content: center;
        }
        .navbar-nav {
            flex-direction: row;
            gap: 20px;
        }
        .nav-link {
            color: #fff !important;
            font-weight: bold;
            font-size: 18px;
        }
        .nav-link:hover {
            color: #0f9d58 !important;
        }
        h2 {
            color: #0f9d58;
            margin-top: 30px;
            font-weight: bold;
            text-align: center;
        }
        .list-group-item {
            background-color: #1e1e1e;
            color: #eee;
            border: 1px solid #0f9d58;
            font-size: 18px;
            font-weight: bold;
        }
        .list-group-item:hover {
            background-color: #0f9d58;
            color: #fff;
        }
    </style>
</head>
<body>

<!-- Navbar ตรงกลาง -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container justify-content-center">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="index.php"><i class="bi bi-house-door-fill"></i> หน้าหลัก</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
            </li>
        </ul>
    </div>
</nav>

<!-- เนื้อหา -->
<div class="container mt-5">
    <h2><i class="bi bi-person-workspace"></i> แผงควบคุมแอดมิน</h2>
    <p class="text-center">ยินดีต้อนรับ, <strong><?= htmlspecialchars($admin_username) ?></strong></p>

    <div class="list-group mt-4 mx-auto" style="max-width: 600px;">
        <a href="manage_movies.php" class="list-group-item list-group-item-action"><i class="bi bi-film"></i> จัดการหนัง</a>
        <a href="manage_bookings.php" class="list-group-item list-group-item-action"><i class="bi bi-list-check"></i> ดูรายการจอง</a>
        <a href="status.php" class="list-group-item list-group-item-action"><i class="bi bi-bar-chart-fill"></i> ดูสถิติ</a>
        <a href="manage_users.php" class="list-group-item list-group-item-action"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
