<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include PATH_VIEW . 'layouts/common-head.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-2">
            <div class="bg-gray-100">
                <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/600x600?text=No+Image'" />
            </div>
            <div class="p-8">
                <p class="text-sm text-slate-500 mb-2"><?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <h1 class="text-3xl font-bold text-slate-900 mb-4"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="text-slate-600 mb-6"><?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                <form action="<?= BASE_URL ?>?action=cart-add" method="POST" class="space-y-6">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Chon size</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <?php foreach ($sizes as $size): ?>
                                <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:border-primary">
                                    <input type="radio" name="size_id" value="<?= $size['size_id'] ?>" class="text-primary" required />
                                    <span class="font-medium"><?= htmlspecialchars($size['size_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="ml-auto text-primary font-semibold"><?= number_format($size['price'], 0, ',', '.') ?>d</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if (!empty($toppings)): ?>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-3">Topping</h3>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($toppings as $topping): ?>
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-sm text-slate-600"><?= htmlspecialchars($topping['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center gap-4">
                        <input type="number" name="quantity" value="1" min="1" class="w-24 rounded-lg border border-gray-300 px-3 py-2" />
                        <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90">Them vao gio</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include PATH_VIEW . 'layouts/footer.php'; ?>
</body>
</html>
