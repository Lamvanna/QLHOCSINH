<?php // views/semesters/locked.php ?>
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6">
    <!-- Breadcrumb & Tag -->
    <div class="flex items-center justify-center mb-6">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200/80 dark:border-amber-800/60 shadow-sm">
            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
            TÍNH NĂNG TẠM KHÓA
        </span>
    </div>

    <!-- Main Card -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 sm:p-12 shadow-xl text-center relative overflow-hidden">
        <!-- Background Ambient Glow -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Lock Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white shadow-lg shadow-amber-500/25 mb-6">
            <i data-lucide="calendar-off" class="w-10 h-10 stroke-[1.75]"></i>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
            Phần Quản Lý Học Kỳ Đang Tạm Khóa
        </h1>

        <p class="text-slate-600 dark:text-slate-400 max-w-xl mx-auto text-sm sm:text-base leading-relaxed mb-8">
            Chức năng Quản lý Học kỳ hiện đang được <strong class="text-slate-800 dark:text-slate-200">tạm ẩn khỏi hệ thống</strong> theo yêu cầu quản trị. Toàn bộ cấu trúc cơ sở dữ liệu, giao diện Bento Grid và các hàm xử lý học vụ đã được hoàn thiện và bảo lưu an toàn.
        </p>

        <!-- Information Bento Strip -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl mx-auto mb-8 text-left">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-semibold mb-1">
                    <i data-lucide="shield-check" class="w-4 h-4 text-teal-600 dark:text-teal-400"></i>
                    Dữ liệu an toàn
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">Các học kỳ đã tạo được lưu trữ nguyên vẹn trong CSDL.</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-semibold mb-1">
                    <i data-lucide="eye-off" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                    Menu đã ẩn
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">Thanh điều hướng Sidebar đã ẩn mục Học kỳ gọn gàng.</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-semibold mb-1">
                    <i data-lucide="zap" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                    Sẵn sàng mở lại
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">Bất kỳ khi nào cần phát triển, chỉ cần kích hoạt lại một chạm.</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-3.5">
            <a href="<?= BASE_URL ?>/academic-years" class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-2xl font-bold text-sm shadow-lg shadow-teal-600/20 hover:shadow-teal-600/30 transition-all">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                Đến Quản Lý Năm Học
            </a>

            <a href="<?= BASE_URL ?>/dashboard" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-2xl font-semibold text-sm transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Về Trang Chủ Dashboard
            </a>
        </div>
    </div>
</div>
