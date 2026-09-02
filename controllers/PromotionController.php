<?php
// controllers/PromotionController.php
require_once __DIR__ . '/BaseController.php';

class PromotionController extends BaseController {
    public function index(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('promotions.view');

        $classes = SchoolClass::all('name ASC');
        $years   = AcademicYear::all('id DESC');

        $classId = (int)$request->input('class_id', $classes[0]['id'] ?? 1);
        $academicYearId = (int)$request->input('academic_year_id', $years[0]['id'] ?? 1);

        $promotionData = Promotion::getClassPromotions($classId, $academicYearId);

        if ($request->isAjax()) {
            Response::json($promotionData);
            return;
        }

        View::render('promotions/index', [
            'classes'        => $classes,
            'years'          => $years,
            'selectedClass'  => $classId,
            'selectedYear'   => $academicYearId,
            'promotionData'  => $promotionData
        ]);
    }

    public function save(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('promotions.evaluate');

        $classId        = (int)$request->input('class_id');
        $academicYearId = (int)$request->input('academic_year_id');
        $records        = $request->input('records', []);

        if (empty($classId) || empty($academicYearId) || empty($records)) {
            Response::error('Dữ liệu xét duyệt lên lớp không hợp lệ.');
            return;
        }

        Promotion::saveDecisions($academicYearId, $classId, $records, Auth::id());
        AuditLogger::log('SAVE_PROMOTION_DECISIONS', 'promotions', "c{$classId}_y{$academicYearId}", null, ['count' => count($records)]);

        Response::success(null, 'Lưu kết quả xét lên lớp thành công!');
    }

    public function execute(Request $request): void {
        $this->requireAuth();
        $this->requirePermission('promotions.execute');

        $classId        = (int)$request->input('class_id');
        $academicYearId = (int)$request->input('academic_year_id');
        $nextYearId     = (int)$request->input('next_year_id', $academicYearId);

        if (empty($classId) || empty($academicYearId)) {
            Response::error('Thông tin lớp hoặc năm học không hợp lệ.');
            return;
        }

        $count = Promotion::executePromotions($classId, $academicYearId, $nextYearId, Auth::id());
        AuditLogger::log('EXECUTE_CLASS_PROMOTION', 'promotions', "c{$classId}_y{$academicYearId}", null, ['processed' => $count]);

        Response::success(['processed' => $count], "Đã thực hiện chuyển lớp & cập nhật trạng thái thành công cho {$count} học sinh!");
    }
}
