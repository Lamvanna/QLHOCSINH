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
                       (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id AND s.gender = 'female' AND s.deleted_at IS NULL) as female_count,
                       (SELECT COUNT(*) FROM class_subjects cs WHERE cs.class_id = c.id) as subject_count,
                       (SELECT GROUP_CONCAT(s2.name ORDER BY s2.name SEPARATOR ', ') FROM class_subjects cs2 JOIN subjects s2 ON cs2.subject_id = s2.id WHERE cs2.class_id = c.id) as subject_names,
                       (SELECT GROUP_CONCAT(cs3.subject_id SEPARATOR ',') FROM class_subjects cs3 WHERE cs3.class_id = c.id) as subject_ids
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

    public static function getSubjects(int $classId): array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT s.* FROM subjects s 
                               INNER JOIN class_subjects cs ON s.id = cs.subject_id 
                               WHERE cs.class_id = ? 
                               ORDER BY s.name ASC");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    public static function getSubjectIds(int $classId): array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT subject_id FROM class_subjects WHERE class_id = ?");
        $stmt->execute([$classId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function syncSubjects(int $classId, array $subjectIds): void {
        $pdo = self::getPdo();
        $pdo->prepare("DELETE FROM class_subjects WHERE class_id = ?")->execute([$classId]);
        if (!empty($subjectIds)) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO class_subjects (class_id, subject_id) VALUES (?, ?)");
            foreach ($subjectIds as $sId) {
                $sId = (int)$sId;
                if ($sId > 0) {
                    $stmt->execute([$classId, $sId]);
                }
            }
        }
    }
}
