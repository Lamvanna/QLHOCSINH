<?php
// views/grades/print.php - Multi-page A4 Landscape Print with Word-style Margins & Page Setup
$className = htmlspecialchars($selectedClass['name'] ?? ('Lớp #' . $classId));
$yearName = htmlspecialchars($selectedYear['name'] ?? ('Năm học #' . $academicYearId));
$homeroomTeacher = htmlspecialchars($selectedClass['homeroom_teacher_name'] ?? 'Chưa phân công');
$numSubjects = count($subjects);
$numStudents = count($students);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Bảng Điểm - <?= $className ?> (<?= $yearName ?>)</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #475569;
            line-height: 1.35;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ===== TOOLBAR (SCREEN ONLY) ===== */
        .toolbar {
            background: #0f172a;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 200;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            border-bottom: 1px solid #334155;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .toolbar-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .toolbar-left, .toolbar-right { display: flex; align-items: center; gap: 10px; }
        .toolbar a, .toolbar button, .toolbar .btn {
            font-family: inherit; font-size: 12px; text-decoration: none; border: none;
            cursor: pointer; border-radius: 6px; padding: 7px 14px; font-weight: 600;
            transition: all 0.15s; display: inline-flex; align-items: center; gap: 5px;
        }
        .toolbar a { background: #1e293b; color: #94a3b8; border: 1px solid #334155; }
        .toolbar a:hover { background: #334155; color: #fff; }
        .btn-print { background: #0d9488 !important; color: #fff !important; border: none !important; font-size: 13px !important; padding: 8px 20px !important; }
        .btn-print:hover { background: #0f766e !important; }
        .btn-settings { background: #1e293b !important; color: #e2e8f0 !important; border: 1px solid #475569 !important; }
        .btn-settings:hover { background: #334155 !important; }
        .btn-settings.active { background: #334155 !important; border-color: #14b8a6 !important; color: #14b8a6 !important; }
        .toolbar select {
            background: #1e293b; color: #e2e8f0; border: 1px solid #334155;
            padding: 7px 10px; border-radius: 6px; font-size: 12px; font-family: inherit; cursor: pointer;
        }
        .toolbar label { color: #94a3b8; font-weight: 600; font-size: 12px; }

        /* ===== SETTINGS PANEL ===== */
        .settings-panel { display: none; background: #1e293b; border-top: 1px solid #334155; padding: 14px 20px; }
        .settings-panel.open { display: block; }
        .settings-grid { display: flex; align-items: flex-start; gap: 28px; flex-wrap: wrap; }
        .settings-section { display: flex; flex-direction: column; gap: 2px; }
        .settings-section-title {
            font-size: 11px; font-weight: 700; color: #2dd4bf;
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
            font-family: 'Inter', sans-serif;
        }
        .margin-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
        .margin-row label {
            font-size: 12px; color: #cbd5e1; font-weight: 500;
            width: 65px; text-align: right; font-family: 'Inter', sans-serif;
        }
        .margin-row input {
            width: 65px; padding: 5px 6px; border: 1px solid #475569; border-radius: 5px;
            background: #0f172a; color: #f1f5f9; font-size: 12px; font-family: 'Inter', sans-serif;
            font-weight: 600; text-align: center;
        }
        .margin-row input:focus { outline: none; border-color: #2dd4bf; box-shadow: 0 0 0 2px rgba(45,212,191,0.2); }
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
            position: absolute;
            background: repeating-linear-gradient(0deg, #94a3b8 0px, #94a3b8 1px, transparent 1px, transparent 5px);
            opacity: 0.4;
        }
        .btn-apply { background: #0d9488 !important; color: #fff !important; border: none !important; font-size: 12px !important; padding: 7px 18px !important; border-radius: 5px !important; cursor: pointer; font-family: 'Inter', sans-serif; font-weight: 600; margin-top: 18px; }
        .btn-apply:hover { background: #0f766e !important; }
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
        table.master-layout > tbody > tr > td,
        table.master-layout > tfoot > tr > td {
            border: none;
            padding: 0;
        }

        .margin-spacer-top {
            height: 15mm;
            display: block;
        }
        .margin-spacer-bottom {
            height: 15mm;
            display: block;
        }
        .content-container {
            padding-left: 15mm;
            padding-right: 15mm;
            width: 100%;
        }

        /* ===== DOCUMENT HEADER ===== */
        .doc-header { display: table; width: 100%; margin-bottom: 4mm; }
        .doc-header-left, .doc-header-right { display: table-cell; vertical-align: top; width: 50%; }
        .doc-header-left { text-align: left; }
        .doc-header-right { text-align: center; }
        .doc-header p { margin: 0; font-size: 11.5px; line-height: 1.4; }
        .doc-header .org-name { font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .doc-header .school-name { font-weight: 900; font-size: 13px; text-transform: uppercase; }
        .doc-header .school-code { font-style: italic; color: #555; font-size: 10.5px; }
        .doc-header .republic { font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .doc-header .motto { font-weight: bold; font-size: 12px; }
        .doc-header .divider { font-size: 11px; color: #666; }

        /* ===== DOCUMENT TITLE ===== */
        .doc-title { text-align: center; margin-bottom: 4mm; }
        .doc-title h1 { font-size: 17px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .doc-title h2 { font-size: 13px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; color: #1e293b; }
        .doc-title .meta { font-size: 11px; font-style: italic; color: #334155; }
        .doc-title .meta strong { color: #000; }

        /* ===== DATA TABLE (GRADE TABLE) ===== */
        table.grade-table {
            width: 100% !important;
            border-collapse: collapse !important;
            border: 0.5px solid #000 !important;
            border-width: 0.5px !important;
            table-layout: auto !important;
            font-size: 11px;
            line-height: 1.25;
        }
        table.grade-table th, table.grade-table td {
            border: 0.5px solid #000 !important;
            border-width: 0.5px !important;
            padding: 4px 2px !important;
            vertical-align: middle !important;
            overflow: hidden !important;
        }
        table.grade-table thead {
            display: table-header-group !important;
        }
        table.grade-table th {
            background-color: #f1f5f9 !important;
            font-weight: bold !important;
            text-align: center !important;
            font-size: 10px !important;
            text-transform: uppercase;
        }
        table.grade-table th .sub-code {
            font-size: 8.5px;
            color: #0f766e;
            font-family: 'Courier New', monospace;
            font-weight: 900;
        }
        td.c { text-align: center; }
        td.l { text-align: left; }
        td.r { text-align: right; }
        td.b { font-weight: bold; }
        td.mono { font-family: 'Courier New', monospace; }
        td.total-cell {
            background-color: #f8fafc !important;
            font-weight: bold;
            text-align: center;
            font-size: 11.5px;
        }

        /* ===== SIGNATURES ===== */
        .signatures { display: table; width: 100%; margin-top: 6mm; font-size: 11.5px; }
        .sig-col { display: table-cell; width: 33.33%; text-align: center; vertical-align: top; }
        .sig-title { font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .sig-hint { font-style: italic; font-size: 10px; color: #555; margin-bottom: 40px; }
        .sig-name { font-weight: bold; font-size: 12.5px; }
        .sig-date { font-style: italic; font-size: 11px; margin-bottom: 3px; }

        /* ======================================================= */
        /* ===== PRINT STYLES (A4 LANDSCAPE)                 ===== */
        /* ======================================================= */
        @media print {
            .toolbar, .settings-panel { display: none !important; }

            @page {
                size: A4 landscape;
                margin: 0 !important;
            }

            html, body {
                width: 100% !important;
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .paper-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            .paper {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                min-height: auto !important;
            }

            table.master-layout {
                width: 100% !important;
            }

            table.grade-table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 0.5pt solid #000 !important;
                border-width: 0.5pt !important;
            }
            table.grade-table th,
            table.grade-table td {
                border: 0.5pt solid #000 !important;
                border-width: 0.5pt !important;
                padding: 3.5px 2px !important;
            }
            table.grade-table th {
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
            table.grade-table thead {
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
            <a href="<?= BASE_URL ?>/grades?class_id=<?= $classId ?>&academic_year_id=<?= $academicYearId ?>">&#8592; Quay Lại</a>
            <form method="GET" action="<?= BASE_URL ?>/grades/print" style="display:flex;align-items:center;gap:8px;">
                <label>Lớp:</label>
                <select name="class_id" onchange="this.form.submit()">
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($classId == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Năm học:</label>
                <select name="academic_year_id" onchange="this.form.submit()">
                    <?php foreach ($academicYears as $yr): ?>
                        <option value="<?= $yr['id'] ?>" <?= ($academicYearId == $yr['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($yr['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Sắp xếp:</label>
                <select name="sort" onchange="this.form.submit()">
                    <option value="default" <?= ($sort === 'default') ? 'selected' : '' ?>>Thứ tự mặc định</option>
                    <option value="total_desc" <?= ($sort === 'total_desc') ? 'selected' : '' ?>>Tổng điểm: Cao ➔ Thấp</option>
                    <option value="total_asc" <?= ($sort === 'total_asc') ? 'selected' : '' ?>>Tổng điểm: Thấp ➔ Cao</option>
                    <option value="name_asc" <?= ($sort === 'name_asc') ? 'selected' : '' ?>>Tên học sinh: A ➔ Z</option>
                    <option value="name_desc" <?= ($sort === 'name_desc') ? 'selected' : '' ?>>Tên học sinh: Z ➔ A</option>
                    <option value="code_asc" <?= ($sort === 'code_asc') ? 'selected' : '' ?>>Mã học sinh: Tăng dần</option>
                </select>
            </form>
            <button class="btn btn-settings" id="btnToggleSettings" onclick="toggleSettings()">
                &#9881; Thiết Lập Lề Trang
            </button>
        </div>
        <div class="toolbar-right">
            <button class="btn btn-print" onclick="window.print()">&#128424; In Bảng Điểm</button>
        </div>
    </div>

    <!-- Word-style Margin Settings Panel -->
    <div class="settings-panel" id="settingsPanel">
        <div class="settings-grid">
            <!-- Margins -->
            <div class="settings-section">
                <div class="settings-section-title">Lề trang (Margins)</div>
                <div class="margin-row">
                    <label>Top:</label>
                    <input type="number" id="marginTop" value="0.6" min="0" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Bottom:</label>
                    <input type="number" id="marginBottom" value="0.6" min="0" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Left:</label>
                    <input type="number" id="marginLeft" value="0.6" min="0" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Right:</label>
                    <input type="number" id="marginRight" value="0.6" min="0" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
            </div>

            <!-- Gutter -->
            <div class="settings-section">
                <div class="settings-section-title">Gáy sách (Gutter)</div>
                <div class="margin-row">
                    <label>Gutter:</label>
                    <input type="number" id="gutterSize" value="0" min="0" max="2" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Position:</label>
                    <select id="gutterPos">
                        <option value="left" selected>Left</option>
                        <option value="top">Top</option>
                    </select>
                </div>
            </div>

            <!-- Paper -->
            <div class="settings-section">
                <div class="settings-section-title">Khổ giấy</div>
                <div class="margin-row">
                    <label>Size:</label>
                    <select id="paperSize" style="width:115px;">
                        <option value="a4" selected>A4 (297x210)</option>
                        <option value="letter">Letter (279x216)</option>
                        <option value="legal">Legal (356x216)</option>
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
                <div class="settings-section-title">Xem trước</div>
                <div class="settings-preview" id="miniPreview">
                    <div class="settings-preview-inner" id="miniPreviewInner"></div>
                </div>
            </div>

            <!-- Actions -->
            <div class="settings-section">
                <button class="btn-apply" onclick="applyMargins()">&#10003; Áp Dụng</button>
                <button class="btn-reset" onclick="resetMargins()">&#8634; Mặc Định</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== PAPER WRAPPER ===== -->
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
                                <div class="doc-header-left">
                                    <p class="org-name">Sở Giáo Dục Và Đào Tạo</p>
                                    <p class="school-name">Trường THCS &amp; THPT EduManage</p>
                                    <p class="school-code">Mã trường: EDU-2026</p>
                                </div>
                                <div class="doc-header-right">
                                    <p class="republic">Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam</p>
                                    <p class="motto">Độc lập - Tự do - Hạnh phúc</p>
                                    <p class="divider">─────────────────</p>
                                </div>
                            </div>

                            <!-- Document Title -->
                            <div class="doc-title">
                                <h1>BẢNG TỔNG HỢP ĐIỂM THI VÀ ĐÁNH GIÁ HỌC SINH</h1>
                                <h2><?= mb_strtoupper($yearName, 'UTF-8') ?> &nbsp;•&nbsp; LỚP: <?= mb_strtoupper($className, 'UTF-8') ?></h2>
                                <p class="meta">
                                    Sĩ số: <strong><?= $numStudents ?></strong> học sinh
                                    &nbsp;|&nbsp;
                                    Số môn đánh giá: <strong><?= $numSubjects ?></strong> môn
                                    &nbsp;|&nbsp;
                                    Tổng điểm cao nhất: <strong><?= number_format($stats['highest_total'] ?? 0, 1) ?>đ</strong>
                                </p>
                            </div>

                            <!-- Grade Table -->
                            <table class="grade-table">
                                <thead>
                                    <tr>
                                        <th style="width: 3.5%;">STT</th>
                                        <th style="width: 10%;">Mã HS</th>
                                        <th style="width: 18%; text-align: left; padding-left: 6px;">Họ Và Tên Học Sinh</th>
                                        <th style="width: 5.5%;">Giới Tính</th>
                                        <th style="width: 8.5%;">Ngày Sinh</th>

                                        <!-- Cột từng môn học -->
                                        <?php foreach ($subjects as $sub): ?>
                                        <th style="min-width: 48px;">
                                            <?= htmlspecialchars($sub['name']) ?>
                                            
                                        </th>
                                        <?php endforeach; ?>

                                        <!-- Tổng điểm -->
                                        <th style="width: 9%; background-color: #e2e8f0 !important;">Tổng Điểm</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($students)): ?>
                                        <?php foreach ($students as $idx => $st): 
                                            $tot = $st['total_score'];
                                        ?>
                                        <tr>
                                            <td class="c b"><?= $idx + 1 ?></td>
                                            <td class="c b mono"><?= htmlspecialchars($st['student_code']) ?></td>
                                            <td class="l b" style="padding-left: 6px;"><?= htmlspecialchars($st['full_name']) ?></td>
                                            <td class="c"><?= $st['gender_vn'] ?? ($st['gender'] === 'female' ? 'Nữ' : 'Nam') ?></td>
                                            <td class="c mono"><?= $st['dob_formatted'] ?? (!empty($st['dob']) ? date('d/m/Y', strtotime($st['dob'])) : '') ?></td>

                                            <!-- Điểm từng môn -->
                                            <?php foreach ($subjects as $sub): 
                                                $subId = (int)$sub['id'];
                                                $scoreVal = isset($st['scores'][$subId]) && $st['scores'][$subId] !== null ? $st['scores'][$subId] : '';
                                            ?>
                                            <td class="c b mono">
                                                <?= $scoreVal !== '' ? number_format((float)$scoreVal, 1) : '—' ?>
                                            </td>
                                            <?php endforeach; ?>

                                            <!-- Tổng điểm -->
                                            <td class="c b mono total-cell">
                                                <?= $tot !== null ? number_format((float)$tot, 1) : '—' ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?= count($subjects) + 6 ?>" style="text-align: center; padding: 25px; font-style: italic;">
                                                Không có dữ liệu học sinh hoặc điểm số trong lớp học này.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- Signatures -->
                            <div class="signatures" style="display: flex; justify-content: flex-end; margin-top: 6mm;">
                                <div class="sig-col" style="width: 300px; text-align: center;">
                                    <p class="sig-date">......, ngày <?= date('d') ?> tháng <?= date('m') ?> năm <?= date('Y') ?></p>
                                    <p class="sig-title">Ban Giám Hiệu / Hiệu Trưởng</p>
                                    <p class="sig-hint">(Ký tên và đóng dấu)</p>
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
(function() {
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
        spacerTop.style.height = mT + 'mm';
        spacerBottom.style.height = mB + 'mm';
        contentContainer.style.paddingLeft = mL + 'mm';
        contentContainer.style.paddingRight = mR + 'mm';

        var pageOrientation = v.orient === 'landscape' ? 'landscape' : 'portrait';
        var pagePaperSize = (v.pSize === 'a4' ? 'A4' : (v.pSize === 'letter' ? 'letter' : 'legal'));
        var sizeStr = pagePaperSize + ' ' + pageOrientation;

        paper.style.minHeight = ph + 'mm';

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
            '  table.grade-table, table.grade-table th, table.grade-table td {' +
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

    window.applyMargins = applyMargins;
    window.resetMargins = resetMargins;
    window.toggleSettings = toggleSettings;

    applyMargins();
})();
</script>

<?php if (!empty($autoPrint)): ?>
<script>
window.addEventListener('load', function() {
    setTimeout(function() { window.print(); }, 600);
});
</script>
<?php endif; ?>

</body>
</html>
