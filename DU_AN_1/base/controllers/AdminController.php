<?php

class AdminController
{
    private $userModel;
    private $orderModel;
    private $productModel;
    private $reviewModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->reviewModel = new Review();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        // Chỉ Admin (role_id = 2) mới được truy cập
        if ($_SESSION['user']['role_id'] != 2) {
            $_SESSION['errors'] = ['auth' => 'Bạn không có quyền truy cập'];
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public function index()
    {
        $totalOrders = $this->orderModel->countAll();
        $totalUsers = $this->userModel->countAll();
        $totalRevenue = $this->orderModel->getTotalRevenue();
        $recentOrders = $this->orderModel->getRecent(5);

        require_once PATH_VIEW . 'admin/index.php';
    }

    public function orders()
    {
        // Tự động hoàn thành các đơn hàng đang giao quá 30 phút
        $this->orderModel->autoCompleteDeliveringOrders();
        
        $status = $_GET['status'] ?? '';
        
        if ($status) {
            $orders = $this->orderModel->getByStatus($status);
        } else {
            $orders = $this->orderModel->getAllOrders();
        }

        // Lấy chi tiết sản phẩm cho mỗi đơn hàng
        foreach ($orders as &$order) {
            $order['items'] = $this->orderModel->getItems($order['id']);
            
            // Lấy topping cho mỗi item
            foreach ($order['items'] as &$item) {
                $item['toppings'] = $this->orderModel->getItemToppings($item['id']);
            }
            unset($item);
        }
        unset($order);

        require_once PATH_VIEW . 'admin/orders.php';
    }

    public function updateOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $orderId = $_POST['order_id'] ?? 0;
        $newStatus = $_POST['status'] ?? '';

        if (!in_array($newStatus, ['pending', 'processing', 'preparing', 'shipped', 'delivering', 'completed', 'cancelled'])) {
            $_SESSION['errors'] = ['status' => 'Trạng thái không hợp lệ'];
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        try {
            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getById($orderId);
            $currentStatus = $order['status'];
            
            // Định nghĩa thứ tự trạng thái hợp lệ
            $statusOrder = [
                'pending' => 0,
                'processing' => 1,
                'preparing' => 2,
                'shipped' => 3,
                'delivering' => 4,
                'completed' => 5,
                'cancelled' => -1 // Có thể hủy từ bất kỳ trạng thái nào (trừ completed)
            ];
            
            // Kiểm tra logic chuyển trạng thái
            if ($newStatus === 'cancelled') {
                // Không cho phép hủy đơn đã hoàn thành
                if ($currentStatus === 'completed') {
                    $_SESSION['errors'] = ['status' => 'Không thể hủy đơn hàng đã hoàn thành'];
                    header('Location: ' . BASE_URL . '?action=admin-orders');
                    exit;
                }
            } elseif ($currentStatus === 'cancelled') {
                // Không cho phép chuyển từ cancelled sang trạng thái khác
                $_SESSION['errors'] = ['status' => 'Không thể thay đổi trạng thái đơn hàng đã hủy'];
                header('Location: ' . BASE_URL . '?action=admin-orders');
                exit;
            } elseif ($currentStatus === 'completed') {
                // Không cho phép thay đổi đơn đã hoàn thành
                $_SESSION['errors'] = ['status' => 'Không thể thay đổi trạng thái đơn hàng đã hoàn thành'];
                header('Location: ' . BASE_URL . '?action=admin-orders');
                exit;
            } elseif ($currentStatus === 'delivering' && $newStatus === 'completed') {
                // Admin không thể chuyển từ đang giao sang hoàn thành - user tự xác nhận hoặc tự động sau 30 phút
                $_SESSION['errors'] = ['status' => 'Khách hàng sẽ tự xác nhận đã nhận hàng hoặc đơn sẽ tự động hoàn thành sau 30 phút'];
                header('Location: ' . BASE_URL . '?action=admin-orders');
                exit;
            } else {
                // Kiểm tra chuyển trạng thái theo thứ tự
                $currentOrder = $statusOrder[$currentStatus];
                $newOrder = $statusOrder[$newStatus];
                
                // Chỉ cho phép chuyển sang trạng thái kế tiếp hoặc giữ nguyên
                if ($newOrder < $currentOrder) {
                    $_SESSION['errors'] = ['status' => 'Không thể quay lại trạng thái trước đó'];
                    header('Location: ' . BASE_URL . '?action=admin-orders');
                    exit;
                }
                
                if ($newOrder > $currentOrder + 1) {
                    $_SESSION['errors'] = ['status' => 'Phải hoàn thành trạng thái hiện tại trước khi chuyển sang trạng thái tiếp theo'];
                    header('Location: ' . BASE_URL . '?action=admin-orders');
                    exit;
                }
            }
            
            // Cập nhật trạng thái đơn hàng
            $this->orderModel->updateStatus($orderId, $newStatus);
            
            // Nếu đơn COD được chuyển sang completed → cập nhật payment_status = paid
            if ($order && $order['payment_method'] === 'cod' && $newStatus === 'completed') {
                $this->orderModel->updatePaymentStatus($orderId, 'paid');
            }

            // Gửi thông báo cho user khi chuyển sang trạng thái "Đang giao"
            if ($newStatus === 'delivering') {
                $notificationModel = new Notification();
                $orderCode = str_pad($orderId, 6, '0', STR_PAD_LEFT);
                $notificationModel->create(
                    $order['user_id'],
                    'order_delivering',
                    'Đơn hàng đang được giao',
                    'Đơn hàng #' . $orderCode . ' đang được giao đến bạn. Vui lòng chú ý điện thoại!',
                    $orderId
                );
            }
            
            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=admin-orders');
        exit;
    }

    public function cancelOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $orderId = $_POST['order_id'] ?? 0;
        $cancelReason = trim($_POST['cancel_reason'] ?? '');

        // Kiểm tra lý do hủy
        if (empty($cancelReason)) {
            $_SESSION['errors'] = ['cancel' => 'Vui lòng nhập lý do hủy đơn hàng'];
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        try {
            $order = $this->orderModel->getById($orderId);
            
            if (!$order) {
                $_SESSION['errors'] = ['cancel' => 'Đơn hàng không tồn tại'];
                header('Location: ' . BASE_URL . '?action=admin-orders');
                exit;
            }

            // Chỉ cho phép hủy đơn ở trạng thái pending hoặc processing
            if (!in_array($order['status'], ['pending', 'processing'])) {
                $_SESSION['errors'] = ['cancel' => 'Chỉ có thể hủy đơn hàng ở trạng thái Chờ xử lý hoặc Đang xử lý'];
                header('Location: ' . BASE_URL . '?action=admin-orders');
                exit;
            }

            // Cập nhật trạng thái và lưu lý do hủy
            $this->orderModel->cancelWithReason($orderId, $cancelReason);

            // Gửi thông báo cho user khi đơn hàng bị hủy
            $notificationModel = new Notification();
            $orderCode = str_pad($orderId, 6, '0', STR_PAD_LEFT);
            $notificationModel->create(
                $order['user_id'],
                'order_cancelled',
                'Đơn hàng đã bị hủy',
                'Đơn hàng #' . $orderCode . ' đã bị hủy. Lý do: ' . $cancelReason,
                $orderId
            );

            // Hoàn tiền nếu đã thanh toán qua ví
            if ($order['payment_method'] === 'wallet' && $order['payment_status'] === 'paid') {
                $walletModel = new Wallet();
                $wallet = $walletModel->createOrGet($order['user_id']);
                
                $pdo = $this->orderModel->pdo;
                
                // Cộng tiền vào ví
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + :amount, updated_at = NOW() WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $order['user_id'], ':amount' => $order['total']]);
                
                // Tạo giao dịch hoàn tiền
                $stmt = $pdo->prepare("INSERT INTO wallet_transactions (wallet_id, user_id, order_id, type, amount, description) 
                                      VALUES (:wallet_id, :user_id, :order_id, :type, :amount, :description)");
                $stmt->execute([
                    ':wallet_id' => $wallet['id'],
                    ':user_id' => $order['user_id'],
                    ':order_id' => $orderId,
                    ':type' => 'refund',
                    ':amount' => $order['total'],
                    ':description' => 'Hoàn tiền đơn hàng #' . str_pad($orderId, 6, '0', STR_PAD_LEFT) . ' - Lý do: ' . $cancelReason
                ]);
            }

            $_SESSION['success'] = 'Đã hủy đơn hàng thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['cancel' => 'Hủy đơn thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=admin-orders');
        exit;
    }

    public function products()
    {
        $products = $this->productModel->getAllAdmin();
        require_once PATH_VIEW . 'admin/products.php';
    }

    public function users()
    {
        $users = $this->userModel->getAll();
        require_once PATH_VIEW . 'admin/users.php';
    }

    public function updateUserRole()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-users');
            exit;
        }

        $userId = $_POST['user_id'] ?? 0;
        $roleId = $_POST['role_id'] ?? 0;

        if (!in_array($roleId, [1, 2, 3])) {
            $_SESSION['errors'] = ['role' => 'Vai trò không hợp lệ'];
            header('Location: ' . BASE_URL . '?action=admin-users');
            exit;
        }

        try {
            $this->userModel->updateRole($userId, $roleId);
            $_SESSION['success'] = 'Cập nhật vai trò thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    public function lockMultipleUsers()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-users');
            exit;
        }

        $userIds = $_POST['user_ids'] ?? [];

        if (empty($userIds)) {
            $_SESSION['errors'] = ['users' => 'Vui lòng chọn ít nhất một người dùng'];
            header('Location: ' . BASE_URL . '?action=admin-users');
            exit;
        }

        try {
            $count = $this->userModel->lockMultiple($userIds);
            $_SESSION['success'] = "Đã khóa $count tài khoản thành công";
        } catch (Exception $e) {
            $_SESSION['errors'] = ['lock' => 'Khóa tài khoản thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    public function reviews()
    {
        $status = $_GET['status'] ?? '';
        
        if ($status !== '') {
            $reviews = $this->reviewModel->getByStatus($status);
        } else {
            $reviews = $this->reviewModel->getAll();
        }

        require_once PATH_VIEW . 'admin/reviews.php';
    }

    public function updateReviewStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-reviews');
            exit;
        }

        $reviewId = $_POST['review_id'] ?? 0;
        $status = $_POST['status'] ?? 0;

        if (!in_array($status, [0, 1])) {
            $_SESSION['errors'] = ['status' => 'Trạng thái không hợp lệ'];
            header('Location: ' . BASE_URL . '?action=admin-reviews');
            exit;
        }

        try {
            $this->reviewModel->updateStatus($reviewId, $status);
            $statusText = $status == 1 ? 'hiển thị' : 'ẩn';
            $_SESSION['success'] = "Đã $statusText đánh giá thành công";
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=admin-reviews');
        exit;
    }
}
