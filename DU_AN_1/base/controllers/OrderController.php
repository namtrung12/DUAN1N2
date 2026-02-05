<?php

class OrderController
{
    private $orderModel;
    private $cartModel;
    private $productModel;
    private $addressModel;
    private $couponModel;
    private $shippingZoneModel;
    private $walletModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        $this->addressModel = new Address();
        $this->couponModel = new Coupon();
        $this->shippingZoneModel = new ShippingZone();
        $this->walletModel = new Wallet();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    public function checkout()
    {
        $cartItems = $this->cartModel->getByUserId($_SESSION['user']['id']);
        
        if (empty($cartItems)) {
            $_SESSION['errors'] = ['cart' => 'Giỏ hàng trống'];
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        // Nếu có clear_selected, xóa selected_items để thanh toán tất cả
        if (isset($_GET['clear_selected'])) {
            unset($_SESSION['cart_selected_items']);
        }

        // Kiểm tra nếu có selected_items từ session
        $selectedItemIds = [];
        if (isset($_SESSION['cart_selected_items']) && !empty($_SESSION['cart_selected_items'])) {
            $selectedItemIds = explode(',', $_SESSION['cart_selected_items']);
            $selectedItemIds = array_map('intval', $selectedItemIds);
        }

        $addresses = $this->addressModel->getByUserId($_SESSION['user']['id']);
        $defaultAddress = $this->addressModel->getDefault($_SESSION['user']['id']);

        $cartData = [];
        $subtotal = 0;
        $toppingTotal = 0;

        foreach ($cartItems as $item) {
            // Nếu có selected_items, chỉ xử lý các item đã chọn
            if (!empty($selectedItemIds) && !in_array($item['id'], $selectedItemIds)) {
                continue;
            }
            $sizes = $this->productModel->getSizes($item['product_id']);
            $selectedSize = null;
            foreach ($sizes as $size) {
                if ($size['size_name'] === $item['size']) {
                    $selectedSize = $size;
                    break;
                }
            }

            $toppings = $this->cartModel->getToppings($item['id']);
            $itemToppingCost = 0;
            foreach ($toppings as $topping) {
                $itemToppingCost += $topping['price'];
            }

            $itemPrice = $selectedSize ? $selectedSize['price'] : 0;
            $itemTotal = ($itemPrice + $itemToppingCost) * $item['quantity'];

            $cartData[] = [
                'cart_item' => $item,
                'size_info' => $selectedSize,
                'toppings' => $toppings,
                'item_price' => $itemPrice,
                'topping_cost' => $itemToppingCost,
                'item_total' => $itemTotal
            ];

            $subtotal += $itemPrice * $item['quantity'];
            $toppingTotal += $itemToppingCost * $item['quantity'];
        }

        $discount = 0;
        $coupon = null;
        // Lấy rank của user để kiểm tra coupon
        $loyaltyModel = new LoyaltyPoint();
        $loyaltyData = $loyaltyModel->getByUserId($_SESSION['user']['id']);
        $userRank = $loyaltyData['level'] ?? 'bronze';

        if (isset($_SESSION['cart_coupon'])) {
            $coupon = $this->couponModel->getByCode($_SESSION['cart_coupon']);
            if ($coupon && $this->couponModel->isValid($coupon, $subtotal + $toppingTotal, $userRank)) {
                $discount = $this->couponModel->calculateDiscount($coupon, $subtotal + $toppingTotal);
            }
        }

        // Tính phí ship dựa trên địa chỉ mặc định
        $shippingFee = 15000; // Phí mặc định
        if ($defaultAddress) {
            $zone = $this->shippingZoneModel->getByLocation($defaultAddress['province'], $defaultAddress['district']);
            if ($zone) {
                $shippingFee = $zone['base_fee'];
            }
        }

        $total = $subtotal + $toppingTotal + $shippingFee - $discount;

        require_once PATH_VIEW . 'orders/checkout.php';
    }

    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=checkout');
            exit;
        }

