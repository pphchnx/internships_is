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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            <h2 style="font-size: 2rem; margin: 0;"><?= $t['manage_requests'] ?></h2>
            <a href="../dashboard.php" class="btn" style="border: 1px solid var(--border-color); color: var(--text-color);">&larr; <?= $t['back'] ?? 'ย้อนกลับ' ?></a>
        </div>
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
                    <td><?= getStatusBadge($req['status'], $t, 'staff') ?></td>
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
