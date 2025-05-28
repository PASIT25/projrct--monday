<?php
session_start();
require_once('API/connect.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // ตรวจสอบข้อมูล
    if (!$username || !$email || !$password || !$confirm_password) {
        $errors[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }
    if ($password !== $confirm_password) {
        $errors[] = "รหัสผ่านไม่ตรงกัน";
    }
    if (strlen($password) < 6) {
        $errors[] = "รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร";
    }

    // ตรวจสอบ username หรือ email ซ้ำ
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "ชื่อผู้ใช้หรืออีเมลนี้มีคนใช้แล้ว";
        }
        $stmt->close();
    }

    // บันทึกข้อมูลถ้าไม่มี error
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
        $stmt->bind_param("sss", $username, $email, $hash);
        if ($stmt->execute()) {
            $_SESSION['success'] = "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "เกิดข้อผิดพลาดในการสมัครสมาชิก";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background: #121212;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #eee;
        }
        .register-box {
            max-width: 450px;
            margin: 60px auto;
            background: #1e1e1e;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,255,128,0.5);
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
        .input-group-text {
            background: #0f9d58;
            border: none;
            color: #fff;
            border-radius: 10px 0 0 10px;
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
        .btn-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #0f9d58;
        }
        .btn-link:hover {
            color: #0b6d3a;
            text-decoration: none;
        }
        .alert-danger {
            background-color: #8b0000;
            color: #fff;
            border-radius: 10px;
        }
        .alert-success {
            background-color: #0f9d58;
            color: #fff;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>สมัครสมาชิก</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?=htmlspecialchars($e)?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php" novalidate>
            <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                    <input type="text" name="username" class="form-control" value="<?=htmlspecialchars($_POST['username'] ?? '')?>" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">ยืนยันรหัสผ่าน</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
            <a href="login.php" class="btn btn-link">เข้าสู่ระบบ</a>
        </form>
    </div>
</body>
</html>
