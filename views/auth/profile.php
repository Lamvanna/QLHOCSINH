<div class="space-y-6 max-w-xl">
    <div>
        <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Thông Tin Tài Khoản</h2>
        <p class="text-xs text-slate-500 mt-1">Cập nhật họ tên, thông tin liên hệ và thay đổi mật khẩu đăng nhập</p>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <form onsubmit="handleUpdateProfile(event)" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold mb-1">Tên Đăng Nhập</label>
                <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 border rounded-xl font-mono text-slate-400">
            </div>
            <div>
                <label class="block font-bold mb-1">Vai Trò</label>
                <input type="text" value="<?= htmlspecialchars($user['role_display']) ?>" disabled class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 border rounded-xl font-bold text-emerald-600">
            </div>
            <div>
                <label class="block font-bold mb-1">Họ và Tên *</label>
                <input type="text" name="full_name" required value="<?= htmlspecialchars($user['full_name']) ?>" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                </div>
                <div>
                    <label class="block font-bold mb-1">Số Điện Thoại</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                <label class="block font-bold mb-1">Đổi Mật Khẩu Mới (Để trống nếu không đổi)</label>
                <input type="password" name="new_password" placeholder="Nhập mật khẩu mới..." class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
            </div>
            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md">
                    Cập Nhật Hồ Sơ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    async function handleUpdateProfile(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const res = await apiPost('<?= BASE_URL ?>/profile', data);
        if (res) {
            setTimeout(() => location.reload(), 800);
        }
    }
</script>
