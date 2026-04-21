<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();
checkRole('staff');

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

$stmt = $conn->query("SELECT r.*, u.fullname FROM internship_requests r JOIN users u ON r.student_id = u.username ORDER BY r.request_date DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = $t['manage_requests'];
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><?= $t['manage_requests'] ?></h2>
        <table style="margin-top:20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?= $t['student'] ?></th>
                    <th><?= $t['company_name'] ?></th>
                    <th><?= $t['status'] ?></th>
                    <th><?= $t['date'] ?></th>
                    <th><?= $t['action'] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($requests as $req): ?>
                <tr>
                    <td style="opacity: 0.7; font-size: 0.9rem;">#<?= htmlspecialchars($req['request_id']) ?></td>
                    <td><strong><?= htmlspecialchars($req['fullname']) ?></strong> <br><span style="font-size: 0.85rem; opacity: 0.7;"><?= htmlspecialchars($req['student_id']) ?></span></td>
                    <td><strong><?= htmlspecialchars($req['company_name']) ?></strong></td>
                    <?php
                    $statusLabels = [1 => $t['status_pending'], 2 => $t['status_approved'], 3 => $t['status_rejected']];
                    $statusClasses = [1 => 'pending', 2 => 'approved', 3 => 'rejected'];
                    $sLabel = $statusLabels[$req['status']] ?? 'Unknown';
                    $sClass = $statusClasses[$req['status']] ?? 'pending';
                    ?>
                    <td><span class="badge badge-<?= $sClass ?>"><?= $sLabel ?></span></td>
                    <td><?= date('d M Y', strtotime($req['request_date'])) ?></td>
                    <td>
                        <a href="update_status.php?id=<?= $req['request_id'] ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;"><?= $t['update_btn'] ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
