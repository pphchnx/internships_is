<?php
/**
 * staff/update_status.php — หน้าจัดการสถานะคำขอฝึกงาน
 * หน้าที่:
 * - แสดงรายละเอียดคำขอฝึกงานแบบเจาะลึก
 * - อนุญาตให้เจ้าหน้าที่ (Staff) หรืออาจารย์ (Teacher) อัปเดตสถานะ (Pending, Approved, Completed, etc.)
 * - ใส่หมายเหตุ (Remark) หรือข้อความถึงนิสิต
 * - พิมพ์ใบส่งตัว (ถ้าอนุมัติแล้ว)
 */
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
    $remark = $_POST['remark'] ?? $req['remark'];
    $advisor_note = $_POST['advisor_note'] ?? $req['advisor_note'];
    
    $update = $conn->prepare("UPDATE internship_requests SET status = ?, remark = ?, advisor_note = ? WHERE request_id = ?");
    if($update->execute([$status, $remark, $advisor_note, $id])) {
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
            <div style="display: flex; gap: 0.5rem;">
                <?php if ($req['status'] == 3 && ($_SESSION['role'] === 'staff' || $_SESSION['role'] === 'teacher')): ?>
                <a href="print_letter.php?id=<?= $id ?>" target="_blank" class="btn" style="background: #eab308; color: white; border: none;">🖨️ พิมพ์ใบส่งตัว</a>
                <?php endif; ?>
                <a href="../dashboard.php" class="btn" style="border: 1px solid var(--border-color); color: var(--text-color);">&larr; <?= $t['back'] ?? 'ย้อนกลับ' ?></a>
            </div>
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

                    <?php if(!empty($req['certificate_file'])): ?>
                    <div style="margin-top: 1rem; background: rgba(16, 185, 129, 0.1); padding: 0.8rem; border-radius: 6px; border-left: 3px solid #10b981;">
                        <strong style="color: #047857; font-size: 0.9rem; display: block; margin-bottom: 0.3rem;">📄 เอกสารรับรองการฝึกงาน:</strong>
                        <a href="../uploads/certificates/<?= htmlspecialchars($req['certificate_file']) ?>" target="_blank" style="color: #059669; font-size: 0.9rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"></path><path d="M14 3v5h5M16 13H8M16 17H8M10 9H8"></path></svg>
                            คลิกเพื่อดูเอกสาร
                        </a>
                    </div>
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
        
        <?php if ($_SESSION['role'] === 'staff' && empty($req['advisor_id'])): ?>
            <div class="alert alert-warning" style="background: rgba(234, 179, 8, 0.1); color: #ca8a04; padding: 1rem; border-left: 4px solid #eab308; margin-bottom: 1.5rem; border-radius: 4px;">
                <strong>⚠️ ข้อควรระวัง:</strong> นิสิตยังไม่ได้เลือกอาจารย์ที่ปรึกษา (Advisor)<br>
                หากข้อมูลไม่ครบถ้วน ระบบแนะนำให้เลือกสถานะ <strong>"ยกเลิก / ให้นิสิตแก้ไข"</strong> เพื่อให้นิสิตกลับไปแก้ไขข้อมูลให้สมบูรณ์
            </div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="status"><?= $t['status'] ?></label>
                <select id="status" name="status" class="form-control">
                    <?php if ($_SESSION['role'] === 'staff'): ?>
                        <option value="0" <?php if($req['status']==0) echo 'selected'; ?>><?= $t['status_pending_staff'] ?? '0: รอเจ้าหน้าที่รับเรื่อง' ?></option>
                        <option value="1" <?php if($req['status']==1) echo 'selected'; ?>><?= $t['status_pending'] ?? '1: รับเรื่องเข้าระบบ' ?></option>
                        <option value="2" <?php if($req['status']==2) echo 'selected'; ?>><?= $t['status_approved'] ?? '2: อาจารย์ที่ปรึกษาอนุมัติ' ?></option>
                        <option value="3" <?php if($req['status']==3) echo 'selected'; ?>><?= $t['status_doc_issued'] ?? '3: ออกใบส่งตัวแล้ว' ?></option>
                        <option value="4" <?php if($req['status']==4) echo 'selected'; ?>><?= $t['status_completed'] ?? '4: ฝึกงานเสร็จสิ้น' ?></option>
                        <option value="9" <?php if($req['status']==9) echo 'selected'; ?>><?= $t['status_canceled'] ?? '9: ยกเลิก / ให้นิสิตแก้ไข' ?></option>
                    <?php elseif ($_SESSION['role'] === 'teacher'): ?>
                        <option value="1" <?php if($req['status']==1) echo 'selected'; ?>>1: รอดำเนินการ (รับเรื่องแล้ว)</option>
                        <option value="2" <?php if($req['status']==2) echo 'selected'; ?>>2: อนุมัติ</option>
                        <option value="3" <?php if($req['status']==3) echo 'selected'; ?>>3: อนุมัติและออกใบส่งตัว</option>
                        <option value="4" <?php if($req['status']==4) echo 'selected'; ?>>4: ฝึกงานเสร็จสิ้น</option>
                        <option value="9" <?php if($req['status']==9) echo 'selected'; ?>>9: ไม่อนุมัติ / ให้แก้ไข</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group" id="remark-group" style="display: none; margin-top: 1.5rem;">
                <label for="remark" style="color: #ef4444;">หมายเหตุ / เหตุผลที่ต้องแก้ไข (จะแสดงให้นิสิตเห็น)</label>
                <textarea id="remark" name="remark" class="form-control" rows="3" placeholder="โปรดระบุข้อผิดพลาดที่นิสิตต้องแก้ไข..."><?= htmlspecialchars($req['remark'] ?? '') ?></textarea>
            </div>

            <?php if ($_SESSION['role'] === 'teacher'): ?>
            <div class="form-group" style="margin-top: 1.5rem; background: rgba(59, 130, 246, 0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(59, 130, 246, 0.2);">
                <label for="advisor_note" style="color: var(--primary-color); font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    บันทึกผลการนิเทศน์ / ข้อเสนอแนะ (สำหรับอาจารย์)
                </label>
                <p style="margin: 0.5rem 0 1rem 0; font-size: 0.85rem; opacity: 0.7;">บันทึกผลการประเมิน หรือข้อเสนอแนะระหว่างการฝึกงานของนิสิต</p>
                <textarea id="advisor_note" name="advisor_note" class="form-control" rows="5" placeholder="บันทึกผลการประเมินที่นี่..."><?= htmlspecialchars($req['advisor_note'] ?? '') ?></textarea>
            </div>
            <?php elseif (!empty($req['advisor_note'])): ?>
            <div class="form-group" style="margin-top: 1.5rem; background: rgba(59, 130, 246, 0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(59, 130, 246, 0.2);">
                <label style="color: var(--primary-color); font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    บันทึกผลการนิเทศน์ (จากอาจารย์)
                </label>
                <div style="margin-top: 1rem; line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($req['advisor_note'])) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var statusSelect = document.getElementById('status');
                var remarkGroup = document.getElementById('remark-group');
                
                function toggleRemark() {
                    if (statusSelect.value == '9') {
                        remarkGroup.style.display = 'block';
                    } else {
                        remarkGroup.style.display = 'none';
                    }
                }
                
                statusSelect.addEventListener('change', toggleRemark);
                toggleRemark(); // เช็คค่าเริ่มต้นตอนโหลดหน้า
            });
            </script>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;"><?= $t['submit'] ?></button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
