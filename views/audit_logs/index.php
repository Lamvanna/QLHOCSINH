<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Nhật Ký Hệ Thống (Audit Log)</h2>
            <p class="text-xs text-slate-500 mt-1">Lưu trữ toàn bộ lịch sử thao tác thay đổi dữ liệu, đăng nhập, khóa điểm và quản trị</p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                        <th class="p-4 w-12 text-center">STT</th>
                        <th class="p-4">Thời Gian</th>
                        <th class="p-4">Người Thực Hiện</th>
                        <th class="p-4">Hành Động</th>
                        <th class="p-4">Module</th>
                        <th class="p-4">Địa Chỉ IP</th>
                        <th class="p-4">Chi Tiết Dữ Liệu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    <?php foreach ($logs as $idx => $l): ?>
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                        <td class="p-4 text-center text-slate-400"><?= $idx + 1 ?></td>
                        <td class="p-4 font-mono text-slate-500"><?= date('d/m/Y H:i:s', strtotime($l['created_at'])) ?></td>
                        <td class="p-4">
                            <span class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($l['full_name'] ?? 'System') ?></span>
                            <span class="text-[10px] text-slate-400 block"><?= htmlspecialchars($l['username'] ?? '') ?></span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                <?= htmlspecialchars($l['action']) ?>
                            </span>
                        </td>
                        <td class="p-4 font-bold text-slate-700 dark:text-slate-300"><?= htmlspecialchars($l['module']) ?></td>
                        <td class="p-4 font-mono text-slate-400"><?= htmlspecialchars($l['ip_address'] ?? '127.0.0.1') ?></td>
                        <td class="p-4 max-w-xs font-mono text-[10px] text-slate-500 truncate" title="<?= htmlspecialchars($l['new_data'] ?? $l['old_data'] ?? '') ?>">
                            <?= htmlspecialchars($l['new_data'] ?? $l['old_data'] ?? '—') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
