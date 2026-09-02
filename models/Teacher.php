<?php
// models/Teacher.php
require_once __DIR__ . '/BaseModel.php';

class Teacher extends BaseModel {
    protected static string $table = 'teachers';
    protected static bool $softDelete = true;

    public static function getListWithDetails(): array {
        $pdo = self::getPdo();
        $sql = "SELECT t.*, u.username, 
                       (SELECT GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') FROM classes c WHERE c.homeroom_teacher_id = t.id) as homeroom_classes,
                       (SELECT COUNT(DISTINCT ts.class_id) FROM teacher_subjects ts WHERE ts.teacher_id = t.id) as teaching_classes_count
                FROM teachers t 
                LEFT JOIN users u ON t.user_id = u.id 
                WHERE t.deleted_at IS NULL 
                ORDER BY t.id ASC";
        return $pdo->query($sql)->fetchAll();
    }

    public static function getAssignedClasses(int $teacherId): array {
        $pdo = self::getPdo();
        $sql = "SELECT DISTINCT c.*, g.name as grade_name, s.name as subject_name, s.id as subject_id 
                FROM teacher_subjects ts 
                JOIN classes c ON ts.class_id = c.id 
                JOIN grade_levels g ON c.grade_id = g.id 
                JOIN subjects s ON ts.subject_id = s.id 
                WHERE ts.teacher_id = ? 
                ORDER BY c.name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }
}
