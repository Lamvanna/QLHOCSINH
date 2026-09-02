<?php
// config/constants.php

define('APP_NAME', 'EduManage – Student Management System');
define('APP_VERSION', '2.0.0');
define('BASE_URL', '/QLHOCSINH');

// Role constants
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_ADMIN', 'admin');
define('ROLE_TEACHER', 'teacher');
define('ROLE_STUDENT', 'student');

// Status constants
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_ARCHIVED', 'archived');
define('STATUS_LOCKED', 'locked');

// Attendance status
define('ATTENDANCE_PRESENT', 'present');
define('ATTENDANCE_ABSENT_EXCUSED', 'absent_excused');
define('ATTENDANCE_ABSENT_UNEXCUSED', 'absent_unexcused');
define('ATTENDANCE_LATE', 'late');

// Assignment status
define('ASSIGNMENT_NOT_STARTED', 'not_started');
define('ASSIGNMENT_IN_PROGRESS', 'in_progress');
define('ASSIGNMENT_SUBMITTED', 'submitted');
define('ASSIGNMENT_OVERDUE', 'overdue');
define('ASSIGNMENT_GRADED', 'graded');
