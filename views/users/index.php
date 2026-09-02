<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Quản Lý Người Dùng & Tài Khoản</h2>
            <p class="text-xs text-slate-500 mt-1">Danh sách tài khoản hệ thống, phân quyền vai trò và khóa/mở khóa</p>
        </div>
        <?php if (Permission::can('users.create')): ?>
        <button onclick="openModal('createUserModal')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/20">
            <i data-lucide="user-plus" class="w-4 h-4 mr-1.5 inline"></i> Tạo Tài Khoản Mới
        </button>
        <?php endif; ?>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                        <th class="p-4 w-12 text-center">STT</th>
                        <th class="p-4">Tên Đăng Nhập</th>
                        <th class="p-4">Họ và Tên</th>
                        <th class="p-4">Vai Trò (Role)</th>
                        <th class="p-4">Email / SĐT</th>
                        <th class="p-4">Lần Đăng Nhập Cuối</th>
                        <th class="p-4">Trạng Thái</th>
                        <th class="p-4 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    <?php foreach ($users as $idx => $u): ?>
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                        <td class="p-4 text-center text-slate-400"><?= $idx + 1 ?></td>
                        <td class="p-4 font-mono font-bold text-emerald-600"><?= htmlspecialchars($u['username']) ?></td>
                        <td class="p-4 font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                <?= htmlspecialchars($u['role_name'] ?? '—') ?>
                            </span>
                        </td>
                        <td class="p-4 text-slate-500"><?= htmlspecialchars($u['email'] ?? $u['phone'] ?? '—') ?></td>
                        <td class="p-4 text-slate-400"><?= !empty($u['last_login_at']) ? date('d/m/Y H:i', strtotime($u['last_login_at'])) : 'Chưa đăng nhập' ?></td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $u['status'] === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' ?>">
                                <?= $u['status'] === 'active' ? 'Hoạt động' : 'Bị khóa' ?>
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button onclick="toggleUserStatus(<?= $u['id'] ?>)" class="px-2.5 py-1 text-[11px] font-bold <?= $u['status'] === 'active' ? 'text-rose-600 bg-rose-50 hover:bg-rose-100' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100' ?> rounded-lg">
                                <?= $u['status'] === 'active' ? 'Khóa TK' : 'Mở Khóa' ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tạo Tài Khoản -->
<div id="createUserModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-base font-bold">Thêm Mới Tài Khoản</h3>
            <button onclick="closeModal('createUserModal')" class="text-slate-400">&times;</button>
        </div>
        <form onsubmit="handleCreateUser(event)" class="space-y-4 pt-4 text-xs">
            <div>
                <label class="block font-bold mb-1">Tên Đăng Nhập (Username) *</label>
                <input type="text" name="username" required placeholder="admin_user" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
            </div>
            <div>
                <label class="block font-bold mb-1">Mật Khẩu Khởi Tạo *</label>
                <input type="password" name="password" required value="123456" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
            </div>
            <div>
                <label class="block font-bold mb-1">Họ và Tên *</label>
                <input type="text" name="full_name" required placeholder="Nguyễn Văn A" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
            </div>
            <div>
                <label class="block font-bold mb-1">Vai Trò (Role) *</label>
                <select name="role_id" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800 font-bold">
                    <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['display_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="pt-4 flex justify-end space-x-2 border-t">
                <button type="button" onclick="closeModal('createUserModal')" class="px-4 py-2 border rounded-xl">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl">Tạo Người Dùng</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    async function handleCreateUser(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const res = await apiPost('<?= BASE_URL ?>/users', data);
        if (res) {
            closeModal('createUserModal');
            setTimeout(() => location.reload(), 800);
        }
    }

    async function toggleUserStatus(id) {
        const res = await apiPost(`<?= BASE_URL ?>/users/${id}/toggle-status`, {});
        if (res) {
            setTimeout(() => location.reload(), 800);
        }
    }
</script>
