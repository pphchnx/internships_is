<?php
/**
 * student/register.php — ฟอร์มยื่นคำขอฝึกงาน
 * นิสิตกรอกข้อมูลสถานประกอบการ ผู้ติดต่อ ระยะเวลา และเลือกอาจารย์นิเทศ
 * บันทึกลง internship_requests
 */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

checkLogin();
checkRole('student'); // บังคับให้ต้องเป็น role 'student'

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

$user_id = $_SESSION['user_id'];
$msg     = '';
$error   = '';

// ตรวจสอบว่านิสิตเคยยื่นคำขอแล้วหรือยัง
$chk = $conn->prepare("SELECT request_id, company_name, status FROM internship_requests WHERE student_id = ? LIMIT 1");
$chk->execute([$user_id]);
$existing = $chk->fetch(PDO::FETCH_ASSOC);

// ดึงรายชื่ออาจารย์ทั้งหมด (สำหรับ dropdown เลือกอาจารย์นิเทศ)
$teachers = [];
try {
    $tq = $conn->query("SELECT u.username, u.fullname, ti.position FROM users u LEFT JOIN teachers_info ti ON u.username = ti.user_id WHERE u.role = 'teacher' AND u.status = 'active' ORDER BY u.fullname");
    $teachers = $tq->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// ==================== Process Form Submission ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$existing) {

    // ---- ข้อมูลสถานประกอบการ ----
    $company_name     = sanitizeInput($_POST['company_name']     ?? '');
    $company_type     = sanitizeInput($_POST['company_type']     ?? 'private');
    $company_address  = sanitizeInput($_POST['company_address']  ?? '');
    $company_province = sanitizeInput($_POST['company_province'] ?? '');
    $company_phone    = sanitizeInput($_POST['company_phone']    ?? '');
    $company_website  = sanitizeInput($_POST['company_website']  ?? '');

    // ---- ข้อมูลผู้ติดต่อ ----
    $contact_person   = sanitizeInput($_POST['contact_person']   ?? '');
    $contact_position = sanitizeInput($_POST['contact_position'] ?? '');
    $contact_phone    = sanitizeInput($_POST['contact_phone']    ?? '');
    $contact_email    = sanitizeInput($_POST['contact_email']    ?? '');
    $department       = sanitizeInput($_POST['department']       ?? '');

    // ---- ระยะเวลาฝึกงาน ----
    $internship_start = $_POST['internship_start'] ?? '';
    $internship_end   = $_POST['internship_end']   ?? '';
    $work_days        = sanitizeInput($_POST['work_days']   ?? 'จันทร์-ศุกร์');
    $work_hours       = sanitizeInput($_POST['work_hours']  ?? '09:00-18:00');

    // ---- อาจารย์นิเทศ ----
    $advisor_id = sanitizeInput($_POST['advisor_id'] ?? '') ?: null;

    // ---- Validation ----
    if (!$company_name) {
        $error = 'กรุณากรอกชื่อสถานประกอบการ';
    } elseif (!$internship_start || !$internship_end) {
        $error = 'กรุณาระบุวันเริ่มต้นและวันสิ้นสุดการฝึกงาน';
    } elseif (strtotime($internship_end) <= strtotime($internship_start)) {
        $error = 'วันสิ้นสุดต้องหลังจากวันเริ่มต้น';
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO internship_requests
                (student_id, company_name, company_type, company_address, company_province,
                 company_phone, company_website, contact_person, contact_position,
                 contact_phone, contact_email, department,
                 internship_start, internship_end, work_days, work_hours, advisor_id, status)
                VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,0)
            ");
            $stmt->execute([
                $user_id, $company_name, $company_type, $company_address, $company_province,
                $company_phone, $company_website, $contact_person, $contact_position,
                $contact_phone, $contact_email, $department,
                $internship_start, $internship_end, $work_days, $work_hours, $advisor_id
            ]);
            // redirect ไป dashboard เพื่อดูข้อมูล
            header("Location: ../dashboard.php?submitted=1");
            exit;
        } catch (PDOException $e) {
            $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}

// SVG icons
$svg_warn  = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
$svg_check = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';

