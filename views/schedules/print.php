<?php
// views/schedules/print.php - Multi-page A4 Landscape Print for Timetable / Schedule
$className = $currentClass['name'] ?? 'Lớp 1';
$semesterName = $currentSemester['name'] ?? 'Học kỳ I';

$days = [
    2 => 'Thứ Hai',
    3 => 'Thứ Ba',
    4 => 'Thứ Tư',
    5 => 'Thứ Năm',
    6 => 'Thứ Sáu',
    7 => 'Thứ Bảy'
];

$periods = [
    1 => ['name' => 'Tiết 1', 'time' => '07:15 - 08:00', 'session' => 'morning'],
    2 => ['name' => 'Tiết 2', 'time' => '08:05 - 08:50', 'session' => 'morning'],
    3 => ['name' => 'Tiết 3', 'time' => '09:10 - 09:55', 'session' => 'morning'],
    4 => ['name' => 'Tiết 4', 'time' => '10:00 - 10:45', 'session' => 'morning'],
    5 => ['name' => 'Tiết 5', 'time' => '10:50 - 11:35', 'session' => 'morning'],
    6 => ['name' => 'Tiết 6', 'time' => '13:30 - 14:15', 'session' => 'afternoon'],
    7 => ['name' => 'Tiết 7', 'time' => '14:20 - 15:05', 'session' => 'afternoon'],
    8 => ['name' => 'Tiết 8', 'time' => '15:15 - 16:00', 'session' => 'afternoon']
];

