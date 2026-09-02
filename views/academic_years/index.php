<?php // views/academic_years/index.php ?>
<div class="space-y-5">
    <div class="bg-gradient-to-br from-slate-900 to-amber-950 rounded-3xl p-6 md:p-8 relative overflow-hidden shadow-xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-2 border border-amber-500/30">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span> Niên Học
                </div>
                <h2 class="text-2xl font-black text-white">Quản Lý Năm Học</h2>
                <p class="text-xs text-slate-400 mt-1"><?= count($years) ?> năm học đã tạo</p>
            </div>
            <?php if (Permission::can('academic_years.create')): ?>
            <button onclick="openYearModal('create')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white text-xs font-black rounded-2xl shadow-lg shadow-amber-500/30 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add_circle</span> Thêm Năm Học
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($years as $y): ?>
        <div class="bg-white dark:bg-slate-900 border-2 <?= $y['is_current'] ? 'border-amber-400 dark:border-amber-600' : 'border-slate-200 dark:border-slate-800' ?> rounded-2xl p-5 card-subtle group relative">
            <?php if ($y['is_current']): ?>
            <div class="absolute -top-2.5 left-5">
                <span class="badge badge-amber px-3">
                    <span class="material-symbols-outlined text-[12px]">star</span> Năm Hiện Tại
                </span>
            </div>
            <?php endif; ?>
            <div class="flex items-start justify-between mt-1">
                <div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base"><?= htmlspecialchars($y['name']) ?></h3>
                    <p class="text-[11px] font-mono font-bold text-amber-600 dark:text-amber-400 mt-0.5"><?= htmlspecialchars($y['code']) ?></p>
                </div>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <?php if (Permission::can('academic_years.update')): ?>
                    <button onclick="openYearModal('edit',<?= htmlspecialchars(json_encode($y)) ?>)" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </button>
                    <?php endif; ?>
                    <?php if (Permission::can('academic_years.delete') && !$y['is_current']): ?>
                    <button onclick="deleteRecord('<?= BASE_URL ?>/academic-years/<?= $y['id'] ?>','Xóa năm học <?= htmlspecialchars($y['name']) ?>?')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 text-[11px]">
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-2.5">
                    <p class="text-slate-400 font-semibold">Bắt đầu</p>
                    <p class="font-extrabold text-slate-800 dark:text-slate-200 mt-0.5"><?= date('d/m/Y', strtotime($y['start_date'])) ?></p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-2.5">
                    <p class="text-slate-400 font-semibold">Kết thúc</p>
                    <p class="font-extrabold text-slate-800 dark:text-slate-200 mt-0.5"><?= date('d/m/Y', strtotime($y['end_date'])) ?></p>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <span class="badge <?= $y['status'] === 'active' ? 'badge-emerald' : 'badge-slate' ?>"><?= $y['status'] === 'active' ? 'Đang hoạt động' : 'Đã kết thúc' ?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($years)): ?>
        <div class="col-span-3 py-20 text-center text-slate-400">
            <span class="material-symbols-outlined text-5xl block mb-2 opacity-30">calendar_today</span> Chưa có năm học nào
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Năm Học -->
<div id="yearModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-[18px]">calendar_today</span>
                </div>
                <h3 id="yearModalTitle" class="text-sm font-extrabold text-slate-900 dark:text-white">Thêm Năm Học</h3>
            </div>
            <button onclick="closeModal('yearModal')" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"><span class="material-symbols-outlined text-[20px]">close</span></button>
        </div>
        <form onsubmit="handleYearSubmit(event)" class="p-6 space-y-4 text-xs">
            <input type="hidden" id="year_id" value="">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Tên Năm Học <span class="text-rose-500">*</span></label>
                    <input id="year_name" type="text" name="name" required placeholder="Năm học 2025 – 2026" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Mã Năm Học <span class="text-rose-500">*</span></label>
                    <input id="year_code" type="text" name="code" required placeholder="2025-2026" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono font-black text-amber-600">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Ngày Bắt Đầu <span class="text-rose-500">*</span></label>
                    <input id="year_start" type="date" name="start_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Ngày Kết Thúc <span class="text-rose-500">*</span></label>
                    <input id="year_end" type="date" name="end_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Trạng Thái</label>
                    <select id="year_status" name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold appearance-none">
                        <option value="active">Đang hoạt động</option>
                        <option value="completed">Đã kết thúc</option>
                        <option value="upcoming">Sắp tới</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <input id="year_current" type="checkbox" name="is_current" value="1" class="w-4 h-4 accent-amber-500">
                    <label for="year_current" class="font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Đặt làm năm học hiện tại</label>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('yearModal')" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 transition-colors">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-black text-white bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 rounded-xl shadow-md shadow-amber-500/20 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px] align-middle mr-1">save</span>Lưu Năm Học
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function openYearModal(mode, data = null) {
    document.getElementById('yearModal').classList.remove('hidden');
    document.getElementById('yearModalTitle').textContent  = mode === 'edit' ? 'Chỉnh Sửa Năm Học' : 'Thêm Năm Học Mới';
    document.getElementById('year_id').value               = data?.id ?? '';
    document.getElementById('year_name').value             = data?.name ?? '';
    document.getElementById('year_code').value             = data?.code ?? '';
    document.getElementById('year_start').value            = data?.start_date ?? '';
    document.getElementById('year_end').value              = data?.end_date ?? '';
    document.getElementById('year_status').value           = data?.status ?? 'active';
    document.getElementById('year_current').checked        = data?.is_current == 1;
}
async function handleYearSubmit(e) {
    e.preventDefault();
    const id  = document.getElementById('year_id').value;
    const url = id ? `<?= BASE_URL ?>/academic-years/${id}` : `<?= BASE_URL ?>/academic-years`;
    const res = await apiPost(url, Object.fromEntries(new FormData(e.target).entries()));
    if (res) { closeModal('yearModal'); setTimeout(() => location.reload(), 700); }
}
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
async function deleteRecord(url, msg) {
    if (!confirm(msg)) return;
    const res = await apiPost(url, { _method: 'DELETE' });
    if (res) setTimeout(() => location.reload(), 700);
}
</script>
