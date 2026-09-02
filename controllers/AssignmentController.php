<?php
// controllers/AssignmentController.php
require_once __DIR__ . '/BaseController.php';

class AssignmentController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('assignments.view');

        $user = Auth::user();
        $filters = [];
        if (Auth::isTeacher() && !empty($user['teacher'])) {
            $filters['teacher_id'] = $user['teacher']['id'];
        } elseif (Auth::isStudent() && !empty($user['student'])) {
            $filters['class_id'] = $user['student']['class_id'];
        }

        $assignments = Assignment::getListWithRelations($filters);
        $classes = SchoolClass::all('name ASC');
        $subjects = Subject::all('name ASC');

        if ($request->isAjax()) {
            Response::json($assignments);
            return;
        }

        View::render('assignments/index', [
            'assignments' => $assignments,
            'classes' => $classes,
            'subjects' => $subjects
        ]);
    }

    public function show(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('assignments.view');

        $id = (int)($params['id'] ?? 0);
        $assignment = Assignment::find($id);
        if (!$assignment) {
            Response::redirect('/assignments');
            return;
        }

        $submissions = Assignment::getSubmissions($id);

        View::render('assignments/show', [
            'assignment' => $assignment,
            'submissions' => $submissions
        ]);
    }

    public function store(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('assignments.create');

        $data = $request->all();
        $validator = Validator::make($data, [
            'title' => 'required',
            'class_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'due_date' => 'required'
        ]);

        if ($validator->fails()) {
            Response::error($validator->firstError());
            return;
        }

        $user = Auth::user();
        $teacherId = $user['teacher']['id'] ?? 1;

        $id = Assignment::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'teacher_id' => $teacherId,
            'subject_id' => $data['subject_id'],
            'class_id' => $data['class_id'],
            'due_date' => $data['due_date'],
            'max_score' => $data['max_score'] ?? 10.0,
            'attachment_url' => $data['attachment_url'] ?? null,
            'status' => 'published'
        ]);

        AuditLogger::log('CREATE_ASSIGNMENT', 'assignments', (string)$id, null, $data);
        Response::success(['id' => $id], 'Giao bài tập mới thành công!');
    }

    public function submit(Request $request, array $params): void {
        $this->requireAuth();
        $this->requirePermission('assignments.submit');

        $assignmentId = (int)($params['id'] ?? 0);
        $user = Auth::user();
        $studentId = $user['student']['id'] ?? 1;

        $content = trim($request->input('content', ''));
        $attachmentUrl = $request->input('attachment_url', null);

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, submitted_at, content, attachment_url, status)
                               VALUES (?, ?, NOW(), ?, ?, 'submitted')
                               ON DUPLICATE KEY UPDATE 
                               submitted_at = NOW(),
                               content = VALUES(content),
                               attachment_url = VALUES(attachment_url),
                               status = 'submitted'");
        $stmt->execute([$assignmentId, $studentId, $content, $attachmentUrl]);

        AuditLogger::log('SUBMIT_ASSIGNMENT', 'assignments', (string)$assignmentId, null, ['student_id' => $studentId]);
        Response::success(null, 'Nộp bài tập thành công!');
    }

    public function grade(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('assignments.grade');

        $submissionId = (int)$request->input('submission_id');
        $score = (float)$request->input('score');
        $feedback = trim($request->input('feedback', ''));

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE assignment_submissions 
                               SET score = ?, feedback = ?, graded_by = ?, graded_at = NOW(), status = 'graded' 
                               WHERE id = ?");
        $stmt->execute([$score, $feedback, Auth::id(), $submissionId]);

        AuditLogger::log('GRADE_ASSIGNMENT', 'assignments', (string)$submissionId, null, ['score' => $score]);
        Response::success(null, 'Chấm điểm và gửi nhận xét thành công!');
    }
}
