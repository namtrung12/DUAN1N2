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
        render('products.php', [
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
        render('products.php', [
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
        render('product-detail.php', [
            'product' => $product,
            'sizes' => $sizes,
            'toppings' => $toppings
        ]);
        break;
    case 'cart':
        require_login();
        $cartModel = new Cart($store);
        $cart = $cartModel->getByUserId((int)$_SESSION['user']['id']);
        render('cart.php', ['cart' => $cart]);
        break;

    case 'cart-add':
        require_login();
        $productModel = new Product($store);
        $cartModel = new Cart($store);
        $productId = (int)($_POST['product_id'] ?? 0);
        $sizeId = (int)($_POST['size_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $product = $productModel->findById($productId);
        if (!$product) {
            set_flash('errors', ['Product not found.']);
            redirect('products');
        }
        $sizes = $productModel->getSizes($productId);
        $sizeName = '';
        $unitPrice = 0;
        foreach ($sizes as $size) {
            if ((int)$size['size_id'] === $sizeId) {
                $sizeName = $size['size_name'];
                $unitPrice = (int)$size['price'];
                break;
            }
        }
        if ($sizeId === 0 || $unitPrice === 0) {
            set_flash('errors', ['Please select a size.']);
            redirect('product-detail', ['id' => $productId]);
        }
        $cartModel->addItem((int)$_SESSION['user']['id'], [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'image' => $product['image'] ?? 'placeholder.svg',
            'size_id' => $sizeId,
            'size_name' => $sizeName,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'toppings' => []
        ]);
        set_flash('success', 'Added to cart.');
        redirect('cart');
        break;

    case 'cart-update':
        require_login();
        $cartModel = new Cart($store);
        $itemId = (int)($_POST['item_id'] ?? 0);
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

    case 'checkout':
        require_login();
        $cartModel = new Cart($store);
        $orderModel = new Order($store);
        $cart = $cartModel->getByUserId((int)$_SESSION['user']['id']);
        if (empty($cart['items'])) {
            set_flash('errors', ['Cart is empty.']);
            redirect('cart');
        }
        $subtotal = 0;
        $items = [];
        foreach ($cart['items'] as $item) {
            $lineTotal = (int)$item['unit_price'] * (int)$item['quantity'];
            $subtotal += $lineTotal;
            $items[] = [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'image' => $item['image'],
                'size_name' => $item['size_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $lineTotal,
                'ice_level' => 100,
                'sugar_level' => 100,
                'toppings' => $item['toppings'] ?? []
            ];
        }
        $orderModel->createOrder([
            'user_id' => (int)$_SESSION['user']['id'],
            'user_name' => $_SESSION['user']['name'],
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping_fee' => 15000,
            'discount' => 0,
            'total' => $subtotal + 15000
        ]);
        $cartModel->clear((int)$_SESSION['user']['id']);
        set_flash('success', 'Order created successfully.');
        redirect('orders');
        break;

    case 'orders':
        require_login();
        $orderModel = new Order($store);
        $orders = $orderModel->getByUserId((int)$_SESSION['user']['id']);
        render('orders.php', ['orders' => $orders]);
        break;

    case 'order-detail':
        require_login();
        $orderModel = new Order($store);
        $orderId = (int)($_GET['order_id'] ?? 0);
        $order = $orderModel->findById($orderId);
        if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user']['id']) {
            set_flash('errors', ['Order not found.']);
            redirect('orders');
        }
        render('order-detail.php', ['order' => $order]);
        break;

    case 'profile':
        require_login();
        render('profile.php');
        break;

    case 'wallet':
        require_login();
        render('wallet.php');
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $userModel = new User($store);
            $user = $userModel->findByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                if (!(int)$user['is_active']) {
                    set_flash('errors', ['Account is locked.']);
                    redirect('login');
                }
                $_SESSION['user'] = $user;
                set_flash('success', 'Login success.');
                redirect('home');
            }
            set_flash('errors', ['Invalid credentials.']);
            redirect('login');
        }
        render('login.php');
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $errors = [];
            if ($name === '' || $email === '' || $password === '') {
                $errors[] = 'Please fill all required fields.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email.';
            }
            if (empty($errors)) {
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
                    set_flash('success', 'Register success.');
                    redirect('home');
                }
                $errors[] = 'Email already exists.';
            }
            set_flash('errors', $errors);
            redirect('register');
        }
        render('register.php');
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
                'is_default' => 0
            ];
            $errors = [];
            if ($payload['receiver_name'] === '' || $payload['phone'] === '' || $payload['province'] === '' || $payload['district'] === '' || $payload['ward'] === '' || $payload['detail'] === '') {
                $errors[] = 'Please fill all required fields.';
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
            $addressModel->createAddress($payload);
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
                'detail' => trim($_POST['detail'] ?? '')
            ];
            $addressModel->updateAddress($id, $payload);
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
