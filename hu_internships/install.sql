-- =====================================
-- DATABASE: internship_system
-- =====================================
CREATE DATABASE IF NOT EXISTS `internship_system`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `internship_system`;

-- =====================================
-- TABLE: users
-- =====================================
CREATE TABLE IF NOT EXISTS `users` (
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
-- เก็บข้อมูลเพิ่มเติมของนิสิต แยกออกจาก users
-- student_type: 'regular' = ภาคปกติ, 'special' = ภาคพิเศษ
-- program_code: รหัสภาคที่นิสิตสังกัด (อาจใช้ในอนาคต)
-- =====================================
CREATE TABLE IF NOT EXISTS `students_info` (
  `student_id`    VARCHAR(20) PRIMARY KEY,          -- รหัสนิสิต (FK → users.username)
  `prefix_th`     VARCHAR(20),                      -- คำนำหน้าภาษาไทย เช่น นาย / นางสาว
  `first_name_th` VARCHAR(100),                     -- ชื่อภาษาไทย
  `last_name_th`  VARCHAR(100),                     -- นามสกุลภาษาไทย
  `prefix_en`     VARCHAR(20),                      -- คำนำหน้าภาษาอังกฤษ เช่น Mr. / Miss
  `first_name_en` VARCHAR(100),                     -- ชื่อภาษาอังกฤษ
  `last_name_en`  VARCHAR(100),                     -- นามสกุลภาษาอังกฤษ
  `phone`         VARCHAR(20),                      -- เบอร์โทรศัพท์
  `email`         VARCHAR(100),                     -- อีเมลนิสิต
  `major`         VARCHAR(200),                     -- ชื่อหลักสูตร/สาขาวิชาเต็ม
  `student_type`  ENUM('regular','special')
                  NOT NULL DEFAULT 'regular',        -- ประเภทนิสิต: regular=ภาคปกติ, special=ภาคพิเศษ
  `academic_year` VARCHAR(10) DEFAULT NULL,         -- ปีการศึกษาที่เข้า เช่น 2566
  FOREIGN KEY (`student_id`) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================
-- TABLE: teachers_info
-- =====================================
CREATE TABLE IF NOT EXISTS `teachers_info` (
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
CREATE TABLE IF NOT EXISTS `staff_info` (
  `staff_id` VARCHAR(20) PRIMARY KEY,
  `position` VARCHAR(100),
  `department` VARCHAR(100),
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  FOREIGN KEY (`staff_id`) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================
-- TABLE: internship_requests
-- สถานะ (status): 1=รอดำเนินการ, 2=อนุมัติ, 3=ปฏิเสธ
-- doc_status: สถานะเอกสาร 0=ยังไม่ส่ง, 1=ส่งแล้ว, 2=ตรวจแล้ว
-- =====================================
CREATE TABLE IF NOT EXISTS `internship_requests` (
  `request_id`      INT AUTO_INCREMENT PRIMARY KEY,
  `student_id`      VARCHAR(20) NOT NULL,            -- รหัสนิสิต (FK → users)

  -- ข้อมูลสถานประกอบการ
  `company_name`    VARCHAR(255) NOT NULL,            -- ชื่อสถานประกอบการ
  `company_type`    ENUM('private','government','ngo','other') DEFAULT 'private', -- ประเภทองค์กร
  `company_address` TEXT,                             -- ที่อยู่สถานประกอบการ
  `company_province` VARCHAR(100),                   -- จังหวัด
  `company_phone`   VARCHAR(30),                     -- เบอร์โทรสถานประกอบการ
  `company_website` VARCHAR(255),                    -- เว็บไซต์

  -- ข้อมูลผู้ติดต่อที่บริษัท
  `contact_person`  VARCHAR(100),                    -- ชื่อผู้ติดต่อ
  `contact_position` VARCHAR(100),                   -- ตำแหน่งผู้ติดต่อ
  `contact_phone`   VARCHAR(30),                     -- เบอร์ผู้ติดต่อ
  `contact_email`   VARCHAR(100),                    -- อีเมลผู้ติดต่อ

  -- ระยะเวลาฝึกงาน
  `internship_start` DATE,                           -- วันเริ่มฝึกงาน
  `internship_end`   DATE,                           -- วันสิ้นสุดฝึกงาน
  `work_days`        VARCHAR(100) DEFAULT 'จันทร์-ศุกร์', -- วันทำงาน
  `work_hours`       VARCHAR(50)  DEFAULT '09:00-18:00',  -- เวลาทำงาน
  `department`      VARCHAR(150),                    -- แผนกที่ฝึกงาน

  -- อาจารย์นิเทศ
  `advisor_id`      VARCHAR(20),                     -- FK → users.username (teacher)
  `advisor_note`    TEXT,                             -- หมายเหตุจากอาจารย์นิเทศ

  -- สถานะและเอกสาร
  `status`          INT DEFAULT 1,                   -- 1=รอ, 2=อนุมัติ, 3=ปฏิเสธ
  `doc_status`      TINYINT DEFAULT 0,               -- 0=ยังไม่ส่ง, 1=ส่งแล้ว, 2=ตรวจแล้ว
  `remark`          TEXT,                            -- หมายเหตุจากเจ้าหน้าที่
  `request_date`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- วันที่ยื่นคำขอ
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  FOREIGN KEY (`student_id`) REFERENCES users(username) ON DELETE CASCADE,
  FOREIGN KEY (`advisor_id`) REFERENCES users(username) ON DELETE SET NULL
) ENGINE=InnoDB;

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