<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiet don hang - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include PATH_VIEW . 'layouts/common-head.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="<?= BASE_URL ?>?action=orders" class="text-sm text-slate-600">&larr; Quay lai</a>
        <h1 class="text-3xl font-bold text-slate-900 mt-2 mb-6">Don hang #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></h1>

        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex flex-wrap gap-6 mb-6">
                <div>
                    <p class="text-sm text-slate-500">Trang thai</p>
                    <p class="font-semibold text-slate-900"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Ngay dat</p>
                    <p class="font-semibold text-slate-900"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
            </div>

            <div class="space-y-4">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="flex gap-4 border-b pb-4">
                        <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" class="w-20 h-20 rounded-lg object-cover" onerror="this.src='https://via.placeholder.com/80'" />
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-900"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="text-sm text-slate-500">Size: <?= htmlspecialchars($item['size_name'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-sm text-slate-500">So luong: <?= $item['quantity'] ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-900"><?= number_format($item['total_price'], 0, ',', '.') ?>d</p>
                            <p class="text-xs text-slate-500"><?= number_format($item['unit_price'], 0, ',', '.') ?>d/sp</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6 border-t pt-4">
                <div class="flex justify-between text-slate-600 mb-2">
                    <span>Tam tinh</span>
                    <span><?= number_format($order['subtotal'], 0, ',', '.') ?>d</span>
                </div>
                <div class="flex justify-between text-slate-600 mb-2">
                    <span>Phi van chuyen</span>
                    <span><?= number_format($order['shipping_fee'], 0, ',', '.') ?>d</span>
                </div>
                <div class="flex justify-between font-bold text-slate-900 text-lg">
                    <span>Tong</span>
                    <span><?= number_format($order['total'], 0, ',', '.') ?>d</span>
                </div>
            </div>
        </div>
    </main>

    <?php include PATH_VIEW . 'layouts/footer.php'; ?>
</body>
</html>
