<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();
checkRole('student');

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

$page_title = $t['upload_docs'];
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <div class="card" style="max-width: 600px; margin: 2rem auto; text-align: center; padding: 3rem 2rem;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">📁</div>
        <h2 style="margin-bottom: 1rem; font-size: 2rem;"><?= $t['upload_docs'] ?></h2>
        <p style="opacity: 0.7; margin-bottom: 2rem; line-height: 1.6;">
            <?= $t['upload_not_ready_desc'] ?>
        </p>
        <a href="../dashboard.php" class="btn btn-primary" style="display: inline-block; padding: 0.8rem 2rem;">
            &larr; <?= $t['back_to_home'] ?>
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
