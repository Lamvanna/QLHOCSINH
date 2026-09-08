<?php // views/schedules/index.php ?>
<?php
$className = $currentClass['name'] ?? 'Lớp 1';
$semesterName = $currentSemester['name'] ?? 'Học kỳ I';

$days = [
    2 => 'Thứ Hai',
    3 => 'Thứ Ba',
    4 => 'Thứ Tư',
    5 => 'Thứ Năm',
    6 => 'Thứ Sáu',
    7 => 'Thứ Bảy'
];

$periods = [
    1 => ['name' => 'Tiết 1', 'time' => '07:15 - 08:00', 'session' => 'morning'],
    2 => ['name' => 'Tiết 2', 'time' => '08:05 - 08:50', 'session' => 'morning'],
    3 => ['name' => 'Tiết 3', 'time' => '09:10 - 09:55', 'session' => 'morning'],
    4 => ['name' => 'Tiết 4', 'time' => '10:00 - 10:45', 'session' => 'morning'],
    5 => ['name' => 'Tiết 5', 'time' => '10:50 - 11:35', 'session' => 'morning'],
    6 => ['name' => 'Tiết 6', 'time' => '13:30 - 14:15', 'session' => 'afternoon'],
    7 => ['name' => 'Tiết 7', 'time' => '14:20 - 15:05', 'session' => 'afternoon'],
    8 => ['name' => 'Tiết 8', 'time' => '15:15 - 16:00', 'session' => 'afternoon']
];

// Calculate KPIs
$totalPeriods = 0;
$distinctSubjects = [];
$distinctTeachers = [];
foreach ($timetable as $item) {
    $span = max(1, ($item['period_end'] - $item['period_start'] + 1));
    $totalPeriods += $span;
    $distinctSubjects[$item['subject_id']] = true;
    $distinctTeachers[$item['teacher_id']] = true;
}

// Helper to get color style by subject name
function getSubjectColorClass($name) {
    $name = mb_strtolower($name, 'UTF-8');
    if (str_contains($name, 'toán')) return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800';
    if (str_contains($name, 'tiếng việt') || str_contains($name, 'văn')) return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800';
    if (str_contains($name, 'tiếng anh') || str_contains($name, 'ngoại ngữ')) return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800';
    if (str_contains($name, 'tin')) return 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/60 dark:text-cyan-300 dark:border-cyan-800';
    if (str_contains($name, 'tự nhiên') || str_contains($name, 'khoa học') || str_contains($name, 'sinh')) return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800';
    if (str_contains($name, 'mỹ thuật') || str_contains($name, 'hội họa')) return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800';
    if (str_contains($name, 'âm nhạc')) return 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200 dark:bg-fuchsia-950/60 dark:text-fuchsia-300 dark:border-fuchsia-800';
    if (str_contains($name, 'đạo đức') || str_contains($name, 'gdcd')) return 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-800';
    if (str_contains($name, 'thể dục') || str_contains($name, 'gdtc')) return 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800';
    return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800';
}

function findSlotItem($timetable, $day, $period) {
    foreach ($timetable as $item) {
        if ($item['day_of_week'] == $day && $period >= $item['period_start'] && $period <= $item['period_end']) {
            return $item;
        }
    }
    return null;
}
?>

