<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Báo Cáo & Thống Kê</h2>
            <p class="text-xs text-slate-500 mt-1">Tổng hợp phân bổ học sinh theo lớp, kết quả điểm số môn học và phân loại học lực</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="exportReportToExcel()" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold hover:bg-slate-50 flex items-center">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1.5 text-emerald-600"></i> Xuất Excel
            </button>
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold hover:bg-slate-50 flex items-center">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5 text-blue-600"></i> In Báo Cáo
            </button>
        </div>
    </div>

    <!-- Report Type Switcher Tabs -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 text-xs font-bold gap-3">
        <a href="<?= BASE_URL ?>/reports?type=students" class="px-4 py-2.5 border-b-2 <?= $reportType === 'students' ? 'border-emerald-600 text-emerald-600 font-black' : 'border-transparent text-slate-400 hover:text-slate-700' ?>">
            1. Báo Cáo Phân Bổ Học Sinh Theo Lớp
        </a>
        <a href="<?= BASE_URL ?>/reports?type=grades" class="px-4 py-2.5 border-b-2 <?= $reportType === 'grades' ? 'border-emerald-600 text-emerald-600 font-black' : 'border-transparent text-slate-400 hover:text-slate-700' ?>">
            2. Báo Cáo Phổ Điểm & Xếp Loại Học Lực
        </a>
    </div>

    <!-- Report Content Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs" id="reportTable">
                <?php if ($reportType === 'students'): ?>
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                        <th class="p-4 w-12 text-center">STT</th>
                        <th class="p-4">Lớp Học</th>
                        <th class="p-4">Phòng Học</th>
                        <th class="p-4 text-center">Tổng Số Học Sinh</th>
                        <th class="p-4 text-center">Học Sinh Nam</th>
                        <th class="p-4 text-center">Học Sinh Nữ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    <?php 
                    $totalAll = 0; $totalMale = 0; $totalFemale = 0;
                    foreach ($reportData as $idx => $r): 
                        $totalAll += $r['total_students'];
                        $totalMale += $r['male_students'];
                        $totalFemale += $r['female_students'];
                    ?>
                    <tr>
                        <td class="p-4 text-center text-slate-400"><?= $idx + 1 ?></td>
                        <td class="p-4 font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($r['class_name']) ?></td>
                        <td class="p-4 text-slate-500 font-medium"><?= htmlspecialchars($r['room_number'] ?? 'Phòng 10' . ($idx + 1)) ?></td>
                        <td class="p-4 text-center font-bold text-emerald-600"><?= $r['total_students'] ?></td>
                        <td class="p-4 text-center text-blue-600"><?= $r['male_students'] ?></td>
                        <td class="p-4 text-center text-rose-600"><?= $r['female_students'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t border-slate-200 dark:border-slate-700">
                    <tr>
                        <td class="p-4 text-center text-slate-500" colspan="3">TỔNG TOÀN TRƯỜNG</td>
                        <td class="p-4 text-center text-emerald-600 font-black"><?= $totalAll ?></td>
                        <td class="p-4 text-center text-blue-600 font-black"><?= $totalMale ?></td>
                        <td class="p-4 text-center text-rose-600 font-black"><?= $totalFemale ?></td>
                    </tr>
                </tfoot>

                <?php elseif ($reportType === 'grades'): ?>
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                        <th class="p-4 w-12 text-center">STT</th>
                        <th class="p-4">Lớp Học</th>
                        <th class="p-4">Môn Học</th>
                        <th class="p-4 text-center">Điểm TB Môn</th>
                        <th class="p-4 text-center">Giỏi (&ge;8.0)</th>
                        <th class="p-4 text-center">Khá (6.5 - 7.9)</th>
                        <th class="p-4 text-center">Đạt (5.0 - 6.4)</th>
                        <th class="p-4 text-center">Chưa Đạt (&lt;5.0)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    <?php foreach ($reportData as $idx => $r): ?>
                    <tr>
                        <td class="p-4 text-center text-slate-400"><?= $idx + 1 ?></td>
                        <td class="p-4 font-bold"><?= htmlspecialchars($r['class_name']) ?></td>
                        <td class="p-4 font-semibold text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($r['subject_name']) ?></td>
                        <td class="p-4 text-center font-mono font-black text-emerald-600 text-sm"><?= $r['avg_score'] ?></td>
                        <td class="p-4 text-center text-emerald-600 font-bold"><?= $r['excellent_count'] ?></td>
                        <td class="p-4 text-center text-blue-600 font-bold"><?= $r['good_count'] ?></td>
                        <td class="p-4 text-center text-amber-600 font-bold"><?= $r['pass_count'] ?></td>
                        <td class="p-4 text-center text-rose-600 font-bold"><?= $r['fail_count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<script>
    function exportReportToExcel() {
        const table = document.getElementById("reportTable");
        const wb = XLSX.utils.table_to_book(table, {sheet: "BaoCao_EduManage"});
        XLSX.writeFile(wb, "BaoCao_EduManage_<?= $reportType ?>.xlsx");
    }
</script>
