<?php
session_start();
require_once('API/connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ตรวจสอบว่ามีข้อมูลถูกส่งมาหรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "วิธีการเข้าถึงไม่ถูกต้อง กรุณาใช้ POST";
    exit;
}

$movie_id = $_POST['movie_id'] ?? null;
$show_date = $_POST['show_date'] ?? null;
$show_time = $_POST['show_time'] ?? null;
// $seat_type = $_POST['seat_type'] ?? null; // ลบออก
$seats = $_POST['seats'] ?? [];

// ตรวจสอบข้อมูลให้ครบถ้วนและถูกต้อง (ลบ seat_type)
if (!$movie_id) {
    echo "ข้อผิดพลาด: ไม่พบรหัสภาพยนตร์";
    exit;
}
if (!$show_date) {
    echo "ข้อผิดพลาด: ไม่พบวันที่ฉาย";
    exit;
}
if (!$show_time) {
    echo "ข้อผิดพลาด: ไม่พบเวลาฉาย";
    exit;
}
// if (!$seat_type) { // ลบออก
//     echo "ข้อผิดพลาด: ไม่พบประเภทที่นั่ง";
//     exit;
// }
if (empty($seats)) {
    echo "ข้อผิดพลาด: ไม่พบที่นั่งที่เลือก";
    exit;
}

// เริ่ม transaction (เพื่อ rollback ในกรณีเกิดข้อผิดพลาด)
$conn->begin_transaction();

// แก้ไข query และ bind_param (ลบ seat_type)
$stmt = $conn->prepare("INSERT INTO bookings (user_id, movie_id, seat, show_date, show_time) VALUES (?, ?, ?, ?, ?)");

if (!$stmt) {
    echo "ข้อผิดพลาดในการเตรียม query: " . $conn->error;
    $conn->rollback();
    exit;
}

foreach ($seats as $seat) {
    // แก้ไข bind_param (ลบ seat_type)
    $stmt->bind_param("iisss", $user_id, $movie_id, $seat, $show_date, $show_time);
    $result = $stmt->execute();

    if (!$result) {
        echo "ข้อผิดพลาดในการบันทึกที่นั่ง $seat: " . $stmt->error;
        $conn->rollback();
        exit;
    }
}

// commit transaction ถ้าทุกอย่างสำเร็จ
$conn->commit();

$stmt->close();
$conn->close();

// หลังจองเสร็จให้ redirect ไปหน้าดูประวัติการจองหรือหน้าหลัก
header("Location: history.php");
exit;
?>