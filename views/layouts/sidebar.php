<?php
// views/layouts/sidebar.php
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
function isActive(string $route): string {
    global $currentUri;
    $baseRoute = BASE_URL . $route;
    if ($route === '/dashboard' && ($currentUri === BASE_URL || $currentUri === BASE_URL . '/' || $currentUri === $baseRoute)) {
        return 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border-l-4 border-emerald-600 shadow-sm';
    }
    return str_contains($currentUri, $baseRoute) 
        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border-l-4 border-emerald-600 shadow-sm' 
        : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800/60 dark:hover:text-slate-200 border-l-4 border-transparent hover:border-slate-300 transition-all';
}
?>

<aside class="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col flex-shrink-0 z-30 transition-all duration-200">
    <!-- Brand Logo -->
    <div class="h-16 flex items-center px-6 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
                <i data-lucide="graduation-cap" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-base font-bold tracking-tight text-slate-900 dark:text-white leading-tight">EduManage</h1>
                <p class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400">School ERP System</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menus -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
        
        <!-- DASHBOARD -->
        <div>
            <a href="<?= BASE_URL ?>/dashboard" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?= isActive('/dashboard') ?>">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- QUẢN LÝ ĐÀO TẠO -->
        <?php if (Permission::can('students.view') || Permission::can('teachers.view') || Permission::can('classes.view') || Permission::can('subjects.view')): ?>
        <div>
            <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Quản Lý Đào Tạo</div>
            <div class="space-y-1">
                <?php if (Permission::can('students.view')): ?>
                <a href="<?= BASE_URL ?>/students" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/students') ?>">
                    <i data-lucide="users" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Học sinh</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('teachers.view')): ?>
                <a href="<?= BASE_URL ?>/teachers" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/teachers') ?>">
                    <i data-lucide="user-check" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Giáo viên</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('classes.view')): ?>
                <a href="<?= BASE_URL ?>/classes" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/classes') ?>">
                    <i data-lucide="door-open" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Lớp học</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('subjects.view')): ?>
                <a href="<?= BASE_URL ?>/subjects" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/subjects') ?>">
                    <i data-lucide="book-open" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Môn học</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('academic_years.view')): ?>
                <a href="<?= BASE_URL ?>/academic-years" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/academic-years') ?>">
                    <i data-lucide="calendar" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Năm học</span>
                </a>
                <?php endif; ?>

                <?php /* TẠM KHÓA PHẦN HỌC KỲ (Mở lại khi cần dùng)
<?php if (Permission::can('semesters.view')): ?>
                <a href="<?= BASE_URL ?>/semesters" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/semesters') ?>">
                    <i data-lucide="clock" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Học kỳ</span>
                </a>
                <?php endif; ?>
                */ ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- HỌC TẬP & GIẢNG DẠY -->
        <div>
            <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Học Tập & Giảng Dạy</div>
            <div class="space-y-1">
                <?php if (Permission::can('grades.view')): ?>
                <a href="<?= BASE_URL ?>/grades" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/grades') ?>">
                    <i data-lucide="award" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Điểm số</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('promotions.view')): ?>
                <a href="<?= BASE_URL ?>/promotions" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/promotions') ?>">
                    <i data-lucide="graduation-cap" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Xét lên lớp</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('assignments.view')): ?>
                <a href="<?= BASE_URL ?>/assignments" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/assignments') ?>">
                    <i data-lucide="file-check" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Bài tập</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('schedules.view')): ?>
                <a href="<?= BASE_URL ?>/schedules" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/schedules') ?>">
                    <i data-lucide="calendar-days" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Thời khóa biểu</span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- BÁO CÁO -->
        <?php if (Permission::can('reports.view')): ?>
        <div>
            <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Báo Cáo</div>
            <div class="space-y-1">
                <a href="<?= BASE_URL ?>/reports" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/reports') ?>">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Báo cáo & Thống kê</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- HỆ THỐNG -->
        <?php if (Auth::isAdmin()): ?>
        <div>
            <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Hệ Thống</div>
            <div class="space-y-1">
                <?php if (Permission::can('users.view')): ?>
                <a href="<?= BASE_URL ?>/users" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/users') ?>">
                    <i data-lucide="user-cog" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Người dùng</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('roles.view')): ?>
                <a href="<?= BASE_URL ?>/roles" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/roles') ?>">
                    <i data-lucide="shield-check" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Vai trò & Phân quyền</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('audit_logs.view')): ?>
                <a href="<?= BASE_URL ?>/audit-logs" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/audit-logs') ?>">
                    <i data-lucide="scroll" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Audit Log</span>
                </a>
                <?php endif; ?>

                <?php if (Permission::can('settings.view')): ?>
                <a href="<?= BASE_URL ?>/settings" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/settings') ?>">
                    <i data-lucide="settings" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Cấu hình</span>
                </a>
                <?php endif; ?>

                <?php if (Auth::isSuperAdmin()): ?>
                <a href="<?= BASE_URL ?>/backups" class="flex items-center px-3 py-2 rounded-lg transition-colors <?= isActive('/backups') ?>">
                    <i data-lucide="database" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                    <span>Sao lưu & Phục hồi</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </nav>

    <!-- User Mini Profile Footer -->
    <div class="p-3 border-t border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60">
            <div class="flex items-center space-x-3 overflow-hidden">
                <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150') ?>" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border border-slate-200 dark:border-slate-700" alt="Avatar">
                <div class="truncate">
                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate"><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></p>
                    <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($currentUser['role_display'] ?? '') ?></p>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/logout" title="Đăng xuất" class="text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-700 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</aside>
