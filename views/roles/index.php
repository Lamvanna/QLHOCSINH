<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Vai Trò & Ma Trận Phân Quyền (RBAC)</h2>
        <p class="text-xs text-slate-500 mt-1">Cấu hình chi tiết quyền hạn truy cập chức năng cho từng nhóm người dùng</p>
    </div>

    <!-- Roles Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <?php foreach ($roles as $r): ?>
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-mono"><?= htmlspecialchars($r['name']) ?></span>
            <h4 class="font-bold text-base text-slate-900 dark:text-white mt-2"><?= htmlspecialchars($r['display_name']) ?></h4>
            <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($r['description']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Permissions Matrix -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Danh Mục Quyền Hạn Trong Hệ Thống</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
            <?php foreach ($permissions as $p): ?>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <p class="font-mono font-bold text-emerald-600 dark:text-emerald-400"><?= htmlspecialchars($p['code']) ?></p>
                <p class="text-slate-600 dark:text-slate-400 mt-0.5"><?= htmlspecialchars($p['description']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
