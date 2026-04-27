<?php
/**
 * register.php — หน้าสมัครสมาชิกสำหรับนิสิต อาจารย์ และเจ้าหน้าที่
 * ระบบฝึกงานคณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ
 *
 * ขั้นตอนการทำงาน:
 *   1. ผู้ใช้เลือกประเภท (นิสิต / อาจารย์ / เจ้าหน้าที่)
 *   2. กรอกแบบฟอร์มตามประเภทที่เลือก
 *   3. ระบบตรวจสอบ (Validation) และบันทึกลงฐานข้อมูล
 *      - ตาราง users      : ข้อมูลบัญชีพื้นฐาน
 *      - ตาราง students_info / teachers_info / staff_info : ข้อมูลเฉพาะแต่ละ role
 */

session_start();
require_once 'includes/db_connect.php';  // เชื่อมต่อฐานข้อมูล
require_once 'includes/functions.php';   // ฟังก์ชันช่วยเหลือ เช่น sanitizeInput()

// ถ้าล็อกอินอยู่แล้วให้ redirect ไปหน้า dashboard ทันที
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// ==================== ระบบภาษา ====================
// อ่านภาษาจาก URL parameter (?lang=th) หรือจาก session หรือใช้ค่าเริ่มต้นเป็นภาษาไทย
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "lang/{$lang}.php";  // โหลดไฟล์คำแปลตามภาษาที่เลือก

// ==================== ตัวแปรสถานะ ====================
$error         = '';   // ข้อความแสดงข้อผิดพลาด
$success       = '';   // ข้อความแสดงความสำเร็จ
$step          = isset($_POST['step']) ? (int)$_POST['step'] : 1;  // ขั้นตอนฟอร์ม (1=เลือก role, 2=กรอกข้อมูล)
$selected_role = $_POST['role'] ?? $_GET['role'] ?? '';            // role ที่ผู้ใช้เลือก

// ==================== ข้อมูลหลักสูตร ====================
/**
 * $majors_is: หลักสูตรที่ใช้ระบบนี้โดยตรง (สาขาวิชาสารสนเทศศึกษา)
 *             นิสิตสาขานี้สมัครได้ทั้งภาคปกติและภาคพิเศษ
 *
 * $majors_other: หลักสูตรอื่นในคณะมนุษยศาสตร์
 *               สำหรับลิงก์ไปเว็บไซต์ของสาขาวิชานั้นๆ
 *               (เติม URL ที่ช่อง 'url' เมื่อได้รับข้อมูล)
 */
$majors_is = [
    // ภาคปกติ (Regular)
    ['value' => 'ศศ.บ. สารสนเทศศึกษา (ภาคปกติ)',   'label' => 'ศศ.บ. สารสนเทศศึกษา — ภาคปกติ',   'type' => 'regular'],
    // ภาคพิเศษ (Special / Evening Program)
    ['value' => 'ศศ.บ. สารสนเทศศึกษา (ภาคพิเศษ)',  'label' => 'ศศ.บ. สารสนเทศศึกษา — ภาคพิเศษ',  'type' => 'special'],
];

// หลักสูตรอื่นๆ ในคณะมนุษยศาสตร์ — ลิงก์ไปยังเว็บไซต์สาขาวิชา
// TODO: เติม URL จริงของแต่ละสาขาในช่อง 'url'
$majors_other = [
    ['degree' => 'วท.บ.',  'name' => 'สาขาวิชาจิตวิทยา',                              'url' => '#'],
    ['degree' => 'ศศ.บ.',  'name' => 'สาขาวิชาภาษาไทย',                               'url' => '#'],
    ['degree' => 'ศศ.บ.',  'name' => 'สาขาวิชาภาษาเพื่อการสื่อสาร (หลักสูตรนานาชาติ)', 'url' => '#'],
    ['degree' => 'ศศ.บ.',  'name' => 'สาขาวิชาวรรณกรรมสำหรับเด็ก',                    'url' => '#'],
    ['degree' => 'ศศ.บ.',  'name' => 'สาขาวิชาภาษาอังกฤษ',                             'url' => '#'],
    ['degree' => 'ศศ.บ.',  'name' => 'สาขาวิชาภาษาเพื่ออาชีพ (หลักสูตรนานาชาติ)',      'url' => '#'],
    ['degree' => 'ศศ.บ.',  'name' => 'สาขาวิชาปรัชญาและศาสนา',                         'url' => '#'],
    ['degree' => 'ศศ.บ.',  'name' => 'สาขาวิชาภาษาตะวันออก',                           'url' => '#'],
    ['degree' => 'กศ.บ.',  'name' => 'สาขาวิชาภาษาไทย (กศ.บ.)',                        'url' => '#'],
    ['degree' => 'กศ.บ.',  'name' => 'สาขาวิชาภาษาอังกฤษ (กศ.บ.)',                     'url' => '#'],
    ['degree' => 'ศศ.บ.',  'name' => 'สาขาวิชาภาษาและวัฒนธรรมอาเซียน',                  'url' => '#'],
];

// ==================== SVG Icons (inline) ====================
// ใช้ SVG แทน emoji เพื่อให้ render สม่ำเสมอในทุก browser/OS
$svg_student = '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
$svg_teacher = '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M7 7h4M7 11h2"/><circle cx="15" cy="9" r="2"/></svg>';
$svg_staff   = '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
$svg_check   = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
$svg_warn    = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
$svg_link    = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>';

// ==================== PROCESS FORM SUBMISSION ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['final_submit'])) {

    // ดึงและทำความสะอาดข้อมูลที่รับมา (sanitize ป้องกัน XSS/SQL Injection)
    $role         = sanitizeInput($_POST['role']);
    $username     = sanitizeInput($_POST['username']);
    $fullname     = sanitizeInput($_POST['fullname']);
    $email        = sanitizeInput($_POST['email']);
    $phone        = sanitizeInput($_POST['phone']);
    $password     = $_POST['password'];   // รหัสผ่านไม่ต้อง sanitize (จะ hash ก่อนบันทึก)
    $password2    = $_POST['password2'];

    // ---------- Validation ----------
    if (strlen($username) < 4) {
        $error = $t['username_short'];

    } elseif ($password !== $password2) {
        $error = $t['password_mismatch'];

    } elseif (strlen($password) < 6) {
        $error = $t['password_short'];

    } else {
        // ตรวจสอบว่าชื่อผู้ใช้ซ้ำในฐานข้อมูลหรือไม่
        $chk = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $chk->execute([$username]);

        if ($chk->rowCount() > 0) {
            // มีชื่อผู้ใช้นี้อยู่แล้วในระบบ
            $error = $t['username_taken'];

        } else {
            // Hash รหัสผ่านด้วย bcrypt (PASSWORD_DEFAULT) ก่อนบันทึก
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            try {
                // เริ่ม Transaction เพื่อให้ INSERT ทั้ง 2 ตาราง สำเร็จหรือล้มเหลวพร้อมกัน
                $conn->beginTransaction();

                // ----- INSERT ตาราง users (ข้อมูลบัญชีหลัก) -----
                $stmt = $conn->prepare(
                    "INSERT INTO users (username, password, fullname, role, email, phone)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$username, $hashed, $fullname, $role, $email, $phone]);

                // ----- INSERT ตาราง role-specific -----
                if ($role === 'student') {
                    // ข้อมูลเฉพาะนิสิต
                    $prefix_th     = sanitizeInput($_POST['prefix_th']     ?? '');
                    $first_th      = sanitizeInput($_POST['first_name_th'] ?? '');
                    $last_th       = sanitizeInput($_POST['last_name_th']  ?? '');
                    $prefix_en     = sanitizeInput($_POST['prefix_en']     ?? '');
                    $first_en      = sanitizeInput($_POST['first_name_en'] ?? '');
                    $last_en       = sanitizeInput($_POST['last_name_en']  ?? '');
                    $major         = sanitizeInput($_POST['major']         ?? $majors_is[0]['value']);
                    $academic_year = sanitizeInput($_POST['academic_year'] ?? '');

                    // กำหนด student_type จาก major ที่เลือก
                    // ถ้า major มีคำว่า "ภาคพิเศษ" ให้เป็น 'special' มิฉะนั้นเป็น 'regular'
                    $student_type = (strpos($major, 'ภาคพิเศษ') !== false) ? 'special' : 'regular';

                    $si = $conn->prepare(
                        "INSERT INTO students_info
                         (student_id, prefix_th, first_name_th, last_name_th,
                          prefix_en, first_name_en, last_name_en,
                          phone, email, major, student_type, academic_year)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
                    );
                    $si->execute([
                        $username, $prefix_th, $first_th, $last_th,
                        $prefix_en, $first_en, $last_en,
                        $phone, $email, $major, $student_type, $academic_year
                    ]);

                } elseif ($role === 'teacher') {
                    // ข้อมูลเฉพาะอาจารย์
                    $position  = sanitizeInput($_POST['position']     ?? '');
                    $education = sanitizeInput($_POST['education']     ?? '');
                    $full_en   = sanitizeInput($_POST['full_name_en'] ?? '');
                    $pos_en    = sanitizeInput($_POST['position_en']  ?? '');
                    $detail_en = sanitizeInput($_POST['detail_en']    ?? '');

                    $ti = $conn->prepare(
                        "INSERT INTO teachers_info
                         (user_id, full_name, position, education, full_name_en, position_en, detail_en)
                         VALUES (?,?,?,?,?,?,?)"
                    );
                    $ti->execute([$username, $fullname, $position, $education, $full_en, $pos_en, $detail_en]);

                } elseif ($role === 'staff') {
                    // ข้อมูลเฉพาะเจ้าหน้าที่
                    $position   = sanitizeInput($_POST['position']   ?? '');
                    $department = sanitizeInput($_POST['department'] ?? 'Internship Office');

                    $sf = $conn->prepare(
                        "INSERT INTO staff_info (staff_id, position, department, phone, email)
                         VALUES (?,?,?,?,?)"
                    );
                    $sf->execute([$username, $position, $department, $phone, $email]);
                }

                // ยืนยัน Transaction ถ้าทุก INSERT สำเร็จ
                $conn->commit();
                $success = 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ';

            } catch (PDOException $e) {
                // ยกเลิก Transaction ถ้ามีข้อผิดพลาดใดๆ
                $conn->rollBack();
                $error = 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage();
            }
        }
    }
}

