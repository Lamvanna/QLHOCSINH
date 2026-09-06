<?php // views/students/index.php ?>
<?php
$exportList = !empty($allStudentsForExport) ? $allStudentsForExport : (class_exists('Student') ? (Student::getPaginated(1, 100000, $filters ?? [])['data'] ?? ($students ?? [])) : ($students ?? []));

// KPI metrics calculations
$totalStudents = $pagination['total'] ?? count($students ?? []);
$activeStudents = count(array_filter($exportList, fn($s) => ($s['status'] ?? 'studying') === 'studying' || empty($s['status'])));
$maleCount = count(array_filter($exportList, fn($s) => ($s['gender'] ?? '') === 'male'));
$femaleCount = count(array_filter($exportList, fn($s) => ($s['gender'] ?? '') === 'female'));
$classesCount = count($classes ?? []);
?>

<div class="space-y-6">

    <!-- 1. PAGE HEADER & MAIN ACTIONS -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-1">
                <span class="material-symbols-outlined text-[17px]">school</span>
                <span>Quản Lý Học Vụ & Tuyển Sinh</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Danh Sách Học Sinh</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-0.5">
                Tổng quy mô: <strong class="text-emerald-600 dark:text-emerald-400"><?= number_format($totalStudents) ?> học sinh</strong> &nbsp;|&nbsp; 
                Đang học: <strong class="text-slate-800 dark:text-slate-200"><?= number_format($activeStudents) ?> em</strong>
            </p>
        </div>
        
        <!-- Right Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <button onclick="exportCustomExcel()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-bold transition-all shadow-sm group" title="Xuất file Excel chuyên nghiệp">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">table_view</span>
                <span>Xuất Excel</span>
            </button>
            <a href="<?= BASE_URL ?>/students/print<?= !empty($filters['class_id']) ? '?class_id=' . $filters['class_id'] . '&auto=1' : '?auto=1' ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-[#006c4a] hover:bg-[#005137] text-white rounded-xl text-xs font-bold transition-all shadow-sm" title="In danh sách học sinh chuẩn A4">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>In Danh Sách</span>
            </a>
            <button onclick="openModal('importModal')" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold transition-all shadow-sm group" title="Nhập danh sách học sinh từ file Excel">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">upload</span>
                <span>Nhập Excel</span>
            </button>
            <?php if (Permission::can('students.create')): ?>
            <a href="<?= BASE_URL ?>/students/create" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-500/20 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Thêm Học Sinh Mới</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. EXECUTIVE BENTO KPI METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tổng số học sinh -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tổng Học Sinh</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1"><?= number_format($totalStudents) ?></h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> Toàn hệ thống
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">school</span>
            </div>
        </div>

        <!-- Card 2: Đang theo học -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Đang Học Tập</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1"><?= number_format($activeStudents) ?></h3>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Hồ sơ tích cực</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">how_to_reg</span>
            </div>
        </div>

        <!-- Card 3: Cơ cấu Nam / Nữ -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Cơ Cấu Giới Tính</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-base font-extrabold text-blue-600"><?= $maleCount ?> Nam</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-base font-extrabold text-rose-500"><?= $femaleCount ?> Nữ</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-0.5">Tỷ lệ cân đối</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">wc</span>
            </div>
        </div>

        <!-- Card 4: Quy mô phân lớp -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Phân Phối Lớp</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1"><?= $classesCount ?> Lớp</h3>
                
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">meeting_room</span>
            </div>
        </div>
    </div>

    <!-- 3. TOOLBAR: FILTERS, ACTIONS & VIEW SWITCHER -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col xl:flex-row justify-between items-start xl:items-center gap-3">
        <!-- Search & Filter Form -->
        <form method="GET" action="<?= BASE_URL ?>/students" id="filterForm" class="flex flex-wrap items-center gap-2.5 w-full xl:w-auto">
            <!-- Search Input -->
            <div class="relative flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-1.5 focus-within:ring-2 focus-within:ring-emerald-500 w-full sm:w-64 transition-all">
                <span class="material-symbols-outlined text-slate-400 mr-2 text-[18px]">search</span>
                <input name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="bg-transparent border-none outline-none w-full text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:ring-0 p-0" placeholder="Tìm Mã HS, Họ tên, SĐT..." type="text"/>
            </div>

            <!-- Class Filter -->
            <div class="relative">
                <select name="class_id" onchange="document.getElementById('filterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Lớp</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($filters['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select name="status" onchange="document.getElementById('filterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Trạng thái</option>
                    <option value="studying" <?= ($filters['status'] ?? '') === 'studying' ? 'selected' : '' ?>>Đang học</option>
                    <option value="transferred" <?= ($filters['status'] ?? '') === 'transferred' ? 'selected' : '' ?>>Chuyển trường</option>
                    <option value="graduated" <?= ($filters['status'] ?? '') === 'graduated' ? 'selected' : '' ?>>Đã tốt nghiệp</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Gender Filter -->
            <div class="relative">
                <select name="gender" onchange="document.getElementById('filterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Giới tính</option>
                    <option value="male" <?= ($filters['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Nam</option>
                    <option value="female" <?= ($filters['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Nữ</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Reset Filter -->
            <a href="<?= BASE_URL ?>/students" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-rose-500 hover:text-rose-600 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                <span>Xóa lọc</span>
            </a>
        </form>

        <!-- View Mode Switch: Bento Cards vs Data Table -->
        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 self-end xl:self-center">
            <button type="button" id="btnStudentViewTable" onclick="switchStudentView('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">table_rows</span>
                <span class="hidden sm:inline">Dạng Bảng</span>
            </button>
            <button type="button" id="btnStudentViewGrid" onclick="switchStudentView('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white">
                <span class="material-symbols-outlined text-[16px]">grid_view</span>
                <span class="hidden sm:inline">Dạng Thẻ</span>
            </button>
        </div>
    </div>

    <!-- 4. VIEW 1: BENTO CARDS GRID -->
    <div id="studentsGridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $s): ?>
            <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl overflow-hidden hover:shadow-xl hover:-translate-y-1 hover:border-emerald-500/40 transition-all duration-300 group flex flex-col justify-between">
                <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-600"></div>
                <div class="p-5 flex flex-col flex-1 space-y-4">
                    <!-- Top Avatar & Name -->
                    <div class="flex items-start gap-3.5">
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                                <img src="<?= htmlspecialchars($s['avatar'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150') ?>" class="w-full h-full object-cover" alt="Avatar">
                            </div>
                            <?php if (($s['status'] ?? 'studying') === 'studying'): ?>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900" title="Đang học"></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="font-mono text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md inline-block w-fit">
                                <?= htmlspecialchars($s['student_code']) ?>
                            </span>
                            <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="font-extrabold text-slate-900 dark:text-white text-base truncate group-hover:text-emerald-600 transition-colors mt-0.5">
                                <?= htmlspecialchars($s['full_name']) ?>
                            </a>
                            <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-0.5">
                                <span class="font-semibold <?= ($s['gender'] ?? 'male') === 'female' ? 'text-rose-500' : 'text-blue-600' ?>">
                                    <?= ($s['gender'] ?? 'male') === 'female' ? 'Nữ' : 'Nam' ?>
                                </span>
                                <span>•</span>
                                <span><?= !empty($s['dob']) ? date('d/m/Y', strtotime($s['dob'])) : '—' ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Class & Academic Details -->
                    <div class="py-2.5 px-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[11px]">Lớp hiện tại:</span>
                            <span class="font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-2.5 py-0.5 rounded-lg">
                                <?= htmlspecialchars($s['class_name'] ?? 'Chưa phân lớp') ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[11px]">Niên khóa:</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-300">
                                <?= htmlspecialchars($s['academic_year_name'] ?? '2025 - 2026') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">person</span>
                            <span class="truncate">PH: <strong class="text-slate-800 dark:text-slate-200 font-semibold"><?= htmlspecialchars($s['parent_name'] ?? '—') ?></strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">call</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200"><?= htmlspecialchars($s['parent_phone'] ?? '—') ?></span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        <?php if (($s['status'] ?? 'studying') === 'studying'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            ĐANG HỌC
                        </span>
                        <?php elseif (($s['status'] ?? '') === 'transferred'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-[11px] font-bold uppercase tracking-wider border border-amber-200">
                            CHUYỂN TRƯỜNG
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold uppercase tracking-wider border border-blue-200">
                            ĐÃ TỐT NGHIỆP
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 mt-auto">
                        <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-50 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 hover:text-emerald-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[15px]">visibility</span>
                            <span>Xem Hồ Sơ</span>
                        </a>

                        <div class="flex items-center gap-1">
                            <?php if (Permission::can('students.update')): ?>
                            <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>/edit" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-xl transition-colors" title="Chỉnh sửa hồ sơ">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <?php endif; ?>

                            <?php if (Permission::can('students.delete')): ?>
                            <button type="button" 
                                    onclick="openDeleteConfirmModal('<?= $s['id'] ?>', '<?= htmlspecialchars(addslashes($s['full_name'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($s['student_code'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($s['class_name'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($s['avatar'] ?? '')) ?>')" 
                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors" title="Xóa học sinh">
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
                    <span class="material-symbols-outlined text-3xl text-slate-400">person_off</span>
                </div>
                <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy học sinh nào</p>
                <p class="text-xs text-slate-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc bấm Thêm Học Sinh Mới.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 5. VIEW 2: ELEVATED DATA TABLE -->
    <div id="studentsTableView" class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[950px] text-xs" id="studentsTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="p-3.5 pl-5 w-12 text-center whitespace-nowrap">STT</th>
                        <th class="p-3.5 whitespace-nowrap">MÃ HỌC SINH</th>
                        <th class="p-3.5">HỌ VÀ TÊN</th>
                        <th class="p-3.5">GIỚI TÍNH / NGÀY SINH</th>
                        <th class="p-3.5">LỚP HỌC</th>
                        <th class="p-3.5">PHỤ HUYNH / SĐT</th>
                        <th class="p-3.5">TRẠNG THÁI</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-200">
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $idx => $s): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="p-3.5 pl-5 text-center font-normal text-slate-400 whitespace-nowrap">
                                <?= ($pagination['from'] ?? 1) + $idx ?>
                            </td>
                            <td class="p-3.5 font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap font-mono">
                                <?= htmlspecialchars($s['student_code']) ?>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0 shadow-sm">
                                        <img src="<?= htmlspecialchars($s['avatar'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150') ?>" class="w-full h-full object-cover" alt="Avatar">
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="font-bold text-slate-900 dark:text-white text-sm truncate group-hover:text-emerald-600 transition-colors">
                                            <?= htmlspecialchars($s['full_name']) ?>
                                        </a>
                                        <span class="text-[11px] text-slate-400 truncate mt-0.5">Niên khóa: <?= htmlspecialchars($s['academic_year_name'] ?? '2025 - 2026') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-xs <?= ($s['gender'] ?? 'male') === 'female' ? 'text-rose-500' : 'text-blue-600' ?>">
                                        <?= ($s['gender'] ?? 'male') === 'female' ? 'Nữ' : 'Nam' ?>
                                    </span>
                                    <span class="text-xs text-slate-400 mt-0.5"><?= !empty($s['dob']) ? date('d/m/Y', strtotime($s['dob'])) : '—' ?></span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold text-xs border border-blue-200/60 dark:border-blue-900/40">
                                    <span class="material-symbols-outlined text-[14px]">door_front</span>
                                    <?= htmlspecialchars($s['class_name'] ?? 'Chưa xếp lớp') ?>
                                </span>
                            </td>
                            <td class="p-3.5">
                                <div class="flex flex-col text-xs">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($s['parent_name'] ?? '—') ?></span>
                                    <span class="font-mono text-slate-400 mt-0.5"><?= htmlspecialchars($s['parent_phone'] ?? '—') ?></span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <?php if (($s['status'] ?? 'studying') === 'studying'): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Đang học
                                </span>
                                <?php elseif (($s['status'] ?? '') === 'transferred'): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[11px] font-bold border border-amber-200">
                                    Chuyển trường
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold border border-blue-200">
                                    Đã tốt nghiệp
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 rounded-xl transition-colors" title="Xem hồ sơ chi tiết">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <?php if (Permission::can('students.update')): ?>
                                    <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>/edit" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-xl transition-colors" title="Chỉnh sửa">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (Permission::can('students.delete')): ?>
                                    <button type="button" 
                                            onclick="openDeleteConfirmModal('<?= $s['id'] ?>', '<?= htmlspecialchars(addslashes($s['full_name'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($s['student_code'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($s['class_name'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($s['avatar'] ?? '')) ?>')" 
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors" title="Xóa học sinh">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="py-16 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-3xl text-slate-400">person_off</span>
                                    </div>
                                    <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy học sinh nào</p>
                                    <p class="text-xs text-slate-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc thêm học sinh mới.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <?php if (!empty($pagination) && ($pagination['last_page'] ?? 1) > 1): ?>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs bg-white dark:bg-slate-900">
            <span class="text-slate-500 dark:text-slate-400">
                Hiển thị từ <strong class="text-slate-800 dark:text-slate-200"><?= $pagination['from'] ?? 1 ?></strong> đến <strong class="text-slate-800 dark:text-slate-200"><?= $pagination['to'] ?? count($students) ?></strong> trong tổng số <strong class="text-slate-800 dark:text-slate-200"><?= $pagination['total'] ?? count($students) ?></strong> học sinh
            </span>
            <div class="flex items-center gap-1">
                <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                <a href="?page=<?= $i ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['class_id']) ? '&class_id=' . $filters['class_id'] : '' ?><?= !empty($filters['status']) ? '&status=' . $filters['status'] : '' ?><?= !empty($filters['gender']) ? '&gender=' . $filters['gender'] : '' ?>" 
                   class="w-8 h-8 rounded-xl font-bold flex items-center justify-center transition-all <?= ($pagination['current_page'] ?? 1) == $i ? 'bg-emerald-600 text-white shadow-sm' : 'border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: XÁC NHẬN XÓA HỒ SƠ HỌC SINH (CHUẨN MẪU CẢNH BÁO ĐỎ) -->
<!-- ========================================================================= -->
<div id="deleteConfirmModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden text-center transform transition-all scale-100 animate-in fade-in zoom-in-95 duration-150">
        <!-- Header: Nền hồng đỏ nhạt có tam giác cảnh báo ở giữa -->
        <div class="bg-[#feecee] dark:bg-rose-950/40 border-b border-rose-100 dark:border-rose-900/50 pt-7 pb-6 px-6">
            <div class="w-12 h-12 rounded-full bg-rose-200/80 dark:bg-rose-900/70 text-rose-700 dark:text-rose-300 flex items-center justify-center mx-auto mb-3 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-rose-700 dark:text-rose-300">
                Xác nhận xóa hồ sơ học sinh?
            </h3>
        </div>

        <!-- Body: Nền trắng có đoạn văn cảnh báo và thẻ học sinh -->
        <div class="p-6 sm:p-7 bg-white dark:bg-slate-900 space-y-6">
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed text-center">
                Hành động này không thể hoàn tác. Toàn bộ dữ liệu liên quan đến học sinh này sẽ bị loại bỏ khỏi hệ thống.
            </p>

            <!-- Khung thông tin học sinh -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-4 bg-white dark:bg-slate-800/50 flex items-center gap-4 text-left shadow-sm">
                <div class="w-14 h-14 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 shrink-0 shadow-sm flex items-center justify-center">
                    <img id="modalStudentAvatarImg" src="" class="w-full h-full object-cover hidden" alt="Avatar">
                    <span id="modalStudentAvatarText" class="font-bold text-slate-600 dark:text-slate-200 text-sm">HS</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span id="modalStudentName" class="font-bold text-slate-900 dark:text-white text-base truncate">Lê Đình Khoa</span>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            ID: <span id="modalStudentCode">HS01821</span>
                        </span>
                        <span id="modalStudentClass" class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            Lớp 1
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="modalDeleteStudentId" value="">

        <!-- Footer: Nút [Hủy bỏ] và [Xác nhận xóa] -->
        <div class="p-4 sm:p-5 px-7 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('deleteConfirmModal')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition-all shadow-sm">
                Hủy bỏ
            </button>
            <button type="button" id="btnExecuteDelete" onclick="executeDeleteStudent()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#b91c1c] hover:bg-[#991b1b] active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>Xác nhận xóa</span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: NHẬP HỌC SINH TỪ EXCEL -->
<!-- ========================================================================= -->
<div id="importModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 md:p-7 max-w-xl w-full shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Nhập Danh Sách Học Sinh Từ Excel</h3>
                    <p class="text-[11px] text-slate-400">Đồng bộ dữ liệu học sinh từ bảng tính Excel</p>
                </div>
            </div>
            <button onclick="closeModal('importModal')" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50/80 via-emerald-50/40 to-blue-50/80 dark:from-slate-800 dark:to-slate-800 border border-blue-100 dark:border-slate-700/80 space-y-2 text-xs">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-[16px]">info</span>
                    Thông Tin & Quy Định Dữ Liệu
                </span>
                <button type="button" onclick="downloadExcelTemplate()" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-slate-600 hover:bg-blue-50 shadow-sm flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">file_download</span>
                    Tải File Mẫu
                </button>
            </div>
            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                File Excel cần có các cột: Mã HS, Họ và Tên, Giới tính, Ngày sinh, Lớp học, Niên khóa.
            </p>
        </div>

        <div class="space-y-3">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Chọn tệp Excel (.xlsx hoặc .xls)</label>
            <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-6 text-center hover:border-blue-500 transition-colors bg-slate-50/50 dark:bg-slate-800/30 cursor-pointer relative group" onclick="document.getElementById('excelFileInput').click()">
                <input type="file" id="excelFileInput" accept=".xlsx, .xls" class="hidden" onchange="previewExcelFile(this)">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl">upload</span>
                </div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-200" id="uploadFileText">Kéo thả tệp hoặc bấm để chọn từ máy tính</p>
                <p class="text-[10px] text-slate-400 mt-1">Hỗ trợ định dạng .xlsx, .xls tiêu chuẩn</p>
            </div>
        </div>

        <div id="importPreview" class="hidden p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-700 dark:text-slate-200" id="previewFileName">Tệp tin</span>
                <span class="font-bold text-emerald-600" id="previewStudentCount">0 học sinh</span>
            </div>
        </div>

        <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeModal('importModal')" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors">
                Hủy Bỏ
            </button>
            <button type="button" id="btnConfirmImport" onclick="handleImportExcel()" class="inline-flex items-center gap-1.5 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all disabled:opacity-50">
                <span class="material-symbols-outlined text-[18px]">upload</span>
                <span>Xác Nhận Nhập Dữ Liệu</span>
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    const ALL_EXPORT_STUDENTS = <?= json_encode($exportList ?? [], JSON_UNESCAPED_UNICODE) ?>;
    let parsedStudents = [];

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // =========================================================================
    // CHUYỂN ĐỔI CHẾ ĐỘ XEM (VIEW SWITCH: GRID / TABLE)
    // =========================================================================
    function switchStudentView(mode) {
        const gridView = document.getElementById('studentsGridView');
        const tableView = document.getElementById('studentsTableView');
        const btnGrid = document.getElementById('btnStudentViewGrid');
        const btnTable = document.getElementById('btnStudentViewTable');

        if (mode === 'grid') {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');

            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm';
            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('student_view_mode', 'grid');
        } else {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');

            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm';
            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('student_view_mode', 'table');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('student_view_mode');
        if (savedMode === 'grid') {
            switchStudentView('grid');
        } else {
            switchStudentView('table');
        }
    });

    // =========================================================================
    // MODAL XÓA HỒ SƠ HỌC SINH
    // =========================================================================
    function openDeleteConfirmModal(id, name, code, className, avatarUrl) {
        document.getElementById('modalDeleteStudentId').value = id;
        document.getElementById('modalStudentName').textContent = name || '';
        document.getElementById('modalStudentCode').textContent = code || '';
        document.getElementById('modalStudentClass').textContent = className || 'Chưa phân lớp';
        
        const avatarImg = document.getElementById('modalStudentAvatarImg');
        const avatarText = document.getElementById('modalStudentAvatarText');
        if (avatarUrl && avatarUrl.trim() !== '') {
            avatarImg.src = avatarUrl;
            avatarImg.classList.remove('hidden');
            avatarText.classList.add('hidden');
        } else {
            avatarImg.classList.add('hidden');
            avatarText.classList.remove('hidden');
            const nameParts = (name || '').trim().split(' ');
            const initials = nameParts.length > 1 
                ? (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase()
                : (name || 'HS').substring(0, 2).toUpperCase();
            avatarText.textContent = initials;
        }

        openModal('deleteConfirmModal');
    }

    async function executeDeleteStudent() {
        const id = document.getElementById('modalDeleteStudentId').value;
        if (!id) return;

        const btn = document.getElementById('btnExecuteDelete');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang xóa...</span>`;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const res = await fetch(`<?= BASE_URL ?>/students/${id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (data.status === 'success') {
                closeModal('deleteConfirmModal');
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Đã xóa hồ sơ học sinh thành công!', 'success');
                }
                setTimeout(() => window.location.reload(), 600);
            } else {
                alert(data.message || 'Có lỗi xảy ra khi xóa học sinh.');
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            }
        } catch (e) {
            alert('Không thể kết nối đến máy chủ. Vui lòng thử lại!');
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
        }
    }

    // =========================================================================
    // XUẤT EXCEL CHUYÊN NGHIỆP (TOÀN BỘ DANH SÁCH & BỎ CỘT KHMER)
    // =========================================================================
    function exportCustomExcel() {
        const rawData = ALL_EXPORT_STUDENTS;
        if (!rawData || rawData.length === 0) {
            alert('Không có dữ liệu học sinh để xuất.');
            return;
        }

        const dataToExport = rawData.map((s, index) => ({
            "STT": index + 1,
            "Mã Học Sinh": s.student_code || '',
            "Họ và Tên": s.full_name || '',
            "Giới Tính": (s.gender === 'female' || s.gender === 'Nữ') ? 'Nữ' : 'Nam',
            "Ngày Sinh": s.dob ? new Date(s.dob).toLocaleDateString('vi-VN') : '',
            "Lớp": s.class_name || 'Chưa phân lớp',
            "Niên Khóa": s.academic_year_name || '2025 - 2026',
            "Họ Tên Phụ Huynh": s.parent_name || '',
            "Số Điện Thoại PH": s.parent_phone || '',
            "Địa Chỉ": s.address || '',
            "Trạng Thái": s.status === 'studying' ? 'Đang học' : (s.status === 'transferred' ? 'Chuyển trường' : (s.status === 'graduated' ? 'Đã tốt nghiệp' : 'Đang học'))
        }));

        const ws = XLSX.utils.json_to_sheet(dataToExport);

        const wscols = [
            { wch: 6 },
            { wch: 15 },
            { wch: 25 },
            { wch: 12 },
            { wch: 14 },
            { wch: 16 },
            { wch: 15 },
            { wch: 22 },
            { wch: 16 },
            { wch: 30 },
            { wch: 16 }
        ];
        ws['!cols'] = wscols;

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Danh Sách Học Sinh");

        const dateStr = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(wb, `Danh_Sach_Hoc_Sinh_${dateStr}.xlsx`);
    }

    // =========================================================================
    // TẢI FILE MẪU EXCEL
    // =========================================================================
    function downloadExcelTemplate() {
        const sampleData = [
            {
                "Mã Học Sinh": "HS0001",
                "Họ và Tên": "Nguyễn Văn An",
                "Giới Tính": "Nam",
                "Ngày Sinh": "15/08/2012",
                "Lớp": "10A1",
                "Niên Khóa": "2025 - 2026",
                "Họ Tên Phụ Huynh": "Nguyễn Văn Bình",
                "Số Điện Thoại PH": "0987654321",
                "Địa Chỉ": "Hà Nội"
            },
            {
                "Mã Học Sinh": "HS0002",
                "Họ và Tên": "Trần Thị Mai",
                "Giới Tính": "Nữ",
                "Ngày Sinh": "20/11/2012",
                "Lớp": "10A2",
                "Niên Khóa": "2025 - 2026",
                "Họ Tên Phụ Huynh": "Trần Văn Cường",
                "Số Điện Thoại PH": "0912345678",
                "Địa Chỉ": "TP. Hồ Chí Minh"
            }
        ];

        const ws = XLSX.utils.json_to_sheet(sampleData);
        ws['!cols'] = [{ wch: 14 }, { wch: 22 }, { wch: 10 }, { wch: 14 }, { wch: 12 }, { wch: 15 }, { wch: 20 }, { wch: 16 }, { wch: 25 }];
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Mau_Nhap_Hoc_Sinh");
        XLSX.writeFile(wb, "Mau_Nhap_Danh_Sach_Hoc_Sinh.xlsx");
    }

    // =========================================================================
    // XỬ LÝ NHẬP EXCEL
    // =========================================================================
    function previewExcelFile(input) {
        const file = input.files[0];
        if (!file) return;

        document.getElementById('uploadFileText').textContent = file.name;
        document.getElementById('previewFileName').textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            const json = XLSX.utils.sheet_to_json(worksheet);

            parsedStudents = json;
            document.getElementById('previewStudentCount').textContent = `${json.length} học sinh được nhận diện`;
            document.getElementById('importPreview').classList.remove('hidden');
        };
        reader.readAsArrayBuffer(file);
    }

    async function handleImportExcel() {
        if (!parsedStudents || parsedStudents.length === 0) {
            alert('Vui lòng chọn tệp Excel hợp lệ trước khi bấm xác nhận.');
            return;
        }

        const btn = document.getElementById('btnConfirmImport');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang xử lý ${parsedStudents.length} học sinh...</span>`;

        try {
            const res = await fetch('<?= BASE_URL ?>/students/import-excel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    students: parsedStudents
                })
            });
            const result = await res.json();

            if (result.status === 'success') {
                closeModal('importModal');
                if (typeof showToast === 'function') {
                    showToast(result.message || 'Nhập dữ liệu thành công!', 'success');
                }
                setTimeout(() => window.location.reload(), 800);
            } else {
                alert(result.message || 'Lỗi nhập dữ liệu từ máy chủ.');
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">upload</span><span>Xác Nhận Nhập Dữ Liệu</span>`;
            }
        } catch (e) {
            alert('Lỗi gửi dữ liệu lên máy chủ.');
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">upload</span><span>Xác Nhận Nhập Dữ Liệu</span>`;
        }
    }
</script>
