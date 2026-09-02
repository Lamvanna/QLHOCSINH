<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Sao Lưu & Phục Hồi Dữ Liệu</h2>
            <p class="text-xs text-slate-500 mt-1">Tạo bản sao lưu toàn bộ cơ sở dữ liệu MySQL (.sql), tải về và phục hồi an toàn</p>
        </div>
        <button onclick="handleCreateBackup()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/20">
            <i data-lucide="database" class="w-4 h-4 mr-1.5 inline"></i> Tạo Bản Sao Lưu Mới
        </button>
    </div>

    <!-- Backup Files Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                        <th class="p-4 w-12 text-center">STT</th>
                        <th class="p-4">Tên Tệp Sao Lưu (.sql)</th>
                        <th class="p-4">Dung Lượng</th>
                        <th class="p-4">Thời Gian Tạo</th>
                        <th class="p-4 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    <?php if (!empty($backups)): ?>
                        <?php foreach ($backups as $idx => $b): ?>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                            <td class="p-4 text-center text-slate-400"><?= $idx + 1 ?></td>
                            <td class="p-4 font-mono font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($b['filename']) ?></td>
                            <td class="p-4 text-slate-500 font-mono"><?= $b['size'] ?></td>
                            <td class="p-4 text-slate-400"><?= $b['created_at'] ?></td>
                            <td class="p-4 text-right space-x-2">
                                <button onclick="handleRestoreBackup('<?= $b['filename'] ?>')" class="px-3 py-1 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-bold rounded-lg hover:bg-amber-100">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 mr-1 inline"></i> Phục Hồi
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Chưa có bản sao lưu nào. Hãy bấm "Tạo Bản Sao Lưu Mới".</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    async function handleCreateBackup() {
        const res = await apiPost('<?= BASE_URL ?>/backups', {});
        if (res) {
            setTimeout(() => location.reload(), 800);
        }
    }

    async function handleRestoreBackup(filename) {
        if (!confirm(`CẢNH BÁO: Bạn có chắc chắn muốn phục hồi database từ bản '${filename}'? Dữ liệu hiện tại sẽ được ghi đè hoàn toàn.`)) return;
        const res = await apiPost('<?= BASE_URL ?>/backups/restore', { filename: filename });
        if (res) {
            setTimeout(() => location.reload(), 1000);
        }
    }
</script>
