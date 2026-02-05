<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Don hang - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include PATH_VIEW . 'layouts/common-head.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-6">Don hang cua toi</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-2xl p-10 text-center shadow-sm">
                <p class="text-slate-600">Chua co don hang nao.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-sm text-slate-500">Ma don</p>
                                <p class="text-lg font-bold text-slate-900">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Trang thai</p>
                                <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Tong tien</p>
                                <p class="text-lg font-bold text-primary"><?= number_format($order['total'], 0, ',', '.') ?>d</p>
                            </div>
                            <div>
                                <a href="<?= BASE_URL ?>?action=order-detail&order_id=<?= $order['id'] ?>" class="px-4 py-2 bg-gray-100 rounded-lg">Chi tiet</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include PATH_VIEW . 'layouts/footer.php'; ?>
</body>
</html>
