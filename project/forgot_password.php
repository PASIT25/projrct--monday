<?php
session_start();
require_once('API/connect.php');

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // เช็คข้อมูล
    if (!$username || !$new_password || !$confirm_password) {
        $errors[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "รหัสผ่านใหม่ไม่ตรงกัน";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร";
    } else {
        // ตรวจสอบ username มีในระบบไหม
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // อัปเดตรหัสผ่าน
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt2->bind_param("si", $hash, $user_id);

            if ($stmt2->execute()) {
                $success = "เปลี่ยนรหัสผ่านสำเร็จ! กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่";
            } else {
                $errors[] = "เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน";
            }
            $stmt2->close();
        } else {
            $errors[] = "ไม่พบชื่อผู้ใช้นี้ในระบบ";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เปลี่ยนรหัสผ่าน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #121212;
            color: #eee;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .forgot-box {
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
            color: #0f9d58;
            font-weight: bold;
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
            background-color: #0f9d58;
            border: none;
            font-weight: bold;
        }
        .btn-primary:hover {
            background-color: #0b6d3a;
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
        a.btn-link {
            color: #0f9d58;
            text-decoration: none;
        }
        a.btn-link:hover {
            color: #0b6d3a;
        }
    </style>
</head>
<body>
    <div class="forgot-box">
        <h2>เปลี่ยนรหัสผ่าน</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?=htmlspecialchars($e)?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <div class="mt-3 text-center">
                <a href="login.php" class="btn btn-link">กลับไปหน้าเข้าสู่ระบบ</a>
            </div>
        <?php else: ?>
            <form method="post" action="forgot_password.php">
                <div class="mb-3">
                    <label class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" name="username" class="form-control" value="<?=htmlspecialchars($_POST['username'] ?? '')?>" autofocus required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่านใหม่</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
            </form>
        <?php endif; ?>

        <div class="mt-3 text-center">
            <a href="login.php" class="btn btn-link">กลับไปหน้าเข้าสู่ระบบ</a>
        </div>
    </div>
</body>
</html>
