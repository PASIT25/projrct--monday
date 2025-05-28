<?php
session_start();
require_once('API/connect.php');

// ตรวจสอบสิทธิ์ admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// ลบการจอง (ถ้ามีการส่ง id มา)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_bookings.php");
    exit;
}

// ดึงข้อมูลการจองทั้งหมด
$sql = "SELECT b.id, u.username, m.title AS movie_title, b.show_date, b.show_time, b.seat, b.booked_at 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN movies m ON b.movie_id = m.id
        ORDER BY b.booked_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>จัดการการจอง</title>
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
        .table th {
            background-color: #0f9d58;
            color: #fff;
        }
        .table td {
            vertical-align: middle;
        }
        .container {
            max-width: 1100px;
        }
        .navbar .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php"><i class="bi bi-film"></i>HOME</a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="manage_movies.php">จัดการหนัง</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="manage_bookings.php">จัดการการจอง</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_users.php">จัดการผู้ใช้</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="status.php">สถิติ</a> </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">ออกจากระบบ</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mb-4"><i class="bi bi-journal-bookmark"></i> จัดการการจองทั้งหมด</h2>

    <div class="table-responsive">
        <table class="table table-dark table-bordered table-hover">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>ชื่อหนัง</th>
                    <th>วันที่</th>
                    <th>เวลา</th>
                    <th>ที่นั่ง</th>
                    <th>เวลาที่จอง</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['movie_title']) ?></td>
                            <td><?= htmlspecialchars($row['show_date']) ?></td>
                            <td><?= htmlspecialchars($row['show_time']) ?></td>
                            <td><?= htmlspecialchars($row['seat']) ?></td>
                            <td><?= htmlspecialchars($row['booked_at']) ?></td>
                            <td>
                                <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ลบการจองนี้แน่หรือ?')">
                                    <i class="bi bi-trash"></i> ลบ
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">ยังไม่มีการจอง</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>