        $addressId = $_POST['address_id'] ?? 0;
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $note = $_POST['note'] ?? null;

        $errors = [];

        $address = $this->addressModel->getById($addressId);
        if (!$address || $address['user_id'] != $_SESSION['user']['id']) {
            $errors['address'] = 'Địa chỉ không hợp lệ';
        }

        if (!in_array($paymentMethod, ['cod', 'vnpay', 'momo', 'card', 'wallet'])) {
            $errors['payment'] = 'Phương thức thanh toán không hợp lệ';
        }

        $cartItems = $this->cartModel->getByUserId($_SESSION['user']['id']);
        if (empty($cartItems)) {
            $errors['cart'] = 'Giỏ hàng trống';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=checkout');
            exit;
        }

        // Kiểm tra nếu có selected_items từ session (từ cart) hoặc từ POST (từ checkout form)
        $selectedItemIds = [];
        if (isset($_SESSION['cart_selected_items']) && !empty($_SESSION['cart_selected_items'])) {
            $selectedItemIds = explode(',', $_SESSION['cart_selected_items']);
            $selectedItemIds = array_map('intval', $selectedItemIds);
        } elseif (isset($_POST['selected_items']) && !empty($_POST['selected_items'])) {
            $selectedItemIds = explode(',', $_POST['selected_items']);
            $selectedItemIds = array_map('intval', $selectedItemIds);
        }

        $subtotal = 0;
        $toppingTotal = 0;
        $orderItems = [];

