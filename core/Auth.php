<?php
// core/Auth.php
require_once __DIR__ . '/Database.php';

class Auth {
    private static ?array $user = null;

    public static function init(): void {
        if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    public static function attempt(string $username, string $password): bool {
        self::init();
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("SELECT u.*, r.name as role_name, r.display_name as role_display 
                               FROM users u 
                               JOIN roles r ON u.role_id = r.id 
                               WHERE u.username = ? AND u.deleted_at IS NULL");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                return false;
            }

            // Update last login
            $upd = $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
            $upd->execute([$user['id']]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user;
            self::$user = $user;
            return true;
        }

        return false;
    }

    public static function loginUsingId(int $userId): bool {
        self::init();
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT u.*, r.name as role_name, r.display_name as role_display 
                               FROM users u 
                               JOIN roles r ON u.role_id = r.id 
                               WHERE u.id = ? AND u.deleted_at IS NULL");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user;
            self::$user = $user;
            return true;
        }
        return false;
    }

    public static function check(): bool {
        self::init();
        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array {
        self::init();
        if (self::$user !== null) {
            return self::$user;
        }

        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT u.*, r.name as role_name, r.display_name as role_display 
                               FROM users u 
                               JOIN roles r ON u.role_id = r.id 
                               WHERE u.id = ? AND u.deleted_at IS NULL");
        $stmt->execute([$_SESSION['user_id']]);
        self::$user = $stmt->fetch() ?: null;
        
        // If user is a teacher, attach teacher record
        if (self::$user && self::$user['role_name'] === 'teacher') {
            $tStmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ?");
            $tStmt->execute([self::$user['id']]);
            self::$user['teacher'] = $tStmt->fetch() ?: null;
        }

        // If user is a student, attach student record
        if (self::$user && self::$user['role_name'] === 'student') {
            $sStmt = $pdo->prepare("SELECT s.*, c.name as class_name, c.code as class_code, g.name as grade_name 
                                    FROM students s 
                                    LEFT JOIN classes c ON s.class_id = c.id 
                                    LEFT JOIN grade_levels g ON s.grade_id = g.id 
                                    WHERE s.user_id = ?");
            $sStmt->execute([self::$user['id']]);
            self::$user['student'] = $sStmt->fetch() ?: null;
        }

        return self::$user;
    }

    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): ?string {
        $user = self::user();
        return $user['role_name'] ?? null;
    }

    public static function isSuperAdmin(): bool {
        return self::role() === ROLE_SUPER_ADMIN;
    }

    public static function isAdmin(): bool {
        return in_array(self::role(), [ROLE_SUPER_ADMIN, ROLE_ADMIN]);
    }

    public static function isTeacher(): bool {
        return self::role() === ROLE_TEACHER;
    }

    public static function isStudent(): bool {
        return self::role() === ROLE_STUDENT;
    }

    public static function logout(): void {
        self::init();
        unset($_SESSION['user_id']);
        unset($_SESSION['user']);
        session_destroy();
        self::$user = null;
    }
}
