<?php
// database/seeder.php
require_once __DIR__ . '/../core/Database.php';

echo "=== STARTING SEEDING FOR 5 CLASSES (Lớp 1, 2, 3, 4, 5) ===\n";

try {
    $pdo = Database::getConnection();

    // Disable foreign key checks during seed
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Clear operational tables
    $tables = [
        'users', 'academic_years', 'semesters', 'classes', 'teachers', 'students',
        'subjects', 'teacher_subjects', 'grade_records', 'schedules',
        'assignments', 'assignment_submissions', 'student_promotions', 'audit_logs'
    ];
    foreach ($tables as $t) {
        $pdo->exec("TRUNCATE TABLE `{$t}`");
    }

    echo "✔ Cleaned existing data tables.\n";

    // 1. ACADEMIC YEARS
    $stmt = $pdo->prepare("INSERT INTO academic_years (id, code, name, start_date, end_date, is_current, is_locked) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, '2025-2026', 'Năm học 2025 – 2026', '2025-09-05', '2026-05-30', 1, 0]);
    $stmt->execute([2, '2024-2025', 'Năm học 2024 – 2025', '2024-09-05', '2025-05-30', 0, 1]);
    echo "✔ Academic years seeded.\n";

    // 2. SEMESTERS
    $stmt = $pdo->prepare("INSERT INTO semesters (id, academic_year_id, code, name, start_date, end_date, is_current) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 1, 'HK1', 'Học kỳ I', '2025-09-05', '2026-01-15', 0]);
    $stmt->execute([2, 1, 'HK2', 'Học kỳ II', '2026-01-16', '2026-05-30', 1]);
    $stmt->execute([3, 1, 'CN', 'Cả năm', '2025-09-05', '2026-05-30', 0]);
    echo "✔ Semesters seeded.\n";

    // 3. 5 CLASSES (Lớp 1, 2, 3, 4, 5)
    $classesData = [
        [1, 'LOP1', 'Lớp 1', 1, 1, 1, 'Phòng 101', 35],
        [2, 'LOP2', 'Lớp 2', 2, 1, 2, 'Phòng 102', 35],
        [3, 'LOP3', 'Lớp 3', 3, 1, 3, 'Phòng 201', 35],
        [4, 'LOP4', 'Lớp 4', 4, 1, 4, 'Phòng 202', 35],
        [5, 'LOP5', 'Lớp 5', 5, 1, 5, 'Phòng 301', 35],
    ];
    $classStmt = $pdo->prepare("INSERT INTO classes (id, code, name, grade_id, academic_year_id, homeroom_teacher_id, room_number, max_students, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
    foreach ($classesData as $c) {
        $classStmt->execute($c);
    }
    echo "✔ Exactly 5 Classes (Lớp 1, 2, 3, 4, 5) created.\n";

    // 4. SUBJECTS (MÔN HỌC)
    $subjectsData = [
        [1, 'TOAN', 'Toán Học', 4, 2.0],
        [2, 'TV', 'Tiếng Việt', 4, 2.0],
        [3, 'ANH', 'Tiếng Anh', 3, 1.5],
        [4, 'TNXH', 'Tự Nhiên & Xã Hội', 2, 1.0],
        [5, 'DD', 'Đạo Đức', 1, 1.0],
        [6, 'TIN', 'Tin Học', 2, 1.0],
        [7, 'MT', 'Mỹ Thuật', 1, 1.0],
        [8, 'AN', 'Âm Nhạc', 1, 1.0],
    ];
    $stmt = $pdo->prepare("INSERT INTO subjects (id, code, name, periods_per_week, coefficient, status) VALUES (?, ?, ?, ?, ?, 'active')");
    foreach ($subjectsData as $sub) {
        $stmt->execute($sub);
    }
    echo "✔ Subjects seeded.\n";

    // Passwords hash (BCrypt)
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $teacherPass = password_hash('teacher123', PASSWORD_DEFAULT);
    $studentPass = password_hash('student123', PASSWORD_DEFAULT);

    // 5. USERS - SUPER ADMIN & ADMIN
    $userStmt = $pdo->prepare("INSERT INTO users (id, username, password_hash, full_name, email, phone, avatar, role_id, status, last_login_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
    $userStmt->execute([1, 'admin', $adminPass, 'ThS. Nguyễn Văn Quản Trị', 'admin@edumanage.edu.vn', '0901234567', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150', 1]);
    $userStmt->execute([2, 'bgh_hieupho', $adminPass, 'TS. Trần Thị Minh Châu', 'chau.tran@edumanage.edu.vn', '0902345678', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150', 2]);

    // 6. 10 TEACHERS & USERS
    $teacherNames = [
        ['ThS. Nguyễn Văn An', 'Toán Học', 'male', '0911000001', 'an.nguyen@edumanage.edu.vn'],
        ['Cô Lê Thị Bích', 'Tiếng Việt', 'female', '0911000002', 'bich.le@edumanage.edu.vn'],
        ['Thầy Trần Hoàng Cường', 'Tiếng Anh', 'male', '0911000003', 'cuong.tran@edumanage.edu.vn'],
        ['Cô Phạm Thu Dung', 'Tự Nhiên & Xã Hội', 'female', '0911000004', 'dung.pham@edumanage.edu.vn'],
        ['Thầy Hoàng Minh Đức', 'Toán Học', 'male', '0911000005', 'duc.hoang@edumanage.edu.vn'],
        ['Cô Đỗ Hải Yến', 'Tiếng Việt', 'female', '0911000006', 'yen.do@edumanage.edu.vn'],
        ['Thầy Vũ Tiến Giang', 'Tin Học', 'male', '0911000007', 'giang.vu@edumanage.edu.vn'],
        ['Cô Ngô Mỹ Hạnh', 'Mỹ Thuật', 'female', '0911000008', 'hanh.ngo@edumanage.edu.vn'],
        ['Thầy Đinh Quang Khải', 'Âm Nhạc', 'male', '0911000009', 'khai.dinh@edumanage.edu.vn'],
        ['Cô Bùi Thanh Lam', 'Đạo Đức', 'female', '0911000010', 'lam.bui@edumanage.edu.vn'],
    ];

    $teacherStmt = $pdo->prepare("INSERT INTO teachers (id, user_id, teacher_code, full_name, gender, dob, phone, email, address, qualification, specialization, start_date, avatar, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");

    $userIdCounter = 3;
    $teacherId = 1;
    foreach ($teacherNames as $t) {
        $uName = "teacher" . $teacherId;
        $avatar = $t[2] === 'female' 
            ? "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150" 
            : "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150";

        $userStmt->execute([$userIdCounter, $uName, $teacherPass, $t[0], $t[4], $t[3], $avatar, 3]);
        $teacherCode = sprintf("GV25%03d", $teacherId);
        $teacherStmt->execute([
            $teacherId,
            $userIdCounter,
            $teacherCode,
            $t[0],
            $t[2],
            '1988-05-12',
            $t[3],
            $t[4],
            'Quận 10, TP. Hồ Chí Minh',
            'Cử nhân Sư phạm',
            $t[1],
            '2020-09-01',
            $avatar
        ]);
        $userIdCounter++;
        $teacherId++;
    }
    echo "✔ Teachers created.\n";

    // 7. ASSIGN TEACHER TO SUBJECTS & CLASSES
    $tsStmt = $pdo->prepare("INSERT INTO teacher_subjects (teacher_id, subject_id, class_id) VALUES (?, ?, ?)");
    for ($cId = 1; $cId <= 5; $cId++) {
        for ($sId = 1; $sId <= 8; $sId++) {
            $tId = (($sId + $cId) % 10) + 1;
            $tsStmt->execute([$tId, $sId, $cId]);
        }
    }
    echo "✔ Teacher-Subject-Class mappings initialized.\n";

    // 8. 60 STUDENTS & ACCOUNTS (Distributed across 5 Classes, 12 students per class)
    $firstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý'];
    $middleMale = ['Văn', 'Hữu', 'Minh', 'Đức', 'Gia', 'Bảo', 'Tuấn', 'Quang', 'Khắc', 'Đình'];
    $middleFemale = ['Thị', 'Ngọc', 'Mai', 'Thanh', 'Như', 'Quỳnh', 'Phương', 'Khánh', 'Tường'];
    $lastMale = ['An', 'Bình', 'Cường', 'Dũng', 'Đạt', 'Hải', 'Hiếu', 'Huy', 'Khoa', 'Long', 'Nam', 'Phong', 'Quân', 'Sơn', 'Tâm', 'Thịnh', 'Trung', 'Tùng', 'Vinh'];
    $lastFemale = ['Anh', 'Châu', 'Chi', 'Dung', 'Hà', 'Hân', 'Hương', 'Linh', 'Mai', 'Nhi', 'Nhung', 'Oanh', 'Quyên', 'Thảo', 'Trang', 'Tuyết', 'Uyên', 'Vân', 'Vy', 'Yến'];
    $khmerNames = [
        'Sokha Chan', 'Chhay Meas', 'Dara Seng', 'Bopha Vorn', 'Kosal Rin', 'Maly Heng', 'Piseth Chea', 'Rithy Tep', 'Sophea Kim', 'Veasna Nou'
    ];

    $studentStmt = $pdo->prepare("INSERT INTO students (id, user_id, student_code, full_name, khmer_name, gender, dob, pob, address, phone, email, avatar, class_id, grade_id, academic_year_id, admission_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'studying')");

    $totalStudents = 60;
    for ($i = 1; $i <= $totalStudents; $i++) {
        $isMale = ($i % 2 === 1);
        $fn = $firstNames[array_rand($firstNames)];
        $mn = $isMale ? $middleMale[array_rand($middleMale)] : $middleFemale[array_rand($middleFemale)];
        $ln = $isMale ? $lastMale[array_rand($lastMale)] : $lastFemale[array_rand($lastFemale)];
        $fullName = "{$fn} {$mn} {$ln}";
        $khmer = $khmerNames[$i % count($khmerNames)];
        $gender = $isMale ? 'male' : 'female';
        
        // Class 1 to 5 (12 students each)
        $classId = (($i - 1) % 5) + 1;
        $birthYear = 2018 - ($classId - 1); // Class 1 is ~6-7 yrs old (born 2018)
        $birthMonth = sprintf("%02d", rand(1, 12));
        $birthDay = sprintf("%02d", rand(1, 28));
        $dob = "{$birthYear}-{$birthMonth}-{$birthDay}";
        
        $sCode = sprintf("HS%02d%03d", $classId, $i);
        $uName = "student" . $i;
        $email = "hs{$i}@edumanage.edu.vn";
        $phone = sprintf("098%07d", $i);
        $avatar = $isMale 
            ? "https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150" 
            : "https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150";

        $userStmt->execute([$userIdCounter, $uName, $studentPass, $fullName, $email, $phone, $avatar, 4]);
        
        $studentStmt->execute([
            $i,
            $userIdCounter,
            $sCode,
            $fullName,
            $khmer,
            $gender,
            $dob,
            'TP. Hồ Chí Minh',
            'Số ' . rand(10, 250) . ' Đường Nguyễn Trãi, Quận 5, TP.HCM',
            $phone,
            $email,
            $avatar,
            $classId,
            $classId, // grade_id matches classId for simplicity
            1, // Academic year 1
            '2025-09-01'
        ]);

        $userIdCounter++;
    }
    echo "✔ {$totalStudents} Students distributed evenly across Lớp 1, 2, 3, 4, 5.\n";

    // 9. GRADE RECORDS (ĐIỂM SỐ)
    $gradeStmt = $pdo->prepare("INSERT INTO grade_records (student_id, subject_id, class_id, semester_id, component_id, score, is_draft, is_locked, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    for ($sId = 1; $sId <= $totalStudents; $sId++) {
        $cId = (($sId - 1) % 5) + 1;
        for ($subId = 1; $subId <= 5; $subId++) {
            $baseScore = rand(65, 95) / 10.0;
            for ($comp = 1; $comp <= 5; $comp++) {
                $variance = (rand(-10, 10)) / 10.0;
                $score = max(4.5, min(10.0, round($baseScore + $variance, 1)));
                $isLocked = ($comp <= 4) ? 1 : 0;
                $gradeStmt->execute([$sId, $subId, $cId, 2, $comp, $score, 0, $isLocked, 'Đánh giá định kỳ']);
            }
        }
    }
    echo "✔ Grade records populated.\n";

    // 10. SCHEDULES (THỜI KHÓA BIỂU CHO 5 LỚP)
    $schStmt = $pdo->prepare("INSERT INTO schedules (academic_year_id, semester_id, class_id, subject_id, teacher_id, day_of_week, period_start, period_end, room) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    for ($cId = 1; $cId <= 5; $cId++) {
        // Mon -> Fri (days 2..6)
        for ($d = 2; $d <= 6; $d++) {
            $subId = (($d + $cId) % 8) + 1;
            $tId = (($subId + $cId) % 10) + 1;
            $schStmt->execute([1, 2, $cId, $subId, $tId, $d, 1, 2, 'Phòng 10' . $cId]);
            
            $subId2 = (($d + $cId + 3) % 8) + 1;
            $tId2 = (($subId2 + $cId) % 10) + 1;
            $schStmt->execute([1, 2, $cId, $subId2, $tId2, $d, 3, 4, 'Phòng 10' . $cId]);
        }
    }
    echo "✔ Weekly Timetable seeded for all 5 classes.\n";

    // 11. ASSIGNMENTS
    $asgStmt = $pdo->prepare("INSERT INTO assignments (id, title, description, teacher_id, subject_id, class_id, due_date, max_score, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'published')");
    for ($cId = 1; $cId <= 5; $cId++) {
        $asgStmt->execute([
            $cId * 2 - 1,
            "Bài tập Toán tuần cho Lớp {$cId}",
            "Hoàn thành các bài tập trong SGK trang 45-48 và ghi vào vở bài tập.",
            1,
            1,
            $cId,
            date('Y-m-d 23:59:59', strtotime('+5 days')),
            10.0
        ]);
        $asgStmt->execute([
            $cId * 2,
            "Bài tập Tiếng Việt đọc hiểu Lớp {$cId}",
            "Đọc bài tập đọc và trả lời câu hỏi 1, 2, 3 vào vở.",
            2,
            2,
            $cId,
            date('Y-m-d 23:59:59', strtotime('+7 days')),
            10.0
        ]);
    }
    echo "✔ Assignments created.\n";

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "\n=== SEEDING COMPLETED PERFECTLY! ===\n";

} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
}
