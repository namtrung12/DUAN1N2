<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dang ky - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include PATH_VIEW . 'layouts/common-head.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="max-w-md mx-auto bg-white rounded-2xl p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-slate-900 mb-4">Dang ky</h1>

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>?action=register" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ho ten</label>
                    <input type="text" name="name" required class="w-full rounded-lg border border-gray-300 px-4 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full rounded-lg border border-gray-300 px-4 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">So dien thoai</label>
                    <input type="text" name="phone" class="w-full rounded-lg border border-gray-300 px-4 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mat khau</label>
                    <input type="password" name="password" required class="w-full rounded-lg border border-gray-300 px-4 py-2" />
                </div>
                <button class="w-full px-4 py-2 bg-primary text-white rounded-lg font-semibold">Dang ky</button>
            </form>
        </div>
    </main>

    <?php include PATH_VIEW . 'layouts/footer.php'; ?>
</body>
</html>
