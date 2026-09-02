<?php // views/teachers/form.php ?>
<div class="w-full space-y-6 max-w-6xl mx-auto">

    <!-- Breadcrumb Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="<?= BASE_URL ?>/dashboard" class="hover:text-blue-600 transition-colors">Quản lý</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="<?= BASE_URL ?>/teachers" class="hover:text-blue-600 transition-colors">Giáo viên</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-blue-600 font-bold"><?= $isEdit ? 'Cập nhật thông tin' : 'Thêm giáo viên mới' ?></span>
        </div>
        <a href="<?= BASE_URL ?>/teachers" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:shadow-sm transition-all">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Danh sách giáo viên</span>
        </a>
    </div>

    <!-- Page Title Hero Card -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 text-white rounded-3xl p-6 md:p-8 relative overflow-hidden shadow-xl">
        <div class="absolute top-0 right-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-60 h-60 bg-indigo-500/10 rounded-full blur-3xl -ml-10 -mb-10 pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center backdrop-blur-md">
                    <span class="material-symbols-outlined text-3xl text-blue-300"><?= $isEdit ? 'badge' : 'person_add' ?></span>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-bold uppercase tracking-wider mb-1 border border-blue-400/20">
                        <span class="material-symbols-outlined text-[12px]">school</span> Quản Lý Đội Ngũ Sư Phạm
                    </div>
                    <h1 class="text-2xl font-black tracking-tight"><?= $isEdit ? 'Cập Nhật Hồ Sơ Giáo Viên' : 'Thêm Mới Giáo Viên' ?></h1>
                    <p class="text-blue-200/80 text-xs mt-0.5"><?= $isEdit ? 'Chỉnh sửa thông tin cá nhân, chuyên môn và phân công công tác.' : 'Nhập đầy đủ thông tin để tạo hồ sơ và tài khoản giảng dạy mới trong hệ thống.' ?></p>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-3 py-1.5 rounded-xl border border-white/15">
                <span class="text-xs text-slate-300">Mã GV:</span>
                <span class="font-mono font-black text-emerald-400 text-sm"><?= htmlspecialchars($teacher['teacher_code']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Form Container -->
    <form onsubmit="handleSubmitTeacherForm(event)" id="teacherForm" novalidate>
        <div class="space-y-6">

            <!-- ============================================ -->
            <!-- SECTION 1: THÔNG TIN CÁ NHÂN                -->
            <!-- ============================================ -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <!-- Section Header -->
                <div class="flex items-center gap-3 px-6 md:px-8 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/30">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Thông Tin Cá Nhân</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Ảnh chân dung, họ tên, giới tính, ngày sinh và thông tin liên lạc</p>
                    </div>
                    <span class="ml-auto text-[10px] font-bold text-rose-500 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-full border border-rose-100 dark:border-rose-900">Bắt buộc *</span>
                </div>

                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
                        <!-- Photo Upload / Avatar -->
                        <div class="col-span-1 flex flex-col items-center gap-3">
                            <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/60 dark:to-slate-800 flex flex-col items-center justify-center overflow-hidden relative cursor-pointer hover:border-blue-400 hover:from-blue-50/50 dark:hover:border-blue-600 transition-all group shadow-inner">
                                <?php if (!empty($teacher['avatar'])): ?>
                                <img src="<?= htmlspecialchars($teacher['avatar']) ?>" class="w-full h-full object-cover" id="avatarPreview">
                                <?php else: ?>
                                <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600 group-hover:text-blue-400 transition-colors mb-1" id="avatarIcon">account_circle</span>
                                <span class="text-[10px] font-medium text-slate-400 group-hover:text-blue-500 transition-colors">Ảnh chân dung</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-center">
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Ảnh Thẻ Giáo Viên</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">JPG, PNG tối đa 2MB</p>
                            </div>
                        </div>

                        <!-- Fields -->
                        <div class="col-span-1 md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Họ và Tên -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Họ và Tên Đầy Đủ
                                    <span class="text-rose-500 ml-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">person</span>
                                    <input type="text" name="full_name" required 
                                           value="<?= htmlspecialchars($teacher['full_name'] ?? '') ?>" 
                                           placeholder="VD: ThS. Nguyễn Văn An"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white placeholder:text-slate-400 placeholder:font-normal transition-all">
                                </div>
                            </div>

                            <!-- Giới Tính -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Giới Tính <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">wc</span>
                                    <select name="gender" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white appearance-none">
                                        <option value="male" <?= ($teacher['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Nam</option>
                                        <option value="female" <?= ($teacher['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Nữ</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Ngày Sinh -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Ngày Sinh</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">calendar_today</span>
                                    <input type="date" name="dob" 
                                           value="<?= htmlspecialchars($teacher['dob'] ?? '1988-01-01') ?>"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-white">
                                </div>
                            </div>

                            <!-- Số Điện Thoại -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Số Điện Thoại</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">phone</span>
                                    <input type="tel" name="phone" 
                                           value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>" 
                                           placeholder="0911 000 001"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono placeholder:text-slate-400 text-slate-900 dark:text-white">
                                </div>
                            </div>

                            <!-- Email Liên Hệ -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Email Công Tác</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
                                    <input type="email" name="email" 
                                           value="<?= htmlspecialchars($teacher['email'] ?? '') ?>" 
                                           placeholder="giaovien@edumanage.edu.vn"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs placeholder:text-slate-400 text-slate-900 dark:text-white">
                                </div>
                            </div>

                            <!-- Địa Chỉ Thường Trú -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Địa Chỉ Cư Trú</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">home</span>
                                    <input type="text" name="address" 
                                           value="<?= htmlspecialchars($teacher['address'] ?? '') ?>" 
                                           placeholder="Số nhà, đường, phường/xã, quận/huyện..."
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs placeholder:text-slate-400 text-slate-900 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION 2: THÔNG TIN CÔNG TÁC & CHUYÊN MÔN   -->
            <!-- ============================================ -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <!-- Section Header -->
                <div class="flex items-center gap-3 px-6 md:px-8 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/30">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[18px]">work</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Thông Tin Công Tác & Chuyên Môn</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Mã giáo viên, chuyên môn giảng dạy, trình độ học vị và lớp chủ nhiệm</p>
                    </div>
                </div>

                <div class="p-6 md:p-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    <!-- Mã Giáo Viên -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Mã Giáo Viên <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">badge</span>
                            <input type="text" name="teacher_code" required 
                                   <?= $isEdit ? 'readonly' : '' ?>
                                   value="<?= htmlspecialchars($teacher['teacher_code'] ?? 'GV25' . rand(100, 999)) ?>" 
                                   placeholder="GV25001"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-black text-emerald-600 uppercase">
                        </div>
                    </div>

                    <!-- Chuyên Môn Giảng Dạy -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Chuyên Môn Giảng Dạy <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">menu_book</span>
                            <input type="text" name="specialization" required 
                                   value="<?= htmlspecialchars($teacher['specialization'] ?? 'Toán Học') ?>" 
                                   placeholder="Toán Học, Tiếng Việt, Tiếng Anh..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Trình Độ Học Vị -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Trình Độ Học Vị / Bằng Cấp</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">workspace_premium</span>
                            <input type="text" name="qualification" 
                                   value="<?= htmlspecialchars($teacher['qualification'] ?? 'Cử nhân Sư phạm') ?>" 
                                   placeholder="Cử nhân Sư phạm, Thạc sĩ..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Ngày Bắt Đầu Công Tác -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Ngày Bắt Đầu Công Tác</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">event_available</span>
                            <input type="date" name="start_date" 
                                   value="<?= htmlspecialchars($teacher['start_date'] ?? date('Y-m-d')) ?>"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Trạng Thái Làm Việc -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Trạng Thái Làm Việc</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                            <option value="active" <?= ($teacher['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Đang Giảng Dạy</option>
                            <option value="leave" <?= ($teacher['status'] ?? '') === 'leave' ? 'selected' : '' ?>>Nghỉ Phép / Tạm Nghỉ</option>
                            <option value="resigned" <?= ($teacher['status'] ?? '') === 'resigned' ? 'selected' : '' ?>>Đã Nghỉ Việc</option>
                        </select>
                    </div>

                    <!-- Phân Công Chủ Nhiệm (Nếu có) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Phân Công Chủ Nhiệm</label>
                        <select name="homeroom_class_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-emerald-600">
                            <option value="">-- Không phân công chủ nhiệm --</option>
                            <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (($teacher['homeroom_class_id'] ?? '') == $c['id'] || ($teacher['id'] ?? 0) == ($c['homeroom_teacher_id'] ?? -1)) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sticky Action Bar -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center justify-between shadow-lg">
                <a href="<?= BASE_URL ?>/teachers" class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold rounded-xl transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-7 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-black rounded-xl shadow-lg shadow-blue-500/25 transition-all active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span><?= $isEdit ? 'Lưu Thay Đổi Hồ Sơ' : 'Lưu Giáo Viên Mới' ?></span>
                </button>
            </div>

        </div>
    </form>
</div>

<script>
async function handleSubmitTeacherForm(e) {
    e.preventDefault();
    const form = e.target;
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const data = Object.fromEntries(new FormData(form).entries());
    const url = <?= $isEdit ? "'".BASE_URL."/teachers/{$teacher['id']}'" : "'".BASE_URL."/teachers'" ?>;

    const res = await apiPost(url, data);
    if (res) {
        showToast('<?= $isEdit ? 'Cập nhật giáo viên thành công!' : 'Thêm mới giáo viên thành công!' ?>', 'success');
        setTimeout(() => {
            window.location.href = '<?= BASE_URL ?>/teachers';
        }, 800);
    }
}
</script>
