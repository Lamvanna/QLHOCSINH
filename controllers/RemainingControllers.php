<?php
// controllers/RemainingControllers.php
require_once __DIR__ . '/BaseController.php';

// 1. REPORT CONTROLLER
class ReportController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('reports.view');

        $pdo = Database::getConnection();
        $reportType = $request->input('type', 'students');

        $data = [];
        if ($reportType === 'students') {
            $data = $pdo->query("SELECT c.name as class_name, c.room_number,
                                        COUNT(s.id) as total_students,
                                        SUM(CASE WHEN s.gender = 'male' THEN 1 ELSE 0 END) as male_students,
                                        SUM(CASE WHEN s.gender = 'female' THEN 1 ELSE 0 END) as female_students
                                 FROM classes c
                                 LEFT JOIN students s ON s.class_id = c.id AND s.deleted_at IS NULL
                                 GROUP BY c.id, c.name, c.room_number
                                 ORDER BY c.id ASC")->fetchAll();
        } elseif ($reportType === 'grades') {
            $data = $pdo->query("SELECT sub.name as subject_name, c.name as class_name,
                                        ROUND(AVG(gr.score), 2) as avg_score,
                                        SUM(CASE WHEN gr.score >= 8.0 THEN 1 ELSE 0 END) as excellent_count,
                                        SUM(CASE WHEN gr.score >= 6.5 AND gr.score < 8.0 THEN 1 ELSE 0 END) as good_count,
                                        SUM(CASE WHEN gr.score >= 5.0 AND gr.score < 6.5 THEN 1 ELSE 0 END) as pass_count,
                                        SUM(CASE WHEN gr.score < 5.0 THEN 1 ELSE 0 END) as fail_count
                                 FROM grade_records gr
                                 JOIN subjects sub ON gr.subject_id = sub.id
                                 JOIN classes c ON gr.class_id = c.id
                                 WHERE gr.is_draft = 0
                                 GROUP BY sub.id, sub.name, c.id, c.name
                                 ORDER BY c.name, sub.name")->fetchAll();
        }

        View::render('reports/index', [
            'reportType' => $reportType,
            'reportData' => $data
        ]);
    }
}

// 2. USER CONTROLLER
class UserController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('users.view');

        $pdo = Database::getConnection();
        $users = $pdo->query("SELECT u.*, r.display_name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.deleted_at IS NULL ORDER BY u.id ASC LIMIT 100")->fetchAll();
        $roles = Role::all();

        if ($request->isAjax()) {
            Response::json($users);
            return;
        }

        View::render('users/index', ['users' => $users, 'roles' => $roles]);
    }

    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('users.create');

        $data = $request->all();
        $username = trim($data['username']);
        $pdo = Database::getConnection();

        $chk = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $chk->execute([$username]);
        if ($chk->fetch()) {
            Response::error("Tên đăng nhập '{$username}' đã tồn tại.");
            return;
        }

        $id = User::create([
            'username'      => $username,
            'password_hash' => password_hash($data['password'] ?? '123456', PASSWORD_DEFAULT),
            'full_name'     => $data['full_name'],
            'email'         => $data['email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'role_id'       => $data['role_id'],
            'status'        => 'active'
        ]);

        AuditLogger::log('CREATE_USER', 'users', (string)$id, null, $data);
        Response::success(['id' => $id], 'Tạo tài khoản người dùng thành công!');
    }

    public function toggleStatus(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('users.lock');

        $id   = (int)($params['id'] ?? 0);
        $user = User::find($id);
        if (!$user) {
            Response::error('Người dùng không tồn tại.');
            return;
        }

        $newStatus = $user['status'] === 'active' ? 'locked' : 'active';
        User::update($id, ['status' => $newStatus]);
        AuditLogger::log('TOGGLE_USER_STATUS', 'users', (string)$id, ['status' => $user['status']], ['status' => $newStatus]);

        Response::success(null, "Đã " . ($newStatus === 'locked' ? 'khóa' : 'mở khóa') . " tài khoản thành công.");
    }
}

