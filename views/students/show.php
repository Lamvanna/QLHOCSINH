<?php
// views/students/show.php - Applied Exact User Mockup Code & Styles
$statusMap = [
    'studying' => ['text' => 'Đang học', 'class' => 'bg-green-50 text-green-700 border-green-200'],
    'graduated' => ['text' => 'Đã tốt nghiệp', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
    'transferred' => ['text' => 'Chuyển trường', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
    'suspended' => ['text' => 'Bảo lưu', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
    'dropped' => ['text' => 'Đã nghỉ học', 'class' => 'bg-rose-50 text-rose-700 border-rose-200']
];
$currentStatus = $statusMap[$student['status'] ?? 'studying'] ?? ['text' => htmlspecialchars($student['status'] ?? 'Đang học'), 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];

$historyList = $academicHistory ?? [];

// Determine default selected stage for history (current year)
$currentStageKey = 'lop_1';
foreach ($historyList as $h) {
    if (!empty($h['is_current'])) {
        $currentStageKey = $h['stage_key'];
        break;
    }
}
?>

<style>
/* Custom Exact Design System Palette */
.bg-secondary { background-color: #006c4a !important; }
.text-secondary { color: #006c4a !important; }
.border-secondary { border-color: #006c4a !important; }
.hover\:bg-on-secondary-fixed-variant:hover { background-color: #005137 !important; }
.bg-surface-container-lowest { background-color: #ffffff !important; }
.bg-surface-container-low { background-color: #f3f3fe !important; }
.bg-surface-bg { background-color: #f8fafc !important; }
.border-border-subtle { border-color: #e2e8f0 !important; }
.text-text-main { color: #0f172a !important; }
.text-text-muted { color: #64748b !important; }
.text-accent-purple { color: #a855f7 !important; }
.text-primary { color: #004ac6 !important; }
.bg-primary\/5 { background-color: rgba(0, 74, 198, 0.05) !important; }
.border-primary\/20 { border-color: rgba(0, 74, 198, 0.2) !important; }
.bg-secondary\/10 { background-color: rgba(0, 108, 74, 0.1) !important; }
.bg-secondary\/5 { background-color: rgba(0, 108, 74, 0.05) !important; }

.table-row-hover:hover {
    background-color: #f8fafc !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

@media print {
    header, nav, aside, .sidebar, #profileTabs, .no-print, .action-buttons-wrap, .year-selector-wrap, .breadcrumbs-wrap {
        display: none !important;
    }
    body, main {
        background: #fff !important;
        color: #000 !important;
        padding: 0 !important;
        margin: 0 !important;
        font-family: 'Times New Roman', Times, serif !important;
    }
    .print-only-header {
        display: block !important;
    }
    .tab-pane {
        display: block !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin-top: 20px !important;
    }
    #content-academic, #content-overview, #content-history {
        display: none !important;
    }
    #content-personal, #content-grades {
        display: block !important;
    }
    table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    table, th, td {
        border: 1px solid #333 !important;
    }
    th, td {
        padding: 5px 8px !important;
    }
    @page {
        size: A4 portrait;
        margin: 15mm;
    }
}
.print-only-header { display: none; }
</style>

<!-- Page Content matching exact user markup -->
<div class="w-full flex flex-col gap-5 pb-16">

    <!-- Print-only Official Header -->
    <div class="print-only-header mb-6">
        <div class="flex justify-between items-start text-xs font-bold leading-relaxed mb-6">
            <div class="w-1/2 text-left">
                <p class="uppercase">SỞ GIÁO DỤC VÀ ĐÀO TẠO</p>
                <p class="uppercase font-extrabold text-sm">TRƯỜNG TIỂU HỌC &amp; THCS EDUMANAGE</p>
                <p class="italic font-normal text-slate-500">Mã trường: EDU-2026</p>
            </div>
            <div class="w-1/2 text-center">
                <p class="uppercase">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</p>
                <p>Độc lập - Tự do - Hạnh phúc</p>
                <p>-------------------------</p>
            </div>
        </div>
        <div class="text-center my-4">
            <h1 class="text-xl font-bold uppercase tracking-wider">HỒ SƠ CHI TIẾT HỌC SINH</h1>
            <p class="text-xs italic text-slate-600 mt-1">Lớp: <span id="printClassName"><?= htmlspecialchars($student['class_name'] ?? 'Chưa phân lớp') ?></span> &bull; Năm học: <span id="printYearName"><?= htmlspecialchars($student['year_name'] ?? '2025 - 2026') ?></span></p>
        </div>
    </div>

    <!-- Breadcrumbs -->
    <nav class="breadcrumbs-wrap flex text-text-muted text-xs font-semibold items-center gap-1">
        <a class="hover:text-primary transition-colors" href="<?= BASE_URL ?>/students">Students</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-text-main"><?= htmlspecialchars($student['full_name']) ?></span>
    </nav>

    <!-- Header Card -->
    <section class="bg-surface-container-lowest rounded-xl border border-border-subtle shadow-sm p-4 md:p-6 flex flex-col md:flex-row gap-6 items-start md:items-center">
        <!-- Profile Identity -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 flex-1 text-center sm:text-left w-full">
            <div class="w-24 h-24 md:w-32 md:h-32 rounded-xl overflow-hidden border-2 border-surface-bg shadow-sm flex-shrink-0">
                <img alt="Student Profile Picture" class="w-full h-full object-cover" src="<?= htmlspecialchars($student['avatar'] ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=300') ?>"/>
            </div>
            <div class="flex flex-col gap-1 pt-1 md:pt-2">
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <h1 class="text-2xl md:text-3xl font-bold text-text-main"><?= htmlspecialchars($student['full_name']) ?></h1>
                    <div class="flex items-center gap-1">
                        <?php if (!empty($student['khmer_name'])): ?>
                        <span class="bg-purple-100 text-accent-purple text-xs font-semibold px-2 py-0.5 rounded-full"><?= htmlspecialchars($student['khmer_name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center sm:justify-start items-center gap-y-1 gap-x-4 text-slate-500 text-sm mt-1.5">
                    <span>Mã HS: <strong class="text-secondary font-semibold"><?= htmlspecialchars($student['student_code']) ?></strong></span>
                    <span class="w-1 h-1 rounded-full bg-border-subtle"></span>
                    <span>Lớp: <strong id="headerClassName" class="text-text-main font-semibold"><?= htmlspecialchars($student['class_name'] ?? 'Chưa phân lớp') ?></strong></span>
                    <span class="w-1 h-1 rounded-full bg-border-subtle"></span>
                    <div class="flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border <?= $currentStatus['class'] ?>">
                        <?php if ($student['status'] === 'studying'): ?>
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <?php endif; ?>
                        <?= $currentStatus['text'] ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons & Year Selector -->
        <div class="flex flex-col gap-3 w-full md:w-auto">
            <div class="action-buttons-wrap flex items-center justify-center md:justify-end gap-2 w-full">
                <?php if (Permission::can('students.update')): ?>
                <a href="<?= BASE_URL ?>/students/<?= $student['id'] ?>/edit" class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-secondary text-white text-xs font-semibold px-4 py-2.5 rounded-lg hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    <span>Sửa Hồ Sơ</span>
                </a>
                <?php endif; ?>
                <button onclick="window.print()" class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-surface-container-lowest text-text-main border border-border-subtle text-xs font-semibold px-4 py-2.5 rounded-lg hover:bg-surface-container-low transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>In Hồ Sơ</span>
                </button>
            </div>

            <div class="bg-surface-container-low border border-border-subtle rounded-lg p-3 w-full max-w-sm flex flex-col gap-2 relative group overflow-hidden">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-md shadow-sm text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-text-main">Năm Học Tra Cứu:</span>
                                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Đang hiển thị</span>
                            </div>
                            <p class="text-text-muted text-[11px] leading-tight mt-0.5">Dữ liệu lớp học, GVCN và bảng điểm sẽ đồng bộ theo năm được chọn</p>
                        </div>
                    </div>
                </div>
                <div class="mt-1">
                    <select id="globalYearSelect" onchange="changeGlobalYear(this.value)" class="w-full bg-surface-container-lowest border border-border-subtle text-text-main font-semibold text-xs rounded-md px-3 py-2 focus:ring-primary focus:border-primary shadow-sm appearance-none cursor-pointer hover:border-slate-400 transition-colors">
                        <?php foreach ($historyList as $stage): ?>
                        <option value="<?= $stage['stage_key'] ?>" <?= $stage['stage_key'] === $currentStageKey ? 'selected' : '' ?>>
                            <?= htmlspecialchars($stage['title']) ?> – <?= htmlspecialchars($stage['year_name']) ?> <?= !empty($stage['is_current']) ? '★ (Năm hiện tại)' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Summary Cards Bento Grid (4 Cards: Tổng điểm | Môn học | Xếp hạng | Năm học đang xem) -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tổng Điểm Tích Lũy -->
        <div class="bg-surface-container-lowest border border-border-subtle shadow-sm rounded-xl p-5 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-shadow">
            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-2">TỔNG ĐIỂM TÍCH LŨY</span>
            <div id="statGpa" class="flex items-baseline gap-1">
                <span class="text-4xl font-bold text-secondary">0.0</span>
                <span class="text-lg font-semibold text-slate-400">/ 50</span>
            </div>
        </div>

        <!-- Card 2: Môn Học -->
        <div class="bg-surface-container-lowest border border-border-subtle shadow-sm rounded-xl p-5 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-shadow">
            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-2">MÔN HỌC</span>
            <div id="statSubjects" class="flex items-baseline gap-2">
                <span class="text-4xl font-bold text-primary">0</span>
                <span class="text-base font-semibold text-slate-500">môn</span>
            </div>
        </div>

        <!-- Card 3: Xếp Hạng -->
        <div class="bg-surface-container-lowest border border-border-subtle shadow-sm rounded-xl p-5 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-shadow">
            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-2">XẾP HẠNG</span>
            <div id="statRank" class="flex items-baseline">
                <span class="text-4xl font-bold text-amber-500">Hạng 1</span>
            </div>
        </div>

        <!-- Card 4: Năm Học Đang Xem -->
        <div class="bg-surface-container-lowest border border-border-subtle shadow-sm rounded-xl p-5 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-shadow">
            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-2">NĂM HỌC ĐANG XEM</span>
            <div class="flex items-baseline">
                <span id="statYear" class="text-2xl font-bold text-accent-purple leading-tight">—</span>
            </div>
        </div>
    </section>

    <!-- Navigation Tabs -->
    <nav class="bg-surface-container-lowest border border-border-subtle rounded-xl shadow-sm px-2 flex overflow-x-auto" id="profileTabs">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-btn flex items-center gap-2 px-6 py-4 text-xs font-semibold text-secondary border-b-2 border-secondary whitespace-nowrap bg-secondary/5 transition-colors">
            <span class="material-symbols-outlined text-[18px]">grid_view</span>
            <span>Tổng quan</span>
        </button>
        <button onclick="switchTab('personal')" id="tab-personal" class="tab-btn flex items-center gap-2 px-6 py-4 text-xs font-semibold text-slate-500 hover:text-text-main hover:bg-surface-container-low transition-colors whitespace-nowrap border-b-2 border-transparent">
            <span class="material-symbols-outlined text-[18px]">person</span>
            <span>Thông tin cá nhân</span>
        </button>
        <button onclick="switchTab('academic')" id="tab-academic" class="tab-btn flex items-center gap-2 px-6 py-4 text-xs font-semibold text-slate-500 hover:text-text-main hover:bg-surface-container-low transition-colors whitespace-nowrap border-b-2 border-transparent">
            <span class="material-symbols-outlined text-[18px]">school</span>
            <span>Thông tin học tập</span>
        </button>
        <button onclick="switchTab('grades')" id="tab-grades" class="tab-btn flex items-center gap-2 px-6 py-4 text-xs font-semibold text-slate-500 hover:text-text-main hover:bg-surface-container-low transition-colors whitespace-nowrap border-b-2 border-transparent">
            <span class="material-symbols-outlined text-[18px]">grade</span>
            <span>Kết quả điểm số</span>
        </button>
        <button onclick="switchTab('history')" id="tab-history" class="tab-btn flex items-center gap-2 px-6 py-4 text-xs font-semibold text-slate-500 hover:text-text-main hover:bg-surface-container-low transition-colors whitespace-nowrap border-b-2 border-transparent">
            <span class="material-symbols-outlined text-[18px]">history_edu</span>
            <span>Lịch sử học tập</span>
        </button>
    </nav>

    <!-- TAB 1: TỔNG QUAN -->
    <div id="content-overview" class="tab-pane">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Left Column (Grades Table) -->
            <div class="lg:col-span-2 flex flex-col gap-4">
                <div class="bg-surface-container-lowest border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                    <!-- Header -->
                    <div class="p-5 border-b border-border-subtle flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-bg">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h2 class="text-base font-bold text-text-main">Bảng Điểm Môn Học Tóm Tắt</h2>
                                <span id="overviewBadgeYear" class="bg-green-50 text-secondary border border-green-200 px-2 py-0.5 rounded text-[11px] font-semibold">
                                    Lớp 2 (Năm học 2025 - 2026)
                                </span>
                            </div>
                            <p class="text-xs text-text-muted">Kết quả các lần đánh giá điểm theo năm học đang chọn</p>
                        </div>
                        <button onclick="switchTab('grades')" class="text-secondary text-xs font-semibold hover:underline flex items-center gap-1 group">
                            <span>Xem chi tiết</span>
                            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low border-b border-border-subtle text-xs font-semibold text-text-muted uppercase tracking-wider">
                                    <th class="p-4 w-16 text-center">STT</th>
                                    <th class="p-4">Môn Học</th>
                                    <th class="p-4 text-center">Điểm Số</th>
                                    <th class="p-4 text-center">Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody id="overviewGradeTableBody" class="text-sm text-text-main divide-y divide-border-subtle font-medium">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column (Sidebar Info) -->
            <div class="flex flex-col gap-4">
                <div class="bg-surface-container-lowest border border-border-subtle rounded-xl shadow-sm p-5 h-full">
                    <h2 class="text-base font-bold text-text-main mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                        <span>Thông Tin Lớp &amp; GVCN</span>
                    </h2>

                    <div class="flex flex-col gap-3.5">
                        <!-- Info Item 1: Lớp Học -->
                        <div class="bg-surface-bg border border-border-subtle rounded-lg p-3.5 hover:border-slate-400 transition-colors group">
                            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider block mb-1 group-hover:text-primary transition-colors">Lớp Học</span>
                            <span id="overviewClassName" class="text-lg font-bold text-text-main block">Lớp 2</span>
                        </div>

                        <!-- Info Item 2: Giáo Viên Chủ Nhiệm -->
                        <div class="bg-surface-bg border border-border-subtle rounded-lg p-3.5 hover:border-slate-400 transition-colors group flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold text-text-muted uppercase tracking-wider block mb-1 group-hover:text-secondary transition-colors">Giáo Viên Chủ Nhiệm</span>
                                <span id="overviewTeacherName" class="text-base font-bold text-secondary block">Cô Lê Thị Bích</span>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
                                <span class="material-symbols-outlined text-[18px]">face</span>
                            </div>
                        </div>

                        <!-- Info Item 3: Niên Khóa -->
                        <div class="bg-surface-bg border border-border-subtle rounded-lg p-3.5 hover:border-slate-400 transition-colors group">
                            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider block mb-1 group-hover:text-accent-purple transition-colors">Niên Khóa</span>
                            <span id="overviewYearName" class="text-sm font-semibold text-text-main block">Năm học 2025 – 2026</span>
                        </div>

                        <!-- Info Item 4: Kết Quả Đào Tạo -->
                        <div class="bg-primary/5 border border-primary/20 rounded-lg p-3.5">
                            <span class="text-xs font-semibold text-primary uppercase tracking-wider block mb-1">Kết Quả Đào Tạo</span>
                            <span id="overviewStatus" class="text-sm font-bold text-primary block flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                <span>Đang theo học</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: THÔNG TIN CÁ NHÂN -->
    <div id="content-personal" class="tab-pane hidden bg-surface-container-lowest border border-border-subtle rounded-xl shadow-sm p-6">
        <h3 class="text-base font-bold text-text-main mb-6">Thông Tin Cá Nhân</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-xs">
            <div>
                <label class="text-text-muted font-bold block mb-1">Mã Học Sinh</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-mono font-bold text-secondary text-sm"><?= htmlspecialchars($student['student_code']) ?></div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Họ và Tên</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-bold text-text-main text-sm"><?= htmlspecialchars($student['full_name']) ?></div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Giới Tính</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-semibold"><?= $student['gender'] === 'female' ? 'Nữ' : 'Nam' ?></div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Ngày Sinh</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-mono font-semibold"><?= !empty($student['dob']) ? date('d/m/Y', strtotime($student['dob'])) : 'Chưa cập nhật' ?></div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Nơi Sinh</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg"><?= htmlspecialchars($student['pob'] ?? 'Chưa cập nhật') ?></div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Số Điện Thoại</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-mono"><?= htmlspecialchars($student['phone'] ?? 'Chưa cập nhật') ?></div>
            </div>
            <div class="md:col-span-2">
                <label class="text-text-muted font-bold block mb-1">Địa Chỉ Thường Trú</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg"><?= htmlspecialchars($student['address'] ?? 'Chưa cập nhật') ?></div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Email Liên Hệ</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg"><?= htmlspecialchars($student['email'] ?? 'Chưa cập nhật') ?></div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Ngày Nhập Học</label>
                <div class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-mono"><?= !empty($student['admission_date']) ? date('d/m/Y', strtotime($student['admission_date'])) : 'Chưa cập nhật' ?></div>
            </div>
        </div>
    </div>

    <!-- TAB 3: THÔNG TIN HỌC TẬP -->
    <div id="content-academic" class="tab-pane hidden bg-surface-container-lowest border border-border-subtle rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-text-main">Thông Tin Quản Lý Học Tập</h3>
            <span id="academicBadgeYear" class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-accent-purple"></span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-xs">
            <div>
                <label class="text-text-muted font-bold block mb-1">Năm Học</label>
                <div id="academicYearName" class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-semibold">—</div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Lớp Học</label>
                <div id="academicClassName" class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-bold text-text-main">—</div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Giáo Viên Chủ Nhiệm</label>
                <div id="academicTeacherName" class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-bold text-secondary">—</div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Kết Quả Đào Tạo / Lên Lớp</label>
                <div id="academicStatus" class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-bold text-primary">—</div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Xếp Loại Học Lực</label>
                <div id="academicRank" class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-semibold text-text-main">—</div>
            </div>
            <div>
                <label class="text-text-muted font-bold block mb-1">Số Môn Học Trong Năm</label>
                <div id="academicSubjectCount" class="p-3 bg-surface-bg border border-border-subtle rounded-lg font-semibold text-text-main">—</div>
            </div>
        </div>
    </div>

    <!-- TAB 4: KẾT QUẢ ĐIỂM SỐ -->
    <div id="content-grades" class="tab-pane hidden bg-surface-container-lowest border border-border-subtle rounded-xl shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-base font-bold text-text-main">Bảng Điểm Chi Tiết</h3>
                    <span id="gradesBadgeYear" class="bg-green-50 text-secondary border border-green-200 px-2 py-0.5 rounded text-[11px] font-semibold"></span>
                </div>
                <p class="text-xs text-text-muted">Điểm tổng kết theo năm học đang chọn</p>
            </div>
            <div class="p-3 px-4 rounded-lg bg-surface-bg border border-border-subtle flex items-center gap-3">
                <span class="text-xs text-text-muted font-semibold">Tổng Điểm Các Môn:</span>
                <span id="gradesGpaScore" class="font-bold text-secondary text-lg">—</span>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-border-subtle">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-border-subtle text-xs font-semibold text-text-muted uppercase tracking-wider">
                        <th class="p-4 w-16 text-center">STT</th>
                        <th class="p-4">Môn Học</th>
                        <th class="p-4 text-center">Điểm Số</th>
                        <th class="p-4 text-center">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody id="gradesTableBody" class="text-sm text-text-main divide-y divide-border-subtle font-medium">
                    <!-- Populated dynamically by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 5: LỊCH SỬ HỌC TẬP -->
    <div id="content-history" class="tab-pane hidden space-y-6">
        <div class="bg-surface-container-lowest border border-border-subtle rounded-xl shadow-sm p-6">
            <div class="mb-6">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">history_edu</span>
                    <span>Toàn Bộ Quá Trình Học Tập Từ Mẫu Giáo Đến Hiện Tại</span>
                </h3>
                <p class="text-xs text-text-muted mt-1">Tổng hợp quá trình theo học qua từng cấp lớp. Bạn có thể chọn bất kỳ năm nào từ Combobox trên đầu trang để xem chi tiết.</p>
            </div>

            <!-- Summary Table of All Stages -->
            <div class="overflow-x-auto rounded-lg border border-border-subtle">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-border-subtle text-text-muted uppercase font-semibold text-[11px] tracking-wider">
                            <th class="p-4">Giai Đoạn / Cấp Lớp</th>
                            <th class="p-4">Năm Học</th>
                            <th class="p-4">Lớp Học</th>
                            <th class="p-4">Giáo Viên Chủ Nhiệm</th>
                            <th class="p-4 text-center">Tổng Điểm</th>
                            <th class="p-4 text-center">Xếp Loại</th>
                            <th class="p-4">Kết Quả Cuối Năm</th>
                            <th class="p-4 text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle font-medium">
                        <?php foreach ($historyList as $stage): ?>
                        <tr class="<?= !empty($stage['is_current']) ? 'bg-green-50/30' : '' ?>">
                            <td class="p-4 font-bold text-text-main flex items-center gap-2">
                                <span><?= htmlspecialchars($stage['title']) ?></span>
                                <?php if (!empty($stage['is_current'])): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-300 text-slate-900">
                                    ★ Hiện tại
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-slate-600"><?= htmlspecialchars($stage['year_name']) ?></td>
                            <td class="p-4 font-bold text-secondary"><?= htmlspecialchars($stage['class_name']) ?></td>
                            <td class="p-4 text-text-main"><?= htmlspecialchars($stage['teacher_name']) ?></td>
                            <td class="p-4 text-center font-bold text-secondary">
                                <?= $stage['avg_score'] !== null ? number_format($stage['avg_score'], 1) : '<span class="text-slate-400 italic font-normal">Chưa có</span>' ?>
                            </td>
                            <td class="p-4 text-center">
                                <span class="font-semibold"><?= htmlspecialchars($stage['rank'] ?? '—') ?></span>
                            </td>
                            <td class="p-4 font-bold <?= !empty($stage['is_current']) ? 'text-secondary' : 'text-primary' ?>">
                                <?= htmlspecialchars($stage['promotion_status']) ?>
                            </td>
                            <td class="p-4 text-center">
                                <button type="button" 
                                        onclick="selectYearFromTable('<?= $stage['stage_key'] ?>')"
                                        class="px-3 py-1 bg-surface-container-low hover:bg-secondary hover:text-white text-text-main rounded-md text-xs font-semibold transition-all">
                                    Xem năm này
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    // Global academic history data passed from PHP
    const ACADEMIC_STAGES = <?= json_encode($historyList, JSON_UNESCAPED_UNICODE) ?>;
    let currentStageKey = '<?= $currentStageKey ?>';

    // Global year switcher: Updates header, stats, and all tabs to the selected year
    function changeGlobalYear(stageKey) {
        currentStageKey = stageKey;
        const stage = ACADEMIC_STAGES.find(s => s.stage_key === stageKey);
        if (!stage) return;

        // Group grades by subject first so subjectList is always initialized
        const subjectMap = {};
        if (stage.grades && stage.grades.length > 0) {
            stage.grades.forEach(gr => {
                if (!subjectMap[gr.subject_name]) {
                    subjectMap[gr.subject_name] = {
                        name: gr.subject_name,
                        scores: [],
                        is_draft: false
                    };
                }
                if (gr.score !== null && gr.score !== undefined) {
                    subjectMap[gr.subject_name].scores.push(Number(gr.score));
                }
                if (gr.is_draft) {
                    subjectMap[gr.subject_name].is_draft = true;
                }
            });
        }
        const subjectList = Object.values(subjectMap).map(sub => {
            const avg = sub.scores.length > 0 
                ? (sub.scores.reduce((a, b) => a + b, 0) / sub.scores.length) 
                : null;
            return {
                name: sub.name,
                avg: avg !== null ? Number(avg.toFixed(1)) : null,
                is_draft: sub.is_draft
            };
        });

        // 1. Sync global combobox
        const sel = document.getElementById('globalYearSelect');
        if (sel && sel.value !== stageKey) sel.value = stageKey;

        // 2. Update Header Class Name & Print Header
        const headerClass = document.getElementById('headerClassName');
        if (headerClass) headerClass.innerText = stage.class_name;
        const printClass = document.getElementById('printClassName');
        if (printClass) printClass.innerText = stage.class_name;
        const printYear = document.getElementById('printYearName');
        if (printYear) printYear.innerText = stage.year_name;

        // Calculate TOTAL points across all subjects (KHÔNG CHIA TRUNG BÌNH)
        // Ví dụ 5 môn -> Tổng tối đa 50 điểm. 10 môn -> Tổng tối đa 100 điểm.
        let totalSumScore = 0;
        let validSubjectCount = 0;
        subjectList.forEach(sub => {
            if (sub.avg !== null) {
                totalSumScore += sub.avg;
                validSubjectCount++;
            }
        });
        totalSumScore = Number(totalSumScore.toFixed(1));
        const maxTotalScore = validSubjectCount * 10;
        const passThreshold = maxTotalScore / 2; // Mốc 1/2 tổng điểm
        const isOverallPass = validSubjectCount > 0 && totalSumScore >= passThreshold;

        // 3. Update Quick Stats Cards (Matching the 3 cards in mockup)
        const statGpa = document.getElementById('statGpa');
        if (statGpa) {
            if (validSubjectCount > 0) {
                statGpa.innerHTML = `
                    <span class="text-4xl font-bold text-secondary">${totalSumScore.toFixed(1)}</span>
                    <span class="text-lg font-semibold text-slate-400">/ ${maxTotalScore}</span>
                `;
            } else {
                statGpa.innerHTML = `<span class="text-xs font-bold text-slate-400 italic">Chưa có dữ liệu</span>`;
            }
        }

        const distinctSubj = validSubjectCount;
        const statSubjects = document.getElementById('statSubjects');
        if (statSubjects) {
            if (distinctSubj > 0) {
                statSubjects.innerHTML = `
                    <span class="text-4xl font-bold text-primary">${distinctSubj}</span>
                    <span class="text-base font-semibold text-slate-500">môn</span>
                `;
            } else {
                statSubjects.innerHTML = `<span class="text-xs font-bold text-slate-400 italic">Chưa có dữ liệu</span>`;
            }
        }

        // Update Stat Rank (Card 3: Xếp Hạng - Chỉ hiển thị Hạng số)
        const statRank = document.getElementById('statRank');
        if (statRank) {
            if (stage.class_rank) {
                statRank.innerHTML = `<span class="text-4xl font-bold text-amber-500">Hạng ${stage.class_rank}</span>`;
            } else if (validSubjectCount > 0) {
                const avg = totalSumScore / validSubjectCount;
                let rText = 'Hạng 1';
                if (avg < 5.0) { rText = 'Hạng ' + (stage.class_total || 10); }
                else if (avg < 6.5) { rText = 'Hạng 5'; }
                else if (avg < 8.0) { rText = 'Hạng 3'; }
                statRank.innerHTML = `<span class="text-4xl font-bold text-amber-500">${rText}</span>`;
            } else if (stage.rank) {
                statRank.innerHTML = `<span class="text-3xl font-bold text-amber-500">${stage.rank}</span>`;
            } else {
                statRank.innerHTML = `<span class="text-xs font-bold text-slate-400 italic">Chưa xếp hạng</span>`;
            }
        }

        const statYear = document.getElementById('statYear');
        if (statYear) statYear.innerText = stage.year_name;

        // 4. Update Overview Tab (Tab 1)
        const obYear = document.getElementById('overviewBadgeYear');
        if (obYear) obYear.innerText = stage.title + ' (' + stage.year_name + ')';
        const ovClass = document.getElementById('overviewClassName');
        if (ovClass) ovClass.innerText = stage.class_name;
        const ovTeacher = document.getElementById('overviewTeacherName');
        if (ovTeacher) ovTeacher.innerText = stage.teacher_name || 'Chưa phân công';
        const ovYear = document.getElementById('overviewYearName');
        if (ovYear) ovYear.innerText = stage.year_name;
        const ovStatus = document.getElementById('overviewStatus');
        if (ovStatus) {
            ovStatus.innerHTML = `<span class="w-2 h-2 rounded-full bg-primary animate-pulse inline-block"></span><span>${stage.promotion_status}</span>`;
        }

        // Render Overview Grades Table (Consolidated by Subject: STT | MÔN HỌC | ĐIỂM SỐ | TRẠNG THÁI)
        const ovTbody = document.getElementById('overviewGradeTableBody');
        if (ovTbody) {
            if (subjectList.length > 0) {
                let html = '';
                subjectList.forEach((sub, idx) => {
                    const score = sub.avg !== null ? sub.avg.toFixed(1) : '—';
                    const isPass = sub.avg !== null && sub.avg >= 5.0;
                    const scoreColor = sub.avg >= 8.0 ? 'text-secondary' : 'text-primary';
                    const statusText = sub.avg !== null ? (isPass ? 'Đậu' : 'Rớt') : 'Chưa có điểm';
                    const statusClass = sub.avg !== null 
                        ? (isPass 
                            ? 'bg-green-100 text-green-800 border-green-200' 
                            : 'bg-rose-100 text-rose-800 border-rose-200') 
                        : 'bg-slate-100 text-slate-500';
                    html += `
                    <tr class="table-row-hover transition-all duration-200 bg-surface-container-lowest">
                        <td class="p-4 text-center text-slate-500">${idx + 1}</td>
                        <td class="p-4 font-semibold text-text-main">${escapeHtml(sub.name)}</td>
                        <td class="p-4 text-center font-bold ${scoreColor}">${score}</td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[12px] font-medium border ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                    </tr>`;
                });

                // Dòng TỔNG CỘNG TẤT CẢ CÁC MÔN (Tổng điểm cộng dồn không chia)
                if (validSubjectCount > 0) {
                    html += `
                    <tr class="bg-surface-container-low border-t-2 border-border-subtle font-bold">
                        <td colspan="2" class="p-4 text-text-main text-xs uppercase tracking-wider">
                            Tổng Điểm Tất Cả Các Môn (${validSubjectCount} Môn):
                        </td>
                        <td class="p-4 text-center font-bold text-base ${isOverallPass ? 'text-secondary' : 'text-rose-600'}">
                            ${totalSumScore.toFixed(1)} <span class="text-xs font-normal text-slate-400">/ ${maxTotalScore}</span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold shadow-sm ${isOverallPass ? 'bg-secondary text-white' : 'bg-rose-600 text-white'}">
                                ${isOverallPass ? 'ĐẬU' : 'RỚT'}
                            </span>
                        </td>
                    </tr>`;
                }
                ovTbody.innerHTML = html;
            } else {
                ovTbody.innerHTML = `<tr><td colspan="4" class="p-8 text-center text-slate-400 italic">Chưa có dữ liệu điểm cho năm học này</td></tr>`;
            }
        }

        // 5. Update Academic Tab (Tab 3)
        const acbYear = document.getElementById('academicBadgeYear');
        if (acbYear) acbYear.innerText = stage.year_name;
        const acYear = document.getElementById('academicYearName');
        if (acYear) acYear.innerText = stage.year_name;
        const acClass = document.getElementById('academicClassName');
        if (acClass) acClass.innerText = stage.class_name;
        const acTeacher = document.getElementById('academicTeacherName');
        if (acTeacher) acTeacher.innerText = stage.teacher_name || 'Chưa phân công';
        const acStatus = document.getElementById('academicStatus');
        if (acStatus) {
            acStatus.innerText = validSubjectCount > 0 ? (isOverallPass ? 'Đạt chuẩn lên lớp (ĐẬU)' : 'Chưa đạt chuẩn (RỚT)') : stage.promotion_status;
            acStatus.className = isOverallPass ? 'p-3 bg-surface-bg border border-border-subtle rounded-lg font-bold text-secondary' : 'p-3 bg-surface-bg border border-border-subtle rounded-lg font-bold text-rose-600';
        }
        const acRank = document.getElementById('academicRank');
        if (acRank) acRank.innerText = stage.rank || 'Chưa đánh giá';
        const acSubj = document.getElementById('academicSubjectCount');
        if (acSubj) acSubj.innerText = distinctSubj > 0 ? (distinctSubj + ' môn') : 'Chưa có dữ liệu';

        // 6. Update Grades Tab (Tab 4)
        const grBadge = document.getElementById('gradesBadgeYear');
        if (grBadge) grBadge.innerText = stage.title + ' (' + stage.year_name + ')';
        const grGpa = document.getElementById('gradesGpaScore');
        if (grGpa) {
            grGpa.innerText = validSubjectCount > 0 ? `${totalSumScore.toFixed(1)} / ${maxTotalScore}` : 'Chưa có điểm';
        }
        const grTbody = document.getElementById('gradesTableBody');
        if (grTbody) {
            if (subjectList.length > 0) {
                let html = '';
                subjectList.forEach((sub, idx) => {
                    const score = sub.avg !== null ? sub.avg.toFixed(1) : '—';
                    const isPass = sub.avg !== null && sub.avg >= 5.0;
                    const scoreColor = sub.avg >= 8.0 ? 'text-secondary' : 'text-primary';
                    const statusText = sub.avg !== null ? (isPass ? 'Đậu' : 'Rớt') : 'Chưa có điểm';
                    const statusClass = sub.avg !== null 
                        ? (isPass 
                            ? 'bg-green-100 text-green-800 border-green-200' 
                            : 'bg-rose-100 text-rose-800 border-rose-200') 
                        : 'bg-slate-100 text-slate-500';
                    html += `
                    <tr class="table-row-hover transition-all duration-200 bg-surface-container-lowest">
                        <td class="p-4 text-center text-slate-500">${idx + 1}</td>
                        <td class="p-4 font-semibold text-text-main">${escapeHtml(sub.name)}</td>
                        <td class="p-4 text-center font-bold ${scoreColor}">${score}</td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[12px] font-medium border ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                    </tr>`;
                });

                // Dòng TỔNG CỘNG TẤT CẢ CÁC MÔN
                if (validSubjectCount > 0) {
                    html += `
                    <tr class="bg-surface-container-low border-t-2 border-border-subtle font-bold">
                        <td colspan="2" class="p-4 text-text-main text-xs uppercase tracking-wider">
                            Tổng Điểm Tất Cả Các Môn (${validSubjectCount} Môn):
                        </td>
                        <td class="p-4 text-center font-bold text-base ${isOverallPass ? 'text-secondary' : 'text-rose-600'}">
                            ${totalSumScore.toFixed(1)} <span class="text-xs font-normal text-slate-400">/ ${maxTotalScore}</span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold shadow-sm ${isOverallPass ? 'bg-secondary text-white' : 'bg-rose-600 text-white'}">
                                ${isOverallPass ? 'ĐẬU' : 'RỚT'}
                            </span>
                        </td>
                    </tr>`;
                }
                grTbody.innerHTML = html;
            } else {
                grTbody.innerHTML = `<tr><td colspan="4" class="p-8 text-center text-slate-400 italic">Chưa có dữ liệu điểm cho năm học này</td></tr>`;
            }
        }
    }

    // Helper to select year from table in Tab 5
    function selectYearFromTable(stageKey) {
        changeGlobalYear(stageKey);
        switchTab('overview');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    // Tab switcher with secondary active styling
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-secondary', 'border-secondary', 'bg-secondary/5');
            btn.classList.add('text-slate-500', 'border-transparent');
        });
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
        });

        const activeBtn = document.getElementById('tab-' + tabId);
        const activePane = document.getElementById('content-' + tabId);

        if (activeBtn && activePane) {
            activeBtn.classList.add('text-secondary', 'border-secondary', 'bg-secondary/5');
            activeBtn.classList.remove('text-slate-500', 'border-transparent');
            activePane.classList.remove('hidden');
        }
    }

    // Initialize global view on page load with default current year
    window.addEventListener('DOMContentLoaded', () => {
        changeGlobalYear(currentStageKey);
    });
</script>
