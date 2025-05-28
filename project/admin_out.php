<?php
session_start();
session_destroy();
header('Location: login.php'); // หรือ admin_login.php แล้วแต่ระบบ
exit;
