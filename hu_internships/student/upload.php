<?php
/**
 * student/upload.php — อัปโหลดเอกสารรับรองการฝึกงาน
 * หน้าที่:
 * - รับไฟล์ PDF/Image จากนิสิตเมื่อฝึกงานเสร็จ
 * - บันทึกตำแหน่งไฟล์ลงฐานข้อมูล
 * - เจ้าหน้าที่จะสามารถตรวจสอบไฟล์นี้ได้จากหน้าจัดการ
 */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();
checkRole('student');

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

// Get active request
$stmt = $conn->prepare("SELECT * FROM internship_requests WHERE student_id = ? ORDER BY request_id DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$req = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$req) {
    die("ไม่พบคำขอฝึกงาน");
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['certificate_file'])) {
    $file = $_FILES['certificate_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $uploadDir = '../uploads/certificates/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $newFileName = 'cert_' . $req['request_id'] . '_' . time() . '.' . $ext;
            $destination = $uploadDir . $newFileName;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Update database
                $update = $conn->prepare("UPDATE internship_requests SET certificate_file = ?, doc_status = 1 WHERE request_id = ?");
                if ($update->execute([$newFileName, $req['request_id']])) {
                    $msg = "<div style='background: rgba(34, 197, 94, 0.1); color: #15803d; padding: 1rem; border-radius: 8px; border-left: 4px solid #22c55e; margin-bottom: 1.5rem;'>อัปโหลดเอกสารรับรองการฝึกงานสำเร็จ! รออาจารย์ที่ปรึกษาประเมินผล</div>";
                    $req['certificate_file'] = $newFileName;
                    $req['doc_status'] = 1;
                }
            } else {
                $msg = "<div style='background: rgba(239, 68, 68, 0.1); color: #b91c1c; padding: 1rem; border-radius: 8px; border-left: 4px solid #ef4444; margin-bottom: 1.5rem;'>เกิดข้อผิดพลาดในการบันทึกไฟล์</div>";
            }
        } else {
            $msg = "<div style='background: rgba(239, 68, 68, 0.1); color: #b91c1c; padding: 1rem; border-radius: 8px; border-left: 4px solid #ef4444; margin-bottom: 1.5rem;'>รองรับเฉพาะไฟล์ PDF, JPG หรือ PNG เท่านั้น</div>";
        }
    } else {
        $msg = "<div style='background: rgba(239, 68, 68, 0.1); color: #b91c1c; padding: 1rem; border-radius: 8px; border-left: 4px solid #ef4444; margin-bottom: 1.5rem;'>กรุณาเลือกไฟล์ที่ต้องการอัปโหลด</div>";
    }
}

$page_title = "อัปโหลดเอกสารรับรองการฝึกงาน";
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container" style="max-width: 600px; margin: 2rem auto; padding: 0 1rem;">
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 1.8rem; margin: 0;">อัปโหลดเอกสารรับรอง</h2>
        <a href="../dashboard.php" class="btn" style="border: 1px solid var(--border-color); color: var(--text-color); text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px;">&larr; กลับหน้าหลัก</a>
    </div>

    <?= $msg ?>

    <div class="card" style="padding: 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 3.5rem; margin-bottom: 1rem;">🎓</div>
            <h3 style="margin-bottom: 0.5rem;">ยื่นจบการฝึกประสบการณ์วิชาชีพ</h3>
            <p style="color: var(--text-color); opacity: 0.8; line-height: 1.5; font-size: 0.95rem;">
                อัปโหลดไฟล์สแกนหรือรูปถ่าย "เอกสารรับรองการฝึกงาน" หรือ "ใบประเมินจากสถานประกอบการ" เพื่อให้อาจารย์ที่ปรึกษาทำการประเมินและอนุมัติปิดจบการฝึกงาน
            </p>
        </div>

        <?php if (!empty($req['certificate_file'])): ?>
            <div style="background: rgba(59, 130, 246, 0.05); border: 1px dashed rgba(59, 130, 246, 0.3); padding: 1.5rem; border-radius: 8px; text-align: center; margin-bottom: 1.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" style="margin-bottom: 0.5rem;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"></path>
                    <path d="M14 3v5h5M16 13H8M16 17H8M10 9H8"></path>
                </svg>
                <div style="font-weight: 600; color: var(--text-color); margin-bottom: 0.2rem;">ไฟล์ที่อัปโหลดแล้ว</div>
                <a href="../uploads/certificates/<?= htmlspecialchars($req['certificate_file']) ?>" target="_blank" style="color: #3b82f6; font-size: 0.9rem;">ดูเอกสารปัจจุบัน</a>
                <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-color); opacity: 0.6;">สามารถอัปโหลดไฟล์ใหม่เพื่อทับไฟล์เดิมได้</div>
            </div>
        <?php endif; ?>

        <?php if ($req['status'] != 4): ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 1.5rem;">
                <label for="certificate_file" style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.95rem;">เลือกไฟล์ (PDF, JPG, PNG)</label>
                <input type="file" name="certificate_file" id="certificate_file" accept=".pdf,.jpg,.jpeg,.png" required
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-color); font-family: inherit;">
            </div>
            
            <button type="submit" style="width: 100%; padding: 0.8rem; background: var(--primary-color); color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 0.5rem; font-family: inherit;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                บันทึกเอกสารและส่งให้อาจารย์ประเมิน
            </button>
        </form>
        <?php else: ?>
        <div style="background: rgba(34, 197, 94, 0.1); padding: 1.5rem; border-radius: 8px; text-align: center; color: #166534;">
            <strong style="display: block; font-size: 1.1rem; margin-bottom: 0.3rem;">การฝึกงานเสร็จสิ้นแล้ว</strong>
            ไม่สามารถแก้ไขหรืออัปโหลดเอกสารเพิ่มเติมได้
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
