<?php // views/teachers/index.php ?>
<?php
// KPI metrics calculations
$totalTeachers = count($allTeachers ?? $teachers ?? []);
$activeTeachers = count(array_filter($allTeachers ?? $teachers ?? [], fn($t) => ($t['status'] ?? '') === 'active'));
$homeroomTeachers = count(array_filter($allTeachers ?? $teachers ?? [], fn($t) => !empty($t['homeroom_classes'])));
$totalSpecializations = count($specializations ?? []);
?>

<div class="space-y-6">

    <!-- 1. PAGE HEADER & MAIN ACTIONS -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-1">
                <span class="material-symbols-outlined text-[17px]">school</span>
                <span>Quản Lý Nhân Sự Sư Phạm</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Danh Sách Giáo Viên</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-0.5">
                Tổng đội ngũ: <strong class="text-emerald-600 dark:text-emerald-400"><?= number_format($totalTeachers) ?> giáo viên</strong> &nbsp;|&nbsp; 
                Đang công tác: <strong class="text-slate-800 dark:text-slate-200"><?= number_format($activeTeachers) ?> thầy cô</strong>
            </p>
        </div>
        
        <!-- Right Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <button onclick="exportTeachersExcel()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-bold transition-all shadow-sm group" title="Xuất file Excel chuyên nghiệp">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">table_view</span>
                <span>Xuất Excel</span>
            </button>
            <a href="<?= BASE_URL ?>/teachers/print" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-[#006c4a] hover:bg-[#005137] text-white rounded-xl text-xs font-bold transition-all shadow-sm" title="In danh sách giáo viên chuẩn A4">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>In Danh Sách</span>
            </a>
            <button onclick="openModal('importTeacherModal')" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold transition-all shadow-sm group" title="Nhập danh sách giáo viên từ file Excel">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">upload</span>
                <span>Nhập Excel</span>
            </button>
            <?php if (Permission::can('teachers.create')): ?>
            <a href="<?= BASE_URL ?>/teachers/create" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-500/20 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Thêm Giáo Viên Mới</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. EXECUTIVE BENTO KPI METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tổng Giáo Viên -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tổng Giáo Viên</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1"><?= number_format($totalTeachers) ?></h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">verified</span> Nhân sự sư phạm
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">badge</span>
            </div>
        </div>

        <!-- Card 2: Đang Giảng Dạy -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Đang Giảng Dạy</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1"><?= number_format($activeTeachers) ?></h3>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Đang công tác</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">person_check</span>
            </div>
        </div>

        <!-- Card 3: Giáo Viên Chủ Nhiệm -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Chủ Nhiệm Lớp</p>
                <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight mt-1"><?= number_format($homeroomTeachers) ?> Thầy/Cô</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Quản lý nề nếp lớp</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">school</span>
            </div>
        </div>

        <!-- Card 4: Tổ Chuyên Môn -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tổ Chuyên Môn</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1"><?= $totalSpecializations ?> Bộ Môn</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Phân khoa giảng dạy</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">menu_book</span>
            </div>
        </div>
    </div>

    <!-- 3. TOOLBAR: FILTERS, ACTIONS & VIEW SWITCHER -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col xl:flex-row justify-between items-start xl:items-center gap-3">
        <!-- Search & Filter Form -->
        <form method="GET" action="<?= BASE_URL ?>/teachers" id="filterForm" class="flex flex-wrap items-center gap-2.5 w-full xl:w-auto">
            <!-- Search Input -->
            <div class="relative flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-1.5 focus-within:ring-2 focus-within:ring-emerald-500 w-full sm:w-64 transition-all">
                <span class="material-symbols-outlined text-slate-400 mr-2 text-[18px]">search</span>
                <input name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="bg-transparent border-none outline-none w-full text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:ring-0 p-0" placeholder="Tìm Mã GV, Họ tên, SĐT, Môn..." type="text"/>
            </div>

            <!-- Specialization Filter -->
            <div class="relative">
                <select name="specialization" onchange="document.getElementById('filterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Chuyên môn</option>
                    <?php foreach ($specializations as $spec): ?>
                    <option value="<?= htmlspecialchars($spec) ?>" <?= ($filters['specialization'] ?? '') === $spec ? 'selected' : '' ?>><?= htmlspecialchars($spec) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select name="status" onchange="document.getElementById('filterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Trạng thái</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang công tác</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tạm nghỉ</option>
                    <option value="retired" <?= ($filters['status'] ?? '') === 'retired' ? 'selected' : '' ?>>Nghỉ hưu</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Reset Filter -->
            <a href="<?= BASE_URL ?>/teachers" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-rose-500 hover:text-rose-600 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                <span>Xóa lọc</span>
            </a>
        </form>

        <!-- View Mode Switch: Bento Cards vs Data Table -->
        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 self-end xl:self-center">
            <button type="button" id="btnTeacherViewTable" onclick="switchTeacherView('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">table_rows</span>
                <span class="hidden sm:inline">Dạng Bảng</span>
            </button>
            <button type="button" id="btnTeacherViewGrid" onclick="switchTeacherView('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white">
                <span class="material-symbols-outlined text-[16px]">grid_view</span>
                <span class="hidden sm:inline">Dạng Thẻ</span>
            </button>
        </div>
    </div>

    <!-- 4. VIEW 1: BENTO CARDS GRID -->
    <div id="teachersGridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php if (!empty($teachers)): ?>
            <?php foreach ($teachers as $t): ?>
            <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl overflow-hidden hover:shadow-xl hover:-translate-y-1 hover:border-indigo-500/40 transition-all duration-300 group flex flex-col justify-between">
                <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-500"></div>
                <div class="p-5 flex flex-col flex-1 space-y-4">
                    <!-- Top Avatar & Name -->
                    <div class="flex items-start gap-3.5">
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                                <img src="<?= htmlspecialchars($t['avatar'] ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150') ?>" class="w-full h-full object-cover" alt="Avatar">
                            </div>
                            <?php if (($t['status'] ?? 'active') === 'active'): ?>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900" title="Đang công tác"></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="font-mono text-[11px] font-bold text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md inline-block w-fit">
                                <?= htmlspecialchars($t['teacher_code']) ?>
                            </span>
                            <span class="font-extrabold text-slate-900 dark:text-white text-base truncate mt-0.5">
                                <?= htmlspecialchars($t['full_name']) ?>
                            </span>
                            <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-0.5">
                                <span class="font-semibold <?= ($t['gender'] ?? 'male') === 'female' ? 'text-rose-500' : 'text-blue-600' ?>">
                                    <?= ($t['gender'] ?? 'male') === 'female' ? 'Nữ' : 'Nam' ?>
                                </span>
                                <span>•</span>
                                <span><?= !empty($t['dob']) ? date('d/m/Y', strtotime($t['dob'])) : '—' ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Specialization & Homeroom Details -->
                    <div class="py-2.5 px-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[11px]">Chuyên môn:</span>
                            <span class="font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-2.5 py-0.5 rounded-lg">
                                <?= htmlspecialchars($t['specialization'] ?? 'Giảng dạy') ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[11px]">Chủ nhiệm:</span>
                            <span class="font-bold <?= !empty($t['homeroom_classes']) ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-lg' : 'text-slate-400 italic' ?>">
                                <?= htmlspecialchars($t['homeroom_classes'] ?? 'Chưa phân công') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">call</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200"><?= htmlspecialchars($t['phone'] ?? '—') ?></span>
                        </div>
                        <div class="flex items-center gap-2 truncate">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">mail</span>
                            <span class="truncate"><?= htmlspecialchars($t['email'] ?? 'gv@edumanage.edu.vn') ?></span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        <?php if ($t['status'] === 'active'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            ĐANG CÔNG TÁC
                        </span>
                        <?php elseif ($t['status'] === 'inactive'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-[11px] font-bold uppercase tracking-wider border border-amber-200">
                            TẠM NGHỈ
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-bold uppercase tracking-wider border border-slate-200">
                            NGHỈ HƯU
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 mt-auto">
                        <?php if (Permission::can('teachers.update')): ?>
                        <a href="<?= BASE_URL ?>/teachers/<?= $t['id'] ?>/edit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 hover:text-blue-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[15px]">edit</span>
                            <span>Chỉnh Sửa</span>
                        </a>
                        <?php endif; ?>

                        <?php if (Permission::can('teachers.delete')): ?>
                        <button type="button" 
                                onclick="openTeacherDeleteConfirmModal('<?= $t['id'] ?>', '<?= htmlspecialchars(addslashes($t['full_name'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($t['teacher_code'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($t['phone'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($t['avatar'] ?? '')) ?>')" 
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors" title="Xóa giáo viên">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-16 text-center text-slate-400">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-3xl text-slate-400">person_off</span>
                </div>
                <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy giáo viên nào</p>
                <p class="text-xs text-slate-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc bấm Thêm Giáo Viên Mới.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 5. VIEW 2: ELEVATED DATA TABLE -->
    <div id="teachersTableView" class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[950px] text-xs" id="teachersTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="p-3.5 pl-5 w-12 text-center whitespace-nowrap">STT</th>
                        <th class="p-3.5 whitespace-nowrap">MÃ GIÁO VIÊN</th>
                        <th class="p-3.5">THÔNG TIN GIÁO VIÊN</th>
                        <th class="p-3.5">GIỚI TÍNH / NGÀY SINH</th>
                        <th class="p-3.5">LIÊN HỆ</th>
                        <th class="p-3.5">TRẠNG THÁI</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-200">
                    <?php if (!empty($teachers)): ?>
                        <?php foreach ($teachers as $idx => $t): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="p-3.5 pl-5 text-center font-normal text-slate-400 whitespace-nowrap"><?= $idx + 1 ?></td>
                            <td class="p-3.5 font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap font-mono">
                                <?= htmlspecialchars($t['teacher_code']) ?>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0 shadow-sm">
                                        <img src="<?= htmlspecialchars($t['avatar'] ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150') ?>" class="w-full h-full object-cover" alt="Avatar">
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold text-slate-900 dark:text-white text-sm truncate">
                                            <?= htmlspecialchars($t['full_name']) ?>
                                        </span>
                                        <span class="text-xs text-slate-400 truncate mt-0.5"><?= htmlspecialchars($t['email'] ?? 'gv@edumanage.edu.vn') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-xs <?= ($t['gender'] ?? 'male') === 'female' ? 'text-rose-500' : 'text-blue-600' ?>">
                                        <?= ($t['gender'] ?? 'male') === 'female' ? 'Nữ' : 'Nam' ?>
                                    </span>
                                    <span class="text-xs text-slate-400 mt-0.5"><?= !empty($t['dob']) ? date('d/m/Y', strtotime($t['dob'])) : '—' ?></span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div class="flex flex-col text-xs">
                                    <span class="font-mono text-slate-800 dark:text-slate-200"><?= htmlspecialchars($t['phone'] ?? '—') ?></span>
                                    <span class="text-[11px] text-slate-400 truncate max-w-[140px] mt-0.5"><?= htmlspecialchars($t['address'] ?? 'TP. Hồ Chí Minh') ?></span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <?php if ($t['status'] === 'active'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Đang công tác
                                </span>
                                <?php elseif ($t['status'] === 'inactive'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[11px] font-bold border border-amber-200">
                                    Tạm nghỉ
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-bold border border-slate-200">
                                    Nghỉ hưu
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if (Permission::can('teachers.update')): ?>
                                    <a href="<?= BASE_URL ?>/teachers/<?= $t['id'] ?>/edit" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-xl transition-colors" title="Chỉnh sửa thông tin">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (Permission::can('teachers.delete')): ?>
                                    <button type="button" 
                                            onclick="openTeacherDeleteConfirmModal('<?= $t['id'] ?>', '<?= htmlspecialchars(addslashes($t['full_name'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($t['teacher_code'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($t['phone'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($t['avatar'] ?? '')) ?>')" 
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors" title="Xóa giáo viên">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-3xl text-slate-400">person_off</span>
                                    </div>
                                    <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy giáo viên nào</p>
                                    <p class="text-xs text-slate-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc thêm giáo viên mới.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: XÁC NHẬN XÓA GIÁO VIÊN (CHUẨN MẪU CẢNH BÁO ĐỎ) -->
<!-- ========================================================================= -->
<div id="deleteTeacherConfirmModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden text-center transform transition-all scale-100 animate-in fade-in zoom-in-95 duration-150">
        <!-- Header: Nền hồng đỏ nhạt có tam giác cảnh báo ở giữa -->
        <div class="bg-[#feecee] dark:bg-rose-950/40 border-b border-rose-100 dark:border-rose-900/50 pt-7 pb-6 px-6">
            <div class="w-12 h-12 rounded-full bg-rose-200/80 dark:bg-rose-900/70 text-rose-700 dark:text-rose-300 flex items-center justify-center mx-auto mb-3 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-rose-700 dark:text-rose-300">
                Xác nhận xóa hồ sơ giáo viên?
            </h3>
        </div>

        <!-- Body: Nền trắng có đoạn văn cảnh báo và thẻ giáo viên -->
        <div class="p-6 sm:p-7 bg-white dark:bg-slate-900 space-y-6">
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed text-center">
                Hành động này không thể hoàn tác. Toàn bộ dữ liệu giảng dạy và phân công liên quan đến giáo viên này sẽ bị loại bỏ khỏi hệ thống.
            </p>

            <!-- Khung thông tin giáo viên -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-4 bg-white dark:bg-slate-800/50 flex items-center gap-4 text-left shadow-sm">
                <div class="w-14 h-14 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 shrink-0 shadow-sm flex items-center justify-center">
                    <img id="modalTeacherAvatarImg" src="" class="w-full h-full object-cover hidden" alt="Avatar">
                    <span id="modalTeacherAvatarText" class="font-bold text-slate-600 dark:text-slate-200 text-sm">GV</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span id="modalTeacherName" class="font-bold text-slate-900 dark:text-white text-base truncate">Thầy/Cô</span>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            Mã: <span id="modalTeacherCode">GV001</span>
                        </span>
                        <span id="modalTeacherPhone" class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md font-mono">
                            0900000000
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="modalDeleteTeacherId" value="">

        <!-- Footer: Nút [Hủy bỏ] và [Xác nhận xóa] -->
        <div class="p-4 sm:p-5 px-7 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('deleteTeacherConfirmModal')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition-all shadow-sm">
                Hủy bỏ
            </button>
            <button type="button" id="btnExecuteTeacherDelete" onclick="executeDeleteTeacher()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#b91c1c] hover:bg-[#991b1b] active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>Xác nhận xóa</span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: NHẬP GIÁO VIÊN TỪ EXCEL -->
<!-- ========================================================================= -->
<div id="importTeacherModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 md:p-7 max-w-xl w-full shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Nhập Danh Sách Giáo Viên Từ Excel</h3>
                    <p class="text-[11px] text-slate-400">Đồng bộ hồ sơ nhân sự sư phạm từ tệp tính Excel</p>
                </div>
            </div>
            <button onclick="closeModal('importTeacherModal')" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50/80 via-emerald-50/40 to-blue-50/80 dark:from-slate-800 dark:to-slate-800 border border-blue-100 dark:border-slate-700/80 space-y-2 text-xs">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-[16px]">info</span>
                    Thông Tin & Cấu Trúc Dữ Liệu
                </span>
                <button type="button" onclick="downloadTeacherExcelTemplate()" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-slate-600 hover:bg-blue-50 shadow-sm flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">file_download</span>
                    Tải File Mẫu
                </button>
            </div>
            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                File Excel cần có các cột: Mã GV, Họ và Tên, Giới tính, Ngày sinh, Số điện thoại, Email, Chuyên môn.
            </p>
        </div>

        <div class="space-y-3">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Chọn tệp Excel (.xlsx hoặc .xls)</label>
            <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-6 text-center hover:border-blue-500 transition-colors bg-slate-50/50 dark:bg-slate-800/30 cursor-pointer relative group" onclick="document.getElementById('excelTeacherFileInput').click()">
                <input type="file" id="excelTeacherFileInput" accept=".xlsx, .xls" class="hidden" onchange="previewTeacherExcelFile(this)">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl">upload</span>
                </div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-200" id="uploadTeacherFileText">Kéo thả tệp hoặc bấm để chọn từ máy tính</p>
                <p class="text-[10px] text-slate-400 mt-1">Hỗ trợ định dạng .xlsx, .xls tiêu chuẩn</p>
            </div>
        </div>

        <div id="importTeacherPreview" class="hidden p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-700 dark:text-slate-200" id="previewTeacherFileName">Tệp tin</span>
                <span class="font-bold text-emerald-600" id="previewTeacherCount">0 giáo viên</span>
            </div>
        </div>

        <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeModal('importTeacherModal')" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors">
                Hủy Bỏ
            </button>
            <button type="button" id="btnConfirmTeacherImport" onclick="handleTeacherImportExcel()" class="inline-flex items-center gap-1.5 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all disabled:opacity-50">
                <span class="material-symbols-outlined text-[18px]">upload</span>
                <span>Xác Nhận Nhập Dữ Liệu</span>
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    const ALL_TEACHERS = <?= json_encode($teachers ?? [], JSON_UNESCAPED_UNICODE) ?>;
    let parsedTeachers = [];

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // =========================================================================
    // CHUYỂN ĐỔI CHẾ ĐỘ XEM (VIEW SWITCH: GRID / TABLE)
    // =========================================================================
    function switchTeacherView(mode) {
        const gridView = document.getElementById('teachersGridView');
        const tableView = document.getElementById('teachersTableView');
        const btnGrid = document.getElementById('btnTeacherViewGrid');
        const btnTable = document.getElementById('btnTeacherViewTable');

        if (mode === 'grid') {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');

            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm';
            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('teacher_view_mode', 'grid');
        } else {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');

            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm';
            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('teacher_view_mode', 'table');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('teacher_view_mode');
        if (savedMode === 'grid') {
            switchTeacherView('grid');
        } else {
            switchTeacherView('table');
        }
    });

    // =========================================================================
    // MODAL XÓA HỒ SƠ GIÁO VIÊN
    // =========================================================================
    function openTeacherDeleteConfirmModal(id, name, code, phone, avatarUrl) {
        document.getElementById('modalDeleteTeacherId').value = id;
        document.getElementById('modalTeacherName').textContent = name || '';
        document.getElementById('modalTeacherCode').textContent = code || '';
        document.getElementById('modalTeacherPhone').textContent = phone || 'Chưa cập nhật SĐT';
        
        const avatarImg = document.getElementById('modalTeacherAvatarImg');
        const avatarText = document.getElementById('modalTeacherAvatarText');
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
                : (name || 'GV').substring(0, 2).toUpperCase();
            avatarText.textContent = initials;
        }

        openModal('deleteTeacherConfirmModal');
    }

    async function executeDeleteTeacher() {
        const id = document.getElementById('modalDeleteTeacherId').value;
        if (!id) return;

        const btn = document.getElementById('btnExecuteTeacherDelete');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang xóa...</span>`;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const res = await fetch(`<?= BASE_URL ?>/teachers/${id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (data.status === 'success') {
                closeModal('deleteTeacherConfirmModal');
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Đã xóa hồ sơ giáo viên thành công!', 'success');
                }
                setTimeout(() => window.location.reload(), 600);
            } else {
                alert(data.message || 'Có lỗi xảy ra khi xóa giáo viên.');
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
    // XUẤT EXCEL GIÁO VIÊN
    // =========================================================================
    function exportTeachersExcel() {
        const rawData = ALL_TEACHERS;
        if (!rawData || rawData.length === 0) {
            alert('Không có dữ liệu giáo viên để xuất.');
            return;
        }

        const dataToExport = rawData.map((t, index) => ({
            "STT": index + 1,
            "Mã Giáo Viên": t.teacher_code || '',
            "Họ và Tên": t.full_name || '',
            "Giới Tính": (t.gender === 'female' || t.gender === 'Nữ') ? 'Nữ' : 'Nam',
            "Ngày Sinh": t.dob ? new Date(t.dob).toLocaleDateString('vi-VN') : '',
            "Số Điện Thoại": t.phone || '',
            "Email": t.email || '',
            "Chuyên Môn": t.specialization || '',
            "Chủ Nhiệm": t.homeroom_classes || 'Chưa phân công',
            "Địa Chỉ": t.address || '',
            "Trạng Thái": t.status === 'active' ? 'Đang công tác' : (t.status === 'inactive' ? 'Tạm nghỉ' : 'Nghỉ hưu')
        }));

        const ws = XLSX.utils.json_to_sheet(dataToExport);
        ws['!cols'] = [
            { wch: 6 },
            { wch: 15 },
            { wch: 25 },
            { wch: 12 },
            { wch: 14 },
            { wch: 16 },
            { wch: 26 },
            { wch: 18 },
            { wch: 16 },
            { wch: 30 },
            { wch: 16 }
        ];

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Danh Sách Giáo Viên");

        const dateStr = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(wb, `Danh_Sach_Giao_Vien_${dateStr}.xlsx`);
    }

    // =========================================================================
    // TẢI FILE MẪU EXCEL GIÁO VIÊN
    // =========================================================================
    function downloadTeacherExcelTemplate() {
        const sampleData = [
            {
                "Mã Giáo Viên": "GV001",
                "Họ và Tên": "Nguyễn Văn Tuấn",
                "Giới Tính": "Nam",
                "Ngày Sinh": "12/05/1985",
                "Số Điện Thoại": "0912345678",
                "Email": "tuan.nv@school.edu.vn",
                "Chuyên Môn": "Toán Học",
                "Địa Chỉ": "Hà Nội"
            },
            {
                "Mã Giáo Viên": "GV002",
                "Họ và Tên": "Trần Thị Lan",
                "Giới Tính": "Nữ",
                "Ngày Sinh": "18/09/1990",
                "Số Điện Thoại": "0987654321",
                "Email": "lan.tt@school.edu.vn",
                "Chuyên Môn": "Ngữ Văn",
                "Địa Chỉ": "TP. Hồ Chí Minh"
            }
        ];

        const ws = XLSX.utils.json_to_sheet(sampleData);
        ws['!cols'] = [{ wch: 14 }, { wch: 22 }, { wch: 10 }, { wch: 14 }, { wch: 16 }, { wch: 26 }, { wch: 16 }, { wch: 25 }];
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Mau_Nhap_Giao_Vien");
        XLSX.writeFile(wb, "Mau_Nhap_Danh_Sach_Giao_Vien.xlsx");
    }

    // =========================================================================
    // XỬ LÝ NHẬP EXCEL GIÁO VIÊN
    // =========================================================================
    function previewTeacherExcelFile(input) {
        const file = input.files[0];
        if (!file) return;

        document.getElementById('uploadTeacherFileText').textContent = file.name;
        document.getElementById('previewTeacherFileName').textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            const json = XLSX.utils.sheet_to_json(worksheet);

            parsedTeachers = json;
            document.getElementById('previewTeacherCount').textContent = `${json.length} giáo viên được nhận diện`;
            document.getElementById('importTeacherPreview').classList.remove('hidden');
        };
        reader.readAsArrayBuffer(file);
    }

    async function handleTeacherImportExcel() {
        if (!parsedTeachers || parsedTeachers.length === 0) {
            alert('Vui lòng chọn tệp Excel hợp lệ trước khi bấm xác nhận.');
            return;
        }

        const btn = document.getElementById('btnConfirmTeacherImport');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang xử lý ${parsedTeachers.length} giáo viên...</span>`;

        try {
            const res = await fetch('<?= BASE_URL ?>/teachers/import-excel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    teachers: parsedTeachers
                })
            });
            const result = await res.json();

            if (result.status === 'success') {
                closeModal('importTeacherModal');
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
