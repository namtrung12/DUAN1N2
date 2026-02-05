<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Chill Drink - Trang Cá Nhân</title>
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
                        "background-dark": "#111921",
                        "text-main": "#333333",
                        "text-secondary": "#393737ff",
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

<body class="font-display bg-background-light dark:bg-background-dark text-text-main dark:text-white/90">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <main class="flex flex-1 justify-center py-5 sm:py-10 px-4">
                <div class="layout-content-container flex flex-col w-full max-w-4xl flex-1 gap-6">
                    <div>
                        <p class="text-text-main dark:text-white text-4xl font-bold leading-tight tracking-[-0.033em] min-w-72">Tài khoản của tôi</p>
                    </div>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php unset($_SESSION['success']);
                    endif; ?>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-6 @container shadow-sm">
                        <div class="flex w-full flex-col gap-6 @[520px]:flex-row @[520px]:justify-between @[520px]:items-center">
                            <div class="flex gap-6 items-center">
                                <div class="relative group">
                                    <?php 
                                    $avatarUrl = !empty($user['avatar']) 
                                        ? BASE_URL . $user['avatar'] 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=128&background=A0DDE6&color=fff';
                                    ?>
                                    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full min-h-24 w-24 sm:min-h-32 sm:w-32 border-4 border-gray-200" style='background-image: url("<?= $avatarUrl ?>");'></div>
                                    <button onclick="document.getElementById('avatarInput').click()" class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer border-4 border-transparent">
                                        <span class="material-symbols-outlined text-white text-3xl">photo_camera</span>
                                    </button>
                                    <form id="avatarForm" action="<?= BASE_URL ?>?action=update-avatar" method="POST" enctype="multipart/form-data" class="hidden">
                                        <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
                                    </form>
                                </div>
                                <div class="flex flex-col justify-center gap-1">
                                    <p class="text-text-main dark:text-white text-[22px] font-semibold leading-tight tracking-[-0.015em]"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="text-text-secondary dark:text-white/70 text-base font-normal leading-normal"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="text-text-secondary dark:text-white/70 text-base font-normal leading-normal"><?= htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>?action=profile-edit" class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-gray-200 dark:bg-gray-700 text-text-main dark:text-white/90 text-sm font-semibold leading-normal tracking-[0.015em] w-full max-w-[480px] @[480px]:w-auto hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                <span class="truncate">Chỉnh sửa</span>
                            </a>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['errors']['avatar'])): ?>
                        <div class="p-4 bg-red-100 text-red-700 rounded-lg"><?= htmlspecialchars($_SESSION['errors']['avatar'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php unset($_SESSION['errors']['avatar']); endif; ?>
                    <div class="flex flex-col gap-6">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <div class="flex px-4 gap-8">
                                <a class="flex flex-col items-center justify-center border-b-[3px] border-b-primary text-text-main dark:text-white pb-[13px] pt-4" href="<?= BASE_URL ?>?action=profile">
                                    <p class="text-sm font-semibold leading-normal tracking-[0.015em]">Thông tin</p>
                                </a>
    
                                <a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-text-secondary dark:text-white/70 pb-[13px] pt-4 hover:text-text-main dark:hover:text-white" href="<?= BASE_URL ?>?action=change-password">
                                    <p class="text-sm font-semibold leading-normal tracking-[0.015em]">Đổi mật khẩu</p>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Địa chỉ của tôi -->
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-text-main dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">location_on</span>
                                Địa chỉ của tôi
                            </h2>
                            <a href="<?= BASE_URL ?>?action=address-create" class="text-sm text-primary hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">add</span>
                                Thêm địa chỉ
                            </a>
                        </div>
                        
                        <?php if (empty($addresses)): ?>
                        <div class="text-center py-8">
                            <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">location_off</span>
                            <p class="text-text-secondary dark:text-white/70">Bạn chưa có địa chỉ nào</p>
                            <a href="<?= BASE_URL ?>?action=address-create" class="inline-block mt-3 px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-colors">
                                Thêm địa chỉ mới
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($addresses as $address): ?>
                            <div class="flex items-start gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg <?= $address['is_default'] ? 'bg-primary/5 border-primary/30' : '' ?>">
                                <span class="material-symbols-outlined text-text-secondary dark:text-white/70 mt-0.5"><?= $address['is_default'] ? 'home' : 'location_on' ?></span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-text-main dark:text-white"><?= htmlspecialchars($address['receiver_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="text-text-secondary dark:text-white/70">|</span>
                                        <span class="text-text-secondary dark:text-white/70"><?= htmlspecialchars($address['phone'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ($address['is_default']): ?>
                                        <span class="px-2 py-0.5 bg-primary/20 text-primary text-xs font-semibold rounded">Mặc định</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm text-text-secondary dark:text-white/70 truncate">
                                        <?= htmlspecialchars($address['detail'], ENT_QUOTES, 'UTF-8') ?>, 
                                        <?= htmlspecialchars($address['ward'], ENT_QUOTES, 'UTF-8') ?>, 
                                        <?= htmlspecialchars($address['district'], ENT_QUOTES, 'UTF-8') ?>, 
                                        <?= htmlspecialchars($address['province'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                                <a href="<?= BASE_URL ?>?action=address-edit&id=<?= $address['id'] ?>" class="text-primary hover:text-primary/80 text-sm font-medium">
                                    Sửa
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="<?= BASE_URL ?>?action=address" class="text-sm text-primary hover:underline">
                                Quản lý tất cả địa chỉ →
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>