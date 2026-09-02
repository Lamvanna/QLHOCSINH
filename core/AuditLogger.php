<?php
// core/AuditLogger.php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Auth.php';

class AuditLogger {
    public static function log(string $action, string $module, ?string $recordId = null, mixed $oldData = null, mixed $newData = null): void {
        try {
            $pdo = Database::getConnection();
            $userId = Auth::id();
            
            $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 250);

            $oldJson = $oldData !== null ? (is_string($oldData) ? $oldData : json_encode($oldData, JSON_UNESCAPED_UNICODE)) : null;
            $newJson = $newData !== null ? (is_string($newData) ? $newData : json_encode($newData, JSON_UNESCAPED_UNICODE)) : null;

            $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, module, record_id, old_data, new_data, ip_address, user_agent, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$userId, $action, $module, $recordId, $oldJson, $newJson, $ip, $userAgent]);
        } catch (Exception $e) {
            // Silently log error to file if audit insert fails so main action isn't interrupted
            error_log("AuditLog Error: " . $e->getMessage());
        }
    }
}
