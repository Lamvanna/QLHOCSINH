<?php
// models/SchoolClass.php
require_once __DIR__ . '/BaseModel.php';

class SchoolClass extends BaseModel {
    protected static string $table = 'classes';

    public static function getListWithStats(): array {
        $pdo = self::getPdo();
        $sql = "SELECT c.*, g.name as grade_name, y.name as year_name, t.full_name as homeroom_teacher_name,
                       (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id AND s.deleted_at IS NULL) as student_count,
                       (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id AND s.gender = 'male' AND s.deleted_at IS NULL) as male_count,
                       (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id AND s.gender = 'female' AND s.deleted_at IS NULL) as female_count
                FROM classes c 
                LEFT JOIN grade_levels g ON c.grade_id = g.id 
                LEFT JOIN academic_years y ON c.academic_year_id = y.id 
                LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id 
                ORDER BY c.grade_id ASC, c.name ASC";
        return $pdo->query($sql)->fetchAll();
    }

    public static function getStudents(int $classId): array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT s.* FROM students s WHERE s.class_id = ? AND s.deleted_at IS NULL ORDER BY s.student_code ASC");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }
}
