<?php
// views/students/print.php - Multi-page A4 Print with Word-style Margins & Page Setup
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Danh Sách</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #475569;
            line-height: 1.4;
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
        .btn-print { background: #059669 !important; color: #fff !important; border: none !important; font-size: 13px !important; padding: 8px 20px !important; }
        .btn-print:hover { background: #10b981 !important; }
        .btn-settings { background: #1e293b !important; color: #e2e8f0 !important; border: 1px solid #475569 !important; }
        .btn-settings:hover { background: #334155 !important; }
        .btn-settings.active { background: #334155 !important; border-color: #60a5fa !important; color: #60a5fa !important; }
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
            font-size: 11px; font-weight: 700; color: #60a5fa;
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
        .margin-row input:focus { outline: none; border-color: #60a5fa; box-shadow: 0 0 0 2px rgba(96,165,250,0.2); }
        .margin-row .unit { font-size: 11px; color: #64748b; font-family: 'Inter', sans-serif; }
        .margin-row select {
            padding: 5px 8px; border: 1px solid #475569; border-radius: 5px;
            background: #0f172a; color: #f1f5f9; font-size: 12px; font-family: 'Inter', sans-serif;
            font-weight: 600; cursor: pointer;
        }
        .settings-preview {
            width: 70px; height: 99px; border: 2px solid #475569; background: #fff;
            position: relative; border-radius: 2px; margin-top: 18px;
        }
        .settings-preview-inner {
            position: absolute;
            background: repeating-linear-gradient(0deg, #94a3b8 0px, #94a3b8 1px, transparent 1px, transparent 5px);
            opacity: 0.4;
        }
        .btn-apply { background: #2563eb !important; color: #fff !important; border: none !important; font-size: 12px !important; padding: 7px 18px !important; border-radius: 5px !important; cursor: pointer; font-family: 'Inter', sans-serif; font-weight: 600; margin-top: 18px; }
        .btn-apply:hover { background: #3b82f6 !important; }
        .btn-reset { background: transparent !important; color: #94a3b8 !important; border: 1px solid #475569 !important; font-size: 11px !important; padding: 5px 12px !important; border-radius: 5px !important; cursor: pointer; font-family: 'Inter', sans-serif; margin-top: 18px; margin-left: 6px; }
        .btn-reset:hover { border-color: #94a3b8 !important; color: #fff !important; }

        /* ===== SCREEN PREVIEW ===== */
        .paper-wrapper {
            padding: 25px 15px;
            display: flex;
            justify-content: center;
        }
        .paper {
            background: #fff;
            width: 210mm;
            max-width: 210mm;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            min-height: 297mm;
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
            height: 17.78mm;
            display: block;
        }
        .margin-spacer-bottom {
            height: 17.78mm;
            display: block;
        }
        .content-container {
            padding-left: 17.78mm;
            padding-right: 17.78mm;
            width: 100%;
        }

        /* ===== DOCUMENT HEADER ===== */
        .doc-header { display: table; width: 100%; margin-bottom: 5mm; }
        .doc-header-left, .doc-header-right { display: table-cell; vertical-align: top; width: 50%; }
        .doc-header-left { text-align: left; }
        .doc-header-right { text-align: center; }
        .doc-header p { margin: 0; font-size: 12px; line-height: 1.45; }
        .doc-header .org-name { font-weight: bold; font-size: 12.5px; text-transform: uppercase; }
        .doc-header .school-name { font-weight: 900; font-size: 13.5px; text-transform: uppercase; }
        .doc-header .school-code { font-style: italic; color: #555; font-size: 10.5px; }
        .doc-header .republic { font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .doc-header .motto { font-weight: bold; font-size: 12px; }
        .doc-header .divider { font-size: 11px; color: #666; }

        /* ===== DOCUMENT TITLE ===== */
        .doc-title { text-align: center; margin-bottom: 5mm; }
        .doc-title h1 { font-size: 19px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .doc-title h2 { font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; }
        .doc-title .meta { font-size: 11.5px; font-style: italic; color: #444; }
        .doc-title .meta strong { color: #000; }

        /* ===== DATA TABLE ===== */
        table.student-table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            font-size: 12px;
            line-height: 1.3;
        }
        table.student-table th, table.student-table td {
            border: 0.5px solid #777 !important;
            border-width: 0.5px !important;
            padding: 3.5px 4px !important;
            vertical-align: middle !important;
            overflow: hidden !important;
            word-wrap: break-word !important;
        }
        table.student-table thead {
            display: table-header-group !important;
        }
        table.student-table th {
            background-color: #f1f5f9 !important;
            font-weight: bold !important;
            text-align: center !important;
            text-transform: uppercase !important;
            font-size: 11px !important;
        }
        td.c { text-align: center; }
        td.l { text-align: left; }
        td.b { font-weight: bold; }
        td.mono { font-family: 'Courier New', monospace; }
        .col-stt { width: 5%; }
        .col-code { width: 14%; }
        .col-name { width: 30%; }
        .col-gender { width: 10%; }
        .col-dob { width: 13%; }
        .col-class { width: 10%; }
        .col-note { width: 18%; }

        /* ===== SIGNATURES ===== */
        .signatures { display: table; width: 100%; margin-top: 8mm; font-size: 12px; }
        .sig-col { display: table-cell; width: 33.33%; text-align: center; vertical-align: top; }
        .sig-title { font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .sig-hint { font-style: italic; font-size: 10.5px; color: #555; margin-bottom: 35px; }
        .sig-name { font-weight: bold; font-size: 13px; }
        .sig-date { font-style: italic; font-size: 11.5px; margin-bottom: 3px; }

        /* ======================================================= */
        /* ===== PRINT STYLES                                ===== */
        /* ======================================================= */
        @media print {
            .toolbar, .settings-panel { display: none !important; }

            @page {
                size: A4 portrait;
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

            table.student-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            table.student-table th,
            table.student-table td {
                border: 0.5px solid #777 !important;
                border-width: 0.5px !important;
                padding: 3.5px 4px !important;
            }
            table.student-table th {
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
            table.student-table thead {
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
            <a href="<?= BASE_URL ?>/students">&#8592; Quay Lại</a>
            <form method="GET" action="<?= BASE_URL ?>/students/print" style="display:flex;align-items:center;gap:8px;">
                <label>Lớp:</label>
                <select name="class_id" onchange="this.form.submit()">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($classId == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <button class="btn btn-settings" id="btnToggleSettings" onclick="toggleSettings()">
                &#9881; Thiết Lập Lề Trang
            </button>
        </div>
        <div class="toolbar-right">
            <button class="btn btn-print" onclick="window.print()">&#128424; In Danh Sách</button>
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
                    <input type="number" id="marginTop" value="0.7" min="0" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Bottom:</label>
                    <input type="number" id="marginBottom" value="0.7" min="0" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Left:</label>
                    <input type="number" id="marginLeft" value="0.7" min="0" max="3" step="0.05">
                    <span class="unit">inch</span>
                </div>
                <div class="margin-row">
                    <label>Right:</label>
                    <input type="number" id="marginRight" value="0.7" min="0" max="3" step="0.05">
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
                    <select id="paperSize" style="width:110px;">
                        <option value="a4" selected>A4 (210x297)</option>
                        <option value="letter">Letter (216x279)</option>
                        <option value="legal">Legal (216x356)</option>
                    </select>
                </div>
                <div class="margin-row">
                    <label>Hướng:</label>
                    <select id="paperOrientation" style="width:110px;">
                        <option value="portrait" selected>Dọc (Portrait)</option>
                        <option value="landscape">Ngang (Landscape)</option>
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
                                <h1>Danh Sách Học Sinh</h1>
                                <?php if (!empty($selectedClass)): ?>
                                    <h2>Lớp: <?= htmlspecialchars($selectedClass['name']) ?></h2>
                                <?php else: ?>
                                    <h2>Toàn Trường</h2>
                                <?php endif; ?>
                                <p class="meta">
                                    <?php if (!empty($selectedClass)): ?>
                                        Niên khóa: <strong><?= htmlspecialchars($selectedClass['year_name'] ?? '') ?></strong>
                                        &nbsp;|&nbsp;
                                        GVCN: <strong><?= htmlspecialchars($selectedClass['homeroom_teacher_name'] ?? 'Chưa phân công') ?></strong>
                                        &nbsp;|&nbsp;
                                    <?php endif; ?>
                                    Sĩ số: <strong><?= count($students) ?></strong> học sinh
                                </p>
                            </div>

                            <?php $isSingleClass = !empty($selectedClass); ?>
                            <!-- Student Table -->
                            <table class="student-table">
                                <thead>
                                    <tr>
                                        <th style="width: <?= $isSingleClass ? '6%' : '5%' ?>;">STT</th>
                                        <th style="width: <?= $isSingleClass ? '16%' : '14%' ?>;">Mã HS</th>
                                        <th style="width: <?= $isSingleClass ? '34%' : '30%' ?>; text-align:left; padding-left:6px;">Họ Và Tên</th>
                                        <th style="width: <?= $isSingleClass ? '11%' : '10%' ?>;">Giới Tính</th>
                                        <th style="width: <?= $isSingleClass ? '15%' : '13%' ?>;">Ngày Sinh</th>
                                        <?php if (!$isSingleClass): ?>
                                            <th style="width: 10%;">Lớp</th>
                                        <?php endif; ?>
                                        <th style="width: <?= $isSingleClass ? '18%' : '18%' ?>;">Ghi Chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($students)): ?>
                                        <?php foreach ($students as $idx => $s): ?>
                                        <tr>
                                            <td class="c b"><?= $idx + 1 ?></td>
                                            <td class="c b mono"><?= htmlspecialchars($s['student_code']) ?></td>
                                            <td class="l b" style="padding-left:6px;"><?= htmlspecialchars($s['full_name']) ?></td>
                                            <td class="c"><?= $s['gender'] === 'female' ? 'Nữ' : 'Nam' ?></td>
                                            <td class="c"><?= !empty($s['dob']) ? date('d/m/Y', strtotime($s['dob'])) : '' ?></td>
                                            <?php if (!$isSingleClass): ?>
                                                <td class="c b"><?= htmlspecialchars($s['class_name'] ?? '') ?></td>
                                            <?php endif; ?>
                                            <td></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="<?= $isSingleClass ? 6 : 7 ?>" style="text-align:center;padding:20px;font-style:italic;">Không có dữ liệu.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- Signatures -->
                            <div class="signatures">
                                <div class="sig-col">
                                    <p class="sig-title">Người Lập Bảng</p>
                                    <p class="sig-hint">(Ký và ghi rõ họ tên)</p>
                                </div>
                                <div class="sig-col">
                                    <p class="sig-title">Giáo Viên Chủ Nhiệm</p>
                                    <p class="sig-hint">(Ký và ghi rõ họ tên)</p>
                                    <?php if (!empty($selectedClass['homeroom_teacher_name'])): ?>
                                        <p class="sig-name"><?= htmlspecialchars($selectedClass['homeroom_teacher_name']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="sig-col">
                                    <p class="sig-date">....., ngày <?= date('d') ?> tháng <?= date('m') ?> năm <?= date('Y') ?></p>
                                    <p class="sig-title">Ban Giám Hiệu</p>
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

        var sizeStr = v.orient === 'landscape' ? (ph + 'mm ' + pw + 'mm') : (pw + 'mm ' + ph + 'mm');

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
            '}';

        var prev = document.getElementById('miniPreview');
        var inner = document.getElementById('miniPreviewInner');
        var pW = 70, pH = Math.round(pW * (ph / pw));
        prev.style.width = pW + 'px';
        prev.style.height = pH + 'px';
        var s = pW / pw;
        inner.style.top = Math.round(mT * s) + 'px';
        inner.style.bottom = Math.round(mB * s) + 'px';
        inner.style.left = Math.round(mL * s) + 'px';
        inner.style.right = Math.round(mR * s) + 'px';
    }

    function resetMargins() {
        document.getElementById('marginTop').value = '0.7';
        document.getElementById('marginBottom').value = '0.7';
        document.getElementById('marginLeft').value = '0.7';
        document.getElementById('marginRight').value = '0.7';
        document.getElementById('gutterSize').value = '0';
        document.getElementById('gutterPos').value = 'left';
        document.getElementById('paperSize').value = 'a4';
        document.getElementById('paperOrientation').value = 'portrait';
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