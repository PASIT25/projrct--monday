<?php
session_start();
require_once('connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$movie_id = $_GET['id'] ?? null;
if (!$movie_id) {
    header("Location: ../manage_movies.php");
    exit;
}

$errors = [];
$title = '';
$description = '';
$release_date = '';
$show_date = '';
$show_time = '';
$price = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $release_date = $_POST['release_date'] ?? '';
    $show_date = $_POST['show_date'] ?? '';
    $show_time = $_POST['show_time'] ?? '';
    $price = $_POST['price'] ?? 0;

    if (empty($title)) {
        $errors[] = "กรุณากรอกชื่อหนัง";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE movies SET title=?, description=?, release_date=?, show_date=?, show_time=?, price=? WHERE id=?");
        $stmt->bind_param("sssssdi", $title, $description, $release_date, $show_date, $show_time, $price, $movie_id);
        
        if ($stmt->execute()) {
            header("Location: ../manage_movies.php");
            exit;
        } else {
            $errors[] = "เกิดข้อผิดพลาดในการอัปเดตหนัง";
        }
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $movie = $result->fetch_assoc();

    if (!$movie) {
        header("Location: ../manage_movies.php");
        exit;
    }

    $title = $movie['title'];
    $description = $movie['description'];
    $release_date = $movie['release_date'];
    $show_date = $movie['show_date'];
    $show_time = $movie['show_time'];
    $price = $movie['price'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>แก้ไขหนัง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-dark text-white">

<div class="container mt-5" style="max-width: 600px;">
    <h2 class="mb-4">แก้ไขหนัง</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label>ชื่อหนัง</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="mb-3">
            <label>คำอธิบาย</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($description) ?></textarea>
        </div>

        <div class="mb-3">
            <label>วันที่ออกฉาย (release_date)</label>
            <input type="date" name="release_date" class="form-control" value="<?= htmlspecialchars($release_date) ?>">
        </div>

        <div class="mb-3">
            <label>วันที่ฉาย (show_date)</label>
            <input type="date" name="show_date" class="form-control" value="<?= htmlspecialchars($show_date) ?>">
        </div>

        <div class="mb-3">
            <label>เวลาฉาย (show_time)</label>
            <input type="time" name="show_time" class="form-control" value="<?= htmlspecialchars($show_time) ?>">
        </div>

        <div class="mb-3">
            <label>ราคา</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($price) ?>">
        </div>

        <button type="submit" class="btn btn-success">บันทึก</button>
        <a href="../manage_movies.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
</body>
</html>