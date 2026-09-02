<?php
// models/Assignment.php
require_once __DIR__ . '/BaseModel.php';

class Assignment extends BaseModel {
    protected static string $table = 'assignments';

    public static function getListWithRelations(array $filters = []): array {
        $pdo = self::getPdo();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['teacher_id'])) {
            $where[] = "a.teacher_id = ?";
            $params[] = (int)$filters['teacher_id'];
        }
        if (!empty($filters['class_id'])) {
            $where[] = "a.class_id = ?";
            $params[] = (int)$filters['class_id'];
        }
        if (!empty($filters['subject_id'])) {
            $where[] = "a.subject_id = ?";
            $params[] = (int)$filters['subject_id'];
        }

        $whereSql = implode(' AND ', $where);
        $sql = "SELECT a.*, c.name as class_name, sub.name as subject_name, t.full_name as teacher_name,
                       (SELECT COUNT(*) FROM assignment_submissions sub WHERE sub.assignment_id = a.id) as submission_count,
                       (SELECT COUNT(*) FROM assignment_submissions sub WHERE sub.assignment_id = a.id AND sub.status = 'graded') as graded_count,
                       (SELECT COUNT(*) FROM students s WHERE s.class_id = a.class_id AND s.deleted_at IS NULL) as total_students
                FROM assignments a 
                JOIN classes c ON a.class_id = c.id 
                JOIN subjects sub ON a.subject_id = sub.id 
                JOIN teachers t ON a.teacher_id = t.id 
                WHERE {$whereSql} 
                ORDER BY a.due_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getSubmissions(int $assignmentId): array {
        $pdo = self::getPdo();
        $sql = "SELECT sub.*, s.student_code, s.full_name as student_name, s.avatar as student_avatar, s.gender,
                       c.name as class_name 
                FROM assignment_submissions sub 
                JOIN students s ON sub.student_id = s.id 
                JOIN classes c ON s.class_id = c.id 
                WHERE sub.assignment_id = ? 
                ORDER BY sub.submitted_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$assignmentId]);
        return $stmt->fetchAll();
    }
}
