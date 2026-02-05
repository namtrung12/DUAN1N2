<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gio hang - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include PATH_VIEW . 'layouts/common-head.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-6">Gio hang</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (empty($cart['items'])): ?>
            <div class="bg-white rounded-2xl p-10 text-center shadow-sm">
                <p class="text-slate-600">Gio hang trong.</p>
                <a href="<?= BASE_URL ?>?action=products" class="inline-flex mt-4 px-6 py-3 bg-primary text-white rounded-lg font-semibold">Mua ngay</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    <?php $subtotal = 0; ?>
                    <?php foreach ($cart['items'] as $item): ?>
                        <?php $lineTotal = $item['unit_price'] * $item['quantity']; $subtotal += $lineTotal; ?>
                        <div class="bg-white rounded-2xl p-4 shadow-sm flex gap-4">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?>" class="w-20 h-20 rounded-lg object-cover" onerror="this.src='https://via.placeholder.com/80'" />
                            <div class="flex-1">
                                <h3 class="font-semibold text-slate-900"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="text-sm text-slate-500">Size: <?= htmlspecialchars($item['size_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="text-sm text-slate-600 mt-1"><?= number_format($item['unit_price'], 0, ',', '.') ?>d</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <form action="<?= BASE_URL ?>?action=cart-update" method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>" />
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-20 rounded-lg border border-gray-300 px-2 py-1" />
                                    <button class="px-3 py-1 bg-gray-100 rounded-lg text-sm">Cap nhat</button>
                                </form>
                                <a href="<?= BASE_URL ?>?action=cart-remove&item_id=<?= $item['id'] ?>" class="text-sm text-red-600">Xoa</a>
                                <span class="font-semibold text-slate-900"><?= number_format($lineTotal, 0, ',', '.') ?>d</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm h-fit">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Tong cong</h2>
                    <div class="flex justify-between text-slate-600 mb-2">
                        <span>Tam tinh</span>
                        <span><?= number_format($subtotal, 0, ',', '.') ?>d</span>
                    </div>
                    <div class="flex justify-between text-slate-600 mb-4">
                        <span>Phi van chuyen</span>
                        <span>15.000d</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-900 text-lg">
                        <span>Tong</span>
                        <span><?= number_format($subtotal + 15000, 0, ',', '.') ?>d</span>
                    </div>
                    <form action="<?= BASE_URL ?>?action=checkout" method="POST" class="mt-6">
                        <button class="w-full px-6 py-3 bg-primary text-white rounded-lg font-semibold">Dat hang</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include PATH_VIEW . 'layouts/footer.php'; ?>
</body>
</html>
