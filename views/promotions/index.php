<?php // views/promotions/index.php ?>
<?php
$studentsList = $promotionData['students'] ?? [];
$classInfo = $promotionData['class'] ?? [];
$nextClass = $promotionData['next_class'] ?? null;
$targetClasses = $promotionData['target_classes'] ?? [];

$promotedCount = 0;
$retainedCount = 0;
$graduatedCount = 0;

foreach ($studentsList as $st) {
    $stStatus = $st['promotion_status'] ?? $st['recommended_status'] ?? 'promoted';
    if ($stStatus === 'promoted') $promotedCount++;
    elseif ($stStatus === 'retained') $retainedCount++;
    elseif ($stStatus === 'graduated') $graduatedCount++;
    elseif ($stStatus === 'remedial') $retainedCount++; // Fallback
}
$totalStudents = count($studentsList);
$promotionRate = $totalStudents > 0 ? round((($promotedCount + $graduatedCount) / $totalStudents) * 100, 1) : 0;
$isFinalClass = ($classInfo['id'] ?? 1) >= 5;
?>
<div class="space-y-5">

    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 md:p-7 text-white relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -ml-10 -mb-10"></div>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-2 border border-emerald-500/30">
                    <span class="material-symbols-outlined text-[14px]">school</span> Tổng Kết & Đánh Giá Cuối Năm
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Hội Đồng Xét Lên Lớp & Tốt Nghiệp</h1>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                    Đánh giá kết quả học tập & rèn luyện của học sinh <strong class="text-white"><?= htmlspecialchars($classInfo['name'] ?? 'Lớp') ?></strong>, xét điều kiện lên lớp tiếp theo hoặc công nhận hoàn thành chương trình Tiểu học.
                </p>
            </div>

            <!-- Top Actions -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="<?= BASE_URL ?>/promotions/print?class_id=<?= (int)$selectedClass ?>&academic_year_id=<?= (int)$selectedYear ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 rounded-xl transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-blue-400">print</span>
                    <span>In Biên Bản (A4)</span>
                </a>

                <button onclick="exportPromotionToExcel()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 rounded-xl transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-emerald-400">file_download</span>
                    <span>Xuất Excel</span>
                </button>
                
                <?php if (Permission::can('promotions.evaluate')): ?>
                <button onclick="savePromotionDecisions()" id="btnSaveDecisions" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Lưu Kết Quả Xét Duyệt</span>
                </button>
                <?php endif; ?>

                <?php if (Permission::can('promotions.execute')): ?>
                <button onclick="openExecuteModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-black text-amber-950 bg-gradient-to-r from-amber-400 to-yellow-400 hover:from-amber-300 hover:to-yellow-300 rounded-xl shadow-lg shadow-amber-500/30 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                    <span>Chốt & Chuyển Lớp</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Combined Filter + Mini KPI Bento Card (Thanh Lọc + Thống Kê Gọn Đẹp) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        
        <!-- Filter Row -->
        <form method="GET" action="<?= BASE_URL ?>/promotions" id="promotionFilterForm" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Lớp Học</label>
                <div class="relative">
                    <select name="class_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-emerald-500 shadow-sm cursor-pointer">
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Năm Học Xét Duyệt</label>
                <div class="relative">
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 shadow-sm cursor-pointer">
                        <?php foreach ($years as $y): ?>
                        <option value="<?= $y['id'] ?>" <?= $selectedYear == $y['id'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Tìm Kiếm Nhanh</label>
                <div class="relative">
                    <input type="text" id="tableSearch" onkeyup="filterStudentRows()" placeholder="Tìm theo tên hoặc mã HS..." class="w-full px-3.5 py-2.5 pl-9 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 shadow-sm transition-all">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-[17px] text-slate-400">search</span>
                </div>
            </div>
        </form>

        <!-- Status Transition Info Bar -->
        <div class="px-5 py-2.5 bg-slate-50 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-emerald-600">swap_horiz</span>
                <span class="text-slate-600 dark:text-slate-300">
                    Lớp hiện tại: <strong class="text-emerald-700 dark:text-emerald-400"><?= htmlspecialchars($classInfo['name'] ?? '—') ?></strong>
                </span>
                <span class="text-slate-400">&rarr;</span>
                <span class="text-slate-600 dark:text-slate-300">
                    <?= $nextClass 
                        ? 'Chuyển sang: <strong class="text-blue-600 dark:text-blue-400">' . htmlspecialchars($nextClass['name']) . '</strong>' 
                        : '<strong class="text-purple-600 dark:text-purple-400">Khối 5 (Xét công nhận tốt nghiệp Tiểu học)</strong>' ?>
                </span>
            </div>
            <div class="text-[11px] text-slate-400 font-medium">
                Cập nhật tự động theo kết quả xét duyệt
            </div>
        </div>

        <!-- 3 Mini KPI Bento Columns (Gọn gàng, vừa vặn, không bị trống) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 dark:divide-slate-800">
            
            <!-- KPI 1: Sĩ số -->
            <div class="p-4 sm:p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center shrink-0 shadow-inner">
                    <span class="material-symbols-outlined text-2xl">groups</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Sĩ Số Lớp</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white"><?= $totalStudents ?></span>
                        <span class="text-xs text-slate-500 font-semibold">Học sinh</span>
                    </div>
                </div>
            </div>

            <!-- KPI 2: Được lên lớp -->
            <div class="p-4 sm:p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-inner">
                    <span class="material-symbols-outlined text-2xl">verified</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-600 block">Được Lên Lớp</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="text-2xl font-black text-emerald-600" id="kpiPromotedCount"><?= $promotedCount ?></span>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800">
                            Tỷ lệ: <span id="kpiPromotionRate"><?= $promotionRate ?></span>%
                        </span>
                    </div>
                </div>
            </div>

            <!-- KPI 3: Ở lại lớp / Tốt nghiệp (NO Rèn luyện hè) -->
            <div class="p-4 sm:p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl <?= !$isFinalClass ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400' : 'bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400' ?> flex items-center justify-center shrink-0 shadow-inner">
                    <span class="material-symbols-outlined text-2xl"><?= !$isFinalClass ? 'person_cancel' : 'school' ?></span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest <?= !$isFinalClass ? 'text-rose-600' : 'text-purple-600' ?> block">
                        <?= !$isFinalClass ? 'Ở Lại Lớp (Lưu Ban)' : 'Được Tốt Nghiệp' ?>
                    </span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="text-2xl font-black <?= !$isFinalClass ? 'text-rose-600' : 'text-purple-600' ?>" id="kpiRetainedCount">
                            <?= !$isFinalClass ? $retainedCount : $graduatedCount ?>
                        </span>
                        <span class="text-xs text-slate-400 font-semibold">
                            <?= !$isFinalClass ? 'ĐTB < 5.0 hoặc HK Yếu' : 'Hoàn thành CT' ?>
                        </span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Rule Summary Compact Banner -->
    <div class="p-3.5 px-4.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/60 text-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-3 text-slate-700 dark:text-slate-300 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[17px]">info</span>
            </div>
            <div>
                <span class="font-extrabold text-emerald-800 dark:text-emerald-300">Tiêu chuẩn xét duyệt: </span>
                <span>Điểm trung bình các môn &ge; 5.0 và Hạnh kiểm Đạt (Tốt/Khá/Trung Bình) &rarr; <strong>Được lên lớp thẳng</strong> (hoặc Tốt nghiệp); Dưới 5.0 hoặc Hạnh kiểm Yếu &rarr; <strong>Ở lại lớp</strong>.</span>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button onclick="autoApplyRecommendations()" class="px-3.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-bold rounded-xl border border-emerald-300 dark:border-emerald-700 shadow-sm transition-all flex items-center gap-1.5 text-xs">
                <span class="material-symbols-outlined text-[15px]">auto_fix_high</span>
                <span>Áp Dụng Tự Động</span>
            </button>
        </div>
    </div>

    <!-- Evaluation Table Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-4 px-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/60 dark:bg-slate-800/30">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-emerald-600 text-lg">format_list_bulleted</span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Bảng Danh Sách & Quyết Định Xét Lên Lớp — <?= htmlspecialchars($classInfo['name'] ?? '') ?></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Giáo viên chủ nhiệm và Hội đồng xét duyệt có thể điều chỉnh quyết định và chọn lớp mục tiêu</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-xl">
                <?= $totalStudents ?> Học sinh
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs" id="promotionsTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase font-extrabold text-[10px] tracking-widest">
                        <th class="p-3.5 pl-5 w-12 text-center">STT</th>
                        <th class="p-3.5 w-24">Mã HS</th>
                        <th class="p-3.5">Họ và Tên Học Sinh</th>
                        <th class="p-3.5 text-center w-24">ĐTB Cả Năm</th>
                        <th class="p-3.5 text-center w-28">Hạnh Kiểm</th>
                        <th class="p-3.5 text-center w-28">Đề Xuất HT</th>
                        <th class="p-3.5 w-44">Quyết Định Xét Duyệt *</th>
                        <th class="p-3.5 w-40">Lớp Chuyển Đến</th>
                        <th class="p-3.5 pr-5">Ghi Chú</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    <?php if (!empty($studentsList)): ?>
                        <?php foreach ($studentsList as $idx => $st): 
                            $currentStatus = $st['promotion_status'] ?? $st['recommended_status'] ?? 'promoted';
                            if ($currentStatus === 'remedial') $currentStatus = 'retained'; // No remedial
                            $recStatus = $st['recommended_status'] ?? 'promoted';
                            if ($recStatus === 'remedial') $recStatus = 'retained';
                            $targetClassId = $st['target_class_id'] ?? ($nextClass['id'] ?? '');
                        ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors student-row" data-student-id="<?= $st['id'] ?>" data-avg-score="<?= $st['avg_score'] ?? '' ?>">
                            <td class="p-3.5 pl-5 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="p-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400"><?= htmlspecialchars($st['student_code']) ?></td>
                            <td class="p-3.5 student-name-cell">
                                <span class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($st['full_name']) ?></span>
                                <?php if (!empty($st['khmer_name'])): ?>
                                <span class="text-[10px] text-purple-600 block"><?= htmlspecialchars($st['khmer_name']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 text-center font-mono font-black text-sm <?= ($st['avg_score'] ?? 0) >= 8.0 ? 'text-emerald-600 dark:text-emerald-400' : (($st['avg_score'] ?? 0) >= 5.0 ? 'text-blue-600 dark:text-blue-400' : 'text-rose-600 dark:text-rose-400') ?>">
                                <?= $st['avg_score'] !== null ? number_format((float)$st['avg_score'], 2) : '—' ?>
                            </td>
                            <td class="p-3.5 text-center">
                                <select class="conduct-select px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500" onchange="recalculateKPIs()">
                                    <option value="Tot" <?= ($st['conduct'] ?? 'Tot') === 'Tot' ? 'selected' : '' ?>>Tốt</option>
                                    <option value="Kha" <?= ($st['conduct'] ?? '') === 'Kha' ? 'selected' : '' ?>>Khá</option>
                                    <option value="TrungBinh" <?= ($st['conduct'] ?? '') === 'TrungBinh' ? 'selected' : '' ?>>Trung Bình</option>
                                    <option value="Yeu" <?= ($st['conduct'] ?? '') === 'Yeu' ? 'selected' : '' ?>>Yếu</option>
                                </select>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="rec-badge badge <?= $recStatus === 'promoted' ? 'badge-emerald' : ($recStatus === 'graduated' ? 'badge-purple' : 'badge-rose') ?>">
                                    <?= match($recStatus) {
                                        'promoted' => 'Lên lớp',
                                        'graduated' => 'Tốt nghiệp',
                                        'retained' => 'Ở lại lớp',
                                        default => 'Chờ duyệt'
                                    } ?>
                                </span>
                            </td>
                            <td class="p-3.5">
                                <select class="status-select px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500" onchange="onStatusChange(this)">
                                    <option value="promoted" <?= $currentStatus === 'promoted' ? 'selected' : '' ?>>🟢 Được Lên Lớp</option>
                                    <option value="retained" <?= $currentStatus === 'retained' ? 'selected' : '' ?>>🔴 Ở Lại Lớp (Lưu Ban)</option>
                                    <?php if ($isFinalClass): ?>
                                    <option value="graduated" <?= $currentStatus === 'graduated' ? 'selected' : '' ?>>🎓 Xét Tốt Nghiệp</option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td class="p-3.5">
                                <select class="target-class-select px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                                    <option value="">-- Chọn lớp mới --</option>
                                    <?php foreach ($targetClasses as $tc): ?>
                                    <option value="<?= $tc['id'] ?>" <?= ($targetClassId == $tc['id']) ? 'selected' : '' ?>><?= htmlspecialchars($tc['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="p-3.5 pr-5">
                                <input type="text" class="notes-input w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs" placeholder="Ghi chú..." value="<?= htmlspecialchars($st['notes'] ?? '') ?>">
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
            <button onclick="closeModal('executeModal')" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
        </div>
        
        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
            Hệ thống sẽ cập nhật trạng thái học sinh lớp <strong><?= htmlspecialchars($classInfo['name'] ?? '') ?></strong>:
            <br>• Học sinh <strong>Được Lên Lớp</strong> sẽ chuyển sang Lớp mới đã chọn.
            <br>• Học sinh <strong>Tốt nghiệp</strong> sẽ chuyển trạng thái sang <code>Đã tốt nghiệp</code>.
            <br>• Học sinh <strong>Ở lại lớp</strong> sẽ tiếp tục học lại lớp hiện tại ở năm học mới.
        </p>

        <div class="space-y-1.5 text-xs">
            <label class="font-bold text-slate-700 dark:text-slate-300">Chuyển Sang Niên Khóa Kế Tiếp *</label>
            <select id="nextYearSelect" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y['id'] ?>"><?= htmlspecialchars($y['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeModal('executeModal')" class="px-4 py-2 text-xs font-bold border rounded-xl">Hủy</button>
            <button type="button" onclick="confirmExecutePromotion()" id="btnConfirmExecute" class="px-5 py-2 text-xs font-black bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-xl shadow-lg shadow-amber-500/30">
                Xác Nhận & Thực Hiện
            </button>
        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
function openExecuteModal() { openModal('executeModal'); }

function onStatusChange(selectEl) {
    const row = selectEl.closest('.student-row');
    const targetSelect = row.querySelector('.target-class-select');
    if (selectEl.value === 'promoted') {
        if (!targetSelect.value && targetSelect.options.length > 1) {
            targetSelect.selectedIndex = 1;
        }
    } else if (selectEl.value === 'retained') {
        targetSelect.value = '';
    } else if (selectEl.value === 'graduated') {
        targetSelect.value = '';
    }
    recalculateKPIs();
}

function recalculateKPIs() {
    const rows = document.querySelectorAll('.student-row');
    let p = 0, ret = 0, grad = 0;
    rows.forEach(r => {
        const val = r.querySelector('.status-select').value;
        if (val === 'promoted') p++;
        else if (val === 'retained') ret++;
        else if (val === 'graduated') grad++;
    });

    if (document.getElementById('kpiPromotedCount')) document.getElementById('kpiPromotedCount').textContent = p;
    if (document.getElementById('kpiRetainedCount')) document.getElementById('kpiRetainedCount').textContent = <?= $isFinalClass ? 'grad' : 'ret' ?>;
    
    const total = rows.length;
    const rate = total > 0 ? Math.round(((p + grad) / total) * 1000) / 10 : 0;
    if (document.getElementById('kpiPromotionRate')) document.getElementById('kpiPromotionRate').textContent = rate;
}

function autoApplyRecommendations() {
    const rows = document.querySelectorAll('.student-row');
    const defaultNextClassId = '<?= $nextClass['id'] ?? '' ?>';

    rows.forEach(r => {
        const avg = parseFloat(r.dataset.avgScore || 0);
        const conduct = r.querySelector('.conduct-select').value;
        const statusSelect = r.querySelector('.status-select');
        const targetSelect = r.querySelector('.target-class-select');

        <?php if ($isFinalClass): ?>
        if (avg >= 5.0 && ['Tot', 'Kha', 'TrungBinh'].includes(conduct)) {
            statusSelect.value = 'graduated';
            targetSelect.value = '';
        } else {
            statusSelect.value = 'retained';
            targetSelect.value = '';
        }
        <?php else: ?>
        if (avg >= 5.0 && ['Tot', 'Kha', 'TrungBinh'].includes(conduct)) {
            statusSelect.value = 'promoted';
            if (defaultNextClassId) targetSelect.value = defaultNextClassId;
        } else {
            statusSelect.value = 'retained';
            targetSelect.value = '';
        }
        <?php endif; ?>
    });
    recalculateKPIs();
    showToast('Đã áp dụng đề xuất tự động thành công!', 'success');
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

    const btn = document.getElementById('btnSaveDecisions');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span><span>Đang lưu...</span>';
    }

    try {
        const res = await apiPost('<?= BASE_URL ?>/promotions/save', {
            class_id: <?= (int)$selectedClass ?>,
            academic_year_id: <?= (int)$selectedYear ?>,
            records: records
        });

        if (res && res.success !== false) {
            showToast('Lưu kết quả xét lên lớp thành công!', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(res ? res.message : 'Có lỗi khi lưu kết quả', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">save</span><span>Lưu Kết Quả Xét Duyệt</span>';
            }
        }
    } catch(e) {
        showToast('Lỗi kết nối máy chủ', 'error');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">save</span><span>Lưu Kết Quả Xét Duyệt</span>';
        }
    }
}

async function confirmExecutePromotion() {
    const nextYearId = document.getElementById('nextYearSelect').value;
    const btn = document.getElementById('btnConfirmExecute');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Đang xử lý...';
    }

    try {
        const res = await apiPost('<?= BASE_URL ?>/promotions/execute', {
            class_id: <?= (int)$selectedClass ?>,
            academic_year_id: <?= (int)$selectedYear ?>,
            next_year_id: nextYearId
        });

        if (res && res.success !== false) {
            showToast('Chuyển lớp thành công!', 'success');
            closeModal('executeModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(res ? res.message : 'Có lỗi khi chuyển lớp', 'error');
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Xác Nhận & Thực Hiện';
            }
        }
    } catch(e) {
        showToast('Lỗi kết nối máy chủ', 'error');
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Xác Nhận & Thực Hiện';
        }
    }
}

function filterStudentRows() {
    const query = (document.getElementById('tableSearch').value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.student-row');
    rows.forEach(r => {
        const nameCell = r.querySelector('.student-name-cell');
        const text = nameCell ? nameCell.textContent.toLowerCase() : '';
        const code = (r.querySelector('td:nth-child(2)') ? r.querySelector('td:nth-child(2)').textContent.toLowerCase() : '');
        if (!query || text.includes(query) || code.includes(query)) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

function exportPromotionToExcel() {
    const table = document.getElementById("promotionsTable");
    const wb = XLSX.utils.table_to_book(table, {sheet: "XetLenLop_EduManage"});
    XLSX.writeFile(wb, "Bien_Ban_Xet_Len_Lop_<?= (int)$selectedClass ?>.xlsx");
}
</script>
