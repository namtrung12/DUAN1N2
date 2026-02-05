<?php

class LoyaltyController
{
    private $loyaltyPointModel;
    private $loyaltyRewardModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->loyaltyPointModel = new LoyaltyPoint();
        $this->loyaltyRewardModel = new LoyaltyReward();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    public function index()
    {
        $loyaltyPoints = $this->loyaltyPointModel->getByUserId($_SESSION['user']['id']);
        
        if (!$loyaltyPoints) {
            $loyaltyPoints = [
                'total_points' => 0,
                'lifetime_points' => 0,
                'level' => 'new'
            ];
        } else {
            // Recalculate rank dựa trên lifetime_points
            $lifetimePoints = $loyaltyPoints['lifetime_points'];
            if ($lifetimePoints >= 1000) {
                $loyaltyPoints['level'] = 'diamond';
            } elseif ($lifetimePoints >= 600) {
                $loyaltyPoints['level'] = 'gold';
            } elseif ($lifetimePoints >= 400) {
                $loyaltyPoints['level'] = 'silver';
            } elseif ($lifetimePoints >= 200) {
                $loyaltyPoints['level'] = 'bronze';
            } else {
                $loyaltyPoints['level'] = 'new';
            }
        }

        $transactions = $this->loyaltyPointModel->getTransactions($_SESSION['user']['id']);
        $userRewards = $this->loyaltyRewardModel->getUserRewards($_SESSION['user']['id']);

        require_once PATH_VIEW . 'loyalty/index.php';
    }

    public function rewards()
    {
        $loyaltyPoints = $this->loyaltyPointModel->getByUserId($_SESSION['user']['id']);
        
        if (!$loyaltyPoints) {
            $loyaltyPoints = [
                'total_points' => 0,
                'monthly_points' => 0,
                'lifetime_points' => 0,
                'level' => 'bronze'
            ];
        }

        // Lấy các mã giảm giá có thể đổi bằng điểm
        $couponModel = new Coupon();
        $redeemableCoupons = $couponModel->getRedeemableCoupons();

        require_once PATH_VIEW . 'loyalty/rewards.php';
    }

    public function redeem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=loyalty-rewards');
            exit;
        }

        $couponId = $_POST['coupon_id'] ?? 0;

        $couponModel = new Coupon();
        $coupon = $couponModel->getById($couponId);
        
        if (!$coupon || !$coupon['is_redeemable']) {
            $_SESSION['errors'] = ['coupon' => 'Mã giảm giá không tồn tại hoặc không thể đổi'];
            header('Location: ' . BASE_URL . '?action=loyalty-rewards');
            exit;
        }

        // Kiểm tra đã đổi chưa
        if ($couponModel->hasUserRedeemed($_SESSION['user']['id'], $couponId)) {
            $_SESSION['errors'] = ['coupon' => 'Bạn đã đổi mã này rồi'];
            header('Location: ' . BASE_URL . '?action=loyalty-rewards');
            exit;
        }

        $loyaltyPoints = $this->loyaltyPointModel->getByUserId($_SESSION['user']['id']);
        if (!$loyaltyPoints || $loyaltyPoints['total_points'] < $coupon['point_cost']) {
            $_SESSION['errors'] = ['points' => 'Bạn không đủ điểm để đổi mã này'];
            header('Location: ' . BASE_URL . '?action=loyalty-rewards');
            exit;
        }

        try {
            $this->loyaltyPointModel->pdo->beginTransaction();

            // Trừ điểm
            $success = $this->loyaltyPointModel->deductPoints($_SESSION['user']['id'], $coupon['point_cost']);
            if (!$success) {
                throw new Exception('Không thể trừ điểm');
            }

            // Lưu lịch sử đổi mã
            $couponModel->redeemCoupon($_SESSION['user']['id'], $couponId, $coupon['point_cost']);

            // Ghi log
            $this->loyaltyPointModel->addTransaction(
                $_SESSION['user']['id'],
                null,
                'redeem',
                $coupon['point_cost'],
                'Đổi mã giảm giá: ' . $coupon['code']
            );

            $this->loyaltyPointModel->pdo->commit();

            $_SESSION['success'] = 'Đổi mã thành công! Mã của bạn: ' . $coupon['code'];
            header('Location: ' . BASE_URL . '?action=loyalty');
            exit;
        } catch (Exception $e) {
            $this->loyaltyPointModel->pdo->rollBack();
            $_SESSION['errors'] = ['redeem' => 'Đổi mã thất bại: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '?action=loyalty-rewards');
            exit;
        }
    }
}
