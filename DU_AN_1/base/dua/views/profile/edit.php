<!DOCTYPE html>
<html class="light" lang="vi">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Chill Drink - Chỉnh sửa thông tin</title>
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
"text-main": "#333333",
"text-secondary": "#888888",
"error": "#dc3545",
},
fontFamily: { "display": ["Poppins", "sans-serif"] },
borderRadius: { "DEFAULT": "0.5rem", "lg": "0.75rem", "xl": "1rem", "full": "9999px" },
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
<h1 class="text-3xl font-bold mb-6">Chỉnh sửa thông tin</h1>
<form action="<?= BASE_URL ?>?action=profile-update" method="POST" class="bg-white rounded-lg p-6 shadow-sm space-y-4">
<div class="flex flex-col">
<label class="text-text-main text-base font-medium pb-2" for="name">Họ tên</label>
<input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="name" name="name" type="text" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $user['name'], ENT_QUOTES, 'UTF-8') ?>" required/>
<?php if (isset($_SESSION['errors']['name'])): ?>
<span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['name'], ENT_QUOTES, 'UTF-8') ?></span>
<?php endif; ?>
</div>
<div class="flex flex-col">
<label class="text-text-main text-base font-medium pb-2" for="email">Email</label>
<input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 bg-gray-100" id="email" type="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" disabled/>
<span class="text-text-secondary text-sm mt-1">Email không thể thay đổi</span>
</div>
<div class="flex flex-col">
<label class="text-text-main text-base font-medium pb-2" for="phone">Số điện thoại</label>
<input class="form-input w-full rounded-lg border border-gray-300 h-14 p-4 focus:outline-0 focus:ring-2 focus:ring-primary/50" id="phone" name="phone" type="text" value="<?= htmlspecialchars($_SESSION['old']['phone'] ?? $user['phone'], ENT_QUOTES, 'UTF-8') ?>" required/>
<?php if (isset($_SESSION['errors']['phone'])): ?>
<span class="text-error text-sm mt-1"><?= htmlspecialchars($_SESSION['errors']['phone'], ENT_QUOTES, 'UTF-8') ?></span>
<?php endif; ?>
</div>
<button type="submit" class="w-full h-12 bg-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Cập nhật</button>
</form>
<?php unset($_SESSION['errors'], $_SESSION['old']); ?>
</div>
</main>
</div>
</div>
</body>
</html>
