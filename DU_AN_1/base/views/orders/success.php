<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đặt hàng thành công - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4799eb",
                        "background-light": "#f6f7f8",
                        "custom-primary": "#A0E7E5",
                        "custom-text": "#333333",
                    },
                    fontFamily: {
                        "display": ["Poppins", "sans-serif"]
                    },
                },
            },
        }
    </script>
</head>

<body class="bg-background-light font-display text-custom-text">
    <div class="relative flex h-auto min-h-screen w-full flex-col items-center justify-center">
        <div class="max-w-2xl w-full mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-8 md:p-12 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-green-600 text-5xl">check_circle</span>
                </div>
                <h1 class="text-3xl font-bold text-custom-text mb-4">Đặt hàng thành công!</h1>
                <p class="text-custom-text/70 text-lg mb-2">Cảm ơn bạn đã đặt hàng tại Chill Drink</p>
                <p class="text-custom-text/70 mb-8">Mã đơn hàng: <span class="font-semibold text-primary">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></span></p>
                <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-custom-text/70">Tổng tiền:</span>
                        <span class="font-bold text-xl text-primary"><?= number_format($order['total'], 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-custom-text/70">Phương thức thanh toán:</span>
                        <span class="font-semibold"><?php
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
                                                    }
                                                    ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-custom-text/70">Trạng thái:</span>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold">Đang xử lý</span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?= BASE_URL ?>?action=order-detail&id=<?= $order['id'] ?>" class="flex items-center justify-center gap-2 h-12 px-6 bg-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <span>Xem chi tiết đơn hàng</span>
                    </a>
                    <a href="<?= BASE_URL ?>?action=products" class="flex items-center justify-center gap-2 h-12 px-6 bg-gray-200 text-custom-text rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        <span>Tiếp tục mua sắm</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>