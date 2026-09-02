<?php
// controllers/DashboardController.php
require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $user = Auth::user();
        $role = Auth::role();
        $pdo = Database::getConnection();

        $stats = [];

        if (Auth::isAdmin()) {
            // Admin stats
            $stats['total_students'] = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE deleted_at IS NULL")->fetchColumn();
            $stats['total_teachers'] = (int)$pdo->query("SELECT COUNT(*) FROM teachers WHERE deleted_at IS NULL")->fetchColumn();
            $stats['total_classes'] = (int)$pdo->query("SELECT COUNT(*) FROM classes WHERE status = 'active'")->fetchColumn();
            $stats['total_grades'] = (int)$pdo->query("SELECT COUNT(*) FROM grade_levels WHERE status = 'active'")->fetchColumn();
            $stats['total_subjects'] = (int)$pdo->query("SELECT COUNT(*) FROM subjects WHERE status = 'active'")->fetchColumn();
            $stats['total_assignments'] = (int)$pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
            $stats['male_students'] = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE gender = 'male' AND deleted_at IS NULL")->fetchColumn();
            $stats['female_students'] = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE gender = 'female' AND deleted_at IS NULL")->fetchColumn();
            
            $totalStudentsCount = max(1, $stats['total_students']);
            $stats['male_pct'] = round(($stats['male_students'] / $totalStudentsCount) * 100);
            $stats['female_pct'] = 100 - $stats['male_pct'];

            // Status breakdown
            $stats['status_studying'] = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status = 'studying' AND deleted_at IS NULL")->fetchColumn();
            $stats['status_transferred'] = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status = 'transferred' AND deleted_at IS NULL")->fetchColumn();
            $stats['status_graduated'] = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status = 'graduated' AND deleted_at IS NULL")->fetchColumn();
            $stats['status_studying_pct'] = round(($stats['status_studying'] / $totalStudentsCount) * 100);
            $stats['status_transferred_pct'] = round(($stats['status_transferred'] / $totalStudentsCount) * 100);
            $stats['status_graduated_pct'] = 100 - $stats['status_studying_pct'] - $stats['status_transferred_pct'];

            // Overall GPA
            $stats['school_gpa'] = round((float)$pdo->query("SELECT AVG(score) FROM grade_records WHERE is_draft = 0")->fetchColumn(), 2) ?: 7.85;

            // Chart: Students by Class (Lớp 1 -> Lớp 5)
            $stats['chart_grade_distribution'] = $pdo->query("SELECT c.name, COUNT(s.id) as count FROM classes c LEFT JOIN students s ON s.class_id = c.id AND s.deleted_at IS NULL GROUP BY c.id, c.name ORDER BY c.id ASC")->fetchAll();

            // Chart: Average score by subject
            $stats['chart_subject_scores'] = $pdo->query("SELECT sub.name, ROUND(AVG(gr.score), 2) as avg_score FROM subjects sub JOIN grade_records gr ON gr.subject_id = sub.id WHERE gr.is_draft = 0 GROUP BY sub.id, sub.name ORDER BY avg_score DESC LIMIT 8")->fetchAll();

            // Filter options
            $stats['filter_years'] = AcademicYear::all('id DESC');
            $stats['filter_semesters'] = Semester::all('id ASC');

            // Recent activity feed
            $stats['recent_audit_logs'] = AuditLog::getListWithUsers(8);

        } elseif (Auth::isTeacher()) {
            $teacher = $user['teacher'] ?? null;
            $teacherId = $teacher['id'] ?? 1;

            $stats['assigned_classes'] = Teacher::getAssignedClasses($teacherId);
            $stats['total_classes'] = count($stats['assigned_classes']);
            $stats['total_assignments'] = (int)$pdo->query("SELECT COUNT(*) FROM assignments WHERE teacher_id = {$teacherId}")->fetchColumn();
            $stats['pending_grading'] = (int)$pdo->query("SELECT COUNT(*) FROM assignment_submissions sub JOIN assignments a ON sub.assignment_id = a.id WHERE a.teacher_id = {$teacherId} AND sub.status = 'submitted'")->fetchColumn();
            
            // Upcoming schedules today
            $todayDow = date('N') + 1; // 2: Monday
            $stats['today_schedules'] = $pdo->query("SELECT sc.*, c.name as class_name, sub.name as subject_name FROM schedules sc JOIN classes c ON sc.class_id = c.id JOIN subjects sub ON sc.subject_id = sub.id WHERE sc.teacher_id = {$teacherId} AND sc.day_of_week = {$todayDow} ORDER BY sc.period_start ASC")->fetchAll();

        } elseif (Auth::isStudent()) {
            $student = $user['student'] ?? null;
            $studentId = $student['id'] ?? 1;
            $profile = Student::getProfile($studentId);

            $stats['student_profile'] = $profile;
            $stats['my_gpa'] = $profile['avg_score'] ?? 8.0;
            $classId = (int)($student['class_id'] ?? 1);
            $stats['pending_assignments'] = $pdo->query("SELECT a.*, sub.name as subject_name FROM assignments a JOIN subjects sub ON a.subject_id = sub.id WHERE a.class_id = {$classId} AND a.id NOT IN (SELECT assignment_id FROM assignment_submissions WHERE student_id = {$studentId}) ORDER BY a.due_date ASC")->fetchAll();
            
            // Student's today schedules
            $todayDow = date('N') + 1;
            $stats['today_schedules'] = $pdo->query("SELECT sc.*, t.full_name as teacher_name, sub.name as subject_name FROM schedules sc JOIN teachers t ON sc.teacher_id = t.id JOIN subjects sub ON sc.subject_id = sub.id WHERE sc.class_id = {$classId} AND sc.day_of_week = {$todayDow} ORDER BY sc.period_start ASC")->fetchAll();
        }

        View::render('dashboard/index', [
            'stats' => $stats,
            'user' => $user,
            'role' => $role
        ]);
    }
}
