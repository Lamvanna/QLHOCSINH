<?php
// core/View.php
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Database.php';

class View {
    public static function render(string $viewPath, array $data = [], string $layout = 'main'): void {
        extract($data);
        
        // Load current user and app settings for all views
        $currentUser = Auth::user();
        
        $pdo = Database::getConnection();
        $settingsStmt = $pdo->query("SELECT key_name, value FROM system_settings");
        $appSettings = [];
        while ($row = $settingsStmt->fetch()) {
            $appSettings[$row['key_name']] = $row['value'];
        }

        $viewFile = __DIR__ . "/../views/{$viewPath}.php";
        
        if (!file_exists($viewFile)) {
            die("View not found: {$viewPath}");
        }

        // Start buffer for view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === 'none') {
            echo $content;
            return;
        }

        $layoutFile = __DIR__ . "/../views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }
}
