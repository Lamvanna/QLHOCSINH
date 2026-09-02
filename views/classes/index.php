<?php // views/classes/index.php ?>
<div class="space-y-6">

    <!-- Hero Header -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 md:p-8 relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -mr-24 -mt-24"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -ml-12 -mb-12"></div>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-2 border border-emerald-500/30">
                    <span class="material-symbols-outlined text-[14px]">door_front</span> Danh Sách Lớp
                </div>
                <h2 class="text-2xl font-black text-white">Quản Lý Lớp Học</h2>
                <p class="text-xs text-slate-400 mt-1">
                    <span class="text-emerald-300 font-bold"><?= count($classes) ?> lớp học</span> —
                    tổng sĩ số <span class="text-white font-bold"><?= array_sum(array_column($classes, 'student_count')) ?></span> học sinh
                </p>
            </div>
            <div class="flex items-center gap-3">
                <?php if (Permission::can('classes.create')): ?>
                <button onclick="openClassModal('create')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-white text-xs font-black rounded-2xl shadow-lg shadow-emerald-500/30 transition-all active:scale-95 flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Tạo Lớp Học Mới</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php foreach ($classes as $c):
            $fillPct = $c['max_students'] > 0 ? round($c['student_count'] / $c['max_students'] * 100) : 0;
            $isFull  = $fillPct >= 100;
            $isAlmost = $fillPct >= 85 && !$isFull;
        ?>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden card-subtle group hover-lift flex flex-col">
            <!-- Card Top Color Bar -->
            <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 to-teal-500"></div>

            <div class="p-5 flex flex-col flex-1">
                <!-- Header row -->
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-mono font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 px-2.5 py-0.5 rounded-lg"><?= htmlspecialchars($c['code']) ?></span>
                            <?php if ($c['status'] !== 'active'): ?>
                            <span class="badge badge-rose text-[9px]">Không hoạt động</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight"><?= htmlspecialchars($c['name']) ?></h3>
                    </div>
                    <!-- Quick actions (hover) -->
                    <div class="flex gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity ml-1 flex-shrink-0">
                        <?php if (Permission::can('classes.update')): ?>
                        <button onclick="openClassModal('edit',<?= htmlspecialchars(json_encode($c)) ?>)"
                            class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-lg transition-colors" title="Sửa lớp">
                            <span class="material-symbols-outlined text-[15px]">edit</span>
                        </button>
                        <?php endif; ?>
                        <?php if (Permission::can('classes.delete')): ?>
                        <button onclick="deleteRecord('<?= BASE_URL ?>/classes/<?= $c['id'] ?>','Xóa lớp <?= htmlspecialchars($c['name']) ?>? Lớp phải không có học sinh!')"
                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors" title="Xóa lớp">
                            <span class="material-symbols-outlined text-[15px]">delete</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Teacher Info -->
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[15px] text-emerald-600 dark:text-emerald-400">person_pin</span>
                    <span class="text-xs font-semibold <?= !empty($c['homeroom_teacher_name']) ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400 italic' ?>">
                        <?= htmlspecialchars($c['homeroom_teacher_name'] ?? 'Chưa phân công GVCN') ?>
                    </span>
                </div>

                <?php if (!empty($c['room_number'])): ?>
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[15px] text-slate-400">meeting_room</span>
                    <span class="text-xs text-slate-500 font-medium">Phòng <?= htmlspecialchars($c['room_number']) ?></span>
                </div>
                <?php endif; ?>

                <!-- Spacer -->
                <div class="flex-1"></div>

                <!-- Sĩ số Progress -->
                <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-500">Sĩ số</span>
                        <span class="font-extrabold <?= $isFull ? 'text-rose-600' : ($isAlmost ? 'text-amber-600' : 'text-slate-800 dark:text-slate-200') ?>">
                            <?= $c['student_count'] ?> / <?= $c['max_students'] ?>
                            <?php if ($isFull): ?><span class="text-[9px] bg-rose-100 text-rose-600 px-1.5 py-0.5 rounded ml-1">ĐẦY</span><?php endif; ?>
                        </span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all <?= $isFull ? 'bg-rose-500' : ($isAlmost ? 'bg-amber-500' : 'bg-emerald-500') ?>"
                             style="width: <?= min($fillPct, 100) ?>%"></div>
                    </div>
                </div>

                <!-- Footer Link -->
                <a href="<?= BASE_URL ?>/classes/<?= $c['id'] ?>"
                   class="mt-3 flex items-center justify-between px-3 py-2 bg-slate-50 dark:bg-slate-800/60 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 rounded-xl transition-colors group/link">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400 group-hover/link:text-emerald-700 dark:group-hover/link:text-emerald-400 transition-colors">Xem danh sách lớp</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover/link:text-emerald-600 dark:group-hover/link:text-emerald-400 transition-colors">arrow_forward</span>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($classes)): ?>
    <div class="py-24 text-center text-slate-400">
        <span class="material-symbols-outlined text-6xl block mb-3 opacity-30">door_front</span>
        <p class="font-bold text-slate-500">Chưa có lớp học nào trong hệ thống</p>
        <p class="text-xs mt-1">Nhấn "Tạo Lớp Mới" để bắt đầu</p>
    </div>
    <?php endif; ?>

