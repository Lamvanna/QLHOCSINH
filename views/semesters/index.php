<?php // views/semesters/index.php ?>
<div class="space-y-5">
    <div class="bg-gradient-to-br from-slate-900 to-teal-950 rounded-3xl p-6 md:p-8 relative overflow-hidden shadow-xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-teal-500/20 text-teal-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-2 border border-teal-500/30">
                    <span class="material-symbols-outlined text-[14px]">event_note</span> Học Kỳ
                </div>
                <h2 class="text-2xl font-black text-white">Quản Lý Học Kỳ</h2>
                <p class="text-xs text-slate-400 mt-1"><?= count($semesters) ?> học kỳ trong hệ thống</p>
            </div>
            <?php if (Permission::can('semesters.create')): ?>
            <button onclick="openSemModal('create')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-white text-xs font-black rounded-2xl shadow-lg shadow-teal-500/30 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add_circle</span> Thêm Học Kỳ
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs table-premium">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase font-extrabold text-[10px] tracking-widest">
                        <th class="px-5 py-3.5 w-12 text-center">#</th>
                        <th class="px-5 py-3.5">Tên Học Kỳ</th>
                        <th class="px-5 py-3.5">Năm Học</th>
                        <th class="px-5 py-3.5">Thời Gian</th>
                        <th class="px-5 py-3.5">Trạng Thái</th>
                        <th class="px-5 py-3.5 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($semesters as $idx => $s): ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group <?= $s['is_current'] ? 'bg-teal-50/50 dark:bg-teal-950/10' : '' ?>">
                        <td class="px-5 py-3.5 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900 dark:text-white"><?= htmlspecialchars($s['name']) ?></span>
                                <?php if ($s['is_current']): ?>
                                <span class="badge badge-emerald">Hiện tại</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="badge badge-teal"><?= htmlspecialchars($s['year_name'] ?? '—') ?></span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="text-slate-600 dark:text-slate-400 font-medium">
                                <?= date('d/m/Y', strtotime($s['start_date'])) ?> → <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="badge <?= $s['status'] === 'active' ? 'badge-emerald' : ($s['status'] === 'upcoming' ? 'badge-blue' : 'badge-slate') ?>">
                                <?= match($s['status']) { 'active' => 'Đang diễn ra', 'upcoming' => 'Sắp tới', default => 'Đã kết thúc' } ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php if (Permission::can('semesters.update')): ?>
                                <button onclick="openSemModal('edit',<?= htmlspecialchars(json_encode($s)) ?>)" class="p-1.5 text-slate-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-950/40 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                <?php endif; ?>
                                <?php if (Permission::can('semesters.delete') && !$s['is_current']): ?>
                                <button onclick="deleteRecord('<?= BASE_URL ?>/semesters/<?= $s['id'] ?>','Xóa học kỳ này?')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($semesters)): ?>
                    <tr><td colspan="6" class="py-20 text-center text-slate-400">
                        <span class="material-symbols-outlined text-5xl block mb-2 opacity-30">event_note</span> Chưa có học kỳ nào
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Học Kỳ -->
<div id="semModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-teal-100 dark:bg-teal-950 flex items-center justify-center">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400 text-[18px]">event_note</span>
                </div>
                <h3 id="semModalTitle" class="text-sm font-extrabold text-slate-900 dark:text-white">Thêm Học Kỳ</h3>
            </div>
            <button onclick="closeModal('semModal')" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"><span class="material-symbols-outlined text-[20px]">close</span></button>
        </div>
        <form onsubmit="handleSemSubmit(event)" class="p-6 space-y-4 text-xs">
            <input type="hidden" id="sem_id" value="">
            <div class="space-y-1.5">
                <label class="font-bold text-slate-700 dark:text-slate-300">Tên Học Kỳ <span class="text-rose-500">*</span></label>
                <input id="sem_name" type="text" name="name" required placeholder="Học kỳ 1, Học kỳ 2..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
            </div>
            <div class="space-y-1.5">
                <label class="font-bold text-slate-700 dark:text-slate-300">Năm Học <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">calendar_today</span>
                    <select id="sem_year" name="academic_year_id" required class="w-full pl-8 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold appearance-none">
                        <?php foreach ($years as $y): ?>
                        <option value="<?= $y['id'] ?>" <?= $y['is_current'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Ngày Bắt Đầu <span class="text-rose-500">*</span></label>
                    <input id="sem_start" type="date" name="start_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Ngày Kết Thúc <span class="text-rose-500">*</span></label>
                    <input id="sem_end" type="date" name="end_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Trạng Thái</label>
                    <select id="sem_status" name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold appearance-none">
                        <option value="active">Đang diễn ra</option>
                        <option value="upcoming">Sắp tới</option>
                        <option value="completed">Đã kết thúc</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <input id="sem_current" type="checkbox" name="is_current" value="1" class="w-4 h-4 accent-teal-500">
                    <label for="sem_current" class="font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Học kỳ hiện tại</label>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('semModal')" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 transition-colors">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-black text-white bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 rounded-xl shadow-md shadow-teal-500/20 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px] align-middle mr-1">save</span>Lưu Học Kỳ
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function openSemModal(mode, data = null) {
    document.getElementById('semModal').classList.remove('hidden');
    document.getElementById('semModalTitle').textContent  = mode === 'edit' ? 'Chỉnh Sửa Học Kỳ' : 'Thêm Học Kỳ Mới';
    document.getElementById('sem_id').value               = data?.id ?? '';
    document.getElementById('sem_name').value             = data?.name ?? '';
    document.getElementById('sem_year').value             = data?.academic_year_id ?? '';
    document.getElementById('sem_start').value            = data?.start_date ?? '';
    document.getElementById('sem_end').value              = data?.end_date ?? '';
    document.getElementById('sem_status').value           = data?.status ?? 'active';
    document.getElementById('sem_current').checked        = data?.is_current == 1;
}
async function handleSemSubmit(e) {
    e.preventDefault();
    const id  = document.getElementById('sem_id').value;
    const url = id ? `<?= BASE_URL ?>/semesters/${id}` : `<?= BASE_URL ?>/semesters`;
    const res = await apiPost(url, Object.fromEntries(new FormData(e.target).entries()));
    if (res) { closeModal('semModal'); setTimeout(() => location.reload(), 700); }
}
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
async function deleteRecord(url, msg) {
    if (!confirm(msg)) return;
    const res = await apiPost(url, { _method: 'DELETE' });
    if (res) setTimeout(() => location.reload(), 700);
}
</script>
