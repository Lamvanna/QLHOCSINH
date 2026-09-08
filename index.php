<?php
// index.php - Front Controller

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Permission.php';
require_once __DIR__ . '/core/AuditLogger.php';
require_once __DIR__ . '/core/View.php';

// Include All Active Controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/StudentController.php';
require_once __DIR__ . '/controllers/TeacherController.php';
require_once __DIR__ . '/controllers/ClassController.php';
require_once __DIR__ . '/controllers/GradeController.php';
require_once __DIR__ . '/controllers/ScheduleController.php';
require_once __DIR__ . '/controllers/AssignmentController.php';
require_once __DIR__ . '/controllers/PromotionController.php';
require_once __DIR__ . '/controllers/RemainingControllers.php';

Auth::init();

// --- 1. AUTHENTICATION & PROFILE ---
Router::get('/', function() {
    Response::redirect('/dashboard');
});
Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);
Router::post('/logout', [AuthController::class, 'logout']);
Router::get('/switch-role', [AuthController::class, 'switchRole']);
Router::get('/profile', [AuthController::class, 'showProfile']);
Router::post('/profile', [AuthController::class, 'updateProfile']);

// --- 2. DASHBOARD ---
Router::get('/dashboard', [DashboardController::class, 'index']);

// --- 3. STUDENTS (HỌC SINH) ---
Router::get('/students', [StudentController::class, 'index']);
Router::get('/students/create', [StudentController::class, 'create']);
Router::get('/students/print', [StudentController::class, 'printClass']);
Router::post('/students', [StudentController::class, 'store']);
Router::get('/students/{id}', [StudentController::class, 'show']);
Router::get('/students/{id}/edit', [StudentController::class, 'edit']);
Router::post('/students/{id}', [StudentController::class, 'update']);
Router::delete('/students/{id}', [StudentController::class, 'destroy']);
Router::post('/students/{id}/restore', [StudentController::class, 'restore']);
Router::post('/students/import', [StudentController::class, 'import']);

// --- 4. TEACHERS (GIÁO VIÊN) ---
Router::get('/teachers', [TeacherController::class, 'index']);
Router::get('/teachers/print', [TeacherController::class, 'printList']);
Router::get('/teachers/create', [TeacherController::class, 'create']);
Router::post('/teachers', [TeacherController::class, 'store']);
Router::get('/teachers/{id}/edit', [TeacherController::class, 'edit']);
Router::post('/teachers/{id}', [TeacherController::class, 'update']);
Router::delete('/teachers/{id}', [TeacherController::class, 'destroy']);
Router::post('/teachers/import', [TeacherController::class, 'import']);

// --- 5. CLASSES & GRADE LEVELS (LỚP & KHỐI) ---
Router::get('/classes', [ClassController::class, 'index']);
Router::get('/classes/print', [ClassController::class, 'printList']);
Router::post('/classes', [ClassController::class, 'store']);
Router::get('/classes/{id}', [ClassController::class, 'show']);
Router::post('/classes/{id}', [ClassController::class, 'update']);
Router::delete('/classes/{id}', [ClassController::class, 'destroy']);
Router::post('/classes/transfer-student', [ClassController::class, 'transferStudent']);
Router::get('/grade-levels', [GradeLevelController::class, 'index']);
Router::post('/grade-levels', [GradeLevelController::class, 'store']);
Router::post('/grade-levels/{id}', [GradeLevelController::class, 'update']);
Router::delete('/grade-levels/{id}', [GradeLevelController::class, 'destroy']);

// --- 6. SUBJECTS, YEARS & SEMESTERS (MÔN HỌC, NĂM HỌC, HỌC KỲ) ---
Router::get('/subjects', [SubjectController::class, 'index']);
Router::get('/subjects/print', [SubjectController::class, 'printList']);
Router::post('/subjects', [SubjectController::class, 'store']);
Router::post('/subjects/{id}', [SubjectController::class, 'update']);
Router::delete('/subjects/{id}', [SubjectController::class, 'destroy']);
Router::get('/academic-years', [AcademicYearController::class, 'index']);
Router::post('/academic-years', [AcademicYearController::class, 'store']);
Router::post('/academic-years/{id}', [AcademicYearController::class, 'update']);
Router::delete('/academic-years/{id}', [AcademicYearController::class, 'destroy']);
Router::get('/semesters', [SemesterController::class, 'index']);
Router::post('/semesters', [SemesterController::class, 'store']);
Router::post('/semesters/{id}', [SemesterController::class, 'update']);
Router::delete('/semesters/{id}', [SemesterController::class, 'destroy']);

// --- 7. GRADES & PROMOTIONS (ĐIỂM SỐ & XÉT LÊN LỚP) ---
Router::get('/grades', [GradeController::class, 'index']);
Router::get('/grades/print', [GradeController::class, 'printSheet']);
Router::post('/grades/save', [GradeController::class, 'save']);
Router::post('/grades/lock', [GradeController::class, 'lock']);
Router::post('/grades/unlock', [GradeController::class, 'unlock']);
Router::get('/promotions/print', [PromotionController::class, 'printSheet']);
Router::get('/promotions', [PromotionController::class, 'index']);
Router::post('/promotions/save', [PromotionController::class, 'save']);
Router::post('/promotions/execute', [PromotionController::class, 'execute']);

// --- 8. ASSIGNMENTS & SCHEDULES (BÀI TẬP & THỜI KHÓA BIỂU) ---
Router::get('/assignments', [AssignmentController::class, 'index']);
Router::post('/assignments', [AssignmentController::class, 'store']);
Router::get('/assignments/{id}', [AssignmentController::class, 'show']);
Router::post('/assignments/{id}/submit', [AssignmentController::class, 'submit']);
Router::post('/assignments/grade', [AssignmentController::class, 'grade']);
Router::get('/schedules/print', [ScheduleController::class, 'printSheet']);
Router::get('/schedules', [ScheduleController::class, 'index']);
Router::post('/schedules', [ScheduleController::class, 'store']);
Router::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);

// --- 9. REPORTS (BÁO CÁO & THỐNG KÊ) ---
Router::get('/reports', [ReportController::class, 'index']);

// --- 10. USERS, ROLES, AUDIT LOGS, SETTINGS & BACKUPS ---
Router::get('/users', [UserController::class, 'index']);
Router::post('/users', [UserController::class, 'store']);
Router::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
Router::get('/roles', [RoleController::class, 'index']);
Router::get('/audit-logs', [AuditLogController::class, 'index']);
Router::get('/settings', [SettingController::class, 'index']);
Router::post('/settings', [SettingController::class, 'update']);
Router::get('/backups', [BackupController::class, 'index']);
Router::post('/backups', [BackupController::class, 'create']);
Router::post('/backups/restore', [BackupController::class, 'restore']);

// Dispatch the current request
Router::dispatch();
