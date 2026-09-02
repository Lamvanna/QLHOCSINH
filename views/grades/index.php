<?php // views/grades/index.php ?>
<?php
$studentsList = $gradeData['students'] ?? [];
$components = $gradeData['components'] ?? [];

$scoresList = [];
$passCount = 0;
$goodCount = 0;
$totalGraded = 0;
$isAllLocked = count($studentsList) > 0 && !in_array(false, array_column($studentsList, 'is_locked'));

foreach ($studentsList as $st) {
    if ($st['average'] !== null) {
        $scoresList[] = (float)$st['average'];
        $totalGraded++;
        if ($st['average'] >= 5.0) $passCount++;
        if ($st['average'] >= 8.0) $goodCount++;
    }
}
$classAvg = count($scoresList) > 0 ? round(array_sum($scoresList) / count($scoresList), 2) : 0;
$passRate = $totalGraded > 0 ? round(($passCount / $totalGraded) * 100, 1) : 0;
?>
<div class="space-y-6">

    <!-- Header Hero -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-60 h-60 bg-blue-500/10 rounded-full blur-3xl -ml-10 -mb-10"></div>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-2 border border-emerald-500/30">
                    <span class="material-symbols-outlined text-[14px]">edit_document</span> Sổ Điểm Điện Tử
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Quản Lý Điểm Số</h1>
                <p class="text-xs text-slate-300 mt-1">Bảng nhập điểm lưới, tính điểm trung bình môn tự động & cơ chế chốt điểm bảo mật</p>
            </div>

            <!-- Top Actions -->
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="exportGradesToExcel()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-[16px] text-emerald-400">file_download</span>
                    <span>Xuất Excel</span>
                </button>
                <button onclick="saveGrades(true)" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-[16px] text-amber-400">save</span>
                    <span>Lưu Nháp</span>
                </button>
                <button onclick="saveGrades(false)" class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span>Lưu Điểm Chính Thức</span>
                </button>
                
                <?php if (Permission::can('grades.lock')): ?>
                    <?php if ($isAllLocked): ?>
                    <button onclick="unlockGrades()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-amber-300 bg-amber-950/60 hover:bg-amber-900 border border-amber-500/40 rounded-xl transition-all">
                        <span class="material-symbols-outlined text-[16px]">lock_open</span>
                        <span>Mở Khóa</span>
                    </button>
                    <?php else: ?>
                    <button onclick="lockGrades()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-300 bg-slate-800/80 hover:bg-slate-800 border border-slate-700 rounded-xl transition-all">
                        <span class="material-symbols-outlined text-[16px]">lock</span>
                        <span>Chốt & Khóa</span>
                    </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filter Bar & Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <!-- Filter Form -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <form method="GET" action="<?= BASE_URL ?>/grades" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold uppercase text-slate-400">1. Lớp Học</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">class</span>
                        <select name="class_id" onchange="this.form.submit()" class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold appearance-none cursor-pointer">
                            <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold uppercase text-slate-400">2. Môn Học</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">menu_book</span>
                        <select name="subject_id" onchange="this.form.submit()" class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold appearance-none cursor-pointer">
                            <?php foreach ($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $selectedSubject == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold uppercase text-slate-400">3. Học Kỳ</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">calendar_month</span>
                        <select name="semester_id" onchange="this.form.submit()" class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold appearance-none cursor-pointer">
                            <?php foreach ($semesters as $sem): ?>
                            <option value="<?= $sem['id'] ?>" <?= $selectedSemester == $sem['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Quick Stats Cards -->
        <div class="lg:col-span-4 grid grid-cols-3 gap-3">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 text-center shadow-sm card-subtle flex flex-col justify-between">
                <p class="text-[10px] font-extrabold uppercase text-slate-400">ĐTB Lớp</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 my-1" id="kpiClassAvg"><?= $classAvg ?></p>
                <p class="text-[10px] text-slate-400 font-semibold"><?= count($scoresList) ?>/<?= count($studentsList) ?> HS</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 text-center shadow-sm card-subtle flex flex-col justify-between">
                <p class="text-[10px] font-extrabold uppercase text-slate-400">Tỉ Lệ Đạt</p>
                <p class="text-2xl font-black text-blue-600 dark:text-blue-400 my-1"><?= $passRate ?>%</p>
                <p class="text-[10px] text-slate-400 font-semibold">≥ 5.0 điểm</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 text-center shadow-sm card-subtle flex flex-col justify-between">
                <p class="text-[10px] font-extrabold uppercase text-slate-400">Giỏi / Xuất Sắc</p>
                <p class="text-2xl font-black text-violet-600 dark:text-violet-400 my-1"><?= $goodCount ?></p>
                <p class="text-[10px] text-slate-400 font-semibold">≥ 8.0 điểm</p>
            </div>
        </div>
    </div>

    <!-- Grade Matrix Grid -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">table_chart</span>
                <span class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Bảng Điểm Chi Tiết (Sĩ số: <?= count($studentsList) ?> học sinh)</span>
            </div>
            <?php if ($isAllLocked): ?>
            <span class="badge badge-rose">
                <span class="material-symbols-outlined text-[13px]">lock</span> Đã Khóa Sổ Điểm
            </span>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs table-premium" id="gradesTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase font-extrabold text-[10px] tracking-widest">
                        <th class="px-4 py-3 w-12 text-center">STT</th>
                        <th class="px-4 py-3 w-28">Mã HS</th>
                        <th class="px-4 py-3 min-w-[180px]">Họ và Tên</th>
                        
                        <?php foreach ($components as $comp): ?>
                        <th class="px-3 py-3 text-center min-w-[90px]">
                            <div class="font-extrabold text-slate-700 dark:text-slate-200"><?= htmlspecialchars($comp['name']) ?></div>
                            <div class="text-[9px] text-emerald-600 font-semibold uppercase mt-0.5">HS ×<?= $comp['weight'] ?></div>
                        </th>
                        <?php endforeach; ?>

                        <th class="px-4 py-3 text-center min-w-[90px] font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50/70 dark:bg-emerald-950/30">
                            ĐTB Môn
                        </th>
                        <th class="px-4 py-3 text-center w-28">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($studentsList as $idx => $st): ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors grade-row" data-row-idx="<?= $idx ?>">
                        <td class="px-4 py-3 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                        <td class="px-4 py-3 font-mono font-black text-emerald-600 dark:text-emerald-400"><?= htmlspecialchars($st['student_code']) ?></td>
                        <td class="px-4 py-3">
                            <a href="<?= BASE_URL ?>/students/<?= $st['student_id'] ?>" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors">
                                <?= htmlspecialchars($st['full_name']) ?>
                            </a>
                        </td>

                        <?php foreach ($components as $comp): 
                            $cId = $comp['id'];
                            $scoreItem = $st['scores'][$cId] ?? null;
                            $scoreVal = $scoreItem['score'] ?? '';
                            $isLocked = $scoreItem['is_locked'] ?? false;
                        ?>
                        <td class="px-2 py-2 text-center">
                            <input type="number" step="0.1" min="0" max="10" 
                                   class="score-input w-16 text-center font-mono font-black text-xs py-1.5 border border-slate-200 dark:border-slate-700 rounded-xl transition-all
                                          <?= $isLocked ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500' ?>"
                                   data-student-id="<?= $st['student_id'] ?>"
                                   data-component-id="<?= $cId ?>"
                                   data-weight="<?= $comp['weight'] ?>"
                                   value="<?= $scoreVal !== '' ? $scoreVal : '' ?>"
                                   <?= $isLocked ? 'readonly' : '' ?>
                                   oninput="calculateRowAverage(this)">
                        </td>
                        <?php endforeach; ?>

                        <!-- Row Calculated Average -->
                        <td class="px-4 py-3 text-center font-mono font-black text-sm bg-emerald-50/40 dark:bg-emerald-950/20 row-avg-cell text-emerald-600 dark:text-emerald-400">
                            <?= $st['average'] !== null ? number_format((float)$st['average'], 2) : '—' ?>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <?php if ($st['is_locked']): ?>
                                <span class="badge badge-slate">Đã khóa</span>
                            <?php elseif ($st['is_draft']): ?>
                                <span class="badge badge-amber">Bản nháp</span>
                            <?php else: ?>
                                <span class="badge badge-emerald">Đã duyệt</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($studentsList)): ?>
                    <tr>
                        <td colspan="<?= count($components) + 5 ?>" class="py-20 text-center text-slate-400">
                            <span class="material-symbols-outlined text-5xl block mb-2 opacity-30">rule</span>
                            Không có dữ liệu học sinh trong lớp này
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Dynamic client-side calculation
function calculateRowAverage(inputElem) {
    const row = inputElem.closest('tr');
    const inputs = row.querySelectorAll('.score-input');
    let totalScoreWeighted = 0;
    let totalWeight = 0;
    let hasAnyScore = false;

    inputs.forEach(inp => {
        const val = parseFloat(inp.value);
        const weight = parseFloat(inp.dataset.weight || 1);
        if (!isNaN(val) && val >= 0 && val <= 10) {
            totalScoreWeighted += val * weight;
            totalWeight += weight;
            hasAnyScore = true;
            
            // Visual highlight
            if (val < 5.0) {
                inp.classList.add('border-rose-400', 'text-rose-600');
                inp.classList.remove('border-emerald-400', 'text-emerald-600');
            } else if (val >= 8.0) {
                inp.classList.add('border-emerald-400', 'text-emerald-600');
                inp.classList.remove('border-rose-400', 'text-rose-600');
            } else {
                inp.classList.remove('border-rose-400', 'text-rose-600', 'border-emerald-400', 'text-emerald-600');
            }
        }
    });

    const avgCell = row.querySelector('.row-avg-cell');
    if (hasAnyScore && totalWeight > 0) {
        const avg = (totalScoreWeighted / totalWeight).toFixed(2);
        avgCell.textContent = avg;
        if (parseFloat(avg) < 5.0) {
            avgCell.className = 'px-4 py-3 text-center font-mono font-black text-sm bg-rose-50/40 dark:bg-rose-950/20 row-avg-cell text-rose-600';
        } else {
            avgCell.className = 'px-4 py-3 text-center font-mono font-black text-sm bg-emerald-50/40 dark:bg-emerald-950/20 row-avg-cell text-emerald-600 dark:text-emerald-400';
        }
    } else {
        avgCell.textContent = '—';
    }
}

async function saveGrades(isDraft = false) {
    const inputs = document.querySelectorAll('.score-input');
    const entries = [];
    inputs.forEach(input => {
        if (input.value !== '') {
            entries.push({
                student_id: input.dataset.studentId,
                component_id: input.dataset.componentId,
                score: input.value
            });
        }
    });

    const res = await apiPost('<?= BASE_URL ?>/grades/save', {
        class_id: <?= (int)$selectedClass ?>,
        subject_id: <?= (int)$selectedSubject ?>,
        semester_id: <?= (int)$selectedSemester ?>,
        is_draft: isDraft,
        entries: entries
    });

    if (res) {
        setTimeout(() => location.reload(), 700);
    }
}

async function lockGrades() {
    if (!confirm('Khóa bảng điểm sẽ ngăn giáo viên chỉnh sửa. Bạn có chắc chắn muốn chốt điểm?')) return;
    const res = await apiPost('<?= BASE_URL ?>/grades/lock', {
        class_id: <?= (int)$selectedClass ?>,
        subject_id: <?= (int)$selectedSubject ?>,
        semester_id: <?= (int)$selectedSemester ?>
    });
    if (res) setTimeout(() => location.reload(), 700);
}

async function unlockGrades() {
    if (!confirm('Mở khóa bảng điểm cho phép giáo viên cập nhật lại điểm số?')) return;
    const res = await apiPost('<?= BASE_URL ?>/grades/unlock', {
        class_id: <?= (int)$selectedClass ?>,
        subject_id: <?= (int)$selectedSubject ?>,
        semester_id: <?= (int)$selectedSemester ?>
    });
    if (res) setTimeout(() => location.reload(), 700);
}

function exportGradesToExcel() {
    const table = document.getElementById('gradesTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Bảng Điểm"});
    XLSX.writeFile(wb, "Bang_Diem_Lop_<?= (int)$selectedClass ?>_Mon_<?= (int)$selectedSubject ?>.xlsx");
}
</script>
