<?php
// middleware/RoleMiddleware.php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Response.php';

class RoleMiddleware {
    private array $allowedRoles;

    public function __construct(array|string $roles = []) {
        $this->allowedRoles = is_array($roles) ? $roles : [$roles];
    }

    public function handle(Request $request): void {
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }

        $currentRole = Auth::role();
        if (!in_array($currentRole, $this->allowedRoles) && !Auth::isSuperAdmin()) {
            if ($request->isAjax()) {
                Response::error('Bạn không có vai trò phù hợp để truy cập tính năng này.', 403);
            } else {
                http_response_code(403);
                die("<div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                        <h2 style='color: #e11d48;'>403 - Vai trò không hợp lệ</h2>
                        <p>Tài khoản của bạn không được phân công vai trò này.</p>
                        <a href='" . BASE_URL . "/dashboard' style='color: #2563eb; text-decoration: underline;'>Quay lại Trang Chủ</a>
                     </div>");
            }
        }
    }
}
