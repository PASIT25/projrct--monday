<?php
session_start();
require_once('API/connect.php');

// ตรวจสอบว่าเป็นแอดมินหรือไม่
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// ถ้ามีการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $release_date = $_POST['release_date'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $total_seats = intval($_POST['total_seats']);

    // จัดการอัปโหลดไฟล์
    $poster_name = "";
    if ($_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $poster_name = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['poster']['tmp_name'], "uploads/" . $poster_name);
    }

    // เพิ่มหนังลงฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO movies (title, description, release_date, show_date, show_time, poster, total_seats) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $title, $description, $release_date, $show_date, $show_time, $poster_name, $total_seats);
    $stmt->execute();

    header("Location: manage_movies.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มหนังใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container py-5">
        <h1 class="mb-4">เพิ่มหนังใหม่</h1>
        <form method="post" enctype="multipart/form-data" class="bg-secondary p-4 rounded">
            <div class="mb-3">
                <label class="form-label">ชื่อหนัง</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">คำอธิบาย</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">วันที่เข้าฉาย (release)</label>
                <input type="date" name="release_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">วันที่ฉายจริง</label>
                <input type="date" name="show_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">เวลา</label>
                <input type="time" name="show_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">จำนวนที่นั่งทั้งหมด</label>
                <input type="number" name="total_seats" class="form-control" required min="1">
            </div>
            <div class="mb-3">
                <label class="form-label">อัปโหลดโปสเตอร์</label>
                <input type="file" name="poster" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="manage_movies.php" class="btn btn-light">กลับ</a>
        </form>
    </div>
</body>
</html>
