<div class="space-y-6">
    <!-- Hero Header -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 md:p-8 relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -mr-24 -mt-24"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl -ml-12 -mb-12"></div>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="inline-flex items-center gap-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest mb-3 border border-emerald-500/30">
                    <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                    <span>Bảng Điều Khiển <?= htmlspecialchars($user['role_display'] ?? 'Hệ Thống') ?></span>
                </div>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight text-white">Tổng quan hoạt động trường học</h2>
                <p class="text-xs md:text-sm text-slate-400 mt-1.5">Xin chào, <strong class="text-emerald-300"><?= htmlspecialchars($user['full_name'] ?? 'Quản trị viên') ?></strong>. Chúc bạn một ngày làm việc hiệu quả!</p>
            </div>
            
            <?php if (Auth::isAdmin()): ?>
            <form method="GET" action="<?= BASE_URL ?>/dashboard" class="flex flex-wrap items-center gap-2">
                <select name="year_id" class="bg-white/10 backdrop-blur border border-white/20 rounded-xl px-3 py-2 text-xs font-semibold text-white focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 outline-none">
                    <?php foreach ($stats['filter_years'] ?? [] as $y): ?>
                    <option class="text-slate-900" value="<?= $y['id'] ?>" <?= $y['is_current'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="semester_id" class="bg-white/10 backdrop-blur border border-white/20 rounded-xl px-3 py-2 text-xs font-semibold text-white focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 outline-none">
                    <?php foreach ($stats['filter_semesters'] ?? [] as $sem): ?>
                    <option class="text-slate-900" value="<?= $sem['id'] ?>" <?= $sem['is_current'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg shadow-emerald-500/30 transition-all active:scale-95 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Lọc</span>
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (Auth::isAdmin()): ?>
    <!-- ADMIN DASHBOARD: Bento Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        
        <!-- Summary Cards Row (Spans full 12 cols, 6 cards grid inside) -->
        <div class="md:col-span-12 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Card 1: Students -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Học Sinh</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[18px]">groups</span>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight"><?= number_format($stats['total_students'] ?? 0) ?></div>
                    <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 mt-1.5">
                        <span class="material-symbols-outlined text-[13px]">trending_up</span> Hồ sơ hoạt động
                    </div>
                </div>
            </div>

            <!-- Card 2: Teachers -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Giáo Viên</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-950/60 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[18px]">badge</span>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight"><?= number_format($stats['total_teachers'] ?? 0) ?></div>
                    <div class="text-[11px] text-slate-400 mt-1.5 font-semibold">Đang giảng dạy</div>
                </div>
            </div>

            <!-- Card 3: Classes -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Lớp Học</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 flex items-center justify-center">
                        <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-[18px]">class</span>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight"><?= number_format($stats['total_classes'] ?? 0) ?></div>
                    <div class="text-[11px] text-slate-400 mt-1.5 font-semibold">Lớp học chính khóa</div>
                </div>
            </div>

            <!-- Card 4: Subjects -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Môn Học</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-100 dark:bg-purple-950/60 flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-[18px]">menu_book</span>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight"><?= number_format($stats['total_subjects'] ?? 0) ?></div>
                    <div class="text-[11px] text-slate-400 mt-1.5 font-semibold">Chương trình chuẩn</div>
                </div>
            </div>

            <!-- Card 5: Assignments -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Bài Tập</span>
                    <div class="w-8 h-8 rounded-xl bg-teal-100 dark:bg-teal-950/60 flex items-center justify-center">
                        <span class="material-symbols-outlined text-teal-600 dark:text-teal-400 text-[18px]">assignment_turned_in</span>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight"><?= number_format($stats['total_assignments'] ?? 0) ?></div>
                    <div class="text-[11px] font-semibold text-teal-600 dark:text-teal-400 flex items-center mt-1.5">
                        <span class="material-symbols-outlined text-[13px] mr-0.5">task</span> Đang giao
                    </div>
                </div>
            </div>

            <!-- Card 6: GPA -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between card-subtle hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">ĐTB Toàn Trường</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center">
                        <span class="material-symbols-outlined text-amber-600 text-[18px]">grade</span>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight"><?= $stats['school_gpa'] ?? 7.85 ?></div>
                    <div class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center mt-1.5">
                        <span class="material-symbols-outlined text-[13px] mr-0.5">verified</span> Xếp loại Khá - Giỏi
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Charts Area - Left Col (8 spans) -->
        <div class="md:col-span-8 flex flex-col gap-6">
            
            <!-- Chart 1: Enrollment / Large Bar Chart -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 min-h-[320px] flex flex-col shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Phân Bổ Học Sinh Theo Lớp Học</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Phân bổ học sinh các lớp (Lớp 1 đến Lớp 5) trong năm học hiện tại</p>
                    </div>
                    <a href="<?= BASE_URL ?>/students" class="text-xs text-emerald-600 hover:underline font-bold flex items-center">
                        Xem danh sách học sinh &rarr;
                    </a>
                </div>
                <div class="flex-1 relative h-64">
                    <canvas id="gradeDistributionChart"></canvas>
                </div>
            </div>

            <!-- Side-by-side charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Gender Distribution Doughnut -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 flex flex-col min-h-[280px] shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Phân Bố Giới Tính</h3>
                        <span class="text-xs font-bold text-slate-500"><?= $stats['total_students'] ?> Học sinh</span>
                    </div>
                    <div class="flex-1 flex items-center justify-center relative">
                        <div class="w-44 h-44 relative">
                            <canvas id="genderPieChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-xl font-black text-slate-900 dark:text-white"><?= $stats['female_pct'] ?? 50 ?>%</span>
                                <span class="text-[10px] font-bold text-slate-400">Nữ Sinh</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-6 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-slate-600 dark:text-slate-400">Nam: <strong><?= $stats['male_students'] ?? 0 ?> (<?= $stats['male_pct'] ?>%)</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span class="text-slate-600 dark:text-slate-400">Nữ: <strong><?= $stats['female_students'] ?? 0 ?> (<?= $stats['female_pct'] ?>%)</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Subject Average Scores Chart -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 flex flex-col min-h-[280px] shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Điểm Trung Bình Theo Môn</h3>
                        <span class="text-xs font-bold text-emerald-600">Thang điểm 10</span>
                    </div>
                    <div class="flex-1 relative h-48">
                        <canvas id="subjectScoresChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Col (4 spans) -->
        <div class="md:col-span-4 flex flex-col gap-6">
            
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Lối Tắt Thao Tác</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?= BASE_URL ?>/students" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 transition-all gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">person_add</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 text-center">Học Sinh</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>/teachers" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-blue-50 dark:hover:bg-blue-950/40 border border-slate-200 dark:border-slate-700 hover:border-blue-500 transition-all gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">person_add_alt</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 text-center">Giáo Viên</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>/grades" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-amber-50 dark:hover:bg-amber-950/40 border border-slate-200 dark:border-slate-700 hover:border-amber-500 transition-all gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">edit_document</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 text-center">Nhập Điểm Số</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>/schedules" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-teal-50 dark:hover:bg-teal-950/40 border border-slate-200 dark:border-slate-700 hover:border-teal-500 transition-all gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">calendar_month</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 text-center">Thời Khóa Biểu</span>
                    </a>
                </div>
            </div>

            <!-- Student Status Breakdown -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Trạng Thái Học Sinh</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span class="text-slate-800 dark:text-slate-200">Đang học tại trường</span>
                            <span class="text-emerald-600 font-mono"><?= $stats['status_studying_pct'] ?? 95 ?>% (<?= $stats['status_studying'] ?? 0 ?>)</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: <?= $stats['status_studying_pct'] ?? 95 ?>%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span class="text-slate-800 dark:text-slate-200">Chuyển trường / Chờ duyệt</span>
                            <span class="text-blue-600 font-mono"><?= $stats['status_transferred_pct'] ?? 3 ?>% (<?= $stats['status_transferred'] ?? 0 ?>)</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: <?= $stats['status_transferred_pct'] ?? 3 ?>%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span class="text-slate-800 dark:text-slate-200">Đã tốt nghiệp / Khác</span>
                            <span class="text-slate-400 font-mono"><?= $stats['status_graduated_pct'] ?? 2 ?>% (<?= $stats['status_graduated'] ?? 0 ?>)</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-slate-400 h-2 rounded-full transition-all duration-500" style="width: <?= $stats['status_graduated_pct'] ?? 2 ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Feed (Live Audit Log) -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nhật Ký Thao Tác Gần Đây</h3>
                    <a href="<?= BASE_URL ?>/audit-logs" class="text-xs text-indigo-600 hover:underline font-bold">Xem tất cả</a>
                </div>
                
                <div class="flex-1 overflow-y-auto space-y-4 pr-1 max-h-80">
                    <?php foreach ($stats['recent_audit_logs'] ?? [] as $log): ?>
                    <div class="flex gap-3 items-start">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <span class="material-symbols-outlined text-sm">
                                <?= $log['module'] === 'grades' ? 'edit_document' : ($log['module'] === 'auth' ? 'login' : ($log['module'] === 'assignments' ? 'task' : 'person_add')) ?>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1 text-xs">
                            <p class="text-slate-800 dark:text-slate-200 leading-snug">
                                <span class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($log['full_name'] ?? 'Hệ thống') ?></span>
                                <?= htmlspecialchars($log['action']) ?> trên <span class="font-semibold text-emerald-600"><?= htmlspecialchars($log['module']) ?></span>.
                            </p>
                            <span class="text-[10px] text-slate-400 mt-0.5 block"><?= date('H:i d/m', strtotime($log['created_at'])) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </div>

    <!-- Chart.js Setup for Admin -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Grade Distribution Bar Chart
            const gradeCtx = document.getElementById('gradeDistributionChart')?.getContext('2d');
            if (gradeCtx) {
                new Chart(gradeCtx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_column($stats['chart_grade_distribution'] ?? [], 'name')) ?>,
                        datasets: [{
                            label: 'Số học sinh',
                            data: <?= json_encode(array_column($stats['chart_grade_distribution'] ?? [], 'count')) ?>,
                            backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#06b6d4'],
                            borderRadius: 12,
                            barThickness: 38
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { padding: 12, cornerRadius: 8 }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(200, 200, 200, 0.15)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Gender Pie / Doughnut Chart
            const genderCtx = document.getElementById('genderPieChart')?.getContext('2d');
            if (genderCtx) {
                new Chart(genderCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Nam', 'Nữ'],
                        datasets: [{
                            data: [<?= $stats['male_students'] ?? 50 ?>, <?= $stats['female_students'] ?? 50 ?>],
                            backgroundColor: ['#3b82f6', '#f43f5e'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false },
                            tooltip: { padding: 10, cornerRadius: 8 }
                        }
                    }
                });
            }

            // 3. Subject Average Scores Horizontal Bar
            const subCtx = document.getElementById('subjectScoresChart')?.getContext('2d');
            if (subCtx) {
                new Chart(subCtx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_column($stats['chart_subject_scores'] ?? [], 'name')) ?>,
                        datasets: [{
                            label: 'ĐTB Môn',
                            data: <?= json_encode(array_column($stats['chart_subject_scores'] ?? [], 'avg_score')) ?>,
                            backgroundColor: '#3b82f6',
                            borderRadius: 8,
                            barThickness: 16
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { padding: 10, cornerRadius: 8 }
                        },
                        scales: {
                            x: {
                                min: 0,
                                max: 10,
                                grid: { color: 'rgba(200, 200, 200, 0.15)' }
                            },
                            y: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <?php elseif (Auth::isTeacher()): ?>
    <!-- TEACHER DASHBOARD -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Lớp Học Đang Phụ Trách</h3>
            <div class="space-y-3">
                <?php foreach ($stats['assigned_classes'] ?? [] as $cls): ?>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white"><?= htmlspecialchars($cls['name']) ?></h4>
                        <p class="text-xs text-slate-400 font-mono">Sĩ số: <?= $cls['student_count'] ?? 0 ?> học sinh</p>
                    </div>
                    <a href="<?= BASE_URL ?>/classes/<?= $cls['id'] ?>" class="text-xs font-bold text-emerald-600 hover:underline">Chi tiết &rarr;</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Lịch Dạy Hôm Nay</h3>
                <a href="<?= BASE_URL ?>/schedules" class="text-xs text-emerald-600 hover:underline font-bold">Xem toàn bộ TKB</a>
            </div>
            <div class="space-y-3">
                <?php if (!empty($stats['today_schedules'])): ?>
                    <?php foreach ($stats['today_schedules'] as $sch): ?>
                    <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-xl bg-emerald-600 text-white font-mono font-bold text-xs">Tiết <?= $sch['period_start'] ?> - <?= $sch['period_end'] ?></span>
                            <div>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white"><?= htmlspecialchars($sch['subject_name']) ?> — Lớp <?= htmlspecialchars($sch['class_name']) ?></h4>
                                <p class="text-[10px] text-slate-400 font-mono">Phòng học: <?= htmlspecialchars($sch['room'] ?? 'A1') ?></p>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/grades?class_id=<?= $sch['class_id'] ?>&subject_id=<?= $sch['subject_id'] ?>" class="px-3 py-1.5 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-emerald-600 hover:bg-emerald-50 border border-emerald-200 dark:border-emerald-800 shadow-sm">
                            Vào sổ điểm
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-xs text-slate-400 py-12">Hôm nay không có tiết dạy nào được xếp lịch.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php elseif (Auth::isStudent()): ?>
    <!-- STUDENT DASHBOARD -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Hồ Sơ Của Tôi</h3>
            <div class="text-center pb-4 border-b border-slate-100 dark:border-slate-800">
                <img src="<?= htmlspecialchars($user['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150') ?>" class="w-16 h-16 rounded-full mx-auto object-cover ring-4 ring-emerald-500/20 mb-2">
                <h4 class="font-bold text-sm text-slate-900 dark:text-white"><?= htmlspecialchars($user['full_name']) ?></h4>
                <p class="text-xs text-slate-400 font-mono">Mã HS: <?= htmlspecialchars($stats['student_profile']['student_code'] ?? '') ?></p>
                <p class="text-xs font-semibold text-emerald-600 mt-1">Lớp <?= htmlspecialchars($stats['student_profile']['class_name'] ?? '') ?></p>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs">
                <span class="text-slate-400">Điểm Trung Bình (GPA):</span>
                <span class="font-mono font-black text-emerald-600 text-sm"><?= $stats['my_gpa'] ?></span>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <!-- Pending assignments -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Bài Tập Cần Hoàn Thành</h3>
                    <a href="<?= BASE_URL ?>/assignments" class="text-xs text-emerald-600 hover:underline font-bold">Xem tất cả</a>
                </div>
                <div class="space-y-3">
                    <?php if (!empty($stats['pending_assignments'])): ?>
                        <?php foreach ($stats['pending_assignments'] as $asg): ?>
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-mono"><?= htmlspecialchars($asg['subject_name']) ?></span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1"><?= htmlspecialchars($asg['title']) ?></h4>
                                <p class="text-[10px] text-rose-500 mt-0.5 font-semibold">Hạn nộp: <?= date('d/m/Y H:i', strtotime($asg['due_date'])) ?></p>
                            </div>
                            <a href="<?= BASE_URL ?>/assignments/<?= $asg['id'] ?>" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700">
                                Nộp bài
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-xs text-slate-400 py-6">Tuyệt vời! Bạn đã nộp hết tất cả bài tập.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
