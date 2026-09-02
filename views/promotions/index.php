<?php // views/promotions/index.php ?>
<?php
$studentsList = $promotionData['students'] ?? [];
$classInfo = $promotionData['class'] ?? [];
$nextClass = $promotionData['next_class'] ?? null;
$targetClasses = $promotionData['target_classes'] ?? [];

$promotedCount = 0;
$remedialCount = 0;
$retainedCount = 0;
$graduatedCount = 0;

foreach ($studentsList as $st) {
    $stStatus = $st['promotion_status'] ?? $st['recommended_status'] ?? 'promoted';
    if ($stStatus === 'promoted') $promotedCount++;
    elseif ($stStatus === 'remedial') $remedialCount++;
    elseif ($stStatus === 'retained') $retainedCount++;
    elseif ($stStatus === 'graduated') $graduatedCount++;
}
$totalStudents = count($studentsList);
$promotionRate = $totalStudents > 0 ? round((($promotedCount + $graduatedCount) / $totalStudents) * 100, 1) : 0;
$isFinalClass = ($classInfo['id'] ?? 1) >= 5;
?>
<div class="space-y-6">

    <!-- Header Hero -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -mr-24 -mt-24"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl -ml-12 -mb-12"></div>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-2 border border-emerald-500/30">
                    <span class="material-symbols-outlined text-[14px]">school</span> Tổng Kết Năm Học
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Hội Đồng Xét Lên Lớp & Tốt Nghiệp</h1>
                <p class="text-xs text-slate-300 mt-1">Đánh giá kết quả học tập cho 5 lớp (Lớp 1, Lớp 2, Lớp 3, Lớp 4, Lớp 5), xét điều kiện lên lớp thẳng hoặc tốt nghiệp</p>
            </div>

            <!-- Top Actions -->
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="exportPromotionToExcel()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-[16px] text-emerald-400">file_download</span>
                    <span>Xuất Biên Bản Excel</span>
                </button>
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-[16px] text-blue-400">print</span>
                    <span>In Biên Bản</span>
                </button>
                
                <?php if (Permission::can('promotions.evaluate')): ?>
                <button onclick="savePromotionDecisions()" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Lưu Kết Quả Xét Duyệt</span>
                </button>
                <?php endif; ?>

                <?php if (Permission::can('promotions.execute')): ?>
                <button onclick="openExecuteModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-black text-amber-900 bg-gradient-to-r from-amber-400 to-yellow-400 hover:from-amber-300 hover:to-yellow-300 rounded-xl shadow-lg shadow-amber-500/30 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                    <span>Chốt & Chuyển Lớp Hàng Loạt</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filter & KPIs Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Filter Bar (4 spans) -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Chọn Lớp & Năm Học Cần Xét</h3>
            <form method="GET" action="<?= BASE_URL ?>/promotions" class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Năm Học Xét Duyệt</label>
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                        <?php foreach ($years as $y): ?>
                        <option value="<?= $y['id'] ?>" <?= $selectedYear == $y['id'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lớp Học</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-emerald-600">
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="pt-1">
                    <p class="text-[11px] text-slate-400">
                        Lớp hiện tại: <strong class="text-emerald-600"><?= htmlspecialchars($classInfo['name'] ?? '—') ?></strong> 
                        <?= $nextClass ? '→ Lớp tiếp theo: <strong class="text-blue-600">' . htmlspecialchars($nextClass['name']) . '</strong>' : '→ <span class="badge badge-emerald">Lớp cuối cấp (Xét tốt nghiệp Tiểu học)</span>' ?>
                    </p>
                </div>
            </form>
        </div>

        <!-- 4 KPI Cards (8 spans) -->
        <div class="lg:col-span-8 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Sĩ Số Lớp</span>
                <div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white"><?= $totalStudents ?></div>
                    <div class="text-[11px] text-slate-400 font-semibold mt-1">Học sinh</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-600">Được Lên Lớp</span>
                <div>
                    <div class="text-3xl font-black text-emerald-600" id="kpiPromotedCount"><?= $promotedCount ?></div>
                    <div class="text-[11px] font-bold text-emerald-600 mt-1">Tỷ lệ: <?= $promotionRate ?>%</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-600">Rèn Luyện Hè / Thi Lại</span>
                <div>
                    <div class="text-3xl font-black text-amber-600" id="kpiRemedialCount"><?= $remedialCount ?></div>
                    <div class="text-[11px] text-amber-600 font-semibold mt-1">ĐTB 3.5 - 4.9</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle">
                <span class="text-[10px] font-extrabold uppercase tracking-widest <?= !$isFinalClass ? 'text-rose-600' : 'text-purple-600' ?>">
                    <?= !$isFinalClass ? 'Ở Lại Lớp (Lưu Ban)' : 'Tốt Nghiệp' ?>
                </span>
                <div>
                    <div class="text-3xl font-black <?= !$isFinalClass ? 'text-rose-600' : 'text-purple-600' ?>" id="kpiRetainedCount">
                        <?= !$isFinalClass ? $retainedCount : $graduatedCount ?>
                    </div>
                    <div class="text-[11px] text-slate-400 font-semibold mt-1">
                        <?= !$isFinalClass ? 'ĐTB < 3.5' : 'Hoàn thành CT' ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Rule Summary Banner -->
    <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-3 text-slate-700 dark:text-slate-300">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[18px]">verified</span>
            </div>
            <div>
                <span class="font-extrabold text-emerald-800 dark:text-emerald-300">Quy tắc lên lớp: </span>
                <span>Điểm trung bình các môn &ge; 5.0, Hạnh kiểm Khá/Tốt &rarr; Lên lớp tiếp theo (Lớp 1 &rarr; 2 &rarr; 3 &rarr; 4 &rarr; 5 &rarr; Tốt nghiệp).</span>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button onclick="autoApplyRecommendations()" class="px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 font-bold rounded-xl border border-emerald-300 shadow-sm transition-all">
                Áp Dụng Đề Xuất Tự Động
            </button>
        </div>
    </div>

    <!-- Evaluation Table Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/60 dark:bg-slate-800/30">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Bảng Danh Sách & Quyết Định Xét Lên Lớp — <?= htmlspecialchars($classInfo['name'] ?? '') ?></h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Giáo viên chủ nhiệm và hội đồng có thể điều chỉnh quyết định và chọn lớp mục tiêu</p>
            </div>
            <span class="text-xs font-bold text-slate-400"><?= $totalStudents ?> Học sinh</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs" id="promotionsTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase font-extrabold text-[10px] tracking-widest">
                        <th class="p-3.5 pl-5 w-12 text-center">STT</th>
                        <th class="p-3.5">Mã HS</th>
                        <th class="p-3.5">Học Sinh</th>
                        <th class="p-3.5 text-center">ĐTB Cả Năm</th>
                        <th class="p-3.5 text-center">Hạnh Kiểm</th>
                        <th class="p-3.5 text-center">Đề Xuất HT</th>
                        <th class="p-3.5">Quyết Định Xét Duyệt *</th>
                        <th class="p-3.5">Lớp Chuyển Đến</th>
                        <th class="p-3.5 pr-5">Ghi Chú</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    <?php if (!empty($studentsList)): ?>
                        <?php foreach ($studentsList as $idx => $st): 
                            $currentStatus = $st['promotion_status'] ?? $st['recommended_status'] ?? 'promoted';
                            $recStatus = $st['recommended_status'] ?? 'promoted';
                        ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors student-row" data-student-id="<?= $st['id'] ?>" data-avg-score="<?= $st['avg_score'] ?? '' ?>">
                            <td class="p-3.5 pl-5 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="p-3.5 font-mono font-bold text-emerald-600"><?= htmlspecialchars($st['student_code']) ?></td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($st['full_name']) ?></span>
                                <?php if (!empty($st['khmer_name'])): ?>
                                <span class="text-[10px] text-purple-600 block"><?= htmlspecialchars($st['khmer_name']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 text-center font-mono font-black text-sm <?= ($st['avg_score'] ?? 0) >= 8.0 ? 'text-emerald-600' : (($st['avg_score'] ?? 0) >= 5.0 ? 'text-blue-600' : 'text-rose-600') ?>">
                                <?= $st['avg_score'] !== null ? number_format((float)$st['avg_score'], 2) : '—' ?>
                            </td>
                            <td class="p-3.5 text-center">
                                <select class="conduct-select px-2.5 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold" onchange="recalculateKPIs()">
                                    <option value="Tot" <?= ($st['conduct'] ?? 'Tot') === 'Tot' ? 'selected' : '' ?>>Tốt</option>
                                    <option value="Kha" <?= ($st['conduct'] ?? '') === 'Kha' ? 'selected' : '' ?>>Khá</option>
                                    <option value="TrungBinh" <?= ($st['conduct'] ?? '') === 'TrungBinh' ? 'selected' : '' ?>>Trung Bình</option>
                                    <option value="Yeu" <?= ($st['conduct'] ?? '') === 'Yeu' ? 'selected' : '' ?>>Yếu</option>
                                </select>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="rec-badge badge <?= $recStatus === 'promoted' ? 'badge-emerald' : ($recStatus === 'graduated' ? 'badge-blue' : ($recStatus === 'remedial' ? 'badge-amber' : 'badge-rose')) ?>">
                                    <?= match($recStatus) {
                                        'promoted' => 'Lên lớp',
                                        'graduated' => 'Tốt nghiệp',
                                        'remedial' => 'Rèn luyện hè',
                                        'retained' => 'Ở lại lớp',
                                        default => 'Chờ duyệt'
                                    } ?>
                                </span>
                            </td>
                            <td class="p-3.5">
                                <select class="status-select px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold" onchange="recalculateKPIs()">
                                    <option value="promoted" <?= $currentStatus === 'promoted' ? 'selected' : '' ?>>🟢 Được Lên Lớp</option>
                                    <option value="remedial" <?= $currentStatus === 'remedial' ? 'selected' : '' ?>>🟡 Rèn Luyện Hè / Thi Lại</option>
                                    <option value="retained" <?= $currentStatus === 'retained' ? 'selected' : '' ?>>🔴 Ở Lại Lớp (Lưu Ban)</option>
                                    <option value="graduated" <?= $currentStatus === 'graduated' ? 'selected' : '' ?>>🎓 Xét Tốt Nghiệp</option>
                                </select>
                            </td>
                            <td class="p-3.5">
                                <select class="target-class-select px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
                                    <option value="">-- Chọn lớp mới --</option>
                                    <?php foreach ($targetClasses as $tc): ?>
                                    <option value="<?= $tc['id'] ?>" <?= ($st['target_class_id'] == $tc['id']) ? 'selected' : '' ?>><?= htmlspecialchars($tc['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="p-3.5 pr-5">
                                <input type="text" class="notes-input w-full px-2.5 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs" placeholder="Ghi chú..." value="<?= htmlspecialchars($st['notes'] ?? '') ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="p-12 text-center text-slate-400">Không có học sinh nào trong lớp này.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Xác Nhận Thực Hiện Chuyển Lớp Hàng Loạt -->
<div id="executeModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">warning</span>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Xác Nhận Chuyển Lớp Hàng Loạt</h3>
            </div>
            <button onclick="closeModal('executeModal')" class="text-slate-400 hover:text-slate-700">&times;</button>
        </div>
        
        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
            Hệ thống sẽ cập nhật trạng thái học sinh <strong><?= htmlspecialchars($classInfo['name'] ?? '') ?></strong>:
            <br>• Học sinh <strong>Được Lên Lớp</strong> sẽ chuyển sang Lớp mới đã chọn.
            <br>• Học sinh <strong>Tốt nghiệp</strong> sẽ chuyển trạng thái sang <code>Đã tốt nghiệp</code>.
            <br>• Học sinh <strong>Ở lại lớp</strong> sẽ tiếp tục học lại lớp hiện tại ở năm học mới.
        </p>

        <div class="space-y-1.5 text-xs">
            <label class="font-bold text-slate-700 dark:text-slate-300">Chuyển Sang Niên Khóa Kế Tiếp *</label>
            <select id="nextYearSelect" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y['id'] ?>"><?= htmlspecialchars($y['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeModal('executeModal')" class="px-4 py-2 text-xs font-bold border rounded-xl">Hủy</button>
            <button type="button" onclick="confirmExecutePromotion()" class="px-5 py-2 text-xs font-black bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-xl shadow-lg shadow-amber-500/30">
                Xác Nhận & Thực Hiện
            </button>
        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
function openExecuteModal() { openModal('executeModal'); }

function recalculateKPIs() {
    const rows = document.querySelectorAll('.student-row');
    let p = 0, rem = 0, ret = 0, grad = 0;
    rows.forEach(r => {
        const val = r.querySelector('.status-select').value;
        if (val === 'promoted') p++;
        else if (val === 'remedial') rem++;
        else if (val === 'retained') ret++;
        else if (val === 'graduated') grad++;
    });

    if (document.getElementById('kpiPromotedCount')) document.getElementById('kpiPromotedCount').textContent = p;
    if (document.getElementById('kpiRemedialCount')) document.getElementById('kpiRemedialCount').textContent = rem;
    if (document.getElementById('kpiRetainedCount')) document.getElementById('kpiRetainedCount').textContent = <?= $isFinalClass ? 'grad' : 'ret' ?>;
}

function autoApplyRecommendations() {
    const rows = document.querySelectorAll('.student-row');
    rows.forEach(r => {
        const avg = parseFloat(r.dataset.avgScore || 0);
        const conduct = r.querySelector('.conduct-select').value;
        const statusSelect = r.querySelector('.status-select');

        <?php if ($isFinalClass): ?>
        if (avg >= 5.0 && ['Tot', 'Kha'].includes(conduct)) {
            statusSelect.value = 'graduated';
        } else if (avg >= 3.5) {
            statusSelect.value = 'remedial';
        } else {
            statusSelect.value = 'retained';
        }
        <?php else: ?>
        if (avg >= 5.0 && ['Tot', 'Kha', 'TrungBinh'].includes(conduct)) {
            statusSelect.value = 'promoted';
        } else if (avg >= 3.5) {
            statusSelect.value = 'remedial';
        } else {
            statusSelect.value = 'retained';
        }
        <?php endif; ?>
    });
    recalculateKPIs();
    showToast('Đã áp dụng đề xuất tự động!', 'success');
}

async function savePromotionDecisions() {
    const rows = document.querySelectorAll('.student-row');
    const records = [];
    rows.forEach(r => {
        records.push({
            student_id: r.dataset.studentId,
            avg_score: r.dataset.avgScore,
            conduct: r.querySelector('.conduct-select').value,
            promotion_status: r.querySelector('.status-select').value,
            target_class_id: r.querySelector('.target-class-select').value,
            notes: r.querySelector('.notes-input').value
        });
    });

    const res = await apiPost('<?= BASE_URL ?>/promotions/save', {
        class_id: <?= (int)$selectedClass ?>,
        academic_year_id: <?= (int)$selectedYear ?>,
        records: records
    });

    if (res) {
        setTimeout(() => location.reload(), 700);
    }
}

async function confirmExecutePromotion() {
    const nextYearId = document.getElementById('nextYearSelect').value;
    const res = await apiPost('<?= BASE_URL ?>/promotions/execute', {
        class_id: <?= (int)$selectedClass ?>,
        academic_year_id: <?= (int)$selectedYear ?>,
        next_year_id: nextYearId
    });

    if (res) {
        closeModal('executeModal');
        setTimeout(() => location.reload(), 1000);
    }
}

function exportPromotionToExcel() {
    const table = document.getElementById("promotionsTable");
    const wb = XLSX.utils.table_to_book(table, {sheet: "XetLenLop_EduManage"});
    XLSX.writeFile(wb, "Bien_Ban_Xet_Len_Lop_<?= (int)$selectedClass ?>.xlsx");
}
</script>
