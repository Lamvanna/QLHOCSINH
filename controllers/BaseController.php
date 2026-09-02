<?php
// controllers/BaseController.php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Permission.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/AuditLogger.php';
require_once __DIR__ . '/../core/Validator.php';

// Include Active Models
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../models/SchoolClass.php';
require_once __DIR__ . '/../models/GradeRecord.php';
require_once __DIR__ . '/../models/Schedule.php';
require_once __DIR__ . '/../models/Assignment.php';
require_once __DIR__ . '/../models/Promotion.php';
require_once __DIR__ . '/../models/RemainingModels.php';

abstract class BaseController {
    protected function requireAuth(): void {
        if (!Auth::check()) {
            Response::redirect('/login');
        }
    }

    protected function requirePermission(string $code): void {
        Permission::require($code);
    }
}