        foreach ($cartItems as $item) {
            // Nếu có selected_items, chỉ xử lý các item đã chọn
            if (!empty($selectedItemIds) && !in_array($item['id'], $selectedItemIds)) {
                continue;
            }
            $sizes = $this->productModel->getSizes($item['product_id']);
            $selectedSize = null;
            
            // Tìm size phù hợp cho sản phẩm này
            foreach ($sizes as $sizeOption) {
                if ($sizeOption['size_name'] === $item['size']) {
                    $selectedSize = $sizeOption;
                    break;
                }
            }
            
            // Nếu không tìm thấy size, bỏ qua item này
            if (!$selectedSize) {
                continue;
            }

            $toppings = $this->cartModel->getToppings($item['id']);
            $itemToppingCost = 0;
            foreach ($toppings as $topping) {
                $itemToppingCost += $topping['price'];
            }

            $itemPrice = $selectedSize['price'];
            $subtotal += $itemPrice * $item['quantity'];
            $toppingTotal += $itemToppingCost * $item['quantity'];

            $orderItems[] = [
                'product_id' => $item['product_id'],
                'product_size_id' => $selectedSize['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $itemPrice,
                'topping_cost' => $itemToppingCost,
                'toppings' => $toppings,
                'ice_level' => $item['ice_level'] ?? 100,
                'sugar_level' => $item['sugar_level'] ?? 100,
                'cart_id' => $item['id'] // Lưu cart_id để xóa sau khi thanh toán
            ];
        }

        $discount = 0;
        $couponId = null;
        // Lấy rank của user để kiểm tra coupon
        $loyaltyModel = new LoyaltyPoint();
        $loyaltyData = $loyaltyModel->getByUserId($_SESSION['user']['id']);
        $userRank = $loyaltyData['level'] ?? 'bronze';
        
        if (isset($_SESSION['cart_coupon'])) {
            $coupon = $this->couponModel->getByCode($_SESSION['cart_coupon']);
            if ($coupon && $this->couponModel->isValid($coupon, $subtotal + $toppingTotal, $userRank)) {
                $discount = $this->couponModel->calculateDiscount($coupon, $subtotal + $toppingTotal);
                $couponId = $coupon['id'];
            }
        }

        // Tính phí ship dựa trên địa chỉ được chọn
        $zone = $this->shippingZoneModel->getByLocation($address['province'], $address['district']);
        $shippingFee = $zone ? $zone['base_fee'] : 15000; // Phí mặc định nếu không tìm thấy zone
        $shippingZoneId = $zone ? $zone['id'] : null;

        $total = $subtotal + $toppingTotal + $shippingFee - $discount;
        $walletPayment = $paymentMethod === 'wallet';
        $wallet = null;
        if ($walletPayment) {
            $wallet = $this->walletModel->createOrGet($_SESSION['user']['id']);
            if ($wallet['balance'] < $total) {
                $errors['wallet'] = 'Số dư ví không đủ để thanh toán';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=checkout');
            exit;
        }

        try {
            // Bắt đầu transaction cho đơn hàng
            $this->orderModel->pdo->beginTransaction();

            $orderId = $this->orderModel->create([
                'user_id' => $_SESSION['user']['id'],
                'address_id' => $addressId,
                'coupon_id' => $couponId,
                'shipping_zone_id' => $shippingZoneId,
                'subtotal' => $subtotal + $toppingTotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'note' => $note
            ]);

            $orderedCartIds = [];
            foreach ($orderItems as $item) {
                $orderItemId = $this->orderModel->addItem(
                    $orderId,
                    $item['product_id'],
                    $item['product_size_id'],
                    $item['quantity'],
                    $item['unit_price'],
                    ($item['unit_price'] + $item['topping_cost']) * $item['quantity'],
                    $item['ice_level'] ?? 100,
                    $item['sugar_level'] ?? 100
                );

                foreach ($item['toppings'] as $topping) {
                    $this->orderModel->addItemTopping($orderItemId, $topping['id'], $topping['price']);
                }
                
                // Lưu cart_id để xóa sau
                if (isset($item['cart_id'])) {
                    $orderedCartIds[] = $item['cart_id'];
                }
            }

            // Chỉ xóa các items đã thanh toán
            if (!empty($orderedCartIds)) {
                $this->orderModel->clearCart($_SESSION['user']['id'], $orderedCartIds);
            } else {
                // Nếu không có selected items, xóa toàn bộ (tương thích ngược)
                $this->orderModel->clearCart($_SESSION['user']['id']);
            }
            
            // Xóa selected items khỏi session sau khi thanh toán thành công
            unset($_SESSION['cart_selected_items']);

            // Nếu thanh toán bằng ví, xử lý trong cùng transaction
            if ($walletPayment) {
                // Sử dụng cùng PDO connection để đảm bảo transaction hoạt động
                $pdo = $this->orderModel->pdo;
                
                // Lấy wallet_id
                $walletId = $wallet['id'];
                
                // Trừ tiền từ ví
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + :amount, updated_at = NOW() WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $_SESSION['user']['id'], ':amount' => -$total]);
                
                // Tạo giao dịch ví (bao gồm cả wallet_id và user_id để tương thích)
                $stmt = $pdo->prepare("INSERT INTO wallet_transactions (wallet_id, user_id, order_id, type, amount, description) 
                                      VALUES (:wallet_id, :user_id, :order_id, :type, :amount, :description)");
                $stmt->execute([
                    ':wallet_id' => $walletId,
                    ':user_id' => $_SESSION['user']['id'],
                    ':order_id' => $orderId,
                    ':type' => 'withdraw',
                    ':amount' => -$total,
                    ':description' => 'Thanh toán đơn hàng #' . str_pad($orderId, 6, '0', STR_PAD_LEFT)
                ]);
                
                // Cập nhật trạng thái đơn hàng và thanh toán
                $this->orderModel->updateStatus($orderId, 'processing');
                $this->orderModel->updatePaymentStatus($orderId, 'paid');
            }

            // Commit tất cả thay đổi
            $this->orderModel->pdo->commit();
            
            // Cập nhật used_count cho coupon (sau khi commit thành công)
            if ($couponId && $discount > 0) {
                try {
                    // Tăng used_count
                    $this->couponModel->incrementUsedCount($couponId);
                    
                    // Lưu lịch sử sử dụng (chỉ cho mã đổi điểm)
                    $couponData = $this->couponModel->getById($couponId);
                    if ($couponData && $couponData['is_redeemable']) {
                        $this->couponModel->recordCouponUsage($_SESSION['user']['id'], $couponId, $orderId, $discount);
                    }
                } catch (Exception $e) {
                    // Bỏ qua lỗi tracking, không ảnh hưởng đơn hàng
                    error_log('Coupon tracking error: ' . $e->getMessage());
                }
            }

            unset($_SESSION['cart_coupon']);
            $_SESSION['order_id'] = $orderId;
            
            // Nếu thanh toán VNPay, chuyển hướng đến VNPay
            if ($paymentMethod === 'vnpay') {
                require_once PATH_ROOT . 'configs/vnpay.php';
                $orderInfo = 'Thanh toán đơn hàng #' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
                $vnpayUrl = createVNPayPaymentUrl('order_' . $orderId, $total, $orderInfo);
                header('Location: ' . $vnpayUrl);
                exit;
            }
            
            header('Location: ' . BASE_URL . '?action=order-success');
            exit;
        } catch (Exception $e) {
            // Rollback transaction nếu có lỗi
            try {
                if ($this->orderModel->pdo->inTransaction()) {
                    $this->orderModel->pdo->rollBack();
                }
            } catch (Exception $rollbackError) {
                // Ignore rollback errors if connection is lost
            }
            
            $_SESSION['errors'] = ['order' => 'Đặt hàng thất bại: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '?action=checkout');
            exit;
        }
    }

    public function success()
    {
        if (!isset($_SESSION['order_id'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $orderId = $_SESSION['order_id'];
        $order = $this->orderModel->getById($orderId);
        unset($_SESSION['order_id']);

        require_once PATH_VIEW . 'orders/success.php';
    }

    public function index()
    {
        // Tự động hoàn thành các đơn hàng đang giao quá 30 phút
        $this->orderModel->autoCompleteDeliveringOrders();
        
        $orders = $this->orderModel->getByUserId($_SESSION['user']['id']);
        require_once PATH_VIEW . 'orders/index.php';
    }

    public function detail()
    {
        $orderId = $_GET['id'] ?? 0;
        $order = $this->orderModel->getById($orderId);

        if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['order' => 'Đơn hàng không tồn tại'];
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        $orderItems = $this->orderModel->getItems($orderId);
        
        foreach ($orderItems as &$item) {
            $item['toppings'] = $this->orderModel->getItemToppings($item['id']);
        }
        unset($item); // Quan trọng: Phải unset reference sau foreach

        require_once PATH_VIEW . 'orders/detail.php';
    }

    public function cancel()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        $orderId = $_POST['order_id'] ?? 0;
        $order = $this->orderModel->getById($orderId);

        if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['order' => 'Đơn hàng không tồn tại'];
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        if (!in_array($order['status'], ['pending', 'processing'])) {
            $_SESSION['errors'] = ['order' => 'Chỉ có thể hủy đơn hàng đang chờ xử lý hoặc đang xử lý'];
            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
            exit;
        }

        // Kiểm tra nếu đã thanh toán thì không cho hủy (trừ COD)
        if ($order['payment_method'] !== 'cod' && $order['payment_status'] === 'paid') {
            $_SESSION['errors'] = ['order' => 'Không thể hủy đơn hàng đã thanh toán. Vui lòng liên hệ hỗ trợ'];
            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
            exit;
        }

        try {
            // Bắt đầu transaction
            $this->orderModel->pdo->beginTransaction();
            
            // Cập nhật trạng thái đơn hàng
            $this->orderModel->updateStatus($orderId, 'cancelled');

            // Hoàn tiền nếu đã thanh toán qua ví
            if ($order['payment_method'] === 'wallet' && $order['payment_status'] === 'paid') {
                $pdo = $this->orderModel->pdo;
                
                // Lấy wallet_id
                $wallet = $this->walletModel->createOrGet($_SESSION['user']['id']);
                $walletId = $wallet['id'];
                
                // Cộng tiền vào ví
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + :amount, updated_at = NOW() WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $_SESSION['user']['id'], ':amount' => $order['total']]);
                
                // Tạo giao dịch hoàn tiền
                $stmt = $pdo->prepare("INSERT INTO wallet_transactions (wallet_id, user_id, order_id, type, amount, description) 
                                      VALUES (:wallet_id, :user_id, :order_id, :type, :amount, :description)");
                $stmt->execute([
                    ':wallet_id' => $walletId,
                    ':user_id' => $_SESSION['user']['id'],
                    ':order_id' => $orderId,
                    ':type' => 'refund',
                    ':amount' => $order['total'],
                    ':description' => 'Hoàn tiền đơn hàng #' . str_pad($orderId, 6, '0', STR_PAD_LEFT)
                ]);
            }
            
            // Commit transaction
            $this->orderModel->pdo->commit();
            
            // Thông báo thành công
            if ($order['payment_method'] === 'vnpay' && $order['payment_status'] === 'paid') {
                $_SESSION['success'] = 'Đơn hàng đã được hủy. Tiền sẽ được hoàn lại trong 3-5 ngày làm việc';
            } else {
                $_SESSION['success'] = 'Đơn hàng đã được hủy thành công';
            }
        } catch (Exception $e) {
            if ($this->orderModel->pdo->inTransaction()) {
                $this->orderModel->pdo->rollBack();
            }
            $_SESSION['errors'] = ['order' => 'Hủy đơn thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=orders');
        exit;
    }

    public function reorder()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $orderId = $_GET['order_id'] ?? 0;
        
        try {
            $order = $this->orderModel->getById($orderId);
            
            if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
                $_SESSION['errors'] = ['order' => 'Đơn hàng không tồn tại'];
                header('Location: ' . BASE_URL . '?action=orders');
                exit;
            }

            $orderItems = $this->orderModel->getItems($orderId);
            
            foreach ($orderItems as $item) {
                $cartId = $this->cartModel->add(
                    $_SESSION['user']['id'],
                    $item['product_id'],
                    $item['size_name'],
                    $item['quantity']
                );
                
                if ($cartId) {
                    $toppings = $this->orderModel->getItemToppings($item['id']);
                    if (!empty($toppings)) {
                        $toppingIds = array_column($toppings, 'topping_id');
                        $this->cartModel->addToppings($cartId, $toppingIds);
                    }
                }
            }

            $_SESSION['success'] = 'Đã thêm lại đơn hàng vào giỏ hàng';
            header('Location: ' . BASE_URL . '?action=cart');
        } catch (Exception $e) {
            $_SESSION['errors'] = ['reorder' => 'Không thể mua lại đơn hàng: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '?action=orders');
        }
        exit;
    }

    public function review()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        $orderId = $_POST['order_id'] ?? 0;
        $productId = $_POST['product_id'] ?? 0;
        $rating = $_POST['rating'] ?? 0;
        $comment = $_POST['comment'] ?? '';

        try {
            // Kiểm tra đơn hàng đã hoàn thành chưa
            $order = $this->orderModel->getById($orderId);
            
            if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
                throw new Exception('Đơn hàng không hợp lệ');
            }

            if ($order['status'] != 'completed') {
                throw new Exception('Chỉ có thể đánh giá đơn hàng đã hoàn thành');
            }

            // Kiểm tra đã đánh giá chưa
            $reviewModel = new Review();
            if ($reviewModel->hasUserReviewed($_SESSION['user']['id'], $productId, $orderId)) {
                throw new Exception('Bạn đã đánh giá sản phẩm này rồi');
            }

            // Tạo đánh giá
            $reviewModel->create([
                ':user_id' => $_SESSION['user']['id'],
                ':product_id' => $productId,
                ':order_id' => $orderId,
                ':rating' => $rating,
                ':comment' => $comment
            ]);

            $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá sản phẩm';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['review' => $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
        exit;
    }

    public function changePayment()
    {
        $orderId = $_GET['id'] ?? 0;
        $order = $this->orderModel->getById($orderId);

        if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['order' => 'Đơn hàng không tồn tại'];
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        // Chỉ cho phép đổi phương thức thanh toán khi đơn hàng chưa thanh toán
        $paymentStatus = $order['payment_status'] ?? 'pending';
        if ($paymentStatus !== 'pending') {
            $_SESSION['errors'] = ['order' => 'Không thể đổi phương thức thanh toán cho đơn hàng đã thanh toán'];
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        if ($order['status'] !== 'pending') {
            $_SESSION['errors'] = ['order' => 'Chỉ có thể đổi phương thức thanh toán cho đơn hàng đang chờ xử lý'];
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        // Lấy số dư ví
        $wallet = $this->walletModel->createOrGet($_SESSION['user']['id']);
        $walletBalance = $wallet['balance'];

        require_once PATH_VIEW . 'orders/change-payment.php';
    }

    public function updatePayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        $orderId = $_POST['order_id'] ?? 0;
        $newPaymentMethod = $_POST['payment_method'] ?? '';

        $order = $this->orderModel->getById($orderId);

        if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['order' => 'Đơn hàng không tồn tại'];
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        // Kiểm tra trạng thái
        $paymentStatus = $order['payment_status'] ?? 'pending';
        if ($paymentStatus !== 'pending' || $order['status'] !== 'pending') {
            $_SESSION['errors'] = ['order' => 'Không thể đổi phương thức thanh toán cho đơn hàng này'];
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        if (!in_array($newPaymentMethod, ['cod', 'vnpay', 'wallet'])) {
            $_SESSION['errors'] = ['payment' => 'Phương thức thanh toán không hợp lệ'];
            header('Location: ' . BASE_URL . '?action=order-change-payment&id=' . $orderId);
            exit;
        }

        // Nếu đổi sang ví, kiểm tra số dư
        if ($newPaymentMethod === 'wallet') {
            $wallet = $this->walletModel->createOrGet($_SESSION['user']['id']);
            if ($wallet['balance'] < $order['total']) {
                $_SESSION['errors'] = ['wallet' => 'Số dư ví không đủ để thanh toán'];
                header('Location: ' . BASE_URL . '?action=order-change-payment&id=' . $orderId);
                exit;
            }
        }

        try {
            // Nếu đổi sang ví, xử lý thanh toán ngay
            if ($newPaymentMethod === 'wallet') {
                // Bắt đầu transaction
                $this->orderModel->pdo->beginTransaction();
                
                // Sử dụng cùng PDO connection
                $pdo = $this->orderModel->pdo;
                
                // Lấy wallet_id
                $wallet = $this->walletModel->createOrGet($_SESSION['user']['id']);
                $walletId = $wallet['id'];
                
                // Trừ tiền từ ví
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + :amount, updated_at = NOW() WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $_SESSION['user']['id'], ':amount' => -$order['total']]);
                
                // Tạo giao dịch ví
                $stmt = $pdo->prepare("INSERT INTO wallet_transactions (wallet_id, user_id, order_id, type, amount, description) 
                                      VALUES (:wallet_id, :user_id, :order_id, :type, :amount, :description)");
                $stmt->execute([
                    ':wallet_id' => $walletId,
                    ':user_id' => $_SESSION['user']['id'],
                    ':order_id' => $orderId,
                    ':type' => 'withdraw',
                    ':amount' => -$order['total'],
                    ':description' => 'Thanh toán đơn hàng #' . str_pad($orderId, 6, '0', STR_PAD_LEFT)
                ]);
                
                // Cập nhật phương thức thanh toán
                $this->orderModel->updatePaymentMethod($orderId, $newPaymentMethod);
                
                // Cập nhật trạng thái đơn hàng và thanh toán
                $this->orderModel->updateStatus($orderId, 'processing');
                $this->orderModel->updatePaymentStatus($orderId, 'paid');
                
                // Commit transaction
                $this->orderModel->pdo->commit();
                
                $_SESSION['success'] = 'Đã đổi sang thanh toán bằng ví và thanh toán thành công. Đơn hàng đang được xử lý';
            } 
            // Nếu đổi sang VNPay, chuyển hướng đến VNPay
            elseif ($newPaymentMethod === 'vnpay') {
                // Cập nhật phương thức thanh toán
                $this->orderModel->updatePaymentMethod($orderId, $newPaymentMethod);
                
                // Chuyển hướng đến VNPay
                require_once PATH_ROOT . 'configs/vnpay.php';
                $orderInfo = 'Thanh toán đơn hàng #' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
                $vnpayUrl = createVNPayPaymentUrl('order_' . $orderId, $order['total'], $orderInfo);
                header('Location: ' . $vnpayUrl);
                exit;
            }
            // Nếu đổi sang COD
            else {
                $this->orderModel->updatePaymentMethod($orderId, $newPaymentMethod);
                $_SESSION['success'] = 'Đã đổi sang thanh toán khi nhận hàng (COD)';
            }

            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        } catch (Exception $e) {
            if ($this->orderModel->pdo->inTransaction()) {
                $this->orderModel->pdo->rollBack();
            }
            $_SESSION['errors'] = ['update' => 'Đổi phương thức thanh toán thất bại: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '?action=order-change-payment&id=' . $orderId);
            exit;
        }
    }

    /**
     * User xác nhận đã nhận hàng - chuyển đơn hàng sang hoàn thành
     */
    public function confirmReceived()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        $orderId = $_POST['order_id'] ?? 0;
        $order = $this->orderModel->getById($orderId);

        if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['order' => 'Đơn hàng không tồn tại'];
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        // Chỉ cho phép xác nhận khi đơn hàng đang giao
        if ($order['status'] !== 'delivering') {
            $_SESSION['errors'] = ['order' => 'Chỉ có thể xác nhận nhận hàng khi đơn hàng đang được giao'];
            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
            exit;
        }

        try {
            // Cập nhật trạng thái đơn hàng sang hoàn thành
            $this->orderModel->updateStatus($orderId, 'completed');
            
            // Cập nhật payment_status nếu là COD
            if ($order['payment_method'] === 'cod') {
                $this->orderModel->updatePaymentStatus($orderId, 'paid');
            }

            // Cộng điểm loyalty cho user
            $loyaltyModel = new LoyaltyPoint();
            $loyaltyModel->addPointsFromOrder($_SESSION['user']['id'], $order['total']);

            $_SESSION['success'] = 'Cảm ơn bạn đã xác nhận nhận hàng! Đơn hàng đã hoàn thành.';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['order' => 'Xác nhận thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
        exit;
    }

    public function notifications()
    {
        $notificationModel = new Notification();
        $notifications = $notificationModel->getByUserId($_SESSION['user']['id'], 50);
        require_once PATH_VIEW . 'notifications/index.php';
    }

    public function readNotification()
    {
        $id = $_GET['id'] ?? 0;
        $notificationModel = new Notification();
        $notificationModel->markAsRead($id, $_SESSION['user']['id']);

        // Redirect đến trang được chỉ định hoặc về trang thông báo
        $redirect = $_GET['redirect'] ?? '';
        if ($redirect === 'order-detail' && isset($_GET['order_id'])) {
            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $_GET['order_id']);
        } else {
            header('Location: ' . BASE_URL . '?action=notifications');
        }
        exit;
    }

    public function readAllNotifications()
    {
        $notificationModel = new Notification();
        $notificationModel->markAllAsRead($_SESSION['user']['id']);
        
        $_SESSION['success'] = 'Đã đánh dấu tất cả thông báo là đã đọc';
        header('Location: ' . BASE_URL . '?action=notifications');
        exit;
    }

    public function readAllNotificationsAjax()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $notificationModel = new Notification();
        $result = $notificationModel->markAllAsRead($_SESSION['user']['id']);
        
        if ($result) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Đã đánh dấu tất cả thông báo là đã đọc']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
        exit;
    }
}
