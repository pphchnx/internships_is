-- =====================================
-- DATABASE
-- =====================================
CREATE DATABASE IF NOT EXISTS `internship_system`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `internship_system`;

-- =====================================
-- TABLE: users (ศูนย์กลางระบบ)
-- =====================================
CREATE TABLE `users` (
  `username` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(150) NOT NULL,
  `role` ENUM('student','teacher','staff') NOT NULL,
  `email` VARCHAR(100),
  `phone` VARCHAR(20),
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB;

-- =====================================
-- TABLE: students_info
-- =====================================
CREATE TABLE `students_info` (
  `student_id` VARCHAR(20) PRIMARY KEY,
  `prefix_th` VARCHAR(20),
  `first_name_th` VARCHAR(100),
  `last_name_th` VARCHAR(100),
  `prefix_en` VARCHAR(20),
  `first_name_en` VARCHAR(100),
  `last_name_en` VARCHAR(100),
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  `major` VARCHAR(100),
  FOREIGN KEY (`student_id`) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB;
-- เพิ่มสำหรับภาคเปภาคปก
ALTER TABLE students_info 
  ADD COLUMN student_type ENUM('regular','special') NOT NULL DEFAULT 'regular',
  ADD COLUMN academic_year VARCHAR(10) DEFAULT NULL;

-- =====================================
-- TABLE: teachers_info
-- =====================================
CREATE TABLE `teachers_info` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` VARCHAR(20),
  `full_name` VARCHAR(150),
  `position` VARCHAR(150),
  `education` TEXT,
  `full_name_en` VARCHAR(255),
  `position_en` VARCHAR(255),
  `detail_en` TEXT,
  FOREIGN KEY (`user_id`) REFERENCES users(username) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================
-- TABLE: staff_info
-- =====================================
CREATE TABLE `staff_info` (
  `staff_id` VARCHAR(20) PRIMARY KEY,
  `position` VARCHAR(100),
  `department` VARCHAR(100),
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  FOREIGN KEY (`staff_id`) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================
-- TABLE: internship_requests
-- =====================================
CREATE TABLE `internship_requests` (
  `request_id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` VARCHAR(20),
  `company_name` VARCHAR(255),
  `company_address` TEXT,
  `contact_person` VARCHAR(100),
  `status` INT DEFAULT 1,
  `request_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES users(username)
) ENGINE=InnoDB;
ALTER TABLE internship_requests
  ADD COLUMN company_type ENUM('private','government','ngo','other') DEFAULT 'private',
  ADD COLUMN company_province VARCHAR(100),
  ADD COLUMN company_phone VARCHAR(30),
  ADD COLUMN company_website VARCHAR(255),
  ADD COLUMN contact_position VARCHAR(100),
  ADD COLUMN contact_phone VARCHAR(30),
  ADD COLUMN contact_email VARCHAR(100),
  ADD COLUMN department VARCHAR(150),
  ADD COLUMN internship_start DATE,
  ADD COLUMN internship_end DATE,
  ADD COLUMN work_days VARCHAR(100) DEFAULT 'จันทร์-ศุกร์',
  ADD COLUMN work_hours VARCHAR(50) DEFAULT '09:00-18:00',
  ADD COLUMN advisor_id VARCHAR(20),
  ADD COLUMN advisor_note TEXT,
  ADD COLUMN doc_status TINYINT DEFAULT 0,
  ADD COLUMN remark TEXT,
  ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- =====================================
-- TABLE: news_activities
-- =====================================
CREATE TABLE `news_activities` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================
-- INSERT: news_activities DATA
-- =====================================
INSERT INTO `news_activities` (`title`, `content`) VALUES
('โครงการปฐมนิเทศนิสิตใหม่ 2567',
 'ขอเชิญนิสิตชั้นปีที่ 1 เข้าร่วมฟังบรรยายการใช้ชีวิตในมหาวิทยาลัย การปรับตัว และแนวทางการเรียนให้ประสบความสำเร็จ'),

('สัมมนาเตรียมความพร้อมฝึกงาน',
 'เทคนิคการเลือกสถานประกอบการ การเตรียมตัวสมัครฝึกงาน และการเขียน Resume สำหรับนิสิตชั้นปีที่ 3 และ 4'),

('กิจกรรม Open House สาขาสารสนเทศ',
 'เปิดบ้านแนะนำหลักสูตร แนะนำอาชีพในสายงานสารสนเทศ และกิจกรรมให้น้องๆ มัธยมปลายที่สนใจเข้าศึกษาต่อได้เข้าร่วม');


  -- =====================================
-- ตัวอย่างข้อมูล
-- =====================================

-- =====================================
-- INSERT: STAFF USERS + INFO
-- =====================================
INSERT INTO users VALUES
('67101010659','hash','เขมสิริ แก้วหานาม','staff','staff1@swu.ac.th','0811111111','active',NOW()),
('67101010667','hash','ธมลวรรณ เจิมมหานนท์','staff','staff2@swu.ac.th','0811111112','active',NOW()),
('67101010668','hash','ธัญรดี สุรกิจพิบูลย์','staff','staff3@swu.ac.th','0811111113','active',NOW()),
('67101010679','hash','พัชรภา เกิดวิชิต','staff','staff4@swu.ac.th','0811111114','active',NOW()),
('67101010680','hash','พัทนันท์ ทองหล่อ','staff','staff5@swu.ac.th','0811111115','active',NOW()),
('67101010685','hash','ภูชนะ วิรัญจะ','staff','staff6@swu.ac.th','0811111116','active',NOW());

INSERT INTO staff_info VALUES
('67101010659','Admin','Internship Office','0811111111','staff1@swu.ac.th'),
('67101010667','Officer','Internship Office','0811111112','staff2@swu.ac.th'),
('67101010668','Officer','Internship Office','0811111113','staff3@swu.ac.th'),
('67101010679','Coordinator','Internship Office','0811111114','staff4@swu.ac.th'),
('67101010680','Coordinator','Internship Office','0811111115','staff5@swu.ac.th'),
('67101010685','Admin','Internship Office','0811111116','staff6@swu.ac.th');

-- =====================================
-- INSERT: TEACHERS 
-- =====================================
INSERT INTO users VALUES
('t001','1234','อาจารย์ ดร. ดิษฐ์ สุทธิวงศ์','teacher','dit@g.swu.ac.th','0820000001','active',NOW()),
('t002','1234','อาจารย์ ดร. ฐิติ อติชาติชยากร','teacher','thitik@g.swu.ac.th','0820000002','active',NOW()),
('t003','1234','ผู้ช่วยศาสตราจารย์ ดร. วิภากร วัฒนสินธุ์','teacher','vipakorn@g.swu.ac.th','0820000003','active',NOW()),
('t004','1234','อาจารย์ ดร. โชคธำรงค์ จงจอหอ','teacher','chokthamrong@g.swu.ac.th','0820000004','active',NOW()),
('t005','1234','อาจารย์โชติมา วัฒนะ','teacher','chotimaw@g.swu.ac.th','0820000005','active',NOW()),
('t006','1234','ผู้ช่วยศาสตราจารย์ ดร. ดุษฎี สีวังคำ','teacher','dussadee@g.swu.ac.th','0820000006','active',NOW()),
('t007','1234','ผู้ช่วยศาสตราจารย์ ดร. ศศิพิมล ประพินพงศกร','teacher','sasipimol@g.swu.ac.th','0820000007','active',NOW()),
('t008','1234','อาจารย์ ดร. ศุมรรษตรา แสนวา','teacher','sumattra@g.swu.ac.th','0820000008','active',NOW());

-- =====================================
-- INSERT: STUDENTS USERS 
-- =====================================
INSERT INTO users VALUES
('6301001','1234','นายสำเร็จ เสร็จสมบูรณ์','student','6301001@swu.ac.th','0890000001','active',NOW()),
('6301002','1234','นางสาวมุ่งมั่น ตั้งใจ','student','6301002@swu.ac.th','0890000002','active',NOW()),
('6301003','1234','นายอดทน พยายาม','student','6301003@swu.ac.th','0890000003','active',NOW()),
('6301004','1234','นางสาวสู้ตาย ถวายหัว','student','6301004@swu.ac.th','0890000004','active',NOW()),
('6301005','1234','นายก้าวหน้า เดินไกล','student','6301005@swu.ac.th','0890000005','active',NOW()),
('6301006','1234','นางสาวสดชื่น รื่นรมย์','student','6301006@swu.ac.th','0890000006','active',NOW()),
('6301007','1234','นายร่มเย็น เป็นสุข','student','6301007@swu.ac.th','0890000007','active',NOW()),
('6301008','1234','นางสาวเบิกบาน ใจใส','student','6301008@swu.ac.th','0890000008','active',NOW()),
('6301009','1234','นายชำนาญ การงาน','student','6301009@swu.ac.th','0890000009','active',NOW()),
('6301010','1234','นางสาวเก่งจริง ยิ่งใหญ่','student','6301010@swu.ac.th','0890000010','active',NOW()),

('6401001','1234','นายอัศวิน ขี่ม้าขาว','student','6401001@swu.ac.th','0890000011','active',NOW()),
('6401002','1234','นางสาวแก้วตา ขวัญใจ','student','6401002@swu.ac.th','0890000012','active',NOW()),
('6401003','1234','นายเพชรแท้ แข็งแกร่ง','student','6401003@swu.ac.th','0890000013','active',NOW()),
('6401004','1234','นางสาวพลอยใส แวววาว','student','6401004@swu.ac.th','0890000014','active',NOW()),
('6401005','1234','นายทองดี มีค่า','student','6401005@swu.ac.th','0890000015','active',NOW()),
('6401006','1234','นางสาวเงินยวง ผ่องแผ้ว','student','6401006@swu.ac.th','0890000016','active',NOW()),
('6401007','1234','นายรวยรื่น ชื่นมื่น','student','6401007@swu.ac.th','0890000017','active',NOW()),
('6401008','1234','นางสาวมั่งมี ศรีสุข','student','6401008@swu.ac.th','0890000018','active',NOW()),
('6401009','1234','นายร่ำรวย พูนผล','student','6401009@swu.ac.th','0890000019','active',NOW()),
('6401010','1234','นางสาวโชคดี ทวีคูณ','student','6401010@swu.ac.th','0890000020','active',NOW()),

('6501001','1234','นายก้องภพ สดใส','student','6501001@swu.ac.th','0890000021','active',NOW()),
('6501002','1234','นางสาวทอฝัน วันใหม่','student','6501002@swu.ac.th','0890000022','active',NOW()),
('6501003','1234','นายอาทิตย์ ส่องแสง','student','6501003@swu.ac.th','0890000023','active',NOW()),
('6501004','1234','นางสาวจันทร์เจ้า ขาวผ่อง','student','6501004@swu.ac.th','0890000024','active',NOW()),
('6501005','1234','นายดาวเหนือ นำทาง','student','6501005@swu.ac.th','0890000025','active',NOW()),
('6501006','1234','นางสาวลมหนาว พัดมา','student','6501006@swu.ac.th','0890000026','active',NOW()),
('6501007','1234','นายทะเล กว้างไกล','student','6501007@swu.ac.th','0890000027','active',NOW()),
('6501008','1234','นางสาวภูเขา มั่นคง','student','6501008@swu.ac.th','0890000028','active',NOW()),
('6501009','1234','นายสายน้ำ ชื่นใจ','student','6501009@swu.ac.th','0890000029','active',NOW()),
('6501010','1234','นางสาวต้นไม้ เขียวขจี','student','6501010@swu.ac.th','0890000030','active',NOW()),

('6601001','1234','นายสมชาย รักดี','student','6601001@swu.ac.th','0890000031','active',NOW()),
('6601002','1234','นางสาวสมศรี มีสุข','student','6601002@swu.ac.th','0890000032','active',NOW()),
('6601003','1234','นายมานะ ขยันงาน','student','6601003@swu.ac.th','0890000033','active',NOW()),
('6601004','1234','นางสาวชูใจ ใจดี','student','6601004@swu.ac.th','0890000034','active',NOW()),
('6601005','1234','นายปิติ ยินดี','student','6601005@swu.ac.th','0890000035','active',NOW()),
('6601006','1234','นางสาววีระ กล้าหาญ','student','6601006@swu.ac.th','0890000036','active',NOW()),
('6601007','1234','นายดวงดี มีโชค','student','6601007@swu.ac.th','0890000037','active',NOW()),
('6601008','1234','นางสาวฟ้าใส สวยงาม','student','6601008@swu.ac.th','0890000038','active',NOW()),
('6601009','1234','นายเก่งกาจ สามารถ','student','6601009@swu.ac.th','0890000039','active',NOW()),
('6601010','1234','นางสาวตั้งใจ เรียนดี','student','6601010@swu.ac.th','0890000040','active',NOW());
-- =====================================
-- students_info 
-- =====================================
INSERT INTO students_info VALUES
('6301001','นาย','สำเร็จ','เสร็จสมบูรณ์','Mr.','Samret','Setsomboon','0890000001','6301001@swu.ac.th','Information Studies'),
('6301002','นางสาว','มุ่งมั่น','ตั้งใจ','Miss','Mungman','Tangjai','0890000002','6301002@swu.ac.th','Information Studies'),
('6301003','นาย','อดทน','พยายาม','Mr.','Odtone','Payayam','0890000003','6301003@swu.ac.th','Information Studies'),
('6301004','นางสาว','สู้ตาย','ถวายหัว','Miss','Sutai','Thawaihua','0890000004','6301004@swu.ac.th','Information Studies'),
('6301005','นาย','ก้าวหน้า','เดินไกล','Mr.','Kaona','Doenkai','0890000005','6301005@swu.ac.th','Information Studies'),
('6301006','นางสาว','สดชื่น','รื่นรมย์','Miss','Sodchuen','Ruenrom','0890000006','6301006@swu.ac.th','Information Studies'),
('6301007','นาย','ร่มเย็น','เป็นสุข','Mr.','Romyen','Pensuk','0890000007','6301007@swu.ac.th','Information Studies'),
('6301008','นางสาว','เบิกบาน','ใจใส','Miss','Boekban','Jaisai','0890000008','6301008@swu.ac.th','Information Studies'),
('6301009','นาย','ชำนาญ','การงาน','Mr.','Chamnarn','Karnngan','0890000009','6301009@swu.ac.th','Information Studies'),
('6301010','นางสาว','เก่งจริง','ยิ่งใหญ่','Miss','Kengjing','Yingyai','0890000010','6301010@swu.ac.th','Information Studies'),

('6401001','นาย','อัศวิน','ขี่ม้าขาว','Mr.','Asawin','Khimakao','0890000011','6401001@swu.ac.th','Information Studies'),
('6401002','นางสาว','แก้วตา','ขวัญใจ','Miss','Kaewta','Kwanjai','0890000012','6401002@swu.ac.th','Information Studies'),
('6401003','นาย','เพชรแท้','แข็งแกร่ง','Mr.','Phetthae','Khaengkraeng','0890000013','6401003@swu.ac.th','Information Studies'),
('6401004','นางสาว','พลอยใส','แวววาว','Miss','Ploysai','Waewwao','0890000014','6401004@swu.ac.th','Information Studies'),
('6401005','นาย','ทองดี','มีค่า','Mr.','Thongdee','Mikha','0890000015','6401005@swu.ac.th','Information Studies'),
('6401006','นางสาว','เงินยวง','ผ่องแผ้ว','Miss','Ngoenyuang','Phongphaeo','0890000016','6401006@swu.ac.th','Information Studies'),
('6401007','นาย','รวยรื่น','ชื่นมื่น','Mr.','Ruayruen','Chuenmuen','0890000017','6401007@swu.ac.th','Information Studies'),
('6401008','นางสาว','มั่งมี','ศรีสุข','Miss','Mangmee','Srisuk','0890000018','6401008@swu.ac.th','Information Studies'),
('6401009','นาย','ร่ำรวย','พูนผล','Mr.','Ramruay','Poonphon','0890000019','6401009@swu.ac.th','Information Studies'),
('6401010','นางสาว','โชคดี','ทวีคูณ','Miss','Chokdee','Thaweekoon','0890000020','6401010@swu.ac.th','Information Studies'),

('6501001','นาย','ก้องภพ','สดใส','Mr.','Kongphop','Sodsai','0890000021','6501001@swu.ac.th','Information Studies'),
('6501002','นางสาว','ทอฝัน','วันใหม่','Miss','Torfan','Wanmai','0890000022','6501002@swu.ac.th','Information Studies'),
('6501003','นาย','อาทิตย์','ส่องแสง','Mr.','Athit','Songsang','0890000023','6501003@swu.ac.th','Information Studies'),
('6501004','นางสาว','จันทร์เจ้า','ขาวผ่อง','Miss','Janjao','Khaophong','0890000024','6501004@swu.ac.th','Information Studies'),
('6501005','นาย','ดาวเหนือ','นำทาง','Mr.','Daonuea','Namthang','0890000025','6501005@swu.ac.th','Information Studies'),
('6501006','นางสาว','ลมหนาว','พัดมา','Miss','Lomnao','Phatmaa','0890000026','6501006@swu.ac.th','Information Studies'),
('6501007','นาย','ทะเล','กว้างไกล','Mr.','Thale','Kwangklai','0890000027','6501007@swu.ac.th','Information Studies'),
('6501008','นางสาว','ภูเขา','มั่นคง','Miss','Phukhao','Mankong','0890000028','6501008@swu.ac.th','Information Studies'),
('6501009','นาย','สายน้ำ','ชื่นใจ','Mr.','Sainam','Chuenjai','0890000029','6501009@swu.ac.th','Information Studies'),
('6501010','นางสาว','ต้นไม้','เขียวขจี','Miss','Tonmai','Khiaokhajee','0890000030','6501010@swu.ac.th','Information Studies'),

('6601001','นาย','สมชาย','รักดี','Mr.','Somchai','Rakdee','0890000031','6601001@swu.ac.th','Information Studies'),
('6601002','นางสาว','สมศรี','มีสุข','Miss','Somsri','Meesuk','0890000032','6601002@swu.ac.th','Information Studies'),
('6601003','นาย','มานะ','ขยันงาน','Mr.','Mana','Khayannan','0890000033','6601003@swu.ac.th','Information Studies'),
('6601004','นางสาว','ชูใจ','ใจดี','Miss','Choojai','Jaidee','0890000034','6601004@swu.ac.th','Information Studies'),
('6601005','นาย','ปิติ','ยินดี','Mr.','Piti','Yindee','0890000035','6601005@swu.ac.th','Information Studies'),
('6601006','นางสาว','วีระ','กล้าหาญ','Miss','Weera','Klaharn','0890000036','6601006@swu.ac.th','Information Studies'),
('6601007','นาย','ดวงดี','มีโชค','Mr.','Duangdee','Meechok','0890000037','6601007@swu.ac.th','Information Studies'),
('6601008','นางสาว','ฟ้าใส','สวยงาม','Miss','Fahsai','Suayngam','0890000038','6601008@swu.ac.th','Information Studies'),
('6601009','นาย','เก่งกาจ','สามารถ','Mr.','Kengkard','Samart','0890000039','6601009@swu.ac.th','Information Studies'),
('6601010','นางสาว','ตั้งใจ','เรียนดี','Miss','Tangjai','Riandee','0890000040','6601010@swu.ac.th','Information Studies');