<div class="space-y-6">

    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 md:p-7 text-white relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -ml-10 -mb-10"></div>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-2 border border-emerald-500/30">
                    <span class="material-symbols-outlined text-[14px]">calendar_month</span> Kế Hoạch Giảng Dạy & Học Tập
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Thời Khóa Biểu — <?= htmlspecialchars($className) ?></h1>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                    Lịch học theo tuần — <strong><?= htmlspecialchars($semesterName) ?></strong>. Hệ thống tự động kiểm tra trùng tiết, giáo viên và phòng học khi phân bổ.
                </p>
            </div>

            <!-- Top Actions -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="<?= BASE_URL ?>/schedules/print?class_id=<?= (int)$selectedClass ?>&semester_id=<?= (int)$selectedSemester ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 rounded-xl transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-blue-400">print</span>
                    <span>In Thời Khóa Biểu (A4)</span>
                </a>

                <?php if (Permission::can('schedules.create')): ?>
                <button onclick="openCreateModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">add_circle</span>
                    <span>Xếp Tiết Mới</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Combined Filter + Mini Bento KPI Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        
        <!-- Filter Row -->
        <form method="GET" action="<?= BASE_URL ?>/schedules" id="scheduleFilterForm" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20">
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
                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Học Kỳ</label>
                <div class="relative">
                    <select name="semester_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 shadow-sm cursor-pointer">
                        <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['id'] ?>" <?= $selectedSemester == $sem['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Chế Độ Hiển Thị</label>
                <div class="flex items-center gap-1.5 bg-white dark:bg-slate-800 p-1 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm">
                    <button type="button" onclick="switchView('matrix')" id="btnViewMatrix" class="flex-1 py-1.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-emerald-600 text-white shadow-sm">
                        <span class="material-symbols-outlined text-[15px]">table_chart</span>
                        <span>Ma Trận Tuần</span>
                    </button>
                    <button type="button" onclick="switchView('cards')" id="btnViewCards" class="flex-1 py-1.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                        <span class="material-symbols-outlined text-[15px]">view_column</span>
                        <span>Thẻ Ngày</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- 3 Mini KPI Bento Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 dark:divide-slate-800">
            
            <div class="p-4 sm:p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-inner">
                    <span class="material-symbols-outlined text-2xl">schedule</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Tổng Tiết Học / Tuần</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white"><?= $totalPeriods ?></span>
                        <span class="text-xs text-emerald-600 font-bold">Tiết học</span>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-inner">
                    <span class="material-symbols-outlined text-2xl">menu_book</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-600 block">Số Môn Học Trong Lịch</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="text-2xl font-black text-blue-600"><?= count($distinctSubjects) ?></span>
                        <span class="text-xs text-slate-400 font-semibold">Môn</span>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-inner">
                    <span class="material-symbols-outlined text-2xl">badge</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-purple-600 block">Giáo Viên Giảng Dạy</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="text-2xl font-black text-purple-600"><?= count($distinctTeachers) ?></span>
                        <span class="text-xs text-slate-400 font-semibold">Thầy / Cô</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- VIEW 1: TIMETABLE MATRIX TABLE (Chuẩn Giáo Dục) -->
    <div id="matrixViewContainer" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-4 px-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/60 dark:bg-slate-800/30">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-emerald-600 text-lg">calendar_view_week</span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Ma Trận Thời Khóa Biểu Tuần — <?= htmlspecialchars($className) ?></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bấm vào ô trống bất kỳ để xếp tiết nhanh cho buổi học đó</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-xl">
                <?= htmlspecialchars($semesterName) ?>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs table-fixed">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase font-extrabold text-[10px] tracking-wider">
                        <th class="p-3.5 text-center w-28 bg-slate-100/70 dark:bg-slate-800">Buổi / Tiết</th>
                        <?php foreach ($days as $dNum => $dName): ?>
                        <th class="p-3.5 text-center"><?= $dName ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    
                    <!-- SÁNG HEADER -->
                    <tr class="bg-emerald-50/40 dark:bg-emerald-950/20 border-y border-emerald-100 dark:border-emerald-900/40">
                        <td colspan="7" class="p-2 pl-4 font-black text-[11px] uppercase tracking-widest text-emerald-700 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-[13px] align-middle mr-1">light_mode</span> Buổi Sáng (Tiết 1 – 5)
                        </td>
                    </tr>

                    <?php for ($p = 1; $p <= 5; $p++): 
                        $pInfo = $periods[$p];
                    ?>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="p-2.5 text-center bg-slate-50/50 dark:bg-slate-800/40 border-r border-slate-100 dark:border-slate-800">
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 block"><?= $pInfo['name'] ?></span>
                            <span class="text-[10px] text-slate-400 font-mono"><?= $pInfo['time'] ?></span>
                        </td>

                        <?php foreach ($days as $dNum => $dName): 
                            $slot = findSlotItem($timetable, $dNum, $p);
                        ?>
                        <td class="p-2 border-r border-slate-100 dark:border-slate-800 last:border-r-0 align-top h-20">
                            <?php if ($slot): 
                                $color = getSubjectColorClass($slot['subject_name']);
                            ?>
                            <div class="p-2.5 rounded-2xl border <?= $color ?> shadow-sm relative group h-full flex flex-col justify-between transition-transform hover:-translate-y-0.5">
                                <div>
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="font-black text-xs leading-tight"><?= htmlspecialchars($slot['subject_name']) ?></span>
                                        <span class="text-[9px] font-mono opacity-80 uppercase tracking-wider font-bold">T<?= $slot['period_start'] ?>-<?= $slot['period_end'] ?></span>
                                    </div>
                                    <div class="text-[10px] opacity-90 mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">person</span>
                                        <span class="truncate"><?= htmlspecialchars($slot['teacher_name']) ?></span>
                                    </div>
                                </div>
                                <div class="mt-1.5 pt-1 border-t border-current/10 flex items-center justify-between text-[9.5px]">
                                    <span class="opacity-80 font-mono"><?= htmlspecialchars($slot['room'] ?: 'P. ' . $className) ?></span>
                                    
                                    <?php if (Permission::can('schedules.delete')): ?>
                                    <button onclick="confirmDeleteSchedule(<?= $slot['id'] ?>, '<?= htmlspecialchars($slot['subject_name']) ?>')" class="opacity-0 group-hover:opacity-100 text-rose-600 hover:text-rose-800 transition-opacity p-0.5" title="Xóa tiết học">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <button onclick="quickAddSlot(<?= $dNum ?>, <?= $p ?>)" class="w-full h-full rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50/30 dark:hover:bg-emerald-950/20 text-slate-300 hover:text-emerald-600 transition-all flex flex-col items-center justify-center group/btn py-3">
                                <span class="material-symbols-outlined text-base group-hover/btn:scale-110 transition-transform">add</span>
                                <span class="text-[9px] font-bold mt-0.5 opacity-0 group-hover/btn:opacity-100 transition-opacity">Xếp tiết</span>
                            </button>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endfor; ?>

                    <!-- CHIỀU HEADER -->
                    <tr class="bg-blue-50/40 dark:bg-blue-950/20 border-y border-blue-100 dark:border-blue-900/40">
                        <td colspan="7" class="p-2 pl-4 font-black text-[11px] uppercase tracking-widest text-blue-700 dark:text-blue-400">
                            <span class="material-symbols-outlined text-[13px] align-middle mr-1">dark_mode</span> Buổi Chiều (Tiết 6 – 8)
                        </td>
                    </tr>

                    <?php for ($p = 6; $p <= 8; $p++): 
                        $pInfo = $periods[$p];
                    ?>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="p-2.5 text-center bg-slate-50/50 dark:bg-slate-800/40 border-r border-slate-100 dark:border-slate-800">
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 block"><?= $pInfo['name'] ?></span>
                            <span class="text-[10px] text-slate-400 font-mono"><?= $pInfo['time'] ?></span>
                        </td>

                        <?php foreach ($days as $dNum => $dName): 
                            $slot = findSlotItem($timetable, $dNum, $p);
                        ?>
                        <td class="p-2 border-r border-slate-100 dark:border-slate-800 last:border-r-0 align-top h-20">
                            <?php if ($slot): 
                                $color = getSubjectColorClass($slot['subject_name']);
                            ?>
                            <div class="p-2.5 rounded-2xl border <?= $color ?> shadow-sm relative group h-full flex flex-col justify-between transition-transform hover:-translate-y-0.5">
                                <div>
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="font-black text-xs leading-tight"><?= htmlspecialchars($slot['subject_name']) ?></span>
                                        <span class="text-[9px] font-mono opacity-80 uppercase tracking-wider font-bold">T<?= $slot['period_start'] ?>-<?= $slot['period_end'] ?></span>
                                    </div>
                                    <div class="text-[10px] opacity-90 mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">person</span>
                                        <span class="truncate"><?= htmlspecialchars($slot['teacher_name']) ?></span>
                                    </div>
                                </div>
                                <div class="mt-1.5 pt-1 border-t border-current/10 flex items-center justify-between text-[9.5px]">
                                    <span class="opacity-80 font-mono"><?= htmlspecialchars($slot['room'] ?: 'P. ' . $className) ?></span>
                                    
                                    <?php if (Permission::can('schedules.delete')): ?>
                                    <button onclick="confirmDeleteSchedule(<?= $slot['id'] ?>, '<?= htmlspecialchars($slot['subject_name']) ?>')" class="opacity-0 group-hover:opacity-100 text-rose-600 hover:text-rose-800 transition-opacity p-0.5" title="Xóa tiết học">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <button onclick="quickAddSlot(<?= $dNum ?>, <?= $p ?>)" class="w-full h-full rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50/30 dark:hover:bg-emerald-950/20 text-slate-300 hover:text-emerald-600 transition-all flex flex-col items-center justify-center group/btn py-3">
                                <span class="material-symbols-outlined text-base group-hover/btn:scale-110 transition-transform">add</span>
                                <span class="text-[9px] font-bold mt-0.5 opacity-0 group-hover/btn:opacity-100 transition-opacity">Xếp tiết</span>
                            </button>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endfor; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- VIEW 2: CARDS BY DAY VIEW (Thẻ Theo Ngày) -->
    <div id="cardsViewContainer" class="hidden grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <?php foreach ($days as $dNum => $dName): 
            $daySchedules = array_filter($timetable, fn($item) => $item['day_of_week'] == $dNum);
        ?>
        <div class="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm flex flex-col justify-between">
            <div>
                <div class="pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="font-black text-sm text-slate-900 dark:text-white"><?= $dName ?></h4>
                        <span class="text-[10px] font-bold text-emerald-600"><?= count($daySchedules) ?> Tiết học</span>
                    </div>
                    <button onclick="quickAddSlot(<?= $dNum ?>, 1)" class="w-7 h-7 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-100 text-slate-600 hover:text-emerald-700 flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-sm">add</span>
                    </button>
                </div>

                <div class="space-y-2.5 mt-3">
                    <?php if (!empty($daySchedules)): ?>
                        <?php foreach ($daySchedules as $sc): 
                            $c = getSubjectColorClass($sc['subject_name']);
                        ?>
                        <div class="p-3 rounded-2xl border <?= $c ?> shadow-sm relative group">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-white/70 dark:bg-black/30">Tiết <?= $sc['period_start'] ?> – <?= $sc['period_end'] ?></span>
                            <h5 class="font-black text-xs mt-1.5"><?= htmlspecialchars($sc['subject_name']) ?></h5>
                            <p class="text-[10px] opacity-80 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">person</span>
                                <span><?= htmlspecialchars($sc['teacher_name']) ?></span>
                            </p>
                            <p class="text-[9.5px] opacity-70 font-mono mt-0.5"><?= htmlspecialchars($sc['room'] ?: 'P. ' . $className) ?></p>

                            <?php if (Permission::can('schedules.delete')): ?>
                            <button onclick="confirmDeleteSchedule(<?= $sc['id'] ?>, '<?= htmlspecialchars($sc['subject_name']) ?>')" class="absolute top-2.5 right-2.5 text-rose-500 hover:text-rose-700 opacity-0 group-hover:opacity-100 transition-opacity p-0.5">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="py-10 text-center text-slate-400">
                            <span class="material-symbols-outlined text-2xl text-slate-300">event_busy</span>
                            <p class="text-[11px] mt-1">Chưa có tiết học</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Modal Xếp Tiết Mới -->
