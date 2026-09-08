<?php
// models/Promotion.php
require_once __DIR__ . '/BaseModel.php';

class Promotion extends BaseModel {
    protected static string $table = 'student_promotions';

    /**
     * Get students and calculated promotion data for a given class and academic year
     */
    public static function getClassPromotions(int $classId, int $academicYearId): array {
        $pdo = self::getPdo();

        // 1. Get class info
        $classStmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $classStmt->execute([$classId]);
        $class = $classStmt->fetch();

        // 2. Resolve next class
        $nextClass = self::findNextClass($class);

        // 3. Get all active classes for target selection dropdown
        $allClassesStmt = $pdo->query("SELECT id, name, grade_id FROM classes ORDER BY grade_id ASC, name ASC");
        $allClasses = $allClassesStmt->fetchAll();

        // 4. Fetch students in this class with their isolated average score for this specific class & year
        $query = "
            SELECT 
                s.id,
                s.student_code,
                s.full_name,
                s.khmer_name,
                s.gender,
                s.dob,
                s.dob as date_of_birth,
                s.status,
                sp.id as promotion_id,
                sp.avg_score as saved_avg_score,
                sp.conduct as saved_conduct,
                sp.promotion_status,
                sp.target_class_id,
                sp.notes,
                (
                    SELECT ROUND(AVG(gr.score), 2)
                    FROM grade_records gr
                    JOIN semesters sem ON gr.semester_id = sem.id
                    WHERE gr.student_id = s.id 
                      AND gr.class_id = ?
                      AND sem.academic_year_id = ?
                      AND gr.score IS NOT NULL
                      AND gr.score > 0
                ) as calc_avg_score,
                (
                    SELECT ROUND(AVG(gr.score), 2)
                    FROM grade_records gr
                    WHERE gr.student_id = s.id 
                      AND gr.class_id = ?
                      AND gr.score IS NOT NULL
                      AND gr.score > 0
                ) as fallback_avg_score
            FROM students s
            LEFT JOIN student_promotions sp 
                ON s.id = sp.student_id 
                AND sp.academic_year_id = ? 
                AND sp.current_class_id = ?
            WHERE s.class_id = ? AND s.status = 'studying' AND s.deleted_at IS NULL
            ORDER BY s.student_code ASC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$classId, $academicYearId, $classId, $academicYearId, $classId, $classId]);
        $students = $stmt->fetchAll();

        // 5. Compute recommendation status for each student (No "remedial")
        foreach ($students as &$st) {
            $avg = $st['calc_avg_score'] ?? $st['fallback_avg_score'] ?? $st['saved_avg_score'] ?? null;
            $st['avg_score'] = $avg !== null ? (float)$avg : null;

            $conduct = $st['saved_conduct'] ?? 'Tot';
            $st['conduct'] = $conduct;

            // Determine if final class (Class 5)
            $currentNum = (int)preg_replace('/[^0-9]/', '', $class['name'] ?? '');
            $isFinalClass = ($currentNum >= 5 || $classId >= 5);

            // Clean recommendation logic without remedial
            if ($isFinalClass) {
                if ($avg !== null && $avg >= 5.0 && in_array($conduct, ['Tot', 'Kha', 'TrungBinh'])) {
                    $recommendedStatus = 'graduated';
                } else {
                    $recommendedStatus = 'retained';
                }
            } else {
                if ($avg !== null && $avg >= 5.0 && in_array($conduct, ['Tot', 'Kha', 'TrungBinh'])) {
                    $recommendedStatus = 'promoted';
                } else {
                    $recommendedStatus = 'retained';
                }
            }

            $st['recommended_status'] = $recommendedStatus;

            // If existing status is 'remedial', map to 'retained' or 'promoted'
            if (!empty($st['promotion_status']) && $st['promotion_status'] === 'remedial') {
                $st['promotion_status'] = $recommendedStatus;
            }

            if (empty($st['promotion_status'])) {
                $st['promotion_status'] = $recommendedStatus;
                if ($recommendedStatus === 'promoted' && $nextClass) {
                    $st['target_class_id'] = $nextClass['id'];
                } elseif ($recommendedStatus === 'graduated') {
                    $st['target_class_id'] = null;
                } else {
                    $st['target_class_id'] = $classId;
                }
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
                avg_score = VALUES(avg_score),
                conduct = VALUES(conduct),
                promotion_status = VALUES(promotion_status),
                target_class_id = VALUES(target_class_id),
                target_grade_id = VALUES(target_grade_id),
                notes = VALUES(notes),
                approved_by = VALUES(approved_by),
                approved_at = NOW()
        ");

        $class = SchoolClass::find($classId);
        $currentGradeId = $class['grade_id'] ?? $classId;

        // Cache classes for target_grade_id lookup
        $classesStmt = $pdo->query("SELECT id, grade_id FROM classes");
        $classesMap = [];
        while ($c = $classesStmt->fetch()) {
            $classesMap[$c['id']] = $c['grade_id'];
        }

        foreach ($records as $r) {
            $stId = (int)$r['student_id'];
            $avg = !empty($r['avg_score']) ? (float)$r['avg_score'] : null;
            $conduct = $r['conduct'] ?? 'Tot';
            $status = $r['promotion_status'] ?? 'promoted';
            if ($status === 'remedial') $status = 'retained'; // No remedial

            $targetClassId = !empty($r['target_class_id']) ? (int)$r['target_class_id'] : null;
            $targetGradeId = $targetClassId ? ($classesMap[$targetClassId] ?? $targetClassId) : null;
            $notes = $r['notes'] ?? null;

            $stmt->execute([
                $stId,
                $academicYearId,
                $classId,
                $currentGradeId,
                $avg,
                $conduct,
                $status,
                $targetClassId,
                $targetGradeId,
                $notes,
                $userId
            ]);
        }
    }

    /**
     * Execute promotions batch
     */
    public static function executePromotions(int $classId, int $academicYearId, int $nextYearId, int $userId): int {
        $pdo = self::getPdo();
        
        $promotions = $pdo->prepare("SELECT sp.*, tc.grade_id as target_grade_id_resolved 
                                    FROM student_promotions sp
                                    LEFT JOIN classes tc ON sp.target_class_id = tc.id
                                    WHERE sp.current_class_id = ? AND sp.academic_year_id = ?");
        $promotions->execute([$classId, $academicYearId]);
        $rows = $promotions->fetchAll();

        $count = 0;
        foreach ($rows as $row) {
            $studentId = $row['student_id'];
            $status = $row['promotion_status'];

            if ($status === 'promoted' && !empty($row['target_class_id'])) {
                $targetClassId = (int)$row['target_class_id'];
                $targetGradeId = (int)($row['target_grade_id_resolved'] ?? $targetClassId);
                $up = $pdo->prepare("UPDATE students SET class_id = ?, grade_id = ?, academic_year_id = ?, status = 'studying' WHERE id = ?");
                $up->execute([$targetClassId, $targetGradeId, $nextYearId, $studentId]);
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

    /**
     * Determine next class based on class name or grade_id
     */
    private static function findNextClass(?array $class): ?array {
        if (!$class) return null;
        $pdo = self::getPdo();

        $className = $class['name'] ?? '';
        if (preg_match('/(?:lớp|khối)?\s*([1-9][0-9]*)\s*([a-zA-Z]*)/iu', $className, $matches)) {
            $gradeNum = (int)$matches[1];
            $section = trim($matches[2]);

            if ($gradeNum >= 5) {
                return null; // Class 5 is graduating
            }

            $nextGradeNum = $gradeNum + 1;
            $patterns = [
                "Lớp " . $nextGradeNum . $section,
                "Lớp " . $nextGradeNum . " " . $section,
                "Lớp " . $nextGradeNum,
                "Khối " . $nextGradeNum . $section,
                $nextGradeNum . $section
            ];

            foreach ($patterns as $pattern) {
                $pattern = trim($pattern);
                $stmt = $pdo->prepare("SELECT * FROM classes WHERE name = ? LIMIT 1");
                $stmt->execute([$pattern]);
                $res = $stmt->fetch();
                if ($res) return $res;
            }

            $stmt = $pdo->prepare("SELECT * FROM classes WHERE name LIKE ? ORDER BY name ASC LIMIT 1");
            $stmt->execute(["%{$nextGradeNum}%"]);
            $res = $stmt->fetch();
            if ($res) return $res;
        }

        $nextGradeId = ((int)($class['grade_id'] ?? 1)) + 1;
        if ($nextGradeId > 5) return null;

        $stmt = $pdo->prepare("SELECT * FROM classes WHERE grade_id = ? ORDER BY name ASC LIMIT 1");
        $stmt->execute([$nextGradeId]);
        return $stmt->fetch() ?: null;
    }
}
