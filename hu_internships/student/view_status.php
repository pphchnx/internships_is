<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();
checkRole('student');

// จัดการการลบคำขอ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del_id = $_POST['delete_id'];
    // อนุญาตให้ลบเฉพาะคำขอของตัวเองที่ยัง 'รอดำเนินการ' (status = 1) เท่านั้น
    $del_stmt = $conn->prepare("DELETE FROM internship_requests WHERE request_id = ? AND student_id = ? AND status = 1");
    $del_stmt->execute([$del_id, $_SESSION['user_id']]);
    header("Location: view_status.php");
    exit;
}

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

$stmt = $conn->prepare("SELECT * FROM internship_requests WHERE student_id = ? ORDER BY request_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = $t['track_status_title'];
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><?= $t['track_status_title'] ?></h2>
        <?php if(count($requests) > 0): ?>
            <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr>
                        <th style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= $t['table_company'] ?></th>
                        <th style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= $t['table_status'] ?></th>
                        <th style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= $t['table_contact'] ?></th>
                        <th style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= $t['table_date'] ?></th>
                        <th style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= $t['table_manage'] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($requests as $req): ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><strong><?= htmlspecialchars($req['company_name']) ?></strong></td>
                    <?php
                    $statusLabels = [1 => $t['status_pending'], 2 => $t['status_approved'], 3 => $t['status_rejected']];
                    $statusClasses = [1 => 'pending', 2 => 'approved', 3 => 'rejected'];
                    $sLabel = $statusLabels[$req['status']] ?? 'Unknown';
                    $sClass = $statusClasses[$req['status']] ?? 'pending';
                    ?>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><span class="badge badge-<?= $sClass ?>"><?= $sLabel ?></span></td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($req['contact_person'] ?: '-') ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= date('d M Y', strtotime($req['request_date'])) ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                            <?php if ($req['status'] == 1): ?>
                            <form method="POST" action="" onsubmit="return confirm('<?= addslashes($t['confirm_cancel_request']) ?>');" style="margin: 0;">
                                <input type="hidden" name="delete_id" value="<?= $req['request_id'] ?>">
                                <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444; padding: 0.35rem 0.7rem; font-size: 0.8rem; cursor: pointer; border-radius: 4px;"><?= $t['cancel_request_btn'] ?></button>
                            </form>
                            <?php else: ?>
                            <span style="font-size: 0.8rem; opacity: 0.5;"><?= $t['cannot_edit'] ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <p style="margin-top:20px; opacity: 0.8;"><?= $t['no_requests_found'] ?></p>
            <a href="register.php" class="btn btn-primary" style="display:inline-block; margin-top:1rem;"><?= $t['submit_request_btn'] ?></a>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
