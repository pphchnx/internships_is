-- MySQL dump 10.13  Distrib 8.0.46, for macos15 (arm64)
--
-- Host: localhost    Database: internship_system
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `internship_requests`
--

DROP TABLE IF EXISTS `internship_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `internship_requests` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'รหัสนิสิต',
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ชื่อสถานประกอบการ',
  `company_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'ที่อยู่สถานประกอบการ',
  `contact_person` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ชื่อผู้ติดต่อ',
  `status` int DEFAULT '1',
  `request_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `company_type` enum('private','government','ngo','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'private' COMMENT 'ประเภทองค์กร',
  `company_province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'จังหวัด',
  `company_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เบอร์โทรศัพท์ (องค์กร)',
  `company_website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เว็บไซต์',
  `contact_position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ตำแหน่งผู้ติดต่อ',
  `contact_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เบอร์โทรศัพท์ผู้ติดต่อ',
  `contact_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'อีเมลผู้ติดต่อ',
  `department` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ตำแหน่ง',
  `internship_start` date DEFAULT NULL COMMENT 'วันเริ่มฝึกงาน',
  `internship_end` date DEFAULT NULL COMMENT 'วันสิ้นสุดฝึกงาน',
  `work_days` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'จันทร์-ศุกร์' COMMENT 'วันทำงาน',
  `work_hours` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '09:00-18:00' COMMENT 'เวลาทำงาน',
  `advisor_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ไอดีอาจารย์นิเทศ',
  `advisor_note` text COLLATE utf8mb4_unicode_ci,
  `doc_status` tinyint DEFAULT '0',
  `remark` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `certificate_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เอกสารรับรองการฝึกงาน',
  PRIMARY KEY (`request_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `internship_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ยื่นคำขอฝึกงาน';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internship_requests`
--

