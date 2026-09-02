<?php
// views/layouts/header.php
?>
<header class="h-16 glass-header sticky top-0 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between px-4 md:px-8 z-30 flex-shrink-0">
    <!-- Left: Global Search & Breadcrumb -->
    <div class="flex items-center space-x-4">
        <!-- Global Search Bar -->
        <div class="relative hidden sm:block w-72 md:w-96">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input type="text" id="globalSearchInput" placeholder="Tìm kiếm học sinh, giáo viên, lớp học..." 
                   class="w-full pl-10 pr-12 py-2 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700/60 rounded-full text-xs font-medium focus:ring-2 focus:ring-emerald-500 dark:text-white placeholder-slate-400 transition-all shadow-inner">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-mono text-slate-400 border border-slate-300 dark:border-slate-700 px-1.5 py-0.5 rounded-md bg-white dark:bg-slate-900 hidden md:inline">⌘K</span>
        </div>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center space-x-2.5">
        <!-- Language Switcher -->
        <div class="relative">
            <select id="langSelector" class="bg-slate-100/80 dark:bg-slate-800/80 text-xs font-semibold text-slate-700 dark:text-slate-200 py-1.5 px-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60 focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                <option value="vi">🇻🇳 Tiếng Việt</option>
                <option value="en">🇬🇧 English</option>
                <option value="km">🇰🇭 ភាសាខ្មែរ</option>
            </select>
        </div>

        <!-- Dark / Light Theme Toggle -->
        <button onclick="toggleTheme()" class="p-2 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all active:scale-95" title="Đổi giao diện Sáng / Tối">
            <span class="material-symbols-outlined text-[20px] hidden dark:block text-amber-400">light_mode</span>
            <span class="material-symbols-outlined text-[20px] block dark:hidden text-slate-600">dark_mode</span>
        </button>

        <div class="h-5 w-[1px] bg-slate-200 dark:border-slate-700 mx-1"></div>

        <!-- Profile Link -->
        <a href="<?= BASE_URL ?>/profile" class="flex items-center space-x-2.5 pl-1.5 pr-3 py-1 rounded-full bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 transition-all group">
            <div class="relative">
                <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150') ?>" class="w-7 h-7 rounded-full object-cover border border-slate-200 dark:border-slate-700" alt="Avatar">
                <span class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900"></span>
            </div>
            <div class="hidden md:flex flex-col text-left">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 transition-colors leading-none"><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></span>
                <span class="text-[10px] text-slate-400 mt-0.5 leading-none font-medium"><?= htmlspecialchars(Auth::role()) ?></span>
            </div>
        </a>
    </div>
</header>
