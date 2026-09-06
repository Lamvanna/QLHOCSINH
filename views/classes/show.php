<?php // views/classes/show.php ?>
<?php
$maleCount = 0;
$femaleCount = 0;
foreach ($students as $s) {
    if (($s['gender'] ?? 'male') === 'female') $femaleCount++;
    else $maleCount++;
}
$maxSt = (int)($class['max_students'] ?? 45);
$curSt = count($students);
$fillPct = $maxSt > 0 ? round(($curSt / max(1, $maxSt)) * 100) : 0;
$classSubjects = $classSubjects ?? [];
$subjectCount = count($classSubjects);
?>
<div class="space-y-6 pb-12">
    <!-- Breadcrumbs & Quick Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500">
            <a href="<?= BASE_URL ?>/classes" class="hover:text-emerald-600 transition-colors">Lớp học</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-bold"><?= htmlspecialchars($class['name']) ?></span>
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
                    MÃ LỚP: <?= htmlspecialchars($class['code']) ?>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-white"><?= htmlspecialchars($class['name']) ?></h1>
                <div class="flex flex-wrap items-center gap-4 text-xs text-emerald-100/80 mt-2">
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-emerald-400">meeting_room</span>
                        Phòng học: <strong class="text-white"><?= htmlspecialchars($class['room_number'] ?? 'Chưa xếp') ?></strong>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-emerald-400">person_pin</span>
                        GVCN: <strong class="text-white"><?= htmlspecialchars($class['homeroom_teacher_name'] ?? 'Chưa phân công') ?></strong>
                    </span>
                </div>
            </div>

            <!-- Quick Stats Bento -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-5 py-3 text-center min-w-[90px]">
                    <p class="text-[10px] text-emerald-300 font-extrabold uppercase tracking-wider">Sĩ Số</p>
                    <p class="text-2xl font-black text-white mt-0.5"><?= $curSt ?> <span class="text-xs text-slate-300 font-normal">/ <?= $maxSt ?></span></p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-5 py-3 text-center min-w-[90px]">
                    <p class="text-[10px] text-amber-300 font-extrabold uppercase tracking-wider">Môn Học</p>
                    <p class="text-2xl font-black text-white mt-0.5"><?= $subjectCount ?> <span class="text-xs text-slate-300 font-normal">môn</span></p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-5 py-3 text-center min-w-[90px]">
                    <p class="text-[10px] text-blue-300 font-extrabold uppercase tracking-wider">Nam / Nữ</p>
                    <p class="text-lg font-black text-white mt-1">
                        <span class="text-blue-300"><?= $maleCount ?> Nam</span> &bull; 
                        <span class="text-rose-300"><?= $femaleCount ?> Nữ</span>
                    </p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-5 py-3 text-center min-w-[90px]">
                    <p class="text-[10px] text-emerald-300 font-extrabold uppercase tracking-wider">Lấp Đầy</p>
                    <p class="text-2xl font-black text-white mt-0.5"><?= $fillPct ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- BENTO SECTION: CHƯƠNG TRÌNH MÔN HỌC CỦA LỚP (Gồm những môn nào & mấy môn) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 md:p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">menu_book</span>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Chương Trình Môn Học Của Lớp</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            <?= $subjectCount ?> môn học
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Các môn học được phân bổ riêng cho lớp <?= htmlspecialchars($class['name']) ?> để quản lý giảng dạy và tải bảng điểm</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="exportClassToExcel()" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-500/20 transition-all active:scale-[0.98]"
                        title="Tải bảng điểm Excel với đầy đủ cột các môn học của lớp">
                    <span class="material-symbols-outlined text-[17px]">download</span>
                    <span>Tải Bảng Điểm (<?= $subjectCount ?> Môn)</span>
                </button>
                <a href="<?= BASE_URL ?>/classes" 
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all"
                   title="Quay lại danh sách lớp để chỉnh sửa môn học">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    <span>Sửa Môn Học</span>
                </a>
            </div>
        </div>

        <!-- Danh sách môn học hiển thị dạng thẻ đẹp mắt -->
        <?php if (!empty($classSubjects)): ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <?php foreach ($classSubjects as $idx => $cs): ?>
            <div class="p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="font-mono text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                        <?= htmlspecialchars($cs['code']) ?>
                    </span>
                    <span class="text-[10px] font-bold text-slate-400">#<?= $idx + 1 ?></span>
                </div>
                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-emerald-600 transition-colors" title="<?= htmlspecialchars($cs['name']) ?>">
                    <?= htmlspecialchars($cs['name']) ?>
                </h4>
                <div class="text-[10px] text-slate-400 mt-1">
                    <?= !empty($cs['periods_per_week']) ? $cs['periods_per_week'] . ' tiết/tuần' : 'Môn chính khóa' ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="p-6 text-center rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 text-amber-700 dark:text-amber-300 text-xs">
            <span class="material-symbols-outlined text-3xl block mb-1">menu_book</span>
            <p class="font-bold">Lớp học này chưa được phân bổ môn học nào.</p>
            <p class="text-[11px] text-slate-500 mt-1">Vui lòng quay lại danh sách lớp học và bấm nút chỉnh sửa lớp để chọn các môn học áp dụng.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Student Table Section -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Table Toolbar -->
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white dark:bg-slate-900">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Danh Sách Học Sinh Trong Lớp</h2>
                <p class="text-xs text-slate-400 mt-0.5">Tổng số <?= count($students) ?> học sinh đang theo học</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="exportClassToExcel()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-slate-800 border border-emerald-500 hover:bg-emerald-50 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-bold transition-all shadow-sm" title="Tải bảng điểm Excel kèm theo các cột môn học riêng của lớp này">
                    <span class="material-symbols-outlined text-[17px] text-emerald-600">table_view</span>
                    <span>Tải Bảng Điểm Excel</span>
                </button>
                <a href="<?= BASE_URL ?>/students/print?class_id=<?= $class['id'] ?>&auto=1" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#006c4a] hover:bg-[#005137] text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[17px]">print</span>
                    <span>In Danh Sách</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="classStudentTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase font-extrabold text-[10px] tracking-widest">
                        <th class="px-5 py-3.5 w-12 text-center">STT</th>
                        <th class="px-5 py-3.5">MÃ HỌC SINH</th>
                        <th class="px-5 py-3.5">THÔNG TIN HỌC SINH</th>
                        <th class="px-5 py-3.5">GIỚI TÍNH</th>
                        <th class="px-5 py-3.5">NGÀY SINH</th>
                        <th class="px-5 py-3.5">TRẠNG THÁI</th>
                        <th class="px-5 py-3.5 text-right">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $idx => $s): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="px-5 py-3.5 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-lg"><?= htmlspecialchars($s['student_code']) ?></span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0">
                                        <img src="<?= htmlspecialchars($s['avatar'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150') ?>" class="w-full h-full object-cover" alt="Avatar">
                                    </div>
                                    <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors">
                                        <?= htmlspecialchars($s['full_name']) ?>
                                    </a>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-xs <?= $s['gender'] === 'female' ? 'text-rose-500' : 'text-blue-600' ?>">
                                    <?= $s['gender'] === 'female' ? 'Nữ' : 'Nam' ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 font-medium"><?= !empty($s['dob']) ? date('d/m/Y', strtotime($s['dob'])) : '—' ?></td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                                    ● ĐANG HỌC
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-lg transition-colors" title="Xem hồ sơ học sinh">
                                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    </a>
                                    <?php if (Permission::can('classes.assign_students')): ?>
                                    <button onclick="openTransferModal(<?= $s['id'] ?>, '<?= addslashes($s['full_name']) ?>')" 
                                            class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded-lg transition-colors" title="Chuyển lớp">
                                        <span class="material-symbols-outlined text-[17px]">swap_horiz</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="py-16 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 opacity-40">group_off</span>
                            Chưa có học sinh nào được phân vào lớp này
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Chuyển Lớp Cho Học Sinh -->
<div id="transferModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Chuyển Lớp Cho Học Sinh</h3>
                    <p class="text-[11px] text-slate-400">Điều chuyển học sinh sang lớp học khác</p>
                </div>
            </div>
            <button onclick="closeModal('transferModal')" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form onsubmit="handleTransfer(event)" class="space-y-4 text-xs">
            <input type="hidden" name="student_id" id="transferStudentId">
            <div class="bg-blue-50 dark:bg-blue-950/40 p-3 rounded-2xl border border-blue-100 dark:border-blue-900/60">
                <p class="text-slate-500 dark:text-slate-400 text-[11px]">Đang chuyển học sinh:</p>
                <p class="font-bold text-blue-700 dark:text-blue-300 text-sm mt-0.5" id="transferStudentName"></p>
            </div>
            <div class="space-y-1.5">
                <label class="block font-bold text-slate-700 dark:text-slate-300">Chọn Lớp Đích <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <select name="target_class_id" required class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 font-bold appearance-none outline-none cursor-pointer">
                        <?php foreach ($allClasses as $ac): ?>
                            <?php if ($ac['id'] != $class['id']): ?>
                            <option value="<?= $ac['id'] ?>"><?= htmlspecialchars($ac['name']) ?> (Khối <?= htmlspecialchars($ac['grade_name'] ?? '') ?>)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                </div>
            </div>
            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('transferModal')" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl font-bold hover:bg-slate-50 text-slate-700 dark:text-slate-200 transition-colors">Hủy</button>
                <button type="submit" id="btnSubmitTransfer" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-all">Xác Nhận Chuyển</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
