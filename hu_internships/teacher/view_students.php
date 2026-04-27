<?php
/**
 * teacher/view_students.php — รายชื่อนิสิตในความดูแล
 * หน้าที่:
 * - แสดงรายชื่อนิสิตที่อาจารย์คนนั้นๆ เป็นอาจารย์นิเทศ (Advisor)
 * - ดูสถานะคำขอฝึกงานของนิสิตแต่ละคน
 * - เข้าไปจัดการ/อัปเดตสถานะได้ผ่านลิงก์ไปยัง update_status.php
 */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();
checkRole('teacher');

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

// ดึงคำร้องทั้งหมดที่อาจารย์คนนี้เป็นที่ปรึกษา
$stmt = $conn->prepare("
    SELECT r.*, u.fullname, si.major 
    FROM internship_requests r 
    JOIN users u ON r.student_id = u.username 
    LEFT JOIN students_info si ON r.student_id = si.student_id
    WHERE r.advisor_id = ? 
    ORDER BY r.request_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = $t['students_in_care'] ?? 'นิสิตในความดูแล';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            <h2 style="font-size: 2rem; margin: 0;"><?= $page_title ?></h2>
            <a href="../dashboard.php" class="btn" style="border: 1px solid var(--border-color); color: var(--text-color);">&larr; <?= $t['back'] ?? 'ย้อนกลับ' ?></a>
        </div>
        <table style="margin-top:20px; width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;"><?= $t['student'] ?? 'นิสิต' ?></th>
                    <th style="padding: 10px;"><?= $t['company_name'] ?? 'สถานประกอบการ' ?></th>
                    <th style="padding: 10px;"><?= $t['status'] ?? 'สถานะ' ?></th>
                    <th style="padding: 10px;"><?= $t['date'] ?? 'วันที่ยื่น' ?></th>
                    <th style="padding: 10px;"><?= $t['action'] ?? 'จัดการ' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($requests) > 0): ?>
                    <?php foreach($requests as $req): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; opacity: 0.7; font-size: 0.9rem;">#<?= htmlspecialchars($req['request_id']) ?></td>
                        <td style="padding: 10px;">
                            <strong><?= htmlspecialchars($req['fullname']) ?></strong> <br>
                            <span style="font-size: 0.85rem; opacity: 0.7;"><?= htmlspecialchars($req['student_id']) ?> <?= htmlspecialchars($req['major'] ? '('.$req['major'].')' : '') ?></span>
                        </td>
                        <td style="padding: 10px;"><strong><?= htmlspecialchars($req['company_name']) ?></strong></td>
                        <?php
                        $statusLabels = [
                            0 => $t['status_pending_staff'] ?? 'รอเจ้าหน้าที่รับเรื่อง',
                            1 => $t['status_pending'] ?? 'รับเรื่องเข้าระบบ',
                            2 => $t['status_approved'] ?? 'อนุมัติแล้ว',
                            3 => $t['status_doc_issued'] ?? 'ออกใบส่งตัว',
                            4 => $t['status_completed'] ?? 'เสร็จสิ้น',
                            9 => $t['status_canceled'] ?? 'ยกเลิก/ให้แก้ไข'
                        ];
                        $statusClasses = [
                            0 => 'pending', 
                            1 => 'pending', 
                            2 => 'approved', 
                            3 => 'approved', 
                            4 => 'approved', 
                            9 => 'rejected'
                        ];
                        $sLabel = $statusLabels[$req['status']] ?? 'Unknown';
                        $sClass = $statusClasses[$req['status']] ?? 'pending';
                        ?>
                        <td style="padding: 10px;"><span class="badge badge-<?= $sClass ?>"><?= $sLabel ?></span></td>
                        <td style="padding: 10px;"><?= date('d M Y', strtotime($req['request_date'])) ?></td>
                        <td style="padding: 10px;">
                            <a href="../staff/update_status.php?id=<?= $req['request_id'] ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;"><?= $t['update_btn'] ?? 'ตรวจสอบ / ประเมิน' ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; opacity: 0.6;">ยังไม่มีนิสิตในความดูแล</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