</div>

<!-- Modal: Thêm / Sửa Lớp Học -->
<div id="classModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-xl shadow-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-slate-900 flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 z-10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[20px]">door_front</span>
                </div>
                <div>
                    <h3 id="classModalTitle" class="text-sm font-extrabold text-slate-900 dark:text-white">Tạo Lớp Học Mới</h3>
                    <p class="text-[10px] text-slate-400">Điền đầy đủ thông tin lớp học</p>
                </div>
            </div>
            <button onclick="closeModal('classModal')" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form onsubmit="handleClassSubmit(event)" class="p-6 text-xs space-y-5">
            <input type="hidden" id="class_id" value="">
            <input type="hidden" id="c_grade" name="grade_id" value="1">

            <!-- Section 1: Thông tin cơ bản -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-[11px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-[14px]">info</span> Thông Tin Lớp Học
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Mã Lớp <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">badge</span>
                            <input id="c_code" type="text" name="code" required placeholder="10A1, 11B2..."
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono font-black text-emerald-600 uppercase">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Tên Lớp Học <span class="text-rose-500">*</span></label>
                        <input id="c_name" type="text" name="name" required placeholder="Lớp 10A1"
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Năm Học</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">calendar_today</span>
                        <select id="c_year" name="academic_year_id" class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold appearance-none">
                            <?php foreach ($years as $y): ?>
                            <option value="<?= $y['id'] ?>" <?= $y['is_current'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-800"></div>

            <!-- Section 2: Phân công -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-[11px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-[14px]">person_pin</span> Giáo Viên Chủ Nhiệm
                </div>
                <div class="space-y-1.5">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">school</span>
                        <select id="c_teacher" name="homeroom_teacher_id" class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold appearance-none">
                            <option value="">— Chưa phân công GVCN —</option>
                            <?php foreach ($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?> (<?= htmlspecialchars($t['specialization']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-800"></div>

            <!-- Section 3: Cơ sở vật chất -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-[11px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-[14px]">meeting_room</span> Cơ Sở Vật Chất
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Phòng Học</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">meeting_room</span>
                            <input id="c_room" type="text" name="room_number" placeholder="A101, B202..."
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Sĩ Số Tối Đa</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">group</span>
                            <input id="c_max" type="number" name="max_students" min="10" max="60" value="40"
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold font-mono">
                        </div>
                    </div>
                    <div class="col-span-2 space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Trạng Thái</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="active" checked class="accent-emerald-500">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Đang hoạt động</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="inactive" class="accent-rose-500">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Không hoạt động</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('classModal')"
                    class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Hủy Bỏ
                </button>
                <button type="submit" id="classSubmitBtn"
                    class="px-6 py-2.5 text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-2xl shadow-md shadow-emerald-500/20 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px] align-middle mr-1">save</span>
                    <span id="classSubmitLabel">Tạo Lớp Học</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openClassModal(mode, data = null) {
    const modal = document.getElementById('classModal');
    modal.classList.remove('hidden');
    const isEdit = mode === 'edit';

    document.getElementById('classModalTitle').textContent = isEdit ? 'Chỉnh Sửa Lớp Học' : 'Tạo Lớp Học Mới';
    document.getElementById('classSubmitLabel').textContent = isEdit ? 'Lưu Thay Đổi' : 'Tạo Lớp Học';
    document.getElementById('class_id').value = data?.id ?? '';

    // Fill fields
    document.getElementById('c_code').value    = data?.code ?? '';
    document.getElementById('c_code').readOnly = isEdit;
    document.getElementById('c_name').value    = data?.name ?? '';
    document.getElementById('c_grade').value   = data?.grade_id ?? '1';
    document.getElementById('c_year').value    = data?.academic_year_id ?? '';
    document.getElementById('c_teacher').value = data?.homeroom_teacher_id ?? '';
    document.getElementById('c_room').value    = data?.room_number ?? '';
    document.getElementById('c_max').value     = data?.max_students ?? 40;

    // Status radio
    const statusVal = data?.status ?? 'active';
    document.querySelectorAll('input[name="status"]').forEach(r => r.checked = (r.value === statusVal));
}

function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

async function handleClassSubmit(e) {
    e.preventDefault();
    const id  = document.getElementById('class_id').value;
    const btn = document.getElementById('classSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = `<span class="material-symbols-outlined text-[16px] align-middle animate-spin">progress_activity</span> Đang lưu...`;

    const url  = id ? `<?= BASE_URL ?>/classes/${id}` : `<?= BASE_URL ?>/classes`;
    const data = Object.fromEntries(new FormData(e.target).entries());
    const res  = await apiPost(url, data);

    if (res) {
        closeModal('classModal');
        setTimeout(() => location.reload(), 700);
    } else {
        btn.disabled = false;
        btn.innerHTML = `<span class="material-symbols-outlined text-[16px] align-middle mr-1">save</span><span>${id ? 'Lưu Thay Đổi' : 'Tạo Lớp Học'}</span>`;
    }
}

async function deleteRecord(url, msg) {
    if (!confirm(msg)) return;
    const res = await apiPost(url, { _method: 'DELETE' });
    if (res) setTimeout(() => location.reload(), 700);
}
</script>
