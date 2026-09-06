<?php // views/academic_years/index.php ?>
<?php
$currentYear = null;
$totalClasses = 0;
$totalStudents = 0;
foreach ($years as $y) {
    if (!empty($y['is_current'])) {
        $currentYear = $y;
    }
    $totalClasses += (int)($y['class_count'] ?? 0);
    $totalStudents += (int)($y['student_count'] ?? 0);
}
?>
<div class="space-y-6">

    <!-- 1. HEADER & MAIN ACTIONS -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400 mb-1">
                <span class="material-symbols-outlined text-[17px]">calendar_month</span>
                <span>Quản Lý Học Vụ & Niên Khóa</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Quản Lý Năm Học</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-0.5">
                Tổng số: <strong class="text-amber-600 dark:text-amber-400"><?= count($years) ?> niên khóa</strong> &nbsp;|&nbsp; 
                Hiện hành: <strong class="text-slate-800 dark:text-slate-200"><?= htmlspecialchars($currentYear['name'] ?? 'Chưa thiết lập') ?></strong>
            </p>
        </div>
        
        <!-- Right Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <button onclick="exportYearsExcel()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/40 text-amber-700 dark:text-amber-300 rounded-xl text-xs font-bold transition-all shadow-sm" title="Xuất danh sách năm học ra Excel">
                <span class="material-symbols-outlined text-amber-600 text-[18px]">table_view</span>
                <span>Xuất Excel</span>
            </button>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-[#006c4a] hover:bg-[#005137] text-white rounded-xl text-xs font-bold transition-all shadow-sm" title="In danh sách năm học">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>In Danh Sách</span>
            </button>
            <?php if (Permission::can('academic_years.create')): ?>
            <button onclick="openYearModal('create')" class="inline-flex items-center gap-2 px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-amber-500/20 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Thêm Năm Học</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. KPI METRICS CARDS (Bento Stats) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tổng số niên khóa -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">calendar_today</span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng Niên Khóa</p>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5"><?= count($years) ?> <span class="text-xs font-normal text-slate-400">năm</span></p>
            </div>
        </div>

        <!-- Card 2: Niên khóa hiện tại -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">star</span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Năm Học Hiện Tại</p>
                <p class="text-base font-black text-emerald-600 dark:text-emerald-400 truncate mt-0.5" title="<?= htmlspecialchars($currentYear['name'] ?? 'Chưa đặt') ?>">
                    <?= htmlspecialchars($currentYear['code'] ?? 'Chưa đặt') ?>
                </p>
            </div>
        </div>

        <!-- Card 3: Học kỳ hiện hành -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">schedule</span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Học Kỳ Hiện Hành</p>
                <p class="text-base font-black text-slate-900 dark:text-white truncate mt-0.5">
                    <?= htmlspecialchars($currentSemester['name'] ?? 'Học kỳ II') ?>
                </p>
            </div>
        </div>

        <!-- Card 4: Quy mô đào tạo -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">school</span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Quy Mô Hiện Tại</p>
                <p class="text-base font-black text-slate-900 dark:text-white mt-0.5">
                    <?= (int)($currentYear['class_count'] ?? 0) ?> lớp • <?= (int)($currentYear['student_count'] ?? 0) ?> HS
                </p>
            </div>
        </div>
    </div>

    <!-- 3. TOOLBAR: FILTERS & VIEW SWITCHER -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <!-- Filter Form -->
        <form method="GET" action="<?= BASE_URL ?>/academic-years" id="yearFilterForm" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <!-- Search -->
            <div class="relative flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-1.5 focus-within:ring-2 focus-within:ring-amber-500 w-full sm:w-64 transition-all">
                <span class="material-symbols-outlined text-slate-400 mr-2 text-[18px]">search</span>
                <input name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="bg-transparent border-none outline-none w-full text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:ring-0 p-0" placeholder="Tìm tên năm học, mã niên khóa..." type="text"/>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select name="status" onchange="document.getElementById('yearFilterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-amber-500 cursor-pointer">
                    <option value="">Tất cả Trạng thái</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Đã kết thúc</option>
                    <option value="upcoming" <?= ($filters['status'] ?? '') === 'upcoming' ? 'selected' : '' ?>>Sắp tới</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Reset Filter -->
            <a href="<?= BASE_URL ?>/academic-years" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-rose-500 hover:text-rose-600 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                <span>Xóa lọc</span>
            </a>
        </form>

        <!-- View Mode Switch -->
        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 self-end sm:self-center">
            <button type="button" id="btnViewGrid" onclick="switchYearView('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-amber-600 dark:text-amber-400 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">grid_view</span>
                <span class="hidden sm:inline">Dạng Thẻ</span>
            </button>
            <button type="button" id="btnViewTable" onclick="switchYearView('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white">
                <span class="material-symbols-outlined text-[16px]">table_rows</span>
                <span class="hidden sm:inline">Dạng Bảng</span>
            </button>
        </div>
    </div>

    <!-- 4. VIEW 1: BENTO YEAR CARDS GRID -->
    <div id="yearsGridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php if (!empty($years)): ?>
            <?php foreach ($years as $y):
                $isCurr = !empty($y['is_current']);
                $isLocked = !empty($y['is_locked']);
                $cardBorder = $isCurr ? 'border-amber-400 dark:border-amber-500 ring-2 ring-amber-400/20 shadow-md' : 'border-slate-200 dark:border-slate-800';
            ?>
            <div class="bg-white dark:bg-slate-900 border <?= $cardBorder ?> rounded-3xl overflow-hidden hover:shadow-lg transition-all duration-200 group flex flex-col relative">
                <!-- Top Accent Bar -->
                <div class="h-2 w-full <?= $isCurr ? 'bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600' : 'bg-gradient-to-r from-slate-300 via-slate-400 to-slate-500 dark:from-slate-700 dark:to-slate-800' ?>"></div>

                <div class="p-5 flex flex-col flex-1 space-y-4">
                    <!-- Top Meta: Code & Status & Star -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                <span class="font-mono text-xs font-black px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40">
                                    <?= htmlspecialchars($y['code']) ?>
                                </span>

                                <?php if ($isCurr): ?>
                                <span class="text-[10px] font-extrabold text-amber-700 bg-amber-100 dark:bg-amber-950 dark:text-amber-300 px-2 py-0.5 rounded-full border border-amber-300 dark:border-amber-700 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">star</span> HIỆN TẠI
                                </span>
                                <?php endif; ?>

                                <?php if ($y['status'] === 'active'): ?>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/80 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                                    ● HOẠT ĐỘNG
                                </span>
                                <?php elseif ($y['status'] === 'upcoming'): ?>
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 dark:bg-blue-950/80 px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-800">
                                    ● SẮP TỚI
                                </span>
                                <?php else: ?>
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full border border-slate-300 dark:border-slate-700">
                                    ● KẾT THÚC
                                </span>
                                <?php endif; ?>

                                <?php if ($isLocked): ?>
                                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/80 px-2 py-0.5 rounded-full border border-rose-200 dark:border-rose-800 flex items-center gap-0.5" title="Đã khóa sổ dữ liệu">
                                    <span class="material-symbols-outlined text-[12px]">lock</span> ĐÃ KHÓA
                                </span>
                                <?php endif; ?>
                            </div>

                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white group-hover:text-amber-600 transition-colors">
                                <?= htmlspecialchars($y['name']) ?>
                            </h3>
                        </div>
                    </div>

                    <!-- Date Timeline Box -->
                    <div class="grid grid-cols-2 gap-2.5 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bắt đầu</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-emerald-500">event_available</span>
                                <?= !empty($y['start_date']) ? date('d/m/Y', strtotime($y['start_date'])) : '—' ?>
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kết thúc</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-rose-500">event_busy</span>
                                <?= !empty($y['end_date']) ? date('d/m/Y', strtotime($y['end_date'])) : '—' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Stats Strip: Classes, Students, Semesters -->
                    <div class="grid grid-cols-3 gap-2 py-1 text-center text-xs">
                        <div class="p-2 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] block">Lớp học</span>
                            <span class="font-extrabold text-slate-900 dark:text-white text-sm"><?= (int)($y['class_count'] ?? 0) ?></span>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] block">Học sinh</span>
                            <span class="font-extrabold text-slate-900 dark:text-white text-sm"><?= (int)($y['student_count'] ?? 0) ?></span>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] block">Học kỳ</span>
                            <span class="font-extrabold text-slate-900 dark:text-white text-sm"><?= (int)($y['semester_count'] ?? 0) ?></span>
                        </div>
                    </div>

                    <!-- Card Actions Footer -->
                    <div class="pt-3 flex items-center justify-between gap-2 border-t border-slate-100 dark:border-slate-800 mt-auto">
                        <div>
                            <?php if (!$isCurr && Permission::can('academic_years.update')): ?>
                            <button onclick="setYearAsCurrent(<?= $y['id'] ?>, '<?= htmlspecialchars(addslashes($y['name'])) ?>')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 text-xs font-bold transition-all"
                                    title="Đặt làm năm học hiện hành cho toàn trường">
                                <span class="material-symbols-outlined text-[15px]">star</span>
                                <span>Đặt hiện hành</span>
                            </button>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 dark:text-amber-400 px-2 py-1">
                                <span class="material-symbols-outlined text-[15px]">verified</span> Niên khóa chính
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-1">
                            <?php if (Permission::can('academic_years.update')): ?>
                            <button onclick="toggleLockYear(<?= $y['id'] ?>, <?= $isLocked ? 0 : 1 ?>, '<?= htmlspecialchars(addslashes($y['name'])) ?>')" 
                                    class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/60 rounded-xl transition-colors" 
                                    title="<?= $isLocked ? 'Mở khóa sổ năm học' : 'Khóa sổ năm học' ?>">
                                <span class="material-symbols-outlined text-[18px]"><?= $isLocked ? 'lock' : 'lock_open' ?></span>
                            </button>
                            <button onclick="openYearModal('edit', <?= htmlspecialchars(json_encode($y), ENT_QUOTES) ?>)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-xl transition-colors" title="Chỉnh sửa năm học">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <?php endif; ?>
                            <?php if (Permission::can('academic_years.delete')): ?>
                            <button onclick="openDeleteYearModal(<?= $y['id'] ?>, '<?= htmlspecialchars(addslashes($y['name'])) ?>', '<?= htmlspecialchars($y['code']) ?>', <?= $isCurr ? 1 : 0 ?>, <?= (int)($y['class_count'] ?? 0) ?>)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors" title="Xóa năm học">
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
                    <span class="material-symbols-outlined text-3xl text-slate-400">calendar_today</span>
                </div>
                <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy niên khóa nào</p>
                <p class="text-xs text-slate-400 mt-1">Thử thay đổi bộ lọc tìm kiếm hoặc tạo thêm năm học mới.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 5. VIEW 2: DATA TABLE -->
    <div id="yearsTableView" class="hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px] text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="p-3.5 pl-5 w-12 text-center">STT</th>
                        <th class="p-3.5">MÃ NIÊN KHÓA</th>
                        <th class="p-3.5">TÊN NĂM HỌC</th>
                        <th class="p-3.5">THỜI GIAN ĐÀO TẠO</th>
                        <th class="p-3.5 text-center">LỚP HỌC</th>
                        <th class="p-3.5 text-center">HỌC SINH</th>
                        <th class="p-3.5 text-center">HỌC KỲ</th>
                        <th class="p-3.5">HIỆN TẠI</th>
                        <th class="p-3.5">TRẠNG THÁI</th>
                        <th class="p-3.5 pr-5 text-right">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-200">
                    <?php if (!empty($years)): ?>
                        <?php foreach ($years as $idx => $y):
                            $isCurr = !empty($y['is_current']);
                            $isLocked = !empty($y['is_locked']);
                        ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="p-3.5 pl-5 text-center font-normal text-slate-400"><?= $idx + 1 ?></td>
                            <td class="p-3.5 font-mono font-bold text-amber-600 dark:text-amber-400"><?= htmlspecialchars($y['code']) ?></td>
                            <td class="p-3.5 font-bold text-slate-900 dark:text-white text-sm"><?= htmlspecialchars($y['name']) ?></td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-300">
                                <?= date('d/m/Y', strtotime($y['start_date'])) ?> &rarr; <?= date('d/m/Y', strtotime($y['end_date'])) ?>
                            </td>
                            <td class="p-3.5 text-center font-bold text-slate-800 dark:text-slate-200"><?= (int)($y['class_count'] ?? 0) ?></td>
                            <td class="p-3.5 text-center font-bold text-slate-800 dark:text-slate-200"><?= (int)($y['student_count'] ?? 0) ?></td>
                            <td class="p-3.5 text-center font-bold text-slate-800 dark:text-slate-200"><?= (int)($y['semester_count'] ?? 0) ?></td>
                            <td class="p-3.5">
                                <?php if ($isCurr): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 text-[10px] font-bold border border-amber-200 dark:border-amber-800">
                                    ⭐ HIỆN HÀNH
                                </span>
                                <?php else: ?>
                                <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5">
                                <?php if ($y['status'] === 'active'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                                    ● HOẠT ĐỘNG
                                </span>
                                <?php elseif ($y['status'] === 'upcoming'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-950/80 dark:text-blue-300 text-[10px] font-bold uppercase tracking-wider border border-blue-200 dark:border-blue-800">
                                    ● SẮP TỚI
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                    ● KẾT THÚC
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if (!$isCurr && Permission::can('academic_years.update')): ?>
                                    <button onclick="setYearAsCurrent(<?= $y['id'] ?>, '<?= htmlspecialchars(addslashes($y['name'])) ?>')" 
                                            class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/60 rounded-lg transition-colors" 
                                            title="Đặt làm hiện tại">
                                        <span class="material-symbols-outlined text-[18px]">star</span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if (Permission::can('academic_years.update')): ?>
                                    <button onclick="openYearModal('edit', <?= htmlspecialchars(json_encode($y), ENT_QUOTES) ?>)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-lg transition-colors" title="Sửa năm học">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if (Permission::can('academic_years.delete')): ?>
                                    <button onclick="openDeleteYearModal(<?= $y['id'] ?>, '<?= htmlspecialchars(addslashes($y['name'])) ?>', '<?= htmlspecialchars($y['code']) ?>', <?= $isCurr ? 1 : 0 ?>, <?= (int)($y['class_count'] ?? 0) ?>)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-lg transition-colors" title="Xóa năm học">
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

<!-- Modal: Thêm / Sửa Năm Học (Modern Rounded-3xl Glass) -->
<div id="yearModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl" id="modalYearIcon">calendar_today</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white" id="modalYearTitle">Thêm Năm Học Mới</h3>
                    <p class="text-xs text-slate-400">Thiết lập thông tin niên khóa và khoảng thời gian đào tạo</p>
                </div>
            </div>
            <button onclick="closeModal('yearModal')" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Form -->
        <form id="yearForm" onsubmit="handleYearSubmit(event)" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="id" id="yearId" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Tên Năm Học <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="yearName" required oninput="autoSuggestYearCode(this.value)"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none"
                           placeholder="VD: Năm học 2026 – 2027">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Mã Niên Khóa <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="yearCode" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-mono font-bold text-amber-600 dark:text-amber-400 focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none uppercase"
                           placeholder="VD: 2026-2027">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ngày Bắt Đầu <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="yearStartDate" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ngày Kết Thúc <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="yearEndDate" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Trạng Thái Niên Khóa
                </label>
                <select name="status" id="yearStatus" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none cursor-pointer">
                    <option value="active">Đang hoạt động (active)</option>
                    <option value="completed">Đã kết thúc (completed)</option>
                    <option value="upcoming">Sắp tới (upcoming)</option>
                </select>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 hover:bg-amber-50/40 cursor-pointer transition-colors">
                    <input type="checkbox" name="is_current" id="yearIsCurrent" value="1" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200">Đặt làm Năm Học Hiện Tại</span>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tất cả lớp học và học vụ mặc định sẽ áp dụng theo niên khóa này</p>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 hover:bg-rose-50/40 cursor-pointer transition-colors">
                    <input type="checkbox" name="is_locked" id="yearIsLocked" value="1" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200">Khóa sổ dữ liệu (Chỉ xem)</span>
                        <p class="text-[11px] text-slate-400 mt-0.5">Ngăn ngừa sửa điểm, chuyển lớp sau khi năm học đã hoàn tất</p>
                    </div>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('yearModal')" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-50 transition-colors">
                    Hủy Bỏ
                </button>
                <button type="submit" id="btnSubmitYear" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-md shadow-amber-500/20 active:scale-[0.98] transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span id="btnSubmitYearText">Lưu Năm Học</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Xác Nhận Xóa Năm Học (Chuẩn Theo Mẫu Cảnh Báo Đỏ) -->
<div id="deleteYearConfirmModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden text-center transform transition-all scale-100 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Header: Nền hồng đỏ nhạt có tam giác cảnh báo ở giữa -->
        <div class="bg-[#feecee] dark:bg-rose-950/40 border-b border-rose-100 dark:border-rose-900/50 pt-7 pb-6 px-6">
            <div class="w-12 h-12 rounded-full bg-rose-200/80 dark:bg-rose-900/70 text-rose-700 dark:text-rose-300 flex items-center justify-center mx-auto mb-3 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-rose-700 dark:text-rose-300">
                Xác nhận xóa năm học?
            </h3>
        </div>

        <!-- Body: Nền trắng có đoạn văn cảnh báo và thẻ năm học -->
        <div class="p-6 sm:p-7 bg-white dark:bg-slate-900 space-y-6">
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed text-center">
                Hành động này không thể hoàn tác. Năm học chỉ có thể xóa khi không còn lớp học nào trực thuộc và không phải niên khóa hiện tại.
            </p>

            <!-- Khung thông tin năm học -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-4 bg-white dark:bg-slate-800/50 flex items-center gap-4 text-left shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 dark:border-rose-900/40">
                    <span class="material-symbols-outlined text-2xl">calendar_today</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span id="modalDeleteYearName" class="font-bold text-slate-900 dark:text-white text-base truncate">Năm học 2024 – 2025</span>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            Mã: <span id="modalDeleteYearCode">2024-2025</span>
                        </span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            Lớp học: <span id="modalDeleteYearClasses">0</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden ID -->
        <input type="hidden" id="modalDeleteYearId" value="">

        <!-- Footer: Nút [Hủy bỏ] và [Xác nhận xóa] -->
        <div class="p-4 sm:p-5 px-7 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('deleteYearConfirmModal')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition-all shadow-sm">
                Hủy bỏ
            </button>
            <button type="button" id="btnExecuteDeleteYear" onclick="executeDeleteYear()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#b91c1c] hover:bg-[#991b1b] active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>Xác nhận xóa</span>
            </button>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    const ALL_YEARS = <?= json_encode($allYearsForExport ?? $years ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // =========================================================================
    // CHUYỂN ĐỔI CHẾ ĐỘ XEM (VIEW SWITCH: GRID / TABLE)
    // =========================================================================
    function switchYearView(mode) {
        const gridView = document.getElementById('yearsGridView');
        const tableView = document.getElementById('yearsTableView');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnTable = document.getElementById('btnViewTable');

        if (mode === 'table') {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');

            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-amber-600 dark:text-amber-400 shadow-sm';
            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('academic_year_view_mode', 'table');
        } else {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');

            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-amber-600 dark:text-amber-400 shadow-sm';
            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('academic_year_view_mode', 'grid');
        }
    }

    // Auto restore previous view mode
    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('academic_year_view_mode');
        if (savedMode === 'table') switchYearView('table');
    });

    // Tự động gợi ý mã niên khóa khi gõ tên năm học
    function autoSuggestYearCode(val) {
        const matches = val.match(/\d{4}/g);
        if (matches && matches.length >= 2) {
            document.getElementById('yearCode').value = `${matches[0]}-${matches[1]}`;
        }
    }

    // =========================================================================
    // MODAL THÊM / SỬA NĂM HỌC
    // =========================================================================
    function openYearModal(mode, data = null) {
        const isEdit = mode === 'edit';
        document.getElementById('modalYearTitle').textContent = isEdit ? 'Cập Nhật Năm Học' : 'Thêm Năm Học Mới';
        document.getElementById('modalYearIcon').textContent = isEdit ? 'edit_square' : 'calendar_today';
        document.getElementById('btnSubmitYearText').textContent = isEdit ? 'Lưu Thay Đổi' : 'Tạo Năm Học';

        if (isEdit && data) {
            document.getElementById('yearId').value = data.id;
            document.getElementById('yearName').value = data.name || '';
            document.getElementById('yearCode').value = data.code || '';
            document.getElementById('yearStartDate').value = data.start_date || '';
            document.getElementById('yearEndDate').value = data.end_date || '';
            document.getElementById('yearStatus').value = data.status || 'active';
            document.getElementById('yearIsCurrent').checked = Number(data.is_current) === 1;
            document.getElementById('yearIsLocked').checked = Number(data.is_locked) === 1;
        } else {
            document.getElementById('yearId').value = '';
            document.getElementById('yearName').value = '';
            document.getElementById('yearCode').value = '';
            
            // Auto default dates: next school year
            const curY = new Date().getFullYear();
            document.getElementById('yearStartDate').value = `${curY}-09-05`;
            document.getElementById('yearEndDate').value = `${curY + 1}-05-30`;
            document.getElementById('yearStatus').value = 'active';
            document.getElementById('yearIsCurrent').checked = false;
            document.getElementById('yearIsLocked').checked = false;
        }

        openModal('yearModal');
    }

    async function handleYearSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitYear');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Handle checkbox booleans
        data.is_current = document.getElementById('yearIsCurrent').checked ? 1 : 0;
        data.is_locked = document.getElementById('yearIsLocked').checked ? 1 : 0;

        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang lưu...</span>`;

        const isEdit = Boolean(data.id);
        const url = isEdit ? `<?= BASE_URL ?>/academic-years/${data.id}` : '<?= BASE_URL ?>/academic-years';

        try {
            const res = await apiPost(url, data);
            if (res) {
                closeModal('yearModal');
                showToast(isEdit ? 'Đã cập nhật năm học thành công!' : 'Đã tạo mới năm học thành công!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span>${isEdit ? 'Lưu Thay Đổi' : 'Tạo Năm Học'}</span>`;
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span>${isEdit ? 'Lưu Thay Đổi' : 'Tạo Năm Học'}</span>`;
        }
    }

    // =========================================================================
    // ĐẶT LÀM NĂM HỌC HIỆN TẠI (ONE-CLICK AJAX)
    // =========================================================================
    async function setYearAsCurrent(id, name) {
        try {
            showToast(`Đang đặt "${name}" làm niên khóa hiện tại...`, 'info');
            const res = await apiPost(`<?= BASE_URL ?>/academic-years/${id}`, { is_current: 1 });
            if (res) {
                showToast(`Đã thiết lập "${name}" làm niên khóa hiện hành!`, 'success');
                setTimeout(() => location.reload(), 700);
            }
        } catch (e) {
            console.error(e);
            showToast('Lỗi khi thiết lập niên khóa hiện tại!', 'error');
        }
    }

    // =========================================================================
    // KHÓA SỔ / MỞ KHÓA NĂM HỌC (ONE-CLICK AJAX)
    // =========================================================================
    async function toggleLockYear(id, lockStatus, name) {
        try {
            const actionText = lockStatus === 1 ? 'khóa sổ' : 'mở khóa';
            showToast(`Đang ${actionText} niên khóa "${name}"...`, 'info');
            const res = await apiPost(`<?= BASE_URL ?>/academic-years/${id}`, { is_locked: lockStatus });
            if (res) {
                showToast(`Đã ${actionText} niên khóa "${name}" thành công!`, 'success');
                setTimeout(() => location.reload(), 700);
            }
        } catch (e) {
            console.error(e);
            showToast('Lỗi khi cập nhật trạng thái khóa sổ!', 'error');
        }
    }

    // =========================================================================
    // MODAL XÁC NHẬN XÓA NĂM HỌC
    // =========================================================================
    function openDeleteYearModal(id, name, code, isCurrent, classCount) {
        if (isCurrent === 1) {
            showToast(`Không thể xóa "${name}" vì đây là niên khóa hiện hành của trường!`, 'warning');
            return;
        }
        if (classCount > 0) {
            showToast(`Không thể xóa "${name}" vì đang có ${classCount} lớp học trực thuộc niên khóa này!`, 'warning');
            return;
        }

        document.getElementById('modalDeleteYearId').value = id;
        document.getElementById('modalDeleteYearName').textContent = name || '';
        document.getElementById('modalDeleteYearCode').textContent = code || '';
        document.getElementById('modalDeleteYearClasses').textContent = classCount || 0;

        openModal('deleteYearConfirmModal');
    }

    async function executeDeleteYear() {
        const id = document.getElementById('modalDeleteYearId').value;
        if (!id) return;

        const btn = document.getElementById('btnExecuteDeleteYear');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang xóa...</span>`;

        try {
            const res = await apiPost(`<?= BASE_URL ?>/academic-years/${id}`, { _method: 'DELETE' });
            if (res) {
                closeModal('deleteYearConfirmModal');
                showToast('Đã xóa năm học thành công!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            }
        } catch (e) {
            console.error(e);
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            showToast('Không thể xóa năm học, vui lòng thử lại sau!', 'error');
        }
    }

    // =========================================================================
    // XUẤT EXCEL DANH SÁCH NĂM HỌC
    // =========================================================================
    function exportYearsExcel() {
        const yearsToExport = (typeof ALL_YEARS !== 'undefined' && ALL_YEARS.length > 0) 
            ? ALL_YEARS 
            : [];

        if (!yearsToExport || yearsToExport.length === 0) {
            showToast('Không có dữ liệu năm học để xuất!', 'warning');
            return;
        }

        const today = new Date();
        const dateStr = today.toLocaleDateString('vi-VN');
        const fileDate = today.toISOString().slice(0, 10);

        const rows = [
            ["SỞ GIÁO DỤC VÀ ĐÀO TẠO TP. HỒ CHÍ MINH", "", "", "", "CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM"],
            ["TRƯỜNG TIỂU HỌC & THCS EDUMANAGE", "", "", "", "Độc lập - Tự do - Hạnh phúc"],
            ["BỘ PHẬN QUẢN LÝ HỌC VỤ & NIÊN KHÓA", "", "", "", "-------------------------"],
            [""],
            ["DANH SÁCH THỐNG KÊ NIÊN KHÓA & NĂM HỌC"],
            [`Ngày xuất báo cáo: ${dateStr}   |   Tổng số niên khóa: ${yearsToExport.length}`],
            [""],
            [
                "STT",
                "Mã Niên Khóa",
                "Tên Năm Học",
                "Ngày Bắt Đầu",
                "Ngày Kết Thúc",
                "Số Lớp Học",
                "Số Học Sinh",
                "Số Học Kỳ",
                "Niên Khóa Hiện Tại",
                "Khóa Sổ Dữ Liệu",
                "Trạng Thái"
            ]
        ];

        yearsToExport.forEach((y, idx) => {
            rows.push([
                idx + 1,
                y.code || '',
                y.name || '',
                y.start_date ? y.start_date.split('-').reverse().join('/') : '',
                y.end_date ? y.end_date.split('-').reverse().join('/') : '',
                Number(y.class_count || 0),
                Number(y.student_count || 0),
                Number(y.semester_count || 0),
                y.is_current == 1 ? 'Hiển hành' : 'Không',
                y.is_locked == 1 ? 'Đã khóa' : 'Mở',
                y.status === 'active' ? 'Đang hoạt động' : (y.status === 'upcoming' ? 'Sắp tới' : 'Đã kết thúc')
            ]);
        });

        rows.push([""]);
        rows.push(["", "", "", "", "", "", `Ngày ${today.getDate()} tháng ${today.getMonth() + 1} năm ${today.getFullYear()}`]);
        rows.push(["", "NGƯỜI LẬP BIỂU", "", "", "", "", "HIỆU TRƯỞNG / BAN GIÁM HIỆU"]);
        rows.push(["", "(Ký và ghi rõ họ tên)", "", "", "", "", "(Ký tên và đóng dấu)"]);

        const ws = XLSX.utils.aoa_to_sheet(rows);
        ws['!cols'] = [
            { wch: 6 },
            { wch: 15 },
            { wch: 25 },
            { wch: 14 },
            { wch: 14 },
            { wch: 12 },
            { wch: 14 },
            { wch: 12 },
            { wch: 18 },
            { wch: 16 },
            { wch: 16 }
        ];

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "DanhSachNamHoc");
        XLSX.writeFile(wb, `Danh_Sach_Nam_Hoc_EduManage_${fileDate}.xlsx`);
        showToast(`Đã xuất thành công ${yearsToExport.length} niên khóa ra Excel!`, 'success');
    }
</script>
