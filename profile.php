<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ho so - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include PATH_VIEW . 'layouts/common-head.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-6">Ho so</h1>

        <div class="bg-white rounded-2xl p-6 shadow-sm max-w-xl">
            <div class="flex items-center gap-4 mb-6">
                <?php
                $avatarUrl = !empty($_SESSION['user']['avatar'])
                    ? BASE_URL . $_SESSION['user']['avatar']
                    : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user']['name']) . '&size=80&background=A0DDE6&color=fff';
                ?>
                <img src="<?= $avatarUrl ?>" class="w-16 h-16 rounded-full object-cover" />
                <div>
                    <p class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-sm text-slate-500"><?= htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <div class="space-y-2 text-slate-600">
                <p><strong>So dien thoai:</strong> <?= htmlspecialchars($_SESSION['user']['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Vai tro:</strong> <?= (int)$_SESSION['user']['role_id'] === 2 ? 'Admin' : 'Customer' ?></p>
            </div>
            <div class="mt-6 flex gap-3">
                <a href="<?= BASE_URL ?>?action=address" class="px-4 py-2 bg-primary text-white rounded-lg">Quan ly dia chi</a>
                <a href="<?= BASE_URL ?>?action=loyalty" class="px-4 py-2 bg-gray-100 rounded-lg">Diem thuong</a>
            </div>
        </div>
    </main>

    <?php include PATH_VIEW . 'layouts/footer.php'; ?>
</body>
</html>
