<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../core/Database.php';

$pdo = Database::getConnection();

$sql = "CREATE TABLE IF NOT EXISTS `student_promotions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `academic_year_id` INT NOT NULL,
    `current_class_id` INT NOT NULL,
    `current_grade_id` INT NOT NULL,
    `avg_score` DECIMAL(4,2) NULL,
    `conduct` VARCHAR(50) DEFAULT 'Tot',
    `promotion_status` ENUM('promoted', 'retained', 'remedial', 'graduated', 'pending') DEFAULT 'pending',
    `target_class_id` INT NULL,
    `target_grade_id` INT NULL,
    `notes` TEXT NULL,
    `approved_by` INT NULL,
    `approved_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_student_year` (`student_id`, `academic_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$pdo->exec($sql);

// Add permissions if not exists
$permissions = [
    ['code' => 'promotions.view', 'module' => 'promotions', 'action' => 'view', 'description' => 'Xem danh sách xét lên lớp'],
    ['code' => 'promotions.evaluate', 'module' => 'promotions', 'action' => 'evaluate', 'description' => 'Đánh giá và xét duyệt lên lớp'],
    ['code' => 'promotions.execute', 'module' => 'promotions', 'action' => 'execute', 'description' => 'Thực hiện chuyển lớp hàng loạt']
];

foreach ($permissions as $p) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO `permissions` (`code`, `module`, `action`, `description`) VALUES (?, ?, ?, ?)");
    $stmt->execute([$p['code'], $p['module'], $p['action'], $p['description']]);
    
    // Assign to Super Admin (role 1) and Admin (role 2)
    $permId = $pdo->query("SELECT id FROM permissions WHERE code = '{$p['code']}'")->fetchColumn();
    if ($permId) {
        $pdo->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (1, {$permId})");
        $pdo->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (2, {$permId})");
        // Also allow Teacher (role 3) to view and evaluate
        if ($p['code'] !== 'promotions.execute') {
            $pdo->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (3, {$permId})");
        }
    }
}

echo "Promotion table and permissions initialized successfully!\n";