<div id="createScheduleModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">calendar_add_on</span>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Xếp Tiết Thời Khóa Biểu</h3>
            </div>
            <button onclick="closeModal('createScheduleModal')" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
        </div>

        <form id="createScheduleForm" onsubmit="handleCreateSchedule(event)" class="space-y-3.5 text-xs">
            <input type="hidden" name="class_id" value="<?= (int)$selectedClass ?>">
            <input type="hidden" name="semester_id" value="<?= (int)$selectedSemester ?>">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Thứ Trong Tuần *</label>
                    <select id="modalDaySelect" name="day_of_week" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                        <?php foreach ($days as $dNum => $dName): ?>
                        <option value="<?= $dNum ?>"><?= $dName ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Phòng Học</label>
                    <input type="text" id="modalRoomInput" name="room" value="Phòng <?= htmlspecialchars($className) ?>" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold" placeholder="VD: Phòng 101">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Từ Tiết *</label>
                    <select id="modalPeriodStart" name="period_start" required onchange="onPeriodStartChange()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                        <?php for ($p = 1; $p <= 8; $p++): ?>
                        <option value="<?= $p ?>">Tiết <?= $p ?> (<?= $periods[$p]['time'] ?>)</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Đến Tiết *</label>
                    <select id="modalPeriodEnd" name="period_end" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                        <?php for ($p = 1; $p <= 8; $p++): ?>
                        <option value="<?= $p ?>">Tiết <?= $p ?> (<?= $periods[$p]['time'] ?>)</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Môn Học *</label>
                <select name="subject_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-emerald-600">
                    <option value="">-- Chọn môn học --</option>
                    <?php foreach ($subjects as $sub): ?>
                    <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Giáo Viên Phụ Trách *</label>
                <select name="teacher_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                    <option value="">-- Chọn giáo viên --</option>
                    <?php foreach ($teachers as $tea): ?>
                    <option value="<?= $tea['id'] ?>"><?= htmlspecialchars($tea['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('createScheduleModal')" class="px-4 py-2 text-xs font-bold border rounded-xl">Hủy</button>
                <button type="submit" id="btnSubmitSchedule" class="px-5 py-2 text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-lg shadow-emerald-500/30">
                    Xác Nhận Xếp Tiết
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function openCreateModal() {
    openModal('createScheduleModal');
}

function quickAddSlot(day, period) {
    document.getElementById('modalDaySelect').value = day;
    document.getElementById('modalPeriodStart').value = period;
    document.getElementById('modalPeriodEnd').value = period;
    openCreateModal();
}

function onPeriodStartChange() {
    const startVal = parseInt(document.getElementById('modalPeriodStart').value);
    const endSelect = document.getElementById('modalPeriodEnd');
    if (parseInt(endSelect.value) < startVal) {
        endSelect.value = startVal;
    }
}

function switchView(mode) {
    const matrix = document.getElementById('matrixViewContainer');
    const cards = document.getElementById('cardsViewContainer');
    const btnMatrix = document.getElementById('btnViewMatrix');
    const btnCards = document.getElementById('btnViewCards');

    if (mode === 'matrix') {
        matrix.classList.remove('hidden');
        cards.classList.add('hidden');
        btnMatrix.className = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-emerald-600 text-white shadow-sm';
        btnCards.className = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700';
    } else {
        matrix.classList.add('hidden');
        cards.classList.remove('hidden');
        btnCards.className = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-emerald-600 text-white shadow-sm';
        btnMatrix.className = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700';
    }
}

async function handleCreateSchedule(e) {
    e.preventDefault();
    const form = e.target;
    const btn = document.getElementById('btnSubmitSchedule');
    btn.disabled = true;
    btn.textContent = 'Đang xếp tiết...';

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const res = await apiPost('<?= BASE_URL ?>/schedules', data);
        if (res && res.success !== false) {
            showToast('Xếp tiết thành công!', 'success');
            closeModal('createScheduleModal');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(res ? res.message : 'Có xung đột thời khóa biểu', 'error');
            btn.disabled = false;
            btn.textContent = 'Xác Nhận Xếp Tiết';
        }
    } catch(err) {
        showToast('Lỗi máy chủ khi xếp tiết', 'error');
        btn.disabled = false;
        btn.textContent = 'Xác Nhận Xếp Tiết';
    }
}

async function confirmDeleteSchedule(id, subjectName) {
    if (!confirm(`Bạn có chắc muốn xóa tiết học "${subjectName}" khỏi thời khóa biểu?`)) {
        return;
    }

    try {
        const res = await apiDelete(`<?= BASE_URL ?>/schedules/${id}`);
        if (res && res.success !== false) {
            showToast('Đã xóa tiết học thành công!', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(res ? res.message : 'Lỗi khi xóa tiết học', 'error');
        }
    } catch(err) {
        showToast('Lỗi kết nối máy chủ', 'error');
    }
}
</script>
