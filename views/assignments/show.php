<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="<?= BASE_URL ?>/assignments" class="text-xs text-slate-500 hover:text-emerald-600 flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Danh sách bài tập
        </a>
    </div>

    <!-- Assignment Hero Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white"><?= htmlspecialchars($assignment['title']) ?></h2>
                <p class="text-xs text-slate-500 mt-1">Hạn chót nộp bài: <strong class="text-rose-600"><?= date('d/m/Y H:i', strtotime($assignment['due_date'])) ?></strong> | Thang điểm: <strong><?= $assignment['max_score'] ?></strong></p>
            </div>
            <?php if (Auth::isStudent()): ?>
            <button onclick="openModal('submitAssignmentModal')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20">
                <i data-lucide="send" class="w-4 h-4 mr-1.5 inline"></i> Nộp Bài Làm Của Tôi
            </button>
            <?php endif; ?>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
            <?= nl2br(htmlspecialchars($assignment['description'] ?? '')) ?>
        </div>
    </div>

    <!-- Submissions List (For Teacher / Admin) -->
    <?php if (Auth::isTeacher() || Auth::isAdmin()): ?>
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Danh Sách Học Sinh Đã Nộp Bài</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                        <th class="p-3.5 w-12 text-center">STT</th>
                        <th class="p-3.5">Mã HS</th>
                        <th class="p-3.5">Học Sinh</th>
                        <th class="p-3.5">Thời Gian Nộp</th>
                        <th class="p-3.5">Bài Làm / Nội Dung</th>
                        <th class="p-3.5">Điểm</th>
                        <th class="p-3.5 text-right">Chấm Bài</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $idx => $subm): ?>
                        <tr>
                            <td class="p-3 text-center text-slate-400"><?= $idx + 1 ?></td>
                            <td class="p-3 font-mono font-bold text-emerald-600"><?= htmlspecialchars($subm['student_code']) ?></td>
                            <td class="p-3 font-bold"><?= htmlspecialchars($subm['student_name']) ?></td>
                            <td class="p-3 text-slate-500"><?= date('d/m/Y H:i', strtotime($subm['submitted_at'])) ?></td>
                            <td class="p-3 max-w-xs truncate"><?= htmlspecialchars($subm['content'] ?? '—') ?></td>
                            <td class="p-3 font-mono font-bold text-emerald-600 text-sm"><?= $subm['score'] !== null ? $subm['score'] : '—' ?></td>
                            <td class="p-3 text-right">
                                <button onclick="openGradeModal(<?= $subm['id'] ?>, '<?= addslashes($subm['student_name']) ?>', '<?= $subm['score'] ?? '' ?>', '<?= addslashes($subm['feedback'] ?? '') ?>')" class="px-2.5 py-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950 rounded-lg hover:bg-emerald-100">
                                    Chấm điểm
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">Chưa có học sinh nào nộp bài.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Chấm Điểm -->
<div id="gradeModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-base font-bold">Chấm Điểm & Nhận Xét</h3>
            <button onclick="closeModal('gradeModal')" class="text-slate-400">&times;</button>
        </div>
        <form onsubmit="handleGradeSubmit(event)" class="space-y-4 pt-4 text-xs">
            <input type="hidden" name="submission_id" id="gradeSubmissionId">
            <p>Học sinh: <strong id="gradeStudentName" class="text-emerald-600"></strong></p>
            <div>
                <label class="block font-bold mb-1">Điểm Số (Thang 10) *</label>
                <input type="number" step="0.1" min="0" max="10" name="score" id="gradeScoreInput" required class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800 font-mono font-bold text-sm">
            </div>
            <div>
                <label class="block font-bold mb-1">Lời Nhận Xét Của Giáo Viên</label>
                <textarea name="feedback" id="gradeFeedbackInput" rows="3" class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800" placeholder="Nhận xét chi tiết bài làm..."></textarea>
            </div>
            <div class="pt-4 flex justify-end space-x-2 border-t">
                <button type="button" onclick="closeModal('gradeModal')" class="px-4 py-2 border rounded-xl">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl">Lưu Kết Quả</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Học Sinh Nộp Bài -->
<div id="submitAssignmentModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border rounded-3xl p-6 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-base font-bold">Nộp Bài Tập</h3>
            <button onclick="closeModal('submitAssignmentModal')" class="text-slate-400">&times;</button>
        </div>
        <form onsubmit="handleStudentSubmit(event)" class="space-y-4 pt-4 text-xs">
            <div>
                <label class="block font-bold mb-1">Nội Dung Bài Làm / Câu Trả Lời *</label>
                <textarea name="content" rows="4" required placeholder="Nhập câu trả lời hoặc tóm tắt nội dung bài làm của em tại đây..." class="w-full px-3 py-2 border rounded-xl bg-slate-50 dark:bg-slate-800"></textarea>
            </div>
            <div class="pt-4 flex justify-end space-x-2 border-t">
                <button type="button" onclick="closeModal('submitAssignmentModal')" class="px-4 py-2 border rounded-xl">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl">Nộp Bài Ngay</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function openGradeModal(id, name, score, feedback) {
        document.getElementById('gradeSubmissionId').value = id;
        document.getElementById('gradeStudentName').textContent = name;
        document.getElementById('gradeScoreInput').value = score;
        document.getElementById('gradeFeedbackInput').value = feedback;
        openModal('gradeModal');
    }

    async function handleGradeSubmit(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const res = await apiPost('<?= BASE_URL ?>/assignments/grade', data);
        if (res) {
            closeModal('gradeModal');
            setTimeout(() => location.reload(), 800);
        }
    }

    async function handleStudentSubmit(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const res = await apiPost('<?= BASE_URL ?>/assignments/<?= $assignment['id'] ?>/submit', data);
        if (res) {
            closeModal('submitAssignmentModal');
            setTimeout(() => location.reload(), 800);
        }
    }
</script>
