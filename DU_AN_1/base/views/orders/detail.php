<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Chi tiết đơn hàng - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body  {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#A0DDE6",
                        "background-light": "#f6f7f8",
                        "text-main": "#333333",
                        "text-secondary": "#888888",
                    },
                    fontFamily: {
                        "display": ["Poppins", "sans-serif"]
                    },
                },
            },
        }
    </script>
</head>

<body class="font-display bg-background-light text-text-main">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>
    <div class="relative flex h-auto min-h-screen w-full flex-col">
        <div class="layout-container flex h-full grow flex-col">
            <main class="flex flex-1 justify-center py-10 px-4">
                <div class="w-full max-w-4xl">
                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php unset($_SESSION['errors']);
                    endif; ?>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                            <p><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    <?php unset($_SESSION['success']);
                    endif; ?>
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
                        <h1 class="text-3xl font-bold">Chi tiết đơn hàng #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></h1>
                        <?php
                        $statusColors = [
                            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Chờ xử lý'],
                            'processing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Đang xử lý'],
                            'preparing' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Đang thực hiện'],
                            'shipped' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Đã giao ĐVVC'],
                            'delivering' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'label' => 'Đang giao'],
                            'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Hoàn thành'],
                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Đã hủy'],
                        ];
                        $status = $statusColors[$order['status']] ?? $statusColors['pending'];
                        ?>
                        <div class="flex gap-3 items-center flex-wrap">
                            <span class="px-4 py-2 <?= $status['bg'] ?> <?= $status['text'] ?> rounded-full text-sm font-semibold"><?= $status['label'] ?></span>
                            <?php 
                            $paymentStatus = $order['payment_status'] ?? 'pending';
                            if ($order['status'] === 'pending' && $paymentStatus === 'pending'): 
                            ?>
                                <a href="<?= BASE_URL ?>?action=order-change-payment&id=<?= $order['id'] ?>" class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                                    Đổi thanh toán
                                </a>
                            <?php endif; ?>
                            <?php if ($order['status'] === 'pending' || ($order['status'] === 'processing' && $order['payment_method'] === 'cod')): ?>
                                <form method="POST" action="<?= BASE_URL ?>?action=order-cancel" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>" />
                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors">Hủy đơn hàng</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($order['status'] === 'delivering'): ?>
                                <form method="POST" action="<?= BASE_URL ?>?action=order-confirm-received" onsubmit="return confirm('Xác nhận bạn đã nhận được hàng?');">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>" />
                                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors flex items-center gap-2">
                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                        Đã nhận hàng
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white rounded-lg p-6 shadow-sm">
                                <h2 class="text-xl font-bold mb-4">Sản phẩm</h2>
                                <div class="space-y-4">
                                    <?php 
                                    $reviewModel = new Review();
                                    foreach ($orderItems as $item): 
                                        $hasReviewed = $reviewModel->hasUserReviewed($_SESSION['user']['id'], $item['product_id'], $order['id']);
                                    ?>
                                        <div class="flex gap-4 pb-4 border-b last:border-b-0">
                                            <img class="w-20 h-20 object-cover rounded-lg" src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?>" />
                                            <div class="flex-1">
                                                <p class="font-semibold text-lg"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                                <p class="text-sm text-text-secondary">Size: <?= htmlspecialchars($item['size_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                                <?php if (!empty($item['toppings'])): ?>
                                                    <p class="text-sm text-text-secondary">Topping: <?php
                                                                                                    $toppingNames = array_map(function ($t) {
                                                                                                        return htmlspecialchars($t['topping_name'], ENT_QUOTES, 'UTF-8');
                                                                                                    }, $item['toppings']);
                                                                                                    echo implode(', ', $toppingNames);
                                                                                                    ?></p>
                                                <?php endif; ?>
                                                <p class="text-sm text-text-secondary">Số lượng: <?= $item['quantity'] ?></p>
                                                <?php if ($order['status'] === 'completed'): ?>
                                                    <?php if ($hasReviewed): ?>
                                                        <p class="text-sm text-green-600 mt-2 flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                                            Đã đánh giá
                                                        </p>
                                                    <?php else: ?>
                                                        <button onclick="openReviewModal(<?= $order['id'] ?>, <?= $item['product_id'] ?>, '<?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?>')" class="mt-2 text-base text-yellow-500 hover:text-yellow-600 font-semibold flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-xl">star</span>
                                                            Đánh giá sản phẩm
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-primary"><?= number_format($item['total_price'], 0, ',', '.') ?>đ</p>
                                                <p class="text-sm text-text-secondary"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ x <?= $item['quantity'] ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php if ($order['status'] === 'cancelled' && !empty($order['cancel_reason'])): ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-6 shadow-sm">
                                <h2 class="text-xl font-bold mb-4 text-red-700 flex items-center gap-2">
                                    <span class="material-symbols-outlined">cancel</span>
                                    Đơn hàng đã bị hủy
                                </h2>
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-red-500">info</span>
                                        <div>
                                            <p class="font-semibold text-red-700">Lý do hủy:</p>
                                            <p class="text-red-600"><?= htmlspecialchars($order['cancel_reason'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="bg-white rounded-lg p-6 shadow-sm">
                                <h2 class="text-xl font-bold mb-4">Thông tin giao hàng</h2>
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-text-secondary">person</span>
                                        <div>
                                            <p class="font-semibold"><?= htmlspecialchars($order['receiver_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-sm text-text-secondary"><?= htmlspecialchars($order['address_phone'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-text-secondary">location_on</span>
                                        <p class="text-sm"><?= htmlspecialchars($order['address_detail'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($order['ward'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($order['district'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($order['province'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                    <?php if ($order['note']): ?>
                                        <div class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-text-secondary">note</span>
                                            <p class="text-sm"><?= htmlspecialchars($order['note'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-lg p-6 shadow-sm sticky top-8">
                                <h2 class="text-xl font-bold mb-4">Tóm tắt đơn hàng</h2>
                                <div class="space-y-3 mb-4 pb-4 border-b">
                                    <div class="flex justify-between">
                                        <span class="text-text-secondary">Tạm tính:</span>
                                        <span><?= number_format($order['subtotal'], 0, ',', '.') ?>đ</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-text-secondary">Phí vận chuyển:</span>
                                        <span><?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ</span>
                                    </div>
                                    <?php if ($order['discount'] > 0): ?>
                                        <div class="flex justify-between text-green-600">
                                            <span>Giảm giá:</span>
                                            <span>-<?= number_format($order['discount'], 0, ',', '.') ?>đ</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex justify-between text-lg font-bold mb-4">
                                    <span>Tổng cộng:</span>
                                    <span class="text-primary"><?= number_format($order['total'], 0, ',', '.') ?>đ</span>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-text-secondary">Thanh toán:</span>
                                        <span class="font-medium"><?php
                                                                    switch ($order['payment_method']) {
                                                                        case 'cod':
                                                                            echo 'COD';
                                                                            break;
                                                                        case 'vnpay':
                                                                            echo 'VNPay';
                                                                            break;
                                                                        case 'momo':
                                                                            echo 'Momo';
                                                                            break;
                                                                        case 'card':
                                                                            echo 'Thẻ';
                                                                            break;
                                                                        case 'wallet':
                                                                            echo 'Ví của tôi';
                                                                            break;
                                                                    }
                                                                    ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-text-secondary">Trạng thái TT:</span>
                                        <span class="font-medium">
                                            <?php
                                            $paymentStatus = $order['payment_status'] ?? 'pending';
                                            switch ($paymentStatus) {
                                                case 'paid':
                                                    echo '<span class="text-green-600">✓ Đã thanh toán</span>';
                                                    break;
                                                case 'failed':
                                                    echo '<span class="text-red-600">✗ Thất bại</span>';
                                                    break;
                                                default:
                                                    echo '<span class="text-amber-600">⏳ Chưa thanh toán</span>';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-text-secondary">Ngày đặt:</span>
                                        <span><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal đánh giá sản phẩm -->
    <div id="reviewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Đánh giá sản phẩm</h3>
                <button onclick="closeReviewModal()" class="text-gray-500 hover:text-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="reviewForm" method="POST" action="<?= BASE_URL ?>?action=order-review">
                <input type="hidden" name="order_id" id="review_order_id" />
                <input type="hidden" name="product_id" id="review_product_id" />
                <input type="hidden" name="rating" id="review_rating" value="5" />
                
                <div class="mb-4">
                    <p class="font-semibold mb-2" id="review_product_name"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Đánh giá của bạn</label>
                    <div class="flex gap-2 text-4xl">
                        <span class="star cursor-pointer hover:text-yellow-500 transition-colors" data-rating="1">☆</span>
                        <span class="star cursor-pointer hover:text-yellow-500 transition-colors" data-rating="2">☆</span>
                        <span class="star cursor-pointer hover:text-yellow-500 transition-colors" data-rating="3">☆</span>
                        <span class="star cursor-pointer hover:text-yellow-500 transition-colors" data-rating="4">☆</span>
                        <span class="star cursor-pointer hover:text-yellow-500 transition-colors" data-rating="5">☆</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Nhận xét</label>
                    <textarea name="comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeReviewModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">Hủy</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReviewModal(orderId, productId, productName) {
            document.getElementById('review_order_id').value = orderId;
            document.getElementById('review_product_id').value = productId;
            document.getElementById('review_product_name').textContent = productName;
            document.getElementById('reviewModal').classList.remove('hidden');
            updateStars(5);
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
            document.getElementById('reviewForm').reset();
            updateStars(5);
        }

        function updateStars(rating) {
            document.getElementById('review_rating').value = rating;
            const stars = document.querySelectorAll('.star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.textContent = '★';
                    star.classList.add('text-yellow-500');
                    star.classList.remove('text-gray-400');
                } else {
                    star.textContent = '☆';
                    star.classList.remove('text-yellow-500');
                    star.classList.add('text-gray-400');
                }
            });
        }

        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                updateStars(rating);
            });
        });

        // Đóng modal khi click bên ngoài
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });
    </script>
</body>

</html>