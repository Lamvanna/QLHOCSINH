<!DOCTYPE html>
<html lang="vi" class="min-h-screen w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appSettings['school_name'] ?? APP_NAME) ?></title>
    
    <!-- Google Fonts Inter & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#3b82f6',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            800: '#00288e',
                            900: '#0b1c30'
                        },
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'monospace']
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(0, 40, 142, 0.06)',
                        'card': '0 2px 12px -2px rgba(11, 28, 48, 0.06), 0 1px 4px -1px rgba(11, 28, 48, 0.04)',
                        'card-hover': '0 12px 24px -4px rgba(11, 28, 48, 0.1), 0 4px 8px -2px rgba(11, 28, 48, 0.05)',
                        'glow-primary': '0 0 20px -4px rgba(0, 40, 142, 0.35)',
                        'glow-emerald': '0 0 20px -4px rgba(16, 185, 129, 0.35)'
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons & Chart.js & SheetJS -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    
    <style>
        @media print { aside, header, nav, #toast-container { display: none !important; } main { margin: 0 !important; padding: 0 !important; width: 100% !important; } }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8faff;
            color: #0b1c30;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        html { width: 100%; }
        *, *::before, *::after { box-sizing: border-box; }
        .dark body {
            background-color: #0b1120;
            color: #f1f5f9;
        }
        
        .material-symbols-outlined { 
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24; 
            vertical-align: middle;
        }
        .material-symbols-filled { 
            font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24; 
            vertical-align: middle;
        }

        /* Glassmorphism Classes */
        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .dark .glass-header {
            background: rgba(15, 23, 42, 0.85);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(51, 65, 85, 0.8);
        }

        /* Custom Modern Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Micro-animations */
        .hover-lift {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -4px rgba(11, 28, 48, 0.1);
        }

        /* Form elements polish */
        input:focus, select:focus, textarea:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
            border-color: #10b981 !important;
        }

        .card-subtle {
            transition: all 0.25s ease-in-out;
        }
        .card-subtle:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 40, 142, 0.08), 0 8px 10px -6px rgba(0, 40, 142, 0.04);
            border-color: rgba(16, 185, 129, 0.3);
        }

        /* Premium Table Styles */
        .table-premium { border-collapse: separate; border-spacing: 0; }
        .table-premium tr { transition: background-color 0.15s ease; }
        .table-premium tbody tr:hover { background-color: rgba(16, 185, 129, 0.03); }
        .dark .table-premium tbody tr:hover { background-color: rgba(16, 185, 129, 0.05); }

        /* Gradient Button */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
            background-size: 200% auto;
            color: white;
            font-weight: 700;
            border-radius: 12px;
            transition: background-position 0.4s ease, box-shadow 0.3s ease, transform 0.15s ease;
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.35);
        }
        .btn-gradient-primary:hover {
            background-position: right center;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
            transform: translateY(-1px);
        }
        .btn-gradient-primary:active { transform: scale(0.97); }

        /* Fade-in Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .fade-in-up-delay-1 { animation-delay: 0.05s; }
        .fade-in-up-delay-2 { animation-delay: 0.10s; }
        .fade-in-up-delay-3 { animation-delay: 0.15s; }

        /* Badge Pills */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-emerald { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-blue    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-amber   { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-rose    { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
        .badge-slate   { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .dark .badge-emerald { background: rgba(16,185,129,0.15); color: #6ee7b7; border-color: rgba(16,185,129,0.25); }
        .dark .badge-blue    { background: rgba(59,130,246,0.15); color: #93c5fd; border-color: rgba(59,130,246,0.25); }
        .dark .badge-amber   { background: rgba(245,158,11,0.15); color: #fcd34d; border-color: rgba(245,158,11,0.25); }
        .dark .badge-rose    { background: rgba(244,63,94,0.15);  color: #fda4af; border-color: rgba(244,63,94,0.25);  }
        .dark .badge-slate   { background: rgba(100,116,139,0.15);color: #94a3b8; border-color: rgba(100,116,139,0.25);}

        /* Skeleton loading effect */
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 8px;
        }
        .dark .skeleton {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 200% 100%;
        }

        /* Smooth page sections */
        main > div > * { animation: fadeInUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) both; }
    </style>
</head>
<body class="min-h-screen w-full bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 antialiased flex flex-col">

    <!-- Role Switcher Quick Bar (for pairwise developer/user testing) -->
    <div class="bg-indigo-900 text-indigo-100 text-xs px-4 py-1.5 flex items-center justify-between z-50">
        <div class="flex items-center space-x-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="font-semibold uppercase tracking-wider">Hệ Thống Đang Chạy:</span>
            <span class="bg-indigo-800 px-2 py-0.5 rounded font-mono font-bold text-white"><?= htmlspecialchars($currentUser['role_display'] ?? 'Guest') ?></span>
            <span class="text-indigo-300 hidden md:inline">| Tài khoản: <strong class="text-white"><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></strong> (<?= htmlspecialchars($currentUser['username'] ?? '') ?>)</span>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-indigo-300 hidden sm:inline">Chuyển vai trò thử nghiệm:</span>
            <a href="<?= BASE_URL ?>/switch-role?role=admin" class="px-2 py-0.5 rounded <?= Auth::isAdmin() ? 'bg-emerald-500 text-white font-bold' : 'bg-indigo-800 hover:bg-indigo-700' ?>">Super Admin</a>
            <a href="<?= BASE_URL ?>/switch-role?role=teacher" class="px-2 py-0.5 rounded <?= Auth::isTeacher() ? 'bg-emerald-500 text-white font-bold' : 'bg-indigo-800 hover:bg-indigo-700' ?>">Giáo Viên</a>
            <a href="<?= BASE_URL ?>/switch-role?role=student" class="px-2 py-0.5 rounded <?= Auth::isStudent() ? 'bg-emerald-500 text-white font-bold' : 'bg-indigo-800 hover:bg-indigo-700' ?>">Học Sinh</a>
        </div>
    </div>

    <div class="flex flex-1 min-h-0 w-full">
        <!-- Sidebar -->
        <?php require __DIR__ . '/sidebar.php'; ?>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 w-full">
            <!-- Header -->
            <?php require __DIR__ . '/header.php'; ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-7 w-full">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-2 max-w-sm pointer-events-none"></div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Dark / Light Mode handler
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Global Toast Notification Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center p-4 rounded-xl shadow-lg border text-sm transition-all transform duration-300 translate-y-2 opacity-0 ${
                type === 'success' 
                    ? 'bg-emerald-50 text-emerald-900 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-100 dark:border-emerald-800' 
                    : 'bg-rose-50 text-rose-900 border-rose-200 dark:bg-rose-950 dark:text-rose-100 dark:border-rose-800'
            }`;

            const icon = type === 'success' ? 'check-circle-2' : 'alert-circle';
            toast.innerHTML = `
                <i data-lucide="${icon}" class="w-5 h-5 mr-3 flex-shrink-0 ${type === 'success' ? 'text-emerald-600' : 'text-rose-600'}"></i>
                <div class="flex-1 font-medium">${message}</div>
                <button onclick="this.parentElement.remove()" class="ml-3 text-slate-400 hover:text-slate-600">&times;</button>
            `;

            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Global API Request Helper
        async function apiPost(url, data) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });
                const rawText = await response.text();
                // Strip UTF-8 BOM if present
                const cleanText = rawText.replace(/^\uFEFF/, '').trim();
                let result;
                try {
                    result = JSON.parse(cleanText);
                } catch (jsonErr) {
                    showToast('Lỗi phân tích dữ liệu máy chủ: ' + jsonErr.message, 'error');
                    return null;
                }
                if (!response.ok || !result.success) {
                    showToast(result.message || 'Đã xảy ra lỗi!', 'error');
                    return null;
                }
                showToast(result.message || 'Thao tác thành công!', 'success');
                return result.data || true;
            } catch (err) {
                showToast('Lỗi kết nối máy chủ: ' + err.message, 'error');
                return null;
            }
        }
    </script>
</body>
</html>
