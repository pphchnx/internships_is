<?php
session_start();
require_once 'includes/db_connect.php';

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "lang/{$lang}.php";

$page_title = 'สาขาวิชาสารสนเทศศึกษา มศว | Information Studies SWU';
$extra_css = 'index.css';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- =============================================
     HERO SECTION
     ============================================= -->
<section class="hero-section" style="background: linear-gradient(160deg, rgba(0,0,0,0.82) 0%, rgba(20,0,0,0.75) 100%),
           url('assets/images/index1.jpg') center / cover no-repeat fixed;
           min-height: 100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;
           text-align:center; padding: 6rem 2rem 4rem; color:#fff; position:relative;">

    <!-- Badge -->
    <div style="display:inline-flex; align-items:center; gap:0.5rem; background:rgba(229,9,20,0.15);
                border:1px solid rgba(229,9,20,0.4); border-radius:20px; padding:0.4rem 1.1rem;
                font-size:0.85rem; letter-spacing:1px; text-transform:uppercase; margin-bottom:1.5rem; color:#ff8080;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
        <?= $t['hero_badge'] ?>
    </div>

    <h1
        style="font-size: clamp(2.2rem, 5vw, 4rem); font-weight:800; line-height:1.2; margin-bottom:1rem; max-width:800px;">
        <?= $t['hero_title_1'] ?? 'ระบบฝึกงาน ' ?>
        <span style="color:var(--primary-color);"><?= $t['hero_title_2'] ?? 'สารสนเทศศึกษา' ?></span>
    </h1>
    <p style="font-size:1.2rem; opacity:0.85; max-width:600px; line-height:1.7; margin-bottom:2.5rem;">
        <?= $t['hero_subtitle'] ?? 'จัดการ ติดตาม และส่งเอกสารการฝึกงานของคุณได้อย่างสะดวกผ่านระบบออนไลน์' ?>
    </p>

    <div style="display:flex; gap:1rem; flex-wrap:wrap; justify-content:center;">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="btn btn-primary"
                style="font-size:1.1rem; padding:0.9rem 2.5rem; border-radius:8px; font-weight:700;">
                <?= $t['login'] ?>
            </a>

            <!-- แก้ -->
            <a href="register.php" class="btn" style="font-size:1.1rem; padding:0.9rem 2.5rem; border-radius:8px; font-weight:600;
                      border:2px solid rgba(255,255,255,0.5); color:#fff; background:transparent;">
                <?= $t['register'] ?>
            </a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-primary"
                style="font-size:1.1rem; padding:0.9rem 2.5rem; border-radius:8px; font-weight:700;">
                <?= $t['go_to_dashboard'] ?>
            </a>
        <?php endif; ?>

        <!-- แก้ -->
        <a href="#about" class="btn" style="font-size:1.1rem; padding:0.9rem 2.5rem; border-radius:8px; font-weight:600;
                  border:2px solid rgba(255,255,255,0.3); color:rgba(255,255,255,0.8); background:transparent;">
            <?= $t['curriculum_info'] ?>
        </a>
    </div>

    <!-- Scroll indicator -->
    <div style="position:absolute; bottom:2rem; left:50%; transform:translateX(-50%); animation:bounce 2s infinite;">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
            stroke="rgba(255,255,255,0.5)" stroke-width="2">
            <path d="M7 13l5 5 5-5M7 6l5 5 5-5" />
        </svg>
    </div>
</section>



<!-- =============================================
     STATS BAR
     ============================================= -->
<div style="background:var(--primary-color); padding:1.5rem 2rem;">
    <div
        style="max-width:1100px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; text-align:center; color:#fff;">
        <div>
            <div style="font-size:2rem; font-weight:800;">12</div>
            <div style="font-size:0.85rem; opacity:0.85;"><?= $t['stats_programs'] ?></div>
        </div>

        <div>
            <div style="font-size:2rem; font-weight:800;">8</div>
            <div style="font-size:0.85rem; opacity:0.85;"><?= $t['stats_teachers'] ?></div>
        </div>
        <div>
            <div style="font-size:2rem; font-weight:800;">100+</div>
            <div style="font-size:0.85rem; opacity:0.85;"><?= $t['stats_companies'] ?></div>
        </div>
    </div>
