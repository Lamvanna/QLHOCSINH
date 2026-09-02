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

        // Build academic history from Kindergarten to current class
        $academicHistory = $this->buildAcademicHistory($student);

        if ($request->isAjax()) {
            $student['academic_history'] = $academicHistory;
            Response::json($student);
            return;
        }

        View::render('students/show', [
            'student' => $student,
            'academicHistory' => $academicHistory
        ]);
    }

    private function buildAcademicHistory(array $student): array {
        $pdo = \Database::getConnection();
        $studentId = (int)$student['id'];
        $currentClassId = (int)($student['class_id'] ?? 1);

        $cStmt = $pdo->prepare('SELECT id, name, homeroom_teacher_id FROM classes WHERE id = ?');
        $cStmt->execute([$currentClassId]);
        $currClass = $cStmt->fetch();
        $currClassName = $currClass['name'] ?? 'Lớp 1';
        preg_match('/\d+/', $currClassName, $m);
        $currGradeNum = !empty($m[0]) ? (int)$m[0] : 1;

        $currYearEnd = 2026;
        $history = [];

        // 1. Mẫu giáo stage
        $history[] = [
            'stage_key' => 'maugiao',
            'title' => 'Mẫu giáo',
            'class_name' => 'Mầm non / Mẫu giáo',
            'year_name' => 'Trước năm học ' . ($currYearEnd - $currGradeNum),
            'teacher_name' => 'Giáo viên phụ trách mầm non',
            'is_current' => false,
            'promotion_status' => 'Đã hoàn thành chương trình Mầm non',
            'conduct' => 'Chăm ngoan',
            'avg_score' => null,
            'rank' => 'Hoàn thành',
            'has_grades' => false,
            'grades' => [],
            'note' => 'Bậc mầm non đánh giá sự phát triển theo chuẩn phát triển trẻ em, không áp dụng tính điểm số các môn học.'
        ];

        // Fetch all classes
        $allClasses = $pdo->query('SELECT c.id, c.name, t.full_name as teacher_name 
                                  FROM classes c 
                                  LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id 
                                  ORDER BY c.id ASC')->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);

        for ($g = 1; $g <= $currGradeNum; $g++) {
            $isCurrent = ($g === $currGradeNum);
            $yStart = $currYearEnd - ($currGradeNum - $g) - 1;
            $yEnd = $currYearEnd - ($currGradeNum - $g);
            $yearName = 'Năm học ' . $yStart . ' – ' . $yEnd;

            $cls = $allClasses[$g] ?? ['name' => 'Lớp ' . $g, 'teacher_name' => 'Chưa phân công'];

            $grStmt = $pdo->prepare('SELECT gr.*, sub.name as subject_name, sub.coefficient, gc.name as component_name 
                                      FROM grade_records gr 
                                      JOIN subjects sub ON gr.subject_id = sub.id 
                                      JOIN grade_components gc ON gr.component_id = gc.id 
                                      WHERE gr.student_id = ? AND gr.class_id = ? 
                                      ORDER BY sub.id, gc.id');
            $grStmt->execute([$studentId, $g]);
            $grades = $grStmt->fetchAll();

            $avg = null;
            $rank = null;
            $classRank = null;
            $classTotal = 0;

            if (!empty($grades)) {
                $scores = array_filter(array_column($grades, 'score'), function($v) { return $v !== null; });
                if (!empty($scores)) {
                    $avg = round(array_sum($scores) / count($scores), 1);
                    if ($avg >= 8.0) $rank = 'Xuất sắc / Giỏi';
                    elseif ($avg >= 6.5) $rank = 'Khá';
                    elseif ($avg >= 5.0) $rank = 'Trung bình';
                    else $rank = 'Chưa đạt';
                }
            }

            // Calculate class rank position
            $targetCid = !empty($cls['id']) ? (int)$cls['id'] : $g;
            if ($targetCid > 0) {
                try {
                    $rStmt = $pdo->prepare("SELECT student_id, AVG(score) as avg_sc FROM grade_records WHERE class_id = ? GROUP BY student_id ORDER BY avg_sc DESC");
                    $rStmt->execute([$targetCid]);
                    $allRanks = $rStmt->fetchAll();

                    $tStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE class_id = ? AND deleted_at IS NULL");
                    $tStmt->execute([$targetCid]);
                    $classTotal = (int)$tStmt->fetchColumn();

                    $pos = 1;
                    foreach ($allRanks as $ar) {
                        if ((int)$ar['student_id'] === $studentId) {
                            $classRank = $pos;
                            break;
                        }
                        $pos++;
                    }
                } catch (\Exception $e) {}
            }

            $history[] = [
                'stage_key' => 'lop_' . $g,
                'title' => 'Lớp ' . $g,
                'class_name' => $cls['name'],
                'year_name' => $yearName,
                'teacher_name' => $cls['teacher_name'] ?? 'Chưa phân công',
                'is_current' => $isCurrent,
                'promotion_status' => $isCurrent ? 'Đang theo học' : 'Đã hoàn thành chương trình Lớp ' . $g . ' (Được lên lớp)',
                'conduct' => $isCurrent ? 'Đang theo dõi đánh giá' : 'Tốt',
                'avg_score' => $avg,
                'rank' => $rank,
                'class_rank' => $classRank,
                'class_total' => $classTotal,
                'has_grades' => !empty($grades),
                'grades' => $grades,
                'note' => ''
            ];
        }

        return $history;
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

            $dob = $row['dob'] ?? '2010-01-01';
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', trim($dob), $m)) {
                $dob = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }

            $classId = (int)($row['class_id'] ?? 1);
            $gradeId = 1;
            $academicYearId = 1;

            if (!empty($row['class_name'])) {
                $cStmt = $pdo->prepare("SELECT id, grade_id, academic_year_id FROM classes WHERE name = ? AND deleted_at IS NULL LIMIT 1");
                $cStmt->execute([trim($row['class_name'])]);
                if ($cRow = $cStmt->fetch()) {
                    $classId = (int)$cRow['id'];
                    $gradeId = (int)($cRow['grade_id'] ?? 1);
                    $academicYearId = (int)($cRow['academic_year_id'] ?? 1);
                }
            } else if ($classId > 0) {
                $cStmt = $pdo->prepare("SELECT grade_id, academic_year_id FROM classes WHERE id = ? LIMIT 1");
                $cStmt->execute([$classId]);
                if ($cRow = $cStmt->fetch()) {
                    $gradeId = (int)($cRow['grade_id'] ?? 1);
                    $academicYearId = (int)($cRow['academic_year_id'] ?? 1);
                }
            }

            Student::create([
                'user_id' => $uId,
                'student_code' => strtoupper($row['student_code']),
                'full_name' => $row['full_name'],
                'khmer_name' => $row['khmer_name'] ?? null,
                'gender' => $row['gender'] ?? 'male',
                'dob' => $dob,
                'class_id' => $classId,
                'grade_id' => $gradeId,
                'academic_year_id' => $academicYearId,
                'phone' => $row['phone'] ?? null,
                'address' => $row['address'] ?? null,
                'email' => $row['email'] ?? null,
                'status' => $row['status'] ?? 'studying'
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