<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Chill Drink - Địa chỉ</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body  {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#A0DDE6",
                        "background-light": "#f6f7f8",
                        "text-main": "#333333",
                        "text-secondary": "#888888",
                    },
                    fontFamily: {
                        "display": ["Poppins", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>

<body class="font-display bg-background-light text-text-main">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>
    <div class="relative flex h-auto min-h-screen w-full flex-col">
        <div class="layout-container flex h-full grow flex-col">
            <main class="flex flex-1 justify-center py-10 px-4">
                <div class="w-full max-w-4xl">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold">Địa chỉ của tôi</h1>
                        <a href="<?= BASE_URL ?>?action=address-create" class="flex items-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">
                            <span class="material-symbols-outlined">add</span>
                            <span>Thêm địa chỉ</span>
                        </a>
                    </div>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php unset($_SESSION['success']);
                    endif; ?>
                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php unset($_SESSION['errors']);
                    endif; ?>
                    <div class="space-y-4">
                        <?php if (empty($addresses)): ?>
                            <div class="bg-white rounded-lg p-8 text-center shadow-sm">
                                <p class="text-text-secondary">Bạn chưa có địa chỉ nào. Vui lòng thêm địa chỉ mới.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($addresses as $address): ?>
                                <div class="bg-white rounded-lg p-6 shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="font-semibold text-lg"><?= htmlspecialchars($address['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php if ($address['is_default']): ?>
                                                    <span class="px-2 py-1 bg-primary/20 text-primary text-xs rounded-full">Mặc định</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-text-main font-medium"><?= htmlspecialchars($address['receiver_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-text-secondary"><?= htmlspecialchars($address['phone'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-text-secondary mt-2"><?= htmlspecialchars($address['detail'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($address['ward'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($address['district'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($address['province'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="<?= BASE_URL ?>?action=address-edit&id=<?= $address['id'] ?>" class="px-4 py-2 bg-gray-200 text-text-main rounded-lg hover:bg-gray-300 transition-colors">Sửa</a>
                                            <?php if (!$address['is_default']): ?>
                                                <a href="<?= BASE_URL ?>?action=address-set-default&id=<?= $address['id'] ?>" class="px-4 py-2 bg-primary/20 text-primary rounded-lg hover:bg-primary/30 transition-colors">Đặt mặc định</a>
                                                <a href="<?= BASE_URL ?>?action=address-delete&id=<?= $address['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">Xóa</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>