<?php
session_start();
require_once('API/connect.php');

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
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
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
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #121212;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #eee;
        }
        .login-box {
            max-width: 400px;
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
    <div class="login-box">
        <h2>เข้าสู่ระบบ</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>

            <!-- สมัครสมาชิก + ลืมรหัสผ่าน อยู่ในแถวเดียวกัน -->
            <div class="d-flex justify-content-between mt-3">
                <a href="register.php" class="btn btn-link p-0">สมัครสมาชิก</a>
                <a href="forgot_password.php" class="btn btn-link p-0">ลืมรหัสผ่าน?</a>
            </div>
        </form>
    </div>
</body>
</html>
