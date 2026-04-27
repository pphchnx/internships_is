<?php
/**
 * teacher/view_students.php — รายการนิสิตในความดูแลของอาจารย์
 * Teacher เห็นเฉพาะนิสิตที่เลือกตนเป็น advisor
 * ถ้ามี ?id=xxx → redirect ไป staff/update_status.php?id=xxx (ใช้ร่วม)
 */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();
checkRole('teacher');

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";
$user_id = $_SESSION['user_id'];

// Redirect ไปหน้าจัดการถ้ามี ?id=
if (isset($_GET['id'])) {
    header("Location: ../staff/update_status.php?id=" . (int)$_GET['id']);
    exit;
}

// ดึงรายการนิสิตในความดูแล
$stmt = $conn->prepare("
    SELECT r.*, u.fullname, si.major, si.student_type
    FROM internship_requests r
    JOIN users u ON r.student_id = u.username
    LEFT JOIN students_info si ON r.student_id = si.student_id
    WHERE r.advisor_id = ?
    ORDER BY r.request_date DESC
");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusMap = [
    0 => ['label' => $t['status_0'] ?? 'รอรับเรื่อง',        'color' => '#f59e0b'],
    1 => ['label' => $t['status_1'] ?? 'รับเรื่องเข้าระบบ',  'color' => '#06b6d4'],
    2 => ['label' => $t['status_2'] ?? 'อาจารย์อนุมัติ',     'color' => '#22c55e'],
    3 => ['label' => $t['status_3'] ?? 'ออกใบส่งตัว',        'color' => '#6366f1'],
    4 => ['label' => $t['status_4'] ?? 'ฝึกงานเสร็จแล้ว',    'color' => '#10b981'],
    9 => ['label' => $t['status_9'] ?? 'ยกเลิก',              'color' => '#ef4444'],
];

// นับสถิติ
$cnt = array_fill_keys([0,1,2,3,4,9], 0);
foreach ($rows as $r) { $cnt[(int)$r['status']] = ($cnt[(int)$r['status']] ?? 0) + 1; }

$page_title = $t['teacher_students'] ?? 'รายการนิสิตในความดูแล';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container" style="max-width:1000px;padding:2rem 1.5rem;">

    <a href="../dashboard.php" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.88rem;color:var(--primary-color);text-decoration:none;margin-bottom:1.5rem;">
        ← กลับ Dashboard
    </a>

    <h1 style="font-size:1.8rem;font-weight:800;margin:0 0 .3rem;">👩‍🏫 รายการนิสิตในความดูแล</h1>
    <p style="opacity:.6;margin:0 0 1.5rem;">อาจารย์สามารถดูและอนุมัติคำขอที่อยู่ในขั้นตอน "รับเรื่องเข้าระบบ" เท่านั้น</p>

    <!-- สถิติ -->
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        <div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:10px;padding:.75rem 1.2rem;">
            <span style="font-size:1.3rem;font-weight:800;"><?= count($rows) ?></span>
            <span style="font-size:.8rem;opacity:.55;margin-left:.3rem;">ทั้งหมด</span>
        </div>
        <?php foreach ([1=>'รับเรื่องแล้ว', 2=>'อนุมัติ', 9=>'ยกเลิก'] as $s => $lbl): ?>
        <div style="background:var(--card-bg);border:1px solid <?= $statusMap[$s]['color'] ?>40;border-radius:10px;padding:.75rem 1.2rem;">
            <span style="font-size:1.3rem;font-weight:800;color:<?= $statusMap[$s]['color'] ?>;"><?= $cnt[$s] ?></span>
            <span style="font-size:.8rem;opacity:.55;margin-left:.3rem;"><?= $lbl ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <style>
        .t-table{width:100%;border-collapse:collapse;}
        .t-table th{font-size:.8rem;opacity:.5;font-weight:600;padding:.6rem 1rem;text-align:left;border-bottom:2px solid var(--border-color);}
        .t-table td{padding:.85rem 1rem;border-bottom:1px solid var(--border-color);vertical-align:middle;}
        .t-table tr:hover td{background:var(--bg-color);}
        .pill{display:inline-block;padding:.25rem .7rem;border-radius:99px;font-size:.78rem;font-weight:700;}
        .btn-sm{padding:.35rem .8rem;border-radius:6px;font-size:.8rem;font-weight:700;text-decoration:none;display:inline-block;}
    </style>

    <?php if (count($rows) > 0): ?>
    <div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="t-table">
                <thead>
                    <tr>
                        <th>นิสิต</th>
                        <th>สถานประกอบการ</th>
                        <th>ระยะเวลา</th>
                        <th>สถานะ</th>
                        <th>วันที่ยื่น</th>
                        <th>ดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r):
                        $st = (int)$r['status'];
                        $sm = $statusMap[$st] ?? $statusMap[0];
                        $can_approve = ($st === 1); // Teacher อนุมัติได้เฉพาะ status=1
                    ?>
                    <tr>
                        <td>
                            <div style="font-weight:700;"><?= htmlspecialchars($r['fullname']) ?></div>
                            <div style="font-size:.8rem;opacity:.5;"><?= htmlspecialchars($r['student_id']) ?></div>
                            <?php if ($r['major']): ?>
                                <div style="font-size:.75rem;opacity:.4;"><?= htmlspecialchars($r['major']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($r['company_name']) ?></div>
                            <?php if ($r['company_province']): ?>
                                <div style="font-size:.8rem;opacity:.5;"><?= htmlspecialchars($r['company_province']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.85rem;">
                            <?php if ($r['internship_start']): ?>
                                <?= date('d/m/Y', strtotime($r['internship_start'])) ?><br>
                                <span style="opacity:.5;">–</span><br>
                                <?= date('d/m/Y', strtotime($r['internship_end'])) ?>
                            <?php else: echo '—'; endif; ?>
                        </td>
                        <td>
                            <span class="pill" style="background:<?= $sm['color'] ?>18;color:<?= $sm['color'] ?>;border:1px solid <?= $sm['color'] ?>40;">
                                <?= $sm['label'] ?>
                            </span>
                        </td>
                        <td style="font-size:.82rem;opacity:.6;"><?= date('d M Y', strtotime($r['request_date'])) ?></td>
                        <td>
                            <?php if ($can_approve): ?>
                                <a href="../staff/update_status.php?id=<?= $r['request_id'] ?>" class="btn-sm" style="background:#22c55e;color:#fff;">✅ อนุมัติ</a>
                            <?php else: ?>
                                <a href="../staff/update_status.php?id=<?= $r['request_id'] ?>" class="btn-sm" style="background:var(--bg-color);border:1px solid var(--border-color);color:var(--text-color);">ดูรายละเอียด</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php else: ?>
        <div style="text-align:center;padding:4rem 2rem;background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;">
            <div style="font-size:3rem;margin-bottom:1rem;">👥</div>
            <h3 style="margin:0 0 .5rem;">ยังไม่มีนิสิตในความดูแล</h3>
            <p style="opacity:.6;margin:0;">นิสิตที่เลือกท่านเป็นอาจารย์นิเทศจะปรากฏที่นี่</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
