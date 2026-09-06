<?php // views/teachers/form.php ?>
<?php
$isEdit = !empty($teacher['id']);
$pageTitle = $isEdit ? 'Cập Nhật Hồ Sơ Giáo Viên' : 'Thêm Giáo Viên Mới';
$currentGender = $teacher['gender'] ?? 'male';
$currentStatus = $teacher['status'] ?? 'active';
?>

<div class="w-full space-y-6 pb-20">

    <!-- 1. BREADCRUMBS & TOP BACK BUTTON -->
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-1.5 text-xs font-semibold text-slate-500">
            <a href="<?= BASE_URL ?>/teachers" class="hover:text-emerald-600 transition-colors">Giáo viên</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-emerald-700 dark:text-emerald-400 font-bold"><?= $isEdit ? 'Cập nhật hồ sơ' : 'Thêm mới' ?></span>
        </nav>
        <a href="<?= BASE_URL ?>/teachers" 
           class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:shadow-sm transition-all">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Quay lại</span>
        </a>
    </div>

    <!-- 2. PAGE TITLE BANNER -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-sm">
        <div class="flex items-center justify-between relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl text-emerald-400"><?= $isEdit ? 'badge' : 'person_add' ?></span>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Quản Lý Đội Ngũ Sư Phạm
                    </span>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight"><?= $pageTitle ?></h1>
                    <p class="text-xs text-slate-400 mt-0.5"><?= $isEdit ? 'Chỉnh sửa thông tin cá nhân, chuyên môn giảng dạy và phân công công tác.' : 'Điền đầy đủ thông tin để tạo hồ sơ và cấp tài khoản giáo viên mới.' ?></p>
                </div>
            </div>
            <?php if ($isEdit): ?>
            <div class="hidden sm:flex items-center gap-2 bg-white/10 backdrop-blur px-3 py-1.5 rounded-xl border border-white/15">
                <span class="text-xs text-slate-300">Mã GV:</span>
                <span class="font-mono font-black text-emerald-400 text-sm"><?= htmlspecialchars($teacher['teacher_code']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. FORM CONTAINER -->
    <form onsubmit="handleSubmitTeacherForm(event)" id="teacherForm" class="space-y-6" novalidate>
        <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $teacher['id'] ?>">
        <?php endif; ?>

        <!-- SECTION 1: THÔNG TIN CÁ NHÂN & ẢNH ĐẠI DIỆN -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Thông Tin Cá Nhân & Ảnh Thẻ</h2>
                    <p class="text-[11px] text-slate-400">Ảnh chân dung đại diện, họ và tên, giới tính và ngày sinh</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8 items-start">
                <!-- Avatar Upload Zone -->
                <div class="flex flex-col items-center text-center">
                    <div class="relative group cursor-pointer" onclick="document.getElementById('teacherAvatarFile').click()">
                        <div class="w-28 h-28 md:w-32 md:h-32 rounded-3xl overflow-hidden border-2 border-dashed border-emerald-500/50 p-1 bg-slate-50 dark:bg-slate-800 transition-all group-hover:border-emerald-500 group-hover:scale-105">
                            <img id="avatarPreview" 
                                 src="<?= htmlspecialchars($teacher['avatar'] ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200') ?>" 
                                 class="w-full h-full object-cover rounded-2xl" 
                                 alt="Avatar Preview">
                        </div>
                        <div class="absolute inset-0 bg-slate-900/40 rounded-3xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[2px]">
                            <span class="material-symbols-outlined text-white text-2xl">photo_camera</span>
                        </div>
                    </div>
                    <input type="file" id="teacherAvatarFile" accept="image/*" class="hidden" onchange="previewTeacherImage(this)">
                    <input type="hidden" name="avatar" id="avatarInput" value="<?= htmlspecialchars($teacher['avatar'] ?? '') ?>">
                    <p class="text-[11px] font-semibold text-slate-500 mt-2.5">Bấm để thay ảnh thẻ</p>
                    <p class="text-[10px] text-slate-400">PNG, JPG tối đa 2MB</p>
                </div>

                <!-- Input Fields -->
                <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã Giáo Viên <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="teacher_code" required 
                               value="<?= htmlspecialchars($teacher['teacher_code'] ?? '') ?>" 
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-mono font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none uppercase" 
                               placeholder="VD: GV25101">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Họ và Tên Đầy Đủ <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="full_name" required 
                               value="<?= htmlspecialchars($teacher['full_name'] ?? '') ?>" 
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" 
                               placeholder="VD: Nguyễn Thành Long">
                    </div>

                    <!-- Gender Radio Buttons -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Giới Tính <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center gap-2 p-2 rounded-xl border cursor-pointer transition-all <?= $currentGender === 'male' ? 'border-blue-500 bg-blue-50/60 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600' ?>">
                                <input type="radio" name="gender" value="male" <?= $currentGender === 'male' ? 'checked' : '' ?> class="hidden" onchange="updateGenderStyle(this)">
                                <span class="material-symbols-outlined text-[18px]">male</span>
                                <span class="text-xs">Nam</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2 rounded-xl border cursor-pointer transition-all <?= $currentGender === 'female' ? 'border-rose-500 bg-rose-50/60 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 font-bold' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600' ?>">
                                <input type="radio" name="gender" value="female" <?= $currentGender === 'female' ? 'checked' : '' ?> class="hidden" onchange="updateGenderStyle(this)">
                                <span class="material-symbols-outlined text-[18px]">female</span>
                                <span class="text-xs">Nữ</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ngày Sinh</label>
                        <input type="date" name="dob" 
                               value="<?= htmlspecialchars($teacher['dob'] ?? '1988-01-01') ?>" 
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>
            </div>
        </div>
        <!-- SECTION 2: CHUYÊN MÔN & PHÂN CÔNG CHỦ NHIỆM (ĐÃ BỎ TRÌNH ĐỘ HỌC VẤN / BẰNG CẤP) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">school</span>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Chuyên Môn & Phân Công Chủ Nhiệm</h2>
                    <p class="text-[11px] text-slate-400">Bộ môn phụ trách giảng dạy và phân công lớp chủ nhiệm</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Chuyên Môn Giảng Dạy <span class="text-rose-500">*</span>
                    </label>
                    <select name="specialization" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                        <?php 
                        $specs = ['Toán Học', 'Ngữ Văn', 'Tiếng Anh', 'Vật Lý', 'Hóa Học', 'Sinh Học', 'Lịch Sử', 'Địa Lý', 'Tin Học', 'Giáo Dục Công Dân', 'Thể Dục', 'Âm Nhạc', 'Mỹ Thuật', 'Công Nghệ'];
                        $curSpec = $teacher['specialization'] ?? 'Toán Học';
                        foreach ($specs as $sp): ?>
                        <option value="<?= $sp ?>" <?= $curSpec === $sp ? 'selected' : '' ?>><?= $sp ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Phân Công Lớp Chủ Nhiệm
                    </label>
                    <select name="homeroom_class_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                        <option value="">-- Không phân công chủ nhiệm --</option>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($teacher['homeroom_class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- SECTION 3: LIÊN HỆ & TRẠNG THÁI CÔNG TÁC -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">contact_phone</span>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Thông Tin Liên Lạc & Trạng Thái</h2>
                    <p class="text-[11px] text-slate-400">Số điện thoại, email công vụ, địa chỉ và tình trạng công tác</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Số Điện Thoại</label>
                    <input type="text" name="phone" 
                           value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>" 
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-mono font-bold text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" 
                           placeholder="VD: 0987654321">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Liên Hệ</label>
                    <input type="email" name="email" 
                           value="<?= htmlspecialchars($teacher['email'] ?? '') ?>" 
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" 
                           placeholder="VD: gv@edumanage.edu.vn">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ngày Bắt Đầu Công Tác</label>
                    <input type="date" name="start_date" 
                           value="<?= htmlspecialchars($teacher['start_date'] ?? date('Y-m-d')) ?>" 
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Địa Chỉ Thường Trú</label>
                    <input type="text" name="address" 
                           value="<?= htmlspecialchars($teacher['address'] ?? '') ?>" 
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" 
                           placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">
                </div>

                <!-- Status Selector -->
                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Trạng Thái Công Tác</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all <?= $currentStatus === 'active' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50' ?>">
                            <input type="radio" name="status" value="active" <?= $currentStatus === 'active' ? 'checked' : '' ?> class="hidden" onchange="updateStatusStyle(this)">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold">Đang công tác</p>
                                <p class="text-[10px] text-slate-400">Giảng dạy bình thường</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all <?= $currentStatus === 'inactive' ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 ring-1 ring-amber-500' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50' ?>">
                            <input type="radio" name="status" value="inactive" <?= $currentStatus === 'inactive' ? 'checked' : '' ?> class="hidden" onchange="updateStatusStyle(this)">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">pause_circle</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold">Tạm nghỉ</p>
                                <p class="text-[10px] text-slate-400">Nghỉ phép / Thai sản</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all <?= $currentStatus === 'retired' ? 'border-slate-400 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 ring-1 ring-slate-400' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50' ?>">
                            <input type="radio" name="status" value="retired" <?= $currentStatus === 'retired' ? 'checked' : '' ?> class="hidden" onchange="updateStatusStyle(this)">
                            <div class="w-8 h-8 rounded-xl bg-slate-500/10 text-slate-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">logout</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold">Nghỉ hưu</p>
                                <p class="text-[10px] text-slate-400">Đã hoàn thành công tác</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. BOTTOM ACTION BUTTONS -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="<?= BASE_URL ?>/teachers" 
               class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-sm transition-all">
                Hủy Bỏ
            </a>
            <button type="submit" id="btnSubmitTeacher" 
                    class="inline-flex items-center gap-2 px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span><?= $isEdit ? 'Lưu Thay Đổi' : 'Thêm Mới Giáo Viên' ?></span>
            </button>
        </div>
    </form>
</div>

<script>
    function previewTeacherImage(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
            document.getElementById('avatarInput').value = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }

    function updateGenderStyle(input) {
        const isMale = input.value === 'male';
        const labels = input.closest('.grid').querySelectorAll('label');
        labels.forEach(l => {
            l.className = 'flex items-center justify-center gap-2 p-2 rounded-xl border cursor-pointer transition-all border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600';
        });
        if (isMale) {
            labels[0].className = 'flex items-center justify-center gap-2 p-2 rounded-xl border cursor-pointer transition-all border-blue-500 bg-blue-50/60 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold';
        } else {
            labels[1].className = 'flex items-center justify-center gap-2 p-2 rounded-xl border cursor-pointer transition-all border-rose-500 bg-rose-50/60 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 font-bold';
        }
    }

    function updateStatusStyle(input) {
        const labels = input.closest('.grid').querySelectorAll('label');
        labels.forEach(l => {
            l.className = 'flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all border-slate-200 dark:border-slate-700 hover:bg-slate-50';
        });
        const parent = input.closest('label');
        if (input.value === 'active') {
            parent.className = 'flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 ring-1 ring-emerald-500';
        } else if (input.value === 'inactive') {
            parent.className = 'flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all border-amber-500 bg-amber-50/50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 ring-1 ring-amber-500';
        } else {
            parent.className = 'flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all border-slate-400 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 ring-1 ring-slate-400';
        }
    }

    async function handleSubmitTeacherForm(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitTeacher');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span><span>Đang lưu...</span>`;

        const isEdit = Boolean(data.id);
        const url = isEdit ? `<?= BASE_URL ?>/teachers/${data.id}` : '<?= BASE_URL ?>/teachers';

        try {
            const res = await apiPost(url, data);
            if (res) {
                setTimeout(() => window.location.href = '<?= BASE_URL ?>/teachers', 800);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span>${isEdit ? 'Lưu Thay Đổi' : 'Thêm Mới Giáo Viên'}</span>`;
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">save</span><span>${isEdit ? 'Lưu Thay Đổi' : 'Thêm Mới Giáo Viên'}</span>`;
        }
    }
</script>