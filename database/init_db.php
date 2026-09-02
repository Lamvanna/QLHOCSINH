<?php
// database/init_db.php
require_once __DIR__ . '/../core/Database.php';

echo "=== INITIALIZING EDUMANAGE DATABASE ===\n";

try {
    $pdo = Database::getConnection();
    
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Execute schema statements
    $pdo->exec($sql);
    echo "✔ Schema tables created successfully!\n";

    // Insert Default System Settings
    $settings = [
        ['school_name', 'Trường Trung Học Phổ Thông Quốc Tế EduManage', 'Tên trường học'],
        ['school_code', 'EDUMANAGE_VN', 'Mã trường học'],
        ['school_email', 'contact@edumanage.edu.vn', 'Email liên hệ trường'],
        ['school_phone', '028 3829 8888', 'Số điện thoại trường'],
        ['school_address', 'Số 123 Đường Sư Vạn Hạnh, Quận 10, TP. Hồ Chí Minh', 'Địa chỉ trường'],
        ['default_language', 'vi', 'Ngôn ngữ mặc định (vi, en, km)'],
        ['default_theme', 'light', 'Giao diện mặc định (light, dark)'],
        ['timezone', 'Asia/Ho_Chi_Minh', 'Múi giờ hệ thống'],
        ['max_upload_size_mb', '25', 'Dung lượng upload tối đa (MB)'],
        ['allow_teacher_unlock_grade', '0', 'Cho phép GV tự mở khóa điểm (0/1)'],
        ['prevent_homeroom_clash', '1', 'Không cho GVCN phụ trách 2 lớp (0/1)'],
    ];

    $stmt = $pdo->prepare("INSERT INTO system_settings (key_name, value, description) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
    foreach ($settings as $s) {
        $stmt->execute($s);
    }
    echo "✔ Default system settings populated!\n";

    // Insert Roles
    $roles = [
        [1, 'super_admin', 'Super Admin (Toàn quyền)', 'Quản trị viên cấp cao nhất', 1],
        [2, 'admin', 'Ban Giám Hiệu / Quản Trị Viên', 'Quản lý học vụ, giáo viên, học sinh, báo cáo', 1],
        [3, 'teacher', 'Giáo Viên', 'Quản lý điểm, điểm danh, bài tập, lớp phụ trách', 1],
        [4, 'student', 'Học Sinh', 'Xem điểm, thời khóa biểu, nộp bài tập, tài liệu', 1],
    ];
    $stmt = $pdo->prepare("INSERT INTO roles (id, name, display_name, description, is_system) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE display_name = VALUES(display_name)");
    foreach ($roles as $r) {
        $stmt->execute($r);
    }
    echo "✔ Roles populated!\n";

    // Insert Permissions across all modules
    $modules = [
        'dashboard' => ['view'],
        'students' => ['view', 'create', 'update', 'delete', 'restore', 'import', 'export'],
        'teachers' => ['view', 'create', 'update', 'delete', 'import', 'export'],
        'grades_level' => ['view', 'create', 'update', 'delete'],
        'classes' => ['view', 'create', 'update', 'delete', 'assign_students', 'export'],
        'subjects' => ['view', 'create', 'update', 'delete'],
        'academic_years' => ['view', 'create', 'update', 'delete', 'manage'],
        'semesters' => ['view', 'create', 'update', 'delete'],
        'grades' => ['view', 'create', 'update', 'delete', 'approve', 'lock', 'unlock', 'import', 'export'],
        'comments' => ['view', 'create', 'update', 'delete'],
        'attendance' => ['view', 'create', 'update', 'delete', 'export'],
        'schedules' => ['view', 'create', 'update', 'delete', 'export'],
        'exams' => ['view', 'create', 'update', 'delete', 'input_scores', 'export'],
        'assignments' => ['view', 'create', 'update', 'delete', 'submit', 'grade'],
        'documents' => ['view', 'create', 'update', 'delete', 'download'],
        'messages' => ['view', 'send', 'delete'],
        'rewards' => ['view', 'create', 'update', 'delete', 'export'],
        'reports' => ['view', 'export'],
        'users' => ['view', 'create', 'update', 'delete', 'lock', 'reset_password'],
        'roles' => ['view', 'create', 'update', 'delete', 'assign'],
        'audit_logs' => ['view', 'export'],
        'settings' => ['view', 'update'],
        'backups' => ['view', 'create', 'restore', 'delete', 'download']
    ];

    $pdo->exec("TRUNCATE TABLE role_permissions");
    $pdo->exec("DELETE FROM permissions");

    $permInsert = $pdo->prepare("INSERT INTO permissions (code, module, action, description) VALUES (?, ?, ?, ?)");
    $rolePermInsert = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");

    $permId = 1;
    foreach ($modules as $mod => $actions) {
        foreach ($actions as $act) {
            $code = "{$mod}.{$act}";
            $desc = ucfirst($act) . " for module " . ucfirst(str_replace('_', ' ', $mod));
            $permInsert->execute([$code, $mod, $act, $desc]);
            $currentPermId = $pdo->lastInsertId();

            // Assign to Super Admin (All permissions)
            $rolePermInsert->execute([1, $currentPermId]);

            // Assign to Admin (Most permissions except user system core/backup if restricted)
            if (!in_array($mod, ['backups']) || in_array($act, ['view', 'export'])) {
                $rolePermInsert->execute([2, $currentPermId]);
            }

            // Assign to Teacher
            if (
                in_array($mod, ['dashboard', 'messages']) ||
                ($mod === 'students' && in_array($act, ['view', 'export'])) ||
                ($mod === 'teachers' && in_array($act, ['view'])) ||
                ($mod === 'classes' && in_array($act, ['view', 'export'])) ||
                ($mod === 'subjects' && in_array($act, ['view'])) ||
                ($mod === 'academic_years' && in_array($act, ['view'])) ||
                ($mod === 'semesters' && in_array($act, ['view'])) ||
                ($mod === 'grades' && in_array($act, ['view', 'create', 'update', 'import', 'export'])) ||
                ($mod === 'comments' && in_array($act, ['view', 'create', 'update', 'delete'])) ||
                ($mod === 'attendance' && in_array($act, ['view', 'create', 'update', 'export'])) ||
                ($mod === 'schedules' && in_array($act, ['view', 'export'])) ||
                ($mod === 'exams' && in_array($act, ['view', 'input_scores', 'export'])) ||
                ($mod === 'assignments' && in_array($act, ['view', 'create', 'update', 'delete', 'grade'])) ||
                ($mod === 'documents' && in_array($act, ['view', 'create', 'update', 'delete', 'download'])) ||
                ($mod === 'rewards' && in_array($act, ['view', 'export'])) ||
                ($mod === 'reports' && in_array($act, ['view', 'export']))
            ) {
                $rolePermInsert->execute([3, $currentPermId]);
            }

            // Assign to Student
            if (
                in_array($mod, ['dashboard', 'messages']) ||
                ($mod === 'students' && in_array($act, ['view'])) ||
                ($mod === 'classes' && in_array($act, ['view'])) ||
                ($mod === 'grades' && in_array($act, ['view'])) ||
                ($mod === 'comments' && in_array($act, ['view'])) ||
                ($mod === 'attendance' && in_array($act, ['view'])) ||
                ($mod === 'schedules' && in_array($act, ['view'])) ||
                ($mod === 'exams' && in_array($act, ['view'])) ||
                ($mod === 'assignments' && in_array($act, ['view', 'submit'])) ||
                ($mod === 'documents' && in_array($act, ['view', 'download'])) ||
                ($mod === 'rewards' && in_array($act, ['view']))
            ) {
                $rolePermInsert->execute([4, $currentPermId]);
            }
        }
    }
    echo "✔ Permissions matrix generated and linked to roles!\n";

    // Grade components
    $components = [
        ['TX1', 'Điểm Thường Xuyên 1 (Miệng/15p)', 0.10, 'Hệ số 1 - Bài kiểm tra thường xuyên lần 1'],
        ['TX2', 'Điểm Thường Xuyên 2 (15p/Thực hành)', 0.10, 'Hệ số 1 - Bài kiểm tra thường xuyên lần 2'],
        ['TX3', 'Điểm Thường Xuyên 3 (Bài tập/Dự án)', 0.10, 'Hệ số 1 - Điểm đánh giá quá trình'],
        ['GK', 'Điểm Giữa Kỳ (1 Tiết / Giữa kỳ)', 0.30, 'Hệ số 2 - Đánh giá giữa học kỳ'],
        ['CK', 'Điểm Cuối Kỳ (Học kỳ)', 0.40, 'Hệ số 3 - Đánh giá cuối học kỳ'],
    ];
    $stmt = $pdo->prepare("INSERT INTO grade_components (code, name, weight, description) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE weight = VALUES(weight)");
    foreach ($components as $c) {
        $stmt->execute($c);
    }
    echo "✔ Grade components initialized!\n";

    // Comment templates
    $templates = [
        ['learning', 'Học sinh tiếp thu bài rất nhanh, có tinh thần tự giác cao.'],
        ['learning', 'Cần chú ý làm bài tập về nhà đầy đủ hơn và tích cực phát biểu.'],
        ['learning', 'Nắm vững kiến thức trọng tâm, kỹ năng giải quyết bài tập rất tốt.'],
        ['learning', 'Tiến bộ rõ rệt trong thời gian gần đây, tiếp tục phát huy.'],
        ['behavior', 'Chấp hành tốt nội quy trường lớp, lễ phép với thầy cô giáo.'],
        ['behavior', 'Hòa đồng, thân thiện và luôn sẵn sàng hỗ trợ các bạn cùng lớp.'],
        ['behavior', 'Đôi lúc còn mất tập trung trong giờ học, cần cải thiện.'],
        ['training', 'Tích cực tham gia các hoạt động ngoại khóa và phong trào của trường.'],
        ['midterm', 'Kết quả học tập giữa kỳ đạt loại Tốt, tư duy logic rất tốt.'],
        ['final', 'Hoàn thành xuất sắc chương trình học kỳ, xứng đáng tuyên dương.']
    ];
    $stmt = $pdo->prepare("INSERT INTO comment_templates (category, template_text) VALUES (?, ?)");
    foreach ($templates as $t) {
        $stmt->execute($t);
    }
    echo "✔ Comment templates library seeded!\n";

    echo "=== DATABASE INITIALIZATION COMPLETED SUCCESSFULLY! ===\n";

} catch (Exception $e) {
    echo "❌ Error during DB initialization: " . $e->getMessage() . "\n";
    exit(1);
}
