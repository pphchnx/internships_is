<?php
/**
 * students.php — หน้าแสดงรายชื่อนิสิตปัจจุบัน (ต้อง Login ก่อน)
 * ปีคงที่: 65=ปี4, 66=ปี3, 67=ปี2(มีภาคพิเศษ), 68=ปี1(มีภาคพิเศษ)
 */
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// ---- ต้อง Login ก่อนเสมอ ----
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "lang/{$lang}.php";

$page_title = 'รายชื่อนิสิตปัจจุบัน | สารสนเทศศึกษา มศว';
$extra_css = 'students.css';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// ---- ปีคงที่ (67/68 = มีภาคพิเศษ) ----
$year_entry_map = [4 => '65', 3 => '66', 2 => '67', 1 => '68'];
$has_section_yr = ['67', '68']; // ปีที่มีภาคพิเศษ

// ---- ดึงนิสิตทั้งหมด ----
$all_students = [];
try {
    $stmt = $conn->query("
        SELECT si.*, u.status
        FROM students_info si
        JOIN users u ON u.username = si.student_id
        WHERE u.role = 'student' AND u.status = 'active'
        ORDER BY si.student_id ASC
    ");
    $all_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

// ---- จัดกลุ่ม: by_year[ปี][section] ----
// section = 'regular' หรือ 'special'
// ตอนนี้ยังไม่มี field แยกในDB → แบ่งจาก digit ที่ 3 ของรหัส
// ภาคปกติ: digit 3 = 1-5 (นิสิตรหัสปกติ)
// ภาคพิเศษ: digit 3 = 6-9 (สำรองสำหรับอนาคต, ตอนนี้รวมไว้ก่อน)
// หมายเหตุ: admin สามารถขยายลอจิกนี้ได้ภายหลัง
$by_year = [];
foreach ($year_entry_map as $yr => $code) {
    $by_year[$yr] = ['regular' => [], 'special' => []];
}
foreach ($all_students as $s) {
    $prefix = substr($s['student_id'], 0, 2);
    foreach ($year_entry_map as $yr => $code) {
        if ($prefix === $code) {
            // ใช้ข้อมูลจากคอลัมน์ student_type ใน DB: 'regular' = ภาคปกติ, 'special' = ภาคพิเศษ
            // กรณีไม่มีข้อมูล (Null หรือไม่ตรง) ให้ถือเป็นภาคปกติ
            $section = ($s['student_type'] === 'special') ? 'special' : 'regular';
            $by_year[$yr][$section][] = $s;
            break;
        }
    }
}

// ---- ค่า active year จาก query string ----
$active_year = isset($_GET['year']) ? (int) $_GET['year'] : 4;
if (!array_key_exists($active_year, $year_entry_map))
    $active_year = 4;

$total = count($all_students);
$accents = [1 => '#4f4f4fb6', 2 => '#e50914', 3 => '#4f4f4fb6', 4 => '#e50914'];
?>

<!-- HERO -->
<section class="students-hero">
    <div class="students-hero-content">
        <div class="students-hero-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                <path d="M6 12v5c3 3 9 3 12 0v-5" />
            </svg>
            <?= $t['is_swu'] ?>
        </div>
        <h1><?= $t['current_students_list'] ?></h1>
        <p><?= $t['students_1_to_4'] ?></p>
        <div class="students-hero-stats">
            <div class="hero-stat">
                <span class="hero-stat-num"><?= $total ?></span>
                <span class="hero-stat-label"><?= $t['total_students'] ?></span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-num">4</span>
                <span class="hero-stat-label"><?= $t['year_level'] ?></span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-num"><?= date('Y') + 543 ?></span>
                <span class="hero-stat-label"><?= $t['academic_year_buddhist'] ?></span>
            </div>
        </div>
    </div>
</section>

<!-- MAIN -->
<main class="students-main">
    <div class="container">

        <!-- Year Tabs -->
        <div class="year-tabs">
            <?php foreach ($year_entry_map as $yr => $code):
                $total_yr = count($by_year[$yr]['regular']) + count($by_year[$yr]['special']);
                $ac = $accents[$yr];
                ?>
                <button class="year-tab <?= $yr === $active_year ? 'active' : '' ?>" onclick="showYear(<?= $yr ?>)"
                    id="tab-<?= $yr ?>" style="<?= $yr === $active_year ? "--tab-color:{$ac}" : '' ?>">
                    <?= $t['year_level_prefix'] ?> <?= $yr ?>
                    <span class="tab-count"
                        style="<?= $yr === $active_year ? "background:{$ac}" : '' ?>"><?= $total_yr ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Year Panels -->
        <?php foreach ($year_entry_map as $yr => $code):
            $ac = $accents[$yr];
            $has_sec = in_array($code, $has_section_yr);
            $students_regular = $by_year[$yr]['regular'];
            $students_special = $by_year[$yr]['special'];
            $total_yr = count($students_regular) + count($students_special);
            ?>
            <div class="year-panel" id="panel-<?= $yr ?>" <?= $yr !== $active_year ? 'style="display:none;"' : '' ?>>

                <!-- Year Header -->
                <div class="year-header" style="border-left-color:<?= $ac ?>;">
                    <div class="year-header-left">
                        <div class="year-badge" style="background:<?= $ac ?>;"><?= $t['year_prefix'] ?> <?= $yr ?></div>
                        <div>
                            <h2 class="year-title">
                                <?= $t['year_level_prefix'] ?> <?= $yr ?>
                                <span class="year-code-hint">(<?= $t['student_code_prefix'] ?> <?= $code ?>xx)</span>
                            </h2>
                            <p class="year-subtitle">
                                <?= $t['students_entry_year'] ?> <?= $code + 2500 ?>
                                <?php if ($yr === 4): ?>
                                    · <span class="graduating-badge">🎓 <?= $t['graduating_soon'] ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="year-count-pill"
                        style="background:<?= $ac ?>18;color:<?= $ac ?>;border-color:<?= $ac ?>44;"><?= $total_yr ?> <?= $t['people'] ?>
                    </div>
                </div>

                <?php if ($total_yr === 0): ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1" opacity="0.3">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                        <p><?= $t['no_student_in_year'] ?></p>
                    </div>

                <?php elseif ($has_sec): ?>
                    <!-- Sub-tabs: ภาคปกติ / ภาคพิเศษ -->
                    <div class="section-tabs">
                        <button class="section-tab active" id="stab-<?= $yr ?>-r" onclick="showSection(<?= $yr ?>, 'r')"
                            style="--stab-color:<?= $ac ?>;">
                            <?= $t['regular'] ?>
                            <span class="stab-count" style="background:<?= $ac ?>;"><?= count($students_regular) ?></span>
                        </button>
                        <button class="section-tab" id="stab-<?= $yr ?>-s" onclick="showSection(<?= $yr ?>, 's')"
                            style="--stab-color:<?= $ac ?>;">
                            <?= $t['special_program'] ?>
                            <span class="stab-count"><?= count($students_special) ?></span>
                        </button>
                    </div>

                    <!-- ภาคปกติ -->
                    <div class="section-panel" id="spanel-<?= $yr ?>-r">
                        <?php if (empty($students_regular)): ?>
                            <div class="empty-state" style="padding:2rem;">
                                <p style="opacity:0.4;"><?= $t['no_regular_students'] ?></p>
                            </div>
                        <?php else: ?>
                            <div class="students-grid">
                                <?php foreach ($students_regular as $s):
                                    echo renderStudentCard($s, $yr, $ac);
                                endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ภาคพิเศษ -->
                    <div class="section-panel" id="spanel-<?= $yr ?>-s" style="display:none;">
                        <?php if (empty($students_special)): ?>
                            <div class="empty-state" style="padding:2rem;">
                                <p style="opacity:0.4;"><?= $t['no_special_students'] ?></p>
                            </div>
                        <?php else: ?>
                            <div class="students-grid">
                                <?php foreach ($students_special as $s):
                                    echo renderStudentCard($s, $yr, $ac);
                                endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <!-- ปี 3-4 ไม่มีภาค -->
                    <div class="students-grid">
                        <?php foreach ($students_regular as $s):
                            echo renderStudentCard($s, $yr, $ac);
                        endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>

    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<?php
// ---- ฟังก์ชัน render การ์ดนิสิต ----
function renderStudentCard($s, $yr, $ac)
{
    global $base_url;
    $photo = '';
    if (!empty($s['profile_photo'])) {
        $p = __DIR__ . '/uploads/profiles/' . $s['profile_photo'];
        if (file_exists($p))
            $photo = $base_url . '/uploads/profiles/' . $s['profile_photo'];
    }
    $initials = mb_substr($s['first_name_th'] ?? '?', 0, 1);
    $name_th = ($s['prefix_th'] ?? '') . ($s['first_name_th'] ?? '') . ' ' . ($s['last_name_th'] ?? '');
    $name_en = ($s['prefix_en'] ?? '') . ' ' . ($s['first_name_en'] ?? '') . ' ' . ($s['last_name_en'] ?? '');
    ob_start();
    ?>
    <div class="student-card" style="--card-accent:<?= $ac ?>;">
        <div class="student-avatar">
            <?php if ($photo): ?>
                <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($name_th) ?>" class="student-avatar-img">
            <?php else: ?>
                <div class="student-avatar-placeholder" style="background:linear-gradient(135deg,<?= $ac ?>,<?= $ac ?>aa);">
                    <span><?= htmlspecialchars($initials) ?></span>
                </div>
            <?php endif; ?>
            <div class="student-year-dot" style="background:<?= $ac ?>;"><?= $t['year_prefix'] ?> <?= $yr ?></div>
        </div>
        <div class="student-info">
            <div class="student-id" style="color:<?= $ac ?>;"><?= htmlspecialchars($s['student_id']) ?></div>
            <h3 class="student-name"><?= htmlspecialchars($name_th) ?></h3>
            <p class="student-name-en"><?= htmlspecialchars($name_en) ?></p>
            <div class="student-contact">
                <?php if (!empty($s['email'])): ?>
                    <div class="contact-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                            stroke="<?= $ac ?>" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                        <span><?= htmlspecialchars($s['email']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($s['phone'])): ?>
                    <div class="contact-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                            stroke="<?= $ac ?>" stroke-width="2">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.1a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.61 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.08 6.08l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        <span><?= htmlspecialchars($s['phone']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="student-major"><span><?= htmlspecialchars($s['major'] ?? 'Information Studies') ?></span></div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>

<script>
    function showYear(yr) {
        document.querySelectorAll('.year-panel').forEach(p => p.style.display = 'none');
        document.querySelectorAll('.year-tab').forEach(t => t.classList.remove('active'));
        var p = document.getElementById('panel-' + yr);
        var t = document.getElementById('tab-' + yr);
        if (p) { p.style.display = 'block'; p.style.animation = 'fadeInUp 0.3s ease'; }
        if (t) t.classList.add('active');
    }

    function showSection(yr, sec) {
        ['r', 's'].forEach(function (s) {
            var panel = document.getElementById('spanel-' + yr + '-' + s);
            var tab = document.getElementById('stab-' + yr + '-' + s);
            if (panel) panel.style.display = s === sec ? 'block' : 'none';
            if (tab) tab.classList.toggle('active', s === sec);
        });
    }
</script>