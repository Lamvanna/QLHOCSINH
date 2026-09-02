<div class="w-full max-w-md">
    <!-- Brand Header -->
    <div class="text-center mb-8">
        <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-600 items-center justify-center text-white shadow-xl shadow-emerald-500/30 mb-4">
            <i data-lucide="graduation-cap" class="w-8 h-8"></i>
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight text-white">EduManage System</h1>
        <p class="text-sm text-slate-400 mt-1">Cổng Đăng Nhập Quản Lý Học Sinh & Nhà Trường</p>
    </div>

    <!-- Login Card -->
    <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-3xl p-8 shadow-2xl shadow-black/50">
        
        <?php if (!empty($error)): ?>
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center">
            <i data-lucide="alert-circle" class="w-5 h-5 mr-3 flex-shrink-0"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login" method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Tên Đăng Nhập</label>
                <div class="relative">
                    <i data-lucide="user" class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="username" id="usernameInput" required placeholder="Nhập username..." 
                           class="w-full pl-11 pr-4 py-3 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Mật Khẩu</label>
                <div class="relative">
                    <i data-lucide="lock" class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="password" name="password" id="passwordInput" required placeholder="Nhập mật khẩu..." 
                           class="w-full pl-11 pr-4 py-3 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-500/25 transition-all transform active:scale-[0.98] flex items-center justify-center space-x-2">
                <span>Đăng Nhập Hệ Thống</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>

        <!-- Quick Demo Account Fillers -->
        <div class="mt-8 pt-6 border-t border-slate-700/60">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider text-center mb-3">Tài Khoản Dùng Thử Nhanh</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="fillDemo('admin', 'admin123')" class="p-2 bg-slate-900/60 hover:bg-slate-700 border border-slate-700 rounded-lg text-center transition-colors">
                    <p class="text-xs font-bold text-emerald-400">Admin</p>
                    <p class="text-[10px] text-slate-400">admin123</p>
                </button>
                <button type="button" onclick="fillDemo('teacher1', 'teacher123')" class="p-2 bg-slate-900/60 hover:bg-slate-700 border border-slate-700 rounded-lg text-center transition-colors">
                    <p class="text-xs font-bold text-sky-400">Giáo Viên</p>
                    <p class="text-[10px] text-slate-400">teacher123</p>
                </button>
                <button type="button" onclick="fillDemo('student1', 'student123')" class="p-2 bg-slate-900/60 hover:bg-slate-700 border border-slate-700 rounded-lg text-center transition-colors">
                    <p class="text-xs font-bold text-purple-400">Học Sinh</p>
                    <p class="text-[10px] text-slate-400">student123</p>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function fillDemo(u, p) {
        document.getElementById('usernameInput').value = u;
        document.getElementById('passwordInput').value = p;
    }
</script>
