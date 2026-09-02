<?php
// views/students/form.php - Clean, Professional Student Form matching Design System
$isEdit = !empty($student['id']);
$pageTitle = $isEdit ? 'Cập Nhật Hồ Sơ Học Sinh' : 'Thêm Học Sinh Mới';
$currentGender = $student['gender'] ?? 'male';
$currentStatus = $student['status'] ?? 'studying';
?>

<style>
.bg-secondary { background-color: #006c4a !important; }
.text-secondary { color: #006c4a !important; }
.border-secondary { border-color: #006c4a !important; }
.hover\:bg-secondary-dark:hover { background-color: #005137 !important; }
.border-border-subtle { border-color: #e2e8f0 !important; }
.bg-surface-bg { background-color: #f8fafc !important; }
</style>

<div class="w-full space-y-6 pb-20">

    <!-- 1. BREADCRUMBS & TOP BACK BUTTON -->
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-1.5 text-xs font-semibold text-slate-500">
            <a href="<?= BASE_URL ?>/students" class="hover:text-emerald-600 transition-colors">Học sinh</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-emerald-700 dark:text-emerald-400 font-bold"><?= $isEdit ? 'Cập nhật hồ sơ' : 'Thêm mới' ?></span>
        </nav>
        <a href="<?= $isEdit ? BASE_URL . '/students/' . $student['id'] : BASE_URL . '/students' ?>" 
           class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-900 border border-border-subtle rounded-xl hover:shadow-sm transition-all">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Quay lại</span>
        </a>
    </div>

    <!-- 2. PAGE TITLE BANNER (Matching Mockup Dark Hero Card) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-sm">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl text-emerald-400"><?= $isEdit ? 'badge' : 'person_add' ?></span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-black tracking-tight"><?= $pageTitle ?></h1>
                <p class="text-slate-300 text-xs mt-1">Chỉnh sửa thông tin cá nhân và học tập — mọi thay đổi được lưu và ghi nhật ký.</p>
            </div>
        </div>
    </div>

    <!-- 3. FORM CONTAINER -->
    <form onsubmit="handleSubmitStudentForm(event)" id="studentForm" novalidate class="space-y-6">
        <input type="hidden" name="avatar" id="avatarInput" value="<?= htmlspecialchars($student['avatar'] ?? '') ?>">

        <!-- ============================================ -->
        <!-- SECTION 1: THÔNG TIN CÁ NHÂN                -->
        <!-- ============================================ -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-border-subtle shadow-sm overflow-hidden">
            <!-- Section Header -->
            <div class="flex items-center gap-3 px-6 md:px-8 py-4 border-b border-border-subtle bg-surface-bg dark:bg-slate-800/40">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Thông Tin Cá Nhân</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Ảnh thẻ, họ tên, giới tính, ngày sinh và địa chỉ</p>
                </div>
                <span class="ml-auto text-[10px] font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/60 px-2.5 py-0.5 rounded-full border border-rose-200 dark:border-rose-900">
                    Bắt buộc *
                </span>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8 items-start">
                    
                    <!-- Avatar Photo Upload Box -->
                    <div class="col-span-1 flex flex-col items-center gap-2.5">
                        <div class="relative group">
                            <div class="w-28 h-28 md:w-32 md:h-32 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 overflow-hidden flex items-center justify-center shadow-inner relative">
                                <img id="avatarPreview" 
                                     src="<?= htmlspecialchars($student['avatar'] ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=300') ?>" 
                                     alt="Student Avatar" 
                                     class="w-full h-full object-cover">
                                <label for="avatarFileUpload" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white cursor-pointer">
                                    <span class="material-symbols-outlined text-2xl">photo_camera</span>
                                    <span class="text-[10px] font-bold mt-1">Đổi ảnh</span>
                                </label>
                            </div>
                            <!-- Pen badge -->
                            <label for="avatarFileUpload" class="absolute -bottom-1.5 -right-1.5 w-7 h-7 bg-white dark:bg-slate-800 rounded-full border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-emerald-600 cursor-pointer transition-colors">
                                <span class="material-symbols-outlined text-[15px]">edit</span>
                            </label>
                        </div>
                        <input type="file" id="avatarFileUpload" accept="image/*" class="hidden" onchange="handleAvatarChange(this)">
                        <div class="text-center">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Ảnh Thẻ Học Sinh</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">JPG, PNG tối đa 2MB</p>
                        </div>
                    </div>

                    <!-- Fields Grid -->
                    <div class="col-span-1 md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Họ và Tên Đầy Đủ (Full Width) -->
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Họ và Tên Đầy Đủ <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">person</span>
                                <input type="text" name="full_name" required 
                                       value="<?= htmlspecialchars($student['full_name'] ?? '') ?>" 
                                       placeholder="Ví dụ: Hoàng Phương Hân"
                                       class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-border-subtle rounded-xl text-xs font-bold text-slate-900 dark:text-white placeholder:text-slate-400 placeholder:font-normal focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                        </div>

                        <!-- Tên Tiếng Khmer -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tên Tiếng Khmer</label>
                            <input type="text" name="khmer_name" 
                                   value="<?= htmlspecialchars($student['khmer_name'] ?? '') ?>" 
                                   placeholder="Ví dụ: Chhay Meas"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 border border-border-subtle rounded-xl text-xs font-medium placeholder:text-slate-400 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        </div>

                        <!-- Giới Tính (2 Buttons Toggle) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Giới Tính <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex gap-3">
                                <label id="genderMaleLabel" class="flex-1 flex items-center justify-center gap-1.5 cursor-pointer px-3.5 py-2.5 rounded-xl border transition-all <?= $currentGender === 'male' ? 'border-blue-500 bg-blue-50/70 text-blue-700 font-bold' : 'border-border-subtle bg-white text-slate-600' ?>">
                                    <input type="radio" name="gender" value="male" <?= $currentGender === 'male' ? 'checked' : '' ?> class="hidden" onchange="updateGenderUI(this)">
                                    <span class="material-symbols-outlined text-[17px]">man</span>
                                    <span class="text-xs">Nam</span>
                                </label>
                                <label id="genderFemaleLabel" class="flex-1 flex items-center justify-center gap-1.5 cursor-pointer px-3.5 py-2.5 rounded-xl border transition-all <?= $currentGender === 'female' ? 'border-rose-500 bg-rose-50/70 text-rose-700 font-bold' : 'border-border-subtle bg-white text-slate-600' ?>">
                                    <input type="radio" name="gender" value="female" <?= $currentGender === 'female' ? 'checked' : '' ?> class="hidden" onchange="updateGenderUI(this)">
                                    <span class="material-symbols-outlined text-[17px]">woman</span>
                                    <span class="text-xs">Nữ</span>
                                </label>
                            </div>
                        </div>

                        <!-- Ngày Sinh -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Ngày Sinh <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">calendar_today</span>
                                <input type="date" name="dob" required 
                                       value="<?= htmlspecialchars($student['dob'] ?? '') ?>" 
                                       class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-border-subtle rounded-xl text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                        </div>

                        <!-- Số Điện Thoại -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Số Điện Thoại</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">call</span>
                                <input type="text" name="phone" 
                                       value="<?= htmlspecialchars($student['phone'] ?? '') ?>" 
                                       placeholder="Ví dụ: 0980000021"
                                       class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-border-subtle rounded-xl text-xs font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                        </div>

                        <!-- Email Học Sinh (Full Width) -->
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Email Học Sinh</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
                                <input type="email" name="email" 
                                       value="<?= htmlspecialchars($student['email'] ?? '') ?>" 
                                       placeholder="hs21@edumanage.edu.vn"
                                       class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-border-subtle rounded-xl text-xs font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                        </div>

                        <!-- Địa Chỉ Thường Trú (Full Width) -->
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Địa Chỉ Thường Trú</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">location_on</span>
                                <input type="text" name="address" 
                                       value="<?= htmlspecialchars($student['address'] ?? '') ?>" 
                                       placeholder="Số nhà, Đường, Phường/Xã, Quận/Huyện, Tỉnh/TP"
                                       class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-border-subtle rounded-xl text-xs font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECTION 2: THÔNG TIN HỌC TẬP                -->
        <!-- ============================================ -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-border-subtle shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 md:px-8 py-4 border-b border-border-subtle bg-surface-bg dark:bg-slate-800/40">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-primary dark:bg-blue-950 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">school</span>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Thông Tin Học Tập</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Mã học sinh, năm học, khối lớp và lớp học phân công</p>
                </div>
            </div>

            <div class="p-6 md:p-8 grid grid-cols-1 sm:grid-cols-3 gap-5">
                
                <!-- Mã Học Sinh -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Mã Học Sinh <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-primary text-[18px]">badge</span>
                        <input type="text" name="student_code" required 
                               value="<?= htmlspecialchars($student['student_code'] ?? '') ?>" 
                               <?= $isEdit ? 'readonly' : '' ?>
                               class="w-full pl-10 pr-4 py-2.5 bg-blue-50/50 dark:bg-slate-800 border border-blue-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold text-primary dark:text-blue-400 <?= $isEdit ? 'cursor-not-allowed' : '' ?>">
                    </div>
                    <?php if ($isEdit): ?>
                    <p class="text-[10px] text-slate-400 flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-[13px]">lock</span>
                        <span>Mã HS không thể thay đổi.</span>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Năm Học -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Năm Học <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">calendar_today</span>
                        <select name="academic_year_id" class="w-full appearance-none pl-10 pr-8 py-2.5 bg-white dark:bg-slate-800 border border-border-subtle rounded-xl text-xs font-semibold text-slate-900 dark:text-white cursor-pointer focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <?php foreach ($years as $y): ?>
                            <option value="<?= $y['id'] ?>" <?= $y['is_current'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <!-- Lớp Học Phân Công -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Lớp Học Phân Công <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">class</span>
                        <select name="class_id" required class="w-full appearance-none pl-10 pr-8 py-2.5 bg-white dark:bg-slate-800 border border-border-subtle rounded-xl text-xs font-semibold text-slate-900 dark:text-white cursor-pointer focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($student['class_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECTION 3: TRẠNG THÁI NHẬP HỌC              -->
        <!-- ============================================ -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-border-subtle shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 md:px-8 py-4 border-b border-border-subtle bg-surface-bg dark:bg-slate-800/40">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">sync</span>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Trạng Thái Nhập Học</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Tình trạng hiện tại của học sinh trong năm học</p>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="statusCardsContainer">
                    
                    <!-- 1. Đang Học -->
                    <label class="relative cursor-pointer group" onclick="selectStatusCard('studying')">
                        <input type="radio" name="status" value="studying" <?= $currentStatus === 'studying' ? 'checked' : '' ?> class="hidden">
                        <div id="statusCard-studying" class="h-full p-4 rounded-2xl border-2 transition-all <?= $currentStatus === 'studying' ? 'border-emerald-500 bg-emerald-50/50' : 'border-border-subtle bg-white hover:border-slate-300' ?>">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-2xl <?= $currentStatus === 'studying' ? 'text-emerald-600' : 'text-slate-400' ?>">check_circle</span>
                                <div>
                                    <p class="text-xs font-bold <?= $currentStatus === 'studying' ? 'text-emerald-800' : 'text-slate-800 dark:text-slate-200' ?>">Đang Học</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Học sinh đang theo học chính thức</p>
                                </div>
                            </div>
                            <div id="statusBadge-studying" class="absolute top-3.5 right-3.5 <?= $currentStatus === 'studying' ? '' : 'hidden' ?>">
                                <span class="material-symbols-outlined text-emerald-600 text-[18px]">check</span>
                            </div>
                        </div>
                    </label>

                    <!-- 2. Chuyển Trường -->
                    <label class="relative cursor-pointer group" onclick="selectStatusCard('transferred')">
                        <input type="radio" name="status" value="transferred" <?= $currentStatus === 'transferred' ? 'checked' : '' ?> class="hidden">
                        <div id="statusCard-transferred" class="h-full p-4 rounded-2xl border-2 transition-all <?= $currentStatus === 'transferred' ? 'border-blue-500 bg-blue-50/50' : 'border-border-subtle bg-white hover:border-slate-300' ?>">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-2xl <?= $currentStatus === 'transferred' ? 'text-blue-600' : 'text-slate-400' ?>">sync_alt</span>
                                <div>
                                    <p class="text-xs font-bold <?= $currentStatus === 'transferred' ? 'text-blue-800' : 'text-slate-800 dark:text-slate-200' ?>">Chuyển Trường</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Học sinh đã xin chuyển sang trường khác</p>
                                </div>
                            </div>
                            <div id="statusBadge-transferred" class="absolute top-3.5 right-3.5 <?= $currentStatus === 'transferred' ? '' : 'hidden' ?>">
                                <span class="material-symbols-outlined text-blue-600 text-[18px]">check</span>
                            </div>
                        </div>
                    </label>

                    <!-- 3. Tốt Nghiệp / Bảo Lưu -->
                    <label class="relative cursor-pointer group" onclick="selectStatusCard('graduated')">
                        <input type="radio" name="status" value="graduated" <?= $currentStatus === 'graduated' ? 'checked' : '' ?> class="hidden">
                        <div id="statusCard-graduated" class="h-full p-4 rounded-2xl border-2 transition-all <?= $currentStatus === 'graduated' ? 'border-purple-500 bg-purple-50/50' : 'border-border-subtle bg-white hover:border-slate-300' ?>">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-2xl <?= $currentStatus === 'graduated' ? 'text-purple-600' : 'text-slate-400' ?>">school</span>
                                <div>
                                    <p class="text-xs font-bold <?= $currentStatus === 'graduated' ? 'text-purple-800' : 'text-slate-800 dark:text-slate-200' ?>">Tốt Nghiệp / Bảo Lưu</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Đã hoàn thành chương trình hoặc bảo lưu</p>
                                </div>
                            </div>
                            <div id="statusBadge-graduated" class="absolute top-3.5 right-3.5 <?= $currentStatus === 'graduated' ? '' : 'hidden' ?>">
                                <span class="material-symbols-outlined text-purple-600 text-[18px]">check</span>
                            </div>
                        </div>
                    </label>

                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- FORM ACTIONS (Clean Bottom Dock, No Overlap)  -->
        <!-- ============================================ -->
        <div class="flex items-center justify-between gap-4 pt-4">
            <a href="<?= $isEdit ? BASE_URL . '/students/' . $student['id'] : BASE_URL . '/students' ?>" 
               class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-xs font-bold text-slate-700 bg-white border border-border-subtle rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[16px]">close</span>
                <span>Hủy Bỏ</span>
            </a>

            <button type="submit" id="submitBtn" 
                    class="inline-flex items-center justify-center gap-2 px-7 py-2.5 text-xs font-bold text-white bg-secondary hover:bg-secondary-dark rounded-xl shadow-sm transition-all active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span><?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Hồ Sơ Học Sinh' ?></span>
            </button>
        </div>

    </form>
</div>

<script>
    // Preview selected image file immediately
    function handleAvatarChange(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
                document.getElementById('avatarInput').value = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle Gender selection styling
    function updateGenderUI(radio) {
        const maleLabel = document.getElementById('genderMaleLabel');
        const femaleLabel = document.getElementById('genderFemaleLabel');

        if (radio.value === 'male') {
            maleLabel.className = 'flex-1 flex items-center justify-center gap-1.5 cursor-pointer px-3.5 py-2.5 rounded-xl border border-blue-500 bg-blue-50/70 text-blue-700 font-bold transition-all';
            femaleLabel.className = 'flex-1 flex items-center justify-center gap-1.5 cursor-pointer px-3.5 py-2.5 rounded-xl border border-border-subtle bg-white text-slate-600 transition-all';
        } else {
            femaleLabel.className = 'flex-1 flex items-center justify-center gap-1.5 cursor-pointer px-3.5 py-2.5 rounded-xl border border-rose-500 bg-rose-50/70 text-rose-700 font-bold transition-all';
            maleLabel.className = 'flex-1 flex items-center justify-center gap-1.5 cursor-pointer px-3.5 py-2.5 rounded-xl border border-border-subtle bg-white text-slate-600 transition-all';
        }
    }

    // Toggle Status cards
    function selectStatusCard(statusKey) {
        const keys = ['studying', 'transferred', 'graduated'];
        const styles = {
            'studying': { border: 'border-emerald-500', bg: 'bg-emerald-50/50', text: 'text-emerald-800' },
            'transferred': { border: 'border-blue-500', bg: 'bg-blue-50/50', text: 'text-blue-800' },
            'graduated': { border: 'border-purple-500', bg: 'bg-purple-50/50', text: 'text-purple-800' }
        };

        keys.forEach(k => {
            const card = document.getElementById('statusCard-' + k);
            const badge = document.getElementById('statusBadge-' + k);
            const input = card.parentElement.querySelector('input[type="radio"]');

            if (k === statusKey) {
                input.checked = true;
                card.className = `h-full p-4 rounded-2xl border-2 transition-all ${styles[k].border} ${styles[k].bg}`;
                badge.classList.remove('hidden');
            } else {
                card.className = 'h-full p-4 rounded-2xl border-2 transition-all border-border-subtle bg-white hover:border-slate-300';
                badge.classList.add('hidden');
            }
        });
    }

    // Submit Handler
    async function handleSubmitStudentForm(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = `<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span><span>Đang lưu...</span>`;

        const data = Object.fromEntries(new FormData(e.target).entries());
        const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
        const studentId = <?= $isEdit ? (int)($student['id'] ?? 0) : '0' ?>;
        const url = isEdit ? `<?= BASE_URL ?>/students/${studentId}` : `<?= BASE_URL ?>/students`;

        const res = await apiPost(url, data);
        if (res) {
            setTimeout(() => {
                location.href = isEdit ? `<?= BASE_URL ?>/students/${studentId}` : `<?= BASE_URL ?>/students`;
            }, 700);
        } else {
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span><?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Hồ Sơ Học Sinh' ?></span>`;
        }
    }
</script>
