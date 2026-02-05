<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Đổi phương thức thanh toán - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .custom-primary { color: #A0DDE6; }
        .custom-text { color: #333333; }
    </style>
</head>
<body class="bg-gray-50">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>
    
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <a href="<?= BASE_URL ?>?action=orders" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
                    <span class="material-symbols-outlined mr-2">arrow_back</span>
                    Quay lại đơn hàng
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Đổi phương thức thanh toán</h1>
                <p class="text-gray-600 mt-2">Đơn hàng #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></p>
            </div>

            <?php if (isset($_SESSION['errors'])): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Methods -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold mb-4">Chọn phương thức thanh toán mới</h2>
                        
                        <form method="POST" action="<?= BASE_URL ?>?action=order-update-payment" id="paymentForm">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>"/>
                            
                            <div class="space-y-4">
                                <!-- COD -->
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                    <input type="radio" name="payment_method" value="cod" <?= $order['payment_method'] === 'cod' ? 'checked' : '' ?> class="w-5 h-5 text-blue-600 focus:ring-blue-500"/>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="material-symbols-outlined text-gray-600">local_shipping</span>
                                            <div>
                                                <p class="font-semibold text-gray-900">Thanh toán khi nhận hàng (COD)</p>
                                                <p class="text-sm text-gray-600">Thanh toán bằng tiền mặt khi nhận hàng</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- VNPay -->
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                    <input type="radio" name="payment_method" value="vnpay" <?= $order['payment_method'] === 'vnpay' ? 'checked' : '' ?> class="w-5 h-5 text-blue-600 focus:ring-blue-500"/>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="material-symbols-outlined text-gray-600">account_balance</span>
                                            <div>
                                                <p class="font-semibold text-gray-900">VNPay</p>
                                                <p class="text-sm text-gray-600">Thanh toán qua cổng VNPay (ATM, Visa, MasterCard)</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Wallet -->
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                    <input type="radio" name="payment_method" value="wallet" <?= $order['payment_method'] === 'wallet' ? 'checked' : '' ?> class="w-5 h-5 text-blue-600 focus:ring-blue-500"/>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="material-symbols-outlined text-gray-600">account_balance_wallet</span>
                                            <div>
                                                <p class="font-semibold text-gray-900">Ví của tôi</p>
                                                <p class="text-sm text-gray-600">
                                                    Số dư: <span class="font-semibold text-blue-600"><?= number_format($walletBalance, 0, ',', '.') ?>đ</span>
                                                    <?php if ($walletBalance < $order['total']): ?>
                                                        <span class="text-red-600 ml-2">(Không đủ số dư)</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="mt-6 flex gap-4">
                                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                    Xác nhận đổi phương thức
                                </button>
                                <a href="<?= BASE_URL ?>?action=orders" class="flex-1 text-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                                    Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                        <h3 class="text-lg font-bold mb-4">Thông tin đơn hàng</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Mã đơn hàng:</span>
                                <span class="font-semibold">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tạm tính:</span>
                                <span><?= number_format($order['subtotal'], 0, ',', '.') ?>đ</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phí vận chuyển:</span>
                                <span><?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ</span>
                            </div>
                            
                            <?php if ($order['discount'] > 0): ?>
                            <div class="flex justify-between text-green-600">
                                <span>Giảm giá:</span>
                                <span>-<?= number_format($order['discount'], 0, ',', '.') ?>đ</span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="border-t pt-3 flex justify-between items-center">
                                <span class="font-semibold text-gray-900">Tổng cộng:</span>
                                <span class="text-xl font-bold text-blue-600"><?= number_format($order['total'], 0, ',', '.') ?>đ</span>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-xl">info</span>
                                <div class="text-sm text-gray-700">
                                    <p class="font-semibold mb-1">Lưu ý:</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Chỉ đổi được khi đơn chưa thanh toán</li>
                                        <li>VNPay/Ví: Thanh toán ngay, đơn chuyển sang xử lý</li>
                                        <li>COD: Thanh toán khi nhận hàng</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Kiểm tra số dư ví khi chọn
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const walletBalance = <?= $walletBalance ?>;
            const orderTotal = <?= $order['total'] ?>;
            
            if (selectedMethod === 'wallet' && walletBalance < orderTotal) {
                e.preventDefault();
                alert('Số dư ví không đủ để thanh toán đơn hàng này. Vui lòng nạp thêm tiền hoặc chọn phương thức khác.');
                return false;
            }
        });
    </script>
</body>
</html>
