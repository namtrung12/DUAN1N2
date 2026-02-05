<?php

class CouponController
{
    private $couponModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->couponModel = new Coupon();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if ($_SESSION['user']['role_id'] != 2) {
            $_SESSION['errors'] = ['auth' => 'Bạn không có quyền truy cập'];
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public function index()
    {
        $coupons = $this->couponModel->getAllAdmin();
        require_once PATH_VIEW . 'admin/coupons.php';
    }

    public function create()
    {
        require_once PATH_VIEW . 'admin/coupon-create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $type = $_POST['type'] ?? 'fixed';
        $value = $_POST['value'] ?? 0;
        $minOrder = $_POST['min_order'] ?? 0;
        $usageLimit = $_POST['usage_limit'] ?? 0;
        $requiredRank = $_POST['required_rank'] ?? null;
        $pointCost = $_POST['point_cost'] ?? 0;
        $isRedeemable = isset($_POST['is_redeemable']) ? 1 : 0;
        $startsAt = $_POST['starts_at'] ?? null;
        $expiresAt = $_POST['expires_at'] ?? null;
        $status = isset($_POST['status']) ? 1 : 0;

        $errors = [];

        if (empty($code)) {
            $errors['code'] = 'Mã giảm giá không được để trống';
        }

        if ($value <= 0) {
            $errors['value'] = 'Giá trị giảm phải lớn hơn 0';
        }

        if ($type === 'percent' && $value > 100) {
            $errors['value'] = 'Giá trị giảm phần trăm không được vượt quá 100%';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=admin-coupon-create');
            exit;
        }

        $maxDiscount = $_POST['max_discount'] ?? null;
        $description = $_POST['description'] ?? null;

        try {
            $this->couponModel->create([
                'code' => $code,
                'type' => $type,
                'value' => $value,
                'max_discount' => $maxDiscount ?: null,
                'description' => $description,
                'min_order' => $minOrder,
                'usage_limit' => $usageLimit,
                'required_rank' => $requiredRank ?: null,
                'point_cost' => $pointCost,
                'is_redeemable' => $isRedeemable,
                'starts_at' => $startsAt ?: null,
                'expires_at' => $expiresAt ?: null,
                'status' => $status
            ]);

            $_SESSION['success'] = 'Thêm mã giảm giá thành công';
            header('Location: ' . BASE_URL . '?action=admin-coupons');
        } catch (Exception $e) {
            $_SESSION['errors'] = ['create' => 'Thêm mã thất bại: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '?action=admin-coupon-create');
        }
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $coupon = $this->couponModel->getById($id);

        if (!$coupon) {
            $_SESSION['errors'] = ['coupon' => 'Mã giảm giá không tồn tại'];
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        require_once PATH_VIEW . 'admin/coupon-edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $type = $_POST['type'] ?? 'fixed';
        $value = $_POST['value'] ?? 0;
        $minOrder = $_POST['min_order'] ?? 0;
        $usageLimit = $_POST['usage_limit'] ?? 0;
        $requiredRank = $_POST['required_rank'] ?? null;
        $pointCost = $_POST['point_cost'] ?? 0;
        $isRedeemable = isset($_POST['is_redeemable']) ? 1 : 0;
        $startsAt = $_POST['starts_at'] ?? null;
        $expiresAt = $_POST['expires_at'] ?? null;
        $status = isset($_POST['status']) ? 1 : 0;

        $maxDiscount = $_POST['max_discount'] ?? null;
        $description = $_POST['description'] ?? null;

        try {
            $this->couponModel->update($id, [
                'code' => $code,
                'type' => $type,
                'value' => $value,
                'max_discount' => $maxDiscount ?: null,
                'description' => $description,
                'min_order' => $minOrder,
                'usage_limit' => $usageLimit,
                'required_rank' => $requiredRank ?: null,
                'point_cost' => $pointCost,
                'is_redeemable' => $isRedeemable,
                'starts_at' => $startsAt ?: null,
                'expires_at' => $expiresAt ?: null,
                'status' => $status
            ]);

            $_SESSION['success'] = 'Cập nhật mã giảm giá thành công';
            header('Location: ' . BASE_URL . '?action=admin-coupons');
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật mã thất bại: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '?action=admin-coupon-edit&id=' . $id);
        }
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;

        try {
            $this->couponModel->delete($id);
            $_SESSION['success'] = 'Xóa mã giảm giá thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa mã thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-coupons');
        exit;
    }

    public function deleteMultiple()
    {
        $ids = $_GET['ids'] ?? '';
        
        if (empty($ids)) {
            $_SESSION['errors'] = ['delete' => 'Không có mã nào được chọn'];
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        $idArray = explode(',', $ids);
        $successCount = 0;

        try {
            foreach ($idArray as $id) {
                if ($this->couponModel->delete(trim($id))) {
                    $successCount++;
                }
            }
            $_SESSION['success'] = "Đã xóa thành công {$successCount} mã giảm giá";
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa mã thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-coupons');
        exit;
    }
}
