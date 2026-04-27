<?php
/**
 * staff/update_status.php — จัดการคำขอฝึกงาน (Staff + Teacher)
 * Workflow: 0=รอรับเรื่อง → 1=รับเรื่อง → 2=อาจารย์อนุมัติ → 3=ออกใบส่งตัว → 4=เสร็จ / 9=ยกเลิก
 */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();

// อนุญาตทั้ง staff และ teacher
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../dashboard.php");
    exit;
}

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: " . ($role === 'staff' ? "view_all.php" : "../dashboard.php"));
    exit;
}

$id  = (int)$_GET['id'];
$msg = '';

// ดึงข้อมูลคำขอ
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
    echo "<div style='padding:2rem;text-align:center;color:red;'>ไม่พบคำขอที่ระบุ</div>";
    exit;
}

// Teacher: ตรวจสิทธิ์ — เห็นเฉพาะนิสิตในความดูแล
if ($role === 'teacher' && $req['advisor_id'] !== $user_id) {
    echo "<div style='padding:2rem;text-align:center;color:red;'>" . ($t['access_denied'] ?? 'ไม่มีสิทธิ์') . "</div>";
    exit;
}

// ==================== Handle POST ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_status'])) {
    $new_status = (int)$_POST['new_status'];
    $remark     = trim($_POST['remark'] ?? '');
    $current    = (int)$req['status'];

    // ตรวจสอบ Transition ที่อนุญาต
    $allowed = false;
    if ($role === 'staff') {
        // Staff: 0→1, 0→9, 1→9, 2→3, 3→4
        $allowed = in_array([$current, $new_status], [[0,1],[0,9],[1,9],[2,3],[3,4]], true);
    } elseif ($role === 'teacher') {
        // Teacher: 1→2, 1→9
        $allowed = in_array([$current, $new_status], [[1,2],[1,9]], true);
    }

    if ($allowed) {
        $upd = $conn->prepare("UPDATE internship_requests SET status = ?, remark = ?, updated_at = NOW() WHERE request_id = ?");
        if ($upd->execute([$new_status, $remark, $id])) {
            $req['status'] = $new_status;
            $req['remark'] = $remark;
            $msg = "<div class='alert-ok'>✅ อัปเดตสถานะสำเร็จ!</div>";
        }
    } else {
        $msg = "<div class='alert-err'>❌ ไม่สามารถเปลี่ยนสถานะนี้ได้</div>";
    }
}

// Status definitions
$statusMap = [
    0 => ['label' => $t['status_0'] ?? 'รอเจ้าหน้าที่รับเรื่อง', 'color' => '#f59e0b'],
    1 => ['label' => $t['status_1'] ?? 'รับเรื่องเข้าระบบ',        'color' => '#06b6d4'],
    2 => ['label' => $t['status_2'] ?? 'อาจารย์อนุมัติ',          'color' => '#22c55e'],
    3 => ['label' => $t['status_3'] ?? 'ออกใบส่งตัวฝึกงาน',       'color' => '#6366f1'],
    4 => ['label' => $t['status_4'] ?? 'ฝึกงานเสร็จแล้ว',         'color' => '#10b981'],
    9 => ['label' => $t['status_9'] ?? 'ยกเลิก',                   'color' => '#ef4444'],
];

$page_title = 'จัดการคำขอ';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$current_status = (int)$req['status'];
$sm = $statusMap[$current_status] ?? $statusMap[0];
?>

