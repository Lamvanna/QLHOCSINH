<?php
// models/Student.php
require_once __DIR__ . '/BaseModel.php';

class Student extends BaseModel {
    protected static string $table = 'students';
    protected static bool $softDelete = true;

    public static function getPaginated(int $page = 1, int $limit = 15, array $filters = []): array {
        $pdo = self::getPdo();
        $offset = ($page - 1) * $limit;
        
        $where = ["s.deleted_at IS NULL"];
        $params = [];

        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $where[] = "(s.student_code LIKE ? OR s.full_name LIKE ? OR s.phone LIKE ? OR s.email LIKE ? OR s.khmer_name LIKE ?)";
            $params = array_merge($params, [$s, $s, $s, $s, $s]);
        }

        if (!empty($filters['class_id'])) {
            $where[] = "s.class_id = ?";
            $params[] = (int)$filters['class_id'];
        }

        if (!empty($filters['grade_id'])) {
            $where[] = "s.grade_id = ?";
            $params[] = (int)$filters['grade_id'];
        }

        if (!empty($filters['academic_year_id'])) {
            $where[] = "s.academic_year_id = ?";
            $params[] = (int)$filters['academic_year_id'];
        }

        if (!empty($filters['gender'])) {
            $where[] = "s.gender = ?";
            $params[] = $filters['gender'];
        }

        if (!empty($filters['status'])) {
            $where[] = "s.status = ?";
            $params[] = $filters['status'];
        }

        $whereSql = implode(' AND ', $where);

        // Count total
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM students s WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Sort order: Default is Class (grade_id ASC, class_name ASC) then Date of Birth (dob ASC), then Full Name (full_name ASC)
        if (!empty($filters['sort_by']) && in_array($filters['sort_by'], ['s.student_code', 's.full_name', 's.dob', 'c.name', 's.status'])) {
            $sortBy = $filters['sort_by'];
            $sortOrder = strtoupper($filters['sort_order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
            $orderSql = "{$sortBy} {$sortOrder}";
        } else {
            $orderSql = "c.grade_id ASC, c.name ASC, s.dob ASC, s.full_name ASC";
        }

        $sql = "SELECT s.*, c.name as class_name, c.code as class_code, g.name as grade_name, y.name as year_name 
                FROM students s 
                LEFT JOIN classes c ON s.class_id = c.id 
                LEFT JOIN grade_levels g ON s.grade_id = g.id 
                LEFT JOIN academic_years y ON s.academic_year_id = y.id 
                WHERE {$whereSql} 
                ORDER BY {$orderSql} 
                LIMIT {$limit} OFFSET {$offset}";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        return [
            'data' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }

    public static function getProfile(int $id): ?array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT s.*, c.name as class_name, c.code as class_code, c.room_number,
                                      g.name as grade_name, y.name as year_name, t.full_name as homeroom_teacher_name,
                                      u.username, u.last_login_at
                               FROM students s 
                               LEFT JOIN classes c ON s.class_id = c.id 
                               LEFT JOIN grade_levels g ON s.grade_id = g.id 
                               LEFT JOIN academic_years y ON s.academic_year_id = y.id 
                               LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id
                               LEFT JOIN users u ON s.user_id = u.id
                               WHERE s.id = ? AND s.deleted_at IS NULL");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
        if (!$student) return null;

        // Fetch student's grades
        $gradeStmt = $pdo->prepare("SELECT gr.*, sub.name as subject_name, sub.coefficient, gc.name as component_name, gc.code as component_code 
                                    FROM grade_records gr 
                                    JOIN subjects sub ON gr.subject_id = sub.id 
                                    JOIN grade_components gc ON gr.component_id = gc.id 
                                    WHERE gr.student_id = ? 
                                    ORDER BY sub.id, gc.id");
        $gradeStmt->execute([$id]);
        $student['grades'] = $gradeStmt->fetchAll();

        // Calculate average GPA
        $gpaStmt = $pdo->prepare("SELECT AVG(score) as avg_score FROM grade_records WHERE student_id = ? AND is_draft = 0");
        $gpaStmt->execute([$id]);
        $student['avg_score'] = round((float)$gpaStmt->fetchColumn(), 2);

        // Fetch assignments for student's class with submission status
        $asStmt = $pdo->prepare("SELECT a.*, sub.name as subject_name, t.full_name as teacher_name, 
                                        subm.id as submission_id, subm.status as submission_status, subm.score as submission_score, subm.feedback as submission_feedback, subm.submitted_at
                                 FROM assignments a
                                 JOIN subjects sub ON a.subject_id = sub.id
                                 JOIN teachers t ON a.teacher_id = t.id
                                 LEFT JOIN assignment_submissions subm ON subm.assignment_id = a.id AND subm.student_id = ?
                                 WHERE a.class_id = ?
                                 ORDER BY a.due_date DESC");
        $asStmt->execute([$id, $student['class_id']]);
        $student['assignments'] = $asStmt->fetchAll();

        // Fetch class timetable
        $schStmt = $pdo->prepare("SELECT sc.*, sub.name as subject_name, t.full_name as teacher_name 
                                  FROM schedules sc 
                                  JOIN subjects sub ON sc.subject_id = sub.id 
                                  JOIN teachers t ON sc.teacher_id = t.id 
                                  WHERE sc.class_id = ? 
                                  ORDER BY sc.day_of_week ASC, sc.period_start ASC");
        $schStmt->execute([$student['class_id']]);
        $student['schedules'] = $schStmt->fetchAll();

        return $student;
    }
}
