<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Chill Drink - Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#A0D8B3",
                        "background-light": "#F8F9FA",
                        "background-dark": "#111921",
                        "text-primary": "#343A40",
                        "text-secondary": "#6c757d",
                        "accent": "#FFDAB9",
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

<body class="bg-background-light dark:bg-background-dark font-display text-text-primary dark:text-background-light">
    <?php
    $siteSettings = get_site_settings();
    $siteName = $siteSettings['site_name'] ?? 'Chill Drink';
    $siteLogo = $siteSettings['site_logo'] ?? '';
    ?>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <main class="flex-grow">
                <div class="w-full min-h-screen grid grid-cols-1 lg:grid-cols-2">
                    <div class="flex flex-col items-center justify-center p-6 sm:p-12 order-2 lg:order-1">
                        <div class="w-full max-w-md">
                            <div class="text-center lg:text-left mb-10">
                                <a class="inline-flex items-center gap-3 text-2xl font-bold text-text-primary dark:text-white" href="<?= BASE_URL ?>">
                                    <?php if (!empty($siteLogo)): ?>
                                        <img src="<?= BASE_URL . $siteLogo ?>" alt="<?= htmlspecialchars($siteName) ?>" style="width: 70px; height: 70px; object-fit: contain;">
                                    <?php else: ?>
                                        <span class="material-symbols-outlined text-primary text-4xl">local_bar</span>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($siteName) ?></span>
                                </a>
                            </div>
                            <div class="mb-6">
                                <div class="flex border-b border-gray-200 dark:border-gray-700">
                                    <a href="<?= BASE_URL ?>?action=login" class="flex-1 py-3 px-4 text-center font-semibold text-primary border-b-2 border-primary">Đăng nhập</a>
                                    <a href="<?= BASE_URL ?>?action=register" class="flex-1 py-3 px-4 text-center font-medium text-text-secondary dark:text-gray-400">Đăng ký</a>
                                </div>
                            </div>
                            <h1 class="text-text-primary dark:text-white tracking-light text-3xl font-bold leading-tight text-left pb-1">Chào mừng trở lại!</h1>
                            <p class="text-text-secondary dark:text-gray-400 text-base font-normal leading-normal pb-6">Vui lòng nhập thông tin để tiếp tục.</p>
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php unset($_SESSION['success']);
                            endif; ?>
                            <?php if (isset($_SESSION['errors']['login'])): ?>
                                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg"><?= htmlspecialchars($_SESSION['errors']['login'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                            <form action="<?= BASE_URL ?>?action=post-login" method="POST" class="space-y-4">
                                <div class="flex flex-col">
                                    <label class="text-text-primary dark:text-white text-base font-medium leading-normal pb-2" for="email">Email</label>
                                    <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-text-primary dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-background-light dark:bg-gray-700 h-14 placeholder:text-text-secondary p-4 text-base font-normal leading-normal" id="email" name="email" placeholder="Nhập email của bạn" type="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
                                    <?php if (isset($_SESSION['errors']['email'])): ?>
                                        <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex justify-between items-center pb-2">
                                        <label class="text-text-primary dark:text-white text-base font-medium leading-normal" for="password">Mật khẩu</label>
                                    </div>
                                    <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-text-primary dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-background-light dark:bg-gray-700 h-14 placeholder:text-text-secondary p-4 text-base font-normal leading-normal" id="password" name="password" placeholder="Nhập mật khẩu của bạn" type="password" required />
                                    <?php if (isset($_SESSION['errors']['password'])): ?>
                                        <span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['password'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="w-full h-14 px-6 bg-primary text-white rounded-lg font-semibold text-base shadow-sm hover:bg-opacity-90 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Đăng nhập</button>
                            </form>
                            <?php unset($_SESSION['errors'], $_SESSION['old']); ?>
                        </div>
                    </div>
                    <div class="relative hidden lg:flex items-center justify-center bg-accent/20 order-1 lg:order-2">
                        <div class="absolute inset-0 bg-primary opacity-10"></div>
                        <div class="relative w-full max-w-lg aspect-square p-8">
                            <img class="w-full h-full object-cover rounded-xl shadow-2xl" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQnU6kN1CzrbJRxLCIFQLZR2LRWd0k3u-CuPgHz_n9CHOz3ne0gazJQRsOMHVoWP7HG4pE6ELXsEDYAX4LjAsUdCy9r0-URaLzh6OjJdJvZYaD_f-dWSxrWcYjU-QvjQa6ml6oeuIvLEiGHllmiDrPbwhQudqPTmb09DTBLu_91TBIM4ZItlaUl32A03bP9-AHFVG3ClKk4pGexgJns-ljVd5wEv9Up0r9KJhbJXJ_BVkmZcA4Sg7hc7cpdtDiGGL7Egh-F3G4g3cG" />
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>