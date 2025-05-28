<?php
session_start();
require_once('API/connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM movies ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>จัดการหนัง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #eee; }
        .navbar { background-color: #1e1e1e; margin-bottom: 30px; }
        .container { max-width: 1000px; }
        .navbar-nav .nav-link { color: #fff !important; font-weight: bold; font-size: 1.05rem; margin: 0 15px; }
        .navbar-nav .nav-link.active { text-decoration: underline; }
        .navbar-brand { color: #fff !important; margin-right: 40px; font-weight: bold; }
        .table th { background-color: #0f9d58; color: #fff; }
        .table tr td { vertical-align: middle; }
        .navbar .container { display: flex; justify-content: center; align-items: center; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-film"></i> HOME</a>
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link active" href="manage_movies.php">จัดการหนัง</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_bookings.php">จัดการการจอง</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_users.php">จัดการผู้ใช้</a></li>
            <li class="nav-item"><a class="nav-link" href="status.php">สถิติ</a></li> <li class="nav-item"><a class="nav-link" href="logout.php">ออกจากระบบ</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mb-4"><i class="bi bi-gear"></i> จัดการหนัง</h2>
    <a href="add.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> เพิ่มหนังใหม่</a>

    <div class="table-responsive">
        <table class="table table-dark table-bordered table-hover">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อหนัง</th>
                    <th>คำอธิบาย</th>
                    <th>วันที่ออกฉาย</th>
                    <th>วันที่ฉาย</th>
                    <th>เวลาฉาย</th>
                    <th>ราคา</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($movie = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($movie['id']) ?></td>
                            <td><?= htmlspecialchars($movie['title']) ?></td>
                            <td><?= htmlspecialchars($movie['description'] ?? '') ?></td>
                            <td><?= htmlspecialchars($movie['release_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($movie['show_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($movie['show_time'] ?? '') ?></td>
                            <td><?= number_format($movie['price'], 2) ?></td>
                            <td>
                                <a href="API/edit.php?id=<?= $movie['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                                <a href="API/del.php?id=<?= $movie['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('แน่ใจว่าต้องการลบหนังเรื่องนี้?')">ลบ</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">ยังไม่มีข้อมูลหนัง</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>