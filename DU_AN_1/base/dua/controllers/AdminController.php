<?php

class AdminController
{
    private $userModel;
    private $orderModel;
    private $productModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        // Chỉ Admin (role_id = 2) mới được truy cập
        // Staff (role_id = 3) sẽ được chuyển đến trang staff
        if ($_SESSION['user']['role_id'] == 3) {
            header('Location: ' . BASE_URL . '?action=staff');
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
        $totalOrders = $this->orderModel->countAll();
        $totalUsers = $this->userModel->countAll();
        $totalRevenue = $this->orderModel->getTotalRevenue();
        $recentOrders = $this->orderModel->getRecent(5);

        require_once PATH_VIEW . 'admin/index.php';
    }

    public function orders()
    {
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

        if (!in_array($newStatus, ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'])) {
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
                'shipped' => 2,
                'delivered' => 3,
                'completed' => 4,
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
            
            // Nếu đơn COD được chuyển sang delivered/completed → cập nhật payment_status = paid
            if ($order && $order['payment_method'] === 'cod' && in_array($newStatus, ['delivered', 'completed'])) {
                $this->orderModel->updatePaymentStatus($orderId, 'paid');
            }
            
            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật thất bại: ' . $e->getMessage()];
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
}
