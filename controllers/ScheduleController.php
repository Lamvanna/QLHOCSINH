<?php
// controllers/ScheduleController.php
require_once __DIR__ . '/BaseController.php';

class ScheduleController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('schedules.view');

        $classes = SchoolClass::all('name ASC');
        $subjects = Subject::all('name ASC');
        $teachers = Teacher::all('full_name ASC');
        $semesters = Semester::all('id ASC');
        $academicYears = AcademicYear::all('id DESC');

        $classId = (int)$request->input('class_id', $classes[0]['id'] ?? 1);
        $semesterId = (int)$request->input('semester_id', $semesters[0]['id'] ?? 1);

        $currentClass = SchoolClass::find($classId);
        $currentSemester = Semester::find($semesterId);

        $timetable = Schedule::getClassTimetable($classId, $semesterId);

        if ($request->isAjax()) {
            Response::json($timetable);
            return;
        }

        View::render('schedules/index', [
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'semesters' => $semesters,
            'academicYears' => $academicYears,
            'currentClass' => $currentClass,
            'currentSemester' => $currentSemester,
            'selectedClass' => $classId,
            'selectedSemester' => $semesterId,
            'timetable' => $timetable
        ]);
    }

    public function printSheet(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('schedules.view');

        $classes = SchoolClass::all('name ASC');
        $semesters = Semester::all('id ASC');

        $classId = (int)$request->input('class_id', $classes[0]['id'] ?? 1);
        $semesterId = (int)$request->input('semester_id', $semesters[0]['id'] ?? 1);

        $currentClass = SchoolClass::find($classId);
        $currentSemester = Semester::find($semesterId);
        $timetable = Schedule::getClassTimetable($classId, $semesterId);

        View::render('schedules/print', [
            'classes' => $classes,
            'semesters' => $semesters,
            'currentClass' => $currentClass,
            'currentSemester' => $currentSemester,
            'selectedClass' => $classId,
            'selectedSemester' => $semesterId,
            'timetable' => $timetable
        ], 'none');
    }

    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('schedules.create');

        $data = $request->all();
        $teacherId = (int)$data['teacher_id'];
        $classId = (int)$data['class_id'];
        $subjectId = (int)$data['subject_id'];
        $dayOfWeek = (int)$data['day_of_week'];
        $periodStart = (int)$data['period_start'];
        $periodEnd = (int)$data['period_end'];
        $semesterId = (int)($data['semester_id'] ?? 1);
        $room = trim($data['room'] ?? '');

        if ($periodStart > $periodEnd) {
            Response::error("Tiết bắt đầu không thể lớn hơn tiết kết thúc.");
            return;
        }

        // Check for clashes
        $clashCheck = Schedule::checkClash($teacherId, $classId, $room, $dayOfWeek, $periodStart, $periodEnd, $semesterId);
        if ($clashCheck['clash']) {
            Response::error("Xung đột thời khóa biểu: " . $clashCheck['message']);
            return;
        }

        $id = Schedule::create([
            'academic_year_id' => 1,
            'semester_id' => $semesterId,
            'class_id' => $classId,
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
            'day_of_week' => $dayOfWeek,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'room' => $room
        ]);

        AuditLogger::log('CREATE_SCHEDULE', 'schedules', (string)$id, null, $data);
        Response::success(['id' => $id], 'Thêm tiết học vào thời khóa biểu thành công!');
    }

    public function destroy(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('schedules.delete');

        $id = (int)($params['id'] ?? 0);
        Schedule::delete($id);
        AuditLogger::log('DELETE_SCHEDULE', 'schedules', (string)$id);
        Response::success(null, 'Đã xóa tiết học khỏi thời khóa biểu.');
    }
}
