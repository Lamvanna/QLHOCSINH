<?php // views/grades/index.php ?>
<?php
$studentsList = $gradeData['students'] ?? [];
$subjects = $gradeData['subjects'] ?? [];
$stats = $gradeData['stats'] ?? [];

$className = htmlspecialchars($selectedClassObj['name'] ?? ('Lớp ' . $selectedClass));
$yearName = htmlspecialchars($selectedYearObj['name'] ?? ('Năm học #' . $selectedAcademicYear));

$isAllLocked = count($studentsList) > 0;
if (empty($studentsList)) {
    $isAllLocked = false;
} else {
    foreach ($studentsList as $st) {
        if (!$st['is_locked']) {
            $isAllLocked = false;
            break;
        }
    }
}

$numSubjects = count($subjects);
$maxPossibleTotal = $numSubjects * 10;
$todayDay = date('d');
$todayMonth = date('m');
$todayYear = date('Y');
?>

<!-- ================= KHỐI TIÊU ĐỀ IN CHUẨN BIỂU MẪU HỌC ĐƯỜNG (CHỈ HIỆN KHI IN - PRINT ONLY) ================= -->
<div class="print-only" style="display: none;">
    <div class="print-header-grid">
        <div class="print-header-left">
            <div class="font-bold text-xs uppercase tracking-wider">SỞ GIÁO DỤC VÀ ĐÀO TẠO</div>
            <div class="font-black text-sm uppercase">TRƯỜNG TIỂU HỌC / THCS / THPT EDUMANAGE</div>
            <div class="text-xs mt-1">Lớp: <strong class="text-sm font-black"><?= $className ?></strong></div>
        </div>
        <div class="print-header-right">
            <div class="font-bold text-xs uppercase">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
            <div class="font-bold text-xs">Độc lập - Tự do - Hạnh phúc</div>
            <div class="print-dash">-----------------------</div>
        </div>
    </div>

    <div class="print-title-box">
        <h1 class="print-main-title">BẢNG TỔNG HỢP ĐIỂM THI VÀ ĐÁNH GIÁ HỌC SINH</h1>
        <div class="print-sub-title">NĂM HỌC: <?= mb_strtoupper($yearName, 'UTF-8') ?></div>
        <div class="print-meta-info">
            <span>Sĩ số: <strong><?= count($studentsList) ?></strong> học sinh</span>
            <span>•</span>
            <span>Số môn thi đánh giá: <strong><?= $numSubjects ?></strong> môn</span>
            <span>•</span>
            <span>Ngày in biểu mẫu: <?= date('d/m/Y') ?></span>
        </div>
    </div>
</div>

