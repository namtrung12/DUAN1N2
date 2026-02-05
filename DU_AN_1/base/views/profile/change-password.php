<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Chill Drink - Đổi mật khẩu</title>
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
                        "primary": "#14cdeaff",
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
                    <a href="<?= BASE_URL ?>?action=profile" class="inline-flex items-center gap-1 text-slate-600 font-medium hover:text-primary mb-4 transition-colors">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                        <span>Quay lại</span>
                    </a>
                    <h1 class="text-3xl font-bold mb-6">Đổi mật khẩu</h1>
                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?= BASE_URL ?>?action=update-password" method="POST" class="bg-white rounded-lg p-6 shadow-sm space-y-4">
                        <div class="flex flex-col">
                            <label class="text-text-main text-base font-medium pb-2" for="current_password">Mật khẩu hiện tại</label>
                            <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="current_password" name="current_password" type="password" required />
                            <?php if (isset($_SESSION['errors']['current_password'])): ?>
                                <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['current_password'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-text-main text-base font-medium pb-2" for="new_password">Mật khẩu mới</label>
                            <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="new_password" name="new_password" type="password" required />
                            <?php if (isset($_SESSION['errors']['new_password'])): ?>
                                <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['new_password'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-text-main text-base font-medium pb-2" for="confirm_password">Xác nhận mật khẩu mới</label>
                            <input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="confirm_password" name="confirm_password" type="password" required />
                            <?php if (isset($_SESSION['errors']['confirm_password'])): ?>
                                <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['confirm_password'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="w-full h-12 bg-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Đổi mật khẩu</button>
                    </form>
                    <?php unset($_SESSION['errors']); ?>
                </div>
            </main>
        </div>
    </div>
</body>

</html>