<?php
/**
 * dashboard.php — แผงควบคุมหลัก (Role-based Dashboard)
 * แสดงข้อมูลแตกต่างกันตาม role: student / teacher / staff
 *
 * โครงสร้าง:
 *   1. โหลด session และตรวจสอบว่า login อยู่หรือไม่ (checkLogin)
 *   2. ดึงสถิติ (total/pending/approved/rejected) จากฐานข้อมูลตาม role
 *   3. ๆนสิต = ดึงโปรไฟล์ + คำขอฝึกงาน + URL รูปโปรไฟล์
 *   4. ดึงรายการล่าสุด (recent_rows) อาจเป็นของๆนสิต/อาจารย์/เจ้าหน้าที่
 */
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/auth.php';

// ตรวจสอบว่า login อยู่เสมอ — ถ้าไม่ login จะ redirect ไป login.php
checkLogin();

// โหลดตัวแปรภาษาเป็นอันดับแรก: URL param ?lang=, หรือ session, หรือค่าเริ่มต้น 'th'
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;       // จำเลือกภาษาไว้ใน session เพื่อใช้ช่วงต่อไป
$t = require "lang/{$lang}.php"; // โหลด array ข้อความแปลทั้งหมดลงใน $t
$role = $_SESSION['role'];     // student | teacher | staff
$user_id = $_SESSION['user_id'];  // รหัสนิสิต/username

// ==================== Stats ====================
// นับจำนวนคำขอแยกตามสถานะ แตกโลจิกตาม role
$total = 0; // คำขอทั้งหมด
$pending = 0; // รออนุมัติ
$approved = 0; // อนุมัติแล้ว
$rejected = 0; // ปฏิเสธ
try {
    if ($role === 'staff') {
        // staff เห็นทุกคำขอในระบบ
        $stmt = $conn->query("SELECT status, COUNT(*) as count FROM internship_requests GROUP BY status");
    } elseif ($role === 'teacher') {
        // teacher เห็นเฉพาะๆนสิตในความดูแล (advisor_id = user_id ของอาจารย์)
        $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM internship_requests WHERE advisor_id = ? GROUP BY status");
        $stmt->execute([$user_id]);
    } else {
        // student เห็นเฉพาะคำขอของตัวเอง
        $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM internship_requests WHERE student_id = ? GROUP BY status");
        $stmt->execute([$user_id]);
    }
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $total += $row['count'];
        if ($row['status'] == 0) $pending  = $row['count'];
        if ($row['status'] == 1) $pending  += $row['count'];
        if ($row['status'] == 2) $approved  = $row['count'];
        if ($row['status'] == 3) $approved  += $row['count'];
        if ($row['status'] == 4) $approved  += $row['count'];
        if ($row['status'] == 9) $rejected  = $row['count'];
    }
} catch (PDOException $e) {
    // หากมีข้อผิดพลาด ให้แสดง 0 ทั้งหมด (graceful fail)
}