<div class="space-y-5 pb-12 screen-only">
    <!-- Hero Header with Gradient -->
    <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 rounded-3xl p-6 sm:p-8 overflow-hidden shadow-lg">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>
        <div class="absolute top-0 right-0 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div>
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-500/20 backdrop-blur-sm border border-teal-400/30 flex items-center justify-center">
                        <i data-lucide="book-open-check" class="w-5 h-5 text-teal-400"></i>
                    </div>
                    <?php if ($isAllLocked): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-300 border border-rose-400/30 backdrop-blur-sm">
                        <i data-lucide="lock" class="w-3 h-3"></i> Đã Khóa Sổ
                    </span>
                    <?php endif; ?>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Quản Lý Điểm Số
                </h1>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2">
                    <span class="inline-flex items-center gap-1.5 text-sm text-slate-300">
                        <i data-lucide="school" class="w-3.5 h-3.5 text-teal-400"></i>
                        <?= $className ?>
                    </span>
                    <span class="text-slate-600">•</span>
                    <span class="text-sm text-slate-300">
                        <strong class="text-white"><?= count($studentsList) ?></strong> học sinh
                    </span>
                    <span class="text-slate-600">•</span>
                    <span class="text-sm text-slate-300">
                        <strong class="text-white"><?= $numSubjects ?></strong> môn
                    </span>
                    <span class="text-slate-600">•</span>
                    <span class="text-sm text-teal-300 font-semibold"><?= $yearName ?></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Dropdown Sắp Xếp Nhanh -->
                <div class="relative inline-block text-left" id="sortDropdownContainer">
                    <button onclick="toggleSortMenu()" type="button" id="btnSortDropdown" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/10 text-white rounded-xl text-xs font-semibold transition-all">
                        <i data-lucide="arrow-down-up" class="w-3.5 h-3.5 text-amber-300"></i>
                        <span id="currentSortLabel">Sắp Xếp</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 ml-0.5"></i>
                    </button>
                    <div id="sortMenuDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-2 z-50 animate-in fade-in zoom-in-95">
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Tiêu chí sắp xếp</div>
                        <button onclick="applySort('default')" class="w-full text-left px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 flex items-center justify-between font-medium">
                            <span class="flex items-center gap-2"><i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-400"></i> Thứ tự mặc định</span>
                        </button>
                        <button onclick="applySort('total_desc')" class="w-full text-left px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 flex items-center justify-between font-medium">
                            <span class="flex items-center gap-2"><i data-lucide="trophy" class="w-3.5 h-3.5 text-amber-500"></i> Tổng điểm: Cao ➔ Thấp</span>
                        </button>
                        <button onclick="applySort('total_asc')" class="w-full text-left px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 flex items-center justify-between font-medium">
                            <span class="flex items-center gap-2"><i data-lucide="trending-up" class="w-3.5 h-3.5 text-teal-500"></i> Tổng điểm: Thấp ➔ Cao</span>
                        </button>
                        <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                        <button onclick="applySort('name_asc')" class="w-full text-left px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 flex items-center justify-between font-medium">
                            <span class="flex items-center gap-2"><i data-lucide="arrow-down-a-z" class="w-3.5 h-3.5 text-blue-500"></i> Tên học sinh: A ➔ Z</span>
                        </button>
                        <button onclick="applySort('name_desc')" class="w-full text-left px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 flex items-center justify-between font-medium">
                            <span class="flex items-center gap-2"><i data-lucide="arrow-up-z-a" class="w-3.5 h-3.5 text-indigo-500"></i> Tên học sinh: Z ➔ A</span>
                        </button>
                        <button onclick="applySort('code_asc')" class="w-full text-left px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 flex items-center justify-between font-medium">
                            <span class="flex items-center gap-2"><i data-lucide="binary" class="w-3.5 h-3.5 text-slate-400"></i> Mã học sinh: Tăng dần</span>
                        </button>
                    </div>
                </div>

                <!-- Nút Xuất Excel -->
                <button onclick="exportGradesToExcel()" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/10 text-white rounded-xl text-xs font-semibold transition-all">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-teal-300"></i>
                    Excel
                </button>

                <!-- Nút In Bảng Điểm Hoàn Thiện -->
                <button onclick="printGradeSheet()" type="button" title="In Bảng Điểm Khổ Ngang Chuẩn A4" class="inline-flex items-center gap-1.5 px-3 py-2 bg-teal-500/20 hover:bg-teal-500/30 backdrop-blur-sm border border-teal-400/40 text-teal-200 rounded-xl text-xs font-bold transition-all shadow-sm">
                    <i data-lucide="printer" class="w-3.5 h-3.5 text-teal-300"></i>
                    In Bảng Điểm
                </button>

                <?php if (!$isAllLocked && Permission::can('grades.create')): ?>
                <button onclick="openQuickFillModal()" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/10 text-white rounded-xl text-xs font-semibold transition-all">
                    <i data-lucide="clipboard-paste" class="w-3.5 h-3.5 text-amber-300"></i>
                    Điền Nhanh
                </button>
                <button onclick="saveGrades(true)" type="button" id="btnSaveDraft" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-400/30 text-amber-200 rounded-xl text-xs font-bold transition-all active:scale-95">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    Nháp
                </button>
                <button onclick="saveGrades(false)" type="button" id="btnSaveOfficial" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-500 hover:bg-teal-400 text-white rounded-xl text-xs font-black transition-all shadow-lg shadow-teal-500/30 active:scale-95">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    Lưu Chính Thức
                </button>
                <?php endif; ?>

                <?php if (Permission::can('grades.lock')): ?>
                    <?php if ($isAllLocked): ?>
                        <?php if (Permission::can('grades.unlock')): ?>
                        <button onclick="openUnlockModal()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-black transition-all shadow-lg shadow-amber-500/30 active:scale-95">
                            <i data-lucide="lock-open" class="w-3.5 h-3.5"></i>
                            Mở Khóa
                        </button>
                        <?php endif; ?>
                    <?php else: ?>
                    <button onclick="openLockModal()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-rose-500/20 border border-white/10 hover:border-rose-400/30 text-white rounded-xl text-xs font-bold transition-all">
                        <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400"></i>
                        Khóa Sổ
                    </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Locked Banner Notification (When locked) -->
    <?php if ($isAllLocked): ?>
    <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/30 border border-amber-200/80 dark:border-amber-800/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900 dark:text-white">Bảng điểm lớp <?= $className ?> đã được Chốt và Khóa sổ an toàn</p>
                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">Các ô điểm hiện ở chế độ chỉ đọc. Chỉ tài khoản quản trị có quyền mở khóa mới có thể điều chỉnh lại điểm số.</p>
            </div>
        </div>
        <?php if (Permission::can('grades.unlock')): ?>
        <button onclick="openUnlockModal()" class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300 hover:bg-amber-50 rounded-xl text-xs font-bold transition-all flex-shrink-0 shadow-sm">
            Mở Khóa Ngay
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Filter + KPI Combined Card -->
    <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <!-- Filter Row -->
        <form method="GET" action="<?= BASE_URL ?>/grades" id="gradeFilterForm" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 border-b border-slate-100 dark:border-slate-800">
            <div class="relative">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Lớp Học</label>
                <div class="relative">
                    <i data-lucide="school" class="w-4 h-4 text-teal-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <select name="class_id" onchange="this.form.submit()" class="w-full pl-9 pr-8 py-2.5 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 appearance-none focus:ring-2 focus:ring-teal-500/40 focus:border-teal-500 focus:outline-none cursor-pointer transition-all">
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Năm Học</label>
                <div class="relative">
                    <i data-lucide="calendar" class="w-4 h-4 text-teal-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full pl-9 pr-8 py-2.5 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 appearance-none focus:ring-2 focus:ring-teal-500/40 focus:border-teal-500 focus:outline-none cursor-pointer transition-all">
                        <?php foreach ($academicYears as $yr): ?>
                        <option value="<?= $yr['id'] ?>" <?= $selectedAcademicYear == $yr['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($yr['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Tìm Học Sinh</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="text" id="quickStudentSearch" onkeyup="filterStudentRows()" placeholder="Tên hoặc mã HS..." 
                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-teal-500/40 focus:border-teal-500 focus:outline-none transition-all">
                </div>
            </div>
        </form>

        <!-- Mini KPI Row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-slate-100 dark:divide-slate-800">
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500/10 to-teal-600/20 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-4.5 h-4.5 text-teal-600 dark:text-teal-400"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sĩ số</p>
                    <p class="text-lg font-black text-slate-900 dark:text-white leading-tight" id="kpiStudentCount"><?= count($studentsList) ?> <span class="text-[10px] font-medium text-slate-400">HS</span></p>
                </div>
            </div>
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500/10 to-blue-600/20 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="book-marked" class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Môn học</p>
                    <p class="text-lg font-black text-slate-900 dark:text-white leading-tight" id="kpiSubjectCount"><?= $numSubjects ?> <span class="text-[10px] font-medium text-slate-400">/ <?= $maxPossibleTotal ?>đ</span></p>
                </div>
            </div>
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500/10 to-amber-600/20 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="trophy" class="w-4.5 h-4.5 text-amber-500 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cao nhất</p>
                    <p class="text-lg font-black text-amber-600 dark:text-amber-400 leading-tight" id="kpiHighestTotal"><?= number_format($stats['highest_total'] ?? 0, 1) ?><span class="text-[10px] font-medium text-slate-400">đ</span></p>
                </div>
            </div>
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500/10 to-indigo-600/20 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="trending-up" class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tiến độ</p>
                    <div class="flex items-center gap-2">
                        <p class="text-lg font-black text-indigo-600 dark:text-indigo-400 leading-tight" id="kpiCompletionRate"><?= $stats['completion_rate'] ?? 0 ?>%</p>
                        <div class="w-16 bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div id="kpiProgressBar" class="bg-gradient-to-r from-teal-500 to-indigo-500 h-full rounded-full transition-all duration-500" style="width: <?= min(100, $stats['completion_rate'] ?? 0) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Table Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
        <!-- Table Header & Sort Info -->
        <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-900">
            <div class="flex items-center gap-2">
                <i data-lucide="table-2" class="w-4 h-4 text-teal-600 dark:text-teal-400"></i>
                <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider">Bảng Điểm</span>
                <span class="text-[10px] text-slate-400 font-medium">• <?= count($studentsList) ?> HS • <?= $numSubjects ?> môn</span>
                <span id="sortBadgeIndicator" class="hidden ml-2 px-2 py-0.5 rounded-md bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800 text-[10px] font-bold">
                    <!-- Text updated via JS -->
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span id="unsavedIndicator" class="hidden items-center gap-1.5 text-amber-600 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-950/60 px-2.5 py-1 rounded-lg border border-amber-200/80 dark:border-amber-800 text-[11px]">
                    <i data-lucide="alert-circle" class="w-3 h-3"></i> Chưa lưu
                </span>
            </div>
        </div>

        <!-- Table Container with drag-scroll -->
        <div class="relative">
            <div class="overflow-x-auto grade-scroll-container" id="gradeScrollContainer">
            <table class="min-w-full text-left text-xs border-collapse" id="gradesTable" style="width: max-content;">
                <thead>
                    <tr class="bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-extrabold uppercase text-[10px] tracking-wider select-none">
                        <!-- STT (Sortable: Default) -->
                        <th onclick="toggleSortHeader('stt')" class="px-3 py-3.5 text-center w-12 cursor-pointer hover:bg-slate-200/60 dark:hover:bg-slate-700/60 transition-colors group" title="Bấm để quay về thứ tự ban đầu">
                            <div class="flex items-center justify-center gap-1">
                                <span>STT</span>
                                <i data-lucide="chevrons-up-down" class="w-3 h-3 text-slate-400 group-hover:text-teal-500 transition-colors sort-icon" id="sortIcon_stt"></i>
                            </div>
                        </th>
                        
                        <!-- Mã Học Sinh (Sortable) -->
                        <th onclick="toggleSortHeader('code')" class="px-3.5 py-3.5 w-28 font-mono cursor-pointer hover:bg-slate-200/60 dark:hover:bg-slate-700/60 transition-colors group" title="Bấm để sắp xếp theo Mã HS">
                            <div class="flex items-center gap-1">
                                <span>Mã Học Sinh</span>
                                <i data-lucide="chevrons-up-down" class="w-3 h-3 text-slate-400 group-hover:text-teal-500 transition-colors sort-icon" id="sortIcon_code"></i>
                            </div>
                        </th>
                        
                        <!-- Họ và Tên Học Sinh (Sortable: Name A-Z) -->
                        <th onclick="toggleSortHeader('name')" class="px-4 py-3.5 min-w-[170px] cursor-pointer hover:bg-slate-200/60 dark:hover:bg-slate-700/60 transition-colors group" title="Bấm để sắp xếp theo Tên học sinh (A-Z / Z-A)">
                            <div class="flex items-center gap-1">
                                <span>Họ và Tên Học Sinh</span>
                                <i data-lucide="chevrons-up-down" class="w-3 h-3 text-slate-400 group-hover:text-teal-500 transition-colors sort-icon" id="sortIcon_name"></i>
                            </div>
                        </th>
                        
                        <!-- Giới Tính -->
                        <th class="px-3 py-3.5 text-center min-w-[70px] whitespace-nowrap">Giới<br>Tính</th>

                        <!-- Ngày Sinh -->
                        <th class="px-3 py-3.5 text-center min-w-[95px]">Ngày Sinh</th>

                        <!-- Cột Các Môn Học (Mỗi môn một điểm thi - Sortable theo điểm môn) -->
                        <?php foreach ($subjects as $sIdx => $sub): ?>
                        <th onclick="toggleSortHeader('sub_<?= $sub['id'] ?>')" class="px-3 py-3.5 text-center min-w-[110px] cursor-pointer hover:bg-slate-200/60 dark:hover:bg-slate-700/60 transition-colors group" title="Bấm để xếp hạng điểm môn <?= htmlspecialchars($sub['name']) ?>">
                            <div class="flex items-center justify-center gap-1">
                                <div class="font-extrabold text-slate-800 dark:text-slate-100"><?= htmlspecialchars($sub['name']) ?></div>
                                <i data-lucide="chevrons-up-down" class="w-2.5 h-2.5 text-slate-400 group-hover:text-teal-500 transition-colors sort-icon" id="sortIcon_sub_<?= $sub['id'] ?>"></i>
                            </div>
                            
                        </th>
                        <?php endforeach; ?>

                        <!-- Tổng Điểm (Sortable: Điểm Tổng) -->
                        <th onclick="toggleSortHeader('total')" class="px-4 py-3.5 text-center min-w-[115px] bg-teal-50/90 dark:bg-teal-950/60 text-teal-900 dark:text-teal-200 font-black border-l border-teal-200/60 dark:border-teal-800/60 cursor-pointer hover:bg-teal-100/90 dark:hover:bg-teal-900/60 transition-colors group" title="Bấm để xếp hạng Tổng điểm">
                            <div class="flex items-center justify-center gap-1">
                                <span>Tổng Điểm</span>
                                <i data-lucide="chevrons-up-down" class="w-3.5 h-3.5 text-teal-700 dark:text-teal-300 sort-icon" id="sortIcon_total"></i>
                            </div>
                        </th>

                        <!-- Trạng Thái (Screen only) -->
                        <th class="px-3.5 py-3.5 text-center w-28 screen-only">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="gradeTableBody">
                    <?php if (!empty($studentsList)): ?>
                        <?php foreach ($studentsList as $idx => $st): 
                            $tot = $st['total_score'];
                        ?>
                        <tr class="hover:bg-slate-50/90 dark:hover:bg-slate-800/40 transition-colors grade-row" 
                            data-original-idx="<?= $idx ?>"
                            data-row-idx="<?= $idx ?>" 
                            data-student-id="<?= $st['student_id'] ?>"
                            data-student-code="<?= htmlspecialchars($st['student_code']) ?>"
                            data-student-name="<?= htmlspecialchars($st['full_name']) ?>"
                            data-gender="<?= $st['gender_vn'] ?>"
                            data-dob="<?= $st['dob_formatted'] ?>"
                            data-total="<?= $tot !== null ? $tot : -1 ?>">
                            
                            <!-- STT -->
                            <td class="px-3 py-3 text-center text-slate-400 font-mono text-xs row-stt-cell">
                                <?= $idx + 1 ?>
                            </td>

                            <!-- Mã Học Sinh -->
                            <td class="px-3.5 py-3 font-mono font-bold text-teal-600 dark:text-teal-400 text-xs">
                                <?= htmlspecialchars($st['student_code']) ?>
                            </td>

                            <!-- Họ và Tên Học Sinh -->
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                <a href="<?= BASE_URL ?>/students/<?= $st['student_id'] ?>" target="_blank" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors flex items-center gap-1.5 student-name-link" title="Xem hồ sơ học sinh">
                                    <span class="student-name-text"><?= htmlspecialchars($st['full_name']) ?></span>
                                    <i data-lucide="external-link" class="w-3 h-3 text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity screen-only"></i>
                                </a>
                            </td>

                            <!-- Giới Tính -->
                            <td class="px-3 py-3 text-center text-slate-600 dark:text-slate-300 text-xs">
                                <?= $st['gender_vn'] ?>
                            </td>

                            <!-- Ngày Sinh -->
                            <td class="px-3 py-3 text-center font-mono text-slate-600 dark:text-slate-300 text-xs">
                                <?= $st['dob_formatted'] ?>
                            </td>

                            <!-- Cột Điểm của từng môn học (Mỗi môn một điểm thi) -->
                            <?php foreach ($subjects as $sIdx => $sub): 
                                $subId = (int)$sub['id'];
                                $scoreVal = isset($st['scores'][$subId]) && $st['scores'][$subId] !== null ? $st['scores'][$subId] : '';
                                $isScoreLocked = $isAllLocked || $st['is_locked'];

                                $scoreColorClass = 'text-slate-800 dark:text-slate-100 border-slate-200 dark:border-slate-700';
                                if ($scoreVal !== '') {
                                    $numVal = (float)$scoreVal;
                                    if ($numVal < 5.0) {
                                        $scoreColorClass = 'text-rose-600 dark:text-rose-400 border-rose-300 dark:border-rose-700 bg-rose-50/50 dark:bg-rose-950/20 font-bold';
                                    } elseif ($numVal >= 8.0) {
                                        $scoreColorClass = 'text-teal-600 dark:text-teal-400 border-teal-300 dark:border-teal-700 bg-teal-50/50 dark:bg-teal-950/20 font-black';
                                    }
                                }
                            ?>
                            <td class="px-2 py-2 text-center" data-score-sub="<?= $subId ?>">
                                <input type="number" step="0.1" min="0" max="10"
                                       class="score-input w-16 text-center font-mono text-xs py-1.5 border rounded-xl transition-all <?= $scoreColorClass ?> 
                                              <?= $isScoreLocked ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-teal-500 focus:outline-none' ?>"
                                       data-row-idx="<?= $idx ?>"
                                       data-col-idx="<?= $sIdx ?>"
                                       data-student-id="<?= $st['student_id'] ?>"
                                       data-subject-id="<?= $subId ?>"
                                       value="<?= $scoreVal !== '' ? $scoreVal : '' ?>"
                                       <?= $isScoreLocked ? 'readonly tabindex="-1"' : '' ?>
                                       oninput="handleScoreChange(this)"
                                       onkeydown="handleKeyNav(event, this)">
                                <!-- Bản hiển thị text chỉ khi in -->
                                <span class="print-score-text hidden"><?= $scoreVal !== '' ? number_format((float)$scoreVal, 1) : '—' ?></span>
                            </td>
                            <?php endforeach; ?>

                            <!-- Tổng Điểm (Tự động cộng từ các môn) -->
                            <td class="px-4 py-3 text-center font-mono font-black text-sm bg-teal-50/50 dark:bg-teal-950/30 row-total-cell text-teal-700 dark:text-teal-300 border-l border-teal-100 dark:border-teal-900">
                                <?= $tot !== null ? number_format($tot, 1) : '—' ?>
                            </td>

                            <!-- Trạng Thái (Screen only) -->
                            <td class="px-3.5 py-3 text-center row-status-cell screen-only">
                                <?php if ($st['is_locked']): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        <i data-lucide="lock" class="w-3 h-3"></i> Đã khóa
                                    </span>
                                <?php elseif ($st['is_draft']): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200">
                                        Bản nháp
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300">
                                        Đã lưu
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= count($subjects) + 7 ?>" class="py-16 text-center text-slate-400">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mb-3">
                                    <i data-lucide="users" class="w-7 h-7 stroke-[1.5]"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Không có dữ liệu học sinh trong lớp học này</p>
                                <p class="text-xs text-slate-400 mt-1">Vui lòng chọn lớp học khác hoặc thêm học sinh vào lớp trước.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= KHỐI CHỮ KÝ CHUẨN KHI IN (PRINT ONLY) ================= -->
<div class="print-only" style="display: none;">
    <div class="print-date-row">
        <span>......, ngày <?= $todayDay ?> tháng <?= $todayMonth ?> năm <?= $todayYear ?></span>
    </div>
    <div class="print-signatures-grid" style="display: flex; justify-content: flex-end;">
        <div class="print-sig-col" style="width: 320px; text-align: center;">
            <div class="print-sig-title">BAN GIÁM HIỆU / HIỆU TRƯỞNG</div>
            <div class="print-sig-sub">(Ký tên và đóng dấu)</div>
            <div class="print-sig-space"></div>
        </div>
    </div>
</div>

<!-- ================= MODAL ĐIỀN NHANH / DÁN EXCEL ================= -->
<div id="quickFillModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 hidden screen-only">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clipboard-paste" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Điền Nhanh / Dán Từ Excel</h3>
                    <p class="text-xs text-slate-500">Dán cột điểm copy từ file Excel hoặc điền đồng loạt</p>
                </div>
            </div>
            <button onclick="closeQuickFillModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Chọn Môn Học Cần Điền *</label>
                <select id="quickFillSubSelect" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    <?php foreach ($subjects as $sIdx => $sub): ?>
                    <option value="<?= $sIdx ?>"><?= htmlspecialchars($sub['name']) ?> (<?= htmlspecialchars($sub['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Dán Danh Sách Điểm (Mỗi dòng một điểm, tương ứng thứ tự học sinh)
                </label>
                <textarea id="quickFillText" rows="6" placeholder="10&#10;9.5&#10;8.0&#10;10&#10;..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-teal-500 focus:outline-none"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Mẹo: Chọn 1 cột điểm trong Excel, bấm Ctrl+C rồi bấm Ctrl+V vào ô trên.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeQuickFillModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Hủy Bỏ
                </button>
                <button type="button" onclick="applyQuickFill()" class="px-5 py-2.5 rounded-xl text-xs font-black text-white bg-amber-500 hover:bg-amber-600 shadow-md shadow-amber-500/20 transition-all">
                    Áp Dụng Vào Bảng Điểm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL KHÓA ĐIỂM (RED WARNING MODAL) ================= -->
<div id="lockConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 hidden screen-only">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative text-center">
        <div class="w-16 h-16 rounded-3xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-600 mx-auto flex items-center justify-center mb-4">
            <i data-lucide="shield-alert" class="w-8 h-8 stroke-[1.75]"></i>
        </div>

        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">
            Xác Nhận Chốt & Khóa Bảng Điểm Lớp?
        </h3>

        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
            Khi khóa sổ điểm lớp <strong class="text-slate-800 dark:text-slate-200"><?= $className ?></strong>, giáo viên sẽ <span class="text-rose-600 font-bold">không thể chỉnh sửa</span> bất kỳ điểm môn nào cho đến khi được mở khóa bởi Ban Giám Hiệu.
        </p>

        <div class="flex items-center justify-center gap-3">
            <button type="button" onclick="closeLockModal()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                Hủy Bỏ
            </button>
            <button type="button" onclick="confirmLockGrades()" class="px-6 py-2.5 rounded-xl text-xs font-black text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/25 transition-all">
                Chốt & Khóa Ngay
            </button>
        </div>
    </div>
</div>

<!-- ================= MODAL MỞ KHÓA ĐIỂM ================= -->
<div id="unlockConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 hidden screen-only">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative text-center">
        <div class="w-16 h-16 rounded-3xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-600 mx-auto flex items-center justify-center mb-4">
            <i data-lucide="lock-open" class="w-8 h-8 stroke-[1.75]"></i>
        </div>

        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">
            Xác Nhận Mở Khóa Bảng Điểm?
        </h3>

        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
            Mở khóa sẽ cho phép giáo viên tiếp tục chỉnh sửa và cập nhật lại điểm số của lớp <strong class="text-slate-800 dark:text-slate-200"><?= $className ?></strong>.
        </p>

        <div class="flex items-center justify-center gap-3">
            <button type="button" onclick="closeUnlockModal()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                Hủy Bỏ
            </button>
            <button type="button" onclick="confirmUnlockGrades()" class="px-6 py-2.5 rounded-xl text-xs font-black text-white bg-amber-500 hover:bg-amber-600 shadow-md shadow-amber-500/25 transition-all">
                Mở Khóa Ngay
            </button>
        </div>
    </div>
</div>

<script>
// Global config from PHP
const SUBJECTS = <?= json_encode($subjects, JSON_UNESCAPED_UNICODE) ?>;
const SELECTED_CLASS = <?= (int)$selectedClass ?>;
const SELECTED_ACADEMIC_YEAR = <?= (int)$selectedAcademicYear ?>;
const CLASS_NAME = <?= json_encode($className, JSON_UNESCAPED_UNICODE) ?>;
const YEAR_NAME = <?= json_encode($selectedYearObj['name'] ?? '', JSON_UNESCAPED_UNICODE) ?>;

let hasUnsavedChanges = false;
let currentSortKey = 'default';
let currentSortOrder = 'asc'; // 'asc' or 'desc'

// ================= 1. HỆ THỐNG SẮP XẾP (SORTING ENGINE) =================
function toggleSortMenu() {
    const menu = document.getElementById('sortMenuDropdown');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Đóng dropdown khi click ra ngoài
document.addEventListener('click', function(e) {
    const container = document.getElementById('sortDropdownContainer');
    const menu = document.getElementById('sortMenuDropdown');
    if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
    }
});

// Hàm trích xuất Tên (từ cuối cùng) và Họ đệm để so sánh tiếng Việt chuẩn
function parseVietnameseName(fullName) {
    const trimmed = (fullName || '').trim();
    if (!trimmed) return { firstName: '', lastName: '' };
    const parts = trimmed.split(/\s+/);
    const firstName = parts.pop() || '';
    const lastName = parts.join(' ') || '';
    return { firstName, lastName };
}

// Xử lý khi click vào Header cột
function toggleSortHeader(colKey) {
    if (colKey === 'stt') {
        applySort('default');
        return;
    }
    
    let newOrder = 'asc';
    if (currentSortKey === colKey) {
        newOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
    } else {
        // Mặc định tổng điểm hoặc điểm môn nên ưu tiên giảm dần (từ điểm cao xuống)
        if (colKey === 'total' || colKey.startsWith('sub_')) {
            newOrder = 'desc';
        } else {
            newOrder = 'asc';
        }
    }

    currentSortKey = colKey;
    currentSortOrder = newOrder;

    executeSorting(colKey, newOrder);
}

// Xử lý khi chọn từ dropdown
function applySort(type) {
    const menu = document.getElementById('sortMenuDropdown');
    if (menu) menu.classList.add('hidden');

    if (type === 'default') {
        currentSortKey = 'stt';
        currentSortOrder = 'asc';
        executeSorting('stt', 'asc');
    } else if (type === 'total_desc') {
        currentSortKey = 'total';
        currentSortOrder = 'desc';
        executeSorting('total', 'desc');
    } else if (type === 'total_asc') {
        currentSortKey = 'total';
        currentSortOrder = 'asc';
        executeSorting('total', 'asc');
    } else if (type === 'name_asc') {
        currentSortKey = 'name';
        currentSortOrder = 'asc';
        executeSorting('name', 'asc');
    } else if (type === 'name_desc') {
        currentSortKey = 'name';
        currentSortOrder = 'desc';
        executeSorting('name', 'desc');
    } else if (type === 'code_asc') {
        currentSortKey = 'code';
        currentSortOrder = 'asc';
        executeSorting('code', 'asc');
    }
}

function executeSorting(colKey, order) {
    const tbody = document.getElementById('gradeTableBody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('.grade-row'));
    if (rows.length === 0) return;

    // Reset all header sort icons
    document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.setAttribute('data-lucide', 'chevrons-up-down');
        icon.className = 'w-3 h-3 text-slate-400 sort-icon';
    });

    // Update active icon
    const activeIcon = document.getElementById(`sortIcon_${colKey}`);
    if (activeIcon) {
        activeIcon.setAttribute('data-lucide', order === 'asc' ? 'chevron-up' : 'chevron-down');
        activeIcon.className = 'w-3.5 h-3.5 text-teal-600 dark:text-teal-400 sort-icon font-bold';
    }

    // Sort rows array
    rows.sort((a, b) => {
        if (colKey === 'stt') {
            const origA = parseInt(a.dataset.originalIdx || '0', 10);
            const origB = parseInt(b.dataset.originalIdx || '0', 10);
            return origA - origB;
        }

        if (colKey === 'code') {
            const codeA = (a.dataset.studentCode || '').trim();
            const codeB = (b.dataset.studentCode || '').trim();
            return order === 'asc' ? codeA.localeCompare(codeB) : codeB.localeCompare(codeA);
        }

        if (colKey === 'name') {
            const nameA = parseVietnameseName(a.dataset.studentName || '');
            const nameB = parseVietnameseName(b.dataset.studentName || '');
            
            const cmpFirst = nameA.firstName.localeCompare(nameB.firstName, 'vi', { sensitivity: 'base' });
            if (cmpFirst !== 0) {
                return order === 'asc' ? cmpFirst : -cmpFirst;
            }
            const cmpLast = nameA.lastName.localeCompare(nameB.lastName, 'vi', { sensitivity: 'base' });
            return order === 'asc' ? cmpLast : -cmpLast;
        }

        if (colKey === 'total') {
            const valA = parseFloat(a.dataset.total ?? -1);
            const valB = parseFloat(b.dataset.total ?? -1);
            const numA = isNaN(valA) ? -1 : valA;
            const numB = isNaN(valB) ? -1 : valB;
            return order === 'asc' ? numA - numB : numB - numA;
        }

        if (colKey.startsWith('sub_')) {
            const subId = colKey.replace('sub_', '');
            const inpA = a.querySelector(`.score-input[data-subject-id="${subId}"]`);
            const inpB = b.querySelector(`.score-input[data-subject-id="${subId}"]`);
            const scoreA = inpA && inpA.value !== '' ? parseFloat(inpA.value) : -1;
            const scoreB = inpB && inpB.value !== '' ? parseFloat(inpB.value) : -1;
            return order === 'asc' ? scoreA - scoreB : scoreB - scoreA;
        }

        return 0;
    });

    // Re-attach sorted rows to DOM & update STT, row-idx
    rows.forEach((row, newIdx) => {
        row.dataset.rowIdx = newIdx;
        const sttCell = row.querySelector('.row-stt-cell');
        if (sttCell) {
            sttCell.textContent = newIdx + 1;
        }
        row.querySelectorAll('.score-input').forEach(inp => {
            inp.dataset.rowIdx = newIdx;
        });
        tbody.appendChild(row);
    });

    // Update Label and Badges
    const badge = document.getElementById('sortBadgeIndicator');
    const currentSortLabel = document.getElementById('currentSortLabel');

    let labelText = 'Sắp Xếp';
    if (colKey === 'stt') {
        labelText = 'STT Mặc định';
        if (badge) badge.classList.add('hidden');
    } else if (colKey === 'total') {
        labelText = `Tổng điểm (${order === 'desc' ? 'Cao ➔ Thấp' : 'Thấp ➔ Cao'})`;
        if (badge) {
            badge.textContent = `Sắp xếp: ${labelText}`;
            badge.classList.remove('hidden');
        }
    } else if (colKey === 'name') {
        labelText = `Tên (${order === 'asc' ? 'A ➔ Z' : 'Z ➔ A'})`;
        if (badge) {
            badge.textContent = `Sắp xếp: Tên (${order === 'asc' ? 'A-Z' : 'Z-A'})`;
            badge.classList.remove('hidden');
        }
    } else if (colKey === 'code') {
        labelText = `Mã HS (${order === 'asc' ? 'Tăng dần' : 'Giảm dần'})`;
        if (badge) {
            badge.textContent = `Sắp xếp: Mã HS`;
            badge.classList.remove('hidden');
        }
    } else if (colKey.startsWith('sub_')) {
        labelText = `Điểm môn (${order === 'desc' ? 'Cao ➔ Thấp' : 'Thấp ➔ Cao'})`;
        if (badge) {
            badge.textContent = `Sắp xếp: Điểm môn`;
            badge.classList.remove('hidden');
        }
    }

    if (currentSortLabel) {
        currentSortLabel.textContent = labelText;
    }

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// ================= 2. CHỨC NĂNG IN ẤN CHUYÊN NGHIỆP (PRINT HANDLER) =================
function printGradeSheet() {
    const url = '<?= BASE_URL ?>/grades/print?class_id=' + encodeURIComponent(SELECTED_CLASS) + '&academic_year_id=' + encodeURIComponent(SELECTED_ACADEMIC_YEAR) + '&sort=' + encodeURIComponent(currentSortKey);
    window.open(url, '_blank');
}

function syncPrintScoreLabels() {
    const rows = document.querySelectorAll('#gradeTableBody .grade-row');
    rows.forEach(row => {
        row.querySelectorAll('td[data-score-sub]').forEach(td => {
            const input = td.querySelector('.score-input');
            const printSpan = td.querySelector('.print-score-text');
            if (input && printSpan) {
                const val = input.value.trim();
                printSpan.textContent = val !== '' ? parseFloat(val).toFixed(1) : '—';
            }
        });
    });
}

// ================= 3. LỌC TÌM KIẾM HỌC SINH TỨC THÌ =================
function filterStudentRows() {
    const query = (document.getElementById('quickStudentSearch').value || '').trim().toLowerCase();
    const rows = document.querySelectorAll('#gradeTableBody .grade-row');
    rows.forEach(row => {
        const code = (row.dataset.studentCode || '').toLowerCase();
        const name = (row.dataset.studentName || '').toLowerCase();
        if (!query || code.includes(query) || name.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ================= 4. TÍNH ĐIỂM DYNAMIC THEO THỜI GIAN THỰC =================
function handleScoreChange(inputElem) {
    hasUnsavedChanges = true;
    document.getElementById('unsavedIndicator')?.classList.remove('hidden');
    document.getElementById('unsavedIndicator')?.classList.add('inline-flex');

    let val = parseFloat(inputElem.value);
    
    // Bounds guard (0 - 10)
    if (isNaN(val) || inputElem.value === '') {
        inputElem.className = 'score-input w-16 text-center font-mono text-xs py-1.5 border rounded-xl transition-all text-slate-800 dark:text-slate-100 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-teal-500 focus:outline-none';
    } else {
        if (val < 0) { val = 0; inputElem.value = '0'; }
        if (val > 10) { val = 10; inputElem.value = '10'; }

        // Dynamic visual coloring
        if (val < 5.0) {
            inputElem.className = 'score-input w-16 text-center font-mono text-xs py-1.5 border rounded-xl transition-all text-rose-600 dark:text-rose-400 border-rose-300 dark:border-rose-700 bg-rose-50/50 dark:bg-rose-950/20 font-bold focus:ring-2 focus:ring-rose-500 focus:outline-none';
        } else if (val >= 8.0) {
            inputElem.className = 'score-input w-16 text-center font-mono text-xs py-1.5 border rounded-xl transition-all text-teal-600 dark:text-teal-400 border-teal-300 dark:border-teal-700 bg-teal-50/50 dark:bg-teal-950/20 font-black focus:ring-2 focus:ring-teal-500 focus:outline-none';
        } else {
            inputElem.className = 'score-input w-16 text-center font-mono text-xs py-1.5 border rounded-xl transition-all text-slate-800 dark:text-slate-100 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-teal-500 focus:outline-none';
        }
    }

    // Recompute row total
    const row = inputElem.closest('tr');
    recomputeRowTotal(row);

    // Recompute overall KPI metrics
    recomputeAllKPIs();
}

function recomputeRowTotal(row) {
    const inputs = row.querySelectorAll('.score-input');
    let totalScore = 0;
    let hasScore = false;

    inputs.forEach(inp => {
        const score = parseFloat(inp.value);
        if (!isNaN(score) && score >= 0 && score <= 10) {
            totalScore += score;
            hasScore = true;
        }
    });

    const totalCell = row.querySelector('.row-total-cell');
    if (hasScore) {
        totalCell.textContent = totalScore.toFixed(1);
        row.dataset.total = totalScore;
    } else {
        totalCell.textContent = '—';
        row.dataset.total = -1;
    }
}

function recomputeAllKPIs() {
    const rows = document.querySelectorAll('#gradeTableBody .grade-row');
    let highestTotal = 0;
    let filledCells = 0;
    const totalCells = rows.length * (SUBJECTS.length || 1);

    rows.forEach(row => {
        const totText = row.querySelector('.row-total-cell')?.textContent?.trim();
        if (totText && totText !== '—') {
            const num = parseFloat(totText);
            if (!isNaN(num) && num > highestTotal) {
                highestTotal = num;
            }
        }
        row.querySelectorAll('.score-input').forEach(inp => {
            if (inp.value !== '') filledCells++;
        });
    });

    const compRate = totalCells > 0 ? Math.round((filledCells / totalCells) * 100 * 10) / 10 : 0;

    const kpiHigh = document.getElementById('kpiHighestTotal');
    if (kpiHigh) kpiHigh.textContent = highestTotal.toFixed(1);
    
    const kpiComp = document.getElementById('kpiCompletionRate');
    if (kpiComp) kpiComp.textContent = `${compRate}%`;
    
    const pBar = document.getElementById('kpiProgressBar');
    if (pBar) pBar.style.width = `${Math.min(100, compRate)}%`;
}

// ================= 5. ĐIỀU HƯỚNG BÀN PHÍM THÔNG MINH =================
function handleKeyNav(event, currentInput) {
    const rowIdx = parseInt(currentInput.dataset.rowIdx, 10);
    const colIdx = parseInt(currentInput.dataset.colIdx, 10);

    let targetInput = null;

    if (event.key === 'ArrowDown' || event.key === 'Enter') {
        event.preventDefault();
        targetInput = document.querySelector(`.score-input[data-row-idx="${rowIdx + 1}"][data-col-idx="${colIdx}"]`);
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        targetInput = document.querySelector(`.score-input[data-row-idx="${rowIdx - 1}"][data-col-idx="${colIdx}"]`);
    } else if (event.key === 'ArrowRight' && currentInput.selectionStart === currentInput.value.length) {
        targetInput = document.querySelector(`.score-input[data-row-idx="${rowIdx}"][data-col-idx="${colIdx + 1}"]`);
    } else if (event.key === 'ArrowLeft' && currentInput.selectionStart === 0) {
        targetInput = document.querySelector(`.score-input[data-row-idx="${rowIdx}"][data-col-idx="${colIdx - 1}"]`);
    }

    if (targetInput && !targetInput.hasAttribute('readonly')) {
        targetInput.focus();
        targetInput.select();
    }
}

// ================= 6. LƯU BẢNG ĐIỂM (NHÁP / CHÍNH THỨC) =================
async function saveGrades(isDraft = false) {
    const btn = isDraft ? document.getElementById('btnSaveDraft') : document.getElementById('btnSaveOfficial');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block animate-spin mr-1">⏳</span> Đang lưu...`;
    }

    const inputs = document.querySelectorAll('.score-input');
    const entries = [];
    inputs.forEach(input => {
        entries.push({
            student_id: input.dataset.studentId,
            subject_id: input.dataset.subjectId,
            score: input.value !== '' ? input.value : null
        });
    });

    try {
        const res = await apiPost('<?= BASE_URL ?>/grades/save', {
            class_id: SELECTED_CLASS,
            academic_year_id: SELECTED_ACADEMIC_YEAR,
            is_draft: isDraft,
            entries: entries
        });

        if (res && res.success !== false) {
            hasUnsavedChanges = false;
            document.getElementById('unsavedIndicator')?.classList.add('hidden');
            setTimeout(() => location.reload(), 600);
        } else {
            alert(res?.message || 'Có lỗi xảy ra khi lưu bảng điểm.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    } catch (err) {
        alert('Lỗi mạng hoặc kết nối máy chủ: ' + err.message);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
}

// ================= 7. MODAL ĐIỀN NHANH =================
function openQuickFillModal() {
    document.getElementById('quickFillModal')?.classList.remove('hidden');
    document.getElementById('quickFillText')?.focus();
}

function closeQuickFillModal() {
    document.getElementById('quickFillModal')?.classList.add('hidden');
}

function applyQuickFill() {
    const colIdx = parseInt(document.getElementById('quickFillSubSelect').value, 10);
    const text = (document.getElementById('quickFillText').value || '').trim();
    if (!text) {
        alert('Vui lòng dán danh sách điểm.');
        return;
    }

    const lines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);
    const rows = document.querySelectorAll('#gradeTableBody .grade-row');

    lines.forEach((scoreStr, idx) => {
        if (idx < rows.length) {
            const input = rows[idx].querySelector(`.score-input[data-col-idx="${colIdx}"]`);
            if (input && !input.hasAttribute('readonly')) {
                const parsed = parseFloat(scoreStr.replace(',', '.'));
                if (!isNaN(parsed) && parsed >= 0 && parsed <= 10) {
                    input.value = parsed;
                    handleScoreChange(input);
                }
            }
        }
    });

    closeQuickFillModal();
    alert(`Đã áp dụng thành công ${Math.min(lines.length, rows.length)} điểm vào môn học!`);
}

// ================= 8. MODAL KHÓA / MỞ KHÓA SỔ ĐIỂM =================
function openLockModal() {
    document.getElementById('lockConfirmModal')?.classList.remove('hidden');
}

function closeLockModal() {
    document.getElementById('lockConfirmModal')?.classList.add('hidden');
}

async function confirmLockGrades() {
    closeLockModal();
    const res = await apiPost('<?= BASE_URL ?>/grades/lock', {
        class_id: SELECTED_CLASS,
        academic_year_id: SELECTED_ACADEMIC_YEAR
    });
    if (res && res.success !== false) {
        setTimeout(() => location.reload(), 600);
    } else {
        alert(res?.message || 'Không thể khóa bảng điểm.');
    }
}

function openUnlockModal() {
    document.getElementById('unlockConfirmModal')?.classList.remove('hidden');
}

function closeUnlockModal() {
    document.getElementById('unlockConfirmModal')?.classList.add('hidden');
}

async function confirmUnlockGrades() {
    closeUnlockModal();
    const res = await apiPost('<?= BASE_URL ?>/grades/unlock', {
        class_id: SELECTED_CLASS,
        academic_year_id: SELECTED_ACADEMIC_YEAR
    });
    if (res && res.success !== false) {
        setTimeout(() => location.reload(), 600);
    } else {
        alert(res?.message || 'Không thể mở khóa bảng điểm.');
    }
}

// ================= 9. XUẤT EXCEL CHUẨN =================
function exportGradesToExcel() {
    const rows = document.querySelectorAll('#gradeTableBody .grade-row');
    const data = [];

    // Header Info
    data.push(['TRƯỜNG TIỂU HỌC / THCS / THPT EDUMANAGE']);
    data.push(['BẢNG TỔNG HỢP ĐIỂM THI VÀ ĐÁNH GIÁ HỌC SINH']);
    data.push([`Lớp: ${CLASS_NAME}`, `Năm học: ${YEAR_NAME}`]);
    data.push([]); // Empty row

    // Table Headers
    const headers = ['STT', 'Mã Học Sinh', 'Họ và Tên Học Sinh', 'Giới Tính', 'Ngày Sinh'];
    SUBJECTS.forEach(sub => {
        headers.push(`${sub.name} (${sub.code})`);
    });
    headers.push('Tổng Điểm');
    headers.push('Trạng Thái');
    data.push(headers);

    // Data Rows
    rows.forEach((row, idx) => {
        const studentCode = row.dataset.studentCode || '';
        const studentName = row.dataset.studentName || '';
        const gender = row.dataset.gender || '';
        const dob = row.dataset.dob || '';
        
        const rowData = [idx + 1, studentCode, studentName, gender, dob];

        // Scores for each subject
        row.querySelectorAll('.score-input').forEach(inp => {
            const val = inp.value !== '' ? parseFloat(inp.value) : '';
            rowData.push(val);
        });

        const total = row.querySelector('.row-total-cell')?.textContent?.trim() || '';
        const status = row.querySelector('.row-status-cell')?.textContent?.trim() || '';

        rowData.push(total !== '—' ? parseFloat(total) : '');
        rowData.push(status);

        data.push(rowData);
    });

    // Summary line
    data.push([]);
    data.push(['', '', '', '', 'Tổng Điểm Cao Nhất Lớp:', document.getElementById('kpiHighestTotal')?.textContent?.trim() || '']);
    data.push(['', '', '', '', 'Tiến Độ Vào Điểm:', document.getElementById('kpiCompletionRate')?.textContent?.trim() || '']);

    // Build worksheet & workbook
    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Bang_Diem");

    const fileName = `Bang_Diem_${CLASS_NAME.replace(/\s+/g, '_')}_${YEAR_NAME.replace(/\s+/g, '_')}.xlsx`;
    XLSX.writeFile(wb, fileName);
}

// Cảnh báo khi có thay đổi chưa lưu
window.addEventListener('beforeunload', (e) => {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Render Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Drag-to-scroll container handler
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.getElementById('gradeScrollContainer');
    if (scrollContainer) {
        let isDown = false;
        let startX;
        let scrollLeftStart;

        scrollContainer.addEventListener('mousedown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;
            isDown = true;
            scrollContainer.style.cursor = 'grabbing';
            scrollContainer.style.userSelect = 'none';
            startX = e.pageX - scrollContainer.offsetLeft;
            scrollLeftStart = scrollContainer.scrollLeft;
            e.preventDefault();
        });

        scrollContainer.addEventListener('mouseleave', () => {
            if (isDown) {
                isDown = false;
                scrollContainer.style.cursor = 'grab';
                scrollContainer.style.userSelect = '';
            }
        });

        scrollContainer.addEventListener('mouseup', () => {
            isDown = false;
            scrollContainer.style.cursor = 'grab';
            scrollContainer.style.userSelect = '';
        });

        scrollContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 1.5;
            scrollContainer.scrollLeft = scrollLeftStart - walk;
        });

        let touchStartX;
        let touchScrollLeft;

        scrollContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].pageX;
            touchScrollLeft = scrollContainer.scrollLeft;
        }, { passive: true });

        scrollContainer.addEventListener('touchmove', (e) => {
            if (touchStartX === undefined) return;
            const x = e.touches[0].pageX;
            const walk = (touchStartX - x) * 1.2;
            scrollContainer.scrollLeft = touchScrollLeft + walk;
        }, { passive: true });

        scrollContainer.addEventListener('touchend', () => {
            touchStartX = undefined;
        }, { passive: true });
    }
});
</script>

<style>
/* ================= THIẾT KẾ BIỂU MẪU IN ẤN CHUẨN A4 KHỔ NGANG (@MEDIA PRINT) ================= */
@media print {
    /* 1. Ẩn toàn bộ giao diện điều khiển hệ thống */
    aside,
    header,
    nav,
    #sidebar,
    #toastContainer,
    body > div.bg-indigo-900,
    .screen-only,
    #gradeFilterForm,
    #unsavedIndicator,
    #quickFillModal,
    #lockConfirmModal,
    #unlockConfirmModal,
    .sort-icon,
    #btnSortDropdown,
    #sortMenuDropdown,
    button,
    a i[data-lucide="external-link"] {
        display: none !important;
    }

    /* 2. Hiển thị các phần biểu mẫu dành riêng cho in */
    .print-only {
        display: block !important;
    }

    /* 3. Thiết lập trang in A4 Landscape */
    @page {
        size: A4 landscape;
        margin: 12mm 10mm 12mm 10mm;
    }

    html, body {
        background: #ffffff !important;
        color: #000000 !important;
        font-family: "Times New Roman", Times, Georgia, serif !important;
        font-size: 10.5pt !important;
        line-height: 1.3 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    main {
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .grade-scroll-container {
        overflow: visible !important;
        width: 100% !important;
        cursor: default !important;
    }

    /* Tiêu đề biểu mẫu in */
    .print-header-grid {
        display: flex !important;
        justify-content: space-between !important;
        align-items: flex-start !important;
        margin-bottom: 12px !important;
    }

    .print-header-left {
        text-align: left !important;
    }

    .print-header-right {
        text-align: center !important;
    }

    .print-dash {
        margin-top: 1px !important;
        letter-spacing: 2px !important;
    }

    .print-title-box {
        text-align: center !important;
        margin-bottom: 15px !important;
    }

    .print-main-title {
        font-size: 16pt !important;
        font-weight: bold !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        margin: 0 0 4px 0 !important;
        color: #000000 !important;
    }

    .print-sub-title {
        font-size: 12pt !important;
        font-weight: bold !important;
        margin-bottom: 4px !important;
        color: #1e293b !important;
    }

    .print-meta-info {
        font-size: 10pt !important;
        font-style: italic !important;
        display: flex !important;
        justify-content: center !important;
        gap: 15px !important;
        color: #334155 !important;
    }

    /* Bảng in */
    #gradesTable {
        width: 100% !important;
        table-layout: auto !important;
        border-collapse: collapse !important;
        margin-top: 5px !important;
        page-break-inside: auto !important;
    }

    #gradesTable tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }

    #gradesTable thead {
        display: table-header-group !important;
    }

    #gradesTable th,
    #gradesTable td {
        border: 0.5px solid #94a3b8 !important;
        padding: 5px 4px !important;
        color: #000000 !important;
        background: transparent !important;
    }

    #gradesTable th {
        background-color: #f1f5f9 !important;
        font-weight: bold !important;
        font-size: 9pt !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    #gradesTable td {
        font-size: 9.5pt !important;
    }

    /* Ẩn input khi in, hiển thị text điểm nét đậm */
    input.score-input {
        display: none !important;
    }

    .print-score-text {
        display: inline-block !important;
        font-family: "Times New Roman", Times, serif !important;
        font-weight: bold !important;
        font-size: 10pt !important;
        color: #000000 !important;
        text-align: center !important;
    }

    .student-name-text {
        color: #000000 !important;
        font-weight: bold !important;
    }

    .student-name-link {
        color: #000000 !important;
        text-decoration: none !important;
        pointer-events: none !important;
    }

    .row-total-cell {
        font-weight: bold !important;
        font-size: 10.5pt !important;
        color: #000000 !important;
        background-color: #f8fafc !important;
    }

    /* Phần chân trang ký tên */
    .print-date-row {
        text-align: right !important;
        font-style: italic !important;
        margin-top: 15px !important;
        margin-bottom: 8px !important;
        padding-right: 30px !important;
        font-size: 10pt !important;
    }

    .print-signatures-grid {
        display: flex !important;
        justify-content: space-between !important;
        text-align: center !important;
        margin-top: 5px !important;
        page-break-inside: avoid !important;
    }

    .print-sig-col {
        width: 32% !important;
    }

    .print-sig-title {
        font-weight: bold !important;
        font-size: 10.5pt !important;
        text-transform: uppercase !important;
    }

    .print-sig-sub {
        font-size: 9pt !important;
        font-style: italic !important;
        color: #475569 !important;
        margin-top: 2px !important;
    }

    .print-sig-space {
        height: 65px !important;
    }
}

/* Giao diện trên màn hình (Screen) */
.grade-scroll-container {
    cursor: grab;
}
.grade-scroll-container:active {
    cursor: grabbing;
}
.grade-scroll-container input,
.grade-scroll-container a,
.grade-scroll-container button,
.grade-scroll-container select {
    cursor: auto;
}

/* Custom Scrollbar */
.grade-scroll-container {
    scrollbar-width: thin;
    scrollbar-color: #94a3b8 #e2e8f0;
    scroll-behavior: smooth;
}
.grade-scroll-container::-webkit-scrollbar {
    height: 10px;
    width: 8px;
}
.grade-scroll-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}
.grade-scroll-container::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, #0d9488, #14b8a6);
    border-radius: 10px;
    border: 2px solid #f1f5f9;
}
.grade-scroll-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(90deg, #0f766e, #0d9488);
}
</style>
