<?php
// controllers/GradeController.php
require_once __DIR__ . '/BaseController.php';

class GradeController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.view');

        $classes = SchoolClass::all('name ASC');
        $subjects = Subject::all('name ASC');
        $semesters = Semester::all('id ASC');

        $classId = (int)$request->input('class_id', $classes[0]['id'] ?? 1);
        $subjectId = (int)$request->input('subject_id', $subjects[0]['id'] ?? 1);
        $semesterId = (int)$request->input('semester_id', 2); // Default HK2

        $gradeData = GradeRecord::getClassSubjectGrades($classId, $subjectId, $semesterId);

        if ($request->isAjax()) {
            Response::json($gradeData);
            return;
        }

        View::render('grades/index', [
            'classes' => $classes,
            'subjects' => $subjects,
            'semesters' => $semesters,
            'selectedClass' => $classId,
            'selectedSubject' => $subjectId,
            'selectedSemester' => $semesterId,
            'gradeData' => $gradeData
        ]);
    }

    public function save(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.create');

        $classId = (int)$request->input('class_id');
        $subjectId = (int)$request->input('subject_id');
        $semesterId = (int)$request->input('semester_id');
        $isDraft = (bool)$request->input('is_draft', false);
        $entries = $request->input('entries', []);

        if (empty($classId) || empty($subjectId) || empty($semesterId) || empty($entries)) {
            Response::error('Dữ liệu nhập điểm không hợp lệ.');
            return;
        }

        GradeRecord::saveBulk($classId, $subjectId, $semesterId, $entries, $isDraft, Auth::id());
        AuditLogger::log($isDraft ? 'SAVE_DRAFT_GRADES' : 'SAVE_GRADES', 'grades', "c{$classId}_s{$subjectId}_sem{$semesterId}", null, ['count' => count($entries), 'is_draft' => $isDraft]);

        Response::success(null, $isDraft ? 'Đã lưu bản nháp điểm thành công!' : 'Đã cập nhật bảng điểm thành công!');
    }

    public function lock(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.lock');

        $classId = (int)$request->input('class_id');
        $subjectId = (int)$request->input('subject_id');
        $semesterId = (int)$request->input('semester_id');

        GradeRecord::lockGrades($classId, $subjectId, $semesterId, Auth::id());
        AuditLogger::log('LOCK_GRADES', 'grades', "c{$classId}_s{$subjectId}_sem{$semesterId}");

        Response::success(null, 'Đã chốt và khóa bảng điểm. Giáo viên không thể chỉnh sửa.');
    }

    public function unlock(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('grades.unlock');

        $classId = (int)$request->input('class_id');
        $subjectId = (int)$request->input('subject_id');
        $semesterId = (int)$request->input('semester_id');

        GradeRecord::unlockGrades($classId, $subjectId, $semesterId);
        AuditLogger::log('UNLOCK_GRADES', 'grades', "c{$classId}_s{$subjectId}_sem{$semesterId}");

        Response::success(null, 'Đã mở khóa bảng điểm để giáo viên điều chỉnh.');
    }
}
