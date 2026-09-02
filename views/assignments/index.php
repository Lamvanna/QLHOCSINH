<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Quản Lý Bài Tập</h2>
            <p class="text-xs text-slate-500 mt-1">Giao bài tập, nộp bài trực tuyến, chấm điểm và phản hồi</p>
        </div>
        <?php if (Permission::can('assignments.create')): ?>
        <button onclick="openModal('createAssignmentModal')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-1.5 inline"></i> Giao Bài Tập Mới
        </button>
        <?php endif; ?>
    </div>

    <!-- Assignments List Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($assignments as $a): ?>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-mono"><?= htmlspecialchars($a['subject_name']) ?></span>
                    <span class="text-xs font-bold text-slate-500"><?= htmlspecialchars($a['class_name']) ?></span>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-3"><?= htmlspecialchars($a['title']) ?></h3>
                <p class="text-xs text-slate-500 mt-1.5 line-clamp-2"><?= htmlspecialchars($a['description'] ?? 'Chưa có mô tả chi tiết') ?></p>

                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                    <span>Hạn nộp: <strong class="text-rose-600"><?= date('d/m/Y H:i', strtotime($a['due_date'])) ?></strong></span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <span class="text-xs text-slate-400">Đã nộp: <strong class="text-emerald-600 font-bold"><?= $a['submission_count'] ?> / <?= $a['total_students'] ?></strong></span>
                <a href="<?= BASE_URL ?>/assignments/<?= $a['id'] ?>" class="px-3.5 py-1.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 hover:bg-emerald-600 hover:text-white rounded-xl text-xs font-bold transition-colors">
                    Chi Tiết & Chấm Điểm &rarr;
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Giao Bài Tập -->
<div id="createAssignmentModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border rounded-3xl p-6 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-lg font-bold">Giao Bài Tập Mới</h3>
            <button onclick="closeModal('createAssignmentModal')" class="text-slate-400">&times;</button>
        </div>
        <form onsubmit="handleCreateAssignment(event)" class="space-y-4 pt-4 text-xs">
            <div>
                <label class="block font-bold mb-1">Tiêu Đề Bài Tập *</label>
                <input type="text" name="title" required placeholder="VD: Bài tập chuyên đề Hàm số bậc 2" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold mb-1">Lớp Nhận Bài *</label>
                    <select name="class_id" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold mb-1">Môn Học *</label>
                    <select name="subject_id" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                        <?php foreach ($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold mb-1">Hạn Nộp *</label>
                    <input type="datetime-local" name="due_date" required value="<?= date('Y-m-d\TH:i', strtotime('+7 days')) ?>" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                </div>
                <div>
                    <label class="block font-bold mb-1">Thang Điểm Tối Đa</label>
                    <input type="number" name="max_score" value="10" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800">
                </div>
            </div>
            <div>
                <label class="block font-bold mb-1">Hướng Dẫn & Yêu Cầu</label>
                <textarea name="description" rows="3" placeholder="Nhập yêu cầu làm bài chi tiết..." class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800"></textarea>
            </div>
            <div class="pt-4 flex justify-end space-x-2 border-t">
                <button type="button" onclick="closeModal('createAssignmentModal')" class="px-4 py-2 border rounded-xl">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl">Đăng Bài Tập</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    async function handleCreateAssignment(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const res = await apiPost('<?= BASE_URL ?>/assignments', data);
        if (res) {
            closeModal('createAssignmentModal');
            setTimeout(() => location.reload(), 800);
        }
    }
</script>
