<?php
/**
 * logout.php — ออกจากระบบ
 * หน้าที่:
 * - เคลียร์ข้อมูล Session ทั้งหมด
 * - พาผู้ใช้กลับไปยังหน้า Login
 */
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
?>
