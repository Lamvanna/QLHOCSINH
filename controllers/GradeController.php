<?php
// controllers/GradeController.php
require_once __DIR__ . '/BaseController.php';

class GradeController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.view');

        $classes = SchoolClass::all('name ASC');
        $academicYears = AcademicYear::all('id ASC');

        $classId = (int)$request->input('class_id', $classes[0]['id'] ?? 1);
        $selectedClassObj = SchoolClass::find($classId);

        // Xác định năm học:
        $currentYear = AcademicYear::getCurrent();
        $defaultYearId = $selectedClassObj['academic_year_id'] ?? ($currentYear['id'] ?? 1);

        $academicYearId = null;
        if ($request->input('academic_year_id')) {
            $academicYearId = (int)$request->input('academic_year_id');
        } elseif ($request->input('semester_id')) {
            $sem = Semester::find((int)$request->input('semester_id'));
            if ($sem && !empty($sem['academic_year_id'])) {
                $academicYearId = (int)$sem['academic_year_id'];
            }
        }
        if (!$academicYearId) {
            $academicYearId = (int)$defaultYearId;
        }

        // Lấy bảng điểm tất cả môn của lớp theo năm học
        $gradeData = GradeRecord::getClassAllSubjectsGrades($classId, $academicYearId);

        if ($request->isAjax()) {
            Response::json($gradeData);
            return;
        }

        $selectedYearObj = AcademicYear::find($academicYearId);

        View::render('grades/index', [
            'classes' => $classes,
            'academicYears' => $academicYears,
            'selectedClass' => $classId,
            'selectedAcademicYear' => $academicYearId,
            'selectedClassObj' => $selectedClassObj,
            'selectedYearObj' => $selectedYearObj,
            'gradeData' => $gradeData
        ]);
    }

    /**
     * Trang in ấn chuyên dụng chuẩn Word A4 Khổ Ngang (Landscape) tương tự trang in học sinh
     */
    public function printSheet(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.view');

        $classes = SchoolClass::all('name ASC');
        $academicYears = AcademicYear::all('id ASC');

        $classId = (int)$request->input('class_id', $classes[0]['id'] ?? 1);
        $selectedClassObj = SchoolClass::find($classId);

        $currentYear = AcademicYear::getCurrent();
        $defaultYearId = $selectedClassObj['academic_year_id'] ?? ($currentYear['id'] ?? 1);

        $academicYearId = null;
        if ($request->input('academic_year_id')) {
            $academicYearId = (int)$request->input('academic_year_id');
        } elseif ($request->input('semester_id')) {
            $sem = Semester::find((int)$request->input('semester_id'));
            if ($sem && !empty($sem['academic_year_id'])) {
                $academicYearId = (int)$sem['academic_year_id'];
            }
        }
        if (!$academicYearId) {
            $academicYearId = (int)$defaultYearId;
        }

        $selectedYearObj = AcademicYear::find($academicYearId);

        // Lấy thông tin lớp học chi tiết kèm tên GVCN
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT c.*, g.name as grade_name, y.name as year_name, t.full_name as homeroom_teacher_name 
                               FROM classes c 
                               LEFT JOIN grade_levels g ON c.grade_id = g.id 
                               LEFT JOIN academic_years y ON c.academic_year_id = y.id 
                               LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id 
                               WHERE c.id = ?");
        $stmt->execute([$classId]);
        $selectedClassDetail = $stmt->fetch() ?: $selectedClassObj;

        // Lấy bảng điểm tất cả các môn
        $gradeData = GradeRecord::getClassAllSubjectsGrades($classId, $academicYearId);
        $students = $gradeData['students'] ?? [];
        $subjects = $gradeData['subjects'] ?? [];
        $stats = $gradeData['stats'] ?? [];

        // Xử lý sắp xếp nếu có yêu cầu
        $sort = $request->input('sort', 'default');
        if ($sort === 'total_desc') {
            usort($students, fn($a, $b) => ($b['total_score'] ?? -1) <=> ($a['total_score'] ?? -1));
        } elseif ($sort === 'total_asc') {
            usort($students, fn($a, $b) => ($a['total_score'] ?? -1) <=> ($b['total_score'] ?? -1));
        } elseif ($sort === 'name_asc' || $sort === 'name_desc') {
            usort($students, function($a, $b) use ($sort) {
                $namePartsA = explode(' ', trim($a['full_name']));
                $namePartsB = explode(' ', trim($b['full_name']));
                $firstA = end($namePartsA);
                $firstB = end($namePartsB);
                $cmp = strcoll($firstA, $firstB);
                if ($cmp === 0) {
                    $cmp = strcoll($a['full_name'], $b['full_name']);
                }
                return $sort === 'name_asc' ? $cmp : -$cmp;
            });
        } elseif ($sort === 'code_asc') {
            usort($students, fn($a, $b) => strcmp($a['student_code'], $b['student_code']));
        }

        View::render('grades/print', [
            'classes' => $classes,
            'academicYears' => $academicYears,
            'classId' => $classId,
            'academicYearId' => $academicYearId,
            'selectedClass' => $selectedClassDetail,
            'selectedYear' => $selectedYearObj,
            'students' => $students,
            'subjects' => $subjects,
            'stats' => $stats,
            'sort' => $sort,
            'autoPrint' => (bool)$request->input('auto', 0)
        ], 'none');
    }

    public function save(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.create');

        $classId = (int)$request->input('class_id');
        $academicYearId = (int)$request->input('academic_year_id');
        if (!$academicYearId && $request->input('semester_id')) {
            $sem = Semester::find((int)$request->input('semester_id'));
            $academicYearId = $sem['academic_year_id'] ?? (int)$request->input('semester_id');
        }

        $isDraft = (bool)$request->input('is_draft', false);
        $entries = $request->input('entries', []);

        if (empty($classId) || empty($academicYearId) || !is_array($entries)) {
            Response::error('Dữ liệu nhập điểm không hợp lệ.');
            return;
        }

        GradeRecord::saveBulkClassAllSubjects($classId, $academicYearId, $entries, $isDraft, Auth::id());
        AuditLogger::log($isDraft ? 'SAVE_DRAFT_GRADES' : 'SAVE_GRADES', 'grades', "c{$classId}_yr{$academicYearId}", null, ['count' => count($entries), 'is_draft' => $isDraft]);

        Response::success(null, $isDraft ? 'Đã lưu bản nháp bảng điểm thành công!' : 'Đã cập nhật bảng điểm chính thức thành công!');
    }

    public function lock(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.lock');

        $classId = (int)$request->input('class_id');
        $academicYearId = (int)$request->input('academic_year_id');
        if (!$academicYearId && $request->input('semester_id')) {
            $sem = Semester::find((int)$request->input('semester_id'));
            $academicYearId = $sem['academic_year_id'] ?? (int)$request->input('semester_id');
        }

        GradeRecord::lockClassGrades($classId, $academicYearId, Auth::id());
        AuditLogger::log('LOCK_GRADES', 'grades', "c{$classId}_yr{$academicYearId}");

        Response::success(null, 'Đã chốt và khóa sổ điểm lớp. Giáo viên không thể chỉnh sửa.');
    }

    public function unlock(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.unlock');

        $classId = (int)$request->input('class_id');
        $academicYearId = (int)$request->input('academic_year_id');
        if (!$academicYearId && $request->input('semester_id')) {
            $sem = Semester::find((int)$request->input('semester_id'));
            $academicYearId = $sem['academic_year_id'] ?? (int)$request->input('semester_id');
        }

        GradeRecord::unlockClassGrades($classId, $academicYearId);
        AuditLogger::log('UNLOCK_GRADES', 'grades', "c{$classId}_yr{$academicYearId}");

        Response::success(null, 'Đã mở khóa sổ điểm thành công để giáo viên tiếp tục cập nhật.');
    }
}
