<?php
session_start();
require_once('connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("Location: ../manage_users.php");
    exit;
}

$errors = [];
$username = '';
$email = '';
$is_admin = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $role = $is_admin ? 'admin' : 'user';  // ปรับตามค่าที่เก็บใน DB

    if (empty($username)) {
        $errors[] = "กรุณากรอกชื่อผู้ใช้";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sssi", $username, $email, $role, $user_id);
        $stmt->execute();

        header("Location: ../manage_users.php");
        exit;
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        header("Location: ../manage_users.php");
        exit;
    }

    $username = $user['username'];
    $email = $user['email'];
    $is_admin = ($user['role'] ?? 'user') === 'admin' ? 1 : 0;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">

<div class="container mt-5" style="max-width: 600px;">
    <h2 class="mb-4">แก้ไขผู้ใช้</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>ชื่อผู้ใช้</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username) ?>" required>
        </div>

        <div class="mb-3">
            <label>อีเมล</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>">
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" <?= $is_admin ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_admin">กำหนดให้เป็นแอดมิน</label>
        </div>

        <button type="submit" class="btn btn-success">บันทึก</button>
        <a href="../manage_users.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>

</body>
</html>
