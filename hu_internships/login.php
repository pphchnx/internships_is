<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;
$t = require "lang/{$lang}.php";

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
        $_SESSION['user_id'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['fullname'] = $user['fullname'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = $t['invalid_login'];
    }
}

$page_title = $t['login'];
$extra_css = 'login.css';
require_once 'includes/header.php';
// Note: Intentionally omitting navbar on login screen for Netflix aesthetic
?>

<div class="login-container">
    <div class="login-card">
        <h2><?= $t['login'] ?></h2>
        <?php if($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="text" id="username" name="username" class="form-control" placeholder="<?= $t['username'] ?>" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" class="form-control" placeholder="<?= $t['password'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?= $t['login'] ?></button>
        </form>

        <!-- Divider -->
        <div style="display: flex; align-items: center; gap: 0.75rem; margin: 1.5rem 0;">
            <div style="flex:1; height:1px; background: var(--border-color);"></div>
            <span style="font-size: 0.82rem; color: var(--text-color); opacity: 0.45;"><?= $t['or'] ?></span>
            <div style="flex:1; height:1px; background: var(--border-color);"></div>
        </div>

        <!-- Register link -->
        <a href="register.php" style="
            display: block;
            width: 100%;
            text-align: center;
            padding: 0.85rem;
            border: 1.5px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s;
            text-decoration: none;
            background: transparent;
            box-sizing: border-box;
        "
        onmouseover="this.style.borderColor='var(--primary-color)';this.style.color='var(--primary-color)'"
        onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-color)'">
            <?= $t['register'] ?> →
        </a>

        <div style="margin-top: 1.5rem; text-align: center; display: flex; justify-content: center; gap: 10px;">
            <a href="index.php" style="color: var(--text-color); opacity: 0.6; font-size: 0.9rem; margin-right: 15px;">&#8592; <?= $t['home'] ?></a>
            <button id="lang-toggle" style="background: none; border: none; color: var(--text-color); opacity: 0.6; cursor: pointer; font-size: 0.9rem;"><?= $lang === 'th' ? 'EN' : 'TH' ?></button>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
