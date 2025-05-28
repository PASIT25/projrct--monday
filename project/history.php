<?php
session_start();
require_once('API/connect.php');

// ตรวจสอบการ login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลการจองของผู้ใช้
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT m.title AS movie_title, b.seat, b.show_date, b.show_time, b.seat_type, b.booked_at
                        FROM bookings b
                        JOIN movies m ON b.movie_id = m.id
                        WHERE b.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการจอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #eee;
        }
        .navbar {
            background-color: #1e1e1e;
            margin-bottom: 30px;
        }
        .container {
            max-width: 900px;
        }
        .navbar-nav .nav-link {
            color: #ffffff !important;
            font-weight: bold;
            font-size: 1.05rem;
            margin: 0 15px;
        }
        .navbar-nav .nav-link.active {
            text-decoration: underline;
        }
        .navbar-brand {
            color: #ffffff !important;
            margin-right: 40px;
            font-weight: bold;
        }
        .table {
            background-color: #1e1e1e;
            color: #eee;
        }
        .table th {
            background-color: #0f9d58;
            color: #fff;
        }
        .table tr td {
            vertical-align: middle;
        }
        .navbar .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>

<!-- เมนู -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-film"></i> ระบบจองตั๋วหนัง</a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">หน้าหลัก</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="history.php">ประวัติการจอง</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">ออกจากระบบ</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mb-4"><i class="bi bi-clock-history"></i> ประวัติการจองของคุณ</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover rounded shadow-sm">
                <thead>
                    <tr>
                        <th>ชื่อหนัง</th>
                        <th>ที่นั่ง</th>
                        <th>วันที่ฉาย</th>
                        <th>เวลา</th>
                        <th>ประเภทที่นั่ง</th>
                        <th>เวลาที่จอง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['movie_title']) ?></td>
                            <td><?= htmlspecialchars($row['seat']) ?></td>
                            <td><?= htmlspecialchars($row['show_date']) ?></td>
                            <td><?= htmlspecialchars($row['show_time']) ?></td>
                            <td><?= htmlspecialchars($row['seat_type']) ?></td>
                            <td><?= htmlspecialchars($row['booked_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">ยังไม่มีประวัติการจอง</div>
    <?php endif; ?>
</div>
</body>
</html>
