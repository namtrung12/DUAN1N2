<?php

class StaffController
{
    private $orderModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->orderModel = new Order();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        // Chỉ staff (role_id = 3) mới được truy cập
        if ($_SESSION['user']['role_id'] != 3) {
            $_SESSION['errors'] = ['auth' => 'Bạn không có quyền truy cập'];
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public function index()
    {
        // Dashboard cho staff - hiển thị thống kê và đơn hàng gần đây
        $totalOrders = $this->orderModel->countAll();
        $pendingOrders = $this->orderModel->countByStatus('pending');
        $processingOrders = $this->orderModel->countByStatus('processing');
        $completedOrders = $this->orderModel->countByStatus('completed');

        // Phân trang cho đơn hàng gần đây
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Lấy đơn hàng gần đây
        $recentOrders = $this->orderModel->getRecentOrders($limit, $offset);
        $totalPages = ceil($totalOrders / $limit);

        require_once PATH_VIEW . 'staff/index.php';
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

        require_once PATH_VIEW . 'staff/orders.php';
    }

    public function updateOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=staff-orders');
            exit;
        }

        $orderId = $_POST['order_id'] ?? 0;
        $newStatus = $_POST['status'] ?? '';

        // Staff chỉ được phép cập nhật một số trạng thái nhất định
        $allowedStatuses = ['processing', 'shipped', 'delivered', 'completed'];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            $_SESSION['errors'] = ['status' => 'Bạn không có quyền cập nhật trạng thái này'];
            header('Location: ' . BASE_URL . '?action=staff-orders');
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
                'completed' => 4
            ];
            
            // Kiểm tra logic chuyển trạng thái
            if ($currentStatus === 'cancelled') {
                $_SESSION['errors'] = ['status' => 'Không thể thay đổi trạng thái đơn hàng đã hủy'];
                header('Location: ' . BASE_URL . '?action=staff-orders');
                exit;
            }
            
            if ($currentStatus === 'completed') {
                $_SESSION['errors'] = ['status' => 'Không thể thay đổi trạng thái đơn hàng đã hoàn thành'];
                header('Location: ' . BASE_URL . '?action=staff-orders');
                exit;
            }
            
            // Kiểm tra chuyển trạng thái theo thứ tự
            $currentOrder = $statusOrder[$currentStatus];
            $newOrder = $statusOrder[$newStatus];
            
            // Chỉ cho phép chuyển sang trạng thái kế tiếp hoặc giữ nguyên
            if ($newOrder < $currentOrder) {
                $_SESSION['errors'] = ['status' => 'Không thể quay lại trạng thái trước đó'];
                header('Location: ' . BASE_URL . '?action=staff-orders');
                exit;
            }
            
            if ($newOrder > $currentOrder + 1) {
                $_SESSION['errors'] = ['status' => 'Phải hoàn thành trạng thái hiện tại trước khi chuyển sang trạng thái tiếp theo'];
                header('Location: ' . BASE_URL . '?action=staff-orders');
                exit;
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

        header('Location: ' . BASE_URL . '?action=staff-orders');
        exit;
    }

    public function updateOrderStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=staff-orders');
            exit;
        }

        $orderId = $_POST['order_id'] ?? 0;
        $newStatus = $_POST['status'] ?? '';

        // Staff chỉ được phép cập nhật một số trạng thái nhất định
        $allowedStatuses = ['processing', 'shipped', 'delivered', 'completed'];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            $_SESSION['errors'] = ['status' => 'Bạn không có quyền cập nhật trạng thái này'];
            header('Location: ' . BASE_URL . '?action=staff-orders');
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
                'completed' => 4
            ];
            
            // Kiểm tra logic chuyển trạng thái
            if ($currentStatus === 'cancelled') {
                $_SESSION['errors'] = ['status' => 'Không thể thay đổi trạng thái đơn hàng đã hủy'];
                header('Location: ' . BASE_URL . '?action=staff-orders');
                exit;
            }
            
            if ($currentStatus === 'completed') {
                $_SESSION['errors'] = ['status' => 'Không thể thay đổi trạng thái đơn hàng đã hoàn thành'];
                header('Location: ' . BASE_URL . '?action=staff-orders');
                exit;
            }
            
            // Kiểm tra chuyển trạng thái tuần tự
            $currentOrder = $statusOrder[$currentStatus] ?? -1;
            $newOrder = $statusOrder[$newStatus] ?? -1;
            
            if ($newOrder <= $currentOrder) {
                $_SESSION['errors'] = ['status' => 'Không thể lùi trạng thái đơn hàng'];
                header('Location: ' . BASE_URL . '?action=staff-orders');
                exit;
            }
            
            if ($newOrder - $currentOrder > 1) {
                $_SESSION['errors'] = ['status' => 'Phải cập nhật trạng thái theo thứ tự'];
                header('Location: ' . BASE_URL . '?action=staff-orders');
                exit;
            }

            // Cập nhật trạng thái
            $this->orderModel->updateStatus($orderId, $newStatus);
            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=staff-orders');
        exit;
    }

}
