<?php // views/classes/index.php ?>
<div class="space-y-6">

    <!-- 1. HEADER & MAIN ACTIONS -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-1">
                <span class="material-symbols-outlined text-[17px]">door_front</span>
                <span>Quản Lý Học Vụ & Lớp Học</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Danh Sách Lớp Học</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-0.5">
                Tổng số: <strong class="text-emerald-600 dark:text-emerald-400"><?= count($classes) ?> lớp</strong> &nbsp;|&nbsp; 
                Quy mô: <strong class="text-slate-800 dark:text-slate-200"><?= array_sum(array_column($classes, 'student_count')) ?> học sinh</strong>
            </p>
        </div>
        
        <!-- Right Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <button onclick="exportClassesExcel()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-bold transition-all shadow-sm" title="Xuất danh sách tất cả lớp học kèm môn học ra Excel">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">table_view</span>
                <span>Xuất Excel Danh Sách</span>
            </button>
            <a href="<?= BASE_URL ?>/classes/print" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-[#006c4a] hover:bg-[#005137] text-white rounded-xl text-xs font-bold transition-all shadow-sm" title="In danh sách lớp">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>In Danh Sách</span>
            </a>
            <?php if (Permission::can('classes.create')): ?>
            <button onclick="openClassModal('create')" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-500/20 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Thêm Lớp Mới</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. TOOLBAR: FILTERS & VIEW MODE SWITCH -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col xl:flex-row justify-between items-start xl:items-center gap-3">
        <!-- Filter Form -->
        <form method="GET" action="<?= BASE_URL ?>/classes" id="classFilterForm" class="flex flex-wrap items-center gap-2.5 w-full xl:w-auto">
            <!-- Search -->
            <div class="relative flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-1.5 focus-within:ring-2 focus-within:ring-emerald-500 w-full sm:w-60 transition-all">
                <span class="material-symbols-outlined text-slate-400 mr-2 text-[18px]">search</span>
                <input name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="bg-transparent border-none outline-none w-full text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:ring-0 p-0" placeholder="Tìm Mã lớp, Tên lớp, GVCN..." type="text"/>
            </div>

            

            <!-- Year Filter -->
            <div class="relative">
                <select name="academic_year_id" onchange="document.getElementById('classFilterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Niên Khóa</option>
                    <?php foreach ($years as $y): ?>
                    <option value="<?= $y['id'] ?>" <?= ($filters['academic_year_id'] ?? '') == $y['id'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select name="status" onchange="document.getElementById('classFilterForm').submit()" class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-3.5 pr-8 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Trạng thái</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tạm ngưng</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Reset Filter -->
            <a href="<?= BASE_URL ?>/classes" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-rose-500 hover:text-rose-600 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                <span>Xóa lọc</span>
            </a>
        </form>

        <!-- View Mode Switch: Bento Cards vs Data Table -->
        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 self-end xl:self-center">
            <button type="button" id="btnViewGrid" onclick="switchClassView('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">grid_view</span>
                <span class="hidden sm:inline">Dạng Thẻ</span>
            </button>
            <button type="button" id="btnViewTable" onclick="switchClassView('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white">
                <span class="material-symbols-outlined text-[16px]">table_rows</span>
                <span class="hidden sm:inline">Dạng Bảng</span>
            </button>
        </div>
    </div>

    <!-- 3. VIEW 1: BENTO CLASS CARDS GRID -->
    <div id="classesGridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php if (!empty($classes)): ?>
            <?php foreach ($classes as $c):
                $maxSt = (int)($c['max_students'] ?? 45);
                $curSt = (int)($c['student_count'] ?? 0);
                $fillPct = $maxSt > 0 ? round(($curSt / max(1, $maxSt)) * 100) : 0;
                $isFull = $fillPct >= 100;
                $isAlmost = $fillPct >= 85 && !$isFull;

                $barColor = $isFull ? 'bg-rose-500' : ($isAlmost ? 'bg-amber-500' : 'bg-emerald-500');
                $pctColor = $isFull ? 'text-rose-600 dark:text-rose-400' : ($isAlmost ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400');
                $subCount = (int)($c['subject_count'] ?? 0);
            ?>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden hover:shadow-lg transition-all duration-200 group flex flex-col">
                <!-- Top Accent Color Bar -->
                <div class="h-2 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-500"></div>

                <div class="p-5 flex flex-col flex-1 space-y-4">
                    <!-- Top Meta & Title -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                <span class="font-mono text-xs font-black px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">
                                    <?= htmlspecialchars($c['code']) ?>
                                </span>
                                <?php if ($c['status'] === 'active'): ?>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/80 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                                    ● HOẠT ĐỘNG
                                </span>
                                <?php else: ?>
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full border border-slate-300 dark:border-slate-700">
                                    TẠM NGƯNG
                                </span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white group-hover:text-emerald-600 transition-colors">
                                <?= htmlspecialchars($c['name']) ?>
                            </h3>
                        </div>

                        
                    </div>

                    <!-- Teacher & Room Details -->
                    <div class="space-y-2 py-2 border-y border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300">
                            <span class="material-symbols-outlined text-[17px] text-emerald-600 shrink-0">person_pin</span>
                            <span class="truncate">
                                GVCN: <strong><?= htmlspecialchars($c['homeroom_teacher_name'] ?? 'Chưa phân công') ?></strong>
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-[17px] text-blue-500 shrink-0">meeting_room</span>
                            <span>
                                Phòng học: <strong class="text-slate-700 dark:text-slate-200"><?= htmlspecialchars($c['room_number'] ?? 'Chưa xếp') ?></strong>
                            </span>
                        </div>
                    </div>

                    <!-- SUBJECTS INFO & QUICK EXPORT (Phần môn học & Tải bảng điểm riêng) -->
                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                                <span class="material-symbols-outlined text-[16px] text-amber-500">menu_book</span>
                                <span><?= $subCount ?> môn học</span>
                            </div>
                            <button type="button" 
                                    onclick="exportClassGradeSheet(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>', '<?= htmlspecialchars($c['code']) ?>')" 
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-sm transition-all active:scale-95" 
                                    title="Tải bảng điểm Excel riêng theo các môn của lớp <?= htmlspecialchars($c['name']) ?>">
                                <span class="material-symbols-outlined text-[13px]">download</span>
                                <span>Tải Bảng Điểm</span>
                            </button>
                        </div>
                        <?php if (!empty($c['subject_names'])): ?>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate" title="<?= htmlspecialchars($c['subject_names']) ?>">
                            <?= htmlspecialchars($c['subject_names']) ?>
                        </p>
                        <?php else: ?>
                        <p class="text-[11px] text-amber-600 dark:text-amber-400 italic">
                            Chưa chọn môn học cho lớp
                        </p>
                        <?php endif; ?>
                    </div>

                    <!-- Capacity Progress Bar -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Sĩ số hiện tại:</span>
                            <span class="font-bold <?= $pctColor ?>">
                                <?= $curSt ?> / <?= $maxSt ?> (<?= $fillPct ?>%)
                            </span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden p-0.5">
                            <div class="h-full <?= $barColor ?> rounded-full transition-all duration-500" style="width: <?= min(100, $fillPct) ?>%"></div>
                        </div>
                    </div>

                    <!-- Card Actions Footer -->
                    <div class="pt-2 flex items-center justify-between gap-2 mt-auto">
                        <a href="<?= BASE_URL ?>/classes/<?= $c['id'] ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-800 dark:hover:bg-emerald-950/60 dark:text-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors">
                            <span class="material-symbols-outlined text-[16px]">group</span>
                            <span>Xem Học Sinh</span>
                        </a>

                        <div class="flex items-center gap-1">
                            <?php if (Permission::can('classes.update')): ?>
                            <button onclick="openClassModal('edit', <?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-xl transition-colors" title="Chỉnh sửa lớp & môn học">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <?php endif; ?>
                            <?php if (Permission::can('classes.delete')): ?>
                            <button onclick="openDeleteClassModal(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>', '<?= htmlspecialchars($c['code']) ?>', <?= $curSt ?>)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-xl transition-colors" title="Xóa lớp">
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
                    <span class="material-symbols-outlined text-3xl text-slate-400">door_back</span>
                </div>
                <p class="font-bold text-slate-700 dark:text-slate-300">Không tìm thấy lớp học nào</p>
                <p class="text-xs text-slate-400 mt-1">Thử thay đổi bộ lọc tìm kiếm hoặc tạo thêm lớp học mới.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 4. VIEW 2: DATA TABLE (HIDDEN BY DEFAULT, ACCESSIBLE VIA SWITCH) -->
    <div id="classesTableView" class="hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px] text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="p-3.5 pl-5 w-12 text-center">STT</th>
                        <th class="p-3.5">MÃ LỚP</th>
                        <th class="p-3.5">TÊN LỚP HỌC</th>
                        
                        <th class="p-3.5">PHÒNG HỌC</th>
                        <th class="p-3.5">GIÁO VIÊN CHỦ NHIỆM</th>
                        <th class="p-3.5">MÔN HỌC ÁP DỤNG</th>
                        <th class="p-3.5">SĨ SỐ HIỆN TẠI</th>
                        <th class="p-3.5">TRẠNG THÁI</th>
                        <th class="p-3.5 pr-5 text-right">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-200">
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $idx => $c):
                            $maxSt = (int)($c['max_students'] ?? 45);
                            $curSt = (int)($c['student_count'] ?? 0);
                            $fillPct = $maxSt > 0 ? round(($curSt / max(1, $maxSt)) * 100) : 0;
                            $subCount = (int)($c['subject_count'] ?? 0);
                        ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="p-3.5 pl-5 text-center font-normal text-slate-400"><?= $idx + 1 ?></td>
                            <td class="p-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400"><?= htmlspecialchars($c['code']) ?></td>
                            <td class="p-3.5 font-bold text-slate-900 dark:text-white text-sm"><?= htmlspecialchars($c['name']) ?></td>
                            
                            <td class="p-3.5 text-slate-600 dark:text-slate-300 font-medium"><?= htmlspecialchars($c['room_number'] ?? 'Chưa xếp') ?></td>
                            <td class="p-3.5">
                                <span class="font-semibold <?= !empty($c['homeroom_teacher_name']) ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400 italic' ?>">
                                    <?= htmlspecialchars($c['homeroom_teacher_name'] ?? 'Chưa phân công') ?>
                                </span>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-1.5" title="<?= htmlspecialchars($c['subject_names'] ?? 'Chưa chọn môn') ?>">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        <span class="material-symbols-outlined text-[14px]">menu_book</span>
                                        <span><?= $subCount ?> môn</span>
                                    </span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900 dark:text-white"><?= $curSt ?> / <?= $maxSt ?></span>
                                <span class="text-[11px] text-slate-400 ml-1">(<?= $fillPct ?>%)</span>
                            </td>
                            <td class="p-3.5">
                                <?php if ($c['status'] === 'active'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                                    ● HOẠT ĐỘNG
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                    TẠM NGƯNG
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick="exportClassGradeSheet(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>', '<?= htmlspecialchars($c['code']) ?>')" 
                                            class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 rounded-lg transition-colors" 
                                            title="Tải bảng điểm Excel của lớp này">
                                        <span class="material-symbols-outlined text-[18px]">download</span>
                                    </button>
                                    <a href="<?= BASE_URL ?>/classes/<?= $c['id'] ?>" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 rounded-lg transition-colors" title="Xem học sinh">
                                        <span class="material-symbols-outlined text-[18px]">group</span>
                                    </a>
                                    <?php if (Permission::can('classes.update')): ?>
                                    <button onclick="openClassModal('edit', <?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-lg transition-colors" title="Sửa thông tin & môn học của lớp">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if (Permission::can('classes.delete')): ?>
                                    <button onclick="openDeleteClassModal(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>', '<?= htmlspecialchars($c['code']) ?>', <?= $curSt ?>)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-lg transition-colors" title="Xóa lớp">
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

<!-- Modal: Thêm / Sửa Lớp Học (Kèm Phân Bổ Môn Học & Đếm Số Lượng Môn) -->
<div id="classModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150 max-h-[92vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 md:p-6 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl" id="modalClassIcon">add_circle</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white" id="modalClassTitle">Thêm Lớp Học Mới</h3>
                    <p class="text-xs text-slate-400">Thiết lập thông tin lớp, phân công GVCN và chọn môn học của lớp</p>
                </div>
            </div>
            <button onclick="closeModal('classModal')" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Form Body (Scrollable) -->
        <form id="classForm" onsubmit="handleSaveClass(event)" class="p-5 md:p-6 space-y-4 overflow-y-auto flex-1">
            <input type="hidden" name="id" id="classId" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Mã Lớp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="classCode" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-800 dark:text-slate-100 uppercase focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none"
                           placeholder="VD: 1A, 2B, 3C">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Tên Lớp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="className" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none"
                           placeholder="VD: Lớp 1A, Lớp 2B">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div style="display: none;">
                    <select name="grade_id" id="classGradeId">
                        <?php foreach ($grades as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Niên Khóa <span class="text-rose-500">*</span>
                    </label>
                    <select name="academic_year_id" id="classYearId" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                        <?php foreach ($years as $y): ?>
                        <option value="<?= $y['id'] ?>"><?= htmlspecialchars($y['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Giáo Viên Chủ Nhiệm (GVCN)
                </label>
                <select name="homeroom_teacher_id" id="classTeacherId" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                    <option value="">-- Chưa phân công GVCN --</option>
                    <?php foreach ($teachers as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?> (<?= htmlspecialchars($t['teacher_code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Phòng Học
                    </label>
                    <input type="text" name="room_number" id="classRoomNumber"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none"
                           placeholder="VD: Phòng 101, A203">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Sĩ Số Tối Đa
                    </label>
                    <input type="number" name="max_students" id="classMaxStudents" value="45" min="10" max="60"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Trạng Thái Lớp Học
                </label>
                <select name="status" id="classStatus" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                    <option value="active">Đang hoạt động</option>
                    <option value="inactive">Tạm ngưng hoạt động</option>
                </select>
            </div>

            <!-- PHẦN CHỌN MÔN HỌC ÁP DỤNG & ĐẾM MÔN CHO TỪNG LỚP -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                            Chương Trình Môn Học Của Lớp <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Chọn danh sách môn học của lớp này để khi tải bảng điểm file Excel tự động chia đúng cột môn tương ứng.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="selectedSubjectCountBadge" class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            Đã chọn: <span id="selectedSubjectCount">0</span> môn
                        </span>
                        <button type="button" onclick="toggleAllClassSubjects(true)" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                            Chọn tất cả
                        </button>
                        <span class="text-slate-300 dark:text-slate-700">|</span>
                        <button type="button" onclick="toggleAllClassSubjects(false)" class="text-xs font-bold text-slate-500 hover:text-slate-700 hover:underline">
                            Bỏ chọn
                        </button>
                    </div>
                </div>

                <!-- Subjects Checkbox Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-48 overflow-y-auto p-2.5 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $sb): ?>
                        <label class="subject-checkbox-item relative flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-emerald-400 dark:hover:border-emerald-500 transition-all cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="subject_ids[]" 
                                   value="<?= $sb['id'] ?>" 
                                   data-name="<?= htmlspecialchars($sb['name']) ?>"
                                   data-code="<?= htmlspecialchars($sb['code']) ?>"
                                   onchange="updateSubjectSelectionCounter()"
                                   class="class-subject-checkbox rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300 dark:border-slate-600 cursor-pointer">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">
                                    <?= htmlspecialchars($sb['name']) ?>
                                </div>
                                <div class="text-[10px] font-mono text-slate-400 dark:text-slate-500 font-semibold">
                                    <?= htmlspecialchars($sb['code']) ?>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full py-4 text-center text-xs text-slate-400">
                            Chưa có môn học nào trong hệ thống. Vui lòng thêm môn học trước.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800 shrink-0">
                <button type="button" onclick="closeModal('classModal')" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-50 transition-colors">
                    Hủy Bỏ
                </button>
                <button type="submit" id="btnSubmitClass" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span id="btnSubmitClassText">Lưu Lớp Học</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Xác Nhận Xóa Lớp Học (Chuẩn Theo Mẫu Cảnh Báo Đỏ) -->
<div id="deleteClassConfirmModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden text-center transform transition-all scale-100 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Header: Nền hồng đỏ nhạt có tam giác cảnh báo ở giữa -->
        <div class="bg-[#feecee] dark:bg-rose-950/40 border-b border-rose-100 dark:border-rose-900/50 pt-7 pb-6 px-6">
            <div class="w-12 h-12 rounded-full bg-rose-200/80 dark:bg-rose-900/70 text-rose-700 dark:text-rose-300 flex items-center justify-center mx-auto mb-3 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-rose-700 dark:text-rose-300">
                Xác nhận xóa lớp học?
            </h3>
        </div>

        <!-- Body: Nền trắng có đoạn văn cảnh báo và thẻ lớp học -->
        <div class="p-6 sm:p-7 bg-white dark:bg-slate-900 space-y-6">
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed text-center">
                Hành động này không thể hoàn tác. Lớp học chỉ có thể xóa khi không còn học sinh nào đang theo học.
            </p>

            <!-- Khung thông tin lớp học -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-4 bg-white dark:bg-slate-800/50 flex items-center gap-4 text-left shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 dark:border-rose-900/40">
                    <span class="material-symbols-outlined text-2xl">door_front</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span id="modalDeleteClassName" class="font-bold text-slate-900 dark:text-white text-base truncate">Lớp 1A</span>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            Mã: <span id="modalDeleteClassCode">1A</span>
                        </span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                            Sĩ số: <span id="modalDeleteClassStudents">0</span> học sinh
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden ID -->
        <input type="hidden" id="modalDeleteClassId" value="">

        <!-- Footer: Nút [Hủy bỏ] và [Xác nhận xóa] -->
        <div class="p-4 sm:p-5 px-7 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('deleteClassConfirmModal')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition-all shadow-sm">
                Hủy bỏ
            </button>
            <button type="button" id="btnExecuteDeleteClass" onclick="executeDeleteClass()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#b91c1c] hover:bg-[#991b1b] active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>Xác nhận xóa</span>
            </button>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    const ALL_CLASSES = <?= json_encode($allClassesForExport ?? $classes ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // =========================================================================
    // CHUYỂN ĐỔI CHẾ ĐỘ XEM (VIEW SWITCH: GRID / TABLE)
    // =========================================================================
    function switchClassView(mode) {
        const gridView = document.getElementById('classesGridView');
        const tableView = document.getElementById('classesTableView');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnTable = document.getElementById('btnViewTable');

        if (mode === 'table') {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');

            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm';
            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('class_view_mode', 'table');
        } else {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');

            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm';
            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-500 hover:text-slate-900 dark:hover:text-white';
            localStorage.setItem('class_view_mode', 'grid');
        }
    }

    // Auto restore previous view mode
    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('class_view_mode');
        if (savedMode === 'table') switchClassView('table');
    });

    // =========================================================================
    // QUẢN LÝ CHỌN MÔN HỌC TRONG MODAL
    // =========================================================================
    function updateSubjectSelectionCounter() {
        const checkboxes = document.querySelectorAll('.class-subject-checkbox');
        let count = 0;
        checkboxes.forEach(cb => {
            const card = cb.closest('.subject-checkbox-item');
            if (cb.checked) {
                count++;
                if (card) {
                    card.classList.add('border-emerald-500', 'bg-emerald-50/40', 'dark:bg-emerald-950/20');
                    card.classList.remove('border-slate-200', 'dark:border-slate-700');
                }
            } else {
                if (card) {
                    card.classList.remove('border-emerald-500', 'bg-emerald-50/40', 'dark:bg-emerald-950/20');
                    card.classList.add('border-slate-200', 'dark:border-slate-700');
                }
            }
        });

        const countEl = document.getElementById('selectedSubjectCount');
        if (countEl) countEl.textContent = count;
    }

    function toggleAllClassSubjects(shouldCheck) {
        const checkboxes = document.querySelectorAll('.class-subject-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = Boolean(shouldCheck);
        });
        updateSubjectSelectionCounter();
    }

    // =========================================================================
    // MODAL THÊM / SỬA LỚP HỌC
    // =========================================================================
    function openClassModal(mode, classData = null) {
        const isEdit = mode === 'edit';
        document.getElementById('modalClassTitle').textContent = isEdit ? 'Cập Nhật Lớp Học & Môn Học' : 'Thêm Lớp Học Mới';
        document.getElementById('modalClassIcon').textContent = isEdit ? 'edit_square' : 'add_circle';
        document.getElementById('btnSubmitClassText').textContent = isEdit ? 'Lưu Thay Đổi' : 'Tạo Lớp Học';

        const allCheckboxes = document.querySelectorAll('.class-subject-checkbox');

        if (isEdit && classData) {
            document.getElementById('classId').value = classData.id;
            document.getElementById('classCode').value = classData.code;
            document.getElementById('classCode').readOnly = true;
            document.getElementById('className').value = classData.name;
            document.getElementById('classGradeId').value = classData.grade_id;
            document.getElementById('classYearId').value = classData.academic_year_id;
            document.getElementById('classTeacherId').value = classData.homeroom_teacher_id || '';
            document.getElementById('classRoomNumber').value = classData.room_number || '';
            document.getElementById('classMaxStudents').value = classData.max_students || 45;
            document.getElementById('classStatus').value = classData.status || 'active';

            // Pre-select subjects
            const subIds = (classData.subject_ids || '').toString().split(',').map(s => s.trim()).filter(Boolean);
            allCheckboxes.forEach(cb => {
                cb.checked = subIds.includes(cb.value.toString());
            });
        } else {
            document.getElementById('classId').value = '';
            document.getElementById('classCode').value = '';
            document.getElementById('classCode').readOnly = false;
            document.getElementById('className').value = '';
            document.getElementById('classTeacherId').value = '';
            document.getElementById('classRoomNumber').value = '';
            document.getElementById('classMaxStudents').value = 45;
            document.getElementById('classStatus').value = 'active';

            // Default: check all subjects for new class
            allCheckboxes.forEach(cb => {
                cb.checked = true;
            });
        }

        updateSubjectSelectionCounter();
        openModal('classModal');
    }

    async function handleSaveClass(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitClass');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Collect all checked subjects as array
        const checkedSubjectIds = Array.from(form.querySelectorAll('.class-subject-checkbox:checked')).map(cb => cb.value);
        data.subject_ids = checkedSubjectIds;

        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang lưu...</span>`;

        const isEdit = Boolean(data.id);
        const url = isEdit ? `<?= BASE_URL ?>/classes/${data.id}` : '<?= BASE_URL ?>/classes';

        try {
            const res = await apiPost(url, data);
            if (res) {
                closeModal('classModal');
                showToast(isEdit ? 'Đã cập nhật lớp học & môn học thành công!' : 'Đã tạo lớp học và gán môn thành công!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span>${isEdit ? 'Lưu Thay Đổi' : 'Tạo Lớp Học'}</span>`;
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span>${isEdit ? 'Lưu Thay Đổi' : 'Tạo Lớp Học'}</span>`;
        }
    }

    // =========================================================================
    // MODAL XÁC NHẬN XÓA LỚP HỌC
    // =========================================================================
    function openDeleteClassModal(id, name, code, studentCount) {
        if (studentCount > 0) {
            showToast(`Không thể xóa "${name}" vì lớp đang có ${studentCount} học sinh theo học. Hãy chuyển hoặc rút học sinh trước!`, 'warning');
            return;
        }

        document.getElementById('modalDeleteClassId').value = id;
        document.getElementById('modalDeleteClassName').textContent = name || '';
        document.getElementById('modalDeleteClassCode').textContent = code || '';
        document.getElementById('modalDeleteClassStudents').textContent = studentCount || 0;

        openModal('deleteClassConfirmModal');
    }

    async function executeDeleteClass() {
        const id = document.getElementById('modalDeleteClassId').value;
        if (!id) return;

        const btn = document.getElementById('btnExecuteDeleteClass');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang xóa...</span>`;

        try {
            const res = await apiPost(`<?= BASE_URL ?>/classes/${id}`, { _method: 'DELETE' });
            if (res) {
                closeModal('deleteClassConfirmModal');
                showToast('Đã xóa lớp học thành công!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            }
        } catch (e) {
            console.error(e);
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">delete</span><span>Xác nhận xóa</span>`;
            showToast('Không thể xóa lớp học, vui lòng thử lại sau!', 'error');
        }
    }

    // =========================================================================
    // XUẤT BẢNG ĐIỂM RIÊNG THEO DANH SÁCH MÔN CỦA TỪNG LỚP HỌC (CUSTOM EXPORT)
    // =========================================================================
    async function exportClassGradeSheet(classId, className, classCode) {
        try {
            showToast(`Đang tải dữ liệu lớp ${className}...`, 'info');
            const res = await fetch(`<?= BASE_URL ?>/classes/${classId}?format=json`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            
            const cls = data.class || {};
            const students = data.students || [];
            const subjects = data.subjects || [];

            if (subjects.length === 0) {
                showToast(`Lớp "${className}" hiện chưa có môn học nào. Vui lòng bấm sửa lớp để chọn môn!`, 'warning');
                return;
            }

            const today = new Date();
            const dateStr = today.toLocaleDateString('vi-VN');
            const fileDate = today.toISOString().slice(0, 10);

            // Xây dựng Header file Excel
            const rows = [
                ["SỞ GIÁO DỤC VÀ ĐÀO TẠO TP. HỒ CHÍ MINH", "", "", "", "CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM"],
                ["TRƯỜNG TIỂU HỌC & THCS EDUMANAGE", "", "", "", "Độc lập - Tự do - Hạnh phúc"],
                ["BỘ PHẬN ĐÀO TẠO & HỌC VỤ", "", "", "", "-------------------------"],
                [""],
                [`BẢNG THEO DÕI ĐIỂM SỐ & KẾT QUẢ HỌC TẬP - ${className.toUpperCase()}`],
                [`Mã lớp: ${classCode}   |   Sĩ số: ${students.length} học sinh   |   Tổng số môn học: ${subjects.length} môn   |   Ngày xuất: ${dateStr}`],
                [""]
            ];

            // Dòng tiêu đề cột: Cột cố định + Từng cột môn học riêng của lớp + Cột tổng kết
            const tableHeaders = [
                "STT",
                "Mã Học Sinh",
                "Họ và Tên Học Sinh",
                "Giới Tính",
                "Ngày Sinh"
            ];

            // Đưa chính xác các môn học của lớp này vào từng cột riêng
            subjects.forEach(sb => {
                tableHeaders.push(`${sb.name} (${sb.code})`);
            });

            // Cột tổng kết
            tableHeaders.push("Điểm TB", "Xếp Loại", "Ghi Chú");
            rows.push(tableHeaders);

            // Điền dữ liệu học sinh
            if (students.length > 0) {
                students.forEach((s, idx) => {
                    const row = [
                        idx + 1,
                        s.student_code || '',
                        s.full_name || '',
                        s.gender === 'female' ? 'Nữ' : 'Nam',
                        s.dob ? s.dob.split('-').reverse().join('/') : ''
                    ];

                    // Ô điểm trống để giáo viên dễ dàng nhập hoặc theo dõi cho từng môn của lớp
                    subjects.forEach(() => {
                        row.push(''); 
                    });

                    row.push('', '', ''); // Điểm TB, Xếp Loại, Ghi Chú
                    rows.push(row);
                });
            } else {
                const emptyRow = [1, "—", "Chưa có học sinh trong lớp này", "—", "—"];
                subjects.forEach(() => emptyRow.push(''));
                emptyRow.push('', '', '');
                rows.push(emptyRow);
            }

            // Chữ ký cuối trang
            rows.push([""]);
            rows.push(["", "", "", "", `Ngày ${today.getDate()} tháng ${today.getMonth() + 1} năm ${today.getFullYear()}`]);
            rows.push(["", "NGƯỜI LẬP BẢNG", "", "", "GIÁO VIÊN CHỦ NHIỆM", "", "HIỆU TRƯỞNG"]);
            rows.push(["", "(Ký và ghi rõ họ tên)", "", "", "(Ký và ghi rõ họ tên)", "", "(Ký tên và đóng dấu)"]);

            const ws = XLSX.utils.aoa_to_sheet(rows);

            // Canh chỉnh độ rộng cột
            const colWidths = [
                { wch: 6 },  // STT
                { wch: 14 }, // Mã HS
                { wch: 25 }, // Họ Tên
                { wch: 10 }, // Giới tính
                { wch: 14 }  // Ngày sinh
            ];
            subjects.forEach(() => colWidths.push({ wch: 16 })); // Từng môn học
            colWidths.push({ wch: 12 }, { wch: 14 }, { wch: 20 }); // Điểm TB, Xếp loại, Ghi chú
            ws['!cols'] = colWidths;

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, `Diem_${classCode}`);
            XLSX.writeFile(wb, `Bang_Diem_Lop_${classCode}_(${subjects.length}_Mon)_${fileDate}.xlsx`);
            showToast(`Đã tải file Excel bảng điểm lớp ${className} với ${subjects.length} môn học!`, 'success');
        } catch (e) {
            console.error(e);
            showToast('Lỗi khi tải bảng điểm của lớp, vui lòng thử lại!', 'error');
        }
    }

    // =========================================================================
    // XUẤT EXCEL TỔNG HỢP TOÀN BỘ DANH SÁCH LỚP HỌC
    // =========================================================================
    function exportClassesExcel() {
        const classesToExport = (typeof ALL_CLASSES !== 'undefined' && ALL_CLASSES.length > 0) 
            ? ALL_CLASSES 
            : [];

        if (!classesToExport || classesToExport.length === 0) {
            showToast('Không có dữ liệu lớp học để xuất!', 'warning');
            return;
        }

        const today = new Date();
        const dateStr = today.toLocaleDateString('vi-VN');
        const fileDate = today.toISOString().slice(0, 10);

        const rows = [
            ["SỞ GIÁO DỤC VÀ ĐÀO TẠO TP. HỒ CHÍ MINH", "", "", "", "CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM"],
            ["TRƯỜNG TIỂU HỌC & THCS EDUMANAGE", "", "", "", "Độc lập - Tự do - Hạnh phúc"],
            ["BỘ PHẬN QUẢN LÝ HỌC VỤ & LỚP HỌC", "", "", "", "-------------------------"],
            [""],
            ["DANH SÁCH THỐNG KÊ LỚP HỌC & CHƯƠNG TRÌNH MÔN HỌC"],
            [`Năm học: 2025 - 2026   |   Ngày xuất báo cáo: ${dateStr}   |   Tổng số lớp: ${classesToExport.length}`],
            [""],
            [
                "STT",
                "Mã Lớp",
                "Tên Lớp Học",
                
                "Phòng Học",
                "Giáo Viên Chủ Nhiệm",
                "Số Lượng Môn Học",
                "Danh Sách Môn Học Áp Dụng",
                "Sĩ Số Hiện Tại",
                "Sĩ Số Tối Đa",
                "Tỷ Lệ Lấp Đầy (%)",
                "Trạng Thái"
            ]
        ];

        classesToExport.forEach((c, idx) => {
            const maxSt = Number(c.max_students || 45);
            const curSt = Number(c.student_count || 0);
            const pct = maxSt > 0 ? Math.round((curSt / maxSt) * 100) : 0;
            const subCount = Number(c.subject_count || 0);

            rows.push([
                idx + 1,
                c.code || '',
                c.name || '',
                
                c.room_number || 'Chưa xếp',
                c.homeroom_teacher_name || 'Chưa phân công',
                `${subCount} môn`,
                c.subject_names || 'Chưa gán môn',
                curSt,
                maxSt,
                `${pct}%`,
                c.status === 'active' ? 'Đang hoạt động' : 'Tạm ngưng'
            ]);
        });

        rows.push([""]);
        rows.push(["", "", "", "", "", "", "", `Ngày ${today.getDate()} tháng ${today.getMonth() + 1} năm ${today.getFullYear()}`]);
        rows.push(["", "NGƯỜI LẬP BIỂU", "", "", "", "", "", "HIỆU TRƯỞNG / BAN GIÁM HIỆU"]);
        rows.push(["", "(Ký và ghi rõ họ tên)", "", "", "", "", "", "(Ký tên và đóng dấu)"]);

        const ws = XLSX.utils.aoa_to_sheet(rows);
        ws['!cols'] = [
            { wch: 6 },
            { wch: 12 },
            { wch: 18 },
            { wch: 14 },
            { wch: 14 },
            { wch: 25 },
            { wch: 16 },
            { wch: 35 },
            { wch: 16 },
            { wch: 16 },
            { wch: 18 },
            { wch: 16 }
        ];

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "DanhSachLopHoc");
        XLSX.writeFile(wb, `Danh_Sach_Lop_Hoc_EduManage_${fileDate}.xlsx`);
        showToast(`Đã xuất thành công toàn bộ ${classesToExport.length} lớp học kèm thông tin môn học!`, 'success');
    }
</script>
