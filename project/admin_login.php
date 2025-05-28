<?php
session_start();
require_once('API/connect.php'); // ตามโฟลเดอร์ของมึง

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $errors[] = "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($user['role'] !== 'admin') {
                $errors[] = "บัญชีนี้ไม่ใช่แอดมิน";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header('Location: admin.php');
                exit;
            } else {
                $errors[] = "รหัสผ่านไม่ถูกต้อง";
            }
        } else {
            $errors[] = "ไม่พบชื่อผู้ใช้นี้";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เข้าสู่ระบบแอดมิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background: #121212;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #eee;
        }
        .admin-login-box {
            max-width: 450px;
            margin: 60px auto;
            background: #1e1e1e;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,255,128,0.4);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: #0f9d58;
        }
        .form-control {
            border-radius: 10px;
            background: #2c2c2c;
            border: 1px solid #0f9d58;
            color: #eee;
        }
        .form-control:focus {
            background: #1e1e1e;
            border-color: #0f9d58;
            box-shadow: 0 0 8px #0f9d58;
            color: #fff;
        }
        .btn-primary {
            width: 100%;
            border-radius: 10px;
            font-weight: bold;
            background-color: #0f9d58;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b6d3a;
        }
        .alert-danger {
            background-color: #8b0000;
            color: #fff;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-login-box">
        <h2>เข้าสู่ระบบแอดมิน</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="admin_login.php" novalidate>
            <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้</label>
                <div class="input-group">
                    <span class="input-group-text bg-success text-white"><i class="bi bi-person-circle"></i></span>
                    <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <div class="input-group">
                    <span class="input-group-text bg-success text-white"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>
