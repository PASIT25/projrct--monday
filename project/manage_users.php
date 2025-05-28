<?php
session_start();
require_once('API/connect.php');

// เช็คว่าเป็นแอดมินไหม
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// ลบผู้ใช้ (ถ้ามีการส่ง delete_id)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // ลบการจองของผู้ใช้นั้นก่อน (หากมี)
    $conn->prepare("DELETE FROM bookings WHERE user_id = ?")->execute([$delete_id]);

    // ลบผู้ใช้
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: manage_users.php");
    exit;
}

// ดึงข้อมูลผู้ใช้ทั้งหมด
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
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
            max-width: 1000px;
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
        <a class="navbar-brand" href="index.php"><i class="bi bi-film"></i>HOME</a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="manage_movies.php">จัดการหนัง</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_bookings.php">จัดการการจอง</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="manage_users.php">จัดการผู้ใช้</a>
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
    <h2 class="text-center mb-4"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</h2>

    <div class="table-responsive">
        <table class="table table-dark table-bordered table-hover">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>อีเมล</th>
                    <th>วันที่สมัคร</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($user['created_at'] ?? '-') ?></td>
                            <td><?= ($user['is_admin'] ?? 0) ? 'แอดมิน' : 'ผู้ใช้' ?></td>
                            <td>
                                <a href="API/edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> แก้ไข
                                </a>
                                <a href="?delete_id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ลบผู้ใช้นี้แน่หรือ?')">
                                    <i class="bi bi-trash"></i> ลบ
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">ไม่มีข้อมูลผู้ใช้</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>