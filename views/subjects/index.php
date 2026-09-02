<?php // views/subjects/index.php ?>
<div class="space-y-5">
    <div class="bg-gradient-to-br from-slate-900 to-violet-950 rounded-3xl p-6 md:p-8 relative overflow-hidden shadow-xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-violet-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-violet-500/20 text-violet-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-2 border border-violet-500/30">
                    <span class="material-symbols-outlined text-[14px]">menu_book</span> Chương Trình Học
                </div>
                <h2 class="text-2xl font-black text-white">Quản Lý Môn Học</h2>
                <p class="text-xs text-slate-400 mt-1"><?= count($subjects) ?> môn học trong chương trình</p>
            </div>
            <?php if (Permission::can('subjects.create')): ?>
            <button onclick="openSubModal('create')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-500 hover:bg-violet-400 text-white text-xs font-black rounded-2xl shadow-lg shadow-violet-500/30 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add_circle</span> Thêm Môn Học
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
                        <th class="px-5 py-3.5">Mã Môn</th>
                        <th class="px-5 py-3.5">Tên Môn Học</th>
                        <th class="px-5 py-3.5 text-center">Tiết/Tuần</th>
                        <th class="px-5 py-3.5 text-center">Hệ Số</th>
                        <th class="px-5 py-3.5">Trạng Thái</th>
                        <th class="px-5 py-3.5 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($subjects as $idx => $s): ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                        <td class="px-5 py-3.5 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                        <td class="px-5 py-3.5"><span class="font-mono font-black text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-950/40 px-2 py-0.5 rounded-lg"><?= htmlspecialchars($s['code']) ?></span></td>
                        <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($s['name']) ?></td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 font-bold text-slate-700 dark:text-slate-300">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">schedule</span>
                                <?= $s['periods_per_week'] ?> tiết
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="badge <?= $s['coefficient'] >= 2 ? 'badge-amber' : 'badge-slate' ?>">×<?= $s['coefficient'] ?></span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="badge <?= $s['status'] === 'active' ? 'badge-emerald' : 'badge-rose' ?>">
                                <?= $s['status'] === 'active' ? 'Đang dạy' : 'Ngừng' ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php if (Permission::can('subjects.update')): ?>
                                <button onclick="openSubModal('edit',<?= htmlspecialchars(json_encode($s)) ?>)" class="p-1.5 text-slate-400 hover:text-violet-600 hover:bg-violet-50 dark:hover:bg-violet-950/40 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                <?php endif; ?>
                                <?php if (Permission::can('subjects.delete')): ?>
                                <button onclick="deleteRecord('<?= BASE_URL ?>/subjects/<?= $s['id'] ?>','Xóa môn <?= htmlspecialchars($s['name']) ?>?')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($subjects)): ?>
                    <tr><td colspan="7" class="py-20 text-center text-slate-400">
                        <span class="material-symbols-outlined text-5xl block mb-2 opacity-30">menu_book</span> Chưa có môn học nào
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Môn Học -->
<div id="subModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-violet-100 dark:bg-violet-950 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-600 dark:text-violet-400 text-[18px]">menu_book</span>
                </div>
                <h3 id="subModalTitle" class="text-sm font-extrabold text-slate-900 dark:text-white">Thêm Môn Học</h3>
            </div>
            <button onclick="closeModal('subModal')" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"><span class="material-symbols-outlined text-[20px]">close</span></button>
        </div>
        <form onsubmit="handleSubSubmit(event)" class="p-6 space-y-4 text-xs">
            <input type="hidden" id="sub_id" value="">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Mã Môn <span class="text-rose-500">*</span></label>
                    <input id="sub_code" type="text" name="code" required placeholder="TOAN, LY, HOA..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono font-black text-violet-600 uppercase">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Tên Môn Học <span class="text-rose-500">*</span></label>
                    <input id="sub_name" type="text" name="name" required placeholder="Toán Học..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Tiết / Tuần</label>
                    <input id="sub_periods" type="number" name="periods_per_week" min="1" max="10" placeholder="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Hệ Số</label>
                    <select id="sub_coef" name="coefficient" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold appearance-none">
                        <option value="1.0">×1.0 (Thường)</option>
                        <option value="2.0">×2.0 (Chính)</option>
                        <option value="0.5">×0.5 (Phụ)</option>
                    </select>
                </div>
                <div class="col-span-2 space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Trạng Thái</label>
                    <select id="sub_status" name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold appearance-none">
                        <option value="active">Đang Giảng Dạy</option>
                        <option value="inactive">Ngừng Dạy</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('subModal')" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 transition-colors">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-black text-white bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-500 hover:to-violet-400 rounded-xl shadow-md shadow-violet-500/20 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px] align-middle mr-1">save</span>Lưu Môn Học
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function openSubModal(mode, data = null) {
    document.getElementById('subModal').classList.remove('hidden');
    document.getElementById('subModalTitle').textContent = mode === 'edit' ? 'Chỉnh Sửa Môn Học' : 'Thêm Môn Học Mới';
    document.getElementById('sub_id').value      = data?.id ?? '';
    document.getElementById('sub_code').value    = data?.code ?? '';
    document.getElementById('sub_code').readOnly = mode === 'edit';
    document.getElementById('sub_name').value    = data?.name ?? '';
    document.getElementById('sub_periods').value = data?.periods_per_week ?? 2;
    document.getElementById('sub_coef').value    = data?.coefficient ?? '1.0';
    document.getElementById('sub_status').value  = data?.status ?? 'active';
}
async function handleSubSubmit(e) {
    e.preventDefault();
    const id  = document.getElementById('sub_id').value;
    const url = id ? `<?= BASE_URL ?>/subjects/${id}` : `<?= BASE_URL ?>/subjects`;
    const res = await apiPost(url, Object.fromEntries(new FormData(e.target).entries()));
    if (res) { closeModal('subModal'); setTimeout(() => location.reload(), 700); }
}
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
async function deleteRecord(url, msg) {
    if (!confirm(msg)) return;
    const res = await apiPost(url, { _method: 'DELETE' });
    if (res) setTimeout(() => location.reload(), 700);
}
</script>
