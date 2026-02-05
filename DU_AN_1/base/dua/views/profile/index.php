<!DOCTYPE html>
<html class="light" lang="vi">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Chill Drink - Trang Cá Nhân</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<style>
body { font-family: 'Poppins', sans-serif; }
</style>
<script>
tailwind.config = {
darkMode: "class",
theme: {
extend: {
colors: {
"primary": "#A0DDE6",
"background-light": "#f6f7f8",
"background-dark": "#111921",
"text-main": "#333333",
"text-secondary": "#888888",
},
fontFamily: { "display": ["Poppins", "sans-serif"] },
borderRadius: { "DEFAULT": "0.5rem", "lg": "0.75rem", "xl": "1rem", "full": "9999px" },
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
<?php unset($_SESSION['success']); endif; ?>
<div class="bg-white dark:bg-gray-800/50 rounded-lg p-6 @container shadow-sm">
<div class="flex w-full flex-col gap-6 @[520px]:flex-row @[520px]:justify-between @[520px]:items-center">
<div class="flex gap-6 items-center">
<div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full min-h-24 w-24 sm:min-h-32 sm:w-32" style='background-image: url("https://via.placeholder.com/128");'></div>
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
<div class="flex flex-col gap-6">
<div class="border-b border-gray-200 dark:border-gray-700">
<div class="flex px-4 gap-8">
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-primary text-text-main dark:text-white pb-[13px] pt-4" href="<?= BASE_URL ?>?action=profile">
<p class="text-sm font-semibold leading-normal tracking-[0.015em]">Thông tin</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-text-secondary dark:text-white/70 pb-[13px] pt-4 hover:text-text-main dark:hover:text-white" href="<?= BASE_URL ?>?action=address">
<p class="text-sm font-semibold leading-normal tracking-[0.015em]">Địa chỉ</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-text-secondary dark:text-white/70 pb-[13px] pt-4 hover:text-text-main dark:hover:text-white" href="<?= BASE_URL ?>?action=change-password">
<p class="text-sm font-semibold leading-normal tracking-[0.015em]">Đổi mật khẩu</p>
</a>
</div>
</div>
</div>
</div>
</main>
</div>
</div>
</body>
</html>
