<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();
checkRole('staff');

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

$page_title = $t['manage_documents'];
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <div class="card" style="text-align: center; padding: 3rem 2rem;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">📂</div>
        <h2 style="margin-bottom: 1rem; font-size: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><?= $t['manage_documents'] ?></h2>
        <p style="opacity: 0.7; margin-bottom: 2rem; line-height: 1.6;">
            <?= $t['feature_not_ready_desc'] ?>
        </p>
        <a href="../dashboard.php" class="btn btn-primary" style="display: inline-block; padding: 0.8rem 2rem;">
            &larr; <?= $t['back_to_home'] ?>
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
