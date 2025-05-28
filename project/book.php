<?php
session_start();
require_once('API/connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$movie_id = $_GET['movie_id'] ?? null;
if (!$movie_id) {
    header("Location: booking.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ดึงวันที่และเวลาที่เลือก
$show_date = $_GET['show_date'] ?? date('Y-m-d');
$show_time = $_GET['show_time'] ?? ''; // ให้ค่าเริ่มต้นเป็นว่าง

// ดึงข้อมูลที่นั่งที่ถูกจองจากฐานข้อมูล (แก้ไข query)
$bookedSeats = [];
if ($show_time != '') { // ดึงข้อมูลเฉพาะเมื่อมีการเลือกเวลา
    $bookedStmt = $conn->prepare("SELECT seat FROM bookings WHERE movie_id = ? AND show_date = ? AND show_time = ?");
    $bookedStmt->bind_param("iss", $movie_id, $show_date, $show_time);
    $bookedStmt->execute();
    $bookedResult = $bookedStmt->get_result();
    while ($row = $bookedResult->fetch_assoc()) {
        $bookedSeats[] = $row['seat'];
    }
    $bookedStmt->close();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จองที่นั่ง - <?=htmlspecialchars($movie['title'])?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .seat {
            width: 40px;
            height: 40px;
            margin: 5px;
            background-color: #444;
            border-radius: 5px;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .seat.selected {
            background-color: #28a745;
        }
        .seat.booked {
            background-color: #888;
            cursor: not-allowed;
            opacity: 0.6; /* ทำให้ดูจางลง */
        }
        .seat-row {
            display: flex;
            justify-content: center;
        }
        label.seat input {
            display: none;
        }
        .seat .booked-icon {
            /* Style สำหรับไอคอนคน (ถ้าใช้) */
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container py-5">
        <h2>จองที่นั่ง - <?=htmlspecialchars($movie['title'])?></h2>
        <form method="post" action="process_booking.php" class="text-white">
            <input type="hidden" name="movie_id" value="<?= $movie_id ?>">

            <div class="mb-3">
                <label for="show_date" class="form-label">วันที่เดินทาง</label>
                <input type="date" id="show_date" name="show_date" class="form-control" required
                    min="<?= date('Y-m-d') ?>" value="<?= $show_date ?>" onchange="updateSeats()" />
            </div>

            <div class="mb-3">
                <label for="show_time" class="form-label">เวลา</label>
                <select id="show_time" name="show_time" class="form-select" required onchange="updateSeats()">
                    <option value="">-- กรุณาเลือกเวลา --</option>
                    <option value="10:00" <?= $show_time == '10:00' ? 'selected' : '' ?>>10:00 น.</option>
                    <option value="13:00" <?= $show_time == '13:00' ? 'selected' : '' ?>>13:00 น.</option>
                    <option value="16:00" <?= $show_time == '16:00' ? 'selected' : '' ?>>16:00 น.</option>
                    <option value="19:00" <?= $show_time == '19:00' ? 'selected' : '' ?>>19:00 น.</option>
                    <option value="22:00" <?= $show_time == '22:00' ? 'selected' : '' ?>>22:00 น.</option>
                </select>
            </div>

            <div class="my-4 text-center" id="seat-container">
                <?php for ($row = 1; $row <= 5; $row++): ?>
                    <div class="seat-row">
                        <?php for ($col = 1; $col <= 8; $col++):
                            $seat = chr(64 + $row) . $col;
                            $seatClass = in_array($seat, $bookedSeats) ? 'seat booked' : 'seat';
                            ?>
                            <label class="<?= $seatClass ?>" title="ที่นั่ง <?= $seat ?>">
                                <input type="checkbox" name="seats[]" value="<?= $seat ?>" <?= in_array($seat, $bookedSeats) ? 'disabled' : '' ?>>
                                <?= $seat ?>
                                <?php if (in_array($seat, $bookedSeats)): ?>
                                    <div class="booked-icon">👤</div>
                                <?php endif; ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success">ยืนยันการจอง</button>
                <a href="index.php" class="btn btn-secondary">ย้อนกลับ</a>
            </div>
        </form>
    </div>

    <script>
        const seatContainer = document.getElementById('seat-container');
        const showDateSelect = document.getElementById('show_date');
        const showTimeSelect = document.getElementById('show_time');

        function updateSeats() {
            const selectedDate = showDateSelect.value;
            const selectedTime = showTimeSelect.value;

            // สร้าง URL ใหม่พร้อมกับวันที่และเวลาที่เลือก
            const newUrl = `book.php?movie_id=<?= $movie_id ?>&show_date=${selectedDate}&show_time=${selectedTime}`;
            window.location.href = newUrl; // Redirect ไปยัง URL ใหม่ (คุณอาจใช้ AJAX แทนได้)
        }

        // Event listener สำหรับการเลือกที่นั่ง (เฉพาะที่นั่งที่ว่าง)
        seatContainer.querySelectorAll('.seat:not(.booked)').forEach(seat => {
            seat.addEventListener('click', function(e) {
                e.preventDefault();
                const input = seat.querySelector('input');
                input.checked = !input.checked;
                seat.classList.toggle('selected');
            });
        });
    </script>
</body>
</html>