<?php
// models/RemainingModels.php
require_once __DIR__ . '/BaseModel.php';

// models/GradeLevel.php
class GradeLevel extends BaseModel {
    protected static string $table = 'grade_levels';
}

// models/Subject.php
class Subject extends BaseModel {
    protected static string $table = 'subjects';
}

// models/AcademicYear.php
class AcademicYear extends BaseModel {
    protected static string $table = 'academic_years';

    public static function getCurrent(): ?array {
        $pdo = self::getPdo();
        return $pdo->query("SELECT * FROM academic_years WHERE is_current = 1 LIMIT 1")->fetch() ?: null;
    }
}

// models/Semester.php
class Semester extends BaseModel {
    protected static string $table = 'semesters';

    public static function getCurrent(): ?array {
        $pdo = self::getPdo();
        return $pdo->query("SELECT * FROM semesters WHERE is_current = 1 LIMIT 1")->fetch() ?: null;
    }
}

// models/User.php
class User extends BaseModel {
    protected static string $table = 'users';
    protected static bool $softDelete = true;
}

// models/Role.php
class Role extends BaseModel {
    protected static string $table = 'roles';
}

// models/AuditLog.php
class AuditLog extends BaseModel {
    protected static string $table = 'audit_logs';

    public static function getListWithUsers(int $limit = 100): array {
        $pdo = self::getPdo();
        $sql = "SELECT al.*, u.username, u.full_name, r.name as role_name 
                FROM audit_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                LEFT JOIN roles r ON u.role_id = r.id 
                ORDER BY al.created_at DESC 
                LIMIT {$limit}";
        return $pdo->query($sql)->fetchAll();
    }
}

// models/SystemSetting.php
class SystemSetting extends BaseModel {
    protected static string $table = 'system_settings';
    protected static string $primaryKey = 'key_name';

    public static function get(string $key, mixed $default = null): mixed {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT value FROM system_settings WHERE key_name = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false) ? $val : $default;
    }

    public static function set(string $key, mixed $value, ?string $description = null): void {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("INSERT INTO system_settings (key_name, value, description) VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE value = VALUES(value)");
        $stmt->execute([$key, $value, $description]);
    }
}
