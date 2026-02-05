<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

function render(string $view, array $data = []): void
{
    extract($data);
    include PATH_VIEW . $view;
}

function paginate(array $items, int $page, int $perPage): array
{
    $total = count($items);
    $totalPages = (int)ceil($total / $perPage);
    $page = max(1, min($page, max(1, $totalPages)));
    $offset = ($page - 1) * $perPage;
    $slice = array_slice($items, $offset, $perPage);
    return [$slice, $totalPages, $page, $total];
}

function handle_upload(string $field, string $targetDir): ?string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$field];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filename = uniqid('upload_', true) . '.' . $ext;
    $destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    return $filename;
}

function parse_selected_items($input): array
{
    if (is_array($input)) {
        return array_values(array_filter(array_map('intval', $input)));
    }
    if (is_string($input)) {
        $parts = array_filter(array_map('trim', explode(',', $input)));
        return array_values(array_filter(array_map('intval', $parts)));
    }
    return [];
}

function normalize_toppings(array $toppings): array
{
    $result = [];
    foreach ($toppings as $topping) {
        $result[] = [
            'id' => $topping['id'] ?? null,
            'name' => $topping['name'] ?? ($topping['topping_name'] ?? ''),
            'price' => (int)($topping['price'] ?? 0)
        ];
    }
    return $result;
}

function build_cart_data(array $cart, array $selectedIds = []): array
{
    $cartData = [];
    $subtotal = 0;
    $toppingTotal = 0;
    $selectedLookup = [];
    foreach ($selectedIds as $id) {
        $selectedLookup[(int)$id] = true;
    }

    $items = $cart['items'] ?? [];
    foreach ($items as $item) {
        $toppings = normalize_toppings($item['toppings'] ?? []);
        $itemPrice = (int)($item['unit_price'] ?? 0);
        $quantity = (int)($item['quantity'] ?? 0);
        $toppingCost = 0;
        foreach ($toppings as $topping) {
            $toppingCost += (int)$topping['price'];
        }
        $itemTotal = ($itemPrice + $toppingCost) * $quantity;
        $item['size'] = $item['size'] ?? ($item['size_name'] ?? '');

        $isSelected = empty($selectedIds) || isset($selectedLookup[(int)$item['id']]);
        if ($isSelected) {
            $subtotal += $itemPrice * $quantity;
            $toppingTotal += $toppingCost * $quantity;
        }

        $cartData[] = [
            'cart_item' => $item,
            'size_info' => ['price' => $itemPrice],
            'toppings' => $toppings,
            'item_price' => $itemPrice,
            'topping_cost' => $toppingCost,
            'item_total' => $itemTotal,
            'is_selected' => $isSelected
        ];
    }

    return [$cartData, $subtotal, $toppingTotal];
}

function rank_level(string $rank): int
{
    $map = [
        'new' => 0,
        'bronze' => 1,
        'silver' => 2,
        'gold' => 3,
        'diamond' => 4
    ];
    return $map[$rank] ?? 0;
}

function find_coupon_by_code(array $coupons, string $code): ?array
{
    foreach ($coupons as $coupon) {
        if (strcasecmp($coupon['code'] ?? '', $code) === 0) {
            return $coupon;
        }
    }
    return null;
}

function calculate_coupon_discount(array $coupon, int $amount, string $userRank): int
{
    if ((int)($coupon['status'] ?? 0) !== 1) {
        return 0;
    }
    $minOrder = (int)($coupon['min_order'] ?? 0);
    if ($amount < $minOrder) {
        return 0;
    }
    $requiredRank = $coupon['required_rank'] ?? '';
    if ($requiredRank !== '' && rank_level($userRank) < rank_level($requiredRank)) {
        return 0;
    }
    $discount = 0;
    if (($coupon['type'] ?? 'fixed') === 'percent') {
        $discount = (int)round($amount * ((int)($coupon['value'] ?? 0) / 100));
        $maxDiscount = (int)($coupon['max_discount'] ?? 0);
        if ($maxDiscount > 0) {
            $discount = min($discount, $maxDiscount);
        }
    } else {
        $discount = (int)($coupon['value'] ?? 0);
    }
    return max(0, $discount);
}

$action = $_GET['action'] ?? 'home';
$store = get_store();

