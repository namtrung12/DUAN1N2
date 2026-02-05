<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mã giảm giá - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
</head>
<body class="bg-gray-50">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>

    <main class="container mx-auto px-4 py-10">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Đổi Mã giảm giá</h1>
                    <p class="text-slate-600">Sử dụng điểm tích lũy để đổi mã giảm giá</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-600">Điểm của bạn</p>
                    <p class="text-3xl font-bold text-amber-600"><?= number_format($loyaltyPoints['total_points']) ?> điểm</p>
                </div>
            </div>

            <?php if (isset($_SESSION['errors'])): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); endif; ?>

            <?php if (empty($redeemableCoupons)): ?>
            <div class="bg-white rounded-2xl p-10 text-center shadow-sm">
                <span class="material-symbols-outlined text-6xl text-gray-400 mb-4">confirmation_number</span>
                <p class="text-slate-600 text-lg mb-4">Hiện chưa có mã giảm giá nào để đổi</p>
                <a href="<?= BASE_URL ?>?action=loyalty" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Quay lại
                </a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php 
                $couponModel = new Coupon();
                foreach ($redeemableCoupons as $coupon): 
                    $hasRedeemed = $couponModel->hasUserRedeemed($_SESSION['user']['id'], $coupon['id']);
                    $canAfford = $loyaltyPoints['total_points'] >= $coupon['point_cost'];
                ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm border-2 <?= $hasRedeemed ? 'border-gray-200 opacity-60' : 'border-transparent hover:border-blue-200' ?> transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-slate-900 mb-1"><?= htmlspecialchars($coupon['code'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="text-sm text-slate-600">
                                <?php if ($coupon['type'] === 'percent'): ?>
                                    Giảm <?= $coupon['value'] ?>%
                                <?php else: ?>
                                    Giảm <?= number_format($coupon['value']) ?>đ
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <?php if ($coupon['type'] === 'percent'): ?>
                                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-bold text-purple-600"><?= $coupon['value'] ?>%</span>
                                </div>
                            <?php else: ?>
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl text-green-600">sell</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4 text-sm text-slate-600">
                        <?php if ($coupon['min_order'] > 0): ?>
                        <p class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">shopping_cart</span>
                            Đơn tối thiểu: <?= number_format($coupon['min_order']) ?>đ
                        </p>
                        <?php endif; ?>
                        <?php if ($coupon['expires_at']): ?>
                        <p class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            HSD: <?= date('d/m/Y', strtotime($coupon['expires_at'])) ?>
                        </p>
                        <?php endif; ?>
                        <?php if (isset($coupon['max_redemptions']) && $coupon['max_redemptions']): ?>
                        <p class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">people</span>
                            Còn: <?= $coupon['max_redemptions'] - ($coupon['redemption_count'] ?? 0) ?> lượt
                        </p>
                        <?php endif; ?>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-slate-600">Cần:</span>
                            <span class="text-2xl font-bold text-amber-600"><?= $coupon['point_cost'] ?> điểm</span>
                        </div>

                        <?php if ($hasRedeemed): ?>
                            <button disabled class="w-full py-3 bg-gray-200 text-gray-500 rounded-lg font-semibold cursor-not-allowed">
                                Đã đổi
                            </button>
                        <?php elseif (!$canAfford): ?>
                            <button disabled class="w-full py-3 bg-gray-200 text-gray-500 rounded-lg font-semibold cursor-not-allowed">
                                Không đủ điểm
                            </button>
                        <?php else: ?>
                            <form method="POST" action="<?= BASE_URL ?>?action=loyalty-redeem" onsubmit="return confirm('Bạn có chắc muốn đổi mã này?');">
                                <input type="hidden" name="coupon_id" value="<?= $coupon['id'] ?>"/>
                                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                    Đổi ngay
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 text-center">
                <a href="<?= BASE_URL ?>?action=loyalty" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Quay lại
                </a>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