// ==================== Student profile + internship ====================
// ดึงโปรไฟล์นิสิตและคำขอฝึกงาน (สำหรับ student role เท่านั้น)
$student_info = null;
$internship = null;
$profile_photo_url = null; // URL รูปโปรไฟล์ (null = ยังไม่มีรูป)
if ($role === 'student') {
    try {
        // ดึงข้อมูลโปรไฟล์จากตาราง students_info
        $si = $conn->prepare("SELECT * FROM students_info WHERE student_id = ?");
        $si->execute([$user_id]);
        $student_info = $si->fetch(PDO::FETCH_ASSOC);

        // ตรวจสอบรูปโปรไฟล์จาก DB ว่าไฟล์มีอยู่จริงใน filesystem
        if ($student_info && !empty($student_info['profile_photo'])) {
            $photoPath = __DIR__ . '/' . $student_info['profile_photo'];
            if (file_exists($photoPath)) {
                // เพิ่ม ?v=timestamp เพื่อบังคับ browser cache ให้ refresh
                $profile_photo_url = $student_info['profile_photo'] . '?v=' . filemtime($photoPath);
            }
        }

        // ดึงคำขอล่าสุด (เรียงตามวันที่ยื่น เอาอันแรกอย่างเดียว)
        $ir = $conn->prepare("
            SELECT r.*, u.fullname AS advisor_name
            FROM internship_requests r
            LEFT JOIN users u ON r.advisor_id = u.username
            WHERE r.student_id = ?
            ORDER BY r.request_date DESC LIMIT 1
        ");
        $ir->execute([$user_id]);
        $internship = $ir->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
    }
}

// ==================== Recent rows ====================
// ดึงรายการล่าสุด 8 รายการ แตกตาม role ว่าจะดึงข้อมูลของใคร
$recent_rows = [];
try {
    if ($role === 'staff') {
        // staff เห็นคำขอทั้งหมดในระบบ พร้อมชื่อ+สาขาของนิสิต
        $recent_q = $conn->query("
            SELECT r.*, u.fullname, si.major, si.student_type
            FROM internship_requests r
            JOIN users u ON r.student_id = u.username
            LEFT JOIN students_info si ON r.student_id = si.student_id
            ORDER BY r.request_date DESC LIMIT 8
        ");
    } elseif ($role === 'teacher') {
        // teacher เห็นเฉพาะฆนีสิตในความดูแลของตน
        $recent_q = $conn->prepare("
            SELECT r.*, u.fullname, si.major
            FROM internship_requests r
            JOIN users u ON r.student_id = u.username
            LEFT JOIN students_info si ON r.student_id = si.student_id
            WHERE r.advisor_id = ?
            ORDER BY r.request_date DESC LIMIT 8
        ");
        $recent_q->execute([$user_id]);
    } else {
        // student เห็นคำขอของตัวเอง พร้อมชื่ออาจารย์ที่นิเทศ
        $recent_q = $conn->prepare("
            SELECT r.*, u.fullname AS advisor_name
            FROM internship_requests r
            LEFT JOIN users u ON r.advisor_id = u.username
            WHERE r.student_id = ?
            ORDER BY r.request_date DESC LIMIT 8
        ");
        $recent_q->execute([$user_id]);
    }
    $recent_rows = $recent_q->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

// ==================== Helpers ====================
// Map สถานะตัวเลข -> ข้อความ + สีที่จะนำไปแสดงใน UI
// statusMap: map status int → label + color — Workflow 5 ขั้นตอน
$statusMap = [
    0 => ['label' => $t['status_0'], 'class' => 'pending',  'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.1)'],
    1 => ['label' => $t['status_1'], 'class' => 'info',     'color' => '#06b6d4', 'bg' => 'rgba(6,182,212,0.1)'],
    2 => ['label' => $t['status_2'], 'class' => 'approved',  'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.1)'],
    3 => ['label' => $t['status_3'], 'class' => 'letter',   'color' => '#6366f1', 'bg' => 'rgba(99,102,241,0.1)'],
    4 => ['label' => $t['status_4'], 'class' => 'done',     'color' => '#10b981', 'bg' => 'rgba(16,185,129,0.1)'],
    9 => ['label' => $t['status_9'], 'class' => 'rejected', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.1)'],
];
// Map สถานะเอกสาร (doc_status) -> ข้อความ + ไอคอน
$docMap = [
    0 => ['label' => $t['doc_none'], 'color' => '#9ca3af', 'icon' => '○'],
    1 => ['label' => $t['doc_sent'], 'color' => '#f59e0b', 'icon' => '◐'],
    2 => ['label' => $t['doc_checked'], 'color' => '#22c55e', 'icon' => '●'],
];

// แปลง role code -> ข้อความภาษาไทย/อังกฤษจาก $t
$roleName = ['student' => $t['role_student'], 'teacher' => $t['role_teacher'], 'staff' => $t['role_staff']];

// ตั้ง title และโหลด CSS ของ dashboard จากไฟล์ภายนอก (dashboard.css)
// header.php จะ inject <link> อัตโนมัติเมื่อเห็น $extra_css
$page_title = 'Dashboard';
$extra_css = 'dashboard.css'; // โหลด css/dashboard.css ผ่าน header.php
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>


<!-- ============================================================
     HTML ส่วนหน้า Dashboard เริ่มต้นที่นี่
     CSS ทั้งหมดอยู่ใน css/dashboard.css (โหลดผ่าน header.php)
     ============================================================ -->

<div class="db-layout">

    <!-- ===================== SIDEBAR ===================== -->
    <aside class="db-sidebar">

        <!-- User Card: avatar วงกลม (คลิกได้เพื่อเปลี่ยนรูปโปรไฟล์) -->
        <div class="db-user-card">
            <!-- ถ้าเป็น student ลิงก์ avatar ไปหน้าอัปโหลดรูป ถ้าไม่ใช่ก็เป็นแค่ div -->
            <a href="student/upload_profile.php" class="db-avatar-lg" title="<?= $t['edit_profile'] ?>">
                <?php if ($profile_photo_url): ?>
                    <!-- มีรูปโปรไฟล์: แสดงรูป -->
                    <img src="<?= htmlspecialchars($profile_photo_url) ?>" alt="<?= $t['profile_photo'] ?>">
                <?php else: ?>
                    <!-- ไม่มีรูป: แสดงตัวอักษรแรกของชื่อแทน -->
                    <?= mb_strtoupper(mb_substr($_SESSION['fullname'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                <?php endif; ?>
            </a>
            <div class="db-user-meta">
                <div class="db-user-fullname"><?= htmlspecialchars($_SESSION['fullname']) ?></div>
                <div class="db-user-id-text"><?= htmlspecialchars($user_id) ?></div>
                <span class="db-role-chip db-role-<?= $role ?>"><?= $roleName[$role] ?? $role ?></span>
            </div>
        </div>

        <!-- Nav -->
        <nav class="db-nav">
            <a href="dashboard.php" class="db-nav-link active">
                <span class="db-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                </span>
                <?= $t['stats_overview'] ?>
            </a>

            <?php if ($role === 'student'): ?>
                <a href="student/register.php" class="db-nav-link <?= $internship ? 'db-nav-disabled' : '' ?>">
                    <span class="db-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="12" y1="18" x2="12" y2="12" />
                            <line x1="9" y1="15" x2="15" y2="15" />
                        </svg>
                    </span>
                    ยื่นคำขอฝึกงาน
                    <?php if ($internship): ?>
                        <span class="db-nav-badge">✓</span>
                    <?php endif; ?>
                </a>
                <a href="student/view_status.php" class="db-nav-link">
                    <span class="db-nav-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                    </span>
                    <?= $t['all_docs'] ?>
                </a>
                <a href="student/upload.php" class="db-nav-link">
                    <span class="db-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="16 16 12 12 8 16" />
                            <line x1="12" y1="12" x2="12" y2="21" />
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                        </svg>
                    </span>
                    อัปโหลดเอกสาร
                </a>

            <?php elseif ($role === 'teacher'): ?>
                <a href="teacher/view_students.php" class="db-nav-link">
                    <span class="db-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                    นิสิตในความดูแล
                </a>

            <?php elseif ($role === 'staff'): ?>
                <a href="staff/view_all.php" class="db-nav-link">
                    <span class="db-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg>
                    </span>
                    รายการทั้งหมด
                </a>
                <a href="staff/manage_documents.php" class="db-nav-link">
                    <span class="db-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                    </span>
                    จัดการเอกสาร
                </a>
            <?php endif; ?>

            <p class="db-nav-section" style="margin-top:auto; padding-top:1rem;"><?= $t['account'] ?></p>
            <?php if ($role === 'student'): ?>
                <a href="student/upload_profile.php" class="db-nav-link">
                    <span class="db-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </span>
                    <?= $t['edit_profile'] ?>
                    <?php if (!$profile_photo_url): ?>
                        <span class="db-nav-badge" style="background:#f59e0b;">+รูป</span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <a href="logout.php" class="db-nav-link db-nav-logout">
                <span class="db-nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </span>
                ออกจากระบบ
            </a>
        </nav>
    </aside>

    <!-- ===================== MAIN ===================== -->
    <main class="db-main">

        <!-- Page heading -->
        <div class="db-topbar">
            <div class="db-topbar-left">
                <h1 class="db-heading">
                    <?php
                    $greeting_hour = (int) date('H');
                    if ($greeting_hour < 12)
                        echo 'อรุณสวัสดิ์';
                    elseif ($greeting_hour < 17)
                        echo 'สวัสดีตอนบ่าย';
                    else
                        echo 'สวัสดีตอนเย็น';
                    ?>, <span
                        style="color:var(--primary-color);"><?= htmlspecialchars(explode(' ', $_SESSION['fullname'])[0]) ?></span>
                </h1>
                <p class="db-subheading"><?= date('วันl ที่ j F Y', time()) ?> &nbsp;·&nbsp;
                    <?= $roleName[$role] ?? '' ?>
                </p>
            </div>
            <?php if ($role === 'student' && !$internship): ?>
                <a href="student/register.php" class="db-cta-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    ยื่นคำขอฝึกงาน
                </a>
            <?php endif; ?>
        </div>

        <!-- ========== STAT CARDS ========== -->
        <div class="db-stats-row">
            <?php
            $stats = [
                ['label' => $t['total_requests'], 'value' => $total, 'color' => '#6366f1', 'bg' => 'rgba(99,102,241,0.1)', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                ['label' => $t['status_pending'], 'value' => $pending, 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.1)', 'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
                ['label' => $t['status_approved'], 'value' => $approved, 'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.1)', 'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
                ['label' => $t['status_rejected'], 'value' => $rejected, 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.1)', 'icon' => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'],
            ];
            foreach ($stats as $s): ?>
                <div class="db-stat" style="--stat-color:<?= $s['color'] ?>; --stat-bg:<?= $s['bg'] ?>;">
                    <div class="db-stat-icon-wrap">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2"><?= $s['icon'] ?></svg>
                    </div>
                    <div class="db-stat-body">
                        <div class="db-stat-num"><?= $s['value'] ?></div>
                        <div class="db-stat-lbl"><?= $s['label'] ?></div>
                    </div>
                    <div class="db-stat-accent"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ========== STUDENT WITH INTERNSHIP ========== -->
        <?php if ($role === 'student' && $internship): ?>

            <div class="db-two-col">

                <!-- LEFT: Internship card -->
                <div class="db-card db-card-internship">

                    <!-- Company Banner -->
                    <div class="db-company-banner">
                        <div class="db-company-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                            </svg>
                        </div>
                        <div class="db-company-info">
                            <div class="db-company-name"><?= htmlspecialchars($internship['company_name']) ?></div>
                            <?php if ($internship['company_province']): ?>
                                <div class="db-company-sub">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    <?= htmlspecialchars($internship['company_province']) ?>
                                    <?php if ($internship['department']): ?>
                                        &nbsp;·&nbsp; <?= htmlspecialchars($internship['department']) ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                        $st = $internship['status'] ?? 1;
                        $sm = $statusMap[$st] ?? $statusMap[1];
                        ?>
                        <span class="db-status-pill"
                            style="background:<?= $sm['bg'] ?>;color:<?= $sm['color'] ?>;border-color:<?= $sm['color'] ?>30;">
                            <span class="db-status-dot" style="background:<?= $sm['color'] ?>;"></span>
                            <?= $sm['label'] ?>
                        </span>
                    </div>

                    <!-- Timeline ระยะเวลา -->
                    <?php if ($internship['internship_start']): ?>
                        <?php
                        $startTs = strtotime($internship['internship_start']);
                        $endTs = strtotime($internship['internship_end']);
                        $nowTs = time();
                        $totalDays = max(1, ($endTs - $startTs) / 86400);
                        $passedDays = max(0, min($totalDays, ($nowTs - $startTs) / 86400));
                        $progress = round($passedDays / $totalDays * 100);
                        $daysLeft = max(0, ceil(($endTs - $nowTs) / 86400));
                        $weeks = round($totalDays / 7, 1);
                        ?>
                        <div class="db-timeline">
                            <div class="db-timeline-dates">
                                <div class="db-tdate">
                                    <div class="db-tdate-label">วันเริ่มต้น</div>
                                    <div class="db-tdate-val"><?= date('d M Y', $startTs) ?></div>
                                </div>
                                <div class="db-tdate-mid">
                                    <div class="db-tdate-remain" style="color:var(--primary-color);">
                                        <?= $totalDays ?> วัน (<?= $weeks ?> สัปดาห์)
                                    </div>
                                    <?php if ($internship['work_hours']): ?>
                                        <div class="db-tdate-hours"><?= htmlspecialchars($internship['work_hours']) ?> ·
                                            <?= htmlspecialchars($internship['work_days'] ?? '') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="db-tdate" style="text-align:right;">
                                    <div class="db-tdate-label">วันสิ้นสุด</div>
                                    <div class="db-tdate-val"><?= date('d M Y', $endTs) ?></div>
                                </div>
                            </div>
                            <div class="db-progress-track">
                                <div class="db-progress-fill" style="width:<?= $progress ?>%;"></div>
                            </div>
                            <div class="db-timeline-footer">
                                <span><?= $progress ?>% ผ่านไปแล้ว</span>
                                <span><?= $daysLeft > 0 ? "เหลืออีก {$daysLeft} วัน" : "สิ้นสุดแล้ว" ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Detail rows -->
                    <div class="db-detail-rows">
                        <?php
                        $details = [];
                        if ($internship['contact_person']) {
                            $contactVal = htmlspecialchars($internship['contact_person']);
                            if ($internship['contact_position'])
                                $contactVal .= ' <span class="db-detail-note">(' . htmlspecialchars($internship['contact_position']) . ')</span>';
                            if ($internship['contact_phone'])
                                $contactVal .= ' &nbsp;<a href="tel:' . htmlspecialchars($internship['contact_phone']) . '" class="db-detail-link">' . htmlspecialchars($internship['contact_phone']) . '</a>';
                            $details[] = [$t['contact_person'] ?? 'ผู้ติดต่อ', $contactVal, '#06b6d4', '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'];
                        }
                        if ($internship['company_address']) {
                            $details[] = [$t['address'] ?? 'ที่อยู่', htmlspecialchars($internship['company_address']), '#ec4899', '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>'];
                        }
                        if ($internship['company_website']) {
                            $details[] = [$t['website'], '<a href="' . htmlspecialchars($internship['company_website']) . '" target="_blank" class="db-detail-link">' . htmlspecialchars($internship['company_website']) . '</a>', '#8b5cf6', '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'];
                        }
                        if ($internship['advisor_name'] ?? null) {
                            $details[] = [$t['supervisor'] ?? 'อาจารย์นิเทศ', htmlspecialchars($internship['advisor_name']), '#10b981', '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>'];
                        }
                        foreach ($details as [$label, $val, $color, $svgPath]):
                            ?>
                            <div class="db-detail-row">
                                <div class="db-detail-icon" style="color:<?= $color ?>;background:<?= $color ?>15;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2"><?= $svgPath ?></svg>
                                </div>
                                <div class="db-detail-content">
                                    <div class="db-detail-label"><?= $label ?></div>
                                    <div class="db-detail-val"><?= $val ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Doc Status Footer -->
                    <?php
                    $ds = $internship['doc_status'] ?? 0;
                    $dm = $docMap[$ds] ?? $docMap[0];
                    ?>
                    <div class="db-card-footer">
                        <div class="db-doc-info">
                            <span class="db-doc-dot-sm" style="background:<?= $dm['color'] ?>;"></span>
                            <span style="color:<?= $dm['color'] ?>;font-weight:700;"><?= $dm['label'] ?></span>
                            <?php if ($internship['remark']): ?>
                                <span class="db-remark-inline">— <?= htmlspecialchars($internship['remark']) ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="student/upload.php" class="db-upload-link">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="16 16 12 12 8 16" />
                                <line x1="12" y1="12" x2="12" y2="21" />
                                <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                            </svg>
                            อัปโหลดเอกสาร
                        </a>
                    </div>
                </div>

                <!-- RIGHT: Profile card — แสดงข้อมูลนิสิต/ผู้ใช้ -->
                <div class="db-card db-card-profile">
                    <div class="db-profile-head">
                        <!-- ไอคอน User แทนรูปโปรไฟล์ -->
                        <div class="db-profile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <!-- ชื่อผู้ใช้จาก session -->
                            <div class="db-profile-name">
                                <?= htmlspecialchars($_SESSION['fullname']) ?>
                            </div>
                            <!-- รหัสนิสิต/บุคลากร -->
                            <div class="db-profile-id">
                                <?= htmlspecialchars($user_id) ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($student_info):
                        $ptypes = ['regular' => 'ภาคปกติ', 'special' => 'ภาคพิเศษ'];
                        $ptypes = ['regular' => $t['regular'], 'coop' => $t['coop']];
                        $rows = [
                            [$t['name_th'], trim(($student_info['prefix_th'] ?? '') . ' ' . ($student_info['first_name_th'] ?? '') . ' ' . ($student_info['last_name_th'] ?? ''))],
                            [$t['name_en'], trim(($student_info['prefix_en'] ?? '') . ' ' . ($student_info['first_name_en'] ?? '') . ' ' . ($student_info['last_name_en'] ?? ''))],
                            [$t['major'], $student_info['major'] ?? ''],
                            [$t['student_type'], $ptypes[$student_info['student_type'] ?? 'regular'] ?? ''],
                            [$t['academic_year'], $student_info['academic_year'] ?? ''],
                            [$t['email'], $student_info['email'] ?? ''],
                            [$t['phone'], $student_info['phone'] ?? ''],
                        ];
                        ?>
                        <ul class="db-profile-list">
                            <?php foreach ($rows as [$k, $v]):
                                if (!trim($v))
                                    continue; ?>
                                <li class="db-profile-item">
                                    <span class="db-profile-key">
                                        <?= $k ?>
                                    </span>
                                    <span class="db-profile-val">
                                        <?= htmlspecialchars($v) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($role === 'student' && !$internship): ?>
            <!-- Empty state: ยังไม่ยื่น -->
            <div class="db-empty-banner">
                <div class="db-empty-art">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"
                        opacity=".35">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="12" y1="18" x2="12" y2="12" />
                        <line x1="9" y1="15" x2="15" y2="15" />
                    </svg>
                </div>
                <h3 class="db-empty-title"><?= $t['no_requests'] ?></h3>
                <p class="db-empty-desc"><?= $t['register_intro'] ?></p>
                <a href="student/register.php" class="db-cta-btn" style="margin-top:1.25rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <?= $t['register_internship'] ?>
                </a>
            </div>
        <?php endif; ?>

        <!-- ========== RECENT TABLE ========== -->
        <div class="db-card db-card-table">
            <div class="db-card-head">
                <div class="db-card-head-left">
                    <h2 class="db-card-title">
                        <?php
                        if ($role === 'staff')
                            echo $t['recent_requests_system'];
                        elseif ($role === 'teacher')
                            echo $t['students_in_care'];
                        else
                            echo $t['request_history'];
                        ?>
                    </h2>
                    <?php if (count($recent_rows) > 0): ?>
                        <span class="db-card-count"><?= count($recent_rows) ?>     <?= $t['items'] ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($role === 'staff'): ?>
                    <a href="staff/view_all.php" class="db-see-all-link"><?= $t['view_all'] ?> <svg width="13" height="13"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6" />
                        </svg></a>
                <?php endif; ?>
            </div>

            <?php if (count($recent_rows) > 0): ?>
                <div class="db-scroll-x">
                    <table class="db-tbl">
                        <thead>
                            <tr>
                                <?php if ($role !== 'student'): ?>
                                    <th><?= $t['student'] ?></th>
                                <?php endif; ?>
                                <th><?= $t['company'] ?></th>
                                <th><?= $t['period'] ?></th>
                                <th><?= $t['status'] ?></th>
                                <th><?= $t['file'] ?></th>
                                <th><?= $t['date_submitted'] ?></th>
                                <?php if ($role !== 'student'): ?>
                                    <th><?= $t['manage'] ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_rows as $r):
                                $sm = $statusMap[$r['status']] ?? $statusMap[1];
                                $dm = $docMap[$r['doc_status'] ?? 0] ?? $docMap[0];
                                ?>
                                <tr>
                                    <?php if ($role !== 'student'): ?>
                                        <td>
                                            <div class="db-tbl-name"><?= htmlspecialchars($r['fullname']) ?></div>
                                            <div class="db-tbl-sub"><?= htmlspecialchars($r['student_id']) ?></div>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <div class="db-tbl-name"><?= htmlspecialchars($r['company_name']) ?></div>
                                        <?php if ($r['company_province'] ?? null): ?>
                                            <div class="db-tbl-sub"><?= htmlspecialchars($r['company_province']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="db-tbl-period">
                                        <?php if ($r['internship_start'] ?? null): ?>
                                            <?= date('d/m/Y', strtotime($r['internship_start'])) ?>
                                            <span class="db-tbl-dash">–</span>
                                            <?= date('d/m/Y', strtotime($r['internship_end'])) ?>
                                        <?php else:
                                            echo '<span class="db-tbl-dash">—</span>';
                                        endif; ?>
                                    </td>
                                    <td>
                                        <span class="db-pill"
                                            style="background:<?= $sm['bg'] ?>;color:<?= $sm['color'] ?>;border-color:<?= $sm['color'] ?>30;">
                                            <span class="db-pill-dot" style="background:<?= $sm['color'] ?>;"></span>
                                            <?= $sm['label'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            style="color:<?= $dm['color'] ?>;font-size:0.8rem;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                                            <span
                                                style="width:6px;height:6px;border-radius:50%;background:<?= $dm['color'] ?>;flex-shrink:0;"></span>
                                            <?= $dm['label'] ?>
                                        </span>
                                    </td>
                                    <td class="db-tbl-date"><?= date('d M Y', strtotime($r['request_date'])) ?></td>
                                    <?php if ($role !== 'student'): ?>
                                        <td>
                                            <?php
                                            $rid = $r['request_id'];
                                            if ($role === 'staff') {
                                                echo '<a href="staff/update_status.php?id='.$rid.'" class="btn btn-primary" style="padding:0.35rem 0.75rem;font-size:0.75rem;text-decoration:none;border-radius:6px;">จัดการ</a>';
                                            } elseif ($role === 'teacher') {
                                                echo '<a href="teacher/view_students.php?id='.$rid.'" class="btn btn-primary" style="padding:0.35rem 0.75rem;font-size:0.75rem;text-decoration:none;border-radius:6px;background:#22c55e;">อนุมัติ</a>';
                                            }
                                            ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="db-tbl-empty">ยังไม่มีข้อมูลคำขอ</div>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php require_once 'includes/footer.php'; ?>