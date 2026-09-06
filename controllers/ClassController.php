<?php
// controllers/ClassController.php
require_once __DIR__ . '/BaseController.php';

class ClassController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('classes.view');

        $search = trim($request->input('search', ''));
        $gradeId = trim($request->input('grade_id', ''));
        $yearId = trim($request->input('academic_year_id', ''));
        $status = trim($request->input('status', ''));

        $filters = [
            'search' => $search,
            'grade_id' => $gradeId,
            'academic_year_id' => $yearId,
            'status' => $status
        ];

        $classes = SchoolClass::getListWithStats();

        // Apply filters in-memory
        if (!empty($search)) {
            $sLower = mb_strtolower($search, 'UTF-8');
            $classes = array_filter($classes, function($c) use ($sLower) {
                return str_contains(mb_strtolower($c['name'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($c['code'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($c['room_number'] ?? '', 'UTF-8'), $sLower)
                    || str_contains(mb_strtolower($c['homeroom_teacher_name'] ?? '', 'UTF-8'), $sLower);
            });
            $classes = array_values($classes);
        }

        if (!empty($gradeId)) {
            $classes = array_filter($classes, function($c) use ($gradeId) {
                return (string)($c['grade_id'] ?? '') === (string)$gradeId;
            });
            $classes = array_values($classes);
        }

        if (!empty($yearId)) {
            $classes = array_filter($classes, function($c) use ($yearId) {
                return (string)($c['academic_year_id'] ?? '') === (string)$yearId;
            });
            $classes = array_values($classes);
        }

        if (!empty($status)) {
            $classes = array_filter($classes, function($c) use ($status) {
                return ($c['status'] ?? '') === $status;
            });
            $classes = array_values($classes);
        }

        $grades = GradeLevel::all('order_index ASC');
        $years = AcademicYear::all('id DESC');
        $teachers = Teacher::all('full_name ASC');
        $subjects = Subject::all("status = 'active'", 'name ASC');

        if ($request->isAjax()) {
            Response::json($classes);
            return;
        }

        View::render('classes/index', [
            'classes' => $classes,
            'allClassesForExport' => $classes,
            'grades' => $grades,
            'years' => $years,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'filters' => $filters
        ]);
    }

    public function show(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('classes.view');

        $id = (int)($params['id'] ?? 0);
        $class = SchoolClass::find($id);
        if (!$class) {
            Response::redirect('/classes');
            return;
        }

        $students = SchoolClass::getStudents($id);
        $allClasses = SchoolClass::all('name ASC');
        $classSubjects = SchoolClass::getSubjects($id);

        if ($request->isAjax() || $request->input('format') === 'json') {
            Response::json([
                'class' => $class,
                'students' => $students,
                'subjects' => $classSubjects
            ]);
            return;
        }

        View::render('classes/show', [
            'class' => $class,
            'students' => $students,
            'allClasses' => $allClasses,
            'classSubjects' => $classSubjects
        ]);
    }

    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('classes.create');

        $data = $request->all();
        $validator = Validator::make($data, [
            'code' => 'required',
            'name' => 'required',
            'grade_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            Response::error($validator->firstError());
            return;
        }

        $pdo = Database::getConnection();
        // Check duplicate code
        $exists = $pdo->prepare("SELECT id FROM classes WHERE code = ?");
        $exists->execute([$data['code']]);
        if ($exists->fetch()) {
            Response::error("Mã lớp '{$data['code']}' đã tồn tại trong hệ thống.");
            return;
        }

        // Check homeroom teacher clash
        if (!empty($data['homeroom_teacher_id']) && SystemSetting::get('prevent_homeroom_clash', '1') == '1') {
            $tExists = $pdo->prepare("SELECT id, name FROM classes WHERE homeroom_teacher_id = ? AND academic_year_id = ?");
            $tExists->execute([$data['homeroom_teacher_id'], $data['academic_year_id'] ?? 1]);
            $clash = $tExists->fetch();
            if ($clash) {
                Response::error("Giáo viên này đã được phân công làm chủ nhiệm lớp '{$clash['name']}'. Vui lòng chọn giáo viên khác.");
                return;
            }
        }

        $id = SchoolClass::create([
            'code' => strtoupper($data['code']),
            'name' => $data['name'],
            'grade_id' => $data['grade_id'],
            'academic_year_id' => $data['academic_year_id'] ?? 1,
            'homeroom_teacher_id' => !empty($data['homeroom_teacher_id']) ? $data['homeroom_teacher_id'] : null,
            'room_number' => $data['room_number'] ?? null,
            'max_students' => $data['max_students'] ?? 45,
            'status' => $data['status'] ?? 'active'
        ]);

        // Sync subjects
        $subjectIds = $data['subject_ids'] ?? [];
        if (is_string($subjectIds)) {
            $subjectIds = !empty($subjectIds) ? explode(',', $subjectIds) : [];
        }
        if (is_array($subjectIds)) {
            SchoolClass::syncSubjects((int)$id, $subjectIds);
        }

        AuditLogger::log('CREATE_CLASS', 'classes', (string)$id, null, $data);
        Response::success(['id' => $id], 'Tạo mới lớp học và phân bổ môn học thành công!');
    }

    public function update(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('classes.update');

        $id = (int)($params['id'] ?? 0);
        $class = SchoolClass::find($id);
        if (!$class) { 
            Response::error('Lớp học không tồn tại.', 404); 
            return; 
        }

        $data = $request->all();
        $pdo  = Database::getConnection();

        // Check homeroom teacher clash (exclude self)
        if (!empty($data['homeroom_teacher_id'])) {
            $tExists = $pdo->prepare("SELECT id, name FROM classes WHERE homeroom_teacher_id = ? AND academic_year_id = ? AND id != ?");
            $tExists->execute([$data['homeroom_teacher_id'], $class['academic_year_id'], $id]);
            $clash = $tExists->fetch();
            if ($clash) {
                Response::error("Giáo viên này đã làm chủ nhiệm lớp '{$clash['name']}'. Vui lòng chọn giáo viên khác.");
                return;
            }
        }

        SchoolClass::update($id, [
            'name' => $data['name'] ?? $class['name'],
            'grade_id' => $data['grade_id'] ?? $class['grade_id'],
            'academic_year_id' => $data['academic_year_id'] ?? $class['academic_year_id'],
            'homeroom_teacher_id' => !empty($data['homeroom_teacher_id']) ? (int)$data['homeroom_teacher_id'] : null,
            'room_number' => $data['room_number'] ?? $class['room_number'],
            'max_students' => (int)($data['max_students'] ?? $class['max_students']),
            'status' => $data['status'] ?? $class['status'],
        ]);

        // Sync subjects
        if (array_key_exists('subject_ids', $data)) {
            $subjectIds = $data['subject_ids'];
            if (is_string($subjectIds)) {
                $subjectIds = !empty($subjectIds) ? explode(',', $subjectIds) : [];
            }
            if (is_array($subjectIds)) {
                SchoolClass::syncSubjects($id, $subjectIds);
            }
        }

        AuditLogger::log('UPDATE_CLASS', 'classes', (string)$id, $class, $data);
        Response::success(null, 'Cập nhật thông tin và danh sách môn học của lớp thành công!');
    }

    public function destroy(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('classes.delete');

        $id  = (int)($params['id'] ?? 0);
        $pdo = Database::getConnection();

        // Check if class has students
        $hasStudents = $pdo->prepare("SELECT COUNT(*) FROM students WHERE class_id = ? AND deleted_at IS NULL");
        $hasStudents->execute([$id]);
        if ($hasStudents->fetchColumn() > 0) {
            Response::error('Không thể xóa lớp đang có học sinh theo học. Hãy chuyển hoặc xóa học sinh trước.');
            return;
        }

        SchoolClass::delete($id);
        AuditLogger::log('DELETE_CLASS', 'classes', (string)$id);
        Response::success(null, 'Đã xóa lớp học thành công.');
    }

    public function transferStudent(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('classes.assign_students');

        $studentId     = (int)$request->input('student_id');
        $targetClassId = (int)$request->input('target_class_id');

        $student     = Student::find($studentId);
        $targetClass = SchoolClass::find($targetClassId);

        if (!$student || !$targetClass) {
            Response::error('Học sinh hoặc Lớp học đích không tồn tại.');
            return;
        }

        Student::update($studentId, [
            'class_id' => $targetClassId,
            'grade_id' => $targetClass['grade_id']
        ]);

        AuditLogger::log('TRANSFER_STUDENT', 'classes', (string)$studentId, ['old_class_id' => $student['class_id']], ['new_class_id' => $targetClassId]);
        Response::success(null, "Đã chuyển học sinh '{$student['full_name']}' sang lớp '{$targetClass['name']}' thành công!");
    }

    public function printList(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('classes.view');

        $gradeId = trim($request->input('grade_id', ''));
        $yearId  = trim($request->input('academic_year_id', ''));
        $status  = trim($request->input('status', ''));

        $classes = SchoolClass::getListWithStats();

        if (!empty($gradeId)) {
            $classes = array_filter($classes, fn($c) => (string)($c['grade_id'] ?? '') === (string)$gradeId);
            $classes = array_values($classes);
        }
        if (!empty($yearId)) {
            $classes = array_filter($classes, fn($c) => (string)($c['academic_year_id'] ?? '') === (string)$yearId);
            $classes = array_values($classes);
        }
        if (!empty($status)) {
            $classes = array_filter($classes, fn($c) => ($c['status'] ?? '') === $status);
            $classes = array_values($classes);
        }

        $grades = GradeLevel::all('order_index ASC');
        $years  = AcademicYear::all('id DESC');

        View::render('classes/print', [
            'classes'         => $classes,
            'grades'          => $grades,
            'years'           => $years,
            'selectedGradeId' => $gradeId,
            'selectedYearId'  => $yearId,
            'selectedStatus'  => $status
        ], 'none');
    }
}
