<!DOCTYPE html>
<html class="light" lang="vi">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Ví của tôi - Chill Drink</title>
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
"background-light": "#F5F7FA",
"text-main-light": "#1F2937",
"text-secondary-light": "#6B7280",
"success": "#A7F3D0",
"success-text": "#065F46",
"failed": "#FECACA",
"failed-text": "#991B1B",
},
fontFamily: { "display": ["Poppins", "sans-serif"] },
},
},
}
</script>
</head>
<body class="font-display">
<?php require_once PATH_VIEW . 'layouts/header.php'; ?>
<div class="relative flex h-auto min-h-screen w-full flex-col bg-background-light">
<div class="layout-container flex h-full grow flex-col">
<main class="flex flex-1 justify-center py-5 px-4 sm:px-6 md:px-8">
<div class="layout-content-container flex flex-col w-full max-w-5xl flex-1">
<div class="flex flex-wrap justify-between gap-3 p-4">
<p class="text-text-main-light text-4xl font-bold leading-tight tracking-[-0.033em] min-w-72">Ví của tôi</p>
</div>
<?php if (isset($_SESSION['success'])): ?>
<div class="mx-4 mb-4 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
<?php unset($_SESSION['success']); endif; ?>
<div class="p-4">
<div class="flex flex-col sm:flex-row items-stretch justify-between gap-6 rounded-lg bg-white p-6 shadow-sm">
<div class="flex flex-col gap-6">
<div class="flex flex-wrap gap-x-12 gap-y-4">
<div class="flex flex-col gap-1">
<p class="text-text-secondary-light text-sm font-normal leading-normal">Số dư ví</p>
<p class="text-text-main-light text-3xl font-bold leading-tight"><?= number_format($wallet['balance'], 0, ',', '.') ?> VNĐ</p>
</div>
</div>
<a href="<?= BASE_URL ?>?action=wallet-deposit" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-text-main-light gap-2 text-sm font-semibold leading-normal w-fit">
<span class="material-symbols-outlined text-lg">account_balance_wallet</span>
<span class="truncate">Nạp tiền</span>
</a>
</div>
</div>
</div>
<h2 class="text-text-main-light text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-8">Lịch sử giao dịch ví</h2>
<div class="px-4 py-3">
<?php if (empty($transactions)): ?>
<div class="bg-white rounded-lg p-10 text-center shadow-sm">
<p class="text-text-secondary-light">Chưa có giao dịch nào</p>
</div>
<?php else: ?>
<div class="overflow-x-auto bg-white rounded-lg shadow-sm">
<table class="w-full min-w-[700px] text-sm text-left text-text-secondary-light">
<thead class="text-xs text-text-main-light uppercase bg-background-light border-b">
<tr>
<th class="px-6 py-4 font-semibold" scope="col">ID Giao dịch</th>
<th class="px-6 py-4 font-semibold" scope="col">Loại giao dịch</th>
<th class="px-6 py-4 font-semibold" scope="col">Số tiền</th>
<th class="px-6 py-4 font-semibold" scope="col">Ngày/Giờ</th>
</tr>
</thead>
<tbody>
<?php foreach ($transactions as $trans): ?>
<tr class="border-b">
<td class="px-6 py-4 font-medium text-text-main-light whitespace-nowrap">#WT<?= str_pad($trans['id'], 6, '0', STR_PAD_LEFT) ?></td>
<td class="px-6 py-4"><?= htmlspecialchars($trans['description'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="px-6 py-4 <?= in_array($trans['type'], ['deposit', 'refund']) ? 'text-success-text' : 'text-failed-text' ?>">
<?= in_array($trans['type'], ['deposit', 'refund']) ? '+' : '-' ?> <?= number_format(abs($trans['amount']), 0, ',', '.') ?> VNĐ
</td>
<td class="px-6 py-4"><?= date('d/m/Y, H:i', strtotime($trans['created_at'])) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>
</div>
</main>
</div>
</div>
</body>
</html>
