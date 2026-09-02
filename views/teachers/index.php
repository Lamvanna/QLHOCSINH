<?php // views/teachers/index.php ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-blue-700 dark:text-blue-400 mb-1">
                <span class="material-symbols-outlined text-sm">school</span>
                <span>Quản Lý Nhân Sự Sư Phạm</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Danh Sách Giáo Viên</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-0.5">Quản lý hồ sơ giáo viên, chuyên môn giảng dạy và phân công chủ nhiệm các lớp.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="exportTeachersToExcel()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm" title="Xuất file Excel">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">table_chart</span>
                <span class="hidden sm:inline">Xuất Excel</span>
            </button>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm" title="In danh sách">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">print</span>
                <span class="hidden sm:inline">In Bảng</span>
            </button>
            <?php if (Permission::can('teachers.create')): ?>
            <a href="<?= BASE_URL ?>/teachers/create" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Thêm Giáo Viên Mới</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs table-premium" id="teachersTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase font-extrabold text-[10px] tracking-widest">
                        <th class="px-5 py-3.5 w-12 text-center">#</th>
                        <th class="px-5 py-3.5">Giáo Viên</th>
                        <th class="px-5 py-3.5">Chuyên Môn</th>
                        <th class="px-5 py-3.5">Trình Độ</th>
                        <th class="px-5 py-3.5">Chủ Nhiệm</th>
                        <th class="px-5 py-3.5">Liên Hệ</th>
                        <th class="px-5 py-3.5">Trạng Thái</th>
                        <th class="px-5 py-3.5 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($teachers as $idx => $t): ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                        <td class="px-5 py-3.5 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <img src="<?= htmlspecialchars($t['avatar'] ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150') ?>" class="w-9 h-9 rounded-full object-cover ring-2 ring-blue-500/20" alt="">
                                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full <?= $t['status'] === 'active' ? 'bg-emerald-400' : 'bg-rose-400' ?> ring-1 ring-white dark:ring-slate-900"></span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($t['full_name']) ?></p>
                                    <p class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($t['teacher_code']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5"><span class="badge badge-blue font-bold"><?= htmlspecialchars($t['specialization'] ?? '—') ?></span></td>
                        <td class="px-5 py-3.5 text-slate-500"><?= htmlspecialchars($t['qualification'] ?? 'Cử nhân') ?></td>
                        <td class="px-5 py-3.5">
                            <?php if (!empty($t['homeroom_classes'])): ?>
                            <span class="badge badge-emerald font-bold"><?= htmlspecialchars($t['homeroom_classes']) ?></span>
                            <?php else: ?><span class="text-slate-400">—</span><?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="text-slate-600 dark:text-slate-400 font-mono"><?= htmlspecialchars($t['phone'] ?? '—') ?></div>
                            <div class="text-[10px] text-slate-400"><?= htmlspecialchars($t['email'] ?? '') ?></div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="badge <?= $t['status'] === 'active' ? 'badge-emerald' : 'badge-rose' ?>">
                                <?= $t['status'] === 'active' ? 'Đang dạy' : 'Nghỉ phép' ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php if (Permission::can('teachers.update')): ?>
                                <a href="<?= BASE_URL ?>/teachers/<?= $t['id'] ?>/edit" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded-lg transition-colors" title="Chỉnh sửa hồ sơ">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </a>
                                <?php endif; ?>
                                <?php if (Permission::can('teachers.delete')): ?>
                                <button onclick="deleteRecord('<?= BASE_URL ?>/teachers/<?= $t['id'] ?>','Bạn có chắc muốn xóa giáo viên này?')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors" title="Xóa">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($teachers)): ?>
                    <tr><td colspan="8" class="py-20 text-center text-slate-400">
                        <span class="material-symbols-outlined text-5xl block mb-2 opacity-30">school</span>
                        Chưa có giáo viên nào trong hệ thống
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function deleteRecord(url, msg) {
    if (!confirm(msg)) return;
    const res = await apiPost(url, { _method: 'DELETE' });
    if (res) {
        showToast('Đã xóa giáo viên thành công!', 'success');
        setTimeout(() => location.reload(), 700);
    }
}

function exportTeachersToExcel() {
    const table = document.getElementById('teachersTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "DanhSachGiaoVien"});
    XLSX.writeFile(wb, "Danh_Sach_Giao_Vien_EduManage.xlsx");
}
</script>
