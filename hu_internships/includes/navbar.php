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
            <img src="<?= $base_url ?>/assets/images/Logo.png" alt="โลโก้มหาวิทยาลัยศรีนครินทรวิโรฒ"
                style="height:40px; width:auto; object-fit:contain;">
            <span style="font-size: 1.4rem; letter-spacing: -1px;">SWU<span
                    style="color: var(--text-color); font-weight: 300;">Internships</span></span>
        </div>
    </a>

    <ul class="nav-links">
        <li><a href="<?= $base_url ?>/index.php"><?= $t['home'] ?></a></li>
        <li class="nav-dropdown">
            <a href="#" class="nav-dropdown-toggle" onclick="return false;">
                <?= $lang === 'en' ? 'Explore' : 'เมนู' ?>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </a>
            <ul class="nav-dropdown-menu">
                <li><a href="<?= $base_url ?>/index.php#about">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    <?= $t['curriculum'] ?>
                </a></li>
                <li><a href="<?= $base_url ?>/index.php#teachers">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <?= $lang === 'en' ? 'Teachers' : 'อาจารย์' ?>
                </a></li>
                <li><a href="<?= $base_url ?>/students.php">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    <?= $lang === 'en' ? 'Students' : 'นิสิตปัจจุบัน' ?>
                </a></li>
                <li><a href="<?= $base_url ?>/index.php#internship">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"></rect><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"></path><line x1="12" y1="12" x2="12" y2="16"></line><line x1="10" y1="14" x2="14" y2="14"></line></svg>
                    <?= $t['internship'] ?>
                </a></li>
                <li><a href="<?= $base_url ?>/index.php#news">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path><path d="M18 14h-8M15 18h-5M10 6h8v4h-8V6Z"></path></svg>
                    <?= $t['news'] ?>
                </a></li>
                <li><a href="<?= $base_url ?>/about.html">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <?= $lang === 'en' ? 'About' : 'เกี่ยวกับเรา' ?>
                </a></li>
            </ul>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'student'): ?>
                <li class="nav-dropdown">
                    <a href="<?= $base_url ?>/dashboard.php" class="nav-dropdown-toggle">
                        <?= $lang === 'en' ? 'Student' : 'นิสิต' ?>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </a>
                    <ul class="nav-dropdown-menu">
                        <li><a href="<?= $base_url ?>/dashboard.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <?= $t['dashboard'] ?>
                        </a></li>
                        <li><a href="<?= $base_url ?>/student/internship_form.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            <?= $t['register_internship'] ?>
                        </a></li>
                        <li><a href="<?= $base_url ?>/student/view_status.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <?= $t['view_status'] ?>
                        </a></li>
                        <li><a href="<?= $base_url ?>/student/upload.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            <?= $lang === 'en' ? 'Upload Certificate' : 'อัปโหลดเอกสารรับรอง' ?>
                        </a></li>
                    </ul>
                </li>
            <?php elseif ($_SESSION['role'] === 'staff'): ?>
                <li class="nav-dropdown">
                    <a href="<?= $base_url ?>/dashboard.php" class="nav-dropdown-toggle">
                        <?= $lang === 'en' ? 'Staff' : 'เจ้าหน้าที่' ?>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </a>
                    <ul class="nav-dropdown-menu">
                        <li><a href="<?= $base_url ?>/dashboard.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <?= $t['dashboard'] ?>
                        </a></li>
                        <li><a href="<?= $base_url ?>/staff/view_all.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"></path><path d="M14 3v5h5M16 13H8M16 17H8M10 9H8"></path></svg>
                            <?= $t['manage_requests'] ?>
                        </a></li>
                        <li><a href="<?= $base_url ?>/staff/manage_news.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8V6Z"/></svg>
                            <?= $lang === 'en' ? 'Manage News' : 'จัดการข่าวสาร' ?>
                        </a></li>
                    </ul>
                </li>
            <?php elseif ($_SESSION['role'] === 'teacher'): ?>
                <li class="nav-dropdown">
                    <a href="<?= $base_url ?>/dashboard.php" class="nav-dropdown-toggle">
                        <?= $lang === 'en' ? 'Teacher' : 'อาจารย์' ?>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </a>
                    <ul class="nav-dropdown-menu">
                        <li><a href="<?= $base_url ?>/dashboard.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <?= $t['dashboard'] ?>
                        </a></li>
                        <li><a href="<?= $base_url ?>/teacher/view_students.php">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <?= $lang === 'en' ? 'My Students' : 'นิสิตในความดูแล' ?>
                        </a></li>
                    </ul>
                </li>
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
