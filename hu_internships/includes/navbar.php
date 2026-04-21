<?php
/**
 * navbar.php — แถบเมนูด้านบน (Global Navbar)
 * ใช้ร่วมกันทุกหน้าผ่าน require_once 'includes/navbar.php'
 * ตรวจสอบ session เพื่อแสดงเมนูตาม role และสถานะ login
 */
?>
<nav class="navbar">
    <!-- ===== โลโก้ซ้ายมือ: รูปโลโก้มหาวิทยาลัย + ชื่อระบบ ===== -->
    <a href="https://www.swu.ac.th/" target="_blank" style="text-decoration:none; color:inherit;">
        <div class="navbar-brand">
            <!-- รูปโลโก้มหาวิทยาลัยจากโฟลเดอร์ assets/images/Logo.png -->
            <img src="<?= $base_url ?>/assets/images/Logo.png" alt="โลโก้มหาวิทยาลัยศรีนครินทรวิโรฒ"
                style="height:40px; width:auto; object-fit:contain;">
            <!-- ชื่อระบบถัดจากโลโก้ -->
            <span style="font-size: 1.4rem; letter-spacing: -1px;">SWU<span
                    style="color: var(--text-color); font-weight: 300;">Internships</span></span>
        </div>
    </a>

    <ul class="nav-links">
        <li><a href="<?= $base_url ?>/index.php"><?= $t['home'] ?></a></li>
        <li><a href="<?= $base_url ?>/index.php#about"><?= $t['curriculum'] ?></a></li>
        <li><a href="<?= $base_url ?>/index.php#internship"><?= $t['internship'] ?></a></li>
        <li><a href="<?= $base_url ?>/index.php#news"><?= $t['news'] ?></a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?= $base_url ?>/dashboard.php"><?= $t['dashboard'] ?></a></li>
            <?php if ($_SESSION['role'] === 'student'): ?>
                <li><a href="<?= $base_url ?>/student/register.php"><?= $t['register_internship'] ?></a></li>
                <li><a href="<?= $base_url ?>/student/view_status.php"><?= $t['view_status'] ?></a></li>
                <li><a href="<?= $base_url ?>/student/upload.php"><?= $t['upload_docs'] ?></a></li>
            <?php elseif ($_SESSION['role'] === 'staff'): ?>
                <li><a href="<?= $base_url ?>/staff/view_all.php"><?= $t['manage_requests'] ?></a></li>
                <li><a href="<?= $base_url ?>/staff/manage_documents.php"><?= $t['manage_documents'] ?></a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>

    <ul class="nav-links right">
        <li><button id="lang-toggle" class="btn"><?= $lang === 'th' ? 'EN' : 'TH' ?></button></li>
        <li><button id="theme-toggle" class="btn"
                style="background: transparent; color: var(--text-color); display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; padding: 0;">
                <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
                <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
            </button></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?= $base_url ?>/logout.php" class="btn btn-danger"><?= $t['logout'] ?></a></li>
        <?php else: ?>
            <li><a href="<?= $base_url ?>/register.php" class="btn"
                    style="border:1px solid var(--border-color); color:var(--text-color);"><?= $t['register'] ?></a></li>
            <li><a href="<?= $base_url ?>/login.php" class="btn btn-primary"><?= $t['login'] ?></a></li>
        <?php endif; ?>
    </ul>
</nav>