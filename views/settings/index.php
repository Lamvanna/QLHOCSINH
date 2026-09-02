<div class="space-y-6 max-w-4xl">
    <div>
        <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Cấu Hình Hệ Thống</h2>
        <p class="text-xs text-slate-500 mt-1">Thông tin nhà trường, thiết lập năm học, thang điểm và quy tắc nghiệp vụ</p>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 md:p-8 shadow-sm">
        <form onsubmit="handleSaveSettings(event)" class="space-y-6 text-xs">
            <?php foreach ($settings as $s): ?>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                    <?= htmlspecialchars($s['description'] ?? $s['key_name']) ?>
                    <span class="font-mono text-[10px] text-slate-400 font-normal ml-1">(<?= htmlspecialchars($s['key_name']) ?>)</span>
                </label>
                <input type="text" name="<?= htmlspecialchars($s['key_name']) ?>" value="<?= htmlspecialchars($s['value'] ?? '') ?>" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium focus:ring-2 focus:ring-emerald-500">
            </div>
            <?php endforeach; ?>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20">
                    Lưu Toàn Bộ Cấu Hình
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    async function handleSaveSettings(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const res = await apiPost('<?= BASE_URL ?>/settings', data);
        if (res) {
            setTimeout(() => location.reload(), 800);
        }
    }
</script>
