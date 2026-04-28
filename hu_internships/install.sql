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
  `student_type` ENUM('regular', 'special') DEFAULT 'regular',
  FOREIGN KEY (`student_id`) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB;

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

-- =====================================
-- INSERT: STAFF USERS + INFO
-- =====================================
INSERT INTO users VALUES
('67101010659','1234','เขมสิริ แก้วหานาม','staff','staff1@swu.ac.th','0811111111','active',NOW()),
('67101010667','1234','ธมลวรรณ เจิมมหานนท์','staff','staff2@swu.ac.th','0811111112','active',NOW()),
('67101010668','1234','ธัญรดี สุรกิจพิบูลย์','staff','staff3@swu.ac.th','0811111113','active',NOW()),
('67101010679','1234','พัชรภา เกิดวิชิต','staff','staff4@swu.ac.th','0811111114','active',NOW()),
('67101010680','1234','พัทนันท์ ทองหล่อ','staff','staff5@swu.ac.th','0811111115','active',NOW()),
('67101010685','1234','ภูชนะ วิรัญจะ','staff','staff6@swu.ac.th','0811111116','active',NOW());

INSERT INTO staff_info VALUES
('67101010659','Admin','Internship Office','0811111111','staff1@swu.ac.th'),
('67101010667','Officer','Internship Office','0811111112','staff2@swu.ac.th'),
('67101010668','Officer','Internship Office','0811111113','staff3@swu.ac.th'),
('67101010679','Coordinator','Internship Office','0811111114','staff4@swu.ac.th'),
('67101010680','Coordinator','Internship Office','0811111115','staff5@swu.ac.th'),
('67101010685','Admin','Internship Office','0811111116','staff6@swu.ac.th');

-- =====================================
-- INSERT: TEACHERS (ครบตามที่ให้)
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

INSERT INTO teachers_info (user_id,full_name,position,full_name_en,position_en,detail_en) VALUES
('t001','อาจารย์ ดร. ดิษฐ์ สุทธิวงศ์','ประธานกรรมการบริหารหลักสูตร','Lecturer Dit Suthiwong, Ph.D.','Program Chair','Email: dit@g.swu.ac.th'),
('t002','อาจารย์ ดร. ฐิติ อติชาติชยากร','เลขานุการหลักสูตร','Lecturer Thiti Atichartchayakorn, Ph.D.','Program Secretary','Email: thitik@g.swu.ac.th'),
('t003','ผู้ช่วยศาสตราจารย์ ดร. วิภากร วัฒนสินธุ์','กรรมการหลักสูตร','Asst. Prof. Vipakorn Vadhanasin, Ph.D.','Committee Member','Email: vipakorn@g.swu.ac.th'),
('t004','อาจารย์ ดร. โชคธำรงค์ จงจอหอ','กรรมการหลักสูตร','Lecturer Chokthamrong Chongchorhor, Ph.D.','Committee Member','Email: chokthamrong@g.swu.ac.th'),
('t005','อาจารย์โชติมา วัฒนะ','กรรมการหลักสูตร','Lecturer Chotima Watana','Committee Member','Email: chotimaw@g.swu.ac.th'),
('t006','ผู้ช่วยศาสตราจารย์ ดร. ดุษฎี สีวังคำ','อาจารย์ผู้สอน','Asst. Prof. Dussadee Seewungkum, Ph.D.','Lecturer','Email: dussadee@g.swu.ac.th'),
('t007','ผู้ช่วยศาสตราจารย์ ดร. ศศิพิมล ประพินพงศกร','อาจารย์ผู้สอน','Asst. Prof. Sasipimol Prapinpongsakorn, Ph.D.','Lecturer','Email: sasipimol@g.swu.ac.th'),
('t008','อาจารย์ ดร. ศุมรรษตรา แสนวา','อาจารย์ผู้สอน','Lecturer Sumattra Saenwa, Ph.D.','Lecturer','Email: sumattra@g.swu.ac.th');

