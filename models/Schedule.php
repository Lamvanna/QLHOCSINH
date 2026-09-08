<?php
// models/Schedule.php
require_once __DIR__ . '/BaseModel.php';

class Schedule extends BaseModel {
    protected static string $table = 'schedules';

    public static function getClassTimetable(int $classId, int $semesterId): array {
        $pdo = self::getPdo();
        $sql = "SELECT sc.*, sub.name as subject_name, sub.code as subject_code, t.full_name as teacher_name 
                FROM schedules sc 
                JOIN subjects sub ON sc.subject_id = sub.id 
                JOIN teachers t ON sc.teacher_id = t.id 
                WHERE sc.class_id = ? AND sc.semester_id = ? 
                ORDER BY sc.day_of_week ASC, sc.period_start ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$classId, $semesterId]);
        return $stmt->fetchAll();
    }

    public static function checkClash(int $teacherId, int $classId, string $room, int $day, int $periodStart, int $periodEnd, int $semesterId, ?int $excludeId = null): array {
        $pdo = self::getPdo();
        $excludeSql = $excludeId ? "AND id != {$excludeId}" : "";

        $dayName = match($day) {
            2 => 'Thứ Hai',
            3 => 'Thứ Ba',
            4 => 'Thứ Tư',
            5 => 'Thứ Năm',
            6 => 'Thứ Sáu',
            7 => 'Thứ Bảy',
            default => "Thứ {$day}"
        };

        // 1. Check Teacher Clash
        $tSql = "SELECT sc.*, c.name as class_name, sub.name as subject_name 
                 FROM schedules sc 
                 JOIN classes c ON sc.class_id = c.id 
                 JOIN subjects sub ON sc.subject_id = sub.id 
                 WHERE sc.teacher_id = ? AND sc.day_of_week = ? AND sc.semester_id = ? 
                   AND ((sc.period_start <= ? AND sc.period_end >= ?) OR (sc.period_start <= ? AND sc.period_end >= ?)) {$excludeSql}";
        $stmt = $pdo->prepare($tSql);
        $stmt->execute([$teacherId, $day, $semesterId, $periodStart, $periodStart, $periodEnd, $periodEnd]);
        $teacherClash = $stmt->fetch();
        if ($teacherClash) {
            return ['clash' => true, 'message' => "Giáo viên đã có tiết dạy tại lớp '{$teacherClash['class_name']}' môn '{$teacherClash['subject_name']}' vào {$dayName}, Tiết {$teacherClash['period_start']}–{$teacherClash['period_end']}."];
        }

        // 2. Check Class Clash
        $cSql = "SELECT sc.*, sub.name as subject_name, t.full_name as teacher_name 
                 FROM schedules sc 
                 JOIN subjects sub ON sc.subject_id = sub.id 
                 JOIN teachers t ON sc.teacher_id = t.id 
                 WHERE sc.class_id = ? AND sc.day_of_week = ? AND sc.semester_id = ? 
                   AND ((sc.period_start <= ? AND sc.period_end >= ?) OR (sc.period_start <= ? AND sc.period_end >= ?)) {$excludeSql}";
        $stmt = $pdo->prepare($cSql);
        $stmt->execute([$classId, $day, $semesterId, $periodStart, $periodStart, $periodEnd, $periodEnd]);
        $classClash = $stmt->fetch();
        if ($classClash) {
            return ['clash' => true, 'message' => "Lớp học đã có tiết '{$classClash['subject_name']}' (GV: {$classClash['teacher_name']}) vào {$dayName}, Tiết {$classClash['period_start']}–{$classClash['period_end']}."];
        }

        // 3. Check Room Clash (if room specified)
        if (!empty($room)) {
            $rSql = "SELECT sc.*, c.name as class_name 
                     FROM schedules sc 
                     JOIN classes c ON sc.class_id = c.id 
                     WHERE sc.room = ? AND sc.day_of_week = ? AND sc.semester_id = ? 
                       AND ((sc.period_start <= ? AND sc.period_end >= ?) OR (sc.period_start <= ? AND sc.period_end >= ?)) {$excludeSql}";
            $stmt = $pdo->prepare($rSql);
            $stmt->execute([$room, $day, $semesterId, $periodStart, $periodStart, $periodEnd, $periodEnd]);
            $roomClash = $stmt->fetch();
            if ($roomClash) {
                return ['clash' => true, 'message' => "Phòng học '{$room}' đã được xếp cho lớp '{$roomClash['class_name']}' vào {$dayName}, Tiết {$roomClash['period_start']}–{$roomClash['period_end']}."];
            }
        }

        return ['clash' => false];
    }
}
