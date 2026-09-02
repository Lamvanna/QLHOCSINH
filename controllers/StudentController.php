<?php
// controllers/StudentController.php
require_once __DIR__ . '/BaseController.php';

class StudentController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('students.view');

        $page = (int)$request->input('page', 1);
        $limit = (int)$request->input('limit', 15);
        $filters = [
            'search' => $request->input('search'),
            'class_id' => $request->input('class_id'),
            'grade_id' => $request->input('grade_id'),
            'gender' => $request->input('gender'),
            'status' => $request->input('status'),
            'sort_by' => $request->input('sort_by', 's.id'),
            'sort_order' => $request->input('sort_order', 'DESC')
        ];

        $studentsData = Student::getPaginated($page, $limit, $filters);
        
        if ($request->isAjax()) {
            Response::json($studentsData);
            return;
        }

        $classes = SchoolClass::all('name ASC');
        $grades = GradeLevel::all('order_index ASC');
        $years = AcademicYear::all('id DESC');

        View::render('students/index', [
            'students' => $studentsData['data'],
            'pagination' => $studentsData,
            'classes' => $classes,
            'grades' => $grades,
            'years' => $years,
            'filters' => $filters
        ]);
    }

    public function show(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('students.view');
        
        $id = (int)($params['id'] ?? 0);
        $student = Student::getProfile($id);

        if (!$student) {
            Response::redirect('/students');
            return;
        }

        if ($request->isAjax()) {
            Response::json($student);
            return;
        }

        View::render('students/show', ['student' => $student]);
    }

    public function create(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('students.create');

        $classes = SchoolClass::all('name ASC');
        $grades = GradeLevel::all('order_index ASC');
        $years = AcademicYear::all('id DESC');

        View::render('students/form', [
            'isEdit' => false,
            'student' => [
                'student_code' => 'HS' . date('y') . rand(100, 999),
                'full_name' => '',
                'khmer_name' => '',
                'gender' => 'male',
                'dob' => '2010-01-01',
                'class_id' => 1,
                'status' => 'studying',
                'phone' => '',
                'email' => '',
                'address' => ''
            ],
            'classes' => $classes,
            'grades' => $grades,
            'years' => $years
        ]);
    }

    public function edit(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('students.update');

        $id = (int)($params['id'] ?? 0);
        $student = Student::find($id);

        if (!$student) {
            Response::redirect('/students');
            return;
        }

        $classes = SchoolClass::all('name ASC');
        $grades = GradeLevel::all('order_index ASC');
        $years = AcademicYear::all('id DESC');

        View::render('students/form', [
            'isEdit' => true,
            'student' => $student,
            'classes' => $classes,
            'grades' => $grades,
            'years' => $years
        ]);
    }

    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('students.create');

        $data = $request->all();
        $validator = Validator::make($data, [
            'student_code' => 'required|min:3|max:30',
            'full_name' => 'required|min:2|max:150',
            'gender' => 'required',
            'dob' => 'required|date',
            'class_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            Response::error($validator->firstError());
            return;
        }

        $pdo = Database::getConnection();
        // Check duplicate code
        $exists = $pdo->prepare("SELECT id FROM students WHERE student_code = ? AND deleted_at IS NULL");
        $exists->execute([$data['student_code']]);
        if ($exists->fetch()) {
            Response::error("Mã học sinh '{$data['student_code']}' đã tồn tại trong hệ thống.");
            return;
        }

        // Get class info to assign grade
        $class = SchoolClass::find($data['class_id']);
        $gradeId = $class['grade_id'] ?? 1;
        $yearId = $class['academic_year_id'] ?? 1;

        // Create linked user account
        $username = strtolower(trim($data['student_code']));
        $passHash = password_hash('student123', PASSWORD_DEFAULT);
        
        $userStmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, email, phone, role_id, status) VALUES (?, ?, ?, ?, ?, 4, 'active')");
        $userStmt->execute([$username, $passHash, $data['full_name'], $data['email'] ?? null, $data['phone'] ?? null]);
        $userId = (int)$pdo->lastInsertId();

        $studentId = Student::create([
            'user_id' => $userId,
            'student_code' => strtoupper($data['student_code']),
            'full_name' => $data['full_name'],
            'khmer_name' => $data['khmer_name'] ?? null,
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'pob' => $data['pob'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'avatar' => $data['avatar'] ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150',
            'class_id' => $data['class_id'],
            'grade_id' => $gradeId,
            'academic_year_id' => $yearId,
            'admission_date' => $data['admission_date'] ?? date('Y-m-d'),
            'status' => 'studying'
        ]);

        AuditLogger::log('CREATE_STUDENT', 'students', (string)$studentId, null, $data);
        Response::success(['id' => $studentId], 'Thêm mới học sinh thành công!');
    }

    public function update(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('students.update');

        $id = (int)($params['id'] ?? 0);
        $student = Student::find($id);
        if (!$student) {
            Response::error('Học sinh không tồn tại.', 404);
            return;
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'full_name' => 'required|min:2',
            'gender' => 'required',
            'dob' => 'required|date',
            'class_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            Response::error($validator->firstError());
            return;
        }

        $updateFields = [
            'full_name' => $data['full_name'],
            'khmer_name' => $data['khmer_name'] ?? $student['khmer_name'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'pob' => $data['pob'] ?? $student['pob'],
            'address' => $data['address'] ?? $student['address'],
            'phone' => $data['phone'] ?? $student['phone'],
            'email' => $data['email'] ?? $student['email'],
            'class_id' => $data['class_id'],
            'status' => $data['status'] ?? $student['status']
        ];

        Student::update($id, $updateFields);
        AuditLogger::log('UPDATE_STUDENT', 'students', (string)$id, $student, $updateFields);

        Response::success(null, 'Cập nhật thông tin học sinh thành công!');
    }

    public function destroy(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('students.delete');

        $id = (int)($params['id'] ?? 0);
        $student = Student::find($id);
        if (!$student) {
            Response::error('Học sinh không tồn tại.', 404);
            return;
        }

        Student::delete($id);
        AuditLogger::log('SOFT_DELETE_STUDENT', 'students', (string)$id, $student);

        Response::success(null, 'Đã chuyển học sinh vào danh sách lưu trữ (Soft Delete).');
    }

    public function restore(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('students.restore');

        $id = (int)($params['id'] ?? 0);
        Student::restore($id);
        AuditLogger::log('RESTORE_STUDENT', 'students', (string)$id);

        Response::success(null, 'Khôi phục học sinh thành công!');
    }

    public function import(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('students.import');

        $records = $request->input('records', []);
        if (empty($records) || !is_array($records)) {
            Response::error('Dữ liệu tải lên không hợp lệ hoặc danh sách rỗng.');
            return;
        }

        $pdo = Database::getConnection();
        $inserted = 0;
        $errors = [];

        foreach ($records as $index => $row) {
            $line = $index + 1;
            if (empty($row['student_code']) || empty($row['full_name'])) {
                $errors[] = "Dòng {$line}: Thiếu Mã học sinh hoặc Họ tên.";
                continue;
            }

            // Check duplicate
            $chk = $pdo->prepare("SELECT id FROM students WHERE student_code = ? AND deleted_at IS NULL");
            $chk->execute([$row['student_code']]);
            if ($chk->fetch()) {
                $errors[] = "Dòng {$line}: Mã HS '{$row['student_code']}' đã tồn tại.";
                continue;
            }

            $userStmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, role_id, status) VALUES (?, ?, ?, 4, 'active')");
            $userStmt->execute([strtolower($row['student_code']), password_hash('student123', PASSWORD_DEFAULT), $row['full_name']]);
            $uId = $pdo->lastInsertId();

            Student::create([
                'user_id' => $uId,
                'student_code' => strtoupper($row['student_code']),
                'full_name' => $row['full_name'],
                'khmer_name' => $row['khmer_name'] ?? null,
                'gender' => $row['gender'] ?? 'male',
                'dob' => $row['dob'] ?? '2010-01-01',
                'class_id' => $row['class_id'] ?? 1,
                'grade_id' => 1,
                'academic_year_id' => 1,
                'status' => 'studying'
            ]);
            $inserted++;
        }

        AuditLogger::log('IMPORT_STUDENTS', 'students', null, null, ['inserted' => $inserted, 'errors_count' => count($errors)]);

        Response::success([
            'inserted' => $inserted,
            'errors' => $errors
        ], "Đã nhập thành công {$inserted} học sinh." . (count($errors) > 0 ? " Có " . count($errors) . " dòng lỗi." : ""));
    }

    public function printClass(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('students.view');

        $classId = (int)$request->input('class_id', 0);
        $selectedClass = null;
        
        $pdo = Database::getConnection();

        if ($classId > 0) {
            $stmt = $pdo->prepare("SELECT c.*, g.name as grade_name, y.name as year_name, t.full_name as homeroom_teacher_name 
                                   FROM classes c 
                                   LEFT JOIN grade_levels g ON c.grade_id = g.id 
                                   LEFT JOIN academic_years y ON c.academic_year_id = y.id 
                                   LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id 
                                   WHERE c.id = ?");
            $stmt->execute([$classId]);
            $selectedClass = $stmt->fetch() ?: null;

            if ($selectedClass) {
                $stStmt = $pdo->prepare("SELECT s.*, c.name as class_name, g.name as grade_name, y.name as year_name 
                                         FROM students s 
                                         LEFT JOIN classes c ON s.class_id = c.id 
                                         LEFT JOIN grade_levels g ON s.grade_id = g.id 
                                         LEFT JOIN academic_years y ON s.academic_year_id = y.id 
                                         WHERE s.class_id = ? AND s.deleted_at IS NULL 
                                         ORDER BY s.student_code ASC");
                $stStmt->execute([$classId]);
                $students = $stStmt->fetchAll();
            } else {
                $students = [];
            }
        } else {
            $filters = [
                'search' => $request->input('search'),
                'class_id' => $request->input('class_id'),
                'grade_id' => $request->input('grade_id'),
                'gender' => $request->input('gender'),
                'status' => $request->input('status')
            ];
            $studentsData = Student::getPaginated(1, 1000, $filters);
            $students = $studentsData['data'];
        }

        $classes = SchoolClass::all('name ASC');

        View::render('students/print', [
            'students' => $students,
            'selectedClass' => $selectedClass,
            'classes' => $classes,
            'classId' => $classId,
            'autoPrint' => (bool)$request->input('auto', 0)
        ], 'none');
    }
}