<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

checkLogin();

// อนุญาตทั้ง staff และ teacher ให้สามารถแก้ไขสถานะได้
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../dashboard.php");
    exit;
}

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

if (!isset($_GET['id'])) {
    header("Location: " . ($_SESSION['role'] === 'staff' ? "view_all.php" : "../dashboard.php"));
    exit;
}

$id = $_GET['id'];
$msg = '';

$stmt = $conn->prepare("
    SELECT r.*, u.fullname, adv.fullname AS advisor_name 
    FROM internship_requests r 
    JOIN users u ON r.student_id = u.username 
    LEFT JOIN users adv ON r.advisor_id = adv.username 
    WHERE r.request_id = ?
");
$stmt->execute([$id]);
$req = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$req) {
    echo $t['no_requests_found'] ?? "ไม่พบคำขอที่ระบุ (Request not found.)";
    exit;
}

// ตรวจสอบสิทธิ์: ถ้าเป็นอาจารย์ ต้องประเมินเฉพาะนิสิตในความดูแลเท่านั้น
if ($_SESSION['role'] === 'teacher' && $req['advisor_id'] !== $_SESSION['user_id']) {
    echo "<div style='padding: 2rem; text-align: center; color: red; font-size: 1.2rem; font-weight: bold;'>" . ($t['access_denied'] ?? 'ไม่มีสิทธิ์ในการแก้ไขคำขอนี้ (Access Denied)') . "</div>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = (int)$_POST['status'];
    
    $update = $conn->prepare("UPDATE internship_requests SET status = ? WHERE request_id = ?");
    if($update->execute([$status, $id])) {
        $msg = "<div class='alert alert-success' style='background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 10px; border-radius: 4px; border-left: 4px solid #22c55e; margin-bottom: 1rem;'>" . ($t['status_updated_success'] ?? 'อัปเดตสถานะสำเร็จ!') . "</div>";
        // Update $req status after successful update
        $req['status'] = $status;
    }
}

$page_title = 'Update Status';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <div class="card" style="max-width: 600px; margin: 2rem auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            <h2 style="font-size: 2rem; margin: 0;"><?= $t['manage_request_title'] ?></h2>
            <a href="<?= $_SESSION['role'] === 'staff' ? 'view_all.php' : '../dashboard.php' ?>" class="btn" style="border: 1px solid var(--border-color); color: var(--text-color);">&larr; <?= $t['back'] ?></a>
        </div>
        
        <!-- ส่วนแสดงรายละเอียดคำขอ -->
        <div style="background: var(--input-bg); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid var(--border-color); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;"><?= $t['request_details_title'] ?></h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                
                <!-- ข้อมูลนิสิต -->
                <div>
                    <h4 style="font-size: 0.9rem; color: var(--text-color); opacity: 0.6; margin-bottom: 0.3rem;"><?= $t['student_info'] ?></h4>
                    <p style="margin: 0; font-size: 1.05rem;"><strong><?= htmlspecialchars($req['fullname']) ?></strong> (<?= htmlspecialchars($req['student_id']) ?>)</p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8;"><?= $t['role_teacher'] ?>: <?= htmlspecialchars($req['advisor_name'] ?? $t['no_advisor']) ?></p>
                </div>

                <!-- ข้อมูลการฝึกงาน -->
                <div>
                    <h4 style="font-size: 0.9rem; color: var(--text-color); opacity: 0.6; margin-bottom: 0.3rem;"><?= $t['internship_info'] ?></h4>
                    <p style="margin: 0; font-size: 0.9rem; color: var(--text-color); opacity: 0.9;"><?= $t['start_label'] ?>: <?= $req['internship_start'] ? date('d/m/Y', strtotime($req['internship_start'])) : '-' ?> &nbsp;<?= $t['end_label'] ?>&nbsp; <?= $req['internship_end'] ? date('d/m/Y', strtotime($req['internship_end'])) : '-' ?></p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8;"><?= $t['work_days_label'] ?>: <?= htmlspecialchars($req['work_days']) ?> (<?= htmlspecialchars($req['work_hours']) ?>)</p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8;"><?= $t['department_label'] ?>: <?= htmlspecialchars($req['department'] ?: '-') ?></p>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 1.5rem 0;">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                
                <!-- ข้อมูลสถานประกอบการ -->
                <div>
                    <h4 style="font-size: 0.9rem; color: var(--text-color); opacity: 0.6; margin-bottom: 0.3rem;"><?= $t['company_label'] ?></h4>
                    <p style="margin: 0; font-size: 1.05rem;"><strong><?= htmlspecialchars($req['company_name']) ?></strong></p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8;"><?= $t['company_type_label'] ?>: <span style="text-transform: uppercase;"><?= htmlspecialchars($req['company_type']) ?></span></p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8; line-height: 1.5;"><?= $t['address'] ?>: <?= nl2br(htmlspecialchars($req['company_address'])) ?> <?= $t['province_label'] ?> <?= htmlspecialchars($req['company_province']) ?></p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8;"><?= $t['phone'] ?>: <?= htmlspecialchars($req['company_phone'] ?: '-') ?></p>
                    <?php if($req['company_website']): ?>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem;"><a href="<?= htmlspecialchars($req['company_website']) ?>" target="_blank" style="color: var(--primary-color);"><?= $t['website_link_btn'] ?></a></p>
                    <?php endif; ?>
                </div>

                <!-- ข้อมูลผู้ติดต่อประสานงาน -->
                <div>
                    <h4 style="font-size: 0.9rem; color: var(--text-color); opacity: 0.6; margin-bottom: 0.3rem;"><?= $t['contact_label'] ?></h4>
                    <p style="margin: 0; font-size: 1.05rem;"><strong><?= htmlspecialchars($req['contact_person'] ?: '-') ?></strong></p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8;"><?= $t['contact_position_label'] ?>: <?= htmlspecialchars($req['contact_position'] ?: '-') ?></p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8;"><?= $t['contact_phone_label'] ?>: <?= htmlspecialchars($req['contact_phone'] ?: '-') ?></p>
                    <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: var(--text-color); opacity: 0.8;"><?= $t['contact_email_label'] ?>: <?= htmlspecialchars($req['contact_email'] ?: '-') ?></p>
                </div>
            </div>
        </div>
        
        <?= $msg ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="status"><?= $t['status'] ?></label>
                <select id="status" name="status" class="form-control">
                    <option value="1" <?php if($req['status']==1) echo 'selected'; ?>><?= $t['status_pending'] ?></option>
                    <option value="2" <?php if($req['status']==2) echo 'selected'; ?>><?= $t['status_approved'] ?></option>
                    <option value="3" <?php if($req['status']==3) echo 'selected'; ?>><?= $t['status_rejected'] ?></option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;"><?= $t['submit'] ?></button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
