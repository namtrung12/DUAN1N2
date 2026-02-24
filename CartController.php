<?php

class CartController
{
    private $cartModel;
    private $productModel;
    private $toppingModel;
    private $couponModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        $this->toppingModel = new Topping();
        $this->couponModel = new Coupon();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập để sử dụng giỏ hàng'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    private function getCartRedirectUrl($keepSelected = true)
    {
        $redirectUrl = BASE_URL . '?action=cart';
        if ($keepSelected && isset($_SESSION['cart_selected_items']) && !empty($_SESSION['cart_selected_items'])) {
            $redirectUrl .= '&keep_selected=1&selected_items=' . urlencode($_SESSION['cart_selected_items']);
        }
        return $redirectUrl;
    }

    public function index()
    {
        // Kiểm tra nếu có selected_items từ session hoặc GET
        $selectedItemIds = [];
        if (isset($_SESSION['cart_selected_items']) && !empty($_SESSION['cart_selected_items'])) {
            $selectedItemIds = explode(',', $_SESSION['cart_selected_items']);
            $selectedItemIds = array_map('intval', $selectedItemIds);
        } elseif (isset($_GET['selected_items']) && !empty($_GET['selected_items'])) {
            $selectedItemIds = explode(',', $_GET['selected_items']);
            $selectedItemIds = array_map('intval', $selectedItemIds);
            $_SESSION['cart_selected_items'] = $_GET['selected_items'];
        }
        
        // Nếu không có selected_items và có clear_selected trong GET, xóa selected_items
        if (isset($_GET['clear_selected'])) {
            unset($_SESSION['cart_selected_items']);
            $selectedItemIds = [];
        }
        
        $cartItems = $this->cartModel->getByUserId($_SESSION['user']['id']);
        
        $cartData = [];
        $subtotal = 0;
        $toppingTotal = 0;

        foreach ($cartItems as $item) {
            // Nếu có selected_items, chỉ xử lý các item đã chọn
            if (!empty($selectedItemIds) && !in_array($item['id'], $selectedItemIds)) {
                // Vẫn hiển thị item nhưng không tính vào tổng
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
                    'item_total' => $itemTotal,
                    'is_selected' => false
                ];
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
                'item_total' => $itemTotal,
                'is_selected' => empty($selectedItemIds) || in_array($item['id'], $selectedItemIds)
            ];

            $subtotal += $itemPrice * $item['quantity'];
            $toppingTotal += $itemToppingCost * $item['quantity'];
        }

        $discount = 0;
        $coupon = null;
        // Lấy rank của user để kiểm tra coupon
        $loyaltyModel = new LoyaltyPoint();
        $loyaltyData = $loyaltyModel->getByUserId($_SESSION['user']['id']);
        $lifetimePoints = $loyaltyData['lifetime_points'] ?? 0;
        $userRank = $loyaltyData['level'] ?? 'new';
        
        // Recalculate rank dựa trên lifetime_points (đảm bảo chính xác)
        if ($lifetimePoints >= 1000) {
            $userRank = 'diamond';
        } elseif ($lifetimePoints >= 600) {
            $userRank = 'gold';
        } elseif ($lifetimePoints >= 400) {
            $userRank = 'silver';
        } elseif ($lifetimePoints >= 200) {
            $userRank = 'bronze';
        } else {
            $userRank = 'new';
        }
        
        if (isset($_SESSION['cart_coupon'])) {
            $coupon = $this->couponModel->getByCode($_SESSION['cart_coupon']);
            if ($coupon && $this->couponModel->isValid($coupon, $subtotal + $toppingTotal, $userRank)) {
                $discount = $this->couponModel->calculateDiscount($coupon, $subtotal + $toppingTotal);
            } else {
                unset($_SESSION['cart_coupon']);
            }
        }

        $total = $subtotal + $toppingTotal - $discount;

        // Tính toán mã tiếp theo có thể nhận
        // CHỈ hiển thị mã phù hợp với rank của user (không hiển thị mã rank cao hơn)
        $nextCouponInfo = null;
        $currentTotal = $subtotal + $toppingTotal;
        
