<?php
// models/GradeRecord.php
require_once __DIR__ . '/BaseModel.php';

class GradeRecord extends BaseModel {
    protected static string $table = 'grade_records';

    /**
     * Lấy bảng điểm tất cả các môn của một lớp theo Năm học (hoặc Học kỳ)
     * Mỗi môn học chỉ có một điểm thi duy nhất.
     */
    public static function getClassAllSubjectsGrades(int $classId, int $academicYearId): array {
        $pdo = self::getPdo();

        // 1. Danh sách học sinh trong lớp
        $sStmt = $pdo->prepare("SELECT s.id, s.student_code, s.full_name, s.gender, s.dob 
                               FROM students s 
                               WHERE s.class_id = ? AND s.deleted_at IS NULL 
                               ORDER BY s.student_code ASC");
        $sStmt->execute([$classId]);
        $students = $sStmt->fetchAll();

        // 2. Danh sách môn học được phân công cho lớp
        $subjects = SchoolClass::getSubjects($classId);
        if (empty($subjects)) {
            $subjects = Subject::all('name ASC');
        }

        // 3. Tìm các semester_id thuộc năm học này (bao gồm cả trường hợp truyền trực tiếp semester_id)
        $semStmt = $pdo->prepare("SELECT id FROM semesters WHERE academic_year_id = ?");
        $semStmt->execute([$academicYearId]);
        $semIds = $semStmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($semIds)) {
            $semIds = [$academicYearId];
        } else {
            $semIds[] = $academicYearId;
        }
        $semIds = array_unique(array_filter(array_map('intval', $semIds)));
        $inPlaceholders = implode(',', array_fill(0, count($semIds), '?'));

        // Lấy điểm của các môn học trong năm học
        $queryParams = array_merge([$classId], $semIds);
        $gStmt = $pdo->prepare("SELECT student_id, subject_id, 
                                       COALESCE(MAX(CASE WHEN component_id = 1 THEN score END), MAX(score)) as score,
                                       MAX(is_locked) as is_locked,
                                       MAX(is_draft) as is_draft
                                FROM grade_records 
                                WHERE class_id = ? AND semester_id IN ($inPlaceholders)
                                GROUP BY student_id, subject_id");
        $gStmt->execute($queryParams);
        $rawGrades = $gStmt->fetchAll();

        // Bản đồ điểm: [student_id][subject_id] => score
        $gradeMap = [];
        $lockedMap = [];
        $draftMap = [];
        foreach ($rawGrades as $rg) {
            $stId = (int)$rg['student_id'];
            $subId = (int)$rg['subject_id'];
            $gradeMap[$stId][$subId] = ($rg['score'] !== null) ? (float)$rg['score'] : null;
            if (!empty($rg['is_locked'])) {
                $lockedMap[$stId] = true;
            }
            if (!empty($rg['is_draft'])) {
                $draftMap[$stId] = true;
            }
        }

        // 4. Tổ chức dữ liệu từng học sinh với tổng điểm
        $result = [];
        $highestTotal = 0;
        $totalCells = count($students) * count($subjects);
        $filledCells = 0;

        foreach ($students as $st) {
            $stId = (int)$st['id'];
            $scores = [];
            $totalScore = 0;
            $hasAnyScore = false;

            foreach ($subjects as $sub) {
                $subId = (int)$sub['id'];
                $val = $gradeMap[$stId][$subId] ?? null;
                $scores[$subId] = $val;
                if ($val !== null && $val !== '') {
                    $totalScore += (float)$val;
                    $hasAnyScore = true;
                    $filledCells++;
                }
            }

            $totalScoreFormatted = $hasAnyScore ? round($totalScore, 2) : null;
            if ($totalScoreFormatted !== null && $totalScoreFormatted > $highestTotal) {
                $highestTotal = $totalScoreFormatted;
            }

            $result[] = [
                'student_id' => $stId,
                'student_code' => $st['student_code'],
                'full_name' => $st['full_name'],
                'gender' => $st['gender'],
                'dob' => $st['dob'],
                'dob_formatted' => !empty($st['dob']) ? date('d/m/Y', strtotime($st['dob'])) : '—',
                'gender_vn' => ($st['gender'] === 'female') ? 'Nữ' : 'Nam',
                'scores' => $scores,
                'total_score' => $totalScoreFormatted,
                'is_locked' => !empty($lockedMap[$stId]),
                'is_draft' => !empty($draftMap[$stId])
            ];
        }

        return [
            'students' => $result,
            'subjects' => $subjects,
            'stats' => [
                'student_count' => count($students),
                'subject_count' => count($subjects),
                'max_possible_total' => count($subjects) * 10,
                'highest_total' => $highestTotal,
                'total_cells' => $totalCells,
                'filled_cells' => $filledCells,
                'completion_rate' => $totalCells > 0 ? round(($filledCells / $totalCells) * 100, 1) : 0
            ]
        ];
    }

    /**
     * Lưu điểm hàng loạt cho tất cả các môn của một lớp theo Năm học
     */
    public static function saveBulkClassAllSubjects(int $classId, int $academicYearId, array $entries, bool $isDraft, ?int $userId): bool {
        $pdo = self::getPdo();

        // Tìm semester đại diện cho academic_year_id (ưu tiên semester đã có điểm hoặc HK2/CN)
        $semStmt = $pdo->prepare("SELECT id FROM semesters WHERE academic_year_id = ? ORDER BY (id = 2) DESC, is_current DESC, id DESC LIMIT 1");
        $semStmt->execute([$academicYearId]);
        $targetSemesterId = (int)$semStmt->fetchColumn();
        if (!$targetSemesterId) {
            $targetSemesterId = $academicYearId;
        }

        $stmt = $pdo->prepare("INSERT INTO grade_records (student_id, subject_id, class_id, semester_id, component_id, score, is_draft, is_locked, updated_by, updated_at)
                               VALUES (?, ?, ?, ?, 1, ?, ?, 0, ?, NOW())
                               ON DUPLICATE KEY UPDATE 
                               score = VALUES(score),
                               is_draft = VALUES(is_draft),
                               updated_by = VALUES(updated_by),
                               updated_at = NOW()");

        // Khi xóa điểm, xóa ở tất cả semester của năm học
        $semIdsStmt = $pdo->prepare("SELECT id FROM semesters WHERE academic_year_id = ?");
        $semIdsStmt->execute([$academicYearId]);
        $allSemIds = $semIdsStmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($allSemIds)) { $allSemIds = [$targetSemesterId]; }
        $allSemIds = array_unique(array_filter(array_map('intval', $allSemIds)));
        $semIn = implode(',', array_fill(0, count($allSemIds), '?'));
        $delStmt = $pdo->prepare("DELETE FROM grade_records WHERE student_id = ? AND subject_id = ? AND class_id = ? AND semester_id IN ($semIn)");

        foreach ($entries as $entry) {
            $studentId = (int)($entry['student_id'] ?? 0);
            $subjectId = (int)($entry['subject_id'] ?? 0);
            $rawScore = $entry['score'] ?? null;

            if ($studentId <= 0 || $subjectId <= 0) continue;

            if ($rawScore === '' || $rawScore === null) {
                // Xóa điểm nếu người dùng xóa trắng ô điểm
                $delParams = array_merge([$studentId, $subjectId, $classId], $allSemIds);
                $delStmt->execute($delParams);
            } else {
                $score = (float)$rawScore;
                if ($score >= 0 && $score <= 10) {
                    $stmt->execute([$studentId, $subjectId, $classId, $targetSemesterId, $score, $isDraft ? 1 : 0, $userId]);
                }
            }
        }
        return true;
    }

    public static function lockClassGrades(int $classId, int $academicYearId, int $userId): bool {
        $pdo = self::getPdo();
        $semStmt = $pdo->prepare("SELECT id FROM semesters WHERE academic_year_id = ?");
        $semStmt->execute([$academicYearId]);
        $semIds = $semStmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($semIds)) { $semIds = [$academicYearId]; }
        $inClause = implode(',', array_fill(0, count($semIds), '?'));

        $stmt = $pdo->prepare("UPDATE grade_records 
                               SET is_locked = 1, is_draft = 0, locked_by = ?, locked_at = NOW() 
                               WHERE class_id = ? AND semester_id IN ($inClause)");
        return $stmt->execute(array_merge([$userId, $classId], $semIds));
    }

    public static function unlockClassGrades(int $classId, int $academicYearId): bool {
        $pdo = self::getPdo();
        $semStmt = $pdo->prepare("SELECT id FROM semesters WHERE academic_year_id = ?");
        $semStmt->execute([$academicYearId]);
        $semIds = $semStmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($semIds)) { $semIds = [$academicYearId]; }
        $inClause = implode(',', array_fill(0, count($semIds), '?'));

        $stmt = $pdo->prepare("UPDATE grade_records 
                               SET is_locked = 0, locked_by = NULL, locked_at = NULL 
                               WHERE class_id = ? AND semester_id IN ($inClause)");
        return $stmt->execute(array_merge([$classId], $semIds));
    }

    // Giữ lại các phương thức cũ để tương thích ngược nếu có module khác gọi
    public static function getClassSubjectGrades(int $classId, int $subjectId, int $semesterId): array {
        return self::getClassAllSubjectsGrades($classId, $semesterId);
    }

    public static function saveBulk(int $classId, int $subjectId, int $semesterId, array $entries, bool $isDraft, ?int $userId): bool {
        return self::saveBulkClassAllSubjects($classId, $semesterId, $entries, $isDraft, $userId);
    }

    public static function lockGrades(int $classId, int $subjectId, int $semesterId, int $userId): bool {
        return self::lockClassGrades($classId, $semesterId, $userId);
    }

    public static function unlockGrades(int $classId, int $subjectId, int $semesterId): bool {
        return self::unlockClassGrades($classId, $semesterId);
    }
}