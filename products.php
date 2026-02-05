<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>San pham - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include PATH_VIEW . 'layouts/common-head.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">San pham</h1>
                <p class="text-slate-600 mt-1">Tim thay <?= count($products) ?> san pham</p>
            </div>
            <form action="<?= BASE_URL ?>" method="GET" class="flex w-full max-w-md">
                <input type="hidden" name="action" value="products" />
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Tim do uong..." class="w-full rounded-l-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-primary/50" />
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-r-lg">Tim</button>
            </form>
        </div>

        <div class="flex flex-wrap gap-2 mb-8">
            <a href="<?= BASE_URL ?>?action=products" class="px-4 py-2 rounded-full text-sm font-semibold <?= empty($categoryId) ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-slate-600' ?>">
                Tat ca
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>?action=products&category_id=<?= $cat['id'] ?>" class="px-4 py-2 rounded-full text-sm font-semibold <?= (int)$categoryId === (int)$cat['id'] ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-slate-600' ?>">
                    <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="bg-white rounded-xl p-10 text-center shadow-sm">
                <p class="text-slate-600">Khong co san pham phu hop.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <?php
                    $prices = [];
                    if (!empty($product['sizes'])) {
                        foreach ($product['sizes'] as $size) {
                            $prices[] = (int)$size['price'];
                        }
                    }
                    $minPrice = !empty($prices) ? min($prices) : 0;
                    ?>
                    <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $product['id'] ?>" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all">
                        <div class="h-48 bg-gray-100 overflow-hidden">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'" />
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-slate-500 mb-1"><?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            <h3 class="font-semibold text-slate-900 mb-2"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <div class="flex items-center justify-between">
                                <span class="text-primary font-bold"><?= number_format($minPrice, 0, ',', '.') ?>d</span>
                                <span class="text-xs text-slate-500">Xem chi tiet</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include PATH_VIEW . 'layouts/footer.php'; ?>
</body>
</html>