-- =====================================
-- INSERT: STUDENTS USERS + INFO (ครบทั้งหมดที่ให้)
-- =====================================
INSERT INTO users VALUES
('6501001','1234','นายสำเร็จ เสร็จสมบูรณ์','student','6501001@swu.ac.th','0890000001','active',NOW()),
('6501002','1234','นางสาวมุ่งมั่น ตั้งใจ','student','6501002@swu.ac.th','0890000002','active',NOW()),
('6501003','1234','นายอดทน พยายาม','student','6501003@swu.ac.th','0890000003','active',NOW()),
('6501004','1234','นางสาวสู้ตาย ถวายหัว','student','6501004@swu.ac.th','0890000004','active',NOW()),
('6501005','1234','นายก้าวหน้า เดินไกล','student','6501005@swu.ac.th','0890000005','active',NOW()),

('6601001','1234','นายอัศวิน ขี่ม้าขาว','student','6601001@swu.ac.th','0890000011','active',NOW()),
('6601002','1234','นางสาวแก้วตา ขวัญใจ','student','6601002@swu.ac.th','0890000012','active',NOW()),
('6601003','1234','นายเพชรแท้ แข็งแกร่ง','student','6601003@swu.ac.th','0890000013','active',NOW()),
('6601004','1234','นางสาวพลอยใส แวววาว','student','6601004@swu.ac.th','0890000014','active',NOW()),
('6601005','1234','นายทองดี มีค่า','student','6601005@swu.ac.th','0890000015','active',NOW()),

('6801001','1234','นายสมชาย รักดี','student','6801001@swu.ac.th','0890000031','active',NOW()),
('6801002','1234','นางสาวสมศรี มีสุข','student','6801002@swu.ac.th','0890000032','active',NOW()),
('6801003','1234','นายมานะ ขยันงาน','student','6801003@swu.ac.th','0890000033','active',NOW()),
('6801004','1234','นางสาวชูใจ ใจดี','student','6801004@swu.ac.th','0890000034','active',NOW()),
('6801005','1234','นายปิติ ยินดี','student','6801005@swu.ac.th','0890000035','active',NOW());


-- 
-- students_info 
-- 
INSERT INTO students_info (student_id, prefix_th, first_name_th, last_name_th, prefix_en, first_name_en, last_name_en, phone, email, major, student_type) VALUES
('6501001','นาย','สำเร็จ','เสร็จสมบูรณ์','Mr.','Samret','Setsomboon','0890000001','6501001@swu.ac.th','Information Studies','regular'),
('6501002','นางสาว','มุ่งมั่น','ตั้งใจ','Miss','Mungman','Tangjai','0890000002','6501002@swu.ac.th','Information Studies','regular'),
('6501003','นาย','อดทน','พยายาม','Mr.','Odtone','Payayam','0890000003','6501003@swu.ac.th','Information Studies','regular'),
('6501004','นางสาว','สู้ตาย','ถวายหัว','Miss','Sutai','Thawaihua','0890000004','6501004@swu.ac.th','Information Studies','regular'),
('6501005','นาย','ก้าวหน้า','เดินไกล','Mr.','Kaona','Doenkai','0890000005','6501005@swu.ac.th','Information Studies','regular'),


('6601001','นาย','อัศวิน','ขี่ม้าขาว','Mr.','Asawin','Khimakao','0890000011','6601001@swu.ac.th','Information Studies','regular'),
('6601002','นางสาว','แก้วตา','ขวัญใจ','Miss','Kaewta','Kwanjai','0890000012','6601002@swu.ac.th','Information Studies','regular'),
('6601003','นาย','เพชรแท้','แข็งแกร่ง','Mr.','Phetthae','Khaengkraeng','0890000013','6601003@swu.ac.th','Information Studies','regular'),
('6601004','นางสาว','พลอยใส','แวววาว','Miss','Ploysai','Waewwao','0890000014','6601004@swu.ac.th','Information Studies','regular'),
('6601005','นาย','ทองดี','มีค่า','Mr.','Thongdee','Mikha','0890000015','6601005@swu.ac.th','Information Studies','regular'),

