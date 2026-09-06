<?php // views/teachers/print.php - Official Standard A4 Printable View ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Danh Sách Giáo Viên - EduManage</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #475569;
            line-height: 1.4;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .toolbar {
            background: #0f172a;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 200;
            font-family: 'Inter', sans-serif;
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
        .toolbar a, .toolbar button {
            font-family: inherit; font-size: 12px; text-decoration: none; border: none;
            cursor: pointer; border-radius: 6px; padding: 7px 14px; font-weight: 600;
            transition: all 0.15s; display: inline-flex; align-items: center; gap: 5px;
        }
        .toolbar a { background: #1e293b; color: #94a3b8; border: 1px solid #334155; }
        .toolbar a:hover { background: #334155; color: #fff; }
        .btn-print { background: #059669 !important; color: #fff !important; }
        .btn-print:hover { background: #10b981 !important; }
        .btn-gear { background: #334155 !important; color: #e2e8f0 !important; }
        .btn-gear:hover { background: #475569 !important; }

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
            padding: 20mm 15mm 20mm 20mm;
            position: relative;
        }

        .doc-header { display: table; width: 100%; margin-bottom: 5mm; }
        .doc-header-left, .doc-header-right { display: table-cell; vertical-align: top; width: 50%; }
        .doc-header-left { text-align: left; }
        .doc-header-right { text-align: center; }
        .doc-header p { margin: 0; font-size: 12px; line-height: 1.45; }
        .doc-header .org-name { font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .doc-header .school-name { font-weight: 900; font-size: 13px; text-transform: uppercase; }
        .doc-header .republic { font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .doc-header .motto { font-weight: bold; font-size: 12px; }

        .doc-title { text-align: center; margin-bottom: 6mm; }
        .doc-title h1 { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
        .doc-title .meta { font-size: 11.5px; font-style: italic; color: #333; }

        table.teacher-table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            font-size: 11.5px;
            line-height: 1.35;
        }
        table.teacher-table th, table.teacher-table td {
            border: 0.5px solid #000 !important;
            padding: 5px 4px !important;
            vertical-align: middle !important;
            overflow: hidden !important;
            word-wrap: break-word !important;
        }
        table.teacher-table thead { display: table-header-group !important; }
        table.teacher-table th {
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

        .signatures { display: table; width: 100%; margin-top: 8mm; font-size: 12px; page-break-inside: avoid; }
        .sig-col { display: table-cell; width: 33.33%; text-align: center; vertical-align: top; }
        .sig-title { font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .sig-hint { font-style: italic; font-size: 10.5px; color: #555; margin-bottom: 40px; }
        .sig-name { font-weight: bold; font-size: 12.5px; }

        @media print {
            body { background: #fff !important; }
            .toolbar { display: none !important; }
            .paper-wrapper { padding: 0 !important; }
            .paper { width: 100% !important; max-width: 100% !important; box-shadow: none !important; min-height: auto !important; padding: 15mm 10mm 15mm 15mm !important; }
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body>

    <!-- TOP TOOLBAR -->
    <div class="toolbar">
        <div class="toolbar-main">
            <div class="toolbar-left">
                <a href="<?= BASE_URL ?>/teachers">
                    <span>&#8592;</span> Quay lại danh sách
                </a>
                <span style="color:#64748b;font-size:12px;">|</span>
                <span style="font-size:13px;font-weight:700;color:#f8fafc;">BẢN IN CHUẨN A4: DANH SÁCH GIÁO VIÊN</span>
            </div>
            <div class="toolbar-right">
                <button onclick="window.print()" class="btn-print">
                    <span>&#128438;</span> In Ngay (Ctrl + P)
                </button>
            </div>
        </div>
    </div>
    <!-- A4 PAPER WRAPPER -->
    <div class="paper-wrapper">
        <div class="paper">
            
            <!-- DOCUMENT ADMINISTRATIVE HEADER -->
            <div class="doc-header">
                <div class="doc-header-left">
                    <p class="org-name">SỞ GIÁO DỤC VÀ ĐÀO TẠO TP. HỒ CHÍ MINH</p>
                    <p class="school-name">TRƯỜNG TIỂU HỌC & THCS EDUMANAGE</p>
                    <p style="font-size:11px;color:#444;">Số: ...... / DSGV-EDUMANAGE</p>
                </div>
                <div class="doc-header-right">
                    <p class="republic">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</p>
                    <p class="motto">Độc lập - Tự do - Hạnh phúc</p>
                    <p style="font-size:11px;color:#666;">------------------------</p>
                </div>
            </div>

            <!-- DOCUMENT TITLE -->
            <div class="doc-title">
                <h1>DANH SÁCH ĐỘI NGŨ GIÁO VIÊN</h1>
                <?php if (!empty($specialization)): ?>
                <h2 style="font-size:13px;font-weight:bold;color:#000;margin-top:2px;">BỘ MÔN: <?= strtoupper(htmlspecialchars($specialization)) ?></h2>
                <?php endif; ?>
                <p class="meta">
                    Năm học: <strong>2025 – 2026</strong> &nbsp;|&nbsp; 
                    Ngày in: <strong><?= date('d/m/Y') ?></strong> &nbsp;|&nbsp; 
                    Tổng số: <strong><?= count($teachers) ?></strong> giáo viên
                </p>
            </div>

            <!-- TEACHER DATA TABLE -->
            <table class="teacher-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">STT</th>
                        <th style="width: 15%;">MÃ GV</th>
                        <th style="width: 32%;">HỌ VÀ TÊN</th>
                        <th style="width: 10%;">GIỚI TÍNH</th>
                        <th style="width: 15%;">NGÀY SINH</th>
                        <th style="width: 22%;">SỐ ĐIỆN THOẠI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teachers)): ?>
                        <?php foreach ($teachers as $idx => $t): ?>
                        <tr>
                            <td class="c"><?= $idx + 1 ?></td>
                            <td class="c b mono"><?= htmlspecialchars($t['teacher_code']) ?></td>
                            <td class="l b"><?= htmlspecialchars($t['full_name']) ?></td>
                            <td class="c"><?= ($t['gender'] ?? '') === 'female' ? 'Nữ' : 'Nam' ?></td>
                            <td class="c"><?= !empty($t['dob']) ? date('d/m/Y', strtotime($t['dob'])) : '—' ?></td>
                            <td class="c mono"><?= htmlspecialchars($t['phone'] ?? '—') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="c" style="padding: 20px;">Không có dữ liệu giáo viên phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- SIGNATURES BLOCK -->
            <div class="signatures">
                <div class="sig-col">
                    <p class="sig-title">NGƯỜI LẬP BIỂU</p>
                    <p class="sig-hint">(Ký và ghi rõ họ tên)</p>
                    <p class="sig-name">&nbsp;</p>
                </div>
                <div class="sig-col">
                    <p class="sig-title">TỔ TRƯỞNG CHUYÊN MÔN</p>
                    <p class="sig-hint">(Ký và ghi rõ họ tên)</p>
                    <p class="sig-name">&nbsp;</p>
                </div>
                <div class="sig-col">
                    <p style="font-style:italic;font-size:11.5px;margin-bottom:3px;">Ngày <?= date('d') ?> tháng <?= date('m') ?> năm <?= date('Y') ?></p>
                    <p class="sig-title">HIỆU TRƯỞNG / BAN GIÁM HIỆU</p>
                    <p class="sig-hint">(Ký tên và đóng dấu)</p>
                    <p class="sig-name">&nbsp;</p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>