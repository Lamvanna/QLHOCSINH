<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Thời Khóa Biểu</h2>
            <p class="text-xs text-slate-500 mt-1">Lịch học theo tuần, kiểm tra tự động trùng lịch giáo viên, lớp học và phòng học</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-3.5 py-2 border rounded-xl bg-white dark:bg-slate-800 text-xs font-semibold">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5 inline"></i> In Thời Khóa Biểu
            </button>
            <?php if (Permission::can('schedules.create')): ?>
            <button onclick="openModal('createScheduleModal')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/20">
                <i data-lucide="plus" class="w-4 h-4 mr-1.5 inline"></i> Xếp Tiết Mới
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white dark:bg-slate-900 border rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= BASE_URL ?>/schedules" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Lớp Học</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border rounded-xl text-xs font-bold">
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Học Kỳ</label>
                <select name="semester_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border rounded-xl text-xs font-bold">
                    <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['id'] ?>" <?= $selectedSemester == $sem['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 bg-slate-800 text-white rounded-xl text-xs font-bold">Xem Thời Khóa Biểu</button>
            </div>
        </form>
    </div>

    <!-- Weekly Grid View (Thứ 2 đến Thứ 6) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <?php 
            $days = [2 => 'Thứ Hai', 3 => 'Thứ Ba', 4 => 'Thứ Tư', 5 => 'Thứ Năm', 6 => 'Thứ Sáu'];
            foreach ($days as $dNum => $dName): 
                $daySchedules = array_filter($timetable, fn($item) => $item['day_of_week'] == $dNum);
            ?>
            <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 p-4">
                <div class="text-center pb-3 border-b border-slate-200 dark:border-slate-700">
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white"><?= $dName ?></h4>
                    <span class="text-[10px] text-slate-400"><?= count($daySchedules) ?> Tiết học</span>
                </div>

                <div class="space-y-3 mt-3">
                    <?php if (!empty($daySchedules)): ?>
                        <?php foreach ($daySchedules as $sc): ?>
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm relative group">
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Tiết <?= $sc['period_start'] ?> - <?= $sc['period_end'] ?></span>
                            <h5 class="font-extrabold text-xs text-slate-900 dark:text-white mt-1.5"><?= htmlspecialchars($sc['subject_name']) ?></h5>
                            <p class="text-[10px] text-slate-500 mt-0.5">GV: <?= htmlspecialchars($sc['teacher_name']) ?></p>
                            <p class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($sc['room'] ?? '') ?></p>
                            
                            <?php if (Permission::can('schedules.delete')): ?>
                            <button onclick="deleteSchedule(<?= $sc['id'] ?>)" class="absolute top-2 right-2 p-1 text-slate-300 hover:text-rose-600 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-[11px] text-slate-400 py-6">Không có tiết học</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Xếp Tiết Mới -->
<div id="createScheduleModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border rounded-3xl p-6 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-lg font-bold">Xếp Tiết Thời Khóa Biểu</h3>
            <button onclick="closeModal('createScheduleModal')" class="text-slate-400">&times;</button>
        </div>
        <form onsubmit="handleCreateSchedule(event)" class="space-y-4 pt-4 text-xs">
            <input type="hidden" name="class_id" value="<?= $selectedClass ?>">
            <input type="hidden" name="semester_id" value="<?= $selectedSemester ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold mb-1">Môn Học *</label>
                    <select name="subject_id" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                        <?php foreach ($subjects as $sub): ?>
                        <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold mb-1">Giáo Viên Phụ Trách *</label>
                    <select name="teacher_id" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                        <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?> (<?= htmlspecialchars($t['specialization']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold mb-1">Thứ *</label>
                    <select name="day_of_week" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                        <option value="2">Thứ Hai</option>
                        <option value="3">Thứ Ba</option>
                        <option value="4">Thứ Tư</option>
                        <option value="5">Thứ Năm</option>
                        <option value="6">Thứ Sáu</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold mb-1">Tiết Bắt Đầu *</label>
                    <input type="number" name="period_start" min="1" max="5" value="1" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                </div>
                <div>
                    <label class="block font-bold mb-1">Tiết Kết Thúc *</label>
                    <input type="number" name="period_end" min="1" max="5" value="1" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                </div>
            </div>

            <div>
                <label class="block font-bold mb-1">Phòng Học</label>
                <input type="text" name="room" placeholder="Phòng A101" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
            </div>

            <div class="pt-4 flex justify-end space-x-2 border-t">
                <button type="button" onclick="closeModal('createScheduleModal')" class="px-4 py-2 border rounded-xl">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl">Xác Nhận Xếp Tiết</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    async function handleCreateSchedule(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const res = await apiPost('<?= BASE_URL ?>/schedules', data);
        if (res) {
            closeModal('createScheduleModal');
            setTimeout(() => location.reload(), 800);
        }
    }

    async function deleteSchedule(id) {
        if (!confirm('Xóa tiết học này khỏi thời khóa biểu?')) return;
        const res = await apiPost(`<?= BASE_URL ?>/schedules/${id}`, { _method: 'DELETE' });
        if (res) {
            setTimeout(() => location.reload(), 800);
        }
    }
</script>