// Helper to find schedule item for a given day and period
function findScheduleItem($timetable, $day, $period) {
    foreach ($timetable as $item) {
        if ($item['day_of_week'] == $day && $period >= $item['period_start'] && $period <= $item['period_end']) {
            return $item;
        }
    }
    return null;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Thời Khóa Biểu - <?= htmlspecialchars($className) ?> - <?= htmlspecialchars($semesterName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            line-height: 1.35;
            color: #000;
            background: #475569;
            min-height: 100vh;
        }

        /* ===== TOOLBAR ===== */
        .toolbar {
            position: sticky;
            top: 0;
            background: #0f172a;
            color: #f1f5f9;
            padding: 10px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 200;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            border-bottom: 1px solid #334155;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .toolbar-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .toolbar-left, .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .toolbar-title {
            font-weight: 700;
            font-size: 14px;
            color: #f8fafc;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .toolbar button, .toolbar select, .toolbar a {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        }
        .toolbar a.btn-back {
            background: #334155;
            color: #e2e8f0;
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }
        .toolbar a.btn-back:hover { background: #475569; color: #fff; }
        .toolbar button.btn-print {
            background: #006c4a;
            color: #fff;
            border: none;
            padding: 7px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 6px rgba(0,108,74,0.3);
            transition: all 0.15s;
        }
        .toolbar button.btn-print:hover { background: #005137; }
        .toolbar button.btn-settings {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
            padding: 7px 13px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }
        .toolbar button.btn-settings:hover { background: #334155; color: #fff; }
        .toolbar button.btn-settings.active { background: #006c4a; color: #fff; border-color: #006c4a !important; }
        .toolbar select {
            background: #1e293b; color: #e2e8f0; border: 1px solid #334155;
            padding: 7px 10px; border-radius: 6px; font-size: 12px; font-family: inherit; cursor: pointer;
        }
        .toolbar label { color: #94a3b8; font-weight: 600; font-size: 12px; }

        /* ===== SETTINGS PANEL ===== */
        .settings-panel { display: none; background: #1e293b; border-top: 1px solid #334155; padding: 14px 20px; }
        .settings-panel.open { display: block; }
        .settings-grid { display: flex; align-items: flex-start; gap: 28px; flex-wrap: wrap; }
        .settings-section-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 8px;
        }
        .margin-row {
            display: flex; align-items: center; gap: 8px; margin-bottom: 6px;
        }
        .margin-row label {
            font-size: 12px; color: #cbd5e1; font-weight: 500;
            width: 65px; text-align: right; font-family: 'Inter', sans-serif;
        }
        .margin-row input {
            width: 65px; padding: 5px 6px; border: 1px solid #475569; border-radius: 5px;
            background: #0f172a; color: #f1f5f9; font-size: 12px; font-family: 'Inter', sans-serif;
            font-weight: 600; text-align: center;
        }
        .margin-row input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 2px rgba(16,185,129,0.2); }
        .margin-row .unit { font-size: 11px; color: #64748b; font-family: 'Inter', sans-serif; }
        .margin-row select {
            padding: 5px 8px; border: 1px solid #475569; border-radius: 5px;
            background: #0f172a; color: #f1f5f9; font-size: 12px; font-family: 'Inter', sans-serif;
            font-weight: 600; cursor: pointer;
        }
        .btn-apply { background: #006c4a !important; color: #fff !important; border: none !important; font-size: 12px !important; padding: 7px 18px !important; border-radius: 5px !important; cursor: pointer; font-family: 'Inter', sans-serif; font-weight: 600; margin-top: 18px; }
        .btn-apply:hover { background: #005137 !important; }
        .btn-reset { background: transparent !important; color: #94a3b8 !important; border: 1px solid #475569 !important; font-size: 11px !important; padding: 5px 12px !important; border-radius: 5px !important; cursor: pointer; font-family: 'Inter', sans-serif; margin-top: 18px; margin-left: 6px; }
        .btn-reset:hover { border-color: #94a3b8 !important; color: #fff !important; }

        /* ===== SCREEN PREVIEW (A4 LANDSCAPE KHỔ NGANG) ===== */
        .paper-wrapper {
            padding: 25px 15px;
            display: flex;
            justify-content: center;
        }
        .paper {
            background: #fff;
            width: 297mm;
            max-width: 297mm;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            min-height: 210mm;
            position: relative;
        }

        /* ===== MASTER LAYOUT TABLE ===== */
        table.master-layout {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        table.master-layout > thead > tr > td,
        table.master-layout > tfoot > tr > td {
            border: none;
            padding: 0;
        }
        table.master-layout > tbody > tr > td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .margin-spacer-top    { height: 15.24mm; }
        .margin-spacer-bottom { height: 15.24mm; }
        .content-container    { padding: 0 15.24mm; }

        /* ===== DOCUMENT HEADER ===== */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4mm;
        }
        .header-left {
            text-align: center;
            font-size: 11.5px;
            line-height: 1.35;
        }
        .header-left .org-upper { font-weight: bold; font-size: 11.5px; }
        .header-left .school-name { font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .header-left .school-code { font-size: 10.5px; font-style: italic; color: #333; }

        .header-right {
            text-align: center;
            font-size: 11.5px;
            line-height: 1.35;
        }
        .header-right .country { font-weight: bold; font-size: 12px; }
        .header-right .motto   { font-weight: bold; font-size: 11.5px; }
        .header-right .hr-rule {
            width: 110px; margin: 3px auto 0 auto;
            border: none; border-top: 1px solid #000;
        }

        /* ===== TITLE ===== */
        .title-block {
            text-align: center;
            margin-bottom: 4mm;
        }
        .title-block h1 {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
        }
        .title-block .subtitle {
            font-size: 12.5px;
            font-style: italic;
            color: #222;
        }

        /* ===== SCHEDULE TABLE ===== */
        table.sched-table {
            width: 100% !important;
            border-collapse: collapse !important;
            border: 0.5px solid #000 !important;
            border-width: 0.5px !important;
            table-layout: fixed !important;
            font-size: 11.5px;
            line-height: 1.25;
        }
        table.sched-table th, table.sched-table td {
            border: 0.5px solid #000 !important;
            border-width: 0.5px !important;
            padding: 5px 6px !important;
            vertical-align: middle !important;
            overflow: hidden !important;
        }
        table.sched-table thead {
            display: table-header-group !important;
        }
        table.sched-table th {
            background-color: #f1f5f9 !important;
            font-weight: bold !important;
            text-align: center !important;
            font-size: 11px !important;
            text-transform: uppercase;
        }
        .session-separator td {
            background-color: #f8fafc !important;
            font-weight: bold !important;
            font-size: 11px !important;
            text-transform: uppercase;
            padding: 3px 6px !important;
            letter-spacing: 0.05em;
            color: #334155;
        }

        .sched-cell {
            text-align: center;
            height: 38px;
        }
        .sched-sub {
            font-weight: bold;
            font-size: 11.5px;
            color: #000;
        }
        .sched-tea {
            font-size: 10px;
            color: #333;
            margin-top: 1px;
        }
        .sched-room {
            font-size: 9.5px;
            color: #666;
            font-style: italic;
        }

        /* ===== SIGNATURES ===== */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 8mm;
            font-size: 12px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .sig-col {
            width: 40%;
            text-align: center;
        }
        .sig-date { font-style: italic; font-size: 11px; margin-bottom: 3px; }
        .sig-title { font-weight: bold; text-transform: uppercase; margin-bottom: 2px; font-size: 12px; }
        .sig-hint { font-style: italic; font-size: 10.5px; color: #444; margin-bottom: 40px; }
        .sig-name { font-weight: bold; font-size: 12.5px; }

        /* ===== PRINT STYLES (A4 LANDSCAPE) ===== */
        @media print {
            .toolbar, .settings-panel { display: none !important; }

            @page {
                size: A4 landscape;
                margin: 0 !important;
            }

            body {
                background: #fff !important;
                color: #000 !important;
                font-size: 11pt !important;
            }

            .paper-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }

            .paper {
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            table.master-layout {
                width: 100% !important;
            }

            table.sched-table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 0.5pt solid #000 !important;
                border-width: 0.5pt !important;
            }

            table.sched-table th, table.sched-table td {
                border: 0.5pt solid #000 !important;
                border-width: 0.5pt !important;
            }

            table.sched-table th {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .session-separator td {
                background-color: #f5f5f5 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .signatures {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR -->
    <div class="toolbar no-print">
        <div class="toolbar-main">
            <div class="toolbar-left">
                <a href="<?= BASE_URL ?>/schedules?class_id=<?= (int)$selectedClass ?>&semester_id=<?= (int)$selectedSemester ?>" class="btn-back">
                    &larr; Quay Lại Thời Khóa Biểu
                </a>
                <span class="toolbar-title">
                    Thời Khóa Biểu — <?= htmlspecialchars($className) ?> (<?= htmlspecialchars($semesterName) ?>)
                </span>
            </div>

            <div class="toolbar-right">
                <form method="GET" action="<?= BASE_URL ?>/schedules/print" style="display:inline-flex;align-items:center;gap:6px;">
                    <label for="classSelect">Lớp:</label>
                    <select id="classSelect" name="class_id" onchange="this.form.submit()">
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="semSelect">Học kỳ:</label>
                    <select id="semSelect" name="semester_id" onchange="this.form.submit()">
                        <?php foreach ($semesters as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $selectedSemester == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <button class="btn-settings" id="btnToggleSettings" onclick="toggleSettings()">
                    <span>Căn lề trang</span>
                </button>

                <button class="btn-print" onclick="window.print()">
                    <span>In Thời Khóa Biểu (A4 Ngang)</span>
                </button>
            </div>
        </div>

        <!-- SETTINGS PANEL -->
        <div class="settings-panel" id="settingsPanel">
            <div class="settings-grid">
                <div>
                    <div class="settings-section-title">Mẫu Căn Lề Tiêu Chuẩn</div>
                    <div class="margin-row">
                        <label>Kiểu lề:</label>
                        <select id="presetSelect" onchange="applyPreset(this.value)">
                            <option value="normal" selected>Bình thường (15.24 mm)</option>
                            <option value="narrow">Hẹp (10.00 mm)</option>
                            <option value="moderate">Vừa phải (12.70 mm)</option>
                            <option value="wide">Rộng (20.00 mm)</option>
                            <option value="custom">Tùy chỉnh...</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="settings-section-title">Khoảng Cách Chi Tiết (mm)</div>
                    <div class="margin-row">
                        <label>Trên (Top):</label>
                        <input type="number" id="mTop" value="15.24" min="5" max="40" step="0.5" oninput="onCustomInput()">
                        <span class="unit">mm</span>
                    </div>
                    <div class="margin-row">
                        <label>Dưới (Bottom):</label>
                        <input type="number" id="mBottom" value="15.24" min="5" max="40" step="0.5" oninput="onCustomInput()">
                        <span class="unit">mm</span>
                    </div>
                </div>

                <div>
                    <div class="settings-section-title">&nbsp;</div>
                    <div class="margin-row">
                        <label>Trái (Left):</label>
                        <input type="number" id="mLeft" value="15.24" min="5" max="50" step="0.5" oninput="onCustomInput()">
                        <span class="unit">mm</span>
                    </div>
                    <div class="margin-row">
                        <label>Phải (Right):</label>
                        <input type="number" id="mRight" value="15.24" min="5" max="50" step="0.5" oninput="onCustomInput()">
                        <span class="unit">mm</span>
                    </div>
                </div>

                <div style="display:flex; align-items:flex-end;">
                    <button class="btn-apply" onclick="applyCustomMargins()">Áp Dụng</button>
                    <button class="btn-reset" onclick="resetMargins()">Mặc định</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PREVIEW WRAPPER -->
    <div class="paper-wrapper">
        <div class="paper" id="paperDoc">
            <table class="master-layout">
                <thead>
                    <tr>
                        <td>
                            <div class="margin-spacer-top" id="spacerTop"></div>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="content-container" id="contentContainer">
                                
                                <!-- DOCUMENT HEADER -->
                                <div class="doc-header">
                                    <div class="header-left">
                                        <div class="org-upper">PHÒNG GIÁO DỤC VÀ ĐÀO TẠO</div>
                                        <div class="school-name">TRƯỜNG TIỂU HỌC & THCS EDUMANAGE</div>
                                        <div class="school-code">Mã trường: TH-EDU-01</div>
                                    </div>
                                    <div class="header-right">
                                        <div class="country">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                                        <div class="motto">Độc lập - Tự do - Hạnh phúc</div>
                                        <hr class="hr-rule">
                                    </div>
                                </div>

                                <!-- TITLE -->
                                <div class="title-block">
                                    <h1>THỜI KHÓA BIỂU HỌC TẬP</h1>
                                    <div class="subtitle">
                                        Lớp: <strong><?= htmlspecialchars($className) ?></strong> — <?= htmlspecialchars($semesterName) ?> (Năm học 2025 – 2026)
                                    </div>
                                </div>

                                <!-- TIMETABLE MATRIX TABLE -->
                                <table class="sched-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 105px;">Buổi / Tiết</th>
                                            <?php foreach ($days as $dNum => $dName): ?>
                                            <th><?= $dName ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- BUỔI SÁNG -->
                                        <tr class="session-separator">
                                            <td colspan="7">BUỔI SÁNG</td>
                                        </tr>
                                        <?php for ($p = 1; $p <= 5; $p++): 
                                            $pInfo = $periods[$p];
                                        ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: bold; background: #fdfdfd;">
                                                <div><?= $pInfo['name'] ?></div>
                                                <div style="font-size: 9.5px; font-weight: normal; color: #555;"><?= $pInfo['time'] ?></div>
                                            </td>
                                            <?php foreach ($days as $dNum => $dName): 
                                                $item = findScheduleItem($timetable, $dNum, $p);
                                            ?>
                                            <td class="sched-cell">
                                                <?php if ($item): ?>
                                                    <div class="sched-sub"><?= htmlspecialchars($item['subject_name']) ?></div>
                                                    <div class="sched-tea"><?= htmlspecialchars($item['teacher_name']) ?></div>
                                                    <?php if (!empty($item['room'])): ?>
                                                    <div class="sched-room"><?= htmlspecialchars($item['room']) ?></div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span style="color: #ccc;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endfor; ?>

                                        <!-- BUỔI CHIỀU -->
                                        <tr class="session-separator">
                                            <td colspan="7">BUỔI CHIỀU</td>
                                        </tr>
                                        <?php for ($p = 6; $p <= 8; $p++): 
                                            $pInfo = $periods[$p];
                                        ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: bold; background: #fdfdfd;">
                                                <div><?= $pInfo['name'] ?></div>
                                                <div style="font-size: 9.5px; font-weight: normal; color: #555;"><?= $pInfo['time'] ?></div>
                                            </td>
                                            <?php foreach ($days as $dNum => $dName): 
                                                $item = findScheduleItem($timetable, $dNum, $p);
                                            ?>
                                            <td class="sched-cell">
                                                <?php if ($item): ?>
                                                    <div class="sched-sub"><?= htmlspecialchars($item['subject_name']) ?></div>
                                                    <div class="sched-tea"><?= htmlspecialchars($item['teacher_name']) ?></div>
                                                    <?php if (!empty($item['room'])): ?>
                                                    <div class="sched-room"><?= htmlspecialchars($item['room']) ?></div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span style="color: #ccc;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>

                                <!-- SIGNATURES -->
                                <div class="signatures">
                                    <div class="sig-col">
                                        <div class="sig-title">GIÁO VIÊN CHỦ NHIỆM</div>
                                        <div class="sig-hint">(Ký và ghi rõ họ tên)</div>
                                    </div>
                                    <div class="sig-col">
                                        <div class="sig-date">Ngày ..... tháng ..... năm 202...</div>
                                        <div class="sig-title">HIỆU TRƯỞNG / CHUYÊN MÔN</div>
                                        <div class="sig-hint">(Ký tên và đóng dấu)</div>
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                            <div class="margin-spacer-bottom" id="spacerBottom"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- SCRIPT FOR MARGINS AND PRESETS -->
    <script>
        const PRESETS = {
            normal:   { top: 15.24, bottom: 15.24, left: 15.24, right: 15.24 },
            narrow:   { top: 10.00, bottom: 10.00, left: 10.00, right: 10.00 },
            moderate: { top: 12.70, bottom: 12.70, left: 12.70, right: 12.70 },
            wide:     { top: 20.00, bottom: 20.00, left: 20.00, right: 20.00 }
        };

        function toggleSettings() {
            const panel = document.getElementById('settingsPanel');
            const btn = document.getElementById('btnToggleSettings');
            panel.classList.toggle('open');
            btn.classList.toggle('active');
        }

        function applyPreset(name) {
            if (name === 'custom') return;
            const p = PRESETS[name];
            if (!p) return;
            document.getElementById('mTop').value = p.top;
            document.getElementById('mBottom').value = p.bottom;
            document.getElementById('mLeft').value = p.left;
            document.getElementById('mRight').value = p.right;
            applyMargins(p.top, p.bottom, p.left, p.right);
        }

        function onCustomInput() {
            document.getElementById('presetSelect').value = 'custom';
        }

        function applyCustomMargins() {
            const t = parseFloat(document.getElementById('mTop').value) || 15.24;
            const b = parseFloat(document.getElementById('mBottom').value) || 15.24;
            const l = parseFloat(document.getElementById('mLeft').value) || 15.24;
            const r = parseFloat(document.getElementById('mRight').value) || 15.24;
            applyMargins(t, b, l, r);
        }

        function resetMargins() {
            document.getElementById('presetSelect').value = 'normal';
            applyPreset('normal');
        }

        function applyMargins(top, bottom, left, right) {
            document.getElementById('spacerTop').style.height = top + 'mm';
            document.getElementById('spacerBottom').style.height = bottom + 'mm';
            document.getElementById('contentContainer').style.paddingLeft = left + 'mm';
            document.getElementById('contentContainer').style.paddingRight = right + 'mm';

            let printStyle = document.getElementById('dynamicPrintStyle');
            if (!printStyle) {
                printStyle = document.createElement('style');
                printStyle.id = 'dynamicPrintStyle';
                document.head.appendChild(printStyle);
            }
            printStyle.textContent = `
                @media print {
                    .margin-spacer-top { height: ${top}mm !important; }
                    .margin-spacer-bottom { height: ${bottom}mm !important; }
                    .content-container {
                        padding-left: ${left}mm !important;
                        padding-right: ${right}mm !important;
                    }
                }
            `;
        }
    </script>
</body>
</html>
