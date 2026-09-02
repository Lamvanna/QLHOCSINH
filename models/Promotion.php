<?php
// models/Promotion.php
require_once __DIR__ . '/BaseModel.php';

class Promotion extends BaseModel {
    protected static string $table = 'student_promotions';

    /**
     * Get class promotion evaluations for 5 classes (Lớp 1 -> Lớp 5)
     */
    public static function getClassPromotions(int $classId, int $academicYearId): array {
        $pdo = self::getPdo();

        // 1. Get current class details
        $classStmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $classStmt->execute([$classId]);
        $class = $classStmt->fetch();
        if (!$class) return [];

        // 2. Get students in this class
        $studentsStmt = $pdo->prepare("SELECT s.*, 
                                              (SELECT ROUND(AVG(score), 2) FROM grade_records WHERE student_id = s.id AND is_draft = 0) as avg_score,
                                              (SELECT MIN(score) FROM grade_records WHERE student_id = s.id AND is_draft = 0) as min_score,
                                              p.id as promotion_id, p.promotion_status, p.conduct, p.target_class_id, p.notes, p.approved_at,
                                              tc.name as target_class_name
                                       FROM students s
                                       LEFT JOIN student_promotions p ON p.student_id = s.id AND p.academic_year_id = ?
                                       LEFT JOIN classes tc ON p.target_class_id = tc.id
                                       WHERE s.class_id = ? AND s.deleted_at IS NULL
                                       ORDER BY s.student_code ASC");
        $studentsStmt->execute([$academicYearId, $classId]);
        $students = $studentsStmt->fetchAll();

        // 3. Find next class in 5-class sequence (Lớp 1 -> Lớp 2 -> Lớp 3 -> Lớp 4 -> Lớp 5 -> Tốt nghiệp)
        $nextClassId = $classId < 5 ? ($classId + 1) : null;
        $nextClass = null;
        if ($nextClassId) {
            $ncStmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
            $ncStmt->execute([$nextClassId]);
            $nextClass = $ncStmt->fetch() ?: null;
        }

        // Potential target classes
        $allClasses = $pdo->query("SELECT * FROM classes WHERE status = 'active' ORDER BY id ASC")->fetchAll();

        // 4. Auto-calculate recommendation
        foreach ($students as &$st) {
            $avg = (float)($st['avg_score'] ?? 0);
            $minScore = (float)($st['min_score'] ?? 0);
            $conduct = $st['conduct'] ?? 'Tot';

            if ($classId >= 5) {
                // Class 5 (Lớp 5) -> Graduation
                $recommendedStatus = ($avg >= 5.0 && in_array($conduct, ['Tot', 'Kha'])) ? 'graduated' : ($avg >= 3.5 ? 'remedial' : 'retained');
            } else {
                if ($avg >= 5.0 && $minScore >= 3.5 && in_array($conduct, ['Tot', 'Kha', 'TrungBinh'])) {
                    $recommendedStatus = 'promoted';
                } elseif ($avg >= 3.5) {
                    $recommendedStatus = 'remedial';
                } else {
                    $recommendedStatus = 'retained';
                }
            }

            $st['recommended_status'] = $recommendedStatus;
            if (empty($st['promotion_status'])) {
                $st['promotion_status'] = $recommendedStatus;
                $st['conduct'] = $conduct;
                $st['target_class_id'] = ($recommendedStatus === 'promoted' && $nextClass) ? $nextClass['id'] : $classId;
            }
        }

        return [
            'class' => $class,
            'next_class' => $nextClass,
            'target_classes' => $allClasses,
            'students' => $students
        ];
    }

    /**
     * Save/update decisions for a list of students
     */
    public static function saveDecisions(int $academicYearId, int $classId, array $records, int $userId): void {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("INSERT INTO student_promotions 
                               (student_id, academic_year_id, current_class_id, current_grade_id, avg_score, conduct, promotion_status, target_class_id, target_grade_id, notes, approved_by, approved_at)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                               ON DUPLICATE KEY UPDATE
                               conduct = VALUES(conduct),
                               promotion_status = VALUES(promotion_status),
                               target_class_id = VALUES(target_class_id),
                               target_grade_id = VALUES(target_grade_id),
                               notes = VALUES(notes),
                               approved_by = VALUES(approved_by),
                               approved_at = NOW()");

        foreach ($records as $r) {
            $studentId = (int)$r['student_id'];
            $avgScore  = isset($r['avg_score']) && is_numeric($r['avg_score']) ? (float)$r['avg_score'] : null;
            $conduct   = $r['conduct'] ?? 'Tot';
            $status    = $r['promotion_status'] ?? 'promoted';
            $targetClassId = !empty($r['target_class_id']) ? (int)$r['target_class_id'] : null;
            $notes     = $r['notes'] ?? null;

            $stmt->execute([
                $studentId,
                $academicYearId,
                $classId,
                $classId,
                $avgScore,
                $conduct,
                $status,
                $targetClassId,
                $targetClassId,
                $notes,
                $userId
            ]);
        }
    }

    /**
     * Execute batch promotion: Moves students to new classes or sets status to graduated
     */
    public static function executePromotions(int $classId, int $academicYearId, int $nextYearId, int $userId): int {
        $pdo = self::getPdo();
        
        $promotions = $pdo->prepare("SELECT * FROM student_promotions WHERE current_class_id = ? AND academic_year_id = ?");
        $promotions->execute([$classId, $academicYearId]);
        $rows = $promotions->fetchAll();

        $count = 0;
        foreach ($rows as $row) {
            $studentId = $row['student_id'];
            $status = $row['promotion_status'];

            if ($status === 'promoted' && !empty($row['target_class_id'])) {
                $up = $pdo->prepare("UPDATE students SET class_id = ?, grade_id = ?, academic_year_id = ?, status = 'studying' WHERE id = ?");
                $up->execute([$row['target_class_id'], $row['target_class_id'], $nextYearId, $studentId]);
                $count++;
            } elseif ($status === 'graduated') {
                $up = $pdo->prepare("UPDATE students SET status = 'graduated' WHERE id = ?");
                $up->execute([$studentId]);
                $count++;
            } elseif ($status === 'retained') {
                $up = $pdo->prepare("UPDATE students SET academic_year_id = ?, status = 'studying' WHERE id = ?");
                $up->execute([$nextYearId, $studentId]);
                $count++;
            }
        }

        return $count;
    }
}
