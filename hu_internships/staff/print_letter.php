<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

checkLogin();

if (!isset($_GET['id'])) {
    die("No request ID specified.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT r.*, u.fullname, si.major, si.academic_year, adv.fullname AS advisor_name 
    FROM internship_requests r 
    JOIN users u ON r.student_id = u.username 
    LEFT JOIN students_info si ON r.student_id = si.student_id
    LEFT JOIN users adv ON r.advisor_id = adv.username 
    WHERE r.request_id = ?
");
$stmt->execute([$id]);
$req = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$req) {
    die("Request not found.");
}

// ตรวจสอบสิทธิ์การเข้าถึง
if ($_SESSION['role'] === 'student' && $req['student_id'] !== $_SESSION['user_id']) {
    die("Unauthorized access.");
}

// Convert dates to Thai format
function DateThai($strDate) {
    if (!$strDate) return '-';
    $strYear = date("Y",strtotime($strDate))+543;
    $strMonth= date("n",strtotime($strDate));
    $strDay= date("j",strtotime($strDate));
    $strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
    $strMonthThai=$strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หนังสือส่งตัวฝึกงาน - <?= htmlspecialchars($req['fullname']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            padding: 0;
            background: #e2e8f0;
            font-size: 16pt;
            line-height: 1.5;
            color: #000;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 25mm 20mm 25mm; /* ขอบ A4 สำหรับเอกสารราชการ */
            margin: 10mm auto;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
            font-family: inherit;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
            font-family: inherit;
        }
        @media print {
            body { background: white; }
            .page {
                margin: 0;
                border: none;
                box-shadow: none;
                width: 100%;
                padding: 10mm 15mm;
            }
            .print-btn, .back-btn { display: none !important; }
        }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .logo { width: 60px; height: auto; flex-shrink: 0; text-align: center; margin: 0 auto;}
        .doc-no { text-align: left; width: 33%; font-size: 15pt; }
        .uni-address { text-align: right; width: 33%; font-size: 15pt; }
        .date { text-align: center; margin-top: 20px; padding-left: 25%; font-size: 15pt; }
        .subject { margin-top: 20px; font-size: 15pt; }
        .content { margin-top: 20px; text-align: justify; text-justify: distribute; font-size: 15pt; }
        .signature { margin-top: 60px; text-align: center; padding-left: 45%; font-size: 15pt; }
        .footer-info { margin-top: 80px; font-size: 13pt; line-height: 1.2; }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ พิมพ์เอกสาร</button>
    <a href="update_status.php?id=<?= $id ?>" class="back-btn">&larr; ย้อนกลับ</a>

    <div class="page">
        <div class="header">
            <div class="doc-no">ที่ อว ๘๑.... / </div>
            <div class="logo">
                <img src="../assets/krut.png" alt="ครุฑ" style="width: 55px; display: block; margin: 0 auto; margin-top: -15px;" onerror="this.style.display='none'">
            </div>
            <div class="uni-address">
                คณะมนุษยศาสตร์<br>
                มหาวิทยาลัยศรีนครินทรวิโรฒ<br>
                สุขุมวิท 23 กรุงเทพฯ 10110
            </div>
        </div>

        <div class="date">
            <?= DateThai(date('Y-m-d')) ?>
        </div>

        <div class="subject">
            <strong>เรื่อง</strong> ขอความอนุเคราะห์รับนิสิตเข้าฝึกประสบการณ์วิชาชีพ<br>
            <strong>เรียน</strong> <?= htmlspecialchars($req['contact_position'] ?: 'ผู้จัดการ') ?> <?= htmlspecialchars($req['company_name']) ?>
        </div>

        <div class="content">
            <p style="text-indent: 50px;">
                ด้วยหลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ ได้จัดการเรียนการสอนที่มุ่งเน้นให้นิสิตได้รับประสบการณ์ตรงจากการปฏิบัติงานจริงในสถานประกอบการ เพื่อให้เกิดทักษะและความเข้าใจในการทำงาน ซึ่งเป็นส่วนหนึ่งของการศึกษาตามหลักสูตร
            </p>
            <p style="text-indent: 50px;">
                คณะมนุษยศาสตร์พิจารณาแล้วเห็นว่า หน่วยงานของท่านเป็นหน่วยงานที่มีชื่อเสียงและมีศักยภาพสูง จึงใคร่ขอความอนุเคราะห์รับ <strong><?= htmlspecialchars($req['fullname']) ?></strong> รหัสประจำตัวนิสิต <?= htmlspecialchars($req['student_id']) ?> สาขาวิชา<?= htmlspecialchars($req['major'] ?: 'สารสนเทศศึกษา') ?> เข้าฝึกประสบการณ์วิชาชีพในหน่วยงานของท่าน ในระหว่างวันที่ <?= DateThai($req['internship_start']) ?> ถึง <?= DateThai($req['internship_end']) ?>
            </p>
            <p style="text-indent: 50px;">
                จึงเรียนมาเพื่อโปรดพิจารณาให้ความอนุเคราะห์ และขอขอบคุณมา ณ โอกาสนี้
            </p>
        </div>

        <div class="signature">
            ขอแสดงความนับถือ<br><br><br>
            (.......................................................)<br>
            คณบดีคณะมนุษยศาสตร์<br>
            มหาวิทยาลัยศรีนครินทรวิโรฒ
        </div>

        <div class="footer-info">
            สำนักงานคณบดี<br>
            โทรศัพท์ 0-2649-5000<br>
            อาจารย์นิเทศ: <?= htmlspecialchars($req['advisor_name'] ?: '-') ?>
        </div>
    </div>
</body>
</html>
