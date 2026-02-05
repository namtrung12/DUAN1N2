<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Chill Drink - Thêm địa chỉ</title>
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
                        "primary": "#A0DDE6",
                        "background-light": "#f6f7f8",
                        "text-main": "#333333",
                        "text-secondary": "#888888",
                        "error": "#dc3545",
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
                <div class="w-full max-w-2xl">
                    <h1 class="text-3xl font-bold mb-6">Thêm địa chỉ mới</h1>
                    <form action="<?= BASE_URL ?>?action=address-store" method="POST" class="bg-white rounded-lg p-6 shadow-sm space-y-4">
                        <div class="flex flex-col">
                            <label class="text-text-main text-base font-medium pb-2" for="label">Nhãn địa chỉ</label>
                            <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="label" name="label" type="text" value="<?= htmlspecialchars($_SESSION['old']['label'] ?? 'Nhà', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nhà, Văn phòng, ..." />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-text-main text-base font-medium pb-2" for="receiver_name">Tên người nhận</label>
                            <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="receiver_name" name="receiver_name" type="text" value="<?= htmlspecialchars($_SESSION['old']['receiver_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
                            <?php if (isset($_SESSION['errors']['receiver_name'])): ?>
                                <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['receiver_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-text-main text-base font-medium pb-2" for="phone">Số điện thoại</label>
                            <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="phone" name="phone" type="text" value="<?= htmlspecialchars($_SESSION['old']['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
                            <?php if (isset($_SESSION['errors']['phone'])): ?>
                                <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['phone'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex flex-col">
                                <label class="text-text-main text-base font-medium pb-2" for="province">Tỉnh/Thành phố</label>
                                <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="province" name="province" type="text" value="<?= htmlspecialchars($_SESSION['old']['province'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
                                <?php if (isset($_SESSION['errors']['province'])): ?>
                                    <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['province'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-text-main text-base font-medium pb-2" for="district">Quận/Huyện</label>
                                <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="district" name="district" type="text" value="<?= htmlspecialchars($_SESSION['old']['district'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
                                <?php if (isset($_SESSION['errors']['district'])): ?>
                                    <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['district'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-text-main text-base font-medium pb-2" for="ward">Phường/Xã</label>
                                <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="ward" name="ward" type="text" value="<?= htmlspecialchars($_SESSION['old']['ward'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
                                <?php if (isset($_SESSION['errors']['ward'])): ?>
                                    <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['ward'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-text-main text-base font-medium pb-2" for="detail">Địa chỉ chi tiết</label>
                            <textarea class="form-input w-full rounded-lg border border-gray-300 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="detail" name="detail" rows="3" required><?= htmlspecialchars($_SESSION['old']['detail'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            <?php if (isset($_SESSION['errors']['detail'])): ?>
                                <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['detail'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="is_default" name="is_default" class="w-5 h-5 text-primary rounded focus:ring-2 focus:ring-primary/50" <?= isset($_SESSION['old']['is_default']) ? 'checked' : '' ?> />
                            <label for="is_default" class="text-text-main text-base">Đặt làm địa chỉ mặc định</label>
                        </div>
                        <button type="submit" class="w-full h-12 bg-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Thêm địa chỉ</button>
                    </form>
                    <?php unset($_SESSION['errors'], $_SESSION['old']); ?>
                </div>
            </main>
        </div>
    </div>
</body>

</html>