<?php
// controllers/AuthController.php
require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {
    public function showLogin(Request $request): void {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }
        View::render('auth/login', [], 'auth');
    }

    public function login(Request $request): void {
        $username = trim($request->input('username', ''));
        $password = trim($request->input('password', ''));

        if (empty($username) || empty($password)) {
            if ($request->isAjax()) {
                Response::error('Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.');
            }
            View::render('auth/login', ['error' => 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.'], 'auth');
            return;
        }

        if (Auth::attempt($username, $password)) {
            AuditLogger::log('LOGIN', 'auth', (string)Auth::id(), null, ['username' => $username, 'status' => 'success']);
            
            if ($request->isAjax()) {
                Response::success(['redirect' => BASE_URL . '/dashboard'], 'Đăng nhập thành công!');
            }
            Response::redirect('/dashboard');
        } else {
            AuditLogger::log('LOGIN_FAILED', 'auth', null, null, ['username' => $username]);
            
            if ($request->isAjax()) {
                Response::error('Tên đăng nhập hoặc mật khẩu không chính xác.');
            }
            View::render('auth/login', ['error' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'], 'auth');
        }
    }

    public function switchRole(Request $request): void {
        $this->requireAuth();
        $targetRole = $request->input('role');
        
        $pdo = Database::getConnection();
        if ($targetRole === 'admin') {
            Auth::loginUsingId(1); // Super Admin
        } elseif ($targetRole === 'teacher') {
            Auth::loginUsingId(3); // Teacher 1
        } elseif ($targetRole === 'student') {
            Auth::loginUsingId(23); // Student 1
        }

        Response::redirect('/dashboard');
    }

    public function logout(): void {
        if (Auth::check()) {
            AuditLogger::log('LOGOUT', 'auth', (string)Auth::id());
            Auth::logout();
        }
        Response::redirect('/login');
    }

    public function showProfile(): void {
        $this->requireAuth();
        $user = Auth::user();
        View::render('auth/profile', ['user' => $user]);
    }

    public function updateProfile(Request $request): void {
        $this->requireAuth();
        $userId = Auth::id();
        $fullName = trim($request->input('full_name', ''));
        $email = trim($request->input('email', ''));
        $phone = trim($request->input('phone', ''));
        $newPass = trim($request->input('new_password', ''));

        $updateData = [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone
        ];

        if (!empty($newPass)) {
            if (strlen($newPass) < 6) {
                Response::error('Mật khẩu mới phải có tối thiểu 6 ký tự.');
            }
            $updateData['password_hash'] = password_hash($newPass, PASSWORD_DEFAULT);
        }

        User::update($userId, $updateData);
        AuditLogger::log('UPDATE_PROFILE', 'users', (string)$userId, null, $updateData);

        Response::success(null, 'Cập nhật thông tin cá nhân thành công!');
    }
}