// 3. ROLE CONTROLLER
class RoleController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('roles.view');

        $roles = Role::all();
        $pdo   = Database::getConnection();
        $permissions    = $pdo->query("SELECT * FROM permissions ORDER BY module ASC, action ASC")->fetchAll();
        $rolePermissions = $pdo->query("SELECT * FROM role_permissions")->fetchAll();

        View::render('roles/index', [
            'roles'           => $roles,
            'permissions'     => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }
}

// 4. AUDIT LOG CONTROLLER
class AuditLogController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('audit_logs.view');

        $logs = AuditLog::getListWithUsers(150);
        View::render('audit_logs/index', ['logs' => $logs]);
    }
}

// 5. SETTING CONTROLLER
class SettingController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('settings.view');

        $settings = SystemSetting::all();
        View::render('settings/index', ['settings' => $settings]);
    }

    public function update(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('settings.update');

        $data = $request->all();
        foreach ($data as $key => $val) {
            SystemSetting::set($key, (string)$val);
        }

        AuditLogger::log('UPDATE_SETTINGS', 'settings', null, null, $data);
        Response::success(null, 'Cập nhật cấu hình hệ thống thành công!');
    }
}

// 6. BACKUP CONTROLLER
class BackupController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('backups.view');

        $backupDir = __DIR__ . '/../public/uploads/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $files   = glob($backupDir . '/*.sql');
        $backups = [];
        foreach ($files as $f) {
            $backups[] = [
                'filename'   => basename($f),
                'size'       => round(filesize($f) / 1024, 2) . ' KB',
                'created_at' => date('Y-m-d H:i:s', filemtime($f))
            ];
        }

        View::render('backups/index', ['backups' => $backups]);
    }

    public function create(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('backups.create');

        $backupDir = __DIR__ . '/../public/uploads/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $filename = 'edumanage_backup_' . date('Y_m_d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $pdo    = Database::getConnection();
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        $sqlDump = "-- EduManage Database Backup\n-- Generated: " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
            $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n" . $createTable['Create Table'] . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll();
            foreach ($rows as $row) {
                $values = array_map(fn($v) => is_null($v) ? 'NULL' : $pdo->quote($v), array_values($row));
                $sqlDump .= "INSERT INTO `{$table}` VALUES (" . implode(", ", $values) . ");\n";
            }
            $sqlDump .= "\n";
        }
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($filepath, $sqlDump);
        AuditLogger::log('CREATE_BACKUP', 'backups', $filename);

        Response::success(['filename' => $filename], 'Tạo bản sao lưu dữ liệu thành công!');
    }

    public function restore(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('backups.restore');

        $filename = basename($request->input('filename', ''));
        $filepath = __DIR__ . '/../public/uploads/backups/' . $filename;

        if (!file_exists($filepath)) {
            Response::error('Tệp tin sao lưu không tồn tại.');
            return;
        }

        $sql = file_get_contents($filepath);
        $pdo = Database::getConnection();
        $pdo->exec($sql);

        AuditLogger::log('RESTORE_BACKUP', 'backups', $filename);
        Response::success(null, 'Phục hồi cơ sở dữ liệu thành công!');
    }
}

// 7. SUBJECT CONTROLLER - Full CRUD
class SubjectController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('subjects.view');
        
        $search = trim($request->input('search', ''));
        $status = trim($request->input('status', ''));

        $filters = [
            'search' => $search,
            'status' => $status
        ];

        $allSubjects = Subject::all('id ASC');
        $subjects = $allSubjects;

        if (!empty($search)) {
            $sLower = mb_strtolower($search, 'UTF-8');
            $subjects = array_filter($subjects, function($s) use ($sLower) {
                return str_contains(mb_strtolower($s['name'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($s['code'] ?? '', 'UTF-8'), $sLower);
            });
            $subjects = array_values($subjects);
        }

        if (!empty($status)) {
            $subjects = array_filter($subjects, fn($s) => ($s['status'] ?? '') === $status);
            $subjects = array_values($subjects);
        }

        View::render('subjects/index', [
            'subjects' => $subjects,
            'allSubjects' => $allSubjects,
            'filters' => $filters
        ]);
    }
    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('subjects.create');
        $data = $request->all();
        $pdo  = Database::getConnection();
        $chk  = $pdo->prepare("SELECT id FROM subjects WHERE code = ?");
        $chk->execute([strtoupper($data['code'])]);
        if ($chk->fetch()) { Response::error("Mã môn học '{$data['code']}' đã tồn tại."); return; }
        $id = Subject::create([
            'code'             => strtoupper(trim($data['code'])),
            'name'             => trim($data['name']),
            'periods_per_week' => (int)($data['periods_per_week'] ?? 2),
            'coefficient'      => (float)($data['coefficient'] ?? 1.0),
            'status'           => $data['status'] ?? 'active'
        ]);
        AuditLogger::log('CREATE_SUBJECT', 'subjects', (string)$id, null, $data);
        Response::success(['id' => $id], 'Tạo môn học thành công!');
    }
    public function update(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('subjects.update');
        $id      = (int)($params['id'] ?? 0);
        $subject = Subject::find($id);
        if (!$subject) { Response::error('Môn học không tồn tại.', 404); return; }
        $data = $request->all();
        Subject::update($id, [
            'name'             => trim($data['name']),
            'periods_per_week' => (int)($data['periods_per_week'] ?? $subject['periods_per_week']),
            'coefficient'      => (float)($data['coefficient'] ?? $subject['coefficient']),
            'status'           => $data['status'] ?? $subject['status']
        ]);
        AuditLogger::log('UPDATE_SUBJECT', 'subjects', (string)$id, $subject, $data);
        Response::success(null, 'Cập nhật môn học thành công!');
    }
        public function destroy(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('subjects.delete');
        $id = (int)($params['id'] ?? 0);
        Subject::delete($id);
        AuditLogger::log('DELETE_SUBJECT', 'subjects', (string)$id);
        Response::success(null, 'Đã xóa môn học thành công.');
    }

    public function printList(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('subjects.view');

        $status = trim($request->input('status', ''));
        $allSubjects = Subject::all('id ASC');
        $subjects = $allSubjects;

        if (!empty($status)) {
            $subjects = array_filter($subjects, fn($s) => ($s['status'] ?? '') === $status);
            $subjects = array_values($subjects);
        }

        View::render('subjects/print', [
            'subjects' => $subjects,
            'selectedStatus' => $status
        ], 'none');
    }
}

// 8. GRADE LEVEL CONTROLLER - Full CRUD
class GradeLevelController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades_level.view');
        $grades = GradeLevel::all('order_index ASC');
        View::render('grade_levels/index', ['grades' => $grades]);
    }
    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades_level.create');
        $data = $request->all();
        $pdo  = Database::getConnection();
        $chk  = $pdo->prepare("SELECT id FROM grade_levels WHERE name = ?");
        $chk->execute([trim($data['name'])]);
        if ($chk->fetch()) { Response::error("Khối '{$data['name']}' đã tồn tại."); return; }
        $id = GradeLevel::create([
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? null,
            'order_index' => (int)($data['order_index'] ?? 10)
        ]);
        AuditLogger::log('CREATE_GRADE_LEVEL', 'grade_levels', (string)$id, null, $data);
        Response::success(['id' => $id], 'Tạo khối học thành công!');
    }
    public function update(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('grades_level.update');
        $id    = (int)($params['id'] ?? 0);
        $grade = GradeLevel::find($id);
        if (!$grade) { Response::error('Khối học không tồn tại.', 404); return; }
        $data  = $request->all();
        GradeLevel::update($id, [
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? $grade['description'],
            'order_index' => (int)($data['order_index'] ?? $grade['order_index'])
        ]);
        AuditLogger::log('UPDATE_GRADE_LEVEL', 'grade_levels', (string)$id, $grade, $data);
        Response::success(null, 'Cập nhật khối học thành công!');
    }
    public function destroy(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('grades_level.delete');
        $id  = (int)($params['id'] ?? 0);
        $pdo = Database::getConnection();
        $hasClasses = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE grade_id = ?");
        $hasClasses->execute([$id]);
        if ($hasClasses->fetchColumn() > 0) {
            Response::error('Không thể xóa khối đang có lớp học. Hãy chuyển hoặc xóa các lớp trước.');
            return;
        }
        GradeLevel::delete($id);
        AuditLogger::log('DELETE_GRADE_LEVEL', 'grade_levels', (string)$id);
        Response::success(null, 'Đã xóa khối học thành công.');
    }
}

// 9. ACADEMIC YEAR CONTROLLER - Full CRUD
class AcademicYearController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('academic_years.view');

        $search = trim($request->input('search', ''));
        $status = trim($request->input('status', ''));

        $pdo = Database::getConnection();
        $sql = "SELECT y.*, 
                    (SELECT COUNT(*) FROM classes c WHERE c.academic_year_id = y.id) as class_count, 
                    (SELECT COUNT(*) FROM students s WHERE s.academic_year_id = y.id AND s.deleted_at IS NULL) as student_count, 
                    (SELECT COUNT(*) FROM semesters sm WHERE sm.academic_year_id = y.id) as semester_count 
                FROM academic_years y 
                ORDER BY y.is_current DESC, y.start_date DESC";
        $years = $pdo->query($sql)->fetchAll();

        // Apply filters in-memory
        if (!empty($search)) {
            $sLower = mb_strtolower($search, 'UTF-8');
            $years = array_filter($years, function($y) use ($sLower) {
                return str_contains(mb_strtolower($y['name'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($y['code'] ?? '', 'UTF-8'), $sLower);
            });
            $years = array_values($years);
        }

        if (!empty($status)) {
            $years = array_filter($years, function($y) use ($status) {
                return ($y['status'] ?? '') === $status;
            });
            $years = array_values($years);
        }

        $currentSemester = $pdo->query("SELECT s.*, y.name as year_name FROM semesters s JOIN academic_years y ON s.academic_year_id = y.id WHERE s.is_current = 1 LIMIT 1")->fetch() ?: null;

        if ($request->isAjax()) {
            Response::json($years);
            return;
        }

        View::render('academic_years/index', [
            'years' => $years,
            'allYearsForExport' => $years,
            'currentSemester' => $currentSemester,
            'filters' => [
                'search' => $search,
                'status' => $status
            ]
        ]);
    }

    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('academic_years.create');
        $data = $request->all();
        $pdo  = Database::getConnection();

        $validator = Validator::make($data, [
            'name' => 'required',
            'code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);
        if ($validator->fails()) {
            Response::error($validator->firstError());
            return;
        }

        $chk = $pdo->prepare("SELECT id FROM academic_years WHERE code = ?");
        $chk->execute([trim($data['code'])]);
        if ($chk->fetch()) { 
            Response::error("Mã năm học '{$data['code']}' đã tồn tại trong hệ thống."); 
            return; 
        }

        if (!empty($data['is_current'])) {
            $pdo->exec("UPDATE academic_years SET is_current = 0");
        }

        $id = AcademicYear::create([
            'name'       => trim($data['name']),
            'code'       => trim($data['code']),
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
            'is_current' => !empty($data['is_current']) ? 1 : 0,
            'status'     => $data['status'] ?? 'active',
            'is_locked'  => !empty($data['is_locked']) ? 1 : 0
        ]);

        AuditLogger::log('CREATE_ACADEMIC_YEAR', 'academic_years', (string)$id, null, $data);
        Response::success(['id' => $id], 'Tạo năm học mới thành công!');
    }

    public function update(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('academic_years.update');
        $id   = (int)($params['id'] ?? 0);
        $year = AcademicYear::find($id);
        if (!$year) { 
            Response::error('Năm học không tồn tại.', 404); 
            return; 
        }

        $data = $request->all();
        $pdo  = Database::getConnection();

        if (isset($data['is_current']) && !empty($data['is_current'])) {
            $pdo->exec("UPDATE academic_years SET is_current = 0");
        }

        $updateData = [];
        if (isset($data['name'])) $updateData['name'] = trim($data['name']);
        if (isset($data['code'])) $updateData['code'] = trim($data['code']);
        if (isset($data['start_date'])) $updateData['start_date'] = $data['start_date'];
        if (isset($data['end_date'])) $updateData['end_date'] = $data['end_date'];
        if (isset($data['is_current'])) $updateData['is_current'] = !empty($data['is_current']) ? 1 : 0;
        if (isset($data['status'])) $updateData['status'] = $data['status'];
        if (isset($data['is_locked'])) $updateData['is_locked'] = !empty($data['is_locked']) ? 1 : 0;

        AcademicYear::update($id, $updateData);
        AuditLogger::log('UPDATE_ACADEMIC_YEAR', 'academic_years', (string)$id, $year, $data);
        Response::success(null, 'Cập nhật thông tin năm học thành công!');
    }

    public function destroy(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('academic_years.delete');
        $id   = (int)($params['id'] ?? 0);
        $year = AcademicYear::find($id);
        if (!$year) {
            Response::error('Năm học không tồn tại.');
            return;
        }
        if ($year['is_current']) { 
            Response::error('Không thể xóa niên khóa đang được đặt làm hiện hành.'); 
            return; 
        }

        $pdo = Database::getConnection();
        $hasClasses = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE academic_year_id = ?");
        $hasClasses->execute([$id]);
        if ($hasClasses->fetchColumn() > 0) {
            Response::error('Không thể xóa năm học đang có lớp học theo học. Hãy chuyển lớp sang năm học khác trước!');
            return;
        }

        AcademicYear::delete($id);
        AuditLogger::log('DELETE_ACADEMIC_YEAR', 'academic_years', (string)$id);
        Response::success(null, 'Đã xóa năm học thành công.');
    }
}

// 10. SEMESTER CONTROLLER - Full CRUD
class SemesterController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('semesters.view');

        // TẠM KHÓA PHẦN HỌC KỲ (Chuyển sang false khi cần kích hoạt lại)
        $isLocked = true;
        if ($isLocked) {
            View::render('semesters/locked');
            return;
        }

        $search = trim($request->input('search', ''));
        $yearId = trim($request->input('academic_year_id', ''));
        $status = trim($request->input('status', ''));

        $filters = [
            'search' => $search,
            'academic_year_id' => $yearId,
            'status' => $status
        ];

        $pdo = Database::getConnection();
        $sql = "SELECT s.*, a.name as year_name, a.code as year_code,
                    (SELECT COUNT(*) FROM grade_records g WHERE g.semester_id = s.id) as grade_count,
                    (SELECT COUNT(*) FROM schedules sc WHERE sc.semester_id = s.id) as schedule_count,
                    (SELECT COUNT(*) FROM exams e WHERE e.semester_id = s.id) as exam_count
                FROM semesters s 
                LEFT JOIN academic_years a ON s.academic_year_id = a.id 
                ORDER BY s.is_current DESC, s.academic_year_id DESC, s.id ASC";
        $semesters = $pdo->query($sql)->fetchAll();

        // Apply filters in-memory
        if (!empty($search)) {
            $sLower = mb_strtolower($search, 'UTF-8');
            $semesters = array_filter($semesters, function($s) use ($sLower) {
                return str_contains(mb_strtolower($s['name'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($s['code'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($s['year_name'] ?? '', 'UTF-8'), $sLower);
            });
            $semesters = array_values($semesters);
        }

        if (!empty($yearId)) {
            $semesters = array_filter($semesters, function($s) use ($yearId) {
                return (string)($s['academic_year_id'] ?? '') === (string)$yearId;
            });
            $semesters = array_values($semesters);
        }

        if (!empty($status)) {
            $semesters = array_filter($semesters, function($s) use ($status) {
                return ($s['status'] ?? '') === $status;
            });
            $semesters = array_values($semesters);
        }

        $years = AcademicYear::all('is_current DESC, id DESC');

        if ($request->isAjax()) {
            Response::json($semesters);
            return;
        }

        View::render('semesters/index', [
            'semesters' => $semesters,
            'allSemestersForExport' => $semesters,
            'years' => $years,
            'filters' => $filters
        ]);
    }

    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('semesters.create');
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'academic_year_id' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);
        if ($validator->fails()) {
            Response::error($validator->firstError());
            return;
        }

        $pdo = Database::getConnection();
        if (!empty($data['is_current'])) {
            $pdo->exec("UPDATE semesters SET is_current = 0");
        }

        $code = trim($data['code'] ?? '');
        if (empty($code)) {
            $code = 'HK' . (Semester::count("academic_year_id = ?", [(int)$data['academic_year_id']]) + 1);
        }

        $id = Semester::create([
            'name'             => trim($data['name']),
            'code'             => strtoupper($code),
            'academic_year_id' => (int)$data['academic_year_id'],
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'is_current'       => !empty($data['is_current']) ? 1 : 0,
            'status'           => $data['status'] ?? 'active'
        ]);

        AuditLogger::log('CREATE_SEMESTER', 'semesters', (string)$id, null, $data);
        Response::success(['id' => $id], 'Tạo học kỳ mới thành công!');
    }

    public function update(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('semesters.update');
        $id       = (int)($params['id'] ?? 0);
        $semester = Semester::find($id);
        if (!$semester) { 
            Response::error('Học kỳ không tồn tại.', 404); 
            return; 
        }

        $data = $request->all();
        $pdo  = Database::getConnection();

        if (isset($data['is_current']) && !empty($data['is_current'])) {
            $pdo->exec("UPDATE semesters SET is_current = 0");
        }

        $updateData = [];
        if (isset($data['name'])) $updateData['name'] = trim($data['name']);
        if (isset($data['code'])) $updateData['code'] = strtoupper(trim($data['code']));
        if (isset($data['academic_year_id'])) $updateData['academic_year_id'] = (int)$data['academic_year_id'];
        if (isset($data['start_date'])) $updateData['start_date'] = $data['start_date'];
        if (isset($data['end_date'])) $updateData['end_date'] = $data['end_date'];
        if (isset($data['is_current'])) $updateData['is_current'] = !empty($data['is_current']) ? 1 : 0;
        if (isset($data['status'])) $updateData['status'] = $data['status'];

        Semester::update($id, $updateData);
        AuditLogger::log('UPDATE_SEMESTER', 'semesters', (string)$id, $semester, $data);
        Response::success(null, 'Cập nhật thông tin học kỳ thành công!');
    }

    public function destroy(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('semesters.delete');
        $id  = (int)($params['id'] ?? 0);
        $sem = Semester::find($id);
        if (!$sem) {
            Response::error('Học kỳ không tồn tại.');
            return;
        }
        if ($sem['is_current']) { 
            Response::error('Không thể xóa học kỳ đang được đặt làm hiện hành.'); 
            return; 
        }

        $pdo = Database::getConnection();
        $hasGrades = $pdo->prepare("SELECT COUNT(*) FROM grade_records WHERE semester_id = ?");
        $hasGrades->execute([$id]);
        if ($hasGrades->fetchColumn() > 0) {
            Response::error('Không thể xóa học kỳ đã có điểm số học sinh ghi nhận. Vui lòng kiểm tra lại!');
            return;
        }

        Semester::delete($id);
        AuditLogger::log('DELETE_SEMESTER', 'semesters', (string)$id);
        Response::success(null, 'Đã xóa học kỳ thành công.');
    }
}
