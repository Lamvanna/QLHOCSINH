<?php // views/semesters/index.php ?>
<?php
$currentSem = null;
$totalGrades = 0;
$totalExams = 0;
foreach ($semesters as $s) {
    if (!empty($s['is_current'])) {
        $currentSem = $s;
    }
    $totalGrades += (int)($s['grade_count'] ?? 0);
    $totalExams += (int)($s['exam_count'] ?? 0);
}
?>
<div class="space-y-6">

    <!-- 1. HEADER & MAIN ACTIONS -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-teal-600 dark:text-teal-400 mb-1">
                <span class="material-symbols-outlined text-[17px]">event_note</span>
                <span>Quản Lý Học Vụ & Thời Gian Đào Tạo</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Quản Lý Học Kỳ</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-0.5">
                Tổng số: <strong class="text-teal-600 dark:text-teal-400"><?= count($semesters) ?> học kỳ</strong> &nbsp;|&nbsp; 
                Hiện hành: <strong class="text-slate-800 dark:text-slate-200"><?= htmlspecialchars($currentSem['name'] ?? 'Chưa thiết lập') ?></strong> (<?= htmlspecialchars($currentSem['year_name'] ?? '') ?>)
            </p>
        </div>
        
        <!-- Right Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <button onclick="exportSemestersExcel()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-teal-700 dark:text-teal-300 rounded-xl text-xs font-bold transition-all shadow-sm" title="Xuất danh sách học kỳ ra Excel">
                <span class="material-symbols-outlined text-teal-600 text-[18px]">table_view</span>
                <span>Xuất Excel</span>
            </button>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-[#006c4a] hover:bg-[#005137] text-white rounded-xl text-xs font-bold transition-all shadow-sm" title="In danh sách học kỳ">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>In Danh Sách</span>
            </button>
            <?php if (Permission::can('semesters.create')): ?>
            <button onclick="openSemModal('create')" class="inline-flex items-center gap-2 px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-teal-500/20 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Thêm Học Kỳ</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. KPI METRICS CARDS (Bento Stats) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tổng số học kỳ -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">event_note</span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng Học Kỳ</p>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5"><?= count($semesters) ?> <span class="text-xs font-normal text-slate-400">kỳ</span></p>
            </div>
        </div>

        <!-- Card 2: Học kỳ hiện tại -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">star</span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Học Kỳ Hiện Tại</p>
                <p class="text-base font-black text-emerald-600 dark:text-emerald-400 truncate mt-0.5" title="<?= htmlspecialchars($currentSem['name'] ?? 'Chưa đặt') ?>">
                    <?= htmlspecialchars($currentSem['name'] ?? 'Chưa đặt') ?>
                </p>
            </div>
        </div>

        <!-- Card 3: Niên khóa áp dụng -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">calendar_month</span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Niên Khóa Áp Dụng</p>
                <p class="text-base font-black text-amber-600 dark:text-amber-400 truncate mt-0.5">
                    <?= htmlspecialchars($currentSem['year_code'] ?? ($currentSem['year_name'] ?? 'Chưa đặt')) ?>
                </p>
            </div>
        </div>

        <!-- Card 4: Dữ liệu điểm số -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">grade</span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Dữ Liệu Học Vụ</p>
                <p class="text-base font-black text-slate-900 dark:text-white mt-0.5">
                    <?= (int)($currentSem['grade_count'] ?? 0) ?> điểm • <?= (int)($currentSem['schedule_count'] ?? 0) ?> TKB
                </p>
            </div>
        </div>
    </div>

    <!-- 3. TOOLBAR: FILTERS & VIEW SWITCHER -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col xl:flex-row justify-between items-start xl:items-center gap-3">
        <!-- Filter Form -->
        <form method="GET" action="<?= BASE_URL ?>/semesters" id="semFilterForm" class="flex flex-wrap items-center gap-2.5 w-full xl:w-auto">
            <!-- Search -->
            <div class="relative flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-1.5 focus-within:ring-2 focus-within:ring-teal-500 w-full sm:w-60 transition-all">
                <span class="material-symbols-outlined text-slate-400 mr-2 text-[18px]">search</span>
                <input name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="bg-transparent border-none outline-none w-full text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:ring-0 p-0" placeholder="Tìm tên học kỳ, mã HK..." type="text"/>
            </div>

            <!-- Year Filter -->
            <div class="relative">
                <select name="academic_year_id" onchange="document.getElementById('semFilterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-teal-500 cursor-pointer">
                    <option value="">Tất cả Niên Khóa</option>
                    <?php foreach ($years as $y): ?>
                    <option value="<?= $y['id'] ?>" <?= ($filters['academic_year_id'] ?? '') == $y['id'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select name="status" onchange="document.getElementById('semFilterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-teal-500 cursor-pointer">
                    <option value="">Tất cả Trạng thái</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang diễn ra</option>
                    <option value="upcoming" <?= ($filters['status'] ?? '') === 'upcoming' ? 'selected' : '' ?>>Sắp tới</option>
                    <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Đã kết thúc</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Reset Filter -->
            <a href="<?= BASE_URL ?>/semesters" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-rose-500 hover:text-rose-600 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                <span>Xóa lọc</span>
            </a>
        </form>

        <!-- View Mode Switch -->
        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 self-end xl:self-center">
            <button type="button" id="btnViewGrid" onclick="switchSemView('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">grid_view</span>
                <span class="hidden sm:inline">Dạng Thẻ</span>
            </button>
            <button type="button" id="btnViewTable" onclick="switchSemView('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white">
                <span class="material-symbols-outlined text-[16px]">table_rows</span>
                <span class="hidden sm:inline">Dạng Bảng</span>
            </button>
        </div>
    </div>

    <!-- 4. VIEW 1: BENTO SEMESTER CARDS GRID -->
    <div id="semestersGridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php if (!empty($semesters)): ?>
            <?php foreach ($semesters as $s):
                $isCurr = !empty($s['is_current']);
                $cardBorder = $isCurr ? 'border-teal-400 dark:border-teal-500 ring-2 ring-teal-400/20 shadow-md' : 'border-slate-200 dark:border-slate-800';
            ?>
            <div class="bg-white dark:bg-slate-900 border <?= $cardBorder ?> rounded-3xl overflow-hidden hover:shadow-lg transition-all duration-200 group flex flex-col relative">
                <!-- Top Accent Bar -->
                <div class="h-2 w-full <?= $isCurr ? 'bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-600' : 'bg-gradient-to-r from-slate-300 via-slate-400 to-slate-500 dark:from-slate-700 dark:to-slate-800' ?>"></div>

                <div class="p-5 flex flex-col flex-1 space-y-4">
                    <!-- Top Meta: Code & Status & Current Badge -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                <span class="font-mono text-xs font-black px-2.5 py-0.5 rounded-lg bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/40">
                                    <?= htmlspecialchars($s['code'] ?? 'HK') ?>
                                </span>

                                <?php if ($isCurr): ?>
                                <span class="text-[10px] font-extrabold text-teal-700 bg-teal-100 dark:bg-teal-950 dark:text-teal-300 px-2 py-0.5 rounded-full border border-teal-300 dark:border-teal-700 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">star</span> HIỆN HÀNH
                                </span>
                                <?php endif; ?>

                                <?php if ($s['status'] === 'active'): ?>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/80 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                                    ● ĐANG DIỄN RA
                                </span>
                                <?php elseif ($s['status'] === 'upcoming'): ?>
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 dark:bg-blue-950/80 px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-800">
                                    ● SẮP TỚI
                                </span>
                                <?php else: ?>
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full border border-slate-300 dark:border-slate-700">
                                    ● ĐÃ KẾT THÚC
                                </span>
                                <?php endif; ?>
                            </div>

                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white group-hover:text-teal-600 transition-colors">
                                <?= htmlspecialchars($s['name']) ?>
                            </h3>
                        </div>

                        <!-- Year Badge -->
                        <span class="text-xs font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-2.5 py-1 rounded-xl border border-amber-200/60 dark:border-amber-800/40 shrink-0">
                            <?= htmlspecialchars($s['year_code'] ?? ($s['year_name'] ?? 'Niên khóa')) ?>
                        </span>
                    </div>

                    <!-- Date Timeline Box -->
                    <div class="grid grid-cols-2 gap-2.5 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bắt đầu</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-emerald-500">event_available</span>
                                <?= !empty($s['start_date']) ? date('d/m/Y', strtotime($s['start_date'])) : '—' ?>
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kết thúc</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-rose-500">event_busy</span>
                                <?= !empty($s['end_date']) ? date('d/m/Y', strtotime($s['end_date'])) : '—' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Stats Strip: Grades, Schedules, Exams -->
                    <div class="grid grid-cols-3 gap-2 py-1 text-center text-xs">
                        <div class="p-2 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] block">Sổ điểm</span>
                            <span class="font-extrabold text-slate-900 dark:text-white text-sm"><?= (int)($s['grade_count'] ?? 0) ?></span>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] block">Tiết TKB</span>
                            <span class="font-extrabold text-slate-900 dark:text-white text-sm"><?= (int)($s['schedule_count'] ?? 0) ?></span>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] block">Kỳ thi</span>
                            <span class="font-extrabold text-slate-900 dark:text-white text-sm"><?= (int)($s['exam_count'] ?? 0) ?></span>
                        </div>
                    </div>

                    <!-- Card Actions Footer -->
                    <div class="pt-3 flex items-center justify-between gap-2 border-t border-slate-100 dark:border-slate-800 mt-auto">
                        <div>
                            <?php if (!$isCurr && Permission::can('semesters.update')): ?>
                            <button onclick="setSemesterAsCurrent(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['name'])) ?>')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300 text-xs font-bold transition-all"
                                    title="Đặt làm học kỳ hiện hành">
                                <span class="material-symbols-outlined text-[15px]">star</span>
                                <span>Đặt hiện hành</span>
                            </button>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-600 dark:text-teal-400 px-2 py-1">
                                <span class="material-symbols-outlined text-[15px]">verified</span> Đang hoạt động
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-1">
                            <?php if (Permission::can('semesters.update')): ?>
                            <button onclick="openSemModal('edit', <?= htmlspecialchars(json_encode($s), ENT_QUOTES) ?>)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-xl transition-colors" title="Chỉnh sửa học kỳ">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <?php endif; ?>
                            <?php if (Permission::can('semesters.delete')): ?>
                            <button onclick="openDeleteSemModal(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['name'])) ?>', '<?= htmlspecialchars($s['code'] ?? '') ?>', <?= $isCurr ? 1 : 0 ?>, <?= (int)($s['grade_count'] ?? 0) ?>)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors" title="Xóa học kỳ">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-16 text-center text-slate-400">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-3xl text-slate-400">event_note</span>
                </div>
                <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy học kỳ nào</p>
                <p class="text-xs text-slate-400 mt-1">Thử thay đổi bộ lọc tìm kiếm hoặc tạo thêm học kỳ mới.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 5. VIEW 2: DATA TABLE -->
    <div id="semestersTableView" class="hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px] text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="p-3.5 pl-5 w-12 text-center">STT</th>
                        <th class="p-3.5">MÃ HỌC KỲ</th>
                        <th class="p-3.5">TÊN HỌC KỲ</th>
                        <th class="p-3.5">NIÊN KHÓA TRỰC THUỘC</th>
                        <th class="p-3.5">THỜI GIAN ĐÀO TẠO</th>
                        <th class="p-3.5 text-center">SỔ ĐIỂM</th>
                        <th class="p-3.5 text-center">TKB</th>
                        <th class="p-3.5">HIỆN TẠI</th>
                        <th class="p-3.5">TRẠNG THÁI</th>
                        <th class="p-3.5 pr-5 text-right">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-200">
                    <?php if (!empty($semesters)): ?>
                        <?php foreach ($semesters as $idx => $s):
                            $isCurr = !empty($s['is_current']);
                        ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="p-3.5 pl-5 text-center font-normal text-slate-400"><?= $idx + 1 ?></td>
                            <td class="p-3.5 font-mono font-bold text-teal-600 dark:text-teal-400"><?= htmlspecialchars($s['code'] ?? 'HK') ?></td>
                            <td class="p-3.5 font-bold text-slate-900 dark:text-white text-sm"><?= htmlspecialchars($s['name']) ?></td>
                            <td class="p-3.5">
                                <span class="bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 px-2 py-0.5 rounded-md font-bold text-xs">
                                    <?= htmlspecialchars($s['year_name'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-300">
                                <?= date('d/m/Y', strtotime($s['start_date'])) ?> &rarr; <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                            </td>
                            <td class="p-3.5 text-center font-bold text-slate-800 dark:text-slate-200"><?= (int)($s['grade_count'] ?? 0) ?></td>
                            <td class="p-3.5 text-center font-bold text-slate-800 dark:text-slate-200"><?= (int)($s['schedule_count'] ?? 0) ?></td>
                            <td class="p-3.5">
                                <?php if ($isCurr): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-700 dark:bg-teal-950/80 dark:text-teal-300 text-[10px] font-bold border border-teal-200 dark:border-teal-800">
                                    ⭐ HIỆN HÀNH
                                </span>
                                <?php else: ?>
                                <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5">
                                <?php if ($s['status'] === 'active'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                                    ● ĐANG DIỄN RA
                                </span>
                                <?php elseif ($s['status'] === 'upcoming'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-950/80 dark:text-blue-300 text-[10px] font-bold uppercase tracking-wider border border-blue-200 dark:border-blue-800">
                                    ● SẮP TỚI
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                    ● ĐÃ KẾT THÚC
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if (!$isCurr && Permission::can('semesters.update')): ?>
                                    <button onclick="setSemesterAsCurrent(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['name'])) ?>')" 
                                            class="p-1.5 text-slate-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-950/60 rounded-lg transition-colors" 
                                            title="Đặt làm học kỳ hiện hành">
                                        <span class="material-symbols-outlined text-[18px]">star</span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if (Permission::can('semesters.update')): ?>
                                    <button onclick="openSemModal('edit', <?= htmlspecialchars(json_encode($s), ENT_QUOTES) ?>)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-lg transition-colors" title="Sửa học kỳ">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if (Permission::can('semesters.delete')): ?>
                                    <button onclick="openDeleteSemModal(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['name'])) ?>', '<?= htmlspecialchars($s['code'] ?? '') ?>', <?= $isCurr ? 1 : 0 ?>, <?= (int)($s['grade_count'] ?? 0) ?>)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-lg transition-colors" title="Xóa học kỳ">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Thêm / Sửa Học Kỳ (Modern Rounded-3xl Glass) -->
<div id="semModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl" id="modalSemIcon">event_note</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white" id="modalSemTitle">Thêm Học Kỳ Mới</h3>
                    <p class="text-xs text-slate-400">Thiết lập thông tin học kỳ và khoảng thời gian đào tạo</p>
                </div>
            </div>
            <button onclick="closeModal('semModal')" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Form -->
        <form id="semForm" onsubmit="handleSemSubmit(event)" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="id" id="semId" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Tên Học Kỳ <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="semName" required oninput="autoSuggestSemCode(this.value)"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-teal-500 outline-none"
                           placeholder="VD: Học kỳ I, Học kỳ II">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Mã Học Kỳ
                    </label>
                    <input type="text" name="code" id="semCode"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-mono font-bold text-teal-600 dark:text-teal-400 focus:bg-white focus:ring-2 focus:ring-teal-500 outline-none uppercase"
                           placeholder="VD: HK1, HK2, CN">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Niên Khóa Trực Thuộc <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="academic_year_id" id="semYearId" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-2.5 font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-teal-500 outline-none cursor-pointer appearance-none">
                        <?php foreach ($years as $y): ?>
                        <option value="<?= $y['id'] ?>" <?= !empty($y['is_current']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($y['name']) ?> <?= !empty($y['is_current']) ? '(Hiện hành)' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ngày Bắt Đầu <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="semStartDate" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ngày Kết Thúc <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="semEndDate" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Trạng Thái Học Kỳ
                </label>
                <select name="status" id="semStatus" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-teal-500 outline-none cursor-pointer">
                    <option value="active">Đang diễn ra (active)</option>
                    <option value="upcoming">Sắp tới (upcoming)</option>
                    <option value="completed">Đã kết thúc (completed)</option>
                </select>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 hover:bg-teal-50/40 cursor-pointer transition-colors">
                    <input type="checkbox" name="is_current" id="semIsCurrent" value="1" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200">Đặt làm Học Kỳ Hiện Tại</span>
                        <p class="text-[11px] text-slate-400 mt-0.5">Sổ điểm, lịch thi và thời khóa biểu sẽ mặc định chọn học kỳ này</p>
                    </div>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('semModal')" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-50 transition-colors">
                    Hủy Bỏ
                </button>
                <button type="submit" id="btnSubmitSem" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-md shadow-teal-500/20 active:scale-[0.98] transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span id="btnSubmitSemText">Lưu Học Kỳ</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Xác Nhận Xóa Học Kỳ (Chuẩn Theo Mẫu Cảnh Báo Đỏ) -->
<div id="deleteSemConfirmModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden text-center transform transition-all scale-100 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Header: Nền hồng đỏ nhạt có tam giác cảnh báo ở giữa -->
        <div class="bg-[#feecee] dark:bg-rose-950/40 border-b border-rose-100 dark:border-rose-900/50 pt-7 pb-6 px-6">
            <div class="w-12 h-12 rounded-full bg-rose-200/80 dark:bg-rose-900/70 text-rose-700 dark:text-rose-300 flex items-center justify-center mx-auto mb-3 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-rose-700 dark:text-rose-300">
                Xác nhận xóa học kỳ?
            </h3>
        </div>

        <!-- Body: Nền trắng có đoạn văn cảnh báo và thẻ học kỳ -->
        <div class="p-6 sm:p-7 bg-white dark:bg-slate-900 space-y-6">
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed text-center">
                Hành động này không thể hoàn tác. Học kỳ chỉ có thể xóa khi không còn điểm số nào của học sinh và không phải học kỳ hiện hành.
            </p>

            <!-- Khung thông tin học kỳ -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-4 bg-white dark:bg-slate-800/50 flex items-center gap-4 text-left shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 dark:border-rose-900/40">
                    <span class="material-symbols-outlined text-2xl">event_note</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span id="modalDeleteSemName" class="font-bold text-slate-900 dark:text-white text-base truncate">Học kỳ I</span>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            Mã: <span id="modalDeleteSemCode">HK1</span>
                        </span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            Điểm số: <span id="modalDeleteSemGrades">0</span> bản ghi
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden ID -->
        <input type="hidden" id="modalDeleteSemId" value="">

        <!-- Footer: Nút [Hủy bỏ] và [Xác nhận xóa] -->
        <div class="p-4 sm:p-5 px-7 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('deleteSemConfirmModal')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition-all shadow-sm">
                Hủy bỏ
            </button>
            <button type="button" id="btnExecuteDeleteSem" onclick="executeDeleteSem()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#b91c1c] hover:bg-[#991b1b] active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>Xác nhận xóa</span>
            </button>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    const ALL_SEMESTERS = <?= json_encode($allSemestersForExport ?? $semesters ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // =========================================================================
    // CHUYỂN ĐỔI CHẾ ĐỘ XEM (VIEW SWITCH: GRID / TABLE)
    // =========================================================================
    function switchSemView(mode) {
        const gridView = document.getElementById('semestersGridView');
        const tableView = document.getElementById('semestersTableView');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnTable = document.getElementById('btnViewTable');

        if (mode === 'table') {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');

            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-sm';
            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('semester_view_mode', 'table');
        } else {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');

            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-sm';
            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('semester_view_mode', 'grid');
        }
    }

    // Auto restore previous view mode
    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('semester_view_mode');
        if (savedMode === 'table') switchSemView('table');
    });

    // Tự động gợi ý mã học kỳ
    function autoSuggestSemCode(val) {
        const lower = val.toLowerCase();
        if (lower.includes('1') || lower.includes('i')) {
            document.getElementById('semCode').value = 'HK1';
        } else if (lower.includes('2') || lower.includes('ii')) {
            document.getElementById('semCode').value = 'HK2';
        } else if (lower.includes('cả năm') || lower.includes('ca nam')) {
            document.getElementById('semCode').value = 'CN';
        }
    }

    // =========================================================================
    // MODAL THÊM / SỬA HỌC KỲ
    // =========================================================================
    function openSemModal(mode, data = null) {
        const isEdit = mode === 'edit';
        document.getElementById('modalSemTitle').textContent = isEdit ? 'Cập Nhật Học Kỳ' : 'Thêm Học Kỳ Mới';
        document.getElementById('modalSemIcon').textContent = isEdit ? 'edit_square' : 'event_note';
        document.getElementById('btnSubmitSemText').textContent = isEdit ? 'Lưu Thay Đổi' : 'Tạo Học Kỳ';

        if (isEdit && data) {
            document.getElementById('semId').value = data.id;
            document.getElementById('semName').value = data.name || '';
            document.getElementById('semCode').value = data.code || '';
            document.getElementById('semYearId').value = data.academic_year_id || '';
            document.getElementById('semStartDate').value = data.start_date || '';
            document.getElementById('semEndDate').value = data.end_date || '';
            document.getElementById('semStatus').value = data.status || 'active';
            document.getElementById('semIsCurrent').checked = Number(data.is_current) === 1;
        } else {
            document.getElementById('semId').value = '';
            document.getElementById('semName').value = '';
            document.getElementById('semCode').value = '';
            
            const curY = new Date().getFullYear();
            document.getElementById('semStartDate').value = `${curY}-09-05`;
            document.getElementById('semEndDate').value = `${curY + 1}-01-15`;
            document.getElementById('semStatus').value = 'active';
            document.getElementById('semIsCurrent').checked = false;
        }

        openModal('semModal');
    }

    async function handleSemSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitSem');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        data.is_current = document.getElementById('semIsCurrent').checked ? 1 : 0;

        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang lưu...</span>`;

        const isEdit = Boolean(data.id);
        const url = isEdit ? `<?= BASE_URL ?>/semesters/${data.id}` : '<?= BASE_URL ?>/semesters';

        try {
            const res = await apiPost(url, data);
            if (res) {
                closeModal('semModal');
                showToast(isEdit ? 'Đã cập nhật học kỳ thành công!' : 'Đã tạo mới học kỳ thành công!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span>${isEdit ? 'Lưu Thay Đổi' : 'Tạo Học Kỳ'}</span>`;
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span>${isEdit ? 'Lưu Thay Đổi' : 'Tạo Học Kỳ'}</span>`;
        }
    }

    // =========================================================================
    // ĐẶT LÀM HỌC KỲ HIỆN TẠI (ONE-CLICK AJAX)
    // =========================================================================
    async function setSemesterAsCurrent(id, name) {
        try {
            showToast(`Đang đặt "${name}" làm học kỳ hiện hành...`, 'info');
            const res = await apiPost(`<?= BASE_URL ?>/semesters/${id}`, { is_current: 1 });
            if (res) {
                showToast(`Đã thiết lập "${name}" làm học kỳ hiện hành!`, 'success');
                setTimeout(() => location.reload(), 700);
            }
        } catch (e) {
            console.error(e);
            showToast('Lỗi khi thiết lập học kỳ hiện tại!', 'error');
        }
    }

    // =========================================================================
    // MODAL XÁC NHẬN XÓA HỌC KỲ
    // =========================================================================
    function openDeleteSemModal(id, name, code, isCurrent, gradeCount) {
        if (isCurrent === 1) {
            showToast(`Không thể xóa "${name}" vì đây là học kỳ đang hoạt động!`, 'warning');
            return;
        }
        if (gradeCount > 0) {
            showToast(`Không thể xóa "${name}" vì đã có ${gradeCount} bản ghi điểm của học sinh!`, 'warning');
            return;
        }

        document.getElementById('modalDeleteSemId').value = id;
        document.getElementById('modalDeleteSemName').textContent = name || '';
        document.getElementById('modalDeleteSemCode').textContent = code || '';
        document.getElementById('modalDeleteSemGrades').textContent = gradeCount || 0;

        openModal('deleteSemConfirmModal');
    }

    async function executeDeleteSem() {
        const id = document.getElementById('modalDeleteSemId').value;
        if (!id) return;

        const btn = document.getElementById('btnExecuteDeleteSem');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang xóa...</span>`;

        try {
            const res = await apiPost(`<?= BASE_URL ?>/semesters/${id}`, { _method: 'DELETE' });
            if (res) {
                closeModal('deleteSemConfirmModal');
                showToast('Đã xóa học kỳ thành công!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            }
        } catch (e) {
            console.error(e);
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            showToast('Không thể xóa học kỳ, vui lòng thử lại sau!', 'error');
        }
    }

    // =========================================================================
    // XUẤT EXCEL DANH SÁCH HỌC KỲ
    // =========================================================================
    function exportSemestersExcel() {
        const semsToExport = (typeof ALL_SEMESTERS !== 'undefined' && ALL_SEMESTERS.length > 0) 
            ? ALL_SEMESTERS 
            : [];

        if (!semsToExport || semsToExport.length === 0) {
            showToast('Không có dữ liệu học kỳ để xuất!', 'warning');
            return;
        }

        const today = new Date();
        const dateStr = today.toLocaleDateString('vi-VN');
        const fileDate = today.toISOString().slice(0, 10);

        const rows = [
            ["SỞ GIÁO DỤC VÀ ĐÀO TẠO TP. HỒ CHÍ MINH", "", "", "", "CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM"],
            ["TRƯỜNG TIỂU HỌC & THCS EDUMANAGE", "", "", "", "Độc lập - Tự do - Hạnh phúc"],
            ["BỘ PHẬN QUẢN LÝ HỌC VỤ & ĐÀO TẠO", "", "", "", "-------------------------"],
            [""],
            ["DANH SÁCH THỐNG KÊ HỌC KỲ & KẾ HOẠCH ĐÀO TẠO"],
            [`Ngày xuất báo cáo: ${dateStr}   |   Tổng số học kỳ: ${semsToExport.length}`],
            [""],
            [
                "STT",
                "Mã Học Kỳ",
                "Tên Học Kỳ",
                "Niên Khóa Trực Thuộc",
                "Ngày Bắt Đầu",
                "Ngày Kết Thúc",
                "Số Bản Ghi Điểm",
                "Số Tiết TKB",
                "Học Kỳ Hiện Tại",
                "Trạng Thái"
            ]
        ];

        semsToExport.forEach((s, idx) => {
            rows.push([
                idx + 1,
                s.code || '',
                s.name || '',
                s.year_name || '',
                s.start_date ? s.start_date.split('-').reverse().join('/') : '',
                s.end_date ? s.end_date.split('-').reverse().join('/') : '',
                Number(s.grade_count || 0),
                Number(s.schedule_count || 0),
                s.is_current == 1 ? 'Hiện hành' : 'Không',
                s.status === 'active' ? 'Đang diễn ra' : (s.status === 'upcoming' ? 'Sắp tới' : 'Đã kết thúc')
            ]);
        });

        rows.push([""]);
        rows.push(["", "", "", "", "", `Ngày ${today.getDate()} tháng ${today.getMonth() + 1} năm ${today.getFullYear()}`]);
        rows.push(["", "NGƯỜI LẬP BIỂU", "", "", "", "HIỆU TRƯỞNG / BAN GIÁM HIỆU"]);
        rows.push(["", "(Ký và ghi rõ họ tên)", "", "", "", "(Ký tên và đóng dấu)"]);

        const ws = XLSX.utils.aoa_to_sheet(rows);
        ws['!cols'] = [
            { wch: 6 },
            { wch: 14 },
            { wch: 20 },
            { wch: 25 },
            { wch: 14 },
            { wch: 14 },
            { wch: 16 },
            { wch: 14 },
            { wch: 18 },
            { wch: 16 }
        ];

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "DanhSachHocKy");
        XLSX.writeFile(wb, `Danh_Sach_Hoc_Ky_EduManage_${fileDate}.xlsx`);
        showToast(`Đã xuất thành công ${semsToExport.length} học kỳ ra Excel!`, 'success');
    }
</script>
