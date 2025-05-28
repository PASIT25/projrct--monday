<?php
session_start();
require_once('connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../manage_movies.php");
    exit;
}

$movie_id = intval($_GET['id']);

// ลบ booking ที่เกี่ยวข้องก่อน
$stmt = $conn->prepare("DELETE FROM bookings WHERE movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();

// ลบหนัง
$stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();

header("Location: ../manage_movies.php");
exit;
