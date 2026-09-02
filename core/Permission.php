<?php
// core/Permission.php
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Database.php';

class Permission {
    private static array $userPermissions = [];

    public static function can(string $permissionCode): bool {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Super Admin has all permissions
        if ($user['role_name'] === ROLE_SUPER_ADMIN) {
            return true;
        }

        $userId = (int)$user['id'];
        $roleId = (int)$user['role_id'];

        if (!isset(self::$userPermissions[$roleId])) {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT p.code 
                                   FROM permissions p 
                                   JOIN role_permissions rp ON p.id = rp.permission_id 
                                   WHERE rp.role_id = ?");
            $stmt->execute([$roleId]);
            self::$userPermissions[$roleId] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return in_array($permissionCode, self::$userPermissions[$roleId]);
    }

    public static function require(string $permissionCode): void {
        if (!self::can($permissionCode)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                Response::error('Bạn không có quyền thực hiện chức năng này (Mã quyền: ' . $permissionCode . ')', 403);
            } else {
                http_response_code(403);
                die("<div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                        <h2 style='color: #e11d48;'>403 - Truy cập bị từ chối</h2>
                        <p>Tài khoản của bạn không được cấp quyền truy cập tính năng này (<code>{$permissionCode}</code>).</p>
                        <a href='" . BASE_URL . "/dashboard' style='color: #2563eb; text-decoration: underline;'>Quay lại Trang Chủ</a>
                     </div>");
            }
        }
    }
}