switch ($action) {
    case 'home':
    case '':
        render('home/index.php');
        break;

    case 'products':
        $productModel = new Product($store);
        $categoryModel = new Category($store);
        $categories = $categoryModel->getAll();
        $products = $productModel->getAll();
        $search = trim($_GET['search'] ?? '');
        $categoryId = (int)($_GET['category_id'] ?? 0);
        if ($search !== '') {
            $products = $productModel->search($search);
        }
        if ($categoryId > 0) {
            $products = array_values(array_filter($products, function ($p) use ($categoryId) {
                return (int)$p['category_id'] === $categoryId;
            }));
        }
        render('products/index.php', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId
        ]);
        break;

    case 'products-by-category':
        $categoryId = (int)($_GET['category_id'] ?? 0);
        $_GET['category_id'] = $categoryId;
        $productModel = new Product($store);
        $categoryModel = new Category($store);
        $categories = $categoryModel->getAll();
        $products = $productModel->getAll();
        if ($categoryId > 0) {
            $products = array_values(array_filter($products, function ($p) use ($categoryId) {
                return (int)$p['category_id'] === $categoryId;
            }));
        }
        render('products/index.php', [
            'products' => $products,
            'categories' => $categories,
            'search' => '',
            'categoryId' => $categoryId
        ]);
        break;

    case 'product-detail':
        $productModel = new Product($store);
        $id = (int)($_GET['id'] ?? 0);
        $product = $productModel->findById($id);
        if (!$product) {
            set_flash('errors', ['Product not found.']);
            redirect('products');
        }
        $sizes = $productModel->getSizes($id);
        $toppings = $productModel->getToppings($id);
        $reviewModel = new Review($store);
        $reviews = $reviewModel->getByProductId($id, 1);
        $reviewCount = count($reviews);
        $avgRating = 0;
        if ($reviewCount > 0) {
            $sumRating = 0;
            foreach ($reviews as $review) {
                $sumRating += (int)($review['rating'] ?? 0);
            }
            $avgRating = $sumRating / $reviewCount;
        }
        render('products/detail.php', [
            'product' => $product,
            'sizes' => $sizes,
            'toppings' => $toppings,
            'reviews' => $reviews,
            'reviewCount' => $reviewCount,
            'avgRating' => $avgRating
        ]);
        break;
    case 'cart':
        require_login();
        $cartModel = new Cart($store);
        $cart = $cartModel->getByUserId((int)$_SESSION['user']['id']);
        $selectedIds = parse_selected_items($_SESSION['selected_items'] ?? []);
        [$cartData, $subtotal, $toppingTotal] = build_cart_data($cart, $selectedIds);

        $loyaltyModel = new Loyalty($store);
        $loyaltyData = $loyaltyModel->getByUserId((int)$_SESSION['user']['id']);
        $userRank = $loyaltyData['level'] ?? 'new';

        $couponModel = new Coupon($store);
        $coupons = $couponModel->getAll();
        $discount = 0;
        if (isset($_SESSION['cart_coupon'])) {
            $appliedCoupon = find_coupon_by_code($coupons, (string)$_SESSION['cart_coupon']);
            if ($appliedCoupon) {
                $discount = calculate_coupon_discount($appliedCoupon, $subtotal + $toppingTotal, $userRank);
            } else {
                unset($_SESSION['cart_coupon']);
            }
        }
        $total = max(0, $subtotal + $toppingTotal - $discount);

        $suggestedCoupons = [];
        foreach ($coupons as $coupon) {
            if ((int)($coupon['status'] ?? 0) !== 1) {
                continue;
            }
            $coupon['is_redeemed'] = $couponModel->hasUserRedeemed((int)$_SESSION['user']['id'], (int)$coupon['id']);
            $suggestedCoupons[] = $coupon;
        }

        $nextCouponInfo = null;
        $currentRankLevel = rank_level($userRank);
        if ($currentRankLevel >= 1) {
            $thresholds = [
                ['min_order' => 50000, 'code' => 'BRONZE10', 'discount' => '10%', 'rankLevel' => 1],
                ['min_order' => 100000, 'code' => 'SILVER15', 'discount' => '15%', 'rankLevel' => 2],
                ['min_order' => 150000, 'code' => 'GOLD20', 'discount' => '20%', 'rankLevel' => 3],
                ['min_order' => 200000, 'code' => 'DIAMOND25', 'discount' => '25%', 'rankLevel' => 4]
            ];
            $currentTotal = $subtotal + $toppingTotal;
            foreach ($thresholds as $threshold) {
                if ($threshold['rankLevel'] <= $currentRankLevel && $currentTotal < $threshold['min_order']) {
                    $nextCouponInfo = [
                        'needed' => $threshold['min_order'] - $currentTotal,
                        'min_order' => $threshold['min_order'],
                        'code' => $threshold['code'],
                        'discount' => $threshold['discount']
                    ];
                    break;
                }
            }
        }

        render('cart/index.php', [
            'cartData' => $cartData,
            'subtotal' => $subtotal,
            'toppingTotal' => $toppingTotal,
            'discount' => $discount,
            'total' => $total,
            'userRank' => $userRank,
            'nextCouponInfo' => $nextCouponInfo,
            'suggestedCoupons' => $suggestedCoupons
        ]);
        break;

    case 'cart-add':
        require_login();
        $productModel = new Product($store);
        $toppingModel = new Topping($store);
        $cartModel = new Cart($store);
        $productId = (int)($_POST['product_id'] ?? 0);
        $productSizeId = (int)($_POST['product_size_id'] ?? ($_POST['size_id'] ?? 0));
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $iceLevel = (int)($_POST['ice_level'] ?? 100);
        $sugarLevel = (int)($_POST['sugar_level'] ?? 100);
        $note = trim($_POST['note'] ?? '');
        $toppingIds = array_map('intval', $_POST['toppings'] ?? []);
        $product = $productModel->findById($productId);
        if (!$product) {
            set_flash('errors', ['Product not found.']);
            redirect('products');
        }
        $sizes = $productModel->getSizes($productId);
        $sizeName = '';
        $unitPrice = 0;
        $sizeOptionId = 0;
        foreach ($sizes as $size) {
            if ((int)$size['id'] === $productSizeId) {
                $sizeName = $size['size_name'];
                $unitPrice = (int)$size['price'];
                $sizeOptionId = (int)$size['size_id'];
                break;
            }
        }
        if ($productSizeId === 0 || $unitPrice === 0) {
            set_flash('errors', ['Please select a size.']);
            redirect('product-detail', ['id' => $productId]);
        }
        $allToppings = $toppingModel->getAll();
        $selectedToppings = [];
        foreach ($allToppings as $topping) {
            if (in_array((int)$topping['id'], $toppingIds, true)) {
                $selectedToppings[] = [
                    'id' => (int)$topping['id'],
                    'name' => $topping['name'],
                    'topping_name' => $topping['name'],
                    'price' => (int)$topping['price']
                ];
            }
        }
        $cartModel->addItem((int)$_SESSION['user']['id'], [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'image' => $product['image'] ?? 'placeholder.svg',
            'product_size_id' => $productSizeId,
            'size_id' => $sizeOptionId,
            'size_name' => $sizeName,
            'size' => $sizeName,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'ice_level' => $iceLevel,
            'sugar_level' => $sugarLevel,
            'note' => $note,
            'toppings' => $selectedToppings
        ]);
        set_flash('success', 'Added to cart.');
        redirect('cart');
        break;

    case 'cart-update':
        require_login();
        $cartModel = new Cart($store);
        $itemId = (int)($_POST['item_id'] ?? ($_POST['cart_id'] ?? 0));
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $cartModel->updateQuantity((int)$_SESSION['user']['id'], $itemId, $quantity);
        redirect('cart');
        break;

    case 'cart-remove':
        require_login();
        $cartModel = new Cart($store);
        $itemId = (int)($_GET['item_id'] ?? 0);
        $cartModel->removeItem((int)$_SESSION['user']['id'], $itemId);
        redirect('cart');
        break;

    case 'cart-remove-multiple':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = array_map('intval', $_POST['cart_ids'] ?? []);
            $cartModel = new Cart($store);
            foreach ($ids as $id) {
                $cartModel->removeItem((int)$_SESSION['user']['id'], $id);
            }
        }
        redirect('cart');
        break;

    case 'cart-set-selected':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selected = parse_selected_items($_POST['selected_items'] ?? '');
            $actionFlag = $_POST['action'] ?? '';
            if (empty($selected)) {
                unset($_SESSION['selected_items']);
            } else {
                $_SESSION['selected_items'] = $selected;
            }
            if ($actionFlag === 'checkout' || $actionFlag === 'checkout_all') {
                if ($actionFlag === 'checkout_all') {
                    unset($_SESSION['selected_items']);
                }
                redirect('checkout');
            }
        }
        redirect('cart');
        break;

    case 'cart-apply-coupon':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
            if ($code === '') {
                set_flash('errors', ['Vui lòng nhập mã giảm giá.']);
                redirect('cart');
            }
            $couponModel = new Coupon($store);
            $coupons = $couponModel->getAll();
            $coupon = find_coupon_by_code($coupons, $code);
            if (!$coupon || (int)($coupon['status'] ?? 0) !== 1) {
                set_flash('errors', ['Mã giảm giá không hợp lệ.']);
                redirect('cart');
            }
            $cartModel = new Cart($store);
            $cart = $cartModel->getByUserId((int)$_SESSION['user']['id']);
            $selectedIds = parse_selected_items($_SESSION['selected_items'] ?? []);
            [, $subtotal, $toppingTotal] = build_cart_data($cart, $selectedIds);
            $loyaltyModel = new Loyalty($store);
            $userRank = ($loyaltyModel->getByUserId((int)$_SESSION['user']['id']))['level'] ?? 'new';
            $discount = calculate_coupon_discount($coupon, $subtotal + $toppingTotal, $userRank);
            if ($discount <= 0) {
                set_flash('errors', ['Mã giảm giá chưa đủ điều kiện áp dụng.']);
                redirect('cart');
            }
            $_SESSION['cart_coupon'] = $coupon['code'];
            set_flash('success', 'Áp dụng mã giảm giá thành công.');
        }
        redirect('cart');
        break;

    case 'cart-remove-coupon':
        require_login();
        unset($_SESSION['cart_coupon']);
        redirect('cart');
        break;

    case 'checkout':
        require_login();
        $cartModel = new Cart($store);
        $cart = $cartModel->getByUserId((int)$_SESSION['user']['id']);
        $selectedIds = parse_selected_items($_SESSION['selected_items'] ?? []);
        [$cartData, $subtotal, $toppingTotal] = build_cart_data($cart, $selectedIds);
        if (!empty($selectedIds)) {
            $cartData = array_values(array_filter($cartData, function ($row) {
                return !empty($row['is_selected']);
            }));
        }
        if (empty($cartData)) {
            set_flash('errors', ['Giỏ hàng của bạn đang trống.']);
            redirect('cart');
        }

        $addressModel = new Address($store);
        $addresses = $addressModel->getByUserId((int)$_SESSION['user']['id']);
        $defaultAddress = null;
        foreach ($addresses as $addr) {
            if (!empty($addr['is_default'])) {
                $defaultAddress = $addr;
                break;
            }
        }
        if (!$defaultAddress && !empty($addresses)) {
            $defaultAddress = $addresses[0];
        }

        $loyaltyModel = new Loyalty($store);
        $userRank = ($loyaltyModel->getByUserId((int)$_SESSION['user']['id']))['level'] ?? 'new';
        $couponModel = new Coupon($store);
        $coupons = $couponModel->getAll();
        $discount = 0;
        if (isset($_SESSION['cart_coupon'])) {
            $appliedCoupon = find_coupon_by_code($coupons, (string)$_SESSION['cart_coupon']);
            if ($appliedCoupon) {
                $discount = calculate_coupon_discount($appliedCoupon, $subtotal + $toppingTotal, $userRank);
            }
        }

        $shippingFee = 15000;
        $total = max(0, $subtotal + $toppingTotal + $shippingFee - $discount);

        render('orders/checkout.php', [
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'cartData' => $cartData,
            'subtotal' => $subtotal,
            'toppingTotal' => $toppingTotal,
            'shippingFee' => $shippingFee,
            'discount' => $discount,
            'total' => $total
        ]);
        break;

    case 'checkout-process':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartModel = new Cart($store);
            $cart = $cartModel->getByUserId((int)$_SESSION['user']['id']);
            $selectedIds = parse_selected_items($_SESSION['selected_items'] ?? []);
            [$cartData, $subtotal, $toppingTotal] = build_cart_data($cart, $selectedIds);
            if (!empty($selectedIds)) {
                $cartData = array_values(array_filter($cartData, function ($row) {
                    return !empty($row['is_selected']);
                }));
            }
            if (empty($cartData)) {
                set_flash('errors', ['Giỏ hàng của bạn đang trống.']);
                redirect('cart');
            }

            $addressId = (int)($_POST['address_id'] ?? 0);
            $addressModel = new Address($store);
            $address = $addressModel->findById($addressId);
            if (!$address || (int)$address['user_id'] !== (int)$_SESSION['user']['id']) {
                set_flash('errors', ['Vui lòng chọn địa chỉ giao hàng hợp lệ.']);
                redirect('checkout');
            }

            $paymentMethod = $_POST['payment_method'] ?? 'cod';
            $note = trim($_POST['note'] ?? '');

            $loyaltyModel = new Loyalty($store);
            $userRank = ($loyaltyModel->getByUserId((int)$_SESSION['user']['id']))['level'] ?? 'new';
            $couponModel = new Coupon($store);
            $coupons = $couponModel->getAll();
            $discount = 0;
            if (isset($_SESSION['cart_coupon'])) {
                $appliedCoupon = find_coupon_by_code($coupons, (string)$_SESSION['cart_coupon']);
                if ($appliedCoupon) {
                    $discount = calculate_coupon_discount($appliedCoupon, $subtotal + $toppingTotal, $userRank);
                }
            }

            $shippingFee = 15000;
            $orderItems = [];
            foreach ($cartData as $row) {
                $item = $row['cart_item'];
                $toppings = $row['toppings'];
                $toppingCost = (int)$row['topping_cost'];
                $unitPrice = (int)$row['item_price'] + $toppingCost;
                $quantity = (int)($item['quantity'] ?? 0);
                $orderItems[] = [
                    'product_id' => (int)($item['product_id'] ?? 0),
                    'product_name' => $item['product_name'] ?? '',
                    'image' => $item['image'] ?? '',
                    'size_name' => $item['size'] ?? ($item['size_name'] ?? ''),
                    'size_id' => (int)($item['size_id'] ?? 0),
                    'product_size_id' => (int)($item['product_size_id'] ?? 0),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $quantity,
                    'ice_level' => (int)($item['ice_level'] ?? 100),
                    'sugar_level' => (int)($item['sugar_level'] ?? 100),
                    'toppings' => array_map(function ($t) {
                        return [
                            'topping_name' => $t['name'],
                            'price' => (int)$t['price']
                        ];
                    }, $toppings)
                ];
            }

            $orderSubtotal = $subtotal + $toppingTotal;
            $total = max(0, $orderSubtotal + $shippingFee - $discount);
            $paymentStatus = 'pending';

            if ($paymentMethod === 'wallet') {
                $walletModel = new Wallet($store);
                if (!$walletModel->debit((int)$_SESSION['user']['id'], $total, 'Thanh toán đơn hàng')) {
                    set_flash('errors', ['Số dư ví không đủ để thanh toán.']);
                    redirect('checkout');
                }
                $paymentStatus = 'paid';
            }

            $orderModel = new Order($store);
            $order = $orderModel->createOrder([
                'user_id' => (int)$_SESSION['user']['id'],
                'user_name' => $_SESSION['user']['name'],
                'status' => $paymentStatus === 'paid' ? 'processing' : 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'receiver_name' => $address['receiver_name'],
                'address_phone' => $address['phone'],
                'address_detail' => $address['detail'],
                'ward' => $address['ward'],
                'district' => $address['district'],
                'province' => $address['province'],
                'note' => $note,
                'items' => $orderItems,
                'subtotal' => $orderSubtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total' => $total
            ]);

            if (!empty($selectedIds)) {
                foreach ($selectedIds as $id) {
                    $cartModel->removeItem((int)$_SESSION['user']['id'], (int)$id);
                }
            } else {
                $cartModel->clear((int)$_SESSION['user']['id']);
            }

            unset($_SESSION['cart_coupon'], $_SESSION['selected_items']);
            set_flash('success', 'Đặt hàng thành công.');
            redirect('order-success', ['id' => $order['id']]);
        }
        redirect('checkout');
        break;

    case 'orders':
        require_login();
        $orderModel = new Order($store);
        $orders = $orderModel->getByUserId((int)$_SESSION['user']['id']);
        foreach ($orders as &$order) {
            $order['payment_method'] = $order['payment_method'] ?? 'cod';
            $order['payment_status'] = $order['payment_status'] ?? 'pending';
        }
        render('orders/index.php', ['orders' => $orders]);
        break;

    case 'order-detail':
        require_login();
        $orderModel = new Order($store);
        $orderId = (int)($_GET['id'] ?? ($_GET['order_id'] ?? 0));
        $order = $orderModel->findById($orderId);
        if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
            set_flash('errors', ['Order not found.']);
            redirect('orders');
        }
        $order = array_merge([
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'receiver_name' => $_SESSION['user']['name'] ?? '',
            'address_phone' => $_SESSION['user']['phone'] ?? '',
            'address_detail' => '',
            'ward' => '',
            'district' => '',
            'province' => '',
            'note' => ''
        ], $order);
        $orderItems = $order['items'] ?? [];
        render('orders/detail.php', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
        break;

    case 'order-success':
        require_login();
        $orderId = (int)($_GET['id'] ?? 0);
        $orderModel = new Order($store);
        $order = $orderModel->findById($orderId);
        if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
            redirect('orders');
        }
        $order['payment_method'] = $order['payment_method'] ?? 'cod';
        render('orders/success.php', ['order' => $order]);
        break;

    case 'order-change-payment':
        require_login();
        $orderId = (int)($_GET['id'] ?? 0);
        $orderModel = new Order($store);
        $order = $orderModel->findById($orderId);
        if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
            set_flash('errors', ['Order not found.']);
            redirect('orders');
        }
        $order['payment_method'] = $order['payment_method'] ?? 'cod';
        $order['payment_status'] = $order['payment_status'] ?? 'pending';
        $walletModel = new Wallet($store);
        $walletBalance = $walletModel->getByUserId((int)$_SESSION['user']['id'])['balance'] ?? 0;
        render('orders/change-payment.php', [
            'order' => $order,
            'walletBalance' => $walletBalance
        ]);
        break;

    case 'order-update-payment':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $paymentMethod = $_POST['payment_method'] ?? 'cod';
            $orderModel = new Order($store);
            $order = $orderModel->findById($orderId);
            if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
                set_flash('errors', ['Order not found.']);
                redirect('orders');
            }
            $payload = [
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending'
            ];
            if ($paymentMethod === 'wallet') {
                $walletModel = new Wallet($store);
                if (!$walletModel->debit((int)$_SESSION['user']['id'], (int)$order['total'], 'Thanh toán đơn hàng')) {
                    set_flash('errors', ['Số dư ví không đủ để thanh toán.']);
                    redirect('order-change-payment', ['id' => $orderId]);
                }
                $payload['payment_status'] = 'paid';
                $payload['status'] = 'processing';
            }
            $orderModel->update($orderId, $payload);
            set_flash('success', 'Đã cập nhật phương thức thanh toán.');
            redirect('order-detail', ['id' => $orderId]);
        }
        redirect('orders');
        break;

    case 'order-cancel':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $orderModel = new Order($store);
            $order = $orderModel->findById($orderId);
            if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
                set_flash('errors', ['Order not found.']);
                redirect('orders');
            }
            if (($order['payment_status'] ?? 'pending') === 'paid' && ($order['payment_method'] ?? '') === 'wallet') {
                $walletModel = new Wallet($store);
                $walletModel->refund((int)$_SESSION['user']['id'], (int)$order['total'], 'Hoàn tiền đơn hàng #' . $orderId);
            }
            $orderModel->cancel($orderId, 'Khách hàng hủy đơn');
            set_flash('success', 'Đã hủy đơn hàng.');
            redirect('orders');
        }
        redirect('orders');
        break;

    case 'order-confirm-received':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $orderModel = new Order($store);
            $order = $orderModel->findById($orderId);
            if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
                set_flash('errors', ['Order not found.']);
                redirect('orders');
            }
            $orderModel->updateStatus($orderId, 'completed');
            set_flash('success', 'Cảm ơn bạn đã xác nhận nhận hàng.');
            redirect('order-detail', ['id' => $orderId]);
        }
        redirect('orders');
        break;

    case 'order-reorder':
        require_login();
        $orderId = (int)($_GET['order_id'] ?? 0);
        $orderModel = new Order($store);
        $order = $orderModel->findById($orderId);
        if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
            set_flash('errors', ['Order not found.']);
            redirect('orders');
        }
        $cartModel = new Cart($store);
        $productModel = new Product($store);
        $toppingModel = new Topping($store);
        $toppings = $toppingModel->getAll();
        $toppingMap = [];
        foreach ($toppings as $topping) {
            $toppingMap[strtolower($topping['name'])] = $topping;
        }
        foreach ($order['items'] ?? [] as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $productSizes = $productModel->getSizes($productId);
            $sizeName = $item['size_name'] ?? '';
            $productSizeId = 0;
            $sizeOptionId = 0;
            foreach ($productSizes as $size) {
                if (strcasecmp($size['size_name'], $sizeName) === 0) {
                    $productSizeId = (int)$size['id'];
                    $sizeOptionId = (int)$size['size_id'];
                    break;
                }
            }
            if ($productSizeId === 0 && !empty($productSizes)) {
                $productSizeId = (int)$productSizes[0]['id'];
                $sizeOptionId = (int)$productSizes[0]['size_id'];
                $sizeName = $productSizes[0]['size_name'];
            }

            $selectedToppings = [];
            foreach ($item['toppings'] ?? [] as $top) {
                $name = strtolower($top['topping_name'] ?? '');
                if ($name && isset($toppingMap[$name])) {
                    $selectedToppings[] = [
                        'id' => (int)$toppingMap[$name]['id'],
                        'name' => $toppingMap[$name]['name'],
                        'topping_name' => $toppingMap[$name]['name'],
                        'price' => (int)$toppingMap[$name]['price']
                    ];
                }
            }

            $cartModel->addItem((int)$_SESSION['user']['id'], [
                'product_id' => $productId,
                'product_name' => $item['product_name'] ?? '',
                'image' => $item['image'] ?? 'placeholder.svg',
                'product_size_id' => $productSizeId,
                'size_id' => $sizeOptionId,
                'size_name' => $sizeName,
                'size' => $sizeName,
                'quantity' => (int)($item['quantity'] ?? 1),
                'unit_price' => (int)($item['unit_price'] ?? 0),
                'ice_level' => (int)($item['ice_level'] ?? 100),
                'sugar_level' => (int)($item['sugar_level'] ?? 100),
                'note' => '',
                'toppings' => $selectedToppings
            ]);
        }
        set_flash('success', 'Đã thêm sản phẩm vào giỏ hàng.');
        redirect('cart');
        break;

    case 'order-review':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $productId = (int)($_POST['product_id'] ?? 0);
            $rating = (int)($_POST['rating'] ?? 5);
            $comment = trim($_POST['comment'] ?? '');
            $orderModel = new Order($store);
            $order = $orderModel->findById($orderId);
            if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
                set_flash('errors', ['Order not found.']);
                redirect('orders');
            }
            $reviewModel = new Review($store);
            if ($reviewModel->hasUserReviewed((int)$_SESSION['user']['id'], $productId, $orderId)) {
                set_flash('errors', ['Bạn đã đánh giá sản phẩm này.']);
                redirect('order-detail', ['id' => $orderId]);
            }
            $productModel = new Product($store);
            $product = $productModel->findById($productId);
            $reviewModel->createReview([
                'product_id' => $productId,
                'product_name' => $product['name'] ?? '',
                'user_id' => (int)$_SESSION['user']['id'],
                'user_name' => $_SESSION['user']['name'],
                'user_email' => $_SESSION['user']['email'],
                'user_avatar' => $_SESSION['user']['avatar'] ?? '',
                'order_id' => $orderId,
                'rating' => max(1, min(5, $rating)),
                'comment' => $comment,
                'status' => 1
            ]);
            set_flash('success', 'Cảm ơn bạn đã đánh giá.');
            redirect('order-detail', ['id' => $orderId]);
        }
        redirect('orders');
        break;

    case 'profile':
        require_login();
        $userModel = new User($store);
        $user = $userModel->findById((int)$_SESSION['user']['id']);
        $addressModel = new Address($store);
        $addresses = $addressModel->getByUserId((int)$_SESSION['user']['id']);
        render('profile/index.php', [
            'user' => $user,
            'addresses' => $addresses
        ]);
        break;

    case 'profile-edit':
        require_login();
        $userModel = new User($store);
        $user = $userModel->findById((int)$_SESSION['user']['id']);
        render('profile/edit.php', ['user' => $user]);
        break;

    case 'profile-update':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $errors = [];
            if ($name === '') {
                $errors['name'] = 'Vui lòng nhập họ tên.';
            }
            if ($phone === '') {
                $errors['phone'] = 'Vui lòng nhập số điện thoại.';
            }
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                redirect('profile-edit');
            }
            $userModel = new User($store);
            $updated = $userModel->updateUser((int)$_SESSION['user']['id'], [
                'name' => $name,
                'phone' => $phone
            ]);
            if ($updated) {
                $_SESSION['user'] = $updated;
            }
            set_flash('success', 'Cập nhật hồ sơ thành công.');
            redirect('profile');
        }
        redirect('profile-edit');
        break;

    case 'update-avatar':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileName = handle_upload('avatar', BASE_PATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
            if (!$fileName) {
                $_SESSION['errors']['avatar'] = 'Không thể tải ảnh lên.';
                redirect('profile');
            }
            $avatarPath = 'assets/uploads/' . $fileName;
            $userModel = new User($store);
            $updated = $userModel->updateUser((int)$_SESSION['user']['id'], ['avatar' => $avatarPath]);
            if ($updated) {
                $_SESSION['user'] = $updated;
            }
            set_flash('success', 'Cập nhật ảnh đại diện thành công.');
            redirect('profile');
        }
        redirect('profile');
        break;

    case 'change-password':
        require_login();
        render('profile/change-password.php');
        break;

    case 'update-password':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $errors = [];
            $userModel = new User($store);
            $user = $userModel->findById((int)$_SESSION['user']['id']);
            if (!$user || !password_verify($current, $user['password'])) {
                $errors['current_password'] = 'Mật khẩu hiện tại không đúng.';
            }
            if ($newPassword === '') {
                $errors['new_password'] = 'Vui lòng nhập mật khẩu mới.';
            } elseif (strlen($newPassword) < 6) {
                $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            }
            if ($confirm !== $newPassword) {
                $errors['confirm_password'] = 'Xác nhận mật khẩu không khớp.';
            }
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                redirect('change-password');
            }
            $updated = $userModel->updateUser((int)$_SESSION['user']['id'], [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]);
            if ($updated) {
                $_SESSION['user'] = $updated;
            }
            set_flash('success', 'Đổi mật khẩu thành công.');
            redirect('profile');
        }
        redirect('change-password');
        break;

    case 'wallet':
        require_login();
        $walletModel = new Wallet($store);
        $wallet = $walletModel->getByUserId((int)$_SESSION['user']['id']);
        $transactions = $walletModel->getTransactions((int)$_SESSION['user']['id']);
        render('wallet/index.php', [
            'wallet' => $wallet,
            'transactions' => $transactions
        ]);
        break;

    case 'wallet-deposit':
        require_login();
        $walletModel = new Wallet($store);
        $wallet = $walletModel->getByUserId((int)$_SESSION['user']['id']);
        render('wallet/deposit.php', ['wallet' => $wallet]);
        break;

    case 'wallet-process-deposit':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = (int)($_POST['amount'] ?? 0);
            $errors = [];
            if ($amount < 10000) {
                $errors[] = 'Số tiền nạp tối thiểu là 10.000đ.';
            }
            if ($amount > 50000000) {
                $errors[] = 'Số tiền nạp tối đa là 50.000.000đ.';
            }
            if (!empty($errors)) {
                set_flash('errors', $errors);
                redirect('wallet-deposit');
            }
            $walletModel = new Wallet($store);
            $walletModel->deposit((int)$_SESSION['user']['id'], $amount, 'Nạp tiền ví');
            set_flash('success', 'Nạp tiền thành công.');
            redirect('wallet');
        }
        redirect('wallet-deposit');
        break;

    case 'login':
        render('auth/login.php');
        break;

    case 'post-login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $errors = [];
            if ($email === '') {
                $errors['email'] = 'Vui lòng nhập email.';
            }
            if ($password === '') {
                $errors['password'] = 'Vui lòng nhập mật khẩu.';
            }
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = ['email' => $email];
                redirect('login');
            }
            $userModel = new User($store);
            $user = $userModel->findByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                if (!(int)$user['is_active']) {
                    $_SESSION['errors'] = ['login' => 'Tài khoản đã bị khóa.'];
                    redirect('login');
                }
                $_SESSION['user'] = $user;
                set_flash('success', 'Đăng nhập thành công.');
                redirect('home');
            }
            $_SESSION['errors'] = ['login' => 'Email hoặc mật khẩu không đúng.'];
            $_SESSION['old'] = ['email' => $email];
            redirect('login');
        }
        redirect('login');
        break;

    case 'register':
        render('auth/register.php');
        break;

    case 'post-register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm = trim($_POST['confirm_password'] ?? '');
            $errors = [];
            if ($name === '') {
                $errors['name'] = 'Vui lòng nhập họ tên.';
            }
            if ($email === '') {
                $errors['email'] = 'Vui lòng nhập email.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ.';
            }
            if ($phone === '') {
                $errors['phone'] = 'Vui lòng nhập số điện thoại.';
            }
            if ($password === '') {
                $errors['password'] = 'Vui lòng nhập mật khẩu.';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            }
            if ($confirm !== $password) {
                $errors['confirm_password'] = 'Xác nhận mật khẩu không khớp.';
            }
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone
                ];
                redirect('register');
            }
            $userModel = new User($store);
            $user = $userModel->createUser([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role_id' => 1,
                'is_active' => 1,
                'avatar' => '',
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
            if ($user) {
                $_SESSION['user'] = $user;
                set_flash('success', 'Đăng ký thành công.');
                redirect('home');
            }
            $_SESSION['errors'] = ['register' => 'Email đã tồn tại.'];
            $_SESSION['old'] = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ];
            redirect('register');
        }
        redirect('register');
        break;

    case 'logout':
        session_destroy();
        session_start();
        set_flash('success', 'Logged out.');
        redirect('home');
        break;
    case 'notifications':
        require_login();
        $notificationModel = new Notification($store);
        $notifications = $notificationModel->getByUserId((int)$_SESSION['user']['id']);
        render('notifications/index.php', ['notifications' => $notifications]);
        break;

    case 'notification-read':
        require_login();
        $notificationModel = new Notification($store);
        $id = (int)($_GET['id'] ?? 0);
        $notificationModel->markRead($id);
        $redirect = $_GET['redirect'] ?? '';
        if ($redirect === 'order-detail') {
            $orderId = (int)($_GET['order_id'] ?? 0);
            redirect('order-detail', ['order_id' => $orderId]);
        }
        redirect('notifications');
        break;

    case 'notifications-read-all':
        require_login();
        $notificationModel = new Notification($store);
        $notificationModel->markAllRead((int)$_SESSION['user']['id']);
        set_flash('success', 'All notifications marked as read.');
        redirect('notifications');
        break;

    case 'notifications-read-all-ajax':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notificationModel = new Notification($store);
            $notificationModel->markAllRead((int)$_SESSION['user']['id']);
            http_response_code(200);
            exit;
        }
        http_response_code(405);
        exit;

    case 'loyalty':
        require_login();
        $loyaltyModel = new Loyalty($store);
        $data = $loyaltyModel->getByUserId((int)$_SESSION['user']['id']);
        render('loyalty/index.php', [
            'loyaltyPoints' => $data,
            'userRewards' => $data['rewards'] ?? [],
            'transactions' => $data['transactions'] ?? []
        ]);
        break;

    case 'loyalty-rewards':
        require_login();
        $loyaltyModel = new Loyalty($store);
        $couponModel = new Coupon($store);
        $loyaltyPoints = $loyaltyModel->getByUserId((int)$_SESSION['user']['id']);
        $redeemableCoupons = array_values(array_filter($couponModel->getAll(), function ($coupon) {
            return (int)$coupon['is_redeemable'] === 1 && (int)$coupon['status'] === 1;
        }));
        render('loyalty/rewards.php', [
            'loyaltyPoints' => $loyaltyPoints,
            'redeemableCoupons' => $redeemableCoupons
        ]);
        break;

    case 'loyalty-redeem':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $couponId = (int)($_POST['coupon_id'] ?? 0);
            $couponModel = new Coupon($store);
            $loyaltyModel = new Loyalty($store);
            $coupon = $couponModel->findById($couponId);
            $loyalty = $loyaltyModel->getByUserId((int)$_SESSION['user']['id']);
            $errors = [];
            if (!$coupon) {
                $errors[] = 'Coupon not found.';
            } elseif ($couponModel->hasUserRedeemed((int)$_SESSION['user']['id'], $couponId)) {
                $errors[] = 'Already redeemed.';
            } elseif ($loyalty['total_points'] < (int)$coupon['point_cost']) {
                $errors[] = 'Not enough points.';
            }
            if (!empty($errors)) {
                set_flash('errors', $errors);
                redirect('loyalty-rewards');
            }
            if (!$couponModel->redeem((int)$_SESSION['user']['id'], $couponId)) {
                set_flash('errors', ['Cannot redeem this coupon.']);
                redirect('loyalty-rewards');
            }
            $loyaltyModel->addTransaction((int)$_SESSION['user']['id'], 'redeem', (int)$coupon['point_cost'], 'Redeem coupon ' . $coupon['code']);
            $loyaltyModel->addReward((int)$_SESSION['user']['id'], [
                'reward_name' => 'Coupon ' . $coupon['code'],
                'code' => $coupon['code'],
                'is_used' => 0,
                'expires_at' => $coupon['expires_at'] ?? ''
            ]);
            set_flash('success', 'Redeemed successfully.');
            redirect('loyalty');
        }
        redirect('loyalty-rewards');
        break;

    case 'address':
        require_login();
        $addressModel = new Address($store);
        $addresses = $addressModel->getByUserId((int)$_SESSION['user']['id']);
        render('address/index.php', ['addresses' => $addresses]);
        break;

    case 'address-create':
        require_login();
        render('address/create.php');
        break;

    case 'address-store':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = [
                'user_id' => (int)$_SESSION['user']['id'],
                'label' => trim($_POST['label'] ?? 'Home'),
                'receiver_name' => trim($_POST['receiver_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'province' => trim($_POST['province'] ?? ''),
                'district' => trim($_POST['district'] ?? ''),
                'ward' => trim($_POST['ward'] ?? ''),
                'detail' => trim($_POST['detail'] ?? ''),
                'is_default' => isset($_POST['is_default']) ? 1 : 0
            ];
            $errors = [];
            if ($payload['receiver_name'] === '') {
                $errors['receiver_name'] = 'Required.';
            }
            if ($payload['phone'] === '') {
                $errors['phone'] = 'Required.';
            }
            if ($payload['province'] === '') {
                $errors['province'] = 'Required.';
            }
            if ($payload['district'] === '') {
                $errors['district'] = 'Required.';
            }
            if ($payload['ward'] === '') {
                $errors['ward'] = 'Required.';
            }
            if ($payload['detail'] === '') {
                $errors['detail'] = 'Required.';
            }
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                redirect('address-create');
            }
            $addressModel = new Address($store);
            $addresses = $addressModel->getByUserId((int)$_SESSION['user']['id']);
            if (empty($addresses)) {
                $payload['is_default'] = 1;
            }
            $created = $addressModel->createAddress($payload);
            if ($payload['is_default']) {
                $addressModel->setDefault((int)$_SESSION['user']['id'], (int)$created['id']);
            }
            set_flash('success', 'Address created.');
            redirect('address');
        }
        redirect('address');
        break;

    case 'address-edit':
        require_login();
        $addressModel = new Address($store);
        $id = (int)($_GET['id'] ?? 0);
        $address = $addressModel->findById($id);
        if (!$address || (int)$address['user_id'] !== (int)$_SESSION['user']['id']) {
            set_flash('errors', ['Address not found.']);
            redirect('address');
        }
        render('address/edit.php', ['address' => $address]);
        break;

    case 'address-update':
        require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $addressModel = new Address($store);
            $address = $addressModel->findById($id);
            if (!$address || (int)$address['user_id'] !== (int)$_SESSION['user']['id']) {
                set_flash('errors', ['Address not found.']);
                redirect('address');
            }
            $payload = [
                'label' => trim($_POST['label'] ?? 'Home'),
                'receiver_name' => trim($_POST['receiver_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'province' => trim($_POST['province'] ?? ''),
                'district' => trim($_POST['district'] ?? ''),
                'ward' => trim($_POST['ward'] ?? ''),
                'detail' => trim($_POST['detail'] ?? ''),
                'is_default' => isset($_POST['is_default']) ? 1 : 0
            ];
            $addressModel->updateAddress($id, $payload);
            if ($payload['is_default']) {
                $addressModel->setDefault((int)$_SESSION['user']['id'], $id);
            }
            set_flash('success', 'Address updated.');
            redirect('address');
        }
        redirect('address');
        break;

    case 'address-set-default':
        require_login();
        $addressModel = new Address($store);
        $id = (int)($_GET['id'] ?? 0);
        $addressModel->setDefault((int)$_SESSION['user']['id'], $id);
        set_flash('success', 'Default address updated.');
        redirect('address');
        break;

    case 'address-delete':
        require_login();
        $addressModel = new Address($store);
        $id = (int)($_GET['id'] ?? 0);
        $addressModel->deleteAddress($id);
        set_flash('success', 'Address deleted.');
        redirect('address');
        break;
    case 'admin':
        require_admin();
        $orderModel = new Order($store);
        $orders = $orderModel->getAll();
        $totalOrders = count($orders);
        $totalRevenue = 0;
        foreach ($orders as $order) {
            $totalRevenue += (int)$order['total'];
        }
        $userModel = new User($store);
        $totalUsers = count($userModel->getAll());
        $recentOrders = array_slice($orders, 0, 5);
        render('admin/index.php', [
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalUsers' => $totalUsers,
            'recentOrders' => $recentOrders
        ]);
        break;

    case 'admin-categories':
        require_admin();
        $categoryModel = new Category($store);
        $categories = $categoryModel->getAll();
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 8;
        [$paged, $totalPages, $page, $totalCategories] = paginate($categories, $page, $perPage);
        render('admin/categories.php', [
            'categories' => $paged,
            'totalPages' => $totalPages,
            'page' => $page,
            'perPage' => $perPage,
            'totalCategories' => $totalCategories
        ]);
        break;

    case 'admin-category-create':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $errors = [];
            if ($name === '') {
                $errors[] = 'Name is required.';
            }
            if (empty($slug)) {
                $slug = slugify($name);
            }
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                redirect('admin-categories');
            }
            $categoryModel = new Category($store);
            $categoryModel->createCategory($name, $slug);
            set_flash('success', 'Category created.');
            redirect('admin-categories');
        }
        redirect('admin-categories');
        break;

    case 'admin-category-update':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = slugify($name);
            }
            $categoryModel = new Category($store);
            $categoryModel->updateCategory($id, $name, $slug);
            set_flash('success', 'Category updated.');
            redirect('admin-categories');
        }
        redirect('admin-categories');
        break;

    case 'admin-category-delete-multiple':
        require_admin();
        $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        $categoryModel = new Category($store);
        $categoryModel->deleteMany($ids);
        set_flash('success', 'Categories deleted.');
        redirect('admin-categories');
        break;
    case 'admin-products':
        require_admin();
        $productModel = new Product($store);
        $products = $productModel->getAll();
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 8;
        [$paged, $totalPages, $page, $totalProducts] = paginate($products, $page, $perPage);
        render('admin/products.php', [
            'products' => $paged,
            'totalPages' => $totalPages,
            'page' => $page,
            'perPage' => $perPage,
            'totalProducts' => $totalProducts
        ]);
        break;

    case 'admin-product-create':
        require_admin();
        $categoryModel = new Category($store);
        $productModel = new Product($store);
        $toppingModel = new Topping($store);
        $categories = $categoryModel->getAll();
        $allSizes = $productModel->getAllSizes();
        $allToppings = $toppingModel->getAll();
        render('admin/product-create.php', [
            'categories' => $categories,
            'allSizes' => $allSizes,
            'allToppings' => $allToppings
        ]);
        break;

    case 'admin-product-store':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $name = trim($_POST['name'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $status = isset($_POST['status']) ? 1 : 0;
            $sizesInput = $_POST['sizes'] ?? [];
            $pricesInput = $_POST['prices'] ?? [];
            if ($name === '') {
                $errors['name'] = 'Name required.';
            }
            if ($categoryId <= 0) {
                $errors['category'] = 'Category required.';
            }
            if (empty($sizesInput)) {
                $errors['sizes'] = 'Select at least one size.';
            }

            $sizes = [];
            foreach ($sizesInput as $sizeId) {
                $price = (int)($pricesInput[$sizeId] ?? 0);
                if ($price <= 0) {
                    $errors['sizes'] = 'Price required for selected sizes.';
                    break;
                }
                $sizes[] = ['size_id' => (int)$sizeId, 'price' => $price];
            }

            $imageName = handle_upload('image', BASE_PATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
            if (!$imageName) {
                $imageName = 'placeholder.svg';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                redirect('admin-product-create');
            }

            $productModel = new Product($store);
            $productModel->createProduct([
                'name' => $name,
                'slug' => slugify($name),
                'category_id' => $categoryId,
                'description' => $description,
                'image' => $imageName,
                'status' => $status,
                'sizes' => $sizes,
                'topping_ids' => array_map('intval', $_POST['toppings'] ?? [])
            ]);
            set_flash('success', 'Product created.');
            redirect('admin-products');
        }
        redirect('admin-product-create');
        break;

    case 'admin-product-edit':
        require_admin();
        $productModel = new Product($store);
        $id = (int)($_GET['id'] ?? 0);
        $product = $productModel->findById($id);
        if (!$product) {
            set_flash('errors', ['Product not found.']);
            redirect('admin-products');
        }
        $categories = (new Category($store))->getAll();
        $sizes = $productModel->getSizes($id);
        $toppings = $productModel->getToppings($id);
        render('admin/product-edit.php', [
            'product' => $product,
            'categories' => $categories,
            'sizes' => $sizes,
            'toppings' => $toppings,
            'productModel' => $productModel
        ]);
        break;

    case 'admin-product-update':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $productModel = new Product($store);
            $product = $productModel->findById($id);
            if (!$product) {
                set_flash('errors', ['Product not found.']);
                redirect('admin-products');
            }
            $name = trim($_POST['name'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $status = isset($_POST['status']) ? 1 : 0;
            $sizesInput = $_POST['sizes'] ?? [];
            $pricesInput = $_POST['prices'] ?? [];
            $productSizeIds = $_POST['product_size_ids'] ?? [];

            $sizes = [];
            foreach ($sizesInput as $sizeId) {
                $price = (int)($pricesInput[$sizeId] ?? 0);
                if ($price <= 0) {
                    continue;
                }
                $sizes[] = [
                    'id' => (int)($productSizeIds[$sizeId] ?? 0),
                    'size_id' => (int)$sizeId,
                    'price' => $price
                ];
            }

            $imageName = handle_upload('image', BASE_PATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
            if (!$imageName) {
                $imageName = $_POST['current_image'] ?? $product['image'];
            }

            $productModel->updateProduct($id, [
                'name' => $name,
                'slug' => slugify($name),
                'category_id' => $categoryId,
                'description' => $description,
                'image' => $imageName,
                'status' => $status,
                'sizes' => $sizes,
                'topping_ids' => array_map('intval', $_POST['toppings'] ?? [])
            ]);
            set_flash('success', 'Product updated.');
            redirect('admin-products');
        }
        redirect('admin-products');
        break;

    case 'admin-product-delete-multiple':
        require_admin();
        $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        $productModel = new Product($store);
        $productModel->deleteMany($ids);
        set_flash('success', 'Products deleted.');
        redirect('admin-products');
        break;
    case 'admin-toppings':
        require_admin();
        $toppingModel = new Topping($store);
        $toppings = $toppingModel->getAll();
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 8;
        [$paged, $totalPages, $page, $totalToppings] = paginate($toppings, $page, $perPage);
        render('admin/toppings.php', [
            'toppings' => $paged,
            'totalPages' => $totalPages,
            'page' => $page,
            'perPage' => $perPage,
            'totalToppings' => $totalToppings
        ]);
        break;

    case 'admin-topping-create':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $price = (int)($_POST['price'] ?? 0);
            $status = isset($_POST['status']) ? 1 : 0;
            if ($name === '') {
                $_SESSION['errors'] = ['Name required.'];
                redirect('admin-toppings');
            }
            $toppingModel = new Topping($store);
            $toppingModel->createTopping($name, $price, $status);
            set_flash('success', 'Topping created.');
            redirect('admin-toppings');
        }
        redirect('admin-toppings');
        break;

    case 'admin-topping-update':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $price = (int)($_POST['price'] ?? 0);
            $status = isset($_POST['status']) ? 1 : 0;
            $toppingModel = new Topping($store);
            $toppingModel->updateTopping($id, $name, $price, $status);
            set_flash('success', 'Topping updated.');
            redirect('admin-toppings');
        }
        redirect('admin-toppings');
        break;

    case 'admin-topping-delete-multiple':
        require_admin();
        $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        $toppingModel = new Topping($store);
        $toppingModel->deleteMany($ids);
        set_flash('success', 'Toppings deleted.');
        redirect('admin-toppings');
        break;

    case 'admin-coupons':
        require_admin();
        $couponModel = new Coupon($store);
        $coupons = $couponModel->getAll();
        render('admin/coupons.php', ['coupons' => $coupons]);
        break;

    case 'admin-coupon-create':
        require_admin();
        render('admin/coupon-create.php');
        break;

    case 'admin-coupon-edit':
        require_admin();
        $couponModel = new Coupon($store);
        $id = (int)($_GET['id'] ?? 0);
        $coupon = $couponModel->findById($id);
        if (!$coupon) {
            set_flash('errors', ['Coupon not found.']);
            redirect('admin-coupons');
        }
        render('admin/coupon-edit.php', ['coupon' => $coupon]);
        break;

    case 'admin-coupon-store':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = [
                'code' => strtoupper(trim($_POST['code'] ?? '')),
                'type' => $_POST['type'] ?? 'fixed',
                'value' => (int)($_POST['value'] ?? 0),
                'max_discount' => (int)($_POST['max_discount'] ?? 0),
                'min_order' => (int)($_POST['min_order'] ?? 0),
                'usage_limit' => (int)($_POST['usage_limit'] ?? 0),
                'used_count' => 0,
                'required_rank' => $_POST['required_rank'] ?? '',
                'point_cost' => (int)($_POST['point_cost'] ?? 0),
                'is_redeemable' => isset($_POST['is_redeemable']) ? 1 : 0,
                'status' => isset($_POST['status']) ? 1 : 0,
                'description' => trim($_POST['description'] ?? ''),
                'starts_at' => $_POST['starts_at'] ?? '',
                'expires_at' => $_POST['expires_at'] ?? '',
                'max_redemptions' => (int)($_POST['usage_limit'] ?? 0),
                'redemption_count' => 0
            ];
            $couponModel = new Coupon($store);
            $couponModel->createCoupon($payload);
            set_flash('success', 'Coupon created.');
            redirect('admin-coupons');
        }
        redirect('admin-coupon-create');
        break;

    case 'admin-coupon-update':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $payload = [
                'code' => strtoupper(trim($_POST['code'] ?? '')),
                'type' => $_POST['type'] ?? 'fixed',
                'value' => (int)($_POST['value'] ?? 0),
                'max_discount' => (int)($_POST['max_discount'] ?? 0),
                'min_order' => (int)($_POST['min_order'] ?? 0),
                'usage_limit' => (int)($_POST['usage_limit'] ?? 0),
                'required_rank' => $_POST['required_rank'] ?? '',
                'point_cost' => (int)($_POST['point_cost'] ?? 0),
                'is_redeemable' => isset($_POST['is_redeemable']) ? 1 : 0,
                'status' => isset($_POST['status']) ? 1 : 0,
                'description' => trim($_POST['description'] ?? ''),
                'starts_at' => $_POST['starts_at'] ?? '',
                'expires_at' => $_POST['expires_at'] ?? '',
                'max_redemptions' => (int)($_POST['usage_limit'] ?? 0)
            ];
            $couponModel = new Coupon($store);
            $couponModel->updateCoupon($id, $payload);
            set_flash('success', 'Coupon updated.');
            redirect('admin-coupons');
        }
        redirect('admin-coupons');
        break;

    case 'admin-coupon-delete':
        require_admin();
        $couponModel = new Coupon($store);
        $id = (int)($_GET['id'] ?? 0);
        $couponModel->delete($id);
        set_flash('success', 'Coupon deleted.');
        redirect('admin-coupons');
        break;

    case 'admin-coupon-delete-multiple':
        require_admin();
        $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        $couponModel = new Coupon($store);
        $couponModel->deleteMany($ids);
        set_flash('success', 'Coupons deleted.');
        redirect('admin-coupons');
        break;
    case 'admin-orders':
        require_admin();
        $status = $_GET['status'] ?? null;
        $orderModel = new Order($store);
        $orders = $orderModel->getAll($status);
        render('admin/orders.php', ['orders' => $orders]);
        break;

    case 'admin-order-update':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $status = $_POST['status'] ?? 'pending';
            $orderModel = new Order($store);
            $orderModel->updateStatus($orderId, $status);
            set_flash('success', 'Order updated.');
            redirect('admin-orders');
        }
        redirect('admin-orders');
        break;

    case 'admin-order-cancel':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $reason = trim($_POST['cancel_reason'] ?? '');
            $orderModel = new Order($store);
            $orderModel->cancel($orderId, $reason);
            set_flash('success', 'Order cancelled.');
            redirect('admin-orders');
        }
        redirect('admin-orders');
        break;

    case 'admin-users':
        require_admin();
        $userModel = new User($store);
        $users = $userModel->getAll();
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 8;
        [$paged, $totalPages, $page, $totalUsers] = paginate($users, $page, $perPage);
        render('admin/users.php', [
            'users' => $paged,
            'totalPages' => $totalPages,
            'page' => $page,
            'perPage' => $perPage,
            'totalUsers' => $totalUsers
        ]);
        break;

    case 'admin-user-update-role':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $roleId = (int)($_POST['role_id'] ?? 1);
            $userModel = new User($store);
            $userModel->updateRole($userId, $roleId);
            set_flash('success', 'Role updated.');
            redirect('admin-users');
        }
        redirect('admin-users');
        break;

    case 'admin-users-lock-multiple':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = $_POST['user_ids'] ?? [];
            $userModel = new User($store);
            $userModel->lockUsers($ids);
            set_flash('success', 'Users locked.');
            redirect('admin-users');
        }
        redirect('admin-users');
        break;

    case 'admin-users-unlock-multiple':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = $_POST['user_ids'] ?? [];
            $userModel = new User($store);
            $userModel->unlockUsers($ids);
            set_flash('success', 'Users unlocked.');
            redirect('admin-users');
        }
        redirect('admin-users');
        break;

    case 'admin-reviews':
        require_admin();
        $status = $_GET['status'] ?? '';
        $reviewModel = new Review($store);
        $reviews = $reviewModel->getAll($status);
        render('admin/reviews.php', ['reviews' => $reviews]);
        break;

    case 'admin-review-update-status':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reviewId = (int)($_POST['review_id'] ?? 0);
            $status = (int)($_POST['status'] ?? 0);
            $reviewModel = new Review($store);
            $reviewModel->updateStatus($reviewId, $status);
            set_flash('success', 'Review updated.');
            redirect('admin-reviews');
        }
        redirect('admin-reviews');
        break;

    case 'admin-settings':
        require_admin();
        $settingsModel = new Settings($store);
        $settings = $settingsModel->all();
        render('admin/settings.php', ['settings' => $settings]);
        break;

    case 'admin-settings-update':
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingsModel = new Settings($store);
            $payload = [
                'site_name' => trim($_POST['site_name'] ?? 'Chill Drink'),
                'contact_email' => trim($_POST['contact_email'] ?? ''),
                'contact_phone' => trim($_POST['contact_phone'] ?? ''),
                'site_address' => trim($_POST['site_address'] ?? '')
            ];

            $logo = handle_upload('logo', BASE_PATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
            if ($logo) {
                $payload['site_logo'] = 'assets/uploads/' . $logo;
            }

            if (isset($_FILES['banners'])) {
                $bannerFiles = $_FILES['banners'];
                $index = 1;
                for ($i = 0; $i < count($bannerFiles['name']); $i++) {
                    if ($bannerFiles['error'][$i] !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    $temp = [
                        'name' => $bannerFiles['name'][$i],
                        'type' => $bannerFiles['type'][$i],
                        'tmp_name' => $bannerFiles['tmp_name'][$i],
                        'error' => $bannerFiles['error'][$i],
                        'size' => $bannerFiles['size'][$i]
                    ];
                    $_FILES['single_banner'] = $temp;
                    $fileName = handle_upload('single_banner', BASE_PATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
                    if ($fileName) {
                        $payload['banner_' . $index] = 'assets/uploads/' . $fileName;
                        $index++;
                    }
                    if ($index > 3) {
                        break;
                    }
                }
                unset($_FILES['single_banner']);
            }

            $settingsModel->update($payload);
            set_flash('success', 'Settings saved.');
            redirect('admin-settings');
        }
        redirect('admin-settings');
        break;

    default:
        render('home/index.php');
        break;
}