<div class="container" style="max-width:780px;padding:2rem 1.5rem;">

    <!-- Back -->
    <a href="<?= $role === 'staff' ? 'view_all.php' : '../dashboard.php' ?>"
       style="display:inline-flex;align-items:center;gap:.4rem;font-size:.88rem;color:var(--primary-color);text-decoration:none;margin-bottom:1.5rem;">
        ← <?= $t['back'] ?? 'กลับ' ?>
    </a>

    <style>
        .wf-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;}
        .wf-title{font-size:1.4rem;font-weight:800;margin:0 0 1.2rem;}
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
        .info-item label{font-size:.8rem;opacity:.55;display:block;margin-bottom:.2rem;}
        .info-item p{margin:0;font-weight:600;font-size:.95rem;}
        .status-badge{display:inline-block;padding:.35rem .9rem;border-radius:99px;font-size:.85rem;font-weight:700;}
        .btn-action{width:100%;padding:.8rem 1rem;border:none;border-radius:8px;font-size:.95rem;font-weight:700;cursor:pointer;margin-bottom:.6rem;display:flex;align-items:center;justify-content:center;gap:.5rem;text-decoration:none;}
        .btn-green{background:#22c55e;color:#fff;}
        .btn-blue{background:#06b6d4;color:#fff;}
        .btn-purple{background:#6366f1;color:#fff;}
        .btn-teal{background:#10b981;color:#fff;}
        .btn-red{background:#ef4444;color:#fff;}
        .btn-disabled{background:var(--border-color);color:#999;cursor:not-allowed;}
        .alert-ok{background:rgba(34,197,94,.1);color:#22c55e;border-left:4px solid #22c55e;padding:.75rem 1rem;border-radius:6px;margin-bottom:1rem;font-weight:600;}
        .alert-err{background:rgba(239,68,68,.1);color:#ef4444;border-left:4px solid #ef4444;padding:.75rem 1rem;border-radius:6px;margin-bottom:1rem;font-weight:600;}
        .warn-box{background:rgba(245,158,11,.1);border-left:4px solid #f59e0b;padding:.75rem 1rem;border-radius:6px;margin-bottom:1rem;font-size:.9rem;color:#b45309;}
        .wf-steps{display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap;}
        .wf-step{padding:.3rem .7rem;border-radius:99px;font-size:.75rem;font-weight:700;border:2px solid transparent;}
        .wf-step.active{color:#fff;}
        .wf-step.done{opacity:.45;}
        textarea.form-control{padding:.65rem .9rem;border-radius:8px;border:1px solid var(--border-color);background:var(--bg-color);color:var(--text-color);font-size:.9rem;width:100%;box-sizing:border-box;font-family:inherit;resize:vertical;}
    </style>

    <!-- Workflow Steps -->
    <div class="wf-steps">
        <?php
        $steps = [0=>'รอรับเรื่อง',1=>'รับเรื่อง',2=>'อาจารย์อนุมัติ',3=>'ออกใบส่งตัว',4=>'เสร็จสิ้น'];
        foreach ($steps as $s => $label):
            $cls = '';
            if ($s == $current_status) { $cls = 'active'; $bg = 'background:' . $sm['color'] . ';border-color:' . $sm['color'] . ';'; }
            elseif ($current_status != 9 && $s < $current_status) { $cls = 'done'; $bg = 'background:var(--border-color);'; }
            else { $bg = 'border-color:var(--border-color);color:var(--text-color);'; }
        ?>
            <span class="wf-step <?= $cls ?>" style="<?= $bg ?>"><?= ($s+1==9?'':$s+1).'.'  ?><?= $label ?></span>
        <?php endforeach; ?>
        <?php if ($current_status == 9): ?>
            <span class="wf-step active" style="background:#ef4444;border-color:#ef4444;">✕ ยกเลิก</span>
        <?php endif; ?>
    </div>

    <?= $msg ?>

    <!-- คำเตือน: ไม่มีอาจารย์นิเทศ -->
    <?php if (!$req['advisor_id'] && $current_status < 2): ?>
        <div class="warn-box"><?= $t['no_advisor_warning'] ?? '⚠️ นิสิตยังไม่ได้เลือกอาจารย์นิเทศ ควรยกเลิกให้นิสิตแก้ไขก่อน' ?></div>
    <?php endif; ?>

    <!-- รายละเอียดคำขอ -->
    <div class="wf-card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.2rem;">
            <div>
                <div class="wf-title">📋 รายละเอียดคำขอ #<?= $req['request_id'] ?></div>
                <span class="status-badge" style="background:<?= $sm['color'] ?>20;color:<?= $sm['color'] ?>;border:1px solid <?= $sm['color'] ?>40;">
                    <?= $sm['label'] ?>
                </span>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>นิสิต</label>
                <p><?= htmlspecialchars($req['fullname']) ?> <span style="opacity:.55;font-size:.85rem;">(<?= htmlspecialchars($req['student_id']) ?>)</span></p>
            </div>
            <div class="info-item">
                <label>อาจารย์นิเทศ</label>
                <p><?= $req['advisor_name'] ? htmlspecialchars($req['advisor_name']) : '<span style="color:#ef4444;">ยังไม่ระบุ</span>' ?></p>
            </div>
            <div class="info-item">
                <label>สถานประกอบการ</label>
                <p><?= htmlspecialchars($req['company_name']) ?></p>
            </div>
            <div class="info-item">
                <label>จังหวัด / ประเภท</label>
                <p><?= htmlspecialchars($req['company_province'] ?: '-') ?> · <?= htmlspecialchars($req['company_type'] ?: '-') ?></p>
            </div>
            <div class="info-item">
                <label>วันที่ฝึกงาน</label>
                <p>
                    <?= $req['internship_start'] ? date('d/m/Y', strtotime($req['internship_start'])) : '-' ?>
                    –
                    <?= $req['internship_end'] ? date('d/m/Y', strtotime($req['internship_end'])) : '-' ?>
                </p>
            </div>
            <div class="info-item">
                <label>วัน / เวลาทำงาน</label>
                <p><?= htmlspecialchars($req['work_days'] ?: '-') ?> (<?= htmlspecialchars($req['work_hours'] ?: '-') ?>)</p>
            </div>
            <div class="info-item">
                <label>ผู้ติดต่อ</label>
                <p><?= htmlspecialchars($req['contact_person'] ?: '-') ?></p>
            </div>
            <div class="info-item">
                <label>แผนก</label>
                <p><?= htmlspecialchars($req['department'] ?: '-') ?></p>
            </div>
        </div>

        <?php if ($req['remark']): ?>
            <div style="margin-top:1rem;padding:.75rem 1rem;background:var(--bg-color);border-radius:8px;border:1px solid var(--border-color);">
                <label style="font-size:.8rem;opacity:.55;display:block;margin-bottom:.25rem;">หมายเหตุ</label>
                <p style="margin:0;"><?= nl2br(htmlspecialchars($req['remark'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ปุ่มดำเนินการ -->
    <?php if ($current_status != 9): ?>
    <div class="wf-card">
        <div class="wf-title">⚡ ดำเนินการ</div>

        <!-- Remark field -->
        <div style="margin-bottom:1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.4rem;">หมายเหตุ (ไม่บังคับ)</label>
            <form id="action-form" method="POST" style="display:contents;">
                <textarea name="remark" class="form-control" rows="2" placeholder="ระบุหมายเหตุ เช่น เหตุผลการยกเลิก..."><?= htmlspecialchars($req['remark'] ?? '') ?></textarea>
                <input type="hidden" name="new_status" id="new_status_input" value="">
        </div>

        <?php if ($role === 'staff'): ?>
            <?php if ($current_status === 0): ?>
                <!-- Staff: รอรับเรื่อง → รับ หรือ ยกเลิก -->
                <button class="btn-action btn-blue" onclick="submit_action(1)">✅ รับเรื่องเข้าระบบ</button>
                <button class="btn-action btn-red" onclick="submit_action(9)">✕ ยกเลิก / ให้นิสิตแก้ไข</button>

            <?php elseif ($current_status === 1): ?>
                <!-- Staff: รับเรื่องแล้ว รอ Teacher → สามารถยกเลิก -->
                <div style="padding:.75rem;background:rgba(6,182,212,.08);border-radius:8px;margin-bottom:.8rem;font-size:.9rem;">
                    ⏳ รอให้อาจารย์ <strong><?= htmlspecialchars($req['advisor_name'] ?? 'ที่นิเทศ') ?></strong> อนุมัติ
                </div>
                <button class="btn-action btn-red" onclick="submit_action(9)">✕ ยกเลิก / ให้นิสิตแก้ไข</button>

            <?php elseif ($current_status === 2): ?>
                <!-- Staff: อาจารย์อนุมัติแล้ว → ออกใบส่งตัว -->
                <button class="btn-action btn-purple" onclick="submit_action(3)">📄 ออกใบส่งตัวฝึกงาน</button>

            <?php elseif ($current_status === 3): ?>
                <!-- Staff: ออกใบแล้ว → เสร็จสิ้น -->
                <button class="btn-action btn-teal" onclick="submit_action(4)">🎓 เสร็จสิ้นการฝึกงาน</button>

            <?php elseif ($current_status === 4): ?>
                <div style="padding:.75rem;background:rgba(16,185,129,.1);border-radius:8px;font-size:.9rem;color:#10b981;font-weight:700;">
                    ✅ ฝึกงานเสร็จสิ้นแล้ว ไม่มีขั้นตอนต่อไป
                </div>
            <?php endif; ?>

        <?php elseif ($role === 'teacher'): ?>
            <?php if ($current_status === 1): ?>
                <!-- Teacher: อนุมัติ หรือ ไม่อนุมัติ -->
                <button class="btn-action btn-green" onclick="submit_action(2)">✅ อนุมัติ</button>
                <button class="btn-action btn-red" onclick="submit_action(9)">✕ ไม่อนุมัติ / ให้แก้ไข</button>
            <?php else: ?>
                <div style="padding:.75rem;background:var(--bg-color);border-radius:8px;font-size:.9rem;opacity:.7;">
                    ℹ️ ไม่มีการดำเนินการในขั้นตอนนี้สำหรับอาจารย์
                </div>
            <?php endif; ?>
        <?php endif; ?>

            </form>
    </div>
    <?php else: ?>
        <div class="wf-card" style="border-left:4px solid #ef4444;">
            <p style="margin:0;color:#ef4444;font-weight:700;">✕ คำขอนี้ถูกยกเลิกแล้ว นิสิตสามารถยื่นคำขอใหม่ได้</p>
        </div>
    <?php endif; ?>
</div>

<script>
function submit_action(status) {
    if (!confirm('ยืนยันการเปลี่ยนสถานะ?')) return;
    document.getElementById('new_status_input').value = status;
    document.getElementById('action-form').submit();
}
</script>

<?php require_once '../includes/footer.php'; ?>