        if ($lifetimePoints >= 200) {
            // Danh sách ngưỡng mã theo thứ tự
            $couponThresholds = [
                ['min' => 50000, 'code' => 'BRONZE10', 'discount' => '10%', 'rank' => 'bronze', 'rank_name' => 'Bronze'],
                ['min' => 100000, 'code' => 'SILVER15', 'discount' => '15%', 'rank' => 'silver', 'rank_name' => 'Silver'],
                ['min' => 150000, 'code' => 'GOLD20', 'discount' => '20%', 'rank' => 'gold', 'rank_name' => 'Gold'],
                ['min' => 200000, 'code' => 'DIAMOND25', 'discount' => '25%', 'rank' => 'diamond', 'rank_name' => 'Diamond']
            ];
            
            // Xác định rank level
            $rankLevels = ['new' => 0, 'bronze' => 1, 'silver' => 2, 'gold' => 3, 'diamond' => 4];
            $currentRankLevel = $rankLevels[$userRank] ?? 0;
            
            // CHỈ gợi ý mã có rank <= rank của user
            foreach ($couponThresholds as $threshold) {
                $thresholdLevel = $rankLevels[$threshold['rank']] ?? 0;
                
                // Chỉ hiển thị nếu: user đủ rank VÀ đơn hàng chưa đủ ngưỡng
                if ($thresholdLevel <= $currentRankLevel && $currentTotal < $threshold['min']) {
                    $nextCouponInfo = [
                        'needed' => $threshold['min'] - $currentTotal,
                        'code' => $threshold['code'],
                        'discount' => $threshold['discount'],
                        'rank' => $threshold['rank_name'],
                        'min_order' => $threshold['min']
                    ];
                    break;
                }
            }
        }