('6801001','นาย','สมชาย','รักดี','Mr.','Somchai','Rakdee','0890000031','6801001@swu.ac.th','Information Studies','regular'),
('6801002','นางสาว','สมศรี','มีสุข','Miss','Somsri','Meesuk','0890000032','6801002@swu.ac.th','Information Studies','regular'),
('6801003','นาย','มานะ','ขยันงาน','Mr.','Mana','Khayannan','0890000033','6801003@swu.ac.th','Information Studies','regular'),
('6801004','นางสาว','ชูใจ','ใจดี','Miss','Choojai','Jaidee','0890000034','6801004@swu.ac.th','Information Studies','regular'),
('6801005','นาย','ปิติ','ยินดี','Mr.','Piti','Yindee','0890000035','6801005@swu.ac.th','Information Studies','regular');



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
('ฝ่ายวิจัยคณะมนุษยศาสตร์ ขอแสดงความยินดีและร่วมเผยแพร่ผลงานวิจัยจากคณาจารย์หลักสูตร ศศ.ม. สารสนเทศศึกษา กลุ่มสาขาวิชาพัฒนาศักยภาพมนุษย์ ได้รับการตีพิมพ์ในฐานข้อมูลระดับนานาชาติ (SCOPUS)',
 '🌟 ฝ่ายวิจัยคณะมนุษยศาสตร์ ขอแสดงความยินดีและร่วมเผยแพร่ผลงานวิจัยระดับนานาชาติ 🌟
ผลงานวิจัยจากคณาจารย์หลักสูตร ศศ.ม. สารสนเทศศึกษา กลุ่มสาขาวิชาพัฒนาศักยภาพมนุษย์ ได้รับการตีพิมพ์ในฐานข้อมูลระดับนานาชาติ (SCOPUS)
📘 บทความเรื่อง: “Information Services of Bangkok Metropolitan Administration’s Discovery Learning Libraries: Roles and Potential in Driving the Sustainable Development Goals (SDGs)”
👥 คณะผู้วิจัย:
🔸 อ. ดร.ศุมรรษตรา แสนวา  
🔸 ผศ. ดร.ศศิพิมล ประพินพงศกร
💰 ได้รับทุนสนับสนุนการวิจัยจากงบประมาณเงินรายได้คณะมนุษยศาสตร์ ประจำปีงบประมาณ พ.ศ. 2567
📖 อ่านบทความฉบับเต็ม: 🔗 https://so08.tci-thaijo.org/index.php/artssu/article/view/5283

#คณะมนุษยศาสตร์  #HUSWU  #SCOPUS  #Research #HUSWUResearch #SWUIS #SDGs'),

('23 มีนาคม 2569 หลักสูตร ศศ.บ.สารสนเทศศึกษา จัดโครงการปรับปรุงหลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา พ.ศ. 2570 ครั้งที่ 2',
 '🤍หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ ได้จัดโครงการปรับปรุงหลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา พ.ศ. 2570 ครั้งที่ 2 ในวันที่ 23 มีนาคม 2569  โครงการดังกล่าวมีวัตถุประสงค์เพื่อทบทวนและปรับปรุงหลักสูตรให้แล้วเสร็จและพร้อมใช้ภายในปีการศึกษา 2570 เพื่อให้หลักสูตรมีความทันสมัยที่จะผลิตบัณฑิตให้สอดคล้องกับวิสัยทัศน์ของมหาวิทยาลัยตามความต้องการของตลาดแรงงานและสังคม ดังนั้นในการจัดโครงการฯ ครั้งที่ 2 จึงได้มีการประชุมกับคณาจารย์ในหลักสูตรเพื่อวางแผนการทบทวนและปรับปรุงหลักสูตรฯ  ทั้งนี้ มีกำหนดจัดโครงการในครั้งถัดไปเป็นวันที่ 20 และ 27 เมษายน 2569 🤍

 

#hmswu'),

('โครงการ “พัฒนาระบบสารสนเทศเพื่อการบริหารจัดการองค์กร”',
 'วันจันทร์ที่ 16 มีนาคม 2569
คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ จัดโครงการ “พัฒนาระบบสารสนเทศเพื่อการบริหารจัดการองค์กร” ณ ห้อง 38-0301 ชั้น 3 อาคาร 38 คณะมนุษยศาสตร์ โดยได้รับเกียรติจาก ผู้ช่วยศาสตราจารย์ ดร.อัญชลี จันทร์เสม คณบดีคณะมนุษยศาสตร์ กล่าวเปิดโครงการ
ภายในกิจกรรมมีการอบรมเชิงปฏิบัติการเกี่ยวกับ Automation, Workflow และ Cyber Security เพื่อเสริมทักษะการใช้เทคโนโลยีในการบริหารจัดการงานและการใช้ระบบสารสนเทศอย่างปลอดภัย
คณะมนุษยศาสตร์ขอขอบพระคุณวิทยากรจากสาขาวิชาสารสนเทศศึกษา ได้แก่
🖥️ อาจารย์ ดร.ดิษฐ์ สุทธิวงศ์
🖥️ ผู้ช่วยศาสตราจารย์ ดร.วิภากร วัฒนสินธุ์
🖥️ ผู้ช่วยศาสตราจารย์ ดร.ดุษฎี สีวังคำ
🖥️ อาจารย์ ดร.โชคธำรงค์ จงจอหอ
🖥️ อาจารย์ ดร.ฐิติ อติชาติชยากร
ที่ได้ร่วมถ่ายทอดความรู้และประสบการณ์แก่บุคลากรของคณะ เพื่อสนับสนุนการพัฒนาองค์กรสู่ Digital Organization
#HumanitiesSWU
#DigitalTransformation
#Automation
#CyberSecurity');

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

ALTER TABLE students_info 
ADD COLUMN profile_photo VARCHAR(255) NULL DEFAULT NULL;

-- =====================================
-- System Update (รันเฉพาะคำสั่งนี้หากเคยรัน install.sql ด้านบนไปแล้ว)
-- =====================================
ALTER TABLE internship_requests 
ADD COLUMN certificate_file VARCHAR(255) DEFAULT NULL;

-- =====================================
--เพิ่ม culumnเก็บรูปข่าว
-- =====================================
ALTER TABLE news_activities 
ADD COLUMN news_image VARCHAR(255) DEFAULT NULL;
---เพิ่มรูปข่าว---
UPDATE news_activities 
SET news_image = 'news_1777293156_69ef5764ed872.jpeg'
WHERE id = 4;

UPDATE news_activities 
SET news_image = 'news_1777293316_69ef5804a1b7f.jpeg'
WHERE id = 5;

UPDATE news_activities 
SET news_image = 'news_1777293397_69ef5855cf710.jpeg'
WHERE id = 6;

-- =====================================
-- (Removed redundant ALTER TABLE for student_type)
-- =====================================

INSERT INTO users (username, password, fullname, role, email, phone, status, created_at)
VALUES
('6701001','1234','ณรงค์ศักดิ์ ขยันดี','student','6701001@swu.ac.th','0890000041','active',NOW()),
('6701002','1234','กนกวรรณ ตั้งใจเรียน','student','6701002@swu.ac.th','0890000042','active',NOW()),
('6701003','1234','ศุภชัย มุ่งมั่น','student','6701003@swu.ac.th','0890000043','active',NOW()),
('6701004','1234','พัชรินทร์ เรียบร้อย','student','6701004@swu.ac.th','0890000044','active',NOW()),
('6701005','1234','ธีรเดช อดทน','student','6701005@swu.ac.th','0890000045','active',NOW()),

('6701006','1234','ภาคภูมิ ใจดี','student','6701006@swu.ac.th','0890000026','active',NOW()),
('6701007','1234','นภัส สดใส','student','6701007@swu.ac.th','0890000027','active',NOW()),
('6701008','1234','กิตติ เก่งงาน','student','6701008@swu.ac.th','0890000028','active',NOW()),
('6701009','1234','วริศา ขยัน','student','6701009@swu.ac.th','0890000029','active',NOW()),
('6701010','1234','ธีรภัทร มุ่งมั่น','student','6701010@swu.ac.th','0890000030','active',NOW()),

('6801006','1234','ธันวา ตั้งใจ','student','6801006@swu.ac.th','0890000036','active',NOW()),
('6801007','1234','พิมพ์ชนก เรียบร้อย','student','6801007@swu.ac.th','0890000037','active',NOW()),
('6801008','1234','จักริน อดทน','student','6801008@swu.ac.th','0890000038','active',NOW()),
('6801009','1234','ณัฐธิดา ร่าเริง','student','6801009@swu.ac.th','0890000039','active',NOW()),
('6801010','1234','ปวริศ พัฒนา','student','6801010@swu.ac.th','0890000040','active',NOW());


INSERT INTO students_info 
(student_id, prefix_th, first_name_th, last_name_th, prefix_en, first_name_en, last_name_en, phone, email, major, student_type) 
VALUES

('6701001','นาย','ณรงค์ศักดิ์','ขยันดี','Mr.','Narongsak','Khayandee','0890000041','6701001@swu.ac.th','Information Studies','regular'),
('6701002','นางสาว','กนกวรรณ','ตั้งใจเรียน','Miss','Kanokwan','Tangjairian','0890000042','6701002@swu.ac.th','Information Studies','regular'),
('6701003','นาย','ศุภชัย','มุ่งมั่น','Mr.','Supachai','Mungman','0890000043','6701003@swu.ac.th','Information Studies','regular'),
('6701004','นางสาว','พัชรินทร์','เรียบร้อย','Miss','Patcharin','Riabroi','0890000044','6701004@swu.ac.th','Information Studies','regular'),
('6701005','นาย','ธีรเดช','อดทน','Mr.','Theeradech','Odtone','0890000045','6701005@swu.ac.th','Information Studies','regular'),

-- ===== 67 (special 5 คน ) =====
('6701006','นาย','ภาคภูมิ','ใจดี','Mr.','Phakphum','Jaidee','0890000026','6701006@swu.ac.th','Information Studies','special'),
('6701007','นางสาว','นภัส','สดใส','Miss','Naphat','Sodsai','0890000027','6701007@swu.ac.th','Information Studies','special'),
('6701008','นาย','กิตติ','เก่งงาน','Mr.','Kitti','Kengngan','0890000028','6701008@swu.ac.th','Information Studies','special'),
('6701009','นางสาว','วริศา','ขยัน','Miss','Warisa','Khayan','0890000029','6701009@swu.ac.th','Information Studies','special'),
('6701010','นาย','ธีรภัทร','มุ่งมั่น','Mr.','Theerapat','Mungman','0890000030','6701010@swu.ac.th','Information Studies','special'),

-- ===== 68 (special 5 คน ) =====
('6801006','นาย','ธันวา','ตั้งใจ','Mr.','Thanwa','Tangjai','0890000036','6801006@swu.ac.th','Information Studies','special'),
('6801007','นางสาว','พิมพ์ชนก','เรียบร้อย','Miss','Pimchanok','Riabroi','0890000037','6801007@swu.ac.th','Information Studies','special'),
('6801008','นาย','จักริน','อดทน','Mr.','Jakarin','Odtone','0890000038','6801008@swu.ac.th','Information Studies','special'),
('6801009','นางสาว','ณัฐธิดา','ร่าเริง','Miss','Nattida','Raroeng','0890000039','6801009@swu.ac.th','Information Studies','special'),
('6801010','นาย','ปวริศ','พัฒนา','Mr.','Pawarit','Phatthana','0890000040','6801010@swu.ac.th','Information Studies','special');