<?php
// models/GradeRecord.php
require_once __DIR__ . '/BaseModel.php';

class GradeRecord extends BaseModel {
    protected static string $table = 'grade_records';

    public static function getClassSubjectGrades(int $classId, int $subjectId, int $semesterId): array {
        $pdo = self::getPdo();

        // Get all students in class
        $sStmt = $pdo->prepare("SELECT s.id, s.student_code, s.full_name, s.gender FROM students s WHERE s.class_id = ? AND s.deleted_at IS NULL ORDER BY s.student_code ASC");
        $sStmt->execute([$classId]);
        $students = $sStmt->fetchAll();

        // Get grade components
        $components = $pdo->query("SELECT * FROM grade_components ORDER BY id ASC")->fetchAll();

        // Get existing grades
        $gStmt = $pdo->prepare("SELECT * FROM grade_records WHERE class_id = ? AND subject_id = ? AND semester_id = ?");
        $gStmt->execute([$classId, $subjectId, $semesterId]);
        $gradeEntries = $gStmt->fetchAll();

        // Build lookup map: [student_id][component_id] => record
        $gradeMap = [];
        foreach ($gradeEntries as $ge) {
            $gradeMap[$ge['student_id']][$ge['component_id']] = $ge;
        }

        // Build composite student score rows
        $result = [];
        foreach ($students as $student) {
            $stId = $student['id'];
            $row = [
                'student_id' => $stId,
                'student_code' => $student['student_code'],
                'full_name' => $student['full_name'],
                'gender' => $student['gender'],
                'scores' => [],
                'is_locked' => false,
                'is_draft' => false,
                'average' => null
            ];

            $totalWeightedScore = 0;
            $totalWeight = 0;

            foreach ($components as $comp) {
                $compId = $comp['id'];
                $weight = (float)$comp['weight'];
                $rec = $gradeMap[$stId][$compId] ?? null;

                $scoreVal = $rec ? (float)$rec['score'] : null;
                $row['scores'][$compId] = [
                    'id' => $rec['id'] ?? null,
                    'score' => $scoreVal,
                    'is_locked' => (bool)($rec['is_locked'] ?? false),
                    'is_draft' => (bool)($rec['is_draft'] ?? false)
                ];

                if ($rec && $rec['is_locked']) {
                    $row['is_locked'] = true;
                }
                if ($rec && $rec['is_draft']) {
                    $row['is_draft'] = true;
                }

                if ($scoreVal !== null) {
                    $totalWeightedScore += ($scoreVal * $weight);
                    $totalWeight += $weight;
                }
            }

            $row['average'] = $totalWeight > 0 ? round($totalWeightedScore / $totalWeight, 2) : null;
            $result[] = $row;
        }

        return [
            'students' => $result,
            'components' => $components
        ];
    }

    public static function saveBulk(int $classId, int $subjectId, int $semesterId, array $entries, bool $isDraft, ?int $userId): bool {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("INSERT INTO grade_records (student_id, subject_id, class_id, semester_id, component_id, score, is_draft, is_locked, updated_by, updated_at)
                               VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, NOW())
                               ON DUPLICATE KEY UPDATE 
                               score = VALUES(score),
                               is_draft = VALUES(is_draft),
                               updated_by = VALUES(updated_by),
                               updated_at = NOW()");

        foreach ($entries as $entry) {
            $studentId = (int)$entry['student_id'];
            $compId = (int)$entry['component_id'];
            $score = (float)$entry['score'];

            if ($score >= 0 && $score <= 10) {
                $stmt->execute([$studentId, $subjectId, $classId, $semesterId, $compId, $score, $isDraft ? 1 : 0, $userId]);
            }
        }
        return true;
    }

    public static function lockGrades(int $classId, int $subjectId, int $semesterId, int $userId): bool {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE grade_records 
                               SET is_locked = 1, is_draft = 0, locked_by = ?, locked_at = NOW() 
                               WHERE class_id = ? AND subject_id = ? AND semester_id = ?");
        return $stmt->execute([$userId, $classId, $subjectId, $semesterId]);
    }

    public static function unlockGrades(int $classId, int $subjectId, int $semesterId): bool {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE grade_records 
                               SET is_locked = 0, locked_by = NULL, locked_at = NULL 
                               WHERE class_id = ? AND subject_id = ? AND semester_id = ?");
        return $stmt->execute([$classId, $subjectId, $semesterId]);
    }
}