        // Lấy mã gợi ý (bao gồm cả mã theo rank và mã đã đổi)
        $suggestedCoupons = [];
        try {
            // Khách mới: hiển thị mã ngay (không cần đơn tối thiểu)
            // Khách có rank: cần đơn >= 100k mới hiển thị
            $minOrderForSuggestion = ($lifetimePoints < 200) ? 0 : 100000;
            
            if (($subtotal + $toppingTotal) >= $minOrderForSuggestion) {
                // Lấy mã theo rank - LUÔN HIỂN THỊ (dùng không giới hạn)
                $suggestedCoupons = $this->couponModel->getSuggestedCoupons($userRank, $subtotal + $toppingTotal);
                
                // Lấy mã đã đổi (từ user_redeemed_coupons) - CHỈ DÙNG 1 LẦN
                $redeemedCoupons = $this->couponModel->getUserRedeemedCoupons($_SESSION['user']['id']);
                
                foreach ($redeemedCoupons as $redeemed) {
                    // Kiểm tra mã đã đổi có hợp lệ không
                    if ($this->couponModel->isValid($redeemed, $subtotal + $toppingTotal, $userRank)) {
                        // Kiểm tra chưa dùng (chỉ check cho mã đổi điểm)
                        if (!$this->couponModel->hasUserUsedCoupon($_SESSION['user']['id'], $redeemed['id'])) {
                            $redeemed['is_redeemed'] = true; // Đánh dấu là mã đã đổi
                            $suggestedCoupons[] = $redeemed;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Bỏ qua lỗi nếu có vấn đề với database
            error_log('Suggested coupons error: ' . $e->getMessage());
            $suggestedCoupons = [];
        }

        require_once PATH_VIEW . 'cart/index.php';
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=products');
            exit;
        }

        $productId = $_POST['product_id'] ?? 0;
        $productSizeId = $_POST['product_size_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        $toppingIds = $_POST['toppings'] ?? [];
        $iceLevel = $_POST['ice_level'] ?? 100;
        $sugarLevel = $_POST['sugar_level'] ?? 100;
        $note = $_POST['note'] ?? null;

        $errors = [];

        $product = $this->productModel->getById($productId);
        if (!$product) {
            $errors['product'] = 'Sản phẩm không tồn tại';
        }

        $productSize = $this->productModel->getProductSizeById($productSizeId);
        if (!$productSize) {
            $errors['size'] = 'Vui lòng chọn size';
        }

        if ($quantity < 1 || $quantity > 99) {
            $errors['quantity'] = 'Số lượng không hợp lệ';
        }

        if (!empty($toppingIds)) {
            $validToppings = $this->toppingModel->getByIds($toppingIds);
            if (count($validToppings) !== count($toppingIds)) {
                $errors['toppings'] = 'Topping không hợp lệ';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=product-detail&id=' . $productId);
            exit;
        }

        try {
            $cartId = $this->cartModel->add(
                $_SESSION['user']['id'],
                $productId,
                $productSize['size_name'],
                $quantity,
                $iceLevel,
                $sugarLevel,
                $note
            );
            
            if ($cartId && !empty($toppingIds)) {
                $this->cartModel->addToppings($cartId, $toppingIds);
            }

            $_SESSION['success'] = 'Đã thêm sản phẩm vào giỏ hàng';
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        } catch (Exception $e) {
            $_SESSION['errors'] = ['cart' => 'Thêm vào giỏ hàng thất bại. Vui lòng thử lại'];
            header('Location: ' . BASE_URL . '?action=product-detail&id=' . $productId);
            exit;
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        $cartId = $_POST['cart_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;

        $cartItem = $this->cartModel->getById($cartId);
        if (!$cartItem || $cartItem['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['cart' => 'Sản phẩm không tồn tại trong giỏ hàng'];
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        if ($quantity < 1 || $quantity > 99) {
            $_SESSION['errors'] = ['quantity' => 'Số lượng không hợp lệ'];
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        try {
            $this->cartModel->updateQuantity($cartId, $_SESSION['user']['id'], $quantity);
            $_SESSION['success'] = 'Đã cập nhật giỏ hàng';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['cart' => 'Cập nhật giỏ hàng thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=cart');
        exit;
    }

    public function remove()
    {
        $cartId = $_GET['id'] ?? 0;

        $cartItem = $this->cartModel->getById($cartId);
        if (!$cartItem || $cartItem['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['cart' => 'Sản phẩm không tồn tại trong giỏ hàng'];
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        try {
            $this->cartModel->remove($cartId, $_SESSION['user']['id']);
            $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['cart' => 'Xóa sản phẩm thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=cart');
        exit;
    }

    public function removeMultiple()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        $cartIds = $_POST['cart_ids'] ?? [];
        
        if (empty($cartIds) || !is_array($cartIds)) {
            $_SESSION['errors'] = ['cart' => 'Vui lòng chọn ít nhất một sản phẩm để xóa'];
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        $removedCount = 0;
        foreach ($cartIds as $id) {
            $cartItem = $this->cartModel->getById($id);
            if ($cartItem && $cartItem['user_id'] == $_SESSION['user']['id']) {
                if ($this->cartModel->remove($id, $_SESSION['user']['id'])) {
                    $removedCount++;
                }
            }
        }

        if ($removedCount > 0) {
            $_SESSION['success'] = "Đã xóa {$removedCount} sản phẩm khỏi giỏ hàng";
        } else {
            $_SESSION['errors'] = ['cart' => 'Không thể xóa sản phẩm'];
        }

        header('Location: ' . BASE_URL . '?action=cart');
        exit;
    }

    public function applyCoupon()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        $couponCode = $_POST['coupon_code'] ?? '';

        // Kiểm tra nếu có selected_items từ session
        $selectedItemIds = [];
        if (isset($_SESSION['cart_selected_items']) && !empty($_SESSION['cart_selected_items'])) {
            $selectedItemIds = explode(',', $_SESSION['cart_selected_items']);
            $selectedItemIds = array_map('intval', $selectedItemIds);
        }

        if (empty($couponCode)) {
            $_SESSION['errors'] = ['coupon' => 'Vui lòng nhập mã giảm giá'];
            header('Location: ' . $this->getCartRedirectUrl());
            exit;
        }

        $coupon = $this->couponModel->getByCode($couponCode);

        if (!$coupon) {
            $_SESSION['errors'] = ['coupon' => 'Mã giảm giá không tồn tại'];
            header('Location: ' . $this->getCartRedirectUrl());
            exit;
        }

        // Kiểm tra nếu là mã đổi điểm (is_redeemable = 1)
        if ($coupon['is_redeemable']) {
            // Phải đổi điểm trước mới được dùng
            if (!$this->couponModel->hasUserRedeemed($_SESSION['user']['id'], $coupon['id'])) {
                $_SESSION['errors'] = ['coupon' => 'Bạn cần đổi điểm để nhận mã này trước khi sử dụng'];
                header('Location: ' . $this->getCartRedirectUrl());
                exit;
            }
            // Mã đổi điểm chỉ dùng được 1 lần
            if ($this->couponModel->hasUserUsedCoupon($_SESSION['user']['id'], $coupon['id'])) {
                $_SESSION['errors'] = ['coupon' => 'Bạn đã sử dụng mã đổi điểm này rồi'];
                header('Location: ' . $this->getCartRedirectUrl());
                exit;
            }
        }
        
        $cartItems = $this->cartModel->getByUserId($_SESSION['user']['id']);
        $subtotal = 0;
        $toppingTotal = 0;

        foreach ($cartItems as $item) {
            // Nếu có selected_items, chỉ tính cho các item đã chọn
            if (!empty($selectedItemIds) && !in_array($item['id'], $selectedItemIds)) {
                continue;
            }
            
            $sizes = $this->productModel->getSizes($item['product_id']);
            foreach ($sizes as $size) {
                if ($size['size_name'] === $item['size']) {
                    $subtotal += $size['price'] * $item['quantity'];
                    break;
                }
            }
            $toppings = $this->cartModel->getToppings($item['id']);
            foreach ($toppings as $topping) {
                $toppingTotal += $topping['price'] * $item['quantity'];
            }
        }
        
        // Tổng tiền để kiểm tra coupon
        $totalForCoupon = $subtotal + $toppingTotal;

        // Lấy rank của user
        $loyaltyModel = new LoyaltyPoint();
        $loyaltyData = $loyaltyModel->getByUserId($_SESSION['user']['id']);
        $lifetimePoints = $loyaltyData['lifetime_points'] ?? 0;
        
        // Recalculate rank dựa trên lifetime_points
        if ($lifetimePoints >= 1000) {
            $userRank = 'diamond';
        } elseif ($lifetimePoints >= 600) {
            $userRank = 'gold';
        } elseif ($lifetimePoints >= 400) {
            $userRank = 'silver';
        } elseif ($lifetimePoints >= 200) {
            $userRank = 'bronze';
        } else {
            $userRank = 'new';
        }

        // Kiểm tra: Khách mới (< 200 điểm) chỉ được dùng mã WELCOME10 hoặc mã không yêu cầu rank
        if ($lifetimePoints < 200 && $coupon['required_rank']) {
            $_SESSION['errors'] = ['coupon' => 'Khách mới chỉ được sử dụng mã WELCOME10. Tích lũy 200 điểm để mở khóa thêm mã!'];
            header('Location: ' . $this->getCartRedirectUrl());
            exit;
        }

        if (!$this->couponModel->isValid($coupon, $totalForCoupon, $userRank)) {
            $errors = [];
            if (!$coupon['status']) {
                $errors['coupon'] = 'Mã giảm giá không hoạt động';
            } elseif ($coupon['usage_limit'] > 0 && $coupon['used_count'] >= $coupon['usage_limit']) {
                $errors['coupon'] = 'Mã giảm giá đã hết lượt sử dụng';
            } elseif ($coupon['min_order'] > 0 && $totalForCoupon < $coupon['min_order']) {
                $errors['coupon'] = 'Đơn hàng tối thiểu ' . number_format($coupon['min_order'], 0, ',', '.') . 'đ (hiện tại: ' . number_format($totalForCoupon, 0, ',', '.') . 'đ)';
            } elseif ($coupon['starts_at'] && strtotime($coupon['starts_at']) > time()) {
                $errors['coupon'] = 'Mã giảm giá chưa có hiệu lực';
            } elseif ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) {
                $errors['coupon'] = 'Mã giảm giá đã hết hạn';
            } elseif ($coupon['required_rank']) {
                $rankNames = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'diamond' => 'Kim cương'];
                $rankOrder = ['bronze' => 1, 'silver' => 2, 'gold' => 3, 'diamond' => 4];
                $requiredLevel = $rankOrder[$coupon['required_rank']] ?? 0;
                $userLevel = $rankOrder[$userRank] ?? 0;
                if ($userLevel < $requiredLevel) {
                    $errors['coupon'] = 'Mã này chỉ dành cho thành viên ' . $rankNames[$coupon['required_rank']] . ' trở lên';
                } else {
                    $errors['coupon'] = 'Mã giảm giá không hợp lệ';
                }
            } else {
                $errors['coupon'] = 'Mã giảm giá không hợp lệ';
            }
            $_SESSION['errors'] = $errors;
            header('Location: ' . $this->getCartRedirectUrl());
            exit;
        }

        $_SESSION['cart_coupon'] = strtoupper($couponCode);
        $_SESSION['success'] = 'Áp dụng mã giảm giá thành công';
        header('Location: ' . $this->getCartRedirectUrl());
        exit;
    }

    public function removeCoupon()
    {
        unset($_SESSION['cart_coupon']);
        $_SESSION['success'] = 'Đã hủy mã giảm giá';
        header('Location: ' . $this->getCartRedirectUrl());
        exit;
    }

    public function setSelected()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=cart');
            exit;
        }

        $selectedItems = $_POST['selected_items'] ?? '';
        $action = $_POST['action'] ?? 'update_cart'; // 'checkout', 'checkout_all' hoặc 'update_cart'
        
        // Nếu action là checkout_all, xóa selected_items và chuyển đến checkout
        if ($action === 'checkout_all') {
            unset($_SESSION['cart_selected_items']);
            header('Location: ' . BASE_URL . '?action=checkout&clear_selected=1');
            exit;
        }
        
        if (!empty($selectedItems)) {
            $_SESSION['cart_selected_items'] = $selectedItems;
            if ($action === 'checkout') {
                header('Location: ' . BASE_URL . '?action=checkout');
            } else {
                header('Location: ' . BASE_URL . '?action=cart&keep_selected=1&selected_items=' . urlencode($selectedItems));
            }
        } else {
            unset($_SESSION['cart_selected_items']);
            header('Location: ' . BASE_URL . '?action=cart');
        }
        exit;
    }
}
