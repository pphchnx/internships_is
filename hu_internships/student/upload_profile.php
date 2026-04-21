<?php
/**
 * student/upload_profile.php — อัปโหลดรูปโปรไฟล์นิสิต
 * ไฟล์จะถูกบันทึกที่ assets/images/profile/{student_id}.{ext}
 * path จะถูกบันทึกลงคอลัมน์ profile_photo ใน students_info
 */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

if ($_SESSION['role'] !== 'student') {
    header('Location: ../dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// ==================== Handle Upload ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $file = $_FILES['profile_photo'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2 MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'ไม่สามารถอัปโหลดไฟล์ได้ (Error Code: ' . $file['error'] . ')';
    } elseif (!in_array($file['type'], $allowed)) {
        $error = 'ต้องเป็นไฟล์รูปภาพ (JPG, PNG หรือ WEBP) เท่านั้น';
    } elseif ($file['size'] > $maxSize) {
        $error = 'ขนาดไฟล์ต้องไม่เกิน 2 MB';
    } else {
        // 1) กำหนดที่เก็บไฟล์
        $uploadDir = __DIR__ . '/../assets/images/profile/';
        
        // ลองสร้างโฟลเดอร์แบบป้องกัน Error ถ้าไม่มีสิทธิ์
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        // ตรวจสอบว่าหลังจากพยายามสร้างโฟลเดอร์แล้ว สิทธิ์เขียนไฟล์ได้หรือไม่
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            $error = $t['upload_error_perm'];
        } else {
            // 2) ตั้งชื่อไฟล์ใหม่แบบไม่ให้ซ้ำ (เพื่อแก้ปัญหาเว็บจำรูปเดิม / caching)
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $clean_id = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $user_id);
            $newFilename = 'profile_' . $clean_id . '_' . time() . '.' . strtolower($ext);
            $destPath = $uploadDir . $newFilename;
            $dbPath = 'assets/images/profile/' . $newFilename;

            // 3) ย้ายไฟล์ที่อัปโหลดมาไว้ในโฟลเดอร์ชั่วคราว
            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                // 4) ลบไฟล์รูปเดิม (ถ้ามี) แบบป้องกันการลบผิดพลาด 
                try {
                    $q = $conn->prepare("SELECT profile_photo FROM students_info WHERE student_id = ?");
                    $q->execute([$user_id]);
                    $oldData = $q->fetch(PDO::FETCH_ASSOC);
                    
                    if ($oldData && !empty($oldData['profile_photo'])) {
                        $oldFilePath = __DIR__ . '/../' . $oldData['profile_photo'];
                        if (file_exists($oldFilePath) && strpos($oldData['profile_photo'], 'profile_') !== false) {
                            @unlink($oldFilePath); // พยายามลบรูปเก่าทิ้ง
                        }
                    }
                } catch (PDOException $e) { /* ละเว้นถ้ายังไม่มี column ใน DB */ }

                // 5) อัปเดตชื่อรูปใหม่ลงฐานข้อมูล
                try {
                    $upd = $conn->prepare("UPDATE students_info SET profile_photo = ? WHERE student_id = ?");
                    $upd->execute([$dbPath, $user_id]);
                    $success = $t['upload_success'];
                } catch (PDOException $e) {
                    // หากฐานข้อมูล Error 1054 คือไม่มี Field
                    if (strpos($e->getMessage(), '1054') !== false) {
                        $error = $t['upload_error_db'];
                    } else {
                        $error = 'Database Error: ' . $e->getMessage();
                    }
                }
            } else {
                $error = $t['upload_error_perm'];
            }
        }
    }
}

// ==================== Handle Remove ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_photo'])) {
    try {
        $old = $conn->prepare("SELECT profile_photo FROM students_info WHERE student_id = ?");
        $old->execute([$user_id]);
        $oldRow = $old->fetch(PDO::FETCH_ASSOC);
        if ($oldRow && $oldRow['profile_photo']) {
            $oldPath = __DIR__ . '/../' . $oldRow['profile_photo'];
            if (file_exists($oldPath))
                unlink($oldPath);
        }
        $conn->prepare("UPDATE students_info SET profile_photo = NULL WHERE student_id = ?")->execute([$user_id]);
        $success = 'ลบรูปโปรไฟล์แล้ว';
    } catch (PDOException $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}

// ==================== Fetch current photo ====================
$currentPhoto = null;
try {
    $q = $conn->prepare("SELECT profile_photo FROM students_info WHERE student_id = ?");
    $q->execute([$user_id]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['profile_photo'] && file_exists(__DIR__ . '/../' . $row['profile_photo'])) {
        $currentPhoto = '../' . $row['profile_photo'];
    }
} catch (PDOException $e) {
}

$page_title = $t['profile_photo'] . ' — HU Internships';
$extra_css = 'upload_profile.css';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="up-wrap">
    <div class="up-card">
        <div class="up-head">
            <h1><?= $t['profile_photo'] ?></h1>
            <p><?= $t['change_photo_desc'] ?></p>
        </div>
        <div class="up-body">

            <!-- Current avatar -->
            <div class="up-avatar-wrap">
                <?php if ($currentPhoto): ?>
                    <img src="<?= htmlspecialchars($currentPhoto) ?>?v=<?= time() ?>" class="up-avatar" id="avatar-display"
                        alt="รูปโปรไฟล์">
                <?php else: ?>
                    <div class="up-avatar-placeholder" id="avatar-display">
                        <?= mb_strtoupper(mb_substr($_SESSION['fullname'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="up-name"><?= htmlspecialchars($_SESSION['fullname']) ?></div>
                    <div class="up-id"><?= htmlspecialchars($user_id) ?></div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if ($success): ?>
                <div class="up-alert up-alert-ok">✓ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="up-alert up-alert-err">✕ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Upload form -->
            <form method="post" enctype="multipart/form-data" id="up-form">
                <div class="up-dropzone" id="up-dropzone">
                    <input type="file" name="profile_photo" id="up-file" accept="image/jpeg,image/png,image/webp">
                    <div class="up-dz-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                    </div>
                    <div class="up-dz-text"><?= $t['dropzone_text'] ?></div>
                    <div class="up-dz-hint"><?= $t['dropzone_hint'] ?></div>
                    <img id="up-preview-img" alt="preview">
                </div>

                <div class="up-btn-row">
                    <button type="submit" class="up-btn-submit" id="up-submit-btn" disabled>
                        <?= $t['upload_new_photo'] ?>
                    </button>
                    <?php if ($currentPhoto): ?>
                        <button type="submit" name="remove_photo" value="1" class="up-btn-remove"
                            onclick="return confirm('<?= $t['delete_photo'] ?>?')">
                            <?= $t['delete_photo'] ?>
                        </button>
                    <?php endif; ?>
                </div>
            </form>

            <a href="../dashboard.php" class="up-back-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                <?= $t['back_to_dashboard'] ?>
            </a>
        </div>
    </div>
</div>

<script>
    const fileInput = document.getElementById('up-file');
    const previewImg = document.getElementById('up-preview-img');
    const submitBtn = document.getElementById('up-submit-btn');
    const dropzone = document.getElementById('up-dropzone');

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
        };
        reader.readAsDataURL(file);
        submitBtn.disabled = false;
    });

    // Drag & drop visual
    dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
    dropzone.addEventListener('drop', e => { e.preventDefault(); dropzone.classList.remove('drag-over'); });
</script>

<?php require_once '../includes/footer.php'; ?>