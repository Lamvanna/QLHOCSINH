<div class="w-full space-y-6">

    <!-- Breadcrumb Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="<?= BASE_URL ?>/students" class="hover:text-emerald-600 transition-colors">Học sinh</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-emerald-600"><?= $isEdit ? 'Cập nhật hồ sơ' : 'Thêm mới' ?></span>
        </div>
        <a href="<?= $isEdit ? BASE_URL . '/students/' . $student['id'] : BASE_URL . '/students' ?>" 
           class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:shadow-sm transition-all">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Quay lại</span>
        </a>
    </div>

    <!-- Page Title -->
    <div class="bg-gradient-to-r from-slate-900 to-emerald-900 dark:from-slate-800 dark:to-emerald-950 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-60 h-60 bg-blue-500/10 rounded-full blur-3xl -ml-10 -mb-10 pointer-events-none"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl"><?= $isEdit ? 'edit_note' : 'person_add' ?></span>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight"><?= $isEdit ? 'Cập Nhật Hồ Sơ Học Sinh' : 'Thêm Học Sinh Mới' ?></h1>
                <p class="text-emerald-200/80 text-xs mt-1"><?= $isEdit ? 'Chỉnh sửa thông tin cá nhân và học tập — mọi thay đổi được lưu và ghi nhật ký.' : 'Nhập đầy đủ thông tin cá nhân và học tập để đăng ký học sinh mới vào hệ thống.' ?></p>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <form onsubmit="handleSubmitStudentForm(event)" id="studentForm" novalidate>
        <div class="space-y-5">

            <!-- ============================================ -->
            <!-- SECTION 1: THÔNG TIN CÁ NHÂN                -->
            <!-- ============================================ -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <!-- Section Header -->
                <div class="flex items-center gap-3 px-6 md:px-8 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/30">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Thông Tin Cá Nhân</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Ảnh thẻ, họ tên, giới tính, ngày sinh và địa chỉ</p>
                    </div>
                    <span class="ml-auto text-[10px] font-bold text-rose-500 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-full border border-rose-100 dark:border-rose-900">Bắt buộc *</span>
                </div>

                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
                        <!-- Photo Upload -->
                        <div class="col-span-1 flex flex-col items-center gap-3">
                            <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/60 dark:to-slate-800 flex flex-col items-center justify-center overflow-hidden relative cursor-pointer hover:border-emerald-400 hover:from-emerald-50/50 hover:to-teal-50/50 dark:hover:border-emerald-600 transition-all group shadow-inner">
                                <?php if (!empty($student['avatar'])): ?>
                                <img src="<?= htmlspecialchars($student['avatar']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600 group-hover:text-emerald-400 transition-colors mb-1">add_a_photo</span>
                                <span class="text-[10px] font-medium text-slate-400 group-hover:text-emerald-500 transition-colors">Tải ảnh lên</span>
                                <?php endif; ?>
                                <input accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" type="file"/>
                            </div>
                            <div class="text-center">
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Ảnh Thẻ Học Sinh</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">JPG, PNG tối đa 2MB</p>
                            </div>
                        </div>

                        <!-- Fields -->
                        <div class="col-span-1 md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Họ và Tên - full width -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Họ và Tên Đầy Đủ
                                    <span class="text-rose-500 ml-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">person</span>
                                    <input type="text" name="full_name" required 
                                           value="<?= htmlspecialchars($student['full_name'] ?? '') ?>" 
                                           placeholder="VD: Nguyễn Văn A"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white placeholder:text-slate-400 placeholder:font-normal transition-all">
                                </div>
                            </div>

                            <!-- Tên Khmer -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tên Tiếng Khmer</label>
                                <input type="text" name="khmer_name" 
                                       value="<?= htmlspecialchars($student['khmer_name'] ?? '') ?>" 
                                       placeholder="Sokha Chan"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium placeholder:text-slate-400 text-slate-900 dark:text-white transition-all">
                            </div>

                            <!-- Giới Tính -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Giới Tính <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex gap-3 pt-1">
                                    <label class="flex-1 flex items-center gap-2 cursor-pointer px-3.5 py-2.5 rounded-xl border-2 transition-all <?= ($student['gender'] ?? 'male') === 'male' ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400' ?>" id="genderMaleLabel">
                                        <input type="radio" name="gender" value="male" <?= ($student['gender'] ?? 'male') === 'male' ? 'checked' : '' ?> class="hidden" onchange="updateGenderUI(this)">
                                        <span class="material-symbols-outlined text-[18px]">man</span>
                                        <span class="text-xs font-bold">Nam</span>
                                    </label>
                                    <label class="flex-1 flex items-center gap-2 cursor-pointer px-3.5 py-2.5 rounded-xl border-2 transition-all <?= ($student['gender'] ?? '') === 'female' ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400' ?>" id="genderFemaleLabel">
                                        <input type="radio" name="gender" value="female" <?= ($student['gender'] ?? '') === 'female' ? 'checked' : '' ?> class="hidden" onchange="updateGenderUI(this)">
                                        <span class="material-symbols-outlined text-[18px]">woman</span>
                                        <span class="text-xs font-bold">Nữ</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Ngày Sinh -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Ngày Sinh <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">cake</span>
                                    <input type="date" name="dob" required 
                                           value="<?= $student['dob'] ?? '2010-01-01' ?>"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white transition-all">
                                </div>
                            </div>

                            <!-- Số Điện Thoại -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Số Điện Thoại</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">phone</span>
                                    <input type="text" name="phone" 
                                           value="<?= htmlspecialchars($student['phone'] ?? '') ?>" 
                                           placeholder="0901 234 567"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium placeholder:text-slate-400 text-slate-900 dark:text-white transition-all">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Email Học Sinh</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
                                    <input type="email" name="email" 
                                           value="<?= htmlspecialchars($student['email'] ?? '') ?>" 
                                           placeholder="hocsinh@email.com"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium placeholder:text-slate-400 text-slate-900 dark:text-white transition-all">
                                </div>
                            </div>

                            <!-- Địa Chỉ - full width -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Địa Chỉ Thường Trú</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-slate-400 text-[18px]">location_on</span>
                                    <input type="text" name="address" 
                                           value="<?= htmlspecialchars($student['address'] ?? '') ?>" 
                                           placeholder="Số nhà, Đường, Phường/Xã, Quận/Huyện, Tỉnh/TP"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium placeholder:text-slate-400 text-slate-900 dark:text-white transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION 2: THÔNG TIN HỌC TẬP               -->
            <!-- ============================================ -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 px-6 md:px-8 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/30">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
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
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-500 text-[18px]">badge</span>
                            <input type="text" name="student_code" required 
                                   value="<?= htmlspecialchars($student['student_code'] ?? '') ?>" 
                                   <?= $isEdit ? 'readonly' : '' ?>
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-black text-emerald-600 dark:text-emerald-400 transition-all <?= $isEdit ? 'cursor-not-allowed opacity-80' : '' ?>">
                        </div>
                        <?php if ($isEdit): ?>
                        <p class="text-[10px] text-slate-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">lock</span> Mã HS không thể thay đổi.
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
                            <select name="academic_year_id" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white appearance-none transition-all cursor-pointer">
                                <?php foreach ($years as $y): ?>
                                <option value="<?= $y['id'] ?>" <?= $y['is_current'] ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <!-- Lớp Học -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Lớp Học Phân Công <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">class</span>
                            <select name="class_id" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white appearance-none transition-all cursor-pointer">
                                <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($student['class_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION 3: TRẠNG THÁI NHẬP HỌC             -->
            <!-- ============================================ -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 px-6 md:px-8 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/30">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Trạng Thái Nhập Học</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tình trạng hiện tại của học sinh trong năm học</p>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <?php 
                        $statusOptions = [
                            ['value' => 'studying',    'label' => 'Đang Học',           'desc' => 'Học sinh đang theo học chính thức',     'icon' => 'check_circle',     'color' => 'emerald'],
                            ['value' => 'transferred', 'label' => 'Chuyển Trường',       'desc' => 'Học sinh đã xin chuyển sang trường khác', 'icon' => 'swap_horiz',       'color' => 'blue'],
                            ['value' => 'graduated',   'label' => 'Tốt Nghiệp / Bảo Lưu', 'desc' => 'Đã hoàn thành chương trình hoặc bảo lưu', 'icon' => 'school',           'color' => 'slate'],
                        ];
                        foreach ($statusOptions as $opt):
                            $currentStatus = $student['status'] ?? 'studying';
                            $isChecked = $currentStatus === $opt['value'];
                            $colors = [
                                'emerald' => ['border' => 'border-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/40', 'icon' => 'text-emerald-600 dark:text-emerald-400', 'title' => 'text-emerald-700 dark:text-emerald-300'],
                                'blue'    => ['border' => 'border-blue-500',    'bg' => 'bg-blue-50 dark:bg-blue-950/40',       'icon' => 'text-blue-600 dark:text-blue-400',       'title' => 'text-blue-700 dark:text-blue-300'],
                                'slate'   => ['border' => 'border-slate-400',   'bg' => 'bg-slate-50 dark:bg-slate-800/60',     'icon' => 'text-slate-500 dark:text-slate-400',     'title' => 'text-slate-700 dark:text-slate-300'],
                            ];
                            $c = $colors[$opt['color']];
                        ?>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="status" value="<?= $opt['value'] ?>" <?= $isChecked ? 'checked' : '' ?> class="peer hidden" onchange="updateStatusUI()">
                            <div class="h-full p-4 rounded-2xl border-2 transition-all duration-200
                                <?= $isChecked ? $c['border'] . ' ' . $c['bg'] : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600' ?>
                                peer-checked:<?= $c['border'] ?> peer-checked:<?= $c['bg'] ?>">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-2xl <?= $isChecked ? $c['icon'] : 'text-slate-400 group-hover:text-slate-500' ?> transition-colors"><?= $opt['icon'] ?></span>
                                    <div>
                                        <p class="text-xs font-extrabold <?= $isChecked ? $c['title'] : 'text-slate-700 dark:text-slate-300' ?>"><?= $opt['label'] ?></p>
                                        <p class="text-[10px] text-slate-400 mt-0.5 leading-relaxed"><?= $opt['desc'] ?></p>
                                    </div>
                                </div>
                                <?php if ($isChecked): ?>
                                <div class="absolute top-3 right-3 w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-[12px]">check</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- FORM ACTIONS                                 -->
            <!-- ============================================ -->
            <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-2 pb-6">
                <a href="<?= $isEdit ? BASE_URL . '/students/' . $student['id'] : BASE_URL . '/students' ?>" 
                   class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 text-xs font-bold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:shadow-sm transition-all">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    <span>Hủy Bỏ</span>
                </a>
                <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-3 text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-2xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span><?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Hồ Sơ Học Sinh' ?></span>
                </button>
            </div>

        </div>
    </form>
</div>

<script>
    function updateGenderUI(radio) {
        const maleLabel   = document.getElementById('genderMaleLabel');
        const femaleLabel = document.getElementById('genderFemaleLabel');
        maleLabel.className   = maleLabel.className.replace(/border-(blue|rose)-500 bg-(blue|rose)-50 dark:bg-(blue|rose)-950\/40 text-(blue|rose)-700 dark:text-(blue|rose)-300/g, '');
        femaleLabel.className = femaleLabel.className.replace(/border-(blue|rose)-500 bg-(blue|rose)-50 dark:bg-(blue|rose)-950\/40 text-(blue|rose)-700 dark:text-(blue|rose)-300/g, '');
        if (radio.value === 'male') {
            maleLabel.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-950/40', 'text-blue-700');
            femaleLabel.classList.remove('border-rose-500', 'bg-rose-50');
            femaleLabel.className = 'flex-1 flex items-center gap-2 cursor-pointer px-3.5 py-2.5 rounded-xl border-2 transition-all border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400';
        } else {
            femaleLabel.classList.add('border-rose-500', 'bg-rose-50', 'dark:bg-rose-950/40', 'text-rose-700');
            maleLabel.className = 'flex-1 flex items-center gap-2 cursor-pointer px-3.5 py-2.5 rounded-xl border-2 transition-all border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400';
        }
    }

    function updateStatusUI() {
        // Refresh all status cards on change
        document.querySelectorAll('input[name="status"]').forEach(radio => {
            const label = radio.closest('label')?.querySelector('div[class*="rounded-2xl"]');
        });
    }

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
