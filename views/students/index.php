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
            <button onclick="exportTableToExcel()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm" title="Xuất file Excel">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">table_chart</span>
                <span class="hidden sm:inline">Xuất Excel</span>
            </button>
            <button onclick="openModal('importModal')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm" title="Nhập danh sách Excel">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">upload_file</span>
                <span class="hidden sm:inline">Nhập Excel</span>
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
                    <button onclick="exportTableToExcel()" class="p-1.5 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 transition-colors" title="Xuất Excel">
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

<!-- Modal: Import Excel -->
<div id="importModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Nhập Danh Sách Học Sinh Từ Excel</h3>
            <button onclick="closeModal('importModal')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <div class="py-4 text-xs space-y-3">
            <p class="text-slate-500">Chọn tệp tin Excel (.xlsx, .xls) chứa danh sách học sinh theo các cột: <strong>Mã HS, Họ tên, Tên Khmer, Giới tính, Ngày sinh</strong>.</p>
            <input type="file" id="excelFileInput" accept=".xlsx, .xls, .csv" class="w-full p-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
        </div>
        <div class="pt-4 flex justify-end space-x-2 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeModal('importModal')" class="px-4 py-2 border rounded-xl font-semibold">Đóng</button>
            <button type="button" onclick="handleImportExcelFile()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl">Xác Nhận Nhập</button>
        </div>
    </div>
</div>

<script>
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

    function exportTableToExcel() {
        const table = document.getElementById("studentsTable");
        const wb = XLSX.utils.table_to_book(table, {sheet: "DanhSachHocSinh"});
        XLSX.writeFile(wb, "DanhSachHocSinh_EduManage.xlsx");
    }

    function handleImportExcelFile() {
        const fileInput = document.getElementById('excelFileInput');
        if (!fileInput.files.length) {
            showToast('Vui lòng chọn tệp tin Excel!', 'error');
            return;
        }
        const file = fileInput.files[0];
        const reader = new FileReader();
        reader.onload = async function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {type: 'array'});
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const jsonData = XLSX.utils.sheet_to_json(firstSheet);
            
            const records = jsonData.map(r => ({
                student_code: r['Mã HS'] || r['student_code'] || '',
                full_name: r['Họ và Tên'] || r['full_name'] || '',
                khmer_name: r['Tên Khmer'] || r['khmer_name'] || '',
                gender: (r['Giới tính'] === 'Nữ' || r['gender'] === 'female') ? 'female' : 'male',
                dob: r['Ngày sinh'] || '2010-01-01',
                class_id: 1
            }));

            const res = await apiPost('<?= BASE_URL ?>/students/import', { records });
            if (res) {
                closeModal('importModal');
                setTimeout(() => location.reload(), 1000);
            }
        };
        reader.readAsArrayBuffer(file);
    }
</script>
