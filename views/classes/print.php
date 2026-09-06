<?php
// views/classes/print.php - Multi-page A4 Landscape Print for Classes with Word-style Margins
$totalClasses = count($classes);
$totalStudents = array_sum(array_column($classes, 'student_count'));

$yearTitle = '';
if (!empty($selectedYearId)) {
    foreach ($years as $y) {
        if ($y['id'] == $selectedYearId) {
            $yearTitle = $y['name'];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Danh Sách Lớp Học - Trường THCS & THPT EduManage</title>
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
        .toolbar-left, .toolbar-right { display: flex; align-items: center; gap: 10px; }
        .toolbar a, .toolbar button, .toolbar .btn {
            font-family: inherit; font-size: 12px; text-decoration: none; border: none;
            cursor: pointer; border-radius: 6px; padding: 7px 14px; font-weight: 600;
            transition: all 0.15s; display: inline-flex; align-items: center; gap: 5px;
        }
        .toolbar a { background: #1e293b; color: #94a3b8; border: 1px solid #334155; }
        .toolbar a:hover { background: #334155; color: #fff; }
        .btn-print { background: #006c4a !important; color: #fff !important; border: none !important; font-size: 13px !important; padding: 8px 20px !important; }
        .btn-print:hover { background: #005137 !important; }
        .btn-settings { background: #1e293b !important; color: #e2e8f0 !important; border: 1px solid #475569 !important; }
        .btn-settings:hover { background: #334155 !important; }
        .btn-settings.active { background: #334155 !important; border-color: #10b981 !important; color: #10b981 !important; }
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
        .settings-preview {
            width: 99px; height: 70px; border: 2px solid #475569; background: #fff;
            position: relative; border-radius: 2px; margin-top: 18px;
        }
        .settings-preview-inner {
            position: absolute; border: 1px dashed #10b981;
            background: repeating-linear-gradient(0deg, #e2e8f0, #e2e8f0 1px, transparent 1px, transparent 5px);
            opacity: 0.4;
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
            margin-bottom: 5mm;
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
            margin-bottom: 5mm;
        }
        .title-block h1 {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 3px;
        }
        .title-block .subtitle {
            font-size: 12.5px;
            font-style: italic;
            color: #222;
        }
        .title-block .meta-info {
            font-size: 11.5px;
            color: #444;
            margin-top: 2px;
        }

        /* ===== CLASS TABLE ===== */
        table.class-table {
            width: 100% !important;
            border-collapse: collapse !important;
            border: 0.5px solid #000 !important;
            border-width: 0.5px !important;
            table-layout: auto !important;
            font-size: 11.5px;
            line-height: 1.3;
        }
        table.class-table th, table.class-table td {
            border: 0.5px solid #000 !important;
            border-width: 0.5px !important;
            padding: 5px 7px !important;
            vertical-align: middle !important;
            overflow: hidden !important;
        }
        table.class-table thead {
            display: table-header-group !important;
        }
        table.class-table th {
            background-color: #f1f5f9 !important;
            font-weight: bold !important;
            text-align: center !important;
            font-size: 11px !important;
            text-transform: uppercase;
        }
        td.c { text-align: center; }
        td.l { text-align: left; }
        td.r { text-align: right; }
        td.b { font-weight: bold; }
        td.mono { font-family: 'Courier New', monospace; font-weight: bold; }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10.5px;
            font-weight: bold;
        }
        .status-active { color: #006c4a; }
        .status-inactive { color: #dc2626; }

        /* ===== SIGNATURES ===== */
        .signatures {
            display: flex;
            justify-content: flex-end;
            margin-top: 8mm;
            font-size: 12px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .sig-col {
            width: 250px;
            text-align: center;
        }
        .sig-date { font-style: italic; font-size: 11.5px; margin-bottom: 3px; }
        .sig-title { font-weight: bold; text-transform: uppercase; margin-bottom: 2px; font-size: 12.5px; }
        .sig-hint { font-style: italic; font-size: 10.5px; color: #333; margin-bottom: 38px; }
        .sig-name { font-weight: bold; font-size: 13px; }

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

            table.class-table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 0.5pt solid #000 !important;
                border-width: 0.5pt !important;
            }
            table.class-table th,
            table.class-table td {
                border: 0.5pt solid #000 !important;
                border-width: 0.5pt !important;
                padding: 4px 6px !important;
            }
            table.class-table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.master-layout > thead {
                display: table-header-group !important;
            }
            table.master-layout > tfoot {
                display: table-footer-group !important;
            }
            table.class-table thead {
                display: table-header-group !important;
            }
            tbody tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .signatures {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
    <style id="dynamic-print-css"></style>
</head>
<body>

<!-- ===== TOOLBAR ===== -->
<div class="toolbar">
    <div class="toolbar-main">
        <div class="toolbar-left">
            <a href="<?= BASE_URL ?>/classes">&#8592; Quay Lại Quản Lý Lớp</a>
            
            <!-- Filters (No Grade Filter) -->
            <form method="GET" action="<?= BASE_URL ?>/classes/print" style="display:flex;align-items:center;gap:8px;">
                <label>Năm học:</label>
                <select name="academic_year_id" onchange="this.form.submit()">
                    <option value="">-- Tất cả năm học --</option>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y['id'] ?>" <?= ($selectedYearId == $y['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($y['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Trạng thái:</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">-- Tất cả --</option>
                    <option value="active" <?= ($selectedStatus === 'active') ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="inactive" <?= ($selectedStatus === 'inactive') ? 'selected' : '' ?>>Tạm dừng</option>
                </select>
            </form>

            <button class="btn btn-settings" id="btnToggleSettings" onclick="toggleSettings()">
                &#9881; Thiết Lập Lề Trang
            </button>
        </div>
        <div class="toolbar-right">
            <button class="btn btn-print" onclick="window.print()">&#128424; In Danh Sách Lớp</button>
        </div>
    </div>

    <!-- Word-style Margins Settings Panel -->
    <div class="settings-panel" id="settingsPanel">
        <div class="settings-grid">
            <!-- Margins Section -->
            <div class="settings-section">
                <div class="settings-section-title">Căn Lề Trang (Margins)</div>
                <div class="margin-row">
                    <label>Trên (Top):</label>
                    <input type="number" id="marginTop" value="0.6" min="0.1" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Dưới (Bot):</label>
                    <input type="number" id="marginBottom" value="0.6" min="0.1" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Trái (Left):</label>
                    <input type="number" id="marginLeft" value="0.6" min="0.1" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Phải (Right):</label>
                    <input type="number" id="marginRight" value="0.6" min="0.1" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
            </div>

            <!-- Gutter Section -->
            <div class="settings-section">
                <div class="settings-section-title">Gáy Sách (Gutter)</div>
                <div class="margin-row">
                    <label>Gáy (Gutter):</label>
                    <input type="number" id="gutterSize" value="0" min="0" max="2" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Vị trí:</label>
                    <select id="gutterPos">
                        <option value="left">Trái (Left)</option>
                        <option value="top">Trên (Top)</option>
                    </select>
                </div>
            </div>

            <!-- Paper & Orientation -->
            <div class="settings-section">
                <div class="settings-section-title">Khổ Giấy & Hướng In</div>
                <div class="margin-row">
                    <label>Khổ giấy:</label>
                    <select id="paperSize">
                        <option value="a4" selected>A4 (297 &times; 210 mm)</option>
                        <option value="letter">Letter (8.5 &times; 11 in)</option>
                        <option value="legal">Legal (8.5 &times; 14 in)</option>
                    </select>
                </div>
                <div class="margin-row">
                    <label>Hướng:</label>
                    <select id="paperOrientation" style="width:115px;">
                        <option value="landscape" selected>Ngang (Landscape)</option>
                        <option value="portrait">Dọc (Portrait)</option>
                    </select>
                </div>
            </div>

            <!-- Mini Preview -->
            <div class="settings-section" style="align-items:center;">
                <div class="settings-section-title">Xem Trước</div>
                <div class="settings-preview" id="miniPreview">
                    <div class="settings-preview-inner" id="miniPreviewInner"></div>
                </div>
            </div>

            <!-- Actions -->
            <div class="settings-section" style="display:flex; flex-direction:row; align-items:flex-end;">
                <button class="btn btn-apply" onclick="applyMargins()">&#10003; Áp Dụng</button>
                <button class="btn btn-reset" onclick="resetMargins()">&#8634; Mặc Định</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== PRINT SHEET ===== -->
<div class="paper-wrapper">
    <div class="paper" id="paper">
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
                            <!-- Document Header -->
                            <div class="doc-header">
                                <div class="header-left">
                                    <div class="org-upper">SỞ GIÁO DỤC VÀ ĐÀO TẠO</div>
                                    <div class="school-name">TRƯỜNG THCS & THPT EDUMANAGE</div>
                                    <div class="school-code">Mã trường: EDU-2026</div>
                                </div>
                                <div class="header-right">
                                    <div class="country">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                                    <div class="motto">Độc lập - Tự do - Hạnh phúc</div>
                                    <hr class="hr-rule">
                                </div>
                            </div>

                            <!-- Document Title -->
                            <div class="title-block">
                                <h1>DANH SÁCH LỚP HỌC</h1>
                                <div class="subtitle">
                                    <?php if (!empty($yearTitle)): ?>
                                        NĂM HỌC <?= mb_strtoupper($yearTitle, 'UTF-8') ?>
                                    <?php endif; ?>
                                </div>
                                <div class="meta-info">
                                    Tổng số lớp: <strong><?= $totalClasses ?> lớp</strong> | Tổng số học sinh: <strong><?= $totalStudents ?> học sinh</strong>
                                </div>
                            </div>

                            <!-- Class Table (No Grade Column) -->
                            <table class="class-table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">STT</th>
                                        <th style="width: 12%;">Mã Lớp</th>
                                        <th style="width: 17%; text-align: left; padding-left: 8px;">Tên Lớp Học</th>
                                        <th style="width: 16%;">Năm Học</th>
                                        <th style="width: 22%; text-align: left; padding-left: 8px;">Giáo Viên Chủ Nhiệm</th>
                                        <th style="width: 10%;">Phòng Học</th>
                                        <th style="width: 10%;">Sĩ Số (N/Nữ)</th>
                                        <th style="width: 8%;">Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($classes)): ?>
                                        <?php foreach ($classes as $idx => $c): ?>
                                        <tr>
                                            <td class="c b"><?= $idx + 1 ?></td>
                                            <td class="c mono"><?= htmlspecialchars($c['code'] ?? ('L' . $c['id'])) ?></td>
                                            <td class="l b" style="padding-left: 8px !important;"><?= htmlspecialchars($c['name']) ?></td>
                                            <td class="c"><?= htmlspecialchars($c['year_name'] ?? '—') ?></td>
                                            <td class="l" style="padding-left: 8px !important;"><?= htmlspecialchars($c['homeroom_teacher_name'] ?? 'Chưa phân công') ?></td>
                                            <td class="c"><?= htmlspecialchars($c['room_number'] ?? '—') ?></td>
                                            <td class="c">
                                                <strong><?= (int)($c['student_count'] ?? 0) ?></strong>
                                                <span style="font-size: 10px; color: #555;">(<?= (int)($c['male_count'] ?? 0) ?>N/<?= (int)($c['female_count'] ?? 0) ?>Nữ)</span>
                                            </td>
                                            <td class="c">
                                                <?php if (($c['status'] ?? '') === 'active'): ?>
                                                    <span class="status-badge status-active">Đang hoạt động</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-inactive">Tạm dừng</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="c" style="padding: 20px; color: #777;">
                                                Không có dữ liệu lớp học phù hợp với bộ lọc.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- Signatures Section -->
                            <div class="signatures">
                                <div class="sig-col">
                                    <div class="sig-date">......, ngày <?= date('d') ?> tháng <?= date('m') ?> năm <?= date('Y') ?></div>
                                    <div class="sig-title">BAN GIÁM HIỆU / HIỆU TRƯỞNG</div>
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

<script>
    var INCH = 25.4;
    var paper = document.getElementById('paper');
    var spacerTop = document.getElementById('spacerTop');
    var spacerBottom = document.getElementById('spacerBottom');
    var contentContainer = document.getElementById('contentContainer');
    var dynCSS = document.getElementById('dynamic-print-css');

    var SIZES = {
        a4:     { w: 210, h: 297 },
        letter: { w: 215.9, h: 279.4 },
        legal:  { w: 215.9, h: 355.6 }
    };

    function vals() {
        return {
            top:    parseFloat(document.getElementById('marginTop').value) || 0,
            bottom: parseFloat(document.getElementById('marginBottom').value) || 0,
            left:   parseFloat(document.getElementById('marginLeft').value) || 0,
            right:  parseFloat(document.getElementById('marginRight').value) || 0,
            gutter: parseFloat(document.getElementById('gutterSize').value) || 0,
            gPos:   document.getElementById('gutterPos').value,
            pSize:  document.getElementById('paperSize').value,
            orient: document.getElementById('paperOrientation').value
        };
    }

    function applyMargins() {
        var v = vals();
        var sz = SIZES[v.pSize] || SIZES.a4;
        var pw = v.orient === 'landscape' ? sz.h : sz.w;
        var ph = v.orient === 'landscape' ? sz.w : sz.h;

        var mT = v.top * INCH;
        var mB = v.bottom * INCH;
        var mL = v.left * INCH;
        var mR = v.right * INCH;
        if (v.gPos === 'left') mL += v.gutter * INCH;
        else mT += v.gutter * INCH;

        paper.style.width = pw + 'mm';
        paper.style.maxWidth = pw + 'mm';
        paper.style.minHeight = ph + 'mm';
        spacerTop.style.height = mT + 'mm';
        spacerBottom.style.height = mB + 'mm';
        contentContainer.style.paddingLeft = mL + 'mm';
        contentContainer.style.paddingRight = mR + 'mm';

        var pageOrientation = v.orient === 'landscape' ? 'landscape' : 'portrait';
        var pagePaperSize = (v.pSize === 'a4' ? 'A4' : (v.pSize === 'letter' ? 'letter' : 'legal'));
        var sizeStr = pagePaperSize + ' ' + pageOrientation;

        dynCSS.textContent =
            '@media print {' +
            '  @page {' +
            '    size: ' + sizeStr + ';' +
            '    margin: 0 !important;' +
            '  }' +
            '  .margin-spacer-top {' +
            '    height: ' + mT.toFixed(2) + 'mm !important;' +
            '  }' +
            '  .margin-spacer-bottom {' +
            '    height: ' + mB.toFixed(2) + 'mm !important;' +
            '  }' +
            '  .content-container {' +
            '    padding-left: ' + mL.toFixed(2) + 'mm !important;' +
            '    padding-right: ' + mR.toFixed(2) + 'mm !important;' +
            '  }' +
            '  table.class-table, table.class-table th, table.class-table td {' +
            '    border: 0.5pt solid #000 !important;' +
            '    border-width: 0.5pt !important;' +
            '  }' +
            '}';

        var prev = document.getElementById('miniPreview');
        var inner = document.getElementById('miniPreviewInner');
        var pW = v.orient === 'landscape' ? 99 : 70;
        var pH = v.orient === 'landscape' ? 70 : 99;
        prev.style.width = pW + 'px';
        prev.style.height = pH + 'px';
        var sW = pW / pw;
        var sH = pH / ph;
        inner.style.top = Math.round(mT * sH) + 'px';
        inner.style.bottom = Math.round(mB * sH) + 'px';
        inner.style.left = Math.round(mL * sW) + 'px';
        inner.style.right = Math.round(mR * sW) + 'px';
    }

    function resetMargins() {
        document.getElementById('marginTop').value = '0.6';
        document.getElementById('marginBottom').value = '0.6';
        document.getElementById('marginLeft').value = '0.6';
        document.getElementById('marginRight').value = '0.6';
        document.getElementById('gutterSize').value = '0';
        document.getElementById('gutterPos').value = 'left';
        document.getElementById('paperSize').value = 'a4';
        document.getElementById('paperOrientation').value = 'landscape';
        applyMargins();
    }

    function toggleSettings() {
        document.getElementById('settingsPanel').classList.toggle('open');
        document.getElementById('btnToggleSettings').classList.toggle('active');
    }

    ['marginTop','marginBottom','marginLeft','marginRight','gutterSize'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', applyMargins);
    });
    ['gutterPos','paperSize','paperOrientation'].forEach(function(id) {
        document.getElementById(id).addEventListener('change', applyMargins);
    });

    applyMargins();
</script>

</body>
</html>