// ==================== ตั้งค่าหน้าเว็บ ====================
$page_title = $t['register'] ?? 'สมัครสมาชิก';
$extra_css  = 'register.css';
require_once 'includes/header.php';
// ไม่แสดง navbar บนหน้า register เพื่อให้ดูสะอาดและมีสมาธิกับการกรอกฟอร์ม
?>

<!-- ==================== LAYOUT WRAPPER ====================
     Two-column layout:
     - ซ้าย  (.register-brand) : ข้อมูลระบบ / โลโก้ / feature list
     - ขวา  (.register-panel) : ฟอร์มสมัครสมาชิก
-->
<div class="register-wrapper">

    <!-- ========== LEFT: BRAND PANEL ========== -->
    <div class="register-brand">
        <div class="brand-content">
            <!-- โลโก้ -->
            <div class="brand-logo">SWU<span>Internships</span></div>
            <h2>ระบบจัดการฝึกงาน<br>คณะมนุษยศาสตร์ มศว</h2>
            <p>Srinakharinwirot University</p>

            <!-- Features ที่รองรับแต่ละ role -->
            <div class="brand-features">
                <div class="feature-item">
                    <span class="feature-icon"><?= $svg_student ?></span>
                    <span>นิสิต — ยื่นคำขอและติดตามสถานะ</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon"><?= $svg_teacher ?></span>
                    <span>อาจารย์ — นิเทศและประเมินผล</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon"><?= $svg_staff ?></span>
                    <span>เจ้าหน้าที่ — จัดการและอนุมัติ</span>
                </div>
            </div>

            <!-- ======================================================
                 ส่วนแสดงหลักสูตรอื่นๆ ในคณะ พร้อมลิงก์ไปเว็บของสาขา
                 TODO: เติม URL จริงในอาร์เรย์ $majors_other ด้านบน
                 ====================================================== -->
            <div class="brand-other-majors">
                <p class="other-majors-title"><?= $t['other_majors'] ?></p>
                <div class="other-majors-list">
                    <?php foreach ($majors_other as $om): ?>
                    <!-- การ์ดลิงก์สาขาอื่น — เปิดในแท็บใหม่ (target="_blank") -->
                    <a href="<?= htmlspecialchars($om['url']) ?>" target="_blank" class="other-major-link">
                        <span class="other-major-degree"><?= htmlspecialchars($om['degree']) ?></span>
                        <span class="other-major-name"><?= htmlspecialchars($om['name']) ?></span>
                        <!-- ไอคอน external link -->
                        <span class="other-major-icon"><?= $svg_link ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div><!-- /.register-brand -->

    <!-- ========== RIGHT: FORM PANEL ========== -->
    <div class="register-panel">
        <div class="register-card">

            <!-- Header + ลิงก์ไปหน้า login -->
            <div class="register-header">
                <h1><?= $t['register'] ?></h1>
                <p><?= $t['have_account'] ?> <a href="login.php"><?= $t['login_now'] ?></a></p>
            </div>

            <?php if ($success): ?>
            <!-- ========== สมัครสำเร็จ ========== -->
            <div class="alert alert-success">
                <div class="alert-icon"><?= $svg_check ?></div>
                <div>
                    <strong><?= $success ?></strong>
                    <div style="margin-top:1rem;">
                        <a href="login.php" class="btn btn-primary"
                           style="display:inline-block;width:100%;text-align:center;padding:0.85rem;">
                            <?= $t['login_now'] ?>
                        </a>
                    </div>
                </div>
            </div>

            <?php else: ?>

            <?php if ($error): ?>
            <!-- ========== แสดงข้อผิดพลาด ========== -->
            <div class="alert alert-error">
                <div class="alert-icon"><?= $svg_warn ?></div>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
            <?php endif; ?>

            <!-- ===============================
                 STEP 1: เลือกประเภทผู้ใช้งาน
                 =============================== -->
            <?php if (!$selected_role || ($step == 1 && !isset($_POST['final_submit']))): ?>

            <div class="role-selector">
                <p class="role-label"><?= $t['select_user_type'] ?></p>
                <div class="role-cards">

                    <!-- การ์ด: นิสิต -->
                    <form method="POST">
                        <input type="hidden" name="step" value="2">
                        <input type="hidden" name="role" value="student">
                        <button type="submit" class="role-card">
                            <span class="role-icon"><?= $svg_student ?></span>
                            <span class="role-name"><?= $t['role_student'] ?></span>
                            <span class="role-desc">Student</span>
                        </button>
                    </form>

                    <!-- การ์ด: อาจารย์ -->
                    <form method="POST">
                        <input type="hidden" name="step" value="2">
                        <input type="hidden" name="role" value="teacher">
                        <button type="submit" class="role-card">
                            <span class="role-icon"><?= $svg_teacher ?></span>
                            <span class="role-name"><?= $t['role_teacher'] ?></span>
                            <span class="role-desc">Teacher</span>
                        </button>
                    </form>

                    <!-- การ์ด: เจ้าหน้าที่ -->
                    <form method="POST">
                        <input type="hidden" name="step" value="2">
                        <input type="hidden" name="role" value="staff">
                        <button type="submit" class="role-card">
                            <span class="role-icon"><?= $svg_staff ?></span>
                            <span class="role-name"><?= $t['role_staff'] ?></span>
                            <span class="role-desc">Staff</span>
                        </button>
                    </form>

                </div>
            </div><!-- /.role-selector -->

            <!-- ===============================
                 STEP 2: กรอกแบบฟอร์ม
                 =============================== -->
            <?php else: ?>

            <form method="POST" id="registerForm" novalidate>
                <!-- Hidden fields ส่งข้อมูล step และ role กลับมา -->
                <input type="hidden" name="final_submit" value="1">
                <input type="hidden" name="role" value="<?= htmlspecialchars($selected_role) ?>">

                <!-- Badge แสดง role ที่เลือก พร้อมปุ่มเปลี่ยน -->
                <?php
                $badge_icons = ['student' => $svg_student, 'teacher' => $svg_teacher, 'staff' => $svg_staff];
                $badge_names = ['student' => $t['role_student'], 'teacher' => $t['role_teacher'], 'staff' => $t['role_staff']];
                ?>
                <div class="role-badge">
                    <span class="badge-svg"><?= $badge_icons[$selected_role] ?? '' ?></span>
                    <?= $badge_names[$selected_role] ?? '' ?>
                    <a href="register.php" class="change-role"><?= $t['change'] ?></a>
                </div>

                <!-- ==================== ฟิลด์ร่วมทุก role ====================
                     username, fullname, email, phone, password
                -->
                <div class="section-title"><?= $t['account_info'] ?></div>

                <div class="form-row">
                    <div class="form-group">
                        <!-- Label เปลี่ยนตาม role -->
                        <label for="username">
                            <?= $selected_role === 'student' ? $t['student_id'] :
                               ($selected_role === 'teacher' ? $t['teacher_id'] : $t['staff_id']) ?>
                            <span class="required">*</span>
                        </label>
                        <input type="text" id="username" name="username" class="form-control"
                               placeholder="<?= $selected_role === 'student' ? $t['id_placeholder_student'] :
                                               ($selected_role === 'teacher' ? $t['id_placeholder_teacher'] : $t['id_placeholder_staff']) ?>"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fullname"><?= $t['fullname_full'] ?> <span class="required">*</span></label>
                        <input type="text" id="fullname" name="fullname" class="form-control"
                               placeholder="<?= $t['fullname_placeholder'] ?>"
                               value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email"><?= $t['email'] ?></label>
                        <input type="email" id="email" name="email" class="form-control"
                               placeholder="example@swu.ac.th"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone"><?= $t['phone'] ?></label>
                        <input type="text" id="phone" name="phone" class="form-control"
                               placeholder="08XXXXXXXX"
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password"><?= $t['password'] ?> <span class="required">*</span></label>
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="<?= $t['password_short'] ?>" required oninput="checkPassMatch()">
                    </div>
                    <div class="form-group">
                        <label for="password2"><?= $lang === 'en' ? 'Confirm Password' : 'ยืนยันรหัสผ่าน' ?> <span class="required">*</span></label>
                        <input type="password" id="password2" name="password2" class="form-control"
                               placeholder="<?= $lang === 'en' ? 'Re-enter password' : 'กรอกรหัสผ่านอีกครั้ง' ?>" required oninput="checkPassMatch()">
                        <span id="passMismatchMsg" style="display:none; color:#ef4444; font-size:0.82rem; margin-top:0.3rem; display:block;">
                            <?= $lang === 'en' ? 'Passwords do not match' : 'รหัสผ่านไม่ตรงกัน' ?>
                        </span>
                    </div>
                </div>

                <!-- ====================
                     ฟิลด์เฉพาะนิสิต
                     ==================== -->
                <?php if ($selected_role === 'student'): ?>

                <div class="section-title"><?= $t['student'] ?></div>

                <!-- ชื่อภาษาไทย (คำนำหน้า + ชื่อ + นามสกุล) -->
                <div class="form-row-3">
                    <div class="form-group">
                        <label for="prefix_th"><?= $t['prefix'] ?> (TH)</label>
                        <select id="prefix_th" name="prefix_th" class="form-control">
                            <option value="นาย"    <?= ($_POST['prefix_th'] ?? '') === 'นาย'    ? 'selected' : '' ?>>นาย</option>
                            <option value="นางสาว" <?= ($_POST['prefix_th'] ?? '') === 'นางสาว' ? 'selected' : '' ?>>นางสาว</option>
                            <option value="นาง"    <?= ($_POST['prefix_th'] ?? '') === 'นาง'    ? 'selected' : '' ?>>นาง</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="first_name_th"><?= $t['first_name'] ?> (TH)</label>
                        <input type="text" id="first_name_th" name="first_name_th" class="form-control"
                               placeholder="สมชาย"
                               value="<?= htmlspecialchars($_POST['first_name_th'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name_th"><?= $t['last_name'] ?> (TH)</label>
                        <input type="text" id="last_name_th" name="last_name_th" class="form-control"
                               placeholder="ใจดี"
                               value="<?= htmlspecialchars($_POST['last_name_th'] ?? '') ?>">
                    </div>
                </div>

                <!-- ชื่อภาษาอังกฤษ -->
                <div class="form-row-3">
                    <div class="form-group">
                        <label for="prefix_en"><?= $t['prefix'] ?> (EN)</label>
                        <select id="prefix_en" name="prefix_en" class="form-control">
                            <option value="Mr."  <?= ($_POST['prefix_en'] ?? '') === 'Mr.'  ? 'selected' : '' ?>>Mr.</option>
                            <option value="Miss" <?= ($_POST['prefix_en'] ?? '') === 'Miss' ? 'selected' : '' ?>>Miss</option>
                            <option value="Mrs." <?= ($_POST['prefix_en'] ?? '') === 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="first_name_en"><?= $t['first_name'] ?> (EN)</label>
                        <input type="text" id="first_name_en" name="first_name_en" class="form-control"
                               placeholder="Somchai"
                               value="<?= htmlspecialchars($_POST['first_name_en'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name_en"><?= $t['last_name'] ?> (EN)</label>
                        <input type="text" id="last_name_en" name="last_name_en" class="form-control"
                               placeholder="Jaidee"
                               value="<?= htmlspecialchars($_POST['last_name_en'] ?? '') ?>">
                    </div>
                </div>

                <!-- หลักสูตร/ภาค — แบ่งชัดระหว่างภาคปกติและภาคพิเศษ -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="major"><?= $t['major_select'] ?> <span class="required">*</span></label>
                        <select id="major" name="major" class="form-control">
                            <?php
                            // วน loop แสดงตัวเลือกหลักสูตรสารสนเทศศึกษา (ภาคปกติ + ภาคพิเศษ)
                            $sel_major = $_POST['major'] ?? $majors_is[0]['value'];
                            foreach ($majors_is as $m):
                            ?>
                            <option value="<?= htmlspecialchars($m['value']) ?>"
                                    <?= $sel_major === $m['value'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['label']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="academic_year"><?= $t['academic_year_entry'] ?></label>
                        <select id="academic_year" name="academic_year" class="form-control">
                            <option value=""><?= $t['select_year'] ?></option>
                            <?php
                            // สร้างตัวเลือกปีการศึกษาย้อนหลัง 6 ปี
                            $current_year = (int)date('Y') + 543; // แปลงเป็น พ.ศ.
                            for ($y = $current_year; $y >= $current_year - 5; $y--):
                            ?>
                            <option value="<?= $y ?>" <?= ($_POST['academic_year'] ?? '') == $y ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <!-- กล่องข้อมูลประเภทภาค (แสดงแบบ dynamic ตาม dropdown ที่เลือก) -->
                <div id="student-type-info" class="student-type-badge student-type-regular">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span id="student-type-text"><?= $t['regular_desc'] ?></span>
                </div>

                <!-- ====================
                     ฟิลด์เฉพาะอาจารย์
                     ==================== -->
                <?php elseif ($selected_role === 'teacher'): ?>

                <div class="section-title"><?= $t['role_teacher'] ?></div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="position"><?= $t['position'] ?> (TH)</label>
                        <input type="text" id="position" name="position" class="form-control"
                               placeholder="<?= $t['fullname_placeholder'] ?>"
                               value="<?= htmlspecialchars($_POST['position'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="education"><?= $t['education'] ?></label>
                        <input type="text" id="education" name="education" class="form-control"
                               placeholder="<?= $t['fullname_placeholder'] ?>"
                               value="<?= htmlspecialchars($_POST['education'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name_en">Full Name (English)</label>
                        <input type="text" id="full_name_en" name="full_name_en" class="form-control"
                               placeholder="Lecturer Somchai Jaidee, Ph.D."
                               value="<?= htmlspecialchars($_POST['full_name_en'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="position_en">Position (English)</label>
                        <select id="position_en" name="position_en" class="form-control">
                            <?php foreach (['Program Chair','Program Secretary','Committee Member','Lecturer'] as $p): ?>
                            <option value="<?= $p ?>" <?= ($_POST['position_en'] ?? '') === $p ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="detail_en"><?= $t['details_en'] ?></label>
                    <textarea id="detail_en" name="detail_en" class="form-control" rows="2"
                              placeholder="<?= $t['details_placeholder'] ?>"><?= htmlspecialchars($_POST['detail_en'] ?? '') ?></textarea>
                </div>

                <!-- ====================
                     ฟิลด์เฉพาะเจ้าหน้าที่
                     ==================== -->
                <?php elseif ($selected_role === 'staff'): ?>

                <div class="section-title"><?= $t['role_staff'] ?></div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="position"><?= $t['position'] ?></label>
                        <select id="position" name="position" class="form-control">
                            <?php foreach (['Admin','Officer','Coordinator'] as $p): ?>
                            <option value="<?= $p ?>" <?= ($_POST['position'] ?? '') === $p ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="department"><?= $t['department'] ?></label>
                        <input type="text" id="department" name="department" class="form-control"
                               placeholder="e.g. Internship Office"
                               value="<?= htmlspecialchars($_POST['department'] ?? 'Internship Office') ?>">
                    </div>
                </div>

                <?php endif; /* สิ้นสุดการแสดงฟิลด์ตาม role */ ?>

                <!-- ปุ่มสมัครสมาชิก -->
                <button type="submit" class="btn btn-primary btn-register" id="submitBtn">
                    <span><?= $t['register'] ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>

                <p class="form-note"><?= $t['register_agreement'] ?></p>

            </form>

            <?php endif; /* สิ้นสุด STEP 1/2 */ ?>
            <?php endif; /* สิ้นสุด $success else block */ ?>

        </div><!-- /.register-card -->
    </div><!-- /.register-panel -->

</div><!-- /.register-wrapper -->

<!-- ==================== JavaScript ====================
     อัปเดต badge แสดงประเภทภาค (ปกติ/พิเศษ) แบบ dynamic
     ตาม dropdown หลักสูตรที่ผู้ใช้เลือก
-->
<script>
(function() {
    var majorSelect = document.getElementById('major');
    var typeInfo    = document.getElementById('student-type-info');
    var typeText    = document.getElementById('student-type-text');

    // ถ้าไม่มี major dropdown (เช่นหน้า role อื่น) ให้ออกไปเลย
    if (!majorSelect || !typeInfo) return;

    /**
     * updateTypeBadge()
     * อ่านค่า option ที่เลือก — ถ้ามีคำว่า "ภาคพิเศษ" ให้เปลี่ยนสี badge เป็นสีส้ม
     * มิฉะนั้นใช้สีน้ำเงิน (ภาคปกติ)
     */
    function updateTypeBadge() {
        var val = majorSelect.value;
        if (val.indexOf('ภาคพิเศษ') !== -1) {
            typeInfo.className = 'student-type-badge student-type-special';
            typeText.textContent = 'ภาคพิเศษ (Special / Evening Program)';
        } else {
            typeInfo.className = 'student-type-badge student-type-regular';
            typeText.textContent = 'ภาคปกติ (Regular Program)';
        }
    }

    // รันครั้งแรกเมื่อโหลดหน้า และรันทุกครั้งที่เปลี่ยน dropdown
    updateTypeBadge();
    majorSelect.addEventListener('change', updateTypeBadge);
})();

// Real-time password match check
function checkPassMatch() {
    var p1  = document.getElementById('password');
    var p2  = document.getElementById('password2');
    var msg = document.getElementById('passMismatchMsg');
    if (!p1 || !p2 || !msg) return;
    if (p2.value.length === 0) {
        msg.style.display = 'none';
        p2.style.borderColor = '';
        return;
    }
    if (p1.value !== p2.value) {
        msg.style.display = 'block';
        p2.style.borderColor = '#ef4444';
    } else {
        msg.style.display = 'none';
        p2.style.borderColor = '#22c55e';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
