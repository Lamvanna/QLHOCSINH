<?php
// middleware/AuthMiddleware.php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Response.php';

class AuthMiddleware {
    public function handle(Request $request): void {
        if (!Auth::check()) {
            if ($request->isAjax()) {
                Response::error('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 401);
            } else {
                Response::redirect('/login');
            }
        }
    }
}
