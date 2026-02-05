<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Thanh toán - Chill Drink</title>
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
                        "custom-accent": "#4A90E2",
                    },
                    fontFamily: {
                        "display": ["Poppins", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>

<body class="bg-background-light font-display text-custom-text">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>
    <div class="relative flex h-auto min-h-screen w-full flex-col">
        <div class="layout-container flex h-full grow flex-col">
            <div class="px-4 sm:px-8 md:px-16 lg:px-24 xl:px-40 flex flex-1 justify-center py-5">
                <div class="layout-content-container flex flex-col w-full max-w-7xl flex-1">
                    <div class="flex flex-wrap gap-2 p-4">
                        <a class="text-custom-accent/80 text-base font-medium leading-normal" href="<?= BASE_URL ?>?action=cart">Giỏ hàng</a>
                        <span class="text-custom-text/50 text-base font-medium leading-normal">/</span>
                        <span class="text-custom-text text-base font-bold leading-normal">Thanh toán</span>
                    </div>
                    <div class="flex flex-wrap justify-between gap-3 p-4">
                        <div class="flex min-w-72 flex-col gap-3">
                            <p class="text-custom-text text-4xl font-bold leading-tight tracking-[-0.033em]">Thanh Toán</p>
                            <p class="text-custom-text/70 text-base font-normal leading-normal">Vui lòng kiểm tra thông tin giao hàng và đơn hàng trước khi xác nhận.</p>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="mx-4 mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php unset($_SESSION['errors']);
                    endif; ?>
                    <form action="<?= BASE_URL ?>?action=checkout-process" method="POST" class="flex flex-col lg:flex-row gap-12 mt-8">
                        <div class="w-full lg:w-2/3">
                            <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                                <h2 class="text-custom-text text-[22px] font-bold leading-tight tracking-[-0.015em] mb-6">Thông tin nhận hàng</h2>
                                <?php if (empty($addresses)): ?>
                                    <div class="text-center py-6">
                                        <p class="text-custom-text/70 mb-4">Bạn chưa có địa chỉ giao hàng</p>
                                        <a href="<?= BASE_URL ?>?action=address-create" class="inline-flex items-center justify-center h-10 px-4 bg-primary text-white rounded-lg font-semibold hover:bg-opacity-90">Thêm địa chỉ</a>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-4">
                                        <?php foreach ($addresses as $addr): ?>
                                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer has-[:checked]:border-custom-primary has-[:checked]:bg-custom-primary/10 transition-all">
                                                <input class="form-radio h-5 w-5 text-custom-primary focus:ring-custom-primary/50 mt-1" name="address_id" type="radio" value="<?= $addr['id'] ?>" <?= ($defaultAddress && $defaultAddress['id'] == $addr['id']) ? 'checked' : '' ?> required />
                                                <div class="ml-4 flex-grow">
                                                    <p class="font-semibold text-custom-text"><?= htmlspecialchars($addr['receiver_name'], ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($addr['phone'], ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-sm text-custom-text/60 mt-1"><?= htmlspecialchars($addr['detail'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($addr['ward'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($addr['district'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($addr['province'], ENT_QUOTES, 'UTF-8') ?></p>
                                                    <?php if ($addr['is_default']): ?>
                                                        <span class="inline-block mt-2 px-2 py-1 bg-primary/20 text-primary text-xs rounded-full">Mặc định</span>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <a href="<?= BASE_URL ?>?action=address-create" class="inline-flex items-center justify-center mt-4 h-10 px-4 bg-gray-200 text-custom-text rounded-lg font-semibold hover:bg-gray-300">+ Thêm địa chỉ mới</a>
                                <?php endif; ?>
                            </div>
                            <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 mt-8">
                                <h2 class="text-custom-text text-[22px] font-bold leading-tight tracking-[-0.015em] mb-6">Phương thức thanh toán</h2>
                                <div class="space-y-4">
                                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer has-[:checked]:border-custom-primary has-[:checked]:bg-custom-primary/10 transition-all">
                                        <input checked class="form-radio h-5 w-5 text-custom-primary focus:ring-custom-primary/50" name="payment_method" type="radio" value="cod" />
                                        <div class="ml-4 flex-grow flex items-center">
                                            <span class="material-symbols-outlined text-custom-text/80 mr-3">local_shipping</span>
                                            <div>
                                                <p class="font-semibold text-custom-text">Thanh toán khi nhận hàng (COD)</p>
                                                <p class="text-sm text-custom-text/60">Thanh toán trực tiếp cho nhân viên giao hàng.</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer has-[:checked]:border-custom-primary has-[:checked]:bg-custom-primary/10 transition-all">
                                        <input class="form-radio h-5 w-5 text-custom-primary focus:ring-custom-primary/50" name="payment_method" type="radio" value="vnpay" />
                                        <div class="ml-4 flex-grow flex items-center">
                                            <span class="material-symbols-outlined text-custom-text/80 mr-3">account_balance</span>
                                            <div>
                                                <p class="font-semibold text-custom-text">VNPay</p>
                                                <p class="text-sm text-custom-text/60">Thanh toán qua cổng VNPay.</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer has-[:checked]:border-custom-primary has-[:checked]:bg-custom-primary/10 transition-all">
                                        <input class="form-radio h-5 w-5 text-custom-primary focus:ring-custom-primary/50" name="payment_method" type="radio" value="momo" />
                                        <div class="ml-4 flex-grow flex items-center">
                                            <span class="material-symbols-outlined text-custom-text/80 mr-3">wallet</span>
                                            <div>
                                                <p class="font-semibold text-custom-text">Ví điện tử Momo</p>
                                                <p class="text-sm text-custom-text/60">Quét mã QR để thanh toán.</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer has-[:checked]:border-custom-primary has-[:checked]:bg-custom-primary/10 transition-all">
                                        <input class="form-radio h-5 w-5 text-custom-primary focus:ring-custom-primary/50" name="payment_method" type="radio" value="wallet" />
                                        <div class="ml-4 flex-grow flex items-center">
                                            <span class="material-symbols-outlined text-custom-text/80 mr-3">account_balance_wallet</span>
                                            <div>
                                                <p class="font-semibold text-custom-text">Ví của tôi</p>
                                                <p class="text-sm text-custom-text/60">Trừ trực tiếp vào ví Chill Drink của bạn.</p>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 mt-8">
                                <label class="flex flex-col w-full">
                                    <p class="text-custom-text text-base font-medium leading-normal pb-2">Ghi chú đơn hàng (tùy chọn)</p>
                                    <textarea name="note" class="form-textarea flex w-full min-w-0 flex-1 resize-y overflow-hidden rounded-lg text-custom-text focus:outline-0 focus:ring-2 focus:ring-custom-primary/50 border border-gray-200 bg-gray-50 min-h-28 placeholder:text-custom-text/50 p-4 text-base font-normal leading-normal" placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                                </label>
                            </div>
                        </div>
                        <div class="w-full lg:w-1/3">
                            <div class="bg-white rounded-xl shadow-sm p-6 sticky top-8">
                                <h2 class="text-custom-text text-[22px] font-bold leading-tight tracking-[-0.015em] mb-6 border-b pb-4">Đơn hàng của bạn</h2>
                                <div class="space-y-4 max-h-96 overflow-y-auto">
                                    <?php foreach ($cartData as $data): ?>
                                        <?php
                                        $item = $data['cart_item'];
                                        $toppings = $data['toppings'];
                                        ?>
                                        <div class="flex items-center gap-4">
                                            <img class="w-16 h-16 object-cover rounded-lg" src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" />
                                            <div class="flex-grow">
                                                <p class="font-semibold text-custom-text"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                                <p class="text-sm text-custom-text/60">Size: <?= htmlspecialchars($item['size'], ENT_QUOTES, 'UTF-8') ?> x <?= $item['quantity'] ?></p>
                                                <?php if (!empty($toppings)): ?>
                                                    <p class="text-xs text-custom-text/50">+ <?php
                                                                                                $toppingNames = array_map(function ($t) {
                                                                                                    return htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8');
                                                                                                }, $toppings);
                                                                                                echo implode(', ', $toppingNames);
                                                                                                ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <p class="font-semibold text-custom-text"><?= number_format($data['item_total'], 0, ',', '.') ?>đ</p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-6 pt-6 border-t border-dashed">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-custom-text/70">Tạm tính</p>
                                        <p class="font-medium text-custom-text"><?= number_format($subtotal + $toppingTotal, 0, ',', '.') ?>đ</p>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-custom-text/70">Phí vận chuyển</p>
                                        <p class="font-medium text-custom-text"><?= number_format($shippingFee, 0, ',', '.') ?>đ</p>
                                    </div>
                                    <?php if ($discount > 0): ?>
                                        <div class="flex justify-between items-center text-green-600">
                                            <p>Giảm giá</p>
                                            <p class="font-medium">-<?= number_format($discount, 0, ',', '.') ?>đ</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-4 pt-4 border-t">
                                    <div class="flex justify-between items-center text-lg">
                                        <p class="font-semibold text-custom-text">Tổng cộng</p>
                                        <p class="font-bold text-custom-accent text-xl"><?= number_format($total, 0, ',', '.') ?>đ</p>
                                    </div>
                                </div>

                                <button type="submit" class="w-full mt-6 bg-custom-primary text-white font-bold text-lg py-4 px-6 rounded-xl hover:bg-opacity-90 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-primary">XÁC NHẬN ĐẶT HÀNG</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>