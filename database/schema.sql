-- EduManage Database Schema
-- MySQL 8.0+ / MariaDB compatible

CREATE DATABASE IF NOT EXISTS `edumanage_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `edumanage_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Roles Table
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `display_name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `is_system` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Permissions Table
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(100) NOT NULL UNIQUE,
    `module` VARCHAR(50) NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Role Permissions Table
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
    `role_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Users Table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(100) NULL,
    `phone` VARCHAR(20) NULL,
    `avatar` VARCHAR(255) DEFAULT '/uploads/avatars/default.png',
    `role_id` INT NOT NULL,
    `status` ENUM('active', 'inactive', 'locked') DEFAULT 'active',
    `last_login_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`),
    INDEX (`username`),
    INDEX (`status`),
    INDEX (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Academic Years Table
DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE `academic_years` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(50) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_current` TINYINT(1) DEFAULT 0,
    `is_locked` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Semesters Table
DROP TABLE IF EXISTS `semesters`;
CREATE TABLE `semesters` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `academic_year_id` INT NOT NULL,
    `code` VARCHAR(20) NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_current` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Grade Levels (Khối) Table
DROP TABLE IF EXISTS `grade_levels`;
CREATE TABLE `grade_levels` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(50) NOT NULL,
    `education_level` ENUM('primary', 'secondary', 'high_school') DEFAULT 'high_school',
    `order_index` INT DEFAULT 1,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Teachers Table
DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL UNIQUE,
    `teacher_code` VARCHAR(30) NOT NULL UNIQUE,
    `full_name` VARCHAR(150) NOT NULL,
    `gender` ENUM('male', 'female', 'other') DEFAULT 'male',
    `dob` DATE NULL,
    `phone` VARCHAR(20) NULL,
    `email` VARCHAR(100) NULL,
    `address` VARCHAR(255) NULL,
    `qualification` VARCHAR(100) DEFAULT 'Cử nhân Sư phạm',
    `specialization` VARCHAR(100) NULL,
    `start_date` DATE NULL,
    `avatar` VARCHAR(255) NULL,
    `status` ENUM('active', 'inactive', 'retired') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX (`teacher_code`),
    INDEX (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Classes Table
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(30) NOT NULL UNIQUE,
    `name` VARCHAR(50) NOT NULL,
    `grade_id` INT NOT NULL,
    `academic_year_id` INT NOT NULL,
    `homeroom_teacher_id` INT NULL,
    `room_number` VARCHAR(50) NULL,
    `max_students` INT DEFAULT 45,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`grade_id`) REFERENCES `grade_levels`(`id`),
    FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years`(`id`),
    FOREIGN KEY (`homeroom_teacher_id`) REFERENCES `teachers`(`id`) ON DELETE SET NULL,
    INDEX (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Students Table
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL UNIQUE,
    `student_code` VARCHAR(30) NOT NULL UNIQUE,
    `full_name` VARCHAR(150) NOT NULL,
    `khmer_name` VARCHAR(150) NULL,
    `gender` ENUM('male', 'female', 'other') DEFAULT 'male',
    `dob` DATE NOT NULL,
    `pob` VARCHAR(100) NULL,
    `address` VARCHAR(255) NULL,
    `phone` VARCHAR(20) NULL,
    `email` VARCHAR(100) NULL,
    `avatar` VARCHAR(255) NULL,
    `class_id` INT NULL,
    `grade_id` INT NULL,
    `academic_year_id` INT NULL,
    `admission_date` DATE NULL,
    `status` ENUM('studying', 'transferred', 'graduated', 'dropped') DEFAULT 'studying',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`grade_id`) REFERENCES `grade_levels`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years`(`id`) ON DELETE SET NULL,
    INDEX (`student_code`),
    INDEX (`full_name`),
    INDEX (`class_id`),
    INDEX (`status`),
    INDEX (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Subjects Table
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(30) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `grade_id` INT NULL,
    `periods_per_week` INT DEFAULT 2,
    `coefficient` DECIMAL(3,1) DEFAULT 1.0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`grade_id`) REFERENCES `grade_levels`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Teacher Subjects (Assignment of teacher to subject and class)
DROP TABLE IF EXISTS `teacher_subjects`;
CREATE TABLE `teacher_subjects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `teacher_id` INT NOT NULL,
    `subject_id` INT NOT NULL,
    `class_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_teacher_sub_class` (`teacher_id`, `subject_id`, `class_id`),
    FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Grade Components (Thường xuyên, Giữa kỳ, Cuối kỳ, etc.)
DROP TABLE IF EXISTS `grade_components`;
CREATE TABLE `grade_components` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(30) NOT NULL UNIQUE,
    `name` VARCHAR(50) NOT NULL,
    `weight` DECIMAL(3,2) DEFAULT 1.00,
    `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Grade Records (Điểm số)
DROP TABLE IF EXISTS `grade_records`;
CREATE TABLE `grade_records` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `subject_id` INT NOT NULL,
    `class_id` INT NOT NULL,
    `semester_id` INT NOT NULL,
    `component_id` INT NOT NULL,
    `score` DECIMAL(4,2) NOT NULL,
    `is_draft` TINYINT(1) DEFAULT 0,
    `is_locked` TINYINT(1) DEFAULT 0,
    `locked_by` INT NULL,
    `locked_at` DATETIME NULL,
    `notes` TEXT NULL,
    `updated_by` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_student_grade_entry` (`student_id`, `subject_id`, `semester_id`, `component_id`),
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`semester_id`) REFERENCES `semesters`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`component_id`) REFERENCES `grade_components`(`id`),
    INDEX (`student_id`),
    INDEX (`subject_id`),
    INDEX (`class_id`),
    INDEX (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Student Comments (Nhận xét)
DROP TABLE IF EXISTS `student_comments`;
CREATE TABLE `student_comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `teacher_id` INT NOT NULL,
    `subject_id` INT NULL,
    `semester_id` INT NOT NULL,
    `category` ENUM('learning', 'behavior', 'training', 'midterm', 'final') DEFAULT 'learning',
    `comment_text` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`semester_id`) REFERENCES `semesters`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Comment Templates (Thư viện mẫu nhận xét)
DROP TABLE IF EXISTS `comment_templates`;
CREATE TABLE `comment_templates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(50) NOT NULL,
    `template_text` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Attendance (Điểm danh)
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `class_id` INT NOT NULL,
    `subject_id` INT NULL,
    `date` DATE NOT NULL,
    `period` INT DEFAULT 1,
    `status` ENUM('present', 'absent_excused', 'absent_unexcused', 'late') DEFAULT 'present',
    `reason` VARCHAR(255) NULL,
    `marked_by` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_attendance_entry` (`student_id`, `class_id`, `date`, `period`),
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE SET NULL,
    INDEX (`student_id`),
    INDEX (`class_id`),
    INDEX (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. Timetable / Schedules (Thời khóa biểu)
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `academic_year_id` INT NOT NULL,
    `semester_id` INT NOT NULL,
    `class_id` INT NOT NULL,
    `subject_id` INT NOT NULL,
    `teacher_id` INT NOT NULL,
    `day_of_week` TINYINT NOT NULL COMMENT '2: Thứ 2, 3: Thứ 3, ..., 7: Thứ 7',
    `period_start` TINYINT NOT NULL,
    `period_end` TINYINT NOT NULL,
    `room` VARCHAR(50) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`semester_id`) REFERENCES `semesters`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
    INDEX (`class_id`),
    INDEX (`teacher_id`),
    INDEX (`day_of_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Exams (Kỳ thi)
DROP TABLE IF EXISTS `exams`;
CREATE TABLE `exams` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `academic_year_id` INT NOT NULL,
    `semester_id` INT NOT NULL,
    `grade_id` INT NOT NULL,
    `subject_id` INT NOT NULL,
    `exam_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `duration_minutes` INT DEFAULT 60,
    `room` VARCHAR(50) NULL,
    `max_score` DECIMAL(4,2) DEFAULT 10.0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`semester_id`) REFERENCES `semesters`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`grade_id`) REFERENCES `grade_levels`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. Exam Results (Kết quả thi)
DROP TABLE IF EXISTS `exam_results`;
CREATE TABLE `exam_results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT NOT NULL,
    `student_id` INT NOT NULL,
    `candidate_number` VARCHAR(30) NULL COMMENT 'Số báo danh SBD',
    `score` DECIMAL(4,2) NULL,
    `notes` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_exam_student` (`exam_id`, `student_id`),
    FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. Assignments (Bài tập)
DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `teacher_id` INT NOT NULL,
    `subject_id` INT NOT NULL,
    `class_id` INT NOT NULL,
    `due_date` DATETIME NOT NULL,
    `max_score` DECIMAL(4,2) DEFAULT 10.0,
    `attachment_url` VARCHAR(255) NULL,
    `status` ENUM('published', 'draft', 'closed') DEFAULT 'published',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. Assignment Submissions (Nộp bài tập)
DROP TABLE IF EXISTS `assignment_submissions`;
CREATE TABLE `assignment_submissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `assignment_id` INT NOT NULL,
    `student_id` INT NOT NULL,
    `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `content` TEXT NULL,
    `attachment_url` VARCHAR(255) NULL,
    `score` DECIMAL(4,2) NULL,
    `feedback` TEXT NULL,
    `graded_by` INT NULL,
    `graded_at` DATETIME NULL,
    `status` ENUM('submitted', 'graded', 'late', 'resubmit_required') DEFAULT 'submitted',
    UNIQUE KEY `unique_student_assignment` (`assignment_id`, `student_id`),
    FOREIGN KEY (`assignment_id`) REFERENCES `assignments`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`graded_by`) REFERENCES `teachers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. Documents Library (Tài liệu học tập)
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size` BIGINT DEFAULT 0,
    `uploaded_by` INT NOT NULL,
    `subject_id` INT NULL,
    `class_id` INT NULL,
    `grade_id` INT NULL,
    `access_level` ENUM('public', 'teachers_only', 'class_only') DEFAULT 'public',
    `download_count` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`grade_id`) REFERENCES `grade_levels`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 24. Conversations (Hội thoại tin nhắn)
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NULL,
    `type` ENUM('direct', 'group') DEFAULT 'direct',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 25. Conversation Members
DROP TABLE IF EXISTS `conversation_members`;
CREATE TABLE `conversation_members` (
    `conversation_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `joined_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_read_at` DATETIME NULL,
    PRIMARY KEY (`conversation_id`, `user_id`),
    FOREIGN KEY (`conversation_id`) REFERENCES `conversations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 26. Messages (Tin nhắn)
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `sender_id` INT NOT NULL,
    `content` TEXT NULL,
    `attachment_url` VARCHAR(255) NULL,
    `attachment_type` VARCHAR(50) NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `conversations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX (`conversation_id`),
    INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 27. Rewards & Achievements (Khen thưởng & Thành tích)
DROP TABLE IF EXISTS `rewards`;
CREATE TABLE `rewards` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `reward_type` ENUM('academic', 'competition', 'sports', 'arts', 'conduct') DEFAULT 'academic',
    `date_achieved` DATE NOT NULL,
    `recognized_by` VARCHAR(150) NOT NULL,
    `proof_file_url` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    INDEX (`student_id`),
    INDEX (`reward_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 28. Audit Logs (Nhật ký Hệ thống)
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(50) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `record_id` VARCHAR(50) NULL,
    `old_data` LONGTEXT NULL,
    `new_data` LONGTEXT NULL,
    `ip_address` VARCHAR(50) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX (`module`),
    INDEX (`action`),
    INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 29. System Settings (Cấu hình hệ thống)
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
    `key_name` VARCHAR(50) PRIMARY KEY,
    `value` TEXT NULL,
    `description` VARCHAR(255) NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
