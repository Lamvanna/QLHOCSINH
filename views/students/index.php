<div class="space-y-6">
    <!-- Page Header & Main Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 mb-1">
                <span class="material-symbols-outlined text-sm">school</span>
                <span>Quản Lý Học Vụ</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Danh Sách Học Sinh</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-0.5">Quản lý hồ sơ tuyển sinh, theo dõi thông tin cá nhân và tình trạng học tập.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="exportCustomExcel()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-all shadow-sm group" title="Xuất file Excel chuyên nghiệp có thông tin tiêu đề trường học">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">table_chart</span>
                <span>Xuất Excel</span>
            </button>
            <button onclick="openModal('importModal')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 border border-blue-200 dark:border-blue-800/80 rounded-xl text-xs font-bold text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-all shadow-sm group" title="Nhập danh sách học sinh từ file Excel hoặc tải file mẫu">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">upload_file</span>
                <span>Nhập Excel</span>
            </button>
            <?php if (Permission::can('students.create')): ?>
            <a href="<?= BASE_URL ?>/students/create" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Thêm Học Sinh Mới</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Data Table Container (Card Style) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col">
        
        <!-- Toolbar: Filters & Bulk Actions -->
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-slate-50/70 dark:bg-slate-800/40">
            <!-- Search & Filter Cluster -->
            <form method="GET" action="<?= BASE_URL ?>/students" id="filterForm" class="flex flex-wrap items-center gap-2 w-full xl:w-auto">
                <!-- Local Search -->
                <div class="relative flex items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 focus-within:ring-2 focus-within:ring-emerald-500 w-full sm:w-64 transition-all">
                    <span class="material-symbols-outlined text-slate-400 mr-2 text-[18px]">search</span>
                    <input name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="bg-transparent border-none outline-none w-full text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:ring-0 p-0" placeholder="Tìm Mã HS, Họ tên, SĐT..." type="text"/>
                </div>

                <!-- Class Filter -->
                <select name="class_id" onchange="document.getElementById('filterForm').submit()" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Lớp</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($filters['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <!-- Status Filter -->
                <select name="status" onchange="document.getElementById('filterForm').submit()" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Tất cả Trạng thái</option>
                    <option value="studying" <?= ($filters['status'] ?? '') === 'studying' ? 'selected' : '' ?>>Đang học</option>
                    <option value="transferred" <?= ($filters['status'] ?? '') === 'transferred' ? 'selected' : '' ?>>Chuyển trường</option>
                    <option value="graduated" <?= ($filters['status'] ?? '') === 'graduated' ? 'selected' : '' ?>>Đã tốt nghiệp</option>
                </select>

                <a href="<?= BASE_URL ?>/students" class="inline-flex items-center gap-1 px-2 py-1.5 text-emerald-600 dark:text-emerald-400 text-xs font-semibold hover:underline bg-transparent">
                    <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                    <span>Xóa lọc</span>
                </a>
            </form>

            <!-- Bulk Actions Cluster -->
            <div class="flex items-center gap-2 w-full xl:w-auto justify-end">
                <span id="selectedCountBadge" class="text-xs text-slate-400 hidden">0 đã chọn</span>
                <div class="flex items-center gap-1">
                    <a href="<?= BASE_URL ?>/students/print<?= !empty($filters['class_id']) ? '?class_id=' . $filters['class_id'] . '&auto=1' : '?auto=1' ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-emerald-500/20" title="In danh sách học sinh chuẩn A4">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        <span>In Danh Sách <?= !empty($filters['class_id']) ? 'Lớp' : '' ?></span>
                    </a>
                    <button onclick="openModal('printClassSelectModal')" class="p-1.5 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 transition-colors" title="Chọn lớp để in">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    </button>
                    <button onclick="exportCustomExcel()" class="p-1.5 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 transition-colors" title="Xuất Excel">
                        <span class="material-symbols-outlined text-[18px]">file_download</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[950px] text-xs" id="studentsTable">
                <thead>
                    <tr class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="p-3.5 pl-5 w-10 text-center">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="p-3.5 w-12 text-center whitespace-nowrap">STT</th>
                        <th class="p-3.5 whitespace-nowrap">Mã Học Sinh</th>
                        <th class="p-3.5">Thông Tin Học Sinh</th>
                        <th class="p-3.5">Giới Tính / Ngày Sinh</th>
                        <th class="p-3.5">Lớp Học</th>
                        <th class="p-3.5">Niên Khóa</th>
                        <th class="p-3.5">Trạng Thái</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-200">
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $idx => $s): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="p-3.5 pl-5 text-center">
                                <input type="checkbox" class="student-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer" value="<?= $s['id'] ?>" onchange="updateSelectedCount()">
                            </td>
                            <?php $stt = ($pagination['page'] - 1) * $pagination['limit'] + $idx + 1; ?>
                            <td class="p-3.5 text-center font-mono font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap"><?= $stt ?></td>
                            <td class="p-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                <?= htmlspecialchars($s['student_code']) ?>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0">
                                        <img src="<?= htmlspecialchars($s['avatar'] ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150') ?>" class="w-full h-full object-cover" alt="Avatar">
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors truncate">
                                            <?= htmlspecialchars($s['full_name']) ?>
                                        </a>
                                        <span class="text-[11px] text-slate-400 truncate"><?= htmlspecialchars($s['email'] ?? 'hs@edumanage.edu.vn') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div class="flex flex-col">
                                    <span class="font-semibold <?= $s['gender'] === 'female' ? 'text-rose-600 dark:text-rose-400' : 'text-blue-600 dark:text-blue-400' ?>">
                                        <?= $s['gender'] === 'female' ? 'Nữ' : 'Nam' ?>
                                    </span>
                                    <span class="text-[11px] text-slate-400"><?= date('d/m/Y', strtotime($s['dob'])) ?></span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($s['class_name'] ?? 'Chưa phân lớp') ?></span>
                            </td>
                            <td class="p-3.5 text-slate-500 font-mono">
                                <?= htmlspecialchars($s['year_name'] ?? '2025–2026') ?>
                            </td>
                            <td class="p-3.5">
                                <?php if ($s['status'] === 'studying'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-semibold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                    Đang học
                                </span>
                                <?php elseif ($s['status'] === 'transferred'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300 text-[11px] font-semibold uppercase tracking-wider border border-blue-200 dark:border-blue-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                    Chuyển trường
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-[11px] font-semibold uppercase tracking-wider border border-slate-300 dark:border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                    <?= htmlspecialchars($s['status']) ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="flex justify-end items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>" class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 rounded-lg transition-colors" title="Xem Hồ Sơ Chi Tiết">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <?php if (Permission::can('students.update')): ?>
                                    <a href="<?= BASE_URL ?>/students/<?= $s['id'] ?>/edit" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-lg transition-colors" title="Chỉnh Sửa">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (Permission::can('students.delete')): ?>
                                    <button onclick="deleteStudent(<?= $s['id'] ?>)" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 rounded-lg transition-colors" title="Xóa Lưu Trữ">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600 mb-2 block">folder_off</span>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Không tìm thấy học sinh nào</p>
                                <p class="text-xs text-slate-400 mt-0.5">Hãy thử điều chỉnh lại bộ lọc hoặc từ khóa tìm kiếm.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-slate-500">
            <div>
                Hiển thị <span class="font-bold text-slate-900 dark:text-white"><?= ($pagination['page'] - 1) * $pagination['limit'] + 1 ?> – <?= min($pagination['total'], $pagination['page'] * $pagination['limit']) ?></span> trên tổng số <span class="font-bold text-slate-900 dark:text-white"><?= $pagination['total'] ?></span> học sinh
            </div>
            
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1">
                    <?php if ($pagination['page'] > 1): ?>
                    <a href="<?= BASE_URL ?>/students?page=<?= $pagination['page'] - 1 ?>&class_id=<?= $filters['class_id'] ?? '' ?>&grade_id=<?= $filters['grade_id'] ?? '' ?>&status=<?= $filters['status'] ?? '' ?>&search=<?= urlencode($filters['search'] ?? '') ?>" 
                       class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                    <?php else: ?>
                    <button disabled class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 opacity-40 text-slate-400 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <?php endif; ?>

                    <span class="px-3 py-1 font-bold text-slate-800 dark:text-slate-200">
                        <?= $pagination['page'] ?> / <?= max(1, $pagination['total_pages']) ?>
                    </span>

                    <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                    <a href="<?= BASE_URL ?>/students?page=<?= $pagination['page'] + 1 ?>&class_id=<?= $filters['class_id'] ?? '' ?>&grade_id=<?= $filters['grade_id'] ?? '' ?>&status=<?= $filters['status'] ?? '' ?>&search=<?= urlencode($filters['search'] ?? '') ?>" 
                       class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                    <?php else: ?>
                    <button disabled class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 opacity-40 text-slate-400 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Import Excel (Thiết Kế Chuyên Nghiệp Có Thông Tin Ở Trên) -->
<div id="importModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 md:p-7 max-w-xl w-full shadow-2xl space-y-5">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Nhập Danh Sách Học Sinh Từ Excel</h3>
                    <p class="text-[11px] text-slate-400">Tự động đồng bộ và tạo tài khoản học sinh hàng loạt vào hệ thống</p>
                </div>
            </div>
            <button onclick="closeModal('importModal')" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- THÔNG TIN Ở TRÊN XÍU: Hướng Dẫn & Quy Định Chuẩn Sở GD&ĐT -->
        <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50/80 via-emerald-50/40 to-blue-50/80 dark:from-slate-800 dark:to-slate-800 border border-blue-100 dark:border-slate-700/80 space-y-2.5 text-xs">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-[16px]">info</span>
                    Thông Tin & Quy Định Dữ Liệu Nhập
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                    Chuẩn Mẫu Bộ GD&ĐT
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                <p>&bull; <strong>Cột bắt buộc:</strong> Mã HS (*), Họ và Tên (*), Giới tính (*), Ngày sinh (*).</p>
                <p>&bull; <strong>Định dạng ngày:</strong> <code class="bg-white/80 dark:bg-slate-700 px-1 rounded">DD/MM/YYYY</code> hoặc <code class="bg-white/80 dark:bg-slate-700 px-1 rounded">YYYY-MM-DD</code>.</p>
                <p>&bull; <strong>Giới tính:</strong> Điền <em>Nam</em> hoặc <em>Nữ</em>.</p>
                <p>&bull; <strong>Mã học sinh:</strong> Không trùng lặp. Mật khẩu mặc định: <code class="bg-white/80 dark:bg-slate-700 px-1 rounded">student123</code>.</p>
            </div>
        </div>

        <!-- Tải Biểu Mẫu Chuẩn Excel (Có thông tin ở trên) -->
        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Chưa có file mẫu Excel?</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Tải file biểu mẫu chuẩn có sẵn dòng mẫu và thông tin trường</p>
            </div>
            <button type="button" onclick="downloadExcelTemplate()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex-shrink-0 active:scale-95">
                <span class="material-symbols-outlined text-[16px]">download</span>
                <span>Tải File Mẫu (.xlsx)</span>
            </button>
        </div>

        <!-- Lớp Học Mặc Định -->
        <div class="space-y-1.5 text-xs">
            <label class="block font-bold text-slate-700 dark:text-slate-300">
                Phân Công Lớp Học Mặc Định <span class="text-slate-400 font-normal">(khi trong file Excel không ghi cột Tên Lớp)</span>
            </label>
            <div class="relative">
                <select id="importClassSelect" class="w-full appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all cursor-pointer">
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>
        </div>

        <!-- Khung Kéo Thả / Chọn Tệp Tin -->
        <div class="space-y-2 text-xs">
            <label class="block font-bold text-slate-700 dark:text-slate-300">Tệp Tin Excel Cần Nhập Dữ Liệu</label>
            <div id="dropZone" class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 rounded-2xl p-5 text-center bg-slate-50/50 dark:bg-slate-800/40 transition-all cursor-pointer relative group">
                <input type="file" id="excelFileInput" accept=".xlsx, .xls, .csv" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer" onchange="previewExcelFile(this)">
                
                <div id="dropZonePrompt" class="flex flex-col items-center gap-1.5">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[22px]">description</span>
                    </div>
                    <p class="font-bold text-slate-800 dark:text-slate-200">Kéo thả file Excel vào đây hoặc <span class="text-emerald-600 hover:underline">bấm để chọn</span></p>
                    <p class="text-[11px] text-slate-400">Định dạng hỗ trợ: .xlsx, .xls, .csv (Tối đa 10MB)</p>
                </div>

                <!-- Preview File & Records Info -->
                <div id="filePreviewBox" class="hidden flex items-center justify-center gap-2.5 p-3 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl border border-emerald-200 dark:border-emerald-800 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">verified</span>
                    <span id="filePreviewText">File đã chọn</span>
                </div>
            </div>
        </div>

        <!-- Modal Actions -->
        <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeModal('importModal')" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors">
                Hủy Bỏ
            </button>
            <button type="button" id="confirmImportBtn" onclick="handleImportExcelFile()" class="inline-flex items-center gap-1.5 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all disabled:opacity-50">
                <span class="material-symbols-outlined text-[18px]">upload</span>
                <span>Xác Nhận Nhập Dữ Liệu</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Embedded student dataset for high-fidelity export with headers
    const ALL_PAGE_STUDENTS = <?= json_encode($students ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.student-checkbox:checked').length;
        const badge = document.getElementById('selectedCountBadge');
        if (selected > 0) {
            badge.textContent = `${selected} đã chọn`;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function openEditStudentModal(student) {
        document.getElementById('editStudentId').value = student.id;
        document.getElementById('editFullName').value = student.full_name;
        document.getElementById('editKhmerName').value = student.khmer_name || '';
        document.getElementById('editGender').value = student.gender;
        document.getElementById('editDob').value = student.dob;
        document.getElementById('editClassId').value = student.class_id || 1;
        document.getElementById('editStatus').value = student.status || 'studying';
        openModal('editStudentModal');
    }

    async function handleCreateStudent(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const res = await apiPost('<?= BASE_URL ?>/students', data);
        if (res) {
            closeModal('createStudentModal');
            setTimeout(() => location.reload(), 800);
        }
    }

    async function handleUpdateStudent(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const id = data.id;
        const res = await apiPost(`<?= BASE_URL ?>/students/${id}`, data);
        if (res) {
            closeModal('editStudentModal');
            setTimeout(() => location.reload(), 800);
        }
    }

    async function deleteStudent(id) {
        if (!confirm('Bạn có chắc chắn muốn chuyển học sinh này vào danh sách lưu trữ không?')) return;
        const res = await apiPost(`<?= BASE_URL ?>/students/${id}`, { _method: 'DELETE' });
        if (res) {
            setTimeout(() => location.reload(), 800);
        }
    }

    // =========================================================================
    // XUẤT EXCEL CHUYÊN NGHIỆP CÓ THÔNG TIN HÀNH CHÍNH Ở TRÊN (Header Rows)
    // =========================================================================
    function exportCustomExcel() {
        if (!ALL_PAGE_STUDENTS || ALL_PAGE_STUDENTS.length === 0) {
            showToast('Không có dữ liệu học sinh để xuất!', 'warning');
            return;
        }

        const today = new Date();
        const dateStr = today.toLocaleDateString('vi-VN');
        const fileDate = today.toISOString().slice(0, 10);

        // Status mapping to Vietnamese
        const statusTextMap = {
            'studying': 'Đang học',
            'transferred': 'Chuyển trường',
            'graduated': 'Đã tốt nghiệp',
            'suspended': 'Bảo lưu',
            'dropped': 'Đã nghỉ học'
        };

        // Format Date to dd/mm/yyyy
        function formatDate(dStr) {
            if (!dStr) return '';
            const p = dStr.split('-');
            if (p.length === 3) return `${p[2]}/${p[1]}/${p[0]}`;
            return dStr;
        }

        // Build 2D Array with Official Administrative Header at rows 1-6
        const rows = [
            ["SỞ GIÁO DỤC VÀ ĐÀO TẠO TP. HỒ CHÍ MINH", "", "", "", "", "CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM"],
            ["TRƯỜNG TIỂU HỌC & THCS EDUMANAGE", "", "", "", "", "Độc lập - Tự do - Hạnh phúc"],
            ["BỘ PHẬN QUẢN LÝ HỒ SƠ HỌC SINH", "", "", "", "", "-------------------------"],
            [""],
            ["DANH SÁCH HỒ SƠ HỌC SINH"],
            [`Năm học: 2025 - 2026   |   Ngày lập danh sách: ${dateStr}   |   Tổng số học sinh: ${ALL_PAGE_STUDENTS.length}`],
            [""],
            // Row 8: Table Column Headers
            [
                "STT",
                "Mã Học Sinh",
                "Họ và Tên",
                "Tên Tiếng Khmer",
                "Giới Tính",
                "Ngày Sinh",
                "Lớp Học",
                "Trạng Thái",
                "Số Điện Thoại",
                "Email",
                "Địa Chỉ Thường Trú"
            ]
        ];

        // Data Rows
        ALL_PAGE_STUDENTS.forEach((st, idx) => {
            rows.push([
                idx + 1,
                st.student_code || '',
                st.full_name || '',
                st.khmer_name || '',
                st.gender === 'female' ? 'Nữ' : 'Nam',
                formatDate(st.dob),
                st.class_name || 'Chưa phân lớp',
                statusTextMap[st.status] || st.status || 'Đang học',
                st.phone || '',
                st.email || '',
                st.address || ''
            ]);
        });

        // Bottom Signatures Block
        rows.push([""]);
        rows.push(["", "", "", "", "", "", "", `Ngày ${today.getDate()} tháng ${today.getMonth() + 1} năm ${today.getFullYear()}`]);
        rows.push(["", "NGƯỜI LẬP BIỂU", "", "", "", "", "", "HIỆU TRƯỞNG / BAN GIÁM HIỆU"]);
        rows.push(["", "(Ký và ghi rõ họ tên)", "", "", "", "", "", "(Ký tên và đóng dấu)"]);

        // Create Worksheet
        const ws = XLSX.utils.aoa_to_sheet(rows);

        // Column widths for optimal readability
        ws['!cols'] = [
            { wch: 6 },   // STT
            { wch: 15 },  // Mã HS
            { wch: 25 },  // Họ tên
            { wch: 16 },  // Khmer
            { wch: 10 },  // Giới tính
            { wch: 14 },  // Ngày sinh
            { wch: 14 },  // Lớp
            { wch: 16 },  // Trạng thái
            { wch: 15 },  // SĐT
            { wch: 25 },  // Email
            { wch: 32 }   // Địa chỉ
        ];

        // Create Workbook and save
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "DanhSachHocSinh");
        XLSX.writeFile(wb, `Danh_Sach_Hoc_Sinh_EduManage_${fileDate}.xlsx`);
        showToast('Xuất danh sách Excel thành công!', 'success');
    }

    // =========================================================================
    // TẢI FILE EXCEL MẪU (.XLSX) CÓ SẴN THÔNG TIN VÀ DÒNG MẪU Ở TRÊN
    // =========================================================================
    function downloadExcelTemplate() {
        const templateRows = [
            ["TRƯỜNG TIỂU HỌC & THCS EDUMANAGE - BIỂU MẪU NHẬP HỌC SINH MỚI"],
            ["HƯỚNG DẪN: Điền đúng định dạng. Các cột có dấu (*) là bắt buộc. Giữ nguyên thứ tự các cột bên dưới."],
            ["QUY ĐỊNH: Giới tính điền 'Nam' hoặc 'Nữ'. Ngày sinh điền dạng DD/MM/YYYY (ví dụ: 15/05/2018)."],
            [""],
            [
                "STT",
                "Mã Học Sinh (*)",
                "Họ và Tên (*)",
                "Tên Tiếng Khmer",
                "Giới Tính (*)",
                "Ngày Sinh (*)",
                "Tên Lớp",
                "Số Điện Thoại",
                "Email",
                "Địa Chỉ Thường Trú"
            ],
            // 3 Realistic Sample Rows
            [
                1,
                "HS26967",
                "nana Van Na",
                "Chhay Meas",
                "Nam",
                "01/01/2010",
                "Lớp 1",
                "0768839304",
                "lamna11@gmail.com",
                "Số 123 Đường Nguyễn Trãi, Quận 5, TP.HCM"
            ],
            [
                2,
                "HS02012",
                "Hoàng Phương Hân",
                "Dara",
                "Nữ",
                "03/09/2018",
                "Lớp 2",
                "0980000021",
                "han@edumanage.edu.vn",
                "Số 45 Lê Lợi, Quận 1, TP.HCM"
            ],
            [
                3,
                "HS01821",
                "Lê Đình Khoa",
                "Seng",
                "Nam",
                "15/05/2018",
                "Lớp 1",
                "0912345678",
                "khoa@edumanage.edu.vn",
                "Số 82 Nguyễn Du, TP.HCM"
            ]
        ];

        const ws = XLSX.utils.aoa_to_sheet(templateRows);
        ws['!cols'] = [
            { wch: 6 },
            { wch: 18 },
            { wch: 25 },
            { wch: 18 },
            { wch: 12 },
            { wch: 16 },
            { wch: 14 },
            { wch: 16 },
            { wch: 24 },
            { wch: 35 }
        ];

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Mau_Nhap_Hoc_Sinh");
        XLSX.writeFile(wb, "Mau_Nhap_Danh_Sach_Hoc_Sinh_EduManage.xlsx");
        showToast('Đã tải file Excel mẫu thành công!', 'success');
    }

    // Preview Excel File on selection
    let parsedImportRecords = [];

    function previewExcelFile(input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                
                // Convert to array of arrays first to inspect header row
                const aoa = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                
                // Find row index containing "Mã HS" or "Họ và Tên"
                let headerRowIdx = -1;
                for (let i = 0; i < Math.min(aoa.length, 10); i++) {
                    const row = aoa[i];
                    if (Array.isArray(row)) {
                        const rowStr = row.join(' ').toLowerCase();
                        if (rowStr.includes('mã') || rowStr.includes('họ và tên') || rowStr.includes('student_code')) {
                            headerRowIdx = i;
                            break;
                        }
                    }
                }

                let jsonData = [];
                if (headerRowIdx !== -1) {
                    jsonData = XLSX.utils.sheet_to_json(worksheet, { range: headerRowIdx });
                } else {
                    jsonData = XLSX.utils.sheet_to_json(worksheet);
                }

                parsedImportRecords = jsonData;

                // Update UI Preview
                document.getElementById('dropZonePrompt').classList.add('hidden');
                const pBox = document.getElementById('filePreviewBox');
                pBox.classList.remove('hidden');
                document.getElementById('filePreviewText').innerText = 
                    `Đã chọn: ${file.name} — Phát hiện ${jsonData.length} dòng học sinh sẵn sàng nhập.`;
            } catch (err) {
                console.error(err);
                showToast('Lỗi khi đọc file Excel. Vui lòng kiểm tra định dạng!', 'error');
            }
        };
        reader.readAsArrayBuffer(file);
    }

    // =========================================================================
    // XỬ LÝ NHẬP HỌC SINH TỪ FILE EXCEL
    // =========================================================================
    async function handleImportExcelFile() {
        const fileInput = document.getElementById('excelFileInput');
        if (!fileInput.files.length) {
            showToast('Vui lòng chọn tệp tin Excel cần nhập!', 'warning');
            return;
        }

        if (parsedImportRecords.length === 0) {
            showToast('Tệp tin không có dòng dữ liệu hợp lệ!', 'error');
            return;
        }

        const defaultClassId = Number(document.getElementById('importClassSelect').value) || 1;
        const confirmBtn = document.getElementById('confirmImportBtn');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span><span>Đang xử lý nhập...</span>`;

        // Map columns flexibly
        const records = parsedImportRecords.map(r => {
            const code = r['Mã Học Sinh (*)'] || r['Mã Học Sinh'] || r['Mã HS'] || r['student_code'] || r['Ma HS'] || '';
            const name = r['Họ và Tên (*)'] || r['Họ và Tên'] || r['Họ tên'] || r['full_name'] || r['Ho va Ten'] || '';
            const khmer = r['Tên Tiếng Khmer'] || r['Tên Khmer'] || r['khmer_name'] || '';
            const rawGender = String(r['Giới Tính (*)'] || r['Giới Tính'] || r['Giới tính'] || r['gender'] || '').toLowerCase();
            const gender = (rawGender.includes('nữ') || rawGender.includes('nu') || rawGender.includes('female')) ? 'female' : 'male';
            
            let dob = r['Ngày Sinh (*)'] || r['Ngày Sinh'] || r['Ngày sinh'] || r['dob'] || '2010-01-01';
            // Handle numeric Excel date serial if any
            if (typeof dob === 'number') {
                const d = new Date(Math.round((dob - 25569) * 86400 * 1000));
                dob = d.toISOString().slice(0, 10);
            }

            const className = r['Tên Lớp'] || r['Lớp Học'] || r['Lớp'] || r['class_name'] || '';
            const phone = r['Số Điện Thoại'] || r['SĐT'] || r['phone'] || '';
            const email = r['Email'] || r['email'] || '';
            const address = r['Địa Chỉ Thường Trú'] || r['Địa Chỉ'] || r['address'] || '';

            return {
                student_code: String(code).trim(),
                full_name: String(name).trim(),
                khmer_name: String(khmer).trim(),
                gender: gender,
                dob: String(dob).trim(),
                class_name: String(className).trim(),
                class_id: defaultClassId,
                phone: String(phone).trim(),
                email: String(email).trim(),
                address: String(address).trim()
            };
        }).filter(item => item.student_code && item.full_name);

        if (records.length === 0) {
            showToast('Không tìm thấy học sinh hợp lệ (cần đủ Mã HS và Họ tên)!', 'error');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = `<span class="material-symbols-outlined text-[18px]">upload</span><span>Xác Nhận Nhập Dữ Liệu</span>`;
            return;
        }

        const res = await apiPost('<?= BASE_URL ?>/students/import', { records });
        if (res) {
            closeModal('importModal');
            setTimeout(() => location.reload(), 1200);
        } else {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = `<span class="material-symbols-outlined text-[18px]">upload</span><span>Xác Nhận Nhập Dữ Liệu</span>`;
        }
    }
</script>
