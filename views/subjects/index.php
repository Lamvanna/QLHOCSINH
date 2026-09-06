<?php // views/subjects/index.php ?>
<?php
// KPI metrics calculations
$subjectList = !empty($allSubjects) ? $allSubjects : ($subjects ?? []);
$totalCount = count($subjectList);
$activeCount = count(array_filter($subjectList, fn($s) => ($s['status'] ?? '') === 'active'));
$inactiveCount = count(array_filter($subjectList, fn($s) => ($s['status'] ?? '') === 'inactive'));
$totalPeriods = array_sum(array_column($subjectList, 'periods_per_week'));
?>

<div class="space-y-6">

    <!-- 1. PAGE HEADER & MAIN ACTIONS -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-1">
                <span class="material-symbols-outlined text-[17px]">menu_book</span>
                <span>Quản Lý Học Vụ & Đào Tạo</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Danh Sách Môn Học</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-0.5">
                Tổng chương trình: <strong class="text-emerald-600 dark:text-emerald-400"><?= number_format($totalCount) ?> môn học</strong> &nbsp;|&nbsp; 
                Đang giảng dạy: <strong class="text-slate-800 dark:text-slate-200"><?= number_format($activeCount) ?> môn</strong>
            </p>
        </div>
        
        <!-- Right Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <button onclick="exportSubjectsExcel()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-bold transition-all shadow-sm group" title="Xuất file Excel chuyên nghiệp">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">table_view</span>
                <span>Xuất Excel</span>
            </button>
            <a href="<?= BASE_URL ?>/subjects/print" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-[#006c4a] hover:bg-[#005137] text-white rounded-xl text-xs font-bold transition-all shadow-sm" title="In danh sách môn học">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>In Danh Sách</span>
            </a>
            <?php if (Permission::can('subjects.create')): ?>
            <button onclick="openSubModal('create')" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-500/20 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Thêm Môn Học Mới</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. EXECUTIVE BENTO KPI METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tổng Môn Học -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tổng Môn Học</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1"><?= number_format($totalCount) ?></h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">auto_stories</span> Khung chương trình
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">menu_book</span>
            </div>
        </div>

        <!-- Card 2: Đang Giảng Dạy -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Đang Giảng Dạy</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1"><?= number_format($activeCount) ?></h3>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Đang áp dụng</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
        </div>

        <!-- Card 3: Tạm Ngừng Dạy -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tạm Ngừng Dạy</p>
                <h3 class="text-2xl font-black text-slate-700 dark:text-slate-300 tracking-tight mt-1"><?= number_format($inactiveCount) ?> Môn</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Chưa phân bổ lịch</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">pause_circle</span>
            </div>
        </div>

        <!-- Card 4: Tổng Tiết / Tuần -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Thời Lượng Tuần</p>
                <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight mt-1"><?= $totalPeriods ?> Tiết</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Phân bổ giảng dạy</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">schedule</span>
            </div>
        </div>
    </div>

    <!-- 3. TOOLBAR: FILTERS, ACTIONS & VIEW SWITCHER -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col xl:flex-row justify-between items-start xl:items-center gap-3">
        <!-- Search & Filter Form -->
        <form method="GET" action="<?= BASE_URL ?>/subjects" id="subjectFilterForm" class="flex flex-wrap items-center gap-2.5 w-full xl:w-auto">
            <!-- Search Input -->
            <div class="relative flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-1.5 focus-within:ring-2 focus-within:ring-emerald-500 w-full sm:w-64 transition-all">
                <span class="material-symbols-outlined text-slate-400 mr-2 text-[18px]">search</span>
                <input name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="bg-transparent border-none outline-none w-full text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:ring-0 p-0" placeholder="Tìm Mã môn, Tên môn học..." type="text"/>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select name="status" onchange="document.getElementById('subjectFilterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Trạng Thái</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang giảng dạy</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tạm ngừng dạy</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Reset Filter -->
            <a href="<?= BASE_URL ?>/subjects" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-rose-500 hover:text-rose-600 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                <span>Xóa lọc</span>
            </a>
        </form>

        <!-- View Mode Switch: Bento Cards vs Data Table -->
        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 self-end xl:self-center">
            <button type="button" id="btnSubjectViewTable" onclick="switchSubjectView('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">table_rows</span>
                <span class="hidden sm:inline">Dạng Bảng</span>
            </button>
            <button type="button" id="btnSubjectViewGrid" onclick="switchSubjectView('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white">
                <span class="material-symbols-outlined text-[16px]">grid_view</span>
                <span class="hidden sm:inline">Dạng Thẻ</span>
            </button>
        </div>
    </div>

    <!-- 4. VIEW 1: BENTO CARDS GRID -->
    <div id="subjectsGridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php if (!empty($subjects)): ?>
            <?php foreach ($subjects as $s): ?>
            <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl overflow-hidden hover:shadow-xl hover:-translate-y-1 hover:border-violet-500/40 transition-all duration-300 group flex flex-col justify-between">
                <div class="h-1.5 w-full bg-gradient-to-r from-violet-500 via-indigo-500 to-emerald-500"></div>
                <div class="p-5 flex flex-col flex-1 space-y-4">
                    <!-- Top Icon & Code -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-violet-50 dark:bg-violet-950/60 text-violet-600 dark:text-violet-400 border border-violet-100 dark:border-violet-900/40 flex items-center justify-center shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">menu_book</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="font-mono text-xs font-bold text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-950/60 px-2.5 py-0.5 rounded-lg border border-violet-200/60">
                                <?= htmlspecialchars($s['code']) ?>
                            </span>
                            <span class="text-[11px] text-slate-400 mt-1">ID: #<?= $s['id'] ?></span>
                        </div>
                    </div>

                    <!-- Subject Name -->
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-lg group-hover:text-violet-600 transition-colors leading-snug">
                            <?= htmlspecialchars($s['name']) ?>
                        </h3>
                    </div>

                    <!-- Subject Specs: Periods -->
                    <div class="py-2.5 px-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[11px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">schedule</span> Thời lượng giảng dạy:
                            </span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                <?= $s['periods_per_week'] ?> tiết / tuần
                            </span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        <?php if ($s['status'] === 'active'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            ĐANG GIẢNG DẠY
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-bold uppercase tracking-wider border border-slate-200">
                            TẠM NGỪNG DẠY
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 mt-auto">
                        <?php if (Permission::can('subjects.update')): ?>
                        <button type="button" 
                                onclick="openSubModal('edit', <?= htmlspecialchars(json_encode($s)) ?>)" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-violet-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 hover:text-violet-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[15px]">edit</span>
                            <span>Chỉnh Sửa</span>
                        </button>
                        <?php endif; ?>

                        <?php if (Permission::can('subjects.delete')): ?>
                        <button type="button" 
                                onclick="openDeleteSubjectModal('<?= $s['id'] ?>', '<?= htmlspecialchars(addslashes($s['name'])) ?>', '<?= htmlspecialchars(addslashes($s['code'])) ?>', '<?= $s['periods_per_week'] ?>')" 
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors ml-auto" title="Xóa môn học">
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
                    <span class="material-symbols-outlined text-3xl text-slate-400">menu_book</span>
                </div>
                <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy môn học nào</p>
                <p class="text-xs text-slate-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc bấm Thêm Môn Học Mới.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 5. VIEW 2: ELEVATED DATA TABLE -->
    <div id="subjectsTableView" class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px] text-xs" id="subjectsTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="p-3.5 pl-5 w-12 text-center whitespace-nowrap">STT</th>
                        <th class="p-3.5 whitespace-nowrap">MÃ MÔN</th>
                        <th class="p-3.5">TÊN MÔN HỌC</th>
                        <th class="p-3.5 text-center">TIẾT / TUẦN</th>
                        <th class="p-3.5">TRẠNG THÁI</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-200">
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $idx => $s): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="p-3.5 pl-5 text-center font-normal text-slate-400 whitespace-nowrap"><?= $idx + 1 ?></td>
                            <td class="p-3.5 font-bold text-violet-600 dark:text-violet-400 whitespace-nowrap font-mono">
                                <span class="bg-violet-50 dark:bg-violet-950/60 border border-violet-200/60 px-2 py-0.5 rounded-lg">
                                    <?= htmlspecialchars($s['code']) ?>
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900 dark:text-white text-sm">
                                    <?= htmlspecialchars($s['name']) ?>
                                </span>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="inline-flex items-center gap-1 font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-lg">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">schedule</span>
                                    <?= $s['periods_per_week'] ?> tiết
                                </span>
                            </td>
                            <td class="p-3.5">
                                <?php if ($s['status'] === 'active'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Đang giảng dạy
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-bold border border-slate-200">
                                    Tạm ngừng dạy
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if (Permission::can('subjects.update')): ?>
                                    <button type="button" 
                                            onclick="openSubModal('edit', <?= htmlspecialchars(json_encode($s)) ?>)" 
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-xl transition-colors" title="Chỉnh sửa">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <?php endif; ?>

                                    <?php if (Permission::can('subjects.delete')): ?>
                                    <button type="button" 
                                            onclick="openDeleteSubjectModal('<?= $s['id'] ?>', '<?= htmlspecialchars(addslashes($s['name'])) ?>', '<?= htmlspecialchars(addslashes($s['code'])) ?>', '<?= $s['periods_per_week'] ?>')" 
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors" title="Xóa môn học">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-3xl text-slate-400">menu_book</span>
                                    </div>
                                    <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy môn học nào</p>
                                    <p class="text-xs text-slate-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc thêm môn học mới.</p>
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
<!-- MODAL: THÊM / CHỈNH SỬA MÔN HỌC (AJAX) -->
<!-- ========================================================================= -->
<div id="subModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-violet-50 dark:bg-violet-950/60 text-violet-600 dark:text-violet-400 border border-violet-100 dark:border-violet-900/40 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">menu_book</span>
                </div>
                <div>
                    <h3 id="subModalTitle" class="text-base font-extrabold text-slate-900 dark:text-white">Thêm Môn Học Mới</h3>
                    <p class="text-[11px] text-slate-400">Thông tin chương trình giảng dạy</p>
                </div>
            </div>
            <button onclick="closeModal('subModal')" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form onsubmit="handleSubSubmit(event)" class="p-6 space-y-4 text-xs">
            <input type="hidden" id="sub_id" value="">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Mã Môn <span class="text-rose-500">*</span></label>
                        <input id="sub_code" type="text" name="code" required placeholder="TOAN, VAN, ANH..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono font-bold text-violet-600 uppercase focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Tên Môn Học <span class="text-rose-500">*</span></label>
                        <input id="sub_name" type="text" name="name" required placeholder="Toán Học, Ngữ Văn..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Số Tiết / Tuần</label>
                        <input id="sub_periods" type="number" name="periods_per_week" min="1" max="15" placeholder="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Trạng Thái Hoạt Động</label>
                        <select id="sub_status" name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-800 dark:text-white cursor-pointer focus:ring-2 focus:ring-violet-500">
                            <option value="active">Đang Giảng Dạy</option>
                            <option value="inactive">Tạm Ngừng Dạy</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('subModal')" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 transition-colors">
                    Hủy Bỏ
                </button>
                <button type="submit" id="btnSaveSubject" class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm shadow-blue-500/20 transition-all active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Lưu Môn Học</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: XÁC NHẬN XÓA MÔN HỌC (CHUẨN MẪU CẢNH BÁO ĐỎ) -->
<!-- ========================================================================= -->
<div id="deleteSubjectConfirmModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden text-center transform transition-all scale-100 animate-in fade-in zoom-in-95 duration-150">
        <!-- Header: Nền hồng đỏ nhạt có tam giác cảnh báo ở giữa -->
        <div class="bg-[#feecee] dark:bg-rose-950/40 border-b border-rose-100 dark:border-rose-900/50 pt-7 pb-6 px-6">
            <div class="w-12 h-12 rounded-full bg-rose-200/80 dark:bg-rose-900/70 text-rose-700 dark:text-rose-300 flex items-center justify-center mx-auto mb-3 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-rose-700 dark:text-rose-300">
                Xác nhận xóa môn học?
            </h3>
        </div>

        <!-- Body: Nền trắng có đoạn văn cảnh báo và thẻ môn học -->
        <div class="p-6 sm:p-7 bg-white dark:bg-slate-900 space-y-6">
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed text-center">
                Hành động này không thể hoàn tác. Các dữ liệu liên quan như điểm số, thời khóa biểu và phân công của môn học này có thể bị ảnh hưởng.
            </p>

            <!-- Khung thông tin môn học -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-4 bg-white dark:bg-slate-800/50 flex items-center gap-4 text-left shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-100 dark:border-rose-900/40 text-rose-600 flex items-center justify-center shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-2xl">menu_book</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span id="modalSubjectName" class="font-bold text-slate-900 dark:text-white text-base truncate">Toán Học</span>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md font-mono">
                            Mã: <span id="modalSubjectCode">TOAN</span>
                        </span>
                        <span id="modalSubjectDetails" class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            4 tiết / tuần
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="modalDeleteSubjectId" value="">

        <!-- Footer: Nút [Hủy bỏ] và [Xác nhận xóa] -->
        <div class="p-4 sm:p-5 px-7 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('deleteSubjectConfirmModal')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition-all shadow-sm">
                Hủy bỏ
            </button>
            <button type="button" id="btnExecuteSubjectDelete" onclick="executeDeleteSubject()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#b91c1c] hover:bg-[#991b1b] active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>Xác nhận xóa</span>
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    const ALL_SUBJECTS = <?= json_encode($subjects ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // =========================================================================
    // CHUYỂN ĐỔI CHẾ ĐỘ XEM (VIEW SWITCH: GRID / TABLE)
    // =========================================================================
    function switchSubjectView(mode) {
        const gridView = document.getElementById('subjectsGridView');
        const tableView = document.getElementById('subjectsTableView');
        const btnGrid = document.getElementById('btnSubjectViewGrid');
        const btnTable = document.getElementById('btnSubjectViewTable');

        if (mode === 'grid') {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');

            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm';
            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('subject_view_mode', 'grid');
        } else {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');

            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm';
            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('subject_view_mode', 'table');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('subject_view_mode');
        if (savedMode === 'grid') {
            switchSubjectView('grid');
        } else {
            switchSubjectView('table');
        }
    });

    // =========================================================================
    // MODAL THÊM / SỬA MÔN HỌC
    // =========================================================================
    function openSubModal(mode, data = null) {
        document.getElementById('subModalTitle').textContent = mode === 'edit' ? 'Cập Nhật Môn Học' : 'Thêm Môn Học Mới';
        document.getElementById('sub_id').value      = data?.id ?? '';
        document.getElementById('sub_code').value    = data?.code ?? '';
        document.getElementById('sub_code').readOnly = mode === 'edit';
        document.getElementById('sub_name').value    = data?.name ?? '';
        document.getElementById('sub_periods').value = data?.periods_per_week ?? 2;
        document.getElementById('sub_status').value  = data?.status ?? 'active';

        openModal('subModal');
    }

    async function handleSubSubmit(e) {
        e.preventDefault();
        const id  = document.getElementById('sub_id').value;
        const url = id ? `<?= BASE_URL ?>/subjects/${id}` : `<?= BASE_URL ?>/subjects`;
        const btn = document.getElementById('btnSaveSubject');
        
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang lưu...</span>`;

        try {
            const formData = new FormData(e.target);
            const payload = Object.fromEntries(formData.entries());
            const res = await apiPost(url, payload);

            if (res) {
                closeModal('subModal');
                if (typeof showToast === 'function') {
                    showToast(id ? 'Cập nhật môn học thành công!' : 'Tạo môn học mới thành công!', 'success');
                }
                setTimeout(() => location.reload(), 600);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[16px]">save</span><span>Lưu Môn Học</span>`;
            }
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[16px]">save</span><span>Lưu Môn Học</span>`;
            alert('Lỗi xử lý máy chủ. Vui lòng kiểm tra lại!');
        }
    }

    // =========================================================================
    // MODAL XÓA MÔN HỌC (CHUẨN MẪU CẢNH BÁO ĐỎ)
    // =========================================================================
    function openDeleteSubjectModal(id, name, code, periods) {
        document.getElementById('modalDeleteSubjectId').value = id;
        document.getElementById('modalSubjectName').textContent = name || '';
        document.getElementById('modalSubjectCode').textContent = code || '';
        document.getElementById('modalSubjectDetails').textContent = `${periods || 2} tiết / tuần`;

        openModal('deleteSubjectConfirmModal');
    }

    async function executeDeleteSubject() {
        const id = document.getElementById('modalDeleteSubjectId').value;
        if (!id) return;

        const btn = document.getElementById('btnExecuteSubjectDelete');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang xóa...</span>`;

        try {
            const res = await apiPost(`<?= BASE_URL ?>/subjects/${id}`, { _method: 'DELETE' });
            if (res) {
                closeModal('deleteSubjectConfirmModal');
                if (typeof showToast === 'function') {
                    showToast('Đã xóa môn học thành công!', 'success');
                }
                setTimeout(() => location.reload(), 600);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            }
        } catch (e) {
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            alert('Không thể kết nối đến máy chủ. Vui lòng thử lại sau!');
        }
    }

    // =========================================================================
    // XUẤT EXCEL MÔN HỌC
    // =========================================================================
    function exportSubjectsExcel() {
        const rawData = ALL_SUBJECTS;
        if (!rawData || rawData.length === 0) {
            alert('Không có dữ liệu môn học để xuất.');
            return;
        }

        const dataToExport = rawData.map((s, index) => ({
            "STT": index + 1,
            "Mã Môn Học": s.code || '',
            "Tên Môn Học": s.name || '',
            "Số Tiết / Tuần": s.periods_per_week || 2,
            "Trạng Thái": s.status === 'active' ? 'Đang giảng dạy' : 'Tạm ngừng dạy'
        }));

        const ws = XLSX.utils.json_to_sheet(dataToExport);
        ws['!cols'] = [
            { wch: 6 },
            { wch: 16 },
            { wch: 28 },
            { wch: 18 },
            { wch: 20 }
        ];

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Danh Sách Môn Học");

        const dateStr = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(wb, `Danh_Sach_Mon_Hoc_${dateStr}.xlsx`);
    }
</script>