LOCK TABLES `internship_requests` WRITE;
/*!40000 ALTER TABLE `internship_requests` DISABLE KEYS */;
INSERT INTO `internship_requests` VALUES (3,'1429900570124','abc','bkk','ken',3,'2026-05-02 22:40:26','private','bkk','009900','abc@gmail.com','it mng','99009988','ken@gmail.com','dev','2026-06-01','2026-10-31','จันทร์-ศุกร์','09:00-18:00','t001','',0,'','2026-05-02 22:43:58',NULL);
/*!40000 ALTER TABLE `internship_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_activities`
--

DROP TABLE IF EXISTS `news_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'หัวข้อ',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `news_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'รูปข่าว',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ข่าว';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_activities`
--

LOCK TABLES `news_activities` WRITE;
/*!40000 ALTER TABLE `news_activities` DISABLE KEYS */;
INSERT INTO `news_activities` VALUES (8,'ฝ่ายวิจัยคณะมนุษยศาสตร์ ขอแสดงความยินดีและร่วมเผยแพร่ผลงานวิจัยจากคณาจารย์หลักสูตร ศศ.ม. สารสนเทศศึกษา กลุ่มสาขาวิชาพัฒนาศักยภาพมนุษย์ ได้รับการตีพิมพ์ในฐานข้อมูลระดับนานาชาติ (SCOPUS)','? ฝ่ายวิจัยคณะมนุษยศาสตร์ ขอแสดงความยินดีและร่วมเผยแพร่ผลงานวิจัยระดับนานาชาติ ?\r\nผลงานวิจัยจากคณาจารย์หลักสูตร ศศ.ม. สารสนเทศศึกษา กลุ่มสาขาวิชาพัฒนาศักยภาพมนุษย์ ได้รับการตีพิมพ์ในฐานข้อมูลระดับนานาชาติ (SCOPUS)\r\n? บทความเรื่อง: “Information Services of Bangkok Metropolitan Administration’s Discovery Learning Libraries: Roles and Potential in Driving the Sustainable Development Goals (SDGs)”\r\n? คณะผู้วิจัย:\r\n? อ. ดร.ศุมรรษตรา แสนวา  \r\n? ผศ. ดร.ศศิพิมล ประพินพงศกร\r\n? ได้รับทุนสนับสนุนการวิจัยจากงบประมาณเงินรายได้คณะมนุษยศาสตร์ ประจำปีงบประมาณ พ.ศ. 2567\r\n? อ่านบทความฉบับเต็ม: ? https://so08.tci-thaijo.org/index.php/artssu/article/view/5283\r\n\r\n#คณะมนุษยศาสตร์  #HUSWU  #SCOPUS  #Research #HUSWUResearch #SWUIS #SDGs','2026-04-27 12:32:36','news_1777293156_69ef5764ed872.jpeg'),(9,'23 มีนาคม 2569 หลักสูตร ศศ.บ.สารสนเทศศึกษา จัดโครงการปรับปรุงหลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา พ.ศ. 2570 ครั้งที่ 2','?หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ ได้จัดโครงการปรับปรุงหลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา พ.ศ. 2570 ครั้งที่ 2 ในวันที่ 23 มีนาคม 2569  โครงการดังกล่าวมีวัตถุประสงค์เพื่อทบทวนและปรับปรุงหลักสูตรให้แล้วเสร็จและพร้อมใช้ภายในปีการศึกษา 2570 เพื่อให้หลักสูตรมีความทันสมัยที่จะผลิตบัณฑิตให้สอดคล้องกับวิสัยทัศน์ของมหาวิทยาลัยตามความต้องการของตลาดแรงงานและสังคม ดังนั้นในการจัดโครงการฯ ครั้งที่ 2 จึงได้มีการประชุมกับคณาจารย์ในหลักสูตรเพื่อวางแผนการทบทวนและปรับปรุงหลักสูตรฯ  ทั้งนี้ มีกำหนดจัดโครงการในครั้งถัดไปเป็นวันที่ 20 และ 27 เมษายน 2569 ?\r\n\r\n \r\n\r\n#hmswu','2026-04-27 12:35:16','news_1777293316_69ef5804a1b7f.jpeg'),(10,'โครงการ “พัฒนาระบบสารสนเทศเพื่อการบริหารจัดการองค์กร”','วันจันทร์ที่ 16 มีนาคม 2569\r\nคณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ จัดโครงการ “พัฒนาระบบสารสนเทศเพื่อการบริหารจัดการองค์กร” ณ ห้อง 38-0301 ชั้น 3 อาคาร 38 คณะมนุษยศาสตร์ โดยได้รับเกียรติจาก ผู้ช่วยศาสตราจารย์ ดร.อัญชลี จันทร์เสม คณบดีคณะมนุษยศาสตร์ กล่าวเปิดโครงการ\r\nภายในกิจกรรมมีการอบรมเชิงปฏิบัติการเกี่ยวกับ Automation, Workflow และ Cyber Security เพื่อเสริมทักษะการใช้เทคโนโลยีในการบริหารจัดการงานและการใช้ระบบสารสนเทศอย่างปลอดภัย\r\nคณะมนุษยศาสตร์ขอขอบพระคุณวิทยากรจากสาขาวิชาสารสนเทศศึกษา ได้แก่\r\n?️ อาจารย์ ดร.ดิษฐ์ สุทธิวงศ์\r\n?️ ผู้ช่วยศาสตราจารย์ ดร.วิภากร วัฒนสินธุ์\r\n?️ ผู้ช่วยศาสตราจารย์ ดร.ดุษฎี สีวังคำ\r\n?️ อาจารย์ ดร.โชคธำรงค์ จงจอหอ\r\n?️ อาจารย์ ดร.ฐิติ อติชาติชยากร\r\nที่ได้ร่วมถ่ายทอดความรู้และประสบการณ์แก่บุคลากรของคณะ เพื่อสนับสนุนการพัฒนาองค์กรสู่ Digital Organization\r\n#HumanitiesSWU\r\n#DigitalTransformation\r\n#Automation\r\n#CyberSecurity','2026-04-27 12:36:37','news_1777293397_69ef5855cf710.jpeg');
/*!40000 ALTER TABLE `news_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_info`
--

DROP TABLE IF EXISTS `staff_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_info` (
  `staff_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รหัสเจ้าหน้าที่',
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ตำแหน่ง',
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'แผนก / หน่วยงาน',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เบอร์โทร',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'อีเมล',
  PRIMARY KEY (`staff_id`),
  CONSTRAINT `staff_info_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='เจ้าหน้าที่';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_info`
--

LOCK TABLES `staff_info` WRITE;
/*!40000 ALTER TABLE `staff_info` DISABLE KEYS */;
INSERT INTO `staff_info` VALUES ('67101010659','Admin','Internship Office','0811111111','staff1@swu.ac.th'),('67101010667','Officer','Internship Office','0811111112','staff2@swu.ac.th'),('67101010668','Officer','Internship Office','0811111113','staff3@swu.ac.th'),('67101010679','Coordinator','Internship Office','0811111114','staff4@swu.ac.th'),('67101010680','Coordinator','Internship Office','0811111115','staff5@swu.ac.th'),('67101010685','Admin','Internship Office','0811111116','staff6@swu.ac.th');
/*!40000 ALTER TABLE `staff_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students_info`
--

DROP TABLE IF EXISTS `students_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students_info` (
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รหัสนิสิต',
  `prefix_th` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'คำนำหน้าภาษาไทย',
  `first_name_th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ชื่อภาษาไทย',
  `last_name_th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'นามสกุลภาษาไทย',
  `prefix_en` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'คำนำหน้าภาษาอังกฤษ',
  `first_name_en` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ชื่อภาษาอังกฤษ',
  `last_name_en` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'นามสกุลภาษาอังกฤษ',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เบอร์โทร',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'อีเมล',
  `major` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'สาขาวิชา',
  `student_type` enum('regular','special') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular' COMMENT 'ภาคปกติ/พิเศษ',
  `academic_year` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ปีที่เข้าเรียน',
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  CONSTRAINT `students_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='นิสิต';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students_info`
--

LOCK TABLES `students_info` WRITE;
/*!40000 ALTER TABLE `students_info` DISABLE KEYS */;
INSERT INTO `students_info` VALUES ('1429900570124','นาย','ภูชนะ','วิรัญจะ','Mr.','phuchana','wirancha','0966690393','phuchana.wirancha@gmail.com','ศศ.บ. สารสนเทศศึกษา (ภาคพิเศษ)','special','2567',NULL),('6501001','นาย','สำเร็จ','เสร็จสมบูรณ์','Mr.','Samret','Setsomboon','0890000001','6501001@swu.ac.th','Information Studies','regular',NULL,NULL),('6501002','นางสาว','มุ่งมั่น','ตั้งใจ','Miss','Mungman','Tangjai','0890000002','6501002@swu.ac.th','Information Studies','regular',NULL,NULL),('6501003','นาย','อดทน','พยายาม','Mr.','Odtone','Payayam','0890000003','6501003@swu.ac.th','Information Studies','regular',NULL,NULL),('6501004','นางสาว','สู้ตาย','ถวายหัว','Miss','Sutai','Thawaihua','0890000004','6501004@swu.ac.th','Information Studies','regular',NULL,NULL),('6501005','นาย','ก้าวหน้า','เดินไกล','Mr.','Kaona','Doenkai','0890000005','6501005@swu.ac.th','Information Studies','regular',NULL,NULL),('6601001','นาย','อัศวิน','ขี่ม้าขาว','Mr.','Asawin','Khimakao','0890000011','6601001@swu.ac.th','Information Studies','regular',NULL,NULL),('6601002','นางสาว','แก้วตา','ขวัญใจ','Miss','Kaewta','Kwanjai','0890000012','6601002@swu.ac.th','Information Studies','regular',NULL,NULL),('6601003','นาย','เพชรแท้','แข็งแกร่ง','Mr.','Phetthae','Khaengkraeng','0890000013','6601003@swu.ac.th','Information Studies','regular',NULL,NULL),('6601004','นางสาว','พลอยใส','แวววาว','Miss','Ploysai','Waewwao','0890000014','6601004@swu.ac.th','Information Studies','regular',NULL,NULL),('6601005','นาย','ทองดี','มีค่า','Mr.','Thongdee','Mikha','0890000015','6601005@swu.ac.th','Information Studies','regular',NULL,NULL),('6701001','นาย','ณรงค์ศักดิ์','ขยันดี','Mr.','Narongsak','Khayandee','0890000041','6701011@swu.ac.th','Information Studies','regular',NULL,NULL),('6701002','นางสาว','กนกวรรณ','ตั้งใจเรียน','Miss','Kanokwan','Tangjairian','0890000042','6701012@swu.ac.th','Information Studies','regular',NULL,NULL),('6701003','นาย','ศุภชัย','มุ่งมั่น','Mr.','Supachai','Mungman','0890000043','6701013@swu.ac.th','Information Studies','regular',NULL,NULL),('6701004','นางสาว','พัชรินทร์','เรียบร้อย','Miss','Patcharin','Riabroi','0890000044','6701014@swu.ac.th','Information Studies','regular',NULL,NULL),('6701005','นาย','ธีรเดช','อดทน','Mr.','Theeradech','Odtone','0890000045','6701015@swu.ac.th','Information Studies','regular',NULL,NULL),('6701006','นาย','ภาคภูมิ','ใจดี','Mr.','Phakphum','Jaidee','0890000026','6701006@swu.ac.th','Information Studies','special',NULL,NULL),('6701007','นางสาว','นภัส','สดใส','Miss','Naphat','Sodsai','0890000027','6701007@swu.ac.th','Information Studies','special',NULL,NULL),('6701008','นาย','กิตติ','เก่งงาน','Mr.','Kitti','Kengngan','0890000028','6701008@swu.ac.th','Information Studies','special',NULL,NULL),('6701009','นางสาว','วริศา','ขยัน','Miss','Warisa','Khayan','0890000029','6701009@swu.ac.th','Information Studies','special',NULL,NULL),('6701010','นาย','ธีรภัทร','มุ่งมั่น','Mr.','Theerapat','Mungman','0890000030','6701010@swu.ac.th','Information Studies','special',NULL,NULL),('671010679','นางสาว','พัชรภา','เกิดวิชิต','Miss','patcharapa','Kerdvichit','0944307019','patchakerd1010@gmail.com','ศศ.บ. สารสนเทศศึกษา (ภาคพิเศษ)','special','2567',NULL),('671010680','นางสาว','พัทนันท์','ทองหล่อ','Miss','Phatthanan','Thonglor','0924495069','phatthanan.thonglor@g.swu.ac.th','ศศ.บ. สารสนเทศศึกษา (ภาคพิเศษ)','special','2567',NULL),('671010685','นาย','ภูชนะ','วิรัญจะ','Mr.','phuchana','wirancha','0966690393','phuchana.wirancha@g.swu.ac.th','ศศ.บ. สารสนเทศศึกษา (ภาคพิเศษ)','special','2567',NULL),('6801001','นาย','สมชาย','รักดี','Mr.','Somchai','Rakdee','0890000031','6801001@swu.ac.th','Information Studies','regular',NULL,NULL),('6801002','นางสาว','สมศรี','มีสุข','Miss','Somsri','Meesuk','0890000032','6801002@swu.ac.th','Information Studies','regular',NULL,NULL),('6801003','นาย','มานะ','ขยันงาน','Mr.','Mana','Khayannan','0890000033','6801003@swu.ac.th','Information Studies','regular',NULL,NULL),('6801004','นางสาว','ชูใจ','ใจดี','Miss','Choojai','Jaidee','0890000034','6801004@swu.ac.th','Information Studies','regular',NULL,NULL),('6801005','นาย','ปิติ','ยินดี','Mr.','Piti','Yindee','0890000035','6801005@swu.ac.th','Information Studies','regular',NULL,NULL),('6801006','นาย','ธันวา','ตั้งใจ','Mr.','Thanwa','Tangjai','0890000036','6801006@swu.ac.th','Information Studies','special',NULL,NULL),('6801007','นางสาว','พิมพ์ชนก','เรียบร้อย','Miss','Pimchanok','Riabroi','0890000037','6801007@swu.ac.th','Information Studies','special',NULL,NULL),('6801008','นาย','จักริน','อดทน','Mr.','Jakarin','Odtone','0890000038','6801008@swu.ac.th','Information Studies','special',NULL,NULL),('6801009','นางสาว','ณัฐธิดา','ร่าเริง','Miss','Nattida','Raroeng','0890000039','6801009@swu.ac.th','Information Studies','special',NULL,NULL),('6801010','นาย','ปวริศ','พัฒนา','Mr.','Pawarit','Phatthana','0890000040','6801010@swu.ac.th','Information Studies','special',NULL,NULL);
/*!40000 ALTER TABLE `students_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers_info`
--

DROP TABLE IF EXISTS `teachers_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'รหัสอาจารย์',
  `full_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ชื่อนามสกุลอาจารย์',
  `position` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ตำแหน่ง',
  `education` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'วุฒิการศึกษา',
  `full_name_en` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ชื่อนามสกุลภาษาอังกฤษ',
  `position_en` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ตำแหน่งภาษาอังกฤษ',
  `detail_en` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'อีเมล/ช่องทางการติดต่อ',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `teachers_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`username`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='อาจารย์';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers_info`
--

LOCK TABLES `teachers_info` WRITE;
/*!40000 ALTER TABLE `teachers_info` DISABLE KEYS */;
INSERT INTO `teachers_info` VALUES (1,'t001','อาจารย์ ดร. ดิษฐ์ สุทธิวงศ์','ประธานกรรมการบริหารหลักสูตร',NULL,'Lecturer Dit Suthiwong, Ph.D.','Program Chair','Email: dit@g.swu.ac.th'),(2,'t002','อาจารย์ ดร. ฐิติ อติชาติชยากร','เลขานุการหลักสูตร',NULL,'Lecturer Thiti Atichartchayakorn, Ph.D.','Program Secretary','Email: thitik@g.swu.ac.th'),(3,'t003','ผู้ช่วยศาสตราจารย์ ดร. วิภากร วัฒนสินธุ์','กรรมการหลักสูตร',NULL,'Asst. Prof. Vipakorn Vadhanasin, Ph.D.','Committee Member','Email: vipakorn@g.swu.ac.th'),(4,'t004','อาจารย์ ดร. โชคธำรงค์ จงจอหอ','กรรมการหลักสูตร',NULL,'Lecturer Chokthamrong Chongchorhor, Ph.D.','Committee Member','Email: chokthamrong@g.swu.ac.th'),(5,'t005','อาจารย์โชติมา วัฒนะ','กรรมการหลักสูตร',NULL,'Lecturer Chotima Watana','Committee Member','Email: chotimaw@g.swu.ac.th'),(6,'t006','ผู้ช่วยศาสตราจารย์ ดร. ดุษฎี สีวังคำ','อาจารย์ผู้สอน',NULL,'Asst. Prof. Dussadee Seewungkum, Ph.D.','Lecturer','Email: dussadee@g.swu.ac.th'),(7,'t007','ผู้ช่วยศาสตราจารย์ ดร. ศศิพิมล ประพินพงศกร','อาจารย์ผู้สอน',NULL,'Asst. Prof. Sasipimol Prapinpongsakorn, Ph.D.','Lecturer','Email: sasipimol@g.swu.ac.th'),(8,'t008','อาจารย์ ดร. ศุมรรษตรา แสนวา','อาจารย์ผู้สอน',NULL,'Lecturer Sumattra Saenwa, Ph.D.','Lecturer','Email: sumattra@g.swu.ac.th');
/*!40000 ALTER TABLE `teachers_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อผู้ใช้',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รหัสผ่าน',
  `fullname` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อนามสกุล',
  `role` enum('student','teacher','staff') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'นิสิต/อาจารย์/เจ้าหน้าที่',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'อีเมล',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เบอร์โทร',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active' COMMENT 'status',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('1429900570124','16112548','นายภูชนะ วิรัญจะ','student','phuchana.wirancha@gmail.com','0966690393','active','2026-04-10 17:49:38'),('6501001','1234','นายสำเร็จ เสร็จสมบูรณ์','student','6501001@swu.ac.th','0890000001','active','2026-04-27 20:57:45'),('6501002','1234','นางสาวมุ่งมั่น ตั้งใจ','student','6501002@swu.ac.th','0890000002','active','2026-04-27 20:57:45'),('6501003','1234','นายอดทน พยายาม','student','6501003@swu.ac.th','0890000003','active','2026-04-27 20:57:45'),('6501004','1234','นางสาวสู้ตาย ถวายหัว','student','6501004@swu.ac.th','0890000004','active','2026-04-27 20:57:45'),('6501005','1234','นายก้าวหน้า เดินไกล','student','6501005@swu.ac.th','0890000005','active','2026-04-27 20:57:45'),('6601001','1234','นายอัศวิน ขี่ม้าขาว','student','6601001@swu.ac.th','0890000011','active','2026-04-27 20:57:45'),('6601002','1234','นางสาวแก้วตา ขวัญใจ','student','6601002@swu.ac.th','0890000012','active','2026-04-27 20:57:45'),('6601003','1234','นายเพชรแท้ แข็งแกร่ง','student','6601003@swu.ac.th','0890000013','active','2026-04-27 20:57:45'),('6601004','1234','นางสาวพลอยใส แวววาว','student','6601004@swu.ac.th','0890000014','active','2026-04-27 20:57:45'),('6601005','1234','นายทองดี มีค่า','student','6601005@swu.ac.th','0890000015','active','2026-04-27 20:57:45'),('6701001','1234','ณรงค์ศักดิ์ ขยันดี','student','6701011@swu.ac.th','0890000041','active','2026-04-28 04:56:32'),('6701002','1234','กนกวรรณ ตั้งใจเรียน','student','6701012@swu.ac.th','0890000042','active','2026-04-28 04:56:32'),('6701003','1234','ศุภชัย มุ่งมั่น','student','6701013@swu.ac.th','0890000043','active','2026-04-28 04:56:32'),('6701004','1234','พัชรินทร์ เรียบร้อย','student','6701014@swu.ac.th','0890000044','active','2026-04-28 04:56:32'),('6701005','1234','ธีรเดช อดทน','student','6701015@swu.ac.th','0890000045','active','2026-04-28 04:56:32'),('6701006','1234','ภาคภูมิ ใจดี','student','6701006@swu.ac.th','0890000026','active','2026-04-28 05:07:09'),('6701007','1234','นภัส สดใส','student','6701007@swu.ac.th','0890000027','active','2026-04-28 05:07:09'),('6701008','1234','กิตติ เก่งงาน','student','6701008@swu.ac.th','0890000028','active','2026-04-28 05:07:09'),('6701009','1234','วริศา ขยัน','student','6701009@swu.ac.th','0890000029','active','2026-04-28 05:07:09'),('6701010','1234','ธีรภัทร มุ่งมั่น','student','6701010@swu.ac.th','0890000030','active','2026-04-28 05:07:09'),('67101010659','1234','เขมสิริ แก้วหานาม','staff','staff1@swu.ac.th','0811111111','active','2026-04-10 15:34:50'),('67101010667','1234','ธมลวรรณ เจิมมหานนท์','staff','staff2@swu.ac.th','0811111112','active','2026-04-10 15:34:50'),('67101010668','1234','ธัญรดี สุรกิจพิบูลย์','staff','staff3@swu.ac.th','0811111113','active','2026-04-10 15:34:50'),('67101010679','1234','พัชรภา เกิดวิชิต','staff','staff4@swu.ac.th','0811111114','active','2026-04-10 15:34:50'),('67101010680','1234','พัทนันท์ ทองหล่อ','staff','staff5@swu.ac.th','0811111115','active','2026-04-10 15:34:50'),('67101010685','1234','ภูชนะ วิรัญจะ','staff','staff6@swu.ac.th','0811111116','active','2026-04-10 15:34:50'),('671010679','$2y$10$ODIwevOwv5ATEMVcUWUlOeosJl/9eLvuUIO9JuYBsMQeTYFi3UwWu','พัชราภา','student','patchakerd1010@gmail.com','0944307019','active','2026-04-27 08:19:00'),('671010680','1234','พัทนันท์ ทองหล่อ','student','phatthanan.thonglor@g.swu.ac.th','0924495069','active','2026-04-23 08:38:55'),('671010685','$2y$10$DTN18YP62C5fpI8vA71L2uTsXDdSPlQ13UyDvclDbq49FnOZViGZi','นายภูชนะ วิรัญจะ','student','phuchana.wirancha@g.swu.ac.th','0966690393','active','2026-04-27 21:37:39'),('6801001','1234','นายสมชาย รักดี','student','6801001@swu.ac.th','0890000031','active','2026-04-27 20:57:45'),('6801002','1234','นางสาวสมศรี มีสุข','student','6801002@swu.ac.th','0890000032','active','2026-04-27 20:57:45'),('6801003','1234','นายมานะ ขยันงาน','student','6801003@swu.ac.th','0890000033','active','2026-04-27 20:57:45'),('6801004','1234','นางสาวชูใจ ใจดี','student','6801004@swu.ac.th','0890000034','active','2026-04-27 20:57:45'),('6801005','1234','นายปิติ ยินดี','student','6801005@swu.ac.th','0890000035','active','2026-04-27 20:57:45'),('6801006','1234','ธันวา ตั้งใจ','student','6801006@swu.ac.th','0890000036','active','2026-04-28 05:07:09'),('6801007','1234','พิมพ์ชนก เรียบร้อย','student','6801007@swu.ac.th','0890000037','active','2026-04-28 05:07:09'),('6801008','1234','จักริน อดทน','student','6801008@swu.ac.th','0890000038','active','2026-04-28 05:07:09'),('6801009','1234','ณัฐธิดา ร่าเริง','student','6801009@swu.ac.th','0890000039','active','2026-04-28 05:07:09'),('6801010','1234','ปวริศ พัฒนา','student','6801010@swu.ac.th','0890000040','active','2026-04-28 05:07:09'),('t001','1234','อาจารย์ ดร. ดิษฐ์ สุทธิวงศ์','teacher','dit@g.swu.ac.th','0820000001','active','2026-04-10 15:34:50'),('t002','1234','อาจารย์ ดร. ฐิติ อติชาติชยากร','teacher','thitik@g.swu.ac.th','0820000002','active','2026-04-10 15:34:50'),('t003','1234','ผู้ช่วยศาสตราจารย์ ดร. วิภากร วัฒนสินธุ์','teacher','vipakorn@g.swu.ac.th','0820000003','active','2026-04-10 15:34:50'),('t004','1234','อาจารย์ ดร. โชคธำรงค์ จงจอหอ','teacher','chokthamrong@g.swu.ac.th','0820000004','active','2026-04-10 15:34:50'),('t005','1234','อาจารย์โชติมา วัฒนะ','teacher','chotimaw@g.swu.ac.th','0820000005','active','2026-04-10 15:34:50'),('t006','1234','ผู้ช่วยศาสตราจารย์ ดร. ดุษฎี สีวังคำ','teacher','dussadee@g.swu.ac.th','0820000006','active','2026-04-10 15:34:50'),('t007','1234','ผู้ช่วยศาสตราจารย์ ดร. ศศิพิมล ประพินพงศกร','teacher','sasipimol@g.swu.ac.th','0820000007','active','2026-04-10 15:34:50'),('t008','1234','อาจารย์ ดร. ศุมรรษตรา แสนวา','teacher','sumattra@g.swu.ac.th','0820000008','active','2026-04-10 15:34:50');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-04 21:42:27
