<?php
// controllers/TeacherController.php
require_once __DIR__ . '/BaseController.php';

class TeacherController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('teachers.view');

        $search = trim($request->input('search', ''));
        $specialization = trim($request->input('specialization', ''));
        $status = trim($request->input('status', ''));

        $filters = [
            'search' => $search,
            'specialization' => $specialization,
            'status' => $status
        ];

        $allTeachers = Teacher::getListWithDetails();
        $teachers = $allTeachers;

        // Apply filters in memory
        if (!empty($search)) {
            $sLower = mb_strtolower($search, 'UTF-8');
            $teachers = array_filter($teachers, function($t) use ($sLower) {
                return str_contains(mb_strtolower($t['full_name'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($t['teacher_code'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($t['phone'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($t['email'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($t['specialization'] ?? '', 'UTF-8'), $sLower);
            });
            $teachers = array_values($teachers);
        }

        if (!empty($specialization)) {
            $teachers = array_filter($teachers, function($t) use ($specialization) {
                return ($t['specialization'] ?? '') === $specialization;
            });
            $teachers = array_values($teachers);
        }

        if (!empty($status)) {
            $teachers = array_filter($teachers, function($t) use ($status) {
                return ($t['status'] ?? '') === $status;
            });
            $teachers = array_values($teachers);
        }

        if ($request->isAjax()) {
            Response::json($teachers);
            return;
        }

        // Distinct specializations for filter dropdown
        $specializations = array_filter(array_unique(array_column($allTeachers, 'specialization')));
        sort($specializations);

        View::render('teachers/index', [
            'teachers' => $teachers,
            'allTeachersForExport' => $teachers,
            'specializations' => $specializations,
            'filters' => $filters
        ]);
    }

    public function printList(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('teachers.view');

        $search = trim($request->input('search', ''));
        $specialization = trim($request->input('specialization', ''));
        $status = trim($request->input('status', ''));

        $allTeachers = Teacher::getListWithDetails();
        $teachers = $allTeachers;

        if (!empty($search)) {
            $sLower = mb_strtolower($search, 'UTF-8');
            $teachers = array_filter($teachers, function($t) use ($sLower) {
                return str_contains(mb_strtolower($t['full_name'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($t['teacher_code'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($t['phone'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($t['email'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($t['specialization'] ?? '', 'UTF-8'), $sLower);
            });
            $teachers = array_values($teachers);
        }

        if (!empty($specialization)) {
            $teachers = array_filter($teachers, function($t) use ($specialization) {
                return ($t['specialization'] ?? '') === $specialization;
            });
            $teachers = array_values($teachers);
        }

        if (!empty($status)) {
            $teachers = array_filter($teachers, function($t) use ($status) {
                return ($t['status'] ?? '') === $status;
            });
            $teachers = array_values($teachers);
        }

        View::render('teachers/print', [
            'teachers' => $teachers,
            'specialization' => $specialization,
            'status' => $status,
            'search' => $search
        ], 'none');
    }

    public function create(): void {
        $this->requireAuth();
        $this->requirePermission('teachers.create');

        $classes = SchoolClass::all('name ASC');

        View::render('teachers/form', [
            'isEdit' => false,
            'teacher' => [
                'teacher_code' => 'GV25' . rand(100, 999),
                'status' => 'active',
                'qualification' => 'Cử nhân Sư phạm',
                'specialization' => 'Toán Học',
                'gender' => 'male',
                'dob' => '1988-01-01',
                'start_date' => date('Y-m-d')
            ],
            'classes' => $classes
        ]);
    }

    public function edit(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('teachers.update');

        $id = (int)($params['id'] ?? 0);
        $teacher = Teacher::find($id);
        if (!$teacher) {
            Response::redirect('/teachers');
            return;
        }

        $classes = SchoolClass::all('name ASC');

        // Check if homeroom teacher for any class
        $pdo = Database::getConnection();
        $hrStmt = $pdo->prepare("SELECT id FROM classes WHERE homeroom_teacher_id = ? LIMIT 1");
        $hrStmt->execute([$id]);
        $teacher['homeroom_class_id'] = $hrStmt->fetchColumn() ?: null;

        View::render('teachers/form', [
            'isEdit' => true,
            'teacher' => $teacher,
            'classes' => $classes
        ]);
    }

    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('teachers.create');

        $data = $request->all();
        $validator = Validator::make($data, [
            'teacher_code' => 'required|min:3',
            'full_name' => 'required|min:2',
            'specialization' => 'required',
            'gender' => 'required'
        ]);

        if ($validator->fails()) {
            Response::error($validator->firstError());
            return;
        }

        $pdo = Database::getConnection();
        // Check duplicate code
        $exists = $pdo->prepare("SELECT id FROM teachers WHERE teacher_code = ? AND deleted_at IS NULL");
        $exists->execute([$data['teacher_code']]);
        if ($exists->fetch()) {
            Response::error("Mã giáo viên '{$data['teacher_code']}' đã tồn tại.");
            return;
        }

        // Create user account
        $username = strtolower(trim($data['teacher_code']));
        $userStmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, email, phone, role_id, status) VALUES (?, ?, ?, ?, ?, 3, 'active')");
        $userStmt->execute([$username, password_hash('teacher123', PASSWORD_DEFAULT), $data['full_name'], $data['email'] ?? null, $data['phone'] ?? null]);
        $userId = (int)$pdo->lastInsertId();

        $teacherId = Teacher::create([
            'user_id' => $userId,
            'teacher_code' => strtoupper($data['teacher_code']),
            'full_name' => $data['full_name'],
            'gender' => $data['gender'],
            'dob' => $data['dob'] ?? '1988-01-01',
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'qualification' => $data['qualification'] ?? 'Cử nhân Sư phạm',
            'specialization' => $data['specialization'],
            'start_date' => $data['start_date'] ?? date('Y-m-d'),
            'avatar' => !empty($data['avatar']) ? $data['avatar'] : ($data['gender'] === 'female' ? 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150' : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'),
            'status' => $data['status'] ?? 'active'
        ]);

        // Assign homeroom if selected
        if (!empty($data['homeroom_class_id'])) {
            $pdo->prepare("UPDATE classes SET homeroom_teacher_id = ? WHERE id = ?")->execute([$teacherId, (int)$data['homeroom_class_id']]);
        }

        AuditLogger::log('CREATE_TEACHER', 'teachers', (string)$teacherId, null, $data);
        Response::success(['id' => $teacherId], 'Thêm mới giáo viên thành công!');
    }

    public function update(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('teachers.update');

        $id = (int)($params['id'] ?? 0);
        $teacher = Teacher::find($id);
        if (!$teacher) {
            Response::error('Giáo viên không tồn tại.', 404);
            return;
        }

        $data = $request->all();
        Teacher::update($id, [
            'full_name' => $data['full_name'],
            'gender' => $data['gender'],
            'dob' => $data['dob'] ?? $teacher['dob'],
            'phone' => $data['phone'] ?? $teacher['phone'],
            'email' => $data['email'] ?? $teacher['email'],
            'address' => $data['address'] ?? $teacher['address'],
            'qualification' => $data['qualification'] ?? $teacher['qualification'],
            'specialization' => $data['specialization'] ?? $teacher['specialization'],
            'start_date' => $data['start_date'] ?? $teacher['start_date'],
            'avatar' => !empty($data['avatar']) ? $data['avatar'] : $teacher['avatar'],
            'status' => $data['status'] ?? $teacher['status']
        ]);

        // Update homeroom assignment
        $pdo = Database::getConnection();
        if (isset($data['homeroom_class_id'])) {
            $pdo->prepare("UPDATE classes SET homeroom_teacher_id = NULL WHERE homeroom_teacher_id = ?")->execute([$id]);
            if (!empty($data['homeroom_class_id'])) {
                $pdo->prepare("UPDATE classes SET homeroom_teacher_id = ? WHERE id = ?")->execute([$id, (int)$data['homeroom_class_id']]);
            }
        }

        AuditLogger::log('UPDATE_TEACHER', 'teachers', (string)$id, $teacher, $data);
        Response::success(null, 'Cập nhật thông tin giáo viên thành công!');
    }

    public function destroy(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('teachers.delete');

        $id = (int)($params['id'] ?? 0);
        Teacher::delete($id);
        AuditLogger::log('DELETE_TEACHER', 'teachers', (string)$id);
        Response::success(null, 'Đã chuyển giáo viên vào danh sách lưu trữ.');
    }

    public function import(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('teachers.create');

        $records = $request->input('records', []);
        if (empty($records) || !is_array($records)) {
            Response::error('Dữ liệu nhập từ file Excel không hợp lệ hoặc bị rỗng.');
            return;
        }

        $pdo = Database::getConnection();
        $importedCount = 0;
        $errors = [];

        foreach ($records as $idx => $row) {
            $line = $idx + 1;
            $code = strtoupper(trim($row['teacher_code'] ?? $row['code'] ?? ''));
            $fullName = trim($row['full_name'] ?? $row['name'] ?? '');
            $gender = strtolower(trim($row['gender'] ?? ''));
            $genderVal = ($gender === 'nữ' || $gender === 'female') ? 'female' : 'male';
            $specialization = trim($row['specialization'] ?? 'Toán Học');
            $qualification = trim($row['qualification'] ?? 'Cử nhân Sư phạm');
            $phone = trim($row['phone'] ?? '');
            $email = trim($row['email'] ?? '');
            $address = trim($row['address'] ?? '');

            if (empty($code) || empty($fullName)) {
                $errors[] = "Dòng {$line}: Thiếu Mã giáo viên hoặc Họ và tên.";
                continue;
            }

            // Check duplicate
            $checkStmt = $pdo->prepare("SELECT id FROM teachers WHERE teacher_code = ? AND deleted_at IS NULL");
            $checkStmt->execute([$code]);
            if ($checkStmt->fetch()) {
                $errors[] = "Dòng {$line}: Mã GV '{$code}' đã tồn tại.";
                continue;
            }

            // Create user account
            $username = strtolower($code);
            $userStmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, email, phone, role_id, status) VALUES (?, ?, ?, ?, ?, 3, 'active')");
            $userStmt->execute([$username, password_hash('teacher123', PASSWORD_DEFAULT), $fullName, $email ?: null, $phone ?: null]);
            $userId = (int)$pdo->lastInsertId();

            Teacher::create([
                'user_id' => $userId,
                'teacher_code' => $code,
                'full_name' => $fullName,
                'gender' => $genderVal,
                'dob' => '1988-01-01',
                'phone' => $phone ?: null,
                'email' => $email ?: null,
                'address' => $address ?: null,
                'qualification' => $qualification ?: 'Cử nhân Sư phạm',
                'specialization' => $specialization,
                'start_date' => date('Y-m-d'),
                'avatar' => $genderVal === 'female' ? 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150' : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150',
                'status' => 'active'
            ]);

            $importedCount++;
        }

        if ($importedCount === 0 && !empty($errors)) {
            Response::error(implode('<br>', array_slice($errors, 0, 5)));
            return;
        }

        AuditLogger::log('IMPORT_TEACHERS_EXCEL', 'teachers', 'batch', null, ['count' => $importedCount]);
        $msg = "Đã nhập thành công {$importedCount} giáo viên vào hệ thống!";
        if (!empty($errors)) {
            $msg .= " (Bỏ qua " . count($errors) . " dòng bị lỗi/trùng).";
        }
        Response::success(['imported' => $importedCount], $msg);
    }
}