</div>

<!-- =============================================
     ABOUT SECTION
     ============================================= -->
<section id="about" class="index-section" style="background:var(--bg-color);">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:4rem; align-items:center;">
            <div>
                <p
                    style="color:var(--primary-color); font-weight:700; font-size:0.85rem; text-transform:uppercase; letter-spacing:2px; margin-bottom:0.75rem;">
                    <?= $t['about_subtitle'] ?>
                </p>
                <h2 class="section-heading"><?= $t['about_title'] ?></h2>
                <p style="font-size:0.95rem; opacity:0.7; margin-bottom:0.3rem;"><?= $t['about_degree'] ?></p>
                <p style="line-height:1.8; opacity:0.85; margin:1.5rem 0; color:var(--text-color);">
                    <?= $t['about_desc'] ?>
                </p>
                <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:2rem;">
                    <?php
                    $features = [
                        ['รหัสหลักสูตร', '25520091104002'],
                        ['ระยะเวลาการศึกษา', '4 ปี (หลักสูตรปรับปรุง พ.ศ. 2566)'],
                        ['จำนวนหน่วยกิต', 'ไม่น้อยกว่า 129 หน่วยกิต'],
                        ['ภาษาที่ใช้สอน', 'ภาษาไทย'],
                    ];
                    foreach ($features as [$k, $v]):
                        ?>
                        <div style="display:flex; gap:0.75rem; align-items:flex-start;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="var(--primary-color)" stroke-width="2.5" style="flex-shrink:0;margin-top:2px">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            <span style="color:var(--text-color); font-size:0.92rem;"><strong><?= $k ?>:</strong>
                                <?= $v ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button onclick="document.getElementById('curriculumModal').style.display='flex'"
                    class="btn btn-primary" style="padding:0.85rem 2rem; font-size:0.95rem;">
                    <?= $t['learn_more'] ?>
                </button>
            </div>
            <?php
            /* ========================================================
               ข้อมูลหลักสูตรทั้ง 12 สาขา พร้อมรายละเอียดและลิงก์เว็บไซต์
               TODO: เติม URL จริงของแต่ละสาขาในช่อง 'url'
               ======================================================== */
            $index_majors = [
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาสารสนเทศศึกษา',
                    'name_en' => 'Information Studies',
                    'desc' => 'มุ่งผลิตบัณฑิตด้านการจัดการสารสนเทศ เทคโนโลยีดิจิทัล และการบริการสารสนเทศ',
                    'url' => '#',       // ← เว็บไซต์ของสาขานี้ (ระบบปัจจุบัน)
                    'highlight' => true,
                    'system' => true,      // มีระบบฝึกงานในเว็บนี้
                ],
                [
                    'degree' => 'วท.บ.',
                    'name' => 'สาขาวิชาจิตวิทยา',
                    'name_en' => 'Psychology',
                    'desc' => 'ศึกษาพฤติกรรมมนุษย์ จิตวิทยาคลินิก และจิตวิทยาองค์การ',
                    'url' => '#',       // TODO: เติม URL
                ],
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาภาษาไทย',
                    'name_en' => 'Thai Language',
                    'desc' => 'ศึกษาภาษาและวรรณกรรมไทย ภาษาศาสตร์ และการสื่อสาร',
                    'url' => '#',
                ],
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาภาษาเพื่อการสื่อสาร (หลักสูตรนานาชาติ)',
                    'name_en' => 'Language for Communication (International)',
                    'desc' => 'หลักสูตรนานาชาติด้านภาษาและการสื่อสารข้ามวัฒนธรรม',
                    'url' => '#',
                ],
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาวรรณกรรมสำหรับเด็ก',
                    'name_en' => 'Literature for Children',
                    'desc' => 'ผลิตนักเขียนและผู้เชี่ยวชาญด้านวรรณกรรมสำหรับเด็กและเยาวชน',
                    'url' => '#',
                ],
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาภาษาอังกฤษ',
                    'name_en' => 'English',
                    'desc' => 'ภาษาอังกฤษเพื่อการสื่อสาร วรรณกรรม และภาษาศาสตร์ประยุกต์',
                    'url' => '#',
                ],
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาภาษาเพื่ออาชีพ (หลักสูตรนานาชาติ)',
                    'name_en' => 'Language for Careers (International)',
                    'desc' => 'พัฒนาทักษะภาษาเพื่อตอบสนองตลาดแรงงานระดับนานาชาติ',
                    'url' => '#',
                ],
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาปรัชญาและศาสนา',
                    'name_en' => 'Philosophy and Religion',
                    'desc' => 'ศึกษาจริยศาสตร์ อภิปรัชญา และศาสนาเปรียบเทียบ',
                    'url' => '#',
                ],
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาภาษาตะวันออก',
                    'name_en' => 'Oriental Languages',
                    'desc' => 'ภาษาจีน ญี่ปุ่น เกาหลี และภาษาตะวันออกอื่นๆ',
                    'url' => '#',
                ],
                [
                    'degree' => 'กศ.บ.',
                    'name' => 'สาขาวิชาภาษาไทย (กศ.บ.)',
                    'name_en' => 'Thai Language (Education)',
                    'desc' => 'ผลิตครูภาษาไทยที่มีความเชี่ยวชาญด้านการสอนและวรรณกรรม',
                    'url' => '#',
                ],
                [
                    'degree' => 'กศ.บ.',
                    'name' => 'สาขาวิชาภาษาอังกฤษ (กศ.บ.)',
                    'name_en' => 'English (Education)',
                    'desc' => 'ผลิตครูภาษาอังกฤษระดับประถมและมัธยมศึกษา',
                    'url' => '#',
                ],
                [
                    'degree' => 'ศศ.บ.',
                    'name' => 'สาขาวิชาภาษาและวัฒนธรรมอาเซียน',
                    'name_en' => 'ASEAN Languages and Cultures',
                    'desc' => 'ภาษาและวัฒนธรรมของประเทศในกลุ่มอาเซียนเพื่อการทำงานระดับภูมิภาค',
                    'url' => '#',
                ],
            ];
            ?>
            <!-- กล่องแบบ interactive คล้าย register page -->
            <div class="majors-panel">
                <!-- หัวข้อ + badge จำนวน -->
                <div class="majors-panel-header">
                    <h3 class="majors-panel-title"><?= $t['majors_panel_title'] ?></h3>
                    <span class="majors-count-badge"><?= count($index_majors) ?> <?= $t['majors_count'] ?></span>
                </div>

                <!-- Dropdown เลือกสาขา -->
                <div class="majors-select-wrap">
                    <select id="majorSelect" class="majors-select form-control">
                        <?php foreach ($index_majors as $i => $m): ?>
                            <option value="<?= $i ?>" <?= !empty($m['highlight']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['degree']) ?> — <?= htmlspecialchars($m['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="majors-select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </span>
                </div>

                <!-- Detail card — อัปเดตตาม JS -->
                <div class="majors-detail-card" id="majorDetail">
                    <!-- JS จะ inject เนื้อหาที่นี่ -->
                </div>

                <!-- Scrollable list links สาขาทั้งหมด -->
                <div class="majors-links-title"><?= $t['all_programs_link'] ?></div>
                <div class="majors-links-list">
                    <?php foreach ($index_majors as $i => $m): ?>
                        <a href="<?= htmlspecialchars($m['url']) ?>"
                            class="majors-link-item <?= !empty($m['highlight']) ? 'majors-link-active' : '' ?>"
                            data-index="<?= $i ?>" <?= empty($m['system']) ? 'target="_blank"' : '' ?>
                            onclick="selectMajor(<?= $i ?>); <?= !empty($m['system']) ? 'return false;' : '' ?>">
                            <span class="majors-link-degree"><?= htmlspecialchars($m['degree']) ?></span>
                            <span class="majors-link-name"><?= htmlspecialchars($m['name']) ?></span>
                            <?php if (!empty($m['system'])): ?>
                                <!-- ระบบปัจจุบัน: แสดง badge "ระบบนี้" แทนไอคอน external -->
                                <span class="majors-link-system-badge">ระบบนี้</span>
                            <?php else: ?>
                                <svg class="majors-link-ext" xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                    <polyline points="15 3 21 3 21 9" />
                                    <line x1="10" y1="14" x2="21" y2="3" />
                                </svg>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ข้อมูลหลักสูตรทั้งหมดส่งไป JS เป็น JSON -->
            <script>
                var indexMajors = <?= json_encode(array_values($index_majors), JSON_UNESCAPED_UNICODE) ?>;

                /* เปลี่ยน detail card และ highlight item ที่เลือก */
                function selectMajor(idx) {
                    var m = indexMajors[idx];
                    if (!m) return;

                    /* อัปเดต detail card */
                    var detail = document.getElementById('majorDetail');
                    var isSystem = m.system === true;
                    detail.innerHTML =
                        '<div class="majors-detail-degree">' + m.degree + '</div>' +
                        '<div class="majors-detail-name">' + m.name + '</div>' +
                        '<div class="majors-detail-name-en">' + m.name_en + '</div>' +
                        '<p class="majors-detail-desc">' + m.desc + '</p>' +
                        (isSystem
                            ? '<a href="' + m.url + '" class="majors-detail-btn majors-detail-btn-system">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>' +
                            'เข้าสู่ระบบฝึกงาน</a>'
                            : '<a href="' + m.url + '" target="_blank" class="majors-detail-btn">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>' +
                            'เว็บไซต์สาขา</a>');

                    /* อัปเดต dropdown */
                    document.getElementById('majorSelect').value = idx;

                    /* อัปเดต active class ใน list */
                    document.querySelectorAll('.majors-link-item').forEach(function (el, i) {
                        el.classList.toggle('majors-link-active', i === idx);
                    });
                }

                /* Sync dropdown → detail */
                document.getElementById('majorSelect').addEventListener('change', function () {
                    selectMajor(parseInt(this.value));
                });

                /* โหลดครั้งแรก: แสดง default (สารสนเทศศึกษา = index 0) */
                selectMajor(0);
            </script>

        </div>
    </div>
</section>

<!-- =============================================
     INTERNSHIP SYSTEM SECTION
     ============================================= -->
<section id="internship" class="index-section" style="background:var(--card-bg);">
    <div class="container">
        <div style="text-align:center; margin-bottom:3rem;">
            <p
                style="color:var(--primary-color); font-weight:700; font-size:0.85rem; text-transform:uppercase; letter-spacing:2px; margin-bottom:0.5rem;">
                <?= $t['step_online'] ?>
            </p>
            <h2 class="section-heading"><?= $t['internship_system_title'] ?></h2>
            <p class="section-sub"><?= $t['internship_system_subtitle'] ?></p>
        </div>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1.5rem;">
            <?php
            $features_intern = [
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
                    'title' => $t['role_student'],
                    'desc' => $lang === 'th' ? 'ยื่นคำขอฝึกงาน ติดตามสถานะการอนุมัติ และดูข้อมูลบริษัทที่สมัคร' : 'Submit internship requests, track approval status and view company info.',
                    'link' => 'login.php',
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M7 7h4M7 11h2"/><circle cx="15" cy="9" r="2"/></svg>',
                    'title' => $t['role_teacher'],
                    'desc' => $lang === 'th' ? 'นิเทศนิสิตฝึกงาน ประเมินผล และติดตามความก้าวหน้า' : 'Supervise interns, evaluate performance and track progress.',
                    'link' => 'login.php',
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                    'title' => $t['role_staff'],
                    'desc' => $lang === 'th' ? 'จัดการคำขอฝึกงาน อนุมัติ/ปฏิเสธ และดูแลข้อมูลนิสิตทั้งหมด' : 'Manage internship requests, approve/reject and oversee all student data.',
                    'link' => 'login.php',
                ],
            ];
            foreach ($features_intern as $fi):
                ?>
                <div style="background:var(--bg-color); border-radius:12px; padding:2rem; border:1px solid var(--border-color);
                        transition:transform 0.25s, box-shadow 0.25s;"
                    onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 30px rgba(0,0,0,0.1)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div
                        style="width:64px; height:64px; background:rgba(229,9,20,0.1); border-radius:12px;
                             display:flex; align-items:center; justify-content:center; color:var(--primary-color); margin-bottom:1.25rem;">
                        <?= $fi['icon'] ?>
                    </div>
                    <h3 style="font-size:1.2rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-color);">
                        <?= $fi['title'] ?>
                    </h3>
                    <p
                        style="font-size:0.9rem; opacity:0.7; line-height:1.7; margin-bottom:1.5rem; color:var(--text-color);">
                        <?= $fi['desc'] ?>
                    </p>
                    <a href="<?= $fi['link'] ?>"
                        style="display:inline-flex; align-items:center; gap:0.4rem; color:var(--primary-color); font-weight:600; font-size:0.9rem; text-decoration:none;">
                        <?= $t['login'] ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =============================================
     TEACHERS SECTION
     ============================================= -->
<section id="teachers" class="index-section" style="background:var(--bg-color);">
    <div class="container">
        <div style="text-align:center; margin-bottom:1rem;">
            <p
                style="color:var(--primary-color); font-weight:700; font-size:0.85rem; text-transform:uppercase; letter-spacing:2px; margin-bottom:0.5rem;">
                <?= $t['Personnel'] ?>
            </p>
            <h2 class="section-heading"><?= $t['our_teachers'] ?></h2>
            <p class="section-sub"><?= $t['teachers_subtitle'] ?></p>
        </div>
        <div class="teachers-grid">
            <?php
            try {
                $stmt_t = $conn->query("SELECT * FROM teachers_info ORDER BY id ASC");
                while ($teacher = $stmt_t->fetch(PDO::FETCH_ASSOC)):
                    $t_name = $lang === 'en' ? ($teacher['full_name_en'] ?? $teacher['full_name']) : $teacher['full_name'];
                    $t_pos = $lang === 'en' ? ($teacher['position_en'] ?? $teacher['position']) : $teacher['position'];
                    // education = วุฒิการศึกษา, detail_en = ข้อมูลติดต่อ/อีเมล (ใช้ทั้งสองภาษาเพราะ column นี้เก็บอีเมล)
                    $t_edu = $teacher['education'] ?? '';
                    $t_detail = $teacher['detail_en'] ?? ''; // เก็บอีเมลอาจารย์ ไม่ว่าจะภาษาไหน
                    $expected_img = 'assets/images/d' . intval($teacher['id']) . '.jpg';
                    $t_img = file_exists(__DIR__ . '/' . $expected_img) ? $expected_img : 'assets/images/t.jpg';
                    ?>
                    <div class="teacher-card">
                        <img src="<?= htmlspecialchars($base_url . '/' . $t_img) ?>" alt="<?= htmlspecialchars($t_name) ?>"
                            class="teacher-img">
                        <div class="teacher-name"><?= htmlspecialchars($t_name) ?></div>
                        <div class="teacher-position"><?= htmlspecialchars($t_pos) ?></div>
                        <?php if ($t_edu): ?>
                            <div class="teacher-desc"><?= htmlspecialchars($t_edu) ?></div>
                        <?php endif; ?>
                        <?php if ($t_detail): ?>
                            <div class="teacher-desc" style="font-size:0.82rem; opacity:0.65; margin-top:0.25rem;">
                                <?= htmlspecialchars($t_detail) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                endwhile;
            } catch (PDOException $e) {
            }
            ?>
        </div>
    </div>
</section>

<!-- =============================================
     VIDEO SECTION
     ============================================= -->
<section class="index-section" style="background:var(--card-bg);">
    <div class="container">
        <div style="text-align:center; margin-bottom:2rem;">
            <p
                style="color:var(--primary-color); font-weight:700; font-size:0.85rem; text-transform:uppercase; letter-spacing:2px; margin-bottom:0.5rem;">
                <?= $t['intro_video'] ?>
            </p>
            <h2 class="section-heading"><?= $t['intro_video_title'] ?></h2>
        </div>
        <div
            style="text-align:center; background:var(--bg-color); padding:1.5rem; border-radius:12px; border:1px solid var(--border-color);">
            <video controls
                style="width:100%; max-width:800px; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.15);">
                <source src="<?= htmlspecialchars($base_url) ?>/assets/videos/intro.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</section>

<!-- =============================================
     NEWS SECTION
     ============================================= -->
<section id="news" class="index-section" style="background:var(--bg-color);">
    <div class="container">
        <div
            style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <p
                    style="color:var(--primary-color); font-weight:700; font-size:0.85rem; text-transform:uppercase; letter-spacing:2px; margin-bottom:0.5rem;">
                    <?= $t['latest_news'] ?>
                </p>
                <h2 class="section-heading" style="margin-bottom:0;"><?= $t['news_activities'] ?></h2>
            </div>
        </div>
        <div class="teachers-grid" style="margin-top:0; gap:1.25rem;">
            <?php
            try {
                $stmt_news = $conn->query("SELECT * FROM news_activities ORDER BY created_at DESC LIMIT 6");
                $news_rows = $stmt_news->fetchAll(PDO::FETCH_ASSOC);
                if (count($news_rows) > 0):
                    foreach ($news_rows as $news):
                        ?>
                        <div class="card" style="transition:transform 0.22s, box-shadow 0.22s; cursor:default;"
                            onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 30px rgba(0,0,0,0.12)'"
                            onmouseout="this.style.transform='';this.style.boxShadow=''">
                            <div
                                style="display:flex; align-items:center; gap:0.5rem; color:var(--primary-color); font-weight:700; margin-bottom:0.75rem; font-size:0.8rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                                <?= date('d M Y', strtotime($news['created_at'])) ?>
                            </div>
                            <h3
                                style="font-size:1rem; font-weight:700; margin-bottom:0.5rem; line-height:1.4; color:var(--text-color);">
                                <?= htmlspecialchars($news['title']) ?>
                            </h3>
                            <p style="font-size:0.88rem; line-height:1.65; opacity:0.7; color:var(--text-color);">
                                <?= htmlspecialchars(mb_substr($news['content'], 0, 110)) ?>...
                            </p>
                        </div>
                        <?php
                    endforeach;
                else:
                    ?>
                    <div style="grid-column:1/-1; text-align:center; padding:3rem; opacity:0.5; color:var(--text-color);">
                        <?= $t['no_news'] ?>
                    </div>
                    <?php
                endif;
            } catch (PDOException $e) {
            }
            ?>
        </div>
    </div>
</section>

<!-- =============================================
     CTA SECTION
     ============================================= -->
<?php if (!isset($_SESSION['user_id'])): ?>
    <section style="background:var(--primary-color); padding:4rem 2rem; text-align:center; color:#fff;">
        <h2 style="font-size:2rem; font-weight:800; margin-bottom:1rem;"><?= $t['ready_start'] ?></h2>
        <p
            style="font-size:1.05rem; opacity:0.9; margin-bottom:2rem; max-width:500px; margin-left:auto; margin-right:auto;">
            <?= $t['ready_desc'] ?>
        </p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="register.php" class="btn"
                style="background:#fff; color:var(--primary-color); font-weight:700; font-size:1rem; padding:0.85rem 2.5rem; border-radius:8px;">
                <?= $t['register'] ?>
            </a>
            <a href="login.php" class="btn"
                style="background:transparent; color:#fff; border:2px solid rgba(255,255,255,0.6); font-weight:700; font-size:1rem; padding:0.85rem 2.5rem; border-radius:8px;">
                <?= $t['login'] ?>
            </a>
        </div>
    </section>
<?php endif; ?>

<!-- =============================================
     CURRICULUM MODAL
     ============================================= -->
<div id="curriculumModal" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%;
            overflow:auto; background:rgba(0,0,0,0.75); backdrop-filter:blur(6px);
            align-items:center; justify-content:center;">
    <div style="background:var(--card-bg); margin:auto; padding:2.5rem; border-radius:12px; max-width:660px; width:90%;
                color:var(--text-color); box-shadow:0 20px 60px rgba(0,0,0,0.4); position:relative;
                border-top:4px solid var(--primary-color); max-height:85vh; overflow-y:auto;">
        <button onclick="document.getElementById('curriculumModal').style.display='none'" style="position:absolute; right:1rem; top:1rem; background:none; border:none; font-size:1.5rem;
                       cursor:pointer; color:var(--text-color); opacity:0.5; line-height:1; transition:0.2s;"
            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">
            &#x2715;
        </button>

        <h2 style="font-size:1.6rem; font-weight:800; margin-bottom:2rem; text-align:center;">
            <?= $t['curriculum_modal_title'] ?>
        </h2>

        <div style="display:flex; flex-direction:column; gap:1rem;">
            <?php
            $curriculum_items = [
                ['รหัสหลักสูตร', '25520091104002'],
                ['ชื่อภาษาไทย', 'หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา'],
                ['ชื่อภาษาอังกฤษ', 'Bachelor of Arts Program in Information Studies'],
                ['ชื่อปริญญา (ไทย)', 'ศิลปศาสตรบัณฑิต (สารสนเทศศึกษา) / ศศ.บ. (สารสนเทศศึกษา)'],
                ['ชื่อปริญญา (EN)', 'Bachelor of Arts (Information Studies) / B.A. (Information Studies)'],
                ['ระยะเวลาการศึกษา', '4 ปี'],
                ['หน่วยกิตรวม', 'ไม่น้อยกว่า 129 หน่วยกิต'],
                ['ภาษาที่ใช้', 'ภาษาไทย'],
                ['สถานที่จัดการศึกษา', 'มหาวิทยาลัยศรีนครินทรวิโรฒ'],
            ];
            foreach ($curriculum_items as [$k, $v]):
                ?>
                <div
                    style="display:flex; gap:0.75rem; padding:0.75rem; background:var(--bg-color); border-radius:8px; border-left:3px solid var(--primary-color);">
                    <span
                        style="font-weight:700; font-size:0.85rem; color:var(--primary-color); min-width:160px; flex-shrink:0;"><?= $k ?></span>
                    <span style="font-size:0.9rem;"><?= $v ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    /* ปิด Modal เมื่อคลิกพื้นที่สีดำนอก popup */
    document.getElementById('curriculumModal').addEventListener('click', function (e) {
        if (e.target === this) this.style.display = 'none';
    });
</script>

<!-- =============================================
     FACULTY FOOTER — ข้อมูลติดต่อคณะมนุษยศาสตร์
     แสดงที่ด้านล่างสุดของหน้า index
     ============================================= -->
<footer class="faculty-footer">
    <div class="faculty-footer-inner">

        <!-- คอลัมน์ 1: โลโก้ + ชื่อคณะ -->
        <div class="faculty-footer-col faculty-footer-brand">
            <div class="faculty-footer-logo">
                <!-- SVG ไอคอนมหาวิทยาลัย -->
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                    <path d="M6 12v5c3 3 9 3 12 0v-5" />
                </svg>
                <span>SWU</span>
            </div>
            <h3 class="faculty-footer-title"><?= $t['faculty_name'] ?></h3>
            <p class="faculty-footer-subtitle"><?= $t['faculty_name_en'] ?><br>Srinakharinwirot University</p>
            <!-- ลิงก์ระบบฝึกงาน -->
            <a href="<?= $base_url ?>/index.php" class="faculty-internship-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" />
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                </svg>
                <?= $t['online_internship_system'] ?>
            </a>
        </div>

        <!-- คอลัมน์ 2: ข้อมูลติดต่อ -->
        <div class="faculty-footer-col">
            <h4 class="faculty-footer-col-title"><?= $t['contact_us'] ?></h4>
            <ul class="faculty-footer-contact-list">
                <!-- ที่อยู่ -->
                <li>
                    <span class="faculty-contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </span>
                    <span><?= $t['thai_address'] ?><br>
                        <small><?= $t['en_address'] ?></small>
                    </span>
                </li>
                <!-- อีเมล -->
                <li>
                    <span class="faculty-contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </span>
                    <a href="mailto:huswu@g.swu.ac.th">huswu@g.swu.ac.th</a>
                </li>
                <!-- โทรศัพท์ -->
                <li>
                    <span class="faculty-contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.1a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.61 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.08 6.08l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                    </span>
                    <a href="tel:+6626495000">Tel. (662) 649-5000 ext. 16292</a>
                </li>
            </ul>
        </div>

        <!-- คอลัมน์ 3: เวลาทำการ -->
        <div class="faculty-footer-col">
            <h4 class="faculty-footer-col-title"><?= $t['office_hours_title'] ?></h4>
            <div class="faculty-hours">
                <!-- วันทำการ -->
                <div class="faculty-hours-row">
                    <span class="faculty-hours-day">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <?= $t['mon_fri'] ?>
                    </span>
                    <span class="faculty-hours-time"><?= $t['office_hours_desc'] ?></span>
                </div>
                <!-- วันปิด -->
                <div class="faculty-hours-closed">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                    </svg>
                    <?= $t['closed_holidays'] ?>
                </div>
                <!-- Office Hours (English) -->
                <div class="faculty-hours-en">
                    <strong><?= $t['office_hours_en_title'] ?></strong><br>
                    <?= $t['office_hours_en_desc'] ?><br>
                    <small><?= $t['office_hours_en_note'] ?></small>
                </div>
            </div>
        </div>

        <!-- คอลัมน์ 4: แผนที่ -->
        <div class="faculty-footer-col">
            <h4 class="faculty-footer-col-title"><?= $t['map_title'] ?></h4>
            <!-- Google Maps embed — คณะมนุษยศาสตร์ มศว -->
            <div class="faculty-map-wrap">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3876.055948016272!2d100.56787157592816!3d13.737038586744547!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29ed0ccfff0d5%3A0x3db28a4e76fc33f6!2sSrinakharinwirot%20University!5e0!3m2!1sth!2sth!4v1712823600000!5m2!1sth!2sth"
                    width="100%" height="160" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" title="แผนที่คณะมนุษยศาสตร์ มศว">
                </iframe>
                <!-- ลิงก์เปิด Google Maps -->
                <a href="https://goo.gl/maps/swu" target="_blank" class="faculty-map-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg>
                    <?= $t['open_google_maps'] ?>
                </a>
            </div>
        </div>

    </div><!-- /.faculty-footer-inner -->
</footer>

<?php require_once 'includes/footer.php'; ?>