<?php // views/classes/show.php ?>
<?php
$maleCount = 0;
$femaleCount = 0;
foreach ($students as $s) {
    if (($s['gender'] ?? 'male') === 'female') $femaleCount++;
    else $maleCount++;
}
?>
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="<?= BASE_URL ?>/classes" class="hover:text-emerald-600 transition-colors">Lớp học</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-emerald-600"><?= htmlspecialchars($class['name']) ?></span>
        </div>
        <a href="<?= BASE_URL ?>/classes" 
           class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:shadow-sm transition-all">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Danh sách lớp</span>
        </a>
    </div>

    <!-- Class Header Card -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-60 h-60 bg-blue-500/10 rounded-full blur-3xl -ml-10 -mb-10"></div>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-[11px] font-mono font-black uppercase tracking-widest mb-2 border border-emerald-500/30">
                    <?= htmlspecialchars($class['code']) ?>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-white"><?= htmlspecialchars($class['name']) ?></h1>
                <div class="flex flex-wrap items-center gap-4 text-xs text-emerald-100/80 mt-2">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-emerald-400">meeting_room</span>
                        Phòng học: <strong class="text-white"><?= htmlspecialchars($class['room_number'] ?? 'Chưa xếp') ?></strong>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-emerald-400">person_pin</span>
                        GVCN: <strong class="text-white"><?= htmlspecialchars($class['homeroom_teacher_name'] ?? 'Chưa phân công') ?></strong>
                    </span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="flex items-center gap-3">
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-4 py-3 text-center min-w-[90px]">
                    <p class="text-[10px] text-emerald-300 font-extrabold uppercase">Sĩ Số</p>
                    <p class="text-2xl font-black text-white mt-0.5"><?= count($students) ?> <span class="text-xs text-slate-300 font-normal">/ <?= $class['max_students'] ?></span></p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-4 py-3 text-center min-w-[90px]">
                    <p class="text-[10px] text-blue-300 font-extrabold uppercase">Nam / Nữ</p>
                    <p class="text-lg font-black text-white mt-0.5"><?= $maleCount ?> 👦 / <?= $femaleCount ?> 👧</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Students in Class -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Danh Sách Học Sinh Trong Lớp</h3>
                <p class="text-xs text-slate-400 mt-0.5">Sắp xếp theo thứ tự danh sách điểm</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>/students/print?class_id=<?= $class['id'] ?>&auto=1" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-md shadow-emerald-500/20" title="In danh sách lớp chuẩn A4">
                    <span class="material-symbols-outlined text-[16px]">print</span>
                    <span>In Danh Sách Lớp</span>
                </a>
                <button onclick="exportClassToExcel()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-[16px] text-emerald-600">file_download</span>
                    <span>Xuất Excel</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs table-premium" id="classStudentTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase font-extrabold text-[10px] tracking-widest">
                        <th class="px-5 py-3.5 w-12 text-center">STT</th>
                        <th class="px-5 py-3.5">Mã HS</th>
                        <th class="px-5 py-3.5">Họ và Tên</th>
                        
                        <th class="px-5 py-3.5">Giới tính</th>
                        <th class="px-5 py-3.5">Ngày sinh</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($students as $idx => $s): ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                        <td class="px-5 py-3.5 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-lg"><?= htmlspecialchars($s['student_code']) ?></span>
                        </td>
                        <td class="px-5 py-3.5">
                            <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors">
                                <?= htmlspecialchars($s['full_name']) ?>
                            </a>
                        </td>
                        
                        <td class="px-5 py-3.5">
                            <span class="badge <?= $s['gender'] === 'female' ? 'badge-rose' : 'badge-blue' ?>">
                                <?= $s['gender'] === 'female' ? 'Nữ' : 'Nam' ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 font-mono"><?= date('d/m/Y', strtotime($s['dob'])) ?></td>
                        <td class="px-5 py-3.5">
                            <span class="badge <?= ($s['status'] ?? 'studying') === 'studying' ? 'badge-emerald' : 'badge-slate' ?>">
                                <?= ($s['status'] ?? 'studying') === 'studying' ? 'Đang học' : 'Khác' ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-lg transition-colors" title="Hồ sơ">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </a>
                                <?php if (Permission::can('classes.assign_students')): ?>
                                <button onclick="openTransferModal(<?= $s['id'] ?>, '<?= addslashes($s['full_name']) ?>')" 
                                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded-lg transition-colors" title="Chuyển lớp khác">
                                    <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="8" class="py-16 text-center text-slate-400">
                            <span class="material-symbols-outlined text-5xl block mb-2 opacity-30">group_off</span>
                            Chưa có học sinh nào được phân vào lớp này
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Chuyển Lớp -->
<div id="transferModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-950 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[18px]">swap_horiz</span>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Chuyển Lớp Cho Học Sinh</h3>
            </div>
            <button onclick="closeModal('transferModal')" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form onsubmit="handleTransfer(event)" class="space-y-4 pt-4 text-xs">
            <input type="hidden" name="student_id" id="transferStudentId">
            <div class="bg-blue-50 dark:bg-blue-950/40 p-3 rounded-xl border border-blue-100 dark:border-blue-900">
                <p class="text-slate-600 dark:text-slate-400">Đang chuyển học sinh:</p>
                <p class="font-bold text-blue-700 dark:text-blue-300 text-sm mt-0.5" id="transferStudentName"></p>
            </div>
            <div class="space-y-1.5">
                <label class="block font-bold text-slate-700 dark:text-slate-300">Chọn Lớp Đích <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">class</span>
                    <select name="target_class_id" required class="w-full pl-9 pr-3 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 font-bold appearance-none">
                        <?php foreach ($allClasses as $ac): ?>
                            <?php if ($ac['id'] != $class['id']): ?>
                            <option value="<?= $ac['id'] ?>"><?= htmlspecialchars($ac['name']) ?> (Khối <?= htmlspecialchars($ac['grade_name'] ?? '') ?>)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('transferModal')" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Hủy</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-black rounded-xl shadow-md shadow-blue-500/20 transition-all active:scale-95">Xác Nhận Chuyển</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTransferModal(id, name) {
    document.getElementById('transferStudentId').value = id;
    document.getElementById('transferStudentName').textContent = name;
    document.getElementById('transferModal').classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

async function handleTransfer(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());
    const res = await apiPost('<?= BASE_URL ?>/classes/transfer-student', data);
    if (res) {
        closeModal('transferModal');
        setTimeout(() => location.reload(), 700);
    }
}

function exportClassToExcel() {
    const table = document.getElementById('classStudentTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Danh Sách Lớp"});
    XLSX.writeFile(wb, "Danh_Sach_<?= htmlspecialchars($class['code']) ?>.xlsx");
}
</script>