$page_title = $t['internship_form_title'];
$extra_css  = 'dashboard.css';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container" style="max-width:960px; padding:2rem 1.5rem;">

    <!-- Page title -->
    <div style="margin-bottom:2rem;">
        <a href="../dashboard.php" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.88rem;color:var(--primary-color);text-decoration:none;margin-bottom:0.75rem;opacity:0.8;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            <?= $t['back_dashboard'] ?>
        </a>
        <h1 style="font-size:1.8rem;font-weight:800;color:var(--text-color);margin:0;"><?= $t['internship_form_title'] ?></h1>
        <p style="margin-top:0.3rem;opacity:0.6;font-size:0.95rem;"><?= $t['internship_form_subtitle'] ?></p>
    </div>

    <?php if ($existing): ?>
    <!-- ========== เคยยื่นแล้ว ========== -->
    <div style="background:var(--card-bg);border-radius:12px;padding:2rem;border:1px solid var(--border-color);border-left:4px solid var(--warning-color);">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
            <?= $svg_warn ?>
            <strong style="font-size:1.1rem;"><?= $t['already_submitted'] ?></strong>
        </div>
        <p style="opacity:0.75;margin-bottom:1.25rem;">
            <?= $t['company'] ?>: <strong><?= htmlspecialchars($existing['company_name']) ?></strong><br>
            <?= $t['contact_staff_fix'] ?>
        </p>
        <a href="../student/view_status.php" class="btn btn-primary"><?= $t['view_status_btn'] ?></a>
    </div>

    <?php else: ?>

    <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom:1.5rem;">
        <div class="alert-icon"><?= $svg_warn ?></div>
        <div><?= htmlspecialchars($error) ?></div>
    </div>
    <?php endif; ?>

    <!-- ========== FORM ========== -->
    <form method="POST" id="internshipForm" novalidate>

        <!-- =====================================================
             SECTION 1: ข้อมูลสถานประกอบการ
             ===================================================== -->
        <div class="iform-card">
            <div class="iform-section-title">
                <span class="iform-section-num">1</span>
                <?= $t['step_company'] ?>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:2;">
                    <label for="company_name"><?= $t['company_name_label'] ?> <span class="required">*</span></label>
                    <input type="text" id="company_name" name="company_name" class="form-control"
                           placeholder="<?= $t['fullname_placeholder'] ?>"
                           value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="company_type"><?= $t['company_type_label'] ?></label>
                    <select id="company_type" name="company_type" class="form-control">
                        <?php foreach([
                            'private'    => $t['private'],
                            'government' => $t['government'],
                            'ngo'        => $t['ngo'],
                            'other'      => $t['other'] ?? 'อื่นๆ',
                        ] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= ($_POST['company_type'] ?? 'private') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="company_address"><?= $t['company_address_label'] ?></label>
                <textarea id="company_address" name="company_address" class="form-control" rows="2"
                          placeholder="Address details..."><?= htmlspecialchars($_POST['company_address'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="company_province"><?= $t['province_label'] ?></label>
                    <input type="text" id="company_province" name="company_province" class="form-control"
                           placeholder="e.g. Bangkok"
                           value="<?= htmlspecialchars($_POST['company_province'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="company_phone"><?= $t['company_phone_label'] ?></label>
                    <input type="text" id="company_phone" name="company_phone" class="form-control"
                           placeholder="02-XXX-XXXX"
                           value="<?= htmlspecialchars($_POST['company_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="company_website"><?= $t['website_label'] ?></label>
                    <input type="url" id="company_website" name="company_website" class="form-control"
                           placeholder="https://www.example.com"
                           value="<?= htmlspecialchars($_POST['company_website'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="department"><?= $t['department_label'] ?></label>
                <input type="text" id="department" name="department" class="form-control"
                       placeholder="e.g. IT Department"
                       value="<?= htmlspecialchars($_POST['department'] ?? '') ?>">
            </div>
        </div>

        <!-- =====================================================
             SECTION 2: ข้อมูลผู้ติดต่อที่บริษัท
             ===================================================== -->
        <div class="iform-card">
            <div class="iform-section-title">
                <span class="iform-section-num">2</span>
                <?= $t['step_contact'] ?>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:2;">
                    <label for="contact_person"><?= $t['contact_person_label'] ?></label>
                    <input type="text" id="contact_person" name="contact_person" class="form-control"
                           placeholder="<?= $t['fullname_placeholder'] ?>"
                           value="<?= htmlspecialchars($_POST['contact_person'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="contact_position"><?= $t['contact_position_label'] ?></label>
                    <input type="text" id="contact_position" name="contact_position" class="form-control"
                           placeholder="e.g. IT Manager"
                           value="<?= htmlspecialchars($_POST['contact_position'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_phone"><?= $t['contact_phone_label'] ?></label>
                    <input type="text" id="contact_phone" name="contact_phone" class="form-control"
                           placeholder="08X-XXX-XXXX"
                           value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="contact_email"><?= $t['contact_email_label'] ?></label>
                    <input type="email" id="contact_email" name="contact_email" class="form-control"
                           placeholder="supervisor@company.com"
                           value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- =====================================================
             SECTION 3: ระยะเวลาฝึกงาน
             ===================================================== -->
        <div class="iform-card">
            <div class="iform-section-title">
                <span class="iform-section-num">3</span>
                <?= $t['step_period'] ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="internship_start"><?= $t['start_date_label'] ?> <span class="required">*</span></label>
                    <input type="date" id="internship_start" name="internship_start" class="form-control"
                           value="<?= htmlspecialchars($_POST['internship_start'] ?? '') ?>"
                           min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label for="internship_end"><?= $t['end_date_label'] ?> <span class="required">*</span></label>
                    <input type="date" id="internship_end" name="internship_end" class="form-control"
                           value="<?= htmlspecialchars($_POST['internship_end'] ?? '') ?>" required>
                </div>
                <!-- คำนวณระยะเวลาอัตโนมัติ -->
                <div class="form-group" id="duration-display" style="display:none;">
                    <label><?= $t['duration_calc'] ?></label>
                    <div class="form-control" id="duration-value"
                         style="background:rgba(229,9,20,0.05);border-color:rgba(229,9,20,0.2);color:var(--primary-color);font-weight:700;">
                        —
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="work_days"><?= $t['work_days_label'] ?></label>
                    <select id="work_days" name="work_days" class="form-control">
                        <?php foreach([
                            'จันทร์-ศุกร์'     => 'Mon – Fri',
                            'จันทร์-เสาร์'     => 'Mon – Sat',
                            'อังคาร-เสาร์'    => 'Tue – Sat',
                            'อื่นๆ'           => $t['other'] ?? 'Others',
                        ] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= ($_POST['work_days'] ?? 'จันทร์-ศุกร์') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="work_hours"><?= $t['work_hours_label'] ?></label>
                    <input type="text" id="work_hours" name="work_hours" class="form-control"
                           placeholder="e.g. 09:00-18:00"
                           value="<?= htmlspecialchars($_POST['work_hours'] ?? '09:00-18:00') ?>">
                </div>
            </div>
        </div>

        <!-- =====================================================
             SECTION 4: อาจารย์นิเทศ
             ===================================================== -->
        <div class="iform-card">
            <div class="iform-section-title">
                <span class="iform-section-num">4</span>
                <?= $t['step_advisor'] ?>
            </div>

            <div class="form-group">
                <label for="advisor_id"><?= $t['select_advisor_label'] ?></label>
                <select id="advisor_id" name="advisor_id" class="form-control">
                    <option value=""><?= $t['auto_assign'] ?></option>
                    <?php foreach ($teachers as $tc): ?>
                    <option value="<?= htmlspecialchars($tc['username']) ?>"
                            <?= ($_POST['advisor_id'] ?? '') === $tc['username'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tc['fullname']) ?>
                        <?php if ($tc['position']): ?>
                        (<?= htmlspecialchars($tc['position']) ?>)
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <small style="color:var(--text-color);opacity:0.55;font-size:0.8rem;margin-top:0.3rem;display:block;">
                    <?= $t['advisor_hint'] ?>
                </small>
            </div>
        </div>

        <!-- ========== Submit Button ========== -->
        <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:0.5rem;">
            <a href="../dashboard.php" class="btn"
               style="border:1px solid var(--border-color);color:var(--text-color);background:transparent;padding:0.85rem 2rem;">
                ยกเลิก
            </a>
            <button type="submit" class="btn btn-primary" style="padding:0.85rem 2.5rem;font-size:1rem;font-weight:700;display:inline-flex;align-items:center;gap:0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                ยื่นคำขอฝึกงาน
            </button>
        </div>
    </form>

    <?php endif; ?>
</div>

<script>
/* ====================================================
   คำนวณระยะเวลาฝึกงานแบบ real-time
   ==================================================== */
(function() {
    var startInput = document.getElementById('internship_start');
    var endInput   = document.getElementById('internship_end');
    var display    = document.getElementById('duration-display');
    var value      = document.getElementById('duration-value');

    if (!startInput || !endInput) return;

    function calcDuration() {
        var s = new Date(startInput.value);
        var e = new Date(endInput.value);
        if (!startInput.value || !endInput.value || e <= s) {
            display.style.display = 'none';
            return;
        }
        var days  = Math.round((e - s) / 86400000);
        var weeks = (days / 7).toFixed(1);
        value.textContent = days + ' <?= $t['days'] ?> (' + weeks + ' <?= $t['weeks'] ?>)';
        display.style.display = 'block';
    }

    startInput.addEventListener('change', calcDuration);
    endInput.addEventListener('change', calcDuration);
    calcDuration(); // รันครั้งแรก (กรณีรีโหลดหน้า)
})();
</script>
<style>
/* ======= INTERNSHIP FORM CARDS ======= */
.iform-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.75rem;
    margin-bottom: 1.5rem;
}

.iform-section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.75rem;
}

.iform-section-num {
    width: 28px;
    height: 28px;
    background: var(--primary-color);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    font-weight: 800;
    flex-shrink: 0;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.form-group label {
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--text-color);
}

.required { color: var(--primary-color); }

.form-control {
    padding: 0.65rem 0.9rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--bg-color);
    color: var(--text-color);
    font-size: 0.9rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
    width: 100%;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(229,9,20,0.1);
}

textarea.form-control { resize: vertical; }
</style>

<?php require_once '../includes/footer.php'; ?>