const CLASS_INFO = <?= json_encode($class, JSON_UNESCAPED_UNICODE) ?>;
const CLASS_SUBJECTS = <?= json_encode($classSubjects, JSON_UNESCAPED_UNICODE) ?>;
const CLASS_STUDENTS = <?= json_encode($students, JSON_UNESCAPED_UNICODE) ?>;

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
    const btn = document.getElementById('btnSubmitTransfer');
    btn.disabled = true;
    btn.textContent = 'Đang chuyển...';

    const data = Object.fromEntries(new FormData(e.target).entries());
    const res = await apiPost('<?= BASE_URL ?>/classes/transfer-student', data);
    if (res) {
        closeModal('transferModal');
        setTimeout(() => location.reload(), 700);
    } else {
        btn.disabled = false;
        btn.textContent = 'Xác Nhận Chuyển';
    }
}

// =============================================================================
// XUẤT FILE EXCEL BẢNG ĐIỂM THEO DANH SÁCH MÔN HỌC RIÊNG CỦA LỚP NÀY
// =============================================================================
function exportClassToExcel() {
    const today = new Date();
    const dateStr = today.toLocaleDateString('vi-VN');
    const fileDate = today.toISOString().slice(0, 10);

    const className = CLASS_INFO.name || 'Lớp Học';
    const classCode = CLASS_INFO.code || '';
    const teacherName = CLASS_INFO.homeroom_teacher_name || 'Chưa phân công';
    const roomNumber = CLASS_INFO.room_number || 'Chưa xếp';

    // 1. Dòng tiêu đề và thông tin lớp
    const rows = [
        ["SỞ GIÁO DỤC VÀ ĐÀO TẠO TP. HỒ CHÍ MINH", "", "", "", "CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM"],
        ["TRƯỜNG TIỂU HỌC & THCS EDUMANAGE", "", "", "", "Độc lập - Tự do - Hạnh phúc"],
        ["BỘ PHẬN ĐÀO TẠO & HỌC VỤ", "", "", "", "-------------------------"],
        [""],
        [`BẢNG THEO DÕI ĐIỂM SỐ & KẾT QUẢ HỌC TẬP - ${className.toUpperCase()}`],
        [`Mã lớp: ${classCode}   |   GVCN: ${teacherName}   |   Phòng: ${roomNumber}   |   Sĩ số: ${CLASS_STUDENTS.length} học sinh   |   Tổng số môn: ${CLASS_SUBJECTS.length} môn`],
        [`Ngày xuất bảng điểm: ${dateStr}`],
        [""]
    ];

    // 2. Dòng tiêu đề các cột: Cột học sinh + Từng cột môn học riêng của lớp + Cột tổng kết
    const headers = [
        "STT",
        "Mã Học Sinh",
        "Họ và Tên Học Sinh",
        "Giới Tính",
        "Ngày Sinh"
    ];

    // Thêm từng môn học của lớp vào cột riêng biệt
    CLASS_SUBJECTS.forEach(sb => {
        headers.push(`${sb.name} (${sb.code})`);
    });

    headers.push("Điểm TB", "Xếp Loại", "Ghi Chú");
    rows.push(headers);

    // 3. Dữ liệu từng học sinh
    if (CLASS_STUDENTS.length > 0) {
        CLASS_STUDENTS.forEach((s, idx) => {
            const row = [
                idx + 1,
                s.student_code || '',
                s.full_name || '',
                s.gender === 'female' ? 'Nữ' : 'Nam',
                s.dob ? s.dob.split('-').reverse().join('/') : ''
            ];

            // Ô điểm trống sẵn sàng cho giáo viên nhập hoặc theo dõi
            CLASS_SUBJECTS.forEach(() => {
                row.push('');
            });

            row.push('', '', ''); // Điểm TB, Xếp loại, Ghi chú
            rows.push(row);
        });
    } else {
        const emptyRow = [1, "—", "Chưa có học sinh trong lớp", "—", "—"];
        CLASS_SUBJECTS.forEach(() => emptyRow.push(''));
        emptyRow.push('', '', '');
        rows.push(emptyRow);
    }

    // 4. Ký tên & xác nhận
    rows.push([""]);
    rows.push(["", "", "", "", `Ngày ${today.getDate()} tháng ${today.getMonth() + 1} năm ${today.getFullYear()}`]);
    rows.push(["", "NGƯỜI LẬP BẢNG", "", "", "GIÁO VIÊN CHỦ NHIỆM", "", "HIỆU TRƯỞNG"]);
    rows.push(["", "(Ký và ghi rõ họ tên)", "", "", "(Ký và ghi rõ họ tên)", "", "(Ký tên và đóng dấu)"]);

    const ws = XLSX.utils.aoa_to_sheet(rows);

    // 5. Định dạng độ rộng cột
    const colWidths = [
        { wch: 6 },  // STT
        { wch: 14 }, // Mã HS
        { wch: 25 }, // Họ tên
        { wch: 10 }, // Giới tính
        { wch: 14 }  // Ngày sinh
    ];
    CLASS_SUBJECTS.forEach(() => colWidths.push({ wch: 16 })); // Các môn học
    colWidths.push({ wch: 12 }, { wch: 14 }, { wch: 20 });
    ws['!cols'] = colWidths;

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, `Diem_${classCode}`);
    XLSX.writeFile(wb, `Bang_Diem_Lop_${classCode}_(${CLASS_SUBJECTS.length}_Mon)_${fileDate}.xlsx`);
    showToast(`Đã xuất bảng điểm lớp ${className} với ${CLASS_SUBJECTS.length} môn học!`, 'success');
}
</script>
