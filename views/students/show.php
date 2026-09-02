<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="<?= BASE_URL ?>/dashboard" class="hover:text-emerald-600">Quản lý</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="<?= BASE_URL ?>/students" class="hover:text-emerald-600">Học sinh</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-emerald-600 font-bold">Hồ sơ chi tiết</span>
        </div>
        <a href="<?= BASE_URL ?>/students" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Danh sách</span>
        </a>
    </div>

    <!-- Student Header Hero Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 flex flex-col md:flex-row gap-6 items-start relative overflow-hidden shadow-sm">
        <!-- Decorative background accent -->
        <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/5 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>

        <!-- Avatar -->
        <div class="w-24 h-24 md:w-32 md:h-32 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex-shrink-0 overflow-hidden shadow-inner">
            <img src="<?= htmlspecialchars($student['avatar'] ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=300') ?>" alt="Student Photo" class="w-full h-full object-cover">
        </div>

        <!-- Core Info & Quick Stats -->
        <div class="flex-1 flex flex-col gap-4 z-10 w-full">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white"><?= htmlspecialchars($student['full_name']) ?></h2>
                        <?php if (!empty($student['khmer_name'])): ?>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 font-mono">
                            <?= htmlspecialchars($student['khmer_name']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs md:text-sm text-slate-500 mt-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">badge</span>
                        <span>Mã HS: <strong class="text-emerald-600 font-mono"><?= htmlspecialchars($student['student_code']) ?></strong></span>
                        <span>•</span>
                        <span>Lớp: <strong class="text-slate-800 dark:text-slate-200"><?= htmlspecialchars($student['class_name'] ?? 'Chưa xếp lớp') ?></strong> (<?= htmlspecialchars($student['grade_name'] ?? '') ?>)</span>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <?php if ($student['status'] === 'studying'): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Đang học
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border text-xs font-bold uppercase">
                        <?= htmlspecialchars($student['status']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-2">
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-3.5 border border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">ĐTB Tích Lũy</p>
                    <p class="text-xl font-black text-emerald-600"><?= $student['avg_score'] ?? '—' ?> <span class="text-xs font-medium text-slate-400">/ 10</span></p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-3.5 border border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Môn Học</p>
                    <p class="text-xl font-black text-blue-600"><?= count(array_unique(array_column($student['grades'] ?? [], 'subject_name'))) ?> <span class="text-xs font-medium text-slate-400">môn</span></p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-3.5 border border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bài Tập</p>
                    <p class="text-xl font-black text-purple-600"><?= count($student['assignments'] ?? []) ?> <span class="text-xs font-normal text-slate-400">bài</span></p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-3.5 border border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Hạnh Kiểm</p>
                    <p class="text-xl font-black text-teal-600"><?= $student['conduct'] ?? 'Tốt' ?></p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-row md:flex-col gap-2 w-full md:w-auto z-10">
            <?php if (Permission::can('students.update')): ?>
            <a href="<?= BASE_URL ?>/students/<?= $student['id'] ?>/edit" class="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                <span>Sửa Hồ Sơ</span>
            </a>
            <?php endif; ?>
            <button onclick="window.print()" class="flex-1 md:flex-none border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 px-4 py-2.5 rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>In Hồ Sơ</span>
            </button>
        </div>
    </div>

    <!-- Tabs Navigation Bar -->
    <div class="border-b border-slate-200 dark:border-slate-800 overflow-x-auto bg-white dark:bg-slate-900 rounded-2xl px-4 shadow-sm">
        <nav class="flex gap-4 min-w-max text-xs font-bold" id="profileTabs">
            <button onclick="switchTab('overview')" id="tab-overview" class="tab-btn py-3.5 px-3 border-b-2 border-emerald-600 text-emerald-600 flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px]">dashboard</span>
                <span>Tổng quan</span>
            </button>
            <button onclick="switchTab('personal')" id="tab-personal" class="tab-btn py-3.5 px-3 border-b-2 border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px]">person</span>
                <span>Thông tin cá nhân</span>
            </button>
            <button onclick="switchTab('academic')" id="tab-academic" class="tab-btn py-3.5 px-3 border-b-2 border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px]">history_edu</span>
                <span>Thông tin học tập</span>
            </button>
            <button onclick="switchTab('grades')" id="tab-grades" class="tab-btn py-3.5 px-3 border-b-2 border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px]">grade</span>
                <span>Kết quả điểm số</span>
            </button>
            <button onclick="switchTab('assignments')" id="tab-assignments" class="tab-btn py-3.5 px-3 border-b-2 border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px]">task</span>
                <span>Bài tập & Nộp bài (<?= count($student['assignments'] ?? []) ?>)</span>
            </button>
        </nav>
    </div>

    <!-- TAB 1: TỔNG QUAN -->
    <div id="content-overview" class="tab-pane grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Grades Preview Table -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Bảng Điểm Môn Học Tóm Tắt</h3>
                    <button onclick="switchTab('grades')" class="text-xs text-emerald-600 font-bold hover:underline">Xem chi tiết đầy đủ &rarr;</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                <th class="p-3">Môn Học</th>
                                <th class="p-3">Hệ số</th>
                                <th class="p-3">Thành phần</th>
                                <th class="p-3 text-center">Điểm số</th>
                                <th class="p-3 text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                            <?php if (!empty($student['grades'])): ?>
                                <?php foreach (array_slice($student['grades'], 0, 8) as $gr): ?>
                                <tr>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($gr['subject_name']) ?></td>
                                    <td class="p-3 font-mono text-slate-500">x<?= $gr['coefficient'] ?></td>
                                    <td class="p-3 text-slate-500"><?= htmlspecialchars($gr['component_name']) ?></td>
                                    <td class="p-3 text-center font-mono font-bold text-sm <?= ($gr['score'] ?? 0) >= 8 ? 'text-emerald-600' : (($gr['score'] ?? 0) >= 5 ? 'text-blue-600' : 'text-rose-600') ?>">
                                        <?= $gr['score'] !== null ? number_format($gr['score'], 1) : '—' ?>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $gr['is_draft'] ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' ?>">
                                            <?= $gr['is_draft'] ? 'Nháp' : 'Chính thức' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="p-6 text-center text-slate-400">Chưa có bản ghi điểm nào</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Class & Homeroom Info -->
        <div class="flex flex-col gap-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Thông Tin Lớp & GVCN</h3>
                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Lớp Học Chính Khóa</span>
                        <span class="font-extrabold text-slate-900 dark:text-white text-sm mt-0.5 block"><?= htmlspecialchars($student['class_name'] ?? '—') ?></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Giáo Viên Chủ Nhiệm</span>
                        <span class="font-extrabold text-emerald-600 text-sm mt-0.5 block"><?= htmlspecialchars($student['homeroom_teacher_name'] ?? 'Chưa phân công') ?></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Phòng Học / Niên Khóa</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300 mt-0.5 block">Phòng <?= htmlspecialchars($student['room_number'] ?? 'A1') ?> | <?= htmlspecialchars($student['year_name'] ?? '2025-2026') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: THÔNG TIN CÁ NHÂN -->
    <div id="content-personal" class="tab-pane hidden bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-6">Thông Tin Cá Nhân & Gia Đình</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
            <div>
                <label class="text-slate-400 font-bold block mb-1">Họ và Tên</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-extrabold text-slate-900 dark:text-white text-sm"><?= htmlspecialchars($student['full_name']) ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Tên Tiếng Khmer</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-bold text-purple-600 text-sm"><?= htmlspecialchars($student['khmer_name'] ?? '—') ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Giới Tính</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-bold"><?= $student['gender'] === 'female' ? 'Nữ' : 'Nam' ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Ngày Sinh</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-mono font-bold"><?= date('d/m/Y', strtotime($student['dob'])) ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Nơi Sinh</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl"><?= htmlspecialchars($student['pob'] ?? '—') ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Số Điện Thoại</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-mono"><?= htmlspecialchars($student['phone'] ?? '—') ?></div>
            </div>
            <div class="md:col-span-2">
                <label class="text-slate-400 font-bold block mb-1">Địa Chỉ Thường Trú</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl"><?= htmlspecialchars($student['address'] ?? '—') ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Email</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl"><?= htmlspecialchars($student['email'] ?? '—') ?></div>
            </div>
        </div>
    </div>

    <!-- TAB 3: THÔNG TIN HỌC TẬP -->
    <div id="content-academic" class="tab-pane hidden bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-6">Thông Tin Lớp & Quản Lý Đào Tạo</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
            <div>
                <label class="text-slate-400 font-bold block mb-1">Mã Học Sinh</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-mono font-black text-emerald-600 text-sm"><?= htmlspecialchars($student['student_code']) ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Khối Lớp</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-bold"><?= htmlspecialchars($student['grade_name'] ?? '—') ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Lớp Học</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($student['class_name'] ?? '—') ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Năm Học Hiện Tại</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl"><?= htmlspecialchars($student['year_name'] ?? '—') ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Ngày Nhập Học</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-mono"><?= !empty($student['admission_date']) ? date('d/m/Y', strtotime($student['admission_date'])) : '—' ?></div>
            </div>
            <div>
                <label class="text-slate-400 font-bold block mb-1">Trạng Thái Học Tập</label>
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl font-bold text-emerald-600"><?= htmlspecialchars($student['status']) ?></div>
            </div>
        </div>
    </div>

    <!-- TAB 4: KẾT QUẢ ĐIỂM SỐ -->
    <div id="content-grades" class="tab-pane hidden bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Bảng Điểm Chi Tiết Các Môn Học</h3>
                <p class="text-xs text-slate-400 mt-0.5">Điểm các cột đánh giá thường xuyên, định kỳ và kiểm tra học kỳ</p>
            </div>
            <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3">
                <span class="text-xs text-slate-600 dark:text-slate-300 font-bold">Điểm Trung Bình (GPA):</span>
                <span class="font-mono font-black text-emerald-600 text-lg"><?= $student['avg_score'] ?? '—' ?></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                        <th class="p-4 w-12 text-center">STT</th>
                        <th class="p-4">Môn Học</th>
                        <th class="p-4">Hệ Số Môn</th>
                        <th class="p-4">Thành Phần Đánh Giá</th>
                        <th class="p-4 text-center">Điểm Số (Thang 10)</th>
                        <th class="p-4 text-center">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    <?php if (!empty($student['grades'])): ?>
                        <?php foreach ($student['grades'] as $idx => $gr): ?>
                        <tr>
                            <td class="p-4 text-center text-slate-400"><?= $idx + 1 ?></td>
                            <td class="p-4 font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($gr['subject_name']) ?></td>
                            <td class="p-4 font-mono text-slate-500">x<?= $gr['coefficient'] ?></td>
                            <td class="p-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars($gr['component_name']) ?></td>
                            <td class="p-4 text-center font-mono font-black text-sm <?= ($gr['score'] ?? 0) >= 8 ? 'text-emerald-600' : (($gr['score'] ?? 0) >= 5 ? 'text-blue-600' : 'text-rose-600') ?>">
                                <?= $gr['score'] !== null ? number_format($gr['score'], 1) : '—' ?>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $gr['is_draft'] ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' ?>">
                                    <?= $gr['is_draft'] ? 'Bản nháp' : 'Chính thức' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="p-8 text-center text-slate-400">Chưa có dữ liệu điểm môn học</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 5: BÀI TẬP & NỘP BÀI -->
    <div id="content-assignments" class="tab-pane hidden bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Danh Sách Bài Tập Của Lớp</h3>
        
        <div class="space-y-4">
            <?php if (!empty($student['assignments'])): ?>
                <?php foreach ($student['assignments'] as $asg): ?>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-mono"><?= htmlspecialchars($asg['subject_name']) ?></span>
                            <span class="text-xs text-slate-400">GV: <?= htmlspecialchars($asg['teacher_name']) ?></span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white mt-1"><?= htmlspecialchars($asg['title']) ?></h4>
                        <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($asg['description'] ?? '') ?></p>
                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">Hạn nộp: <?= date('d/m/Y H:i', strtotime($asg['due_date'])) ?></p>
                    </div>

                    <div class="flex items-center gap-4">
                        <?php if (!empty($asg['submission_status'])): ?>
                            <div class="text-right">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    <?= $asg['submission_status'] === 'graded' ? 'Đã chấm điểm' : 'Đã nộp bài' ?>
                                </span>
                                <?php if ($asg['submission_score'] !== null): ?>
                                <p class="text-xs font-mono font-black text-emerald-600 mt-1">Điểm: <?= $asg['submission_score'] ?> / <?= $asg['max_score'] ?></p>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                                Chưa nộp bài
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-slate-400 py-8 text-xs">Hiện tại chưa có bài tập nào được giao cho lớp này.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-emerald-600', 'text-emerald-600');
            btn.classList.add('border-transparent', 'text-slate-500');
        });
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
        });

        const activeBtn = document.getElementById('tab-' + tabId);
        const activePane = document.getElementById('content-' + tabId);

        if (activeBtn && activePane) {
            activeBtn.classList.add('border-emerald-600', 'text-emerald-600');
            activeBtn.classList.remove('border-transparent', 'text-slate-500');
            activePane.classList.remove('hidden');
        }
    }
</script>
