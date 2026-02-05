<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - Chill Drink Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <?php include PATH_VIEW . 'layouts/admin-sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8">
<div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý đơn hàng</h1>
                <p class="text-slate-600">Xem và quản lý tất cả đơn hàng</p>
            </div>
<?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['success']); endif; ?>

            <div class="bg-white rounded-2xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
<div class="flex gap-2">
                        <a href="<?= BASE_URL ?>?action=admin-orders" class="px-4 py-2 <?= !isset($_GET['status']) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-slate-600' ?> rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Tất cả</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=pending" class="px-4 py-2 <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-slate-600' ?> rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Chờ xử lý</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=processing" class="px-4 py-2 <?= (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-slate-600' ?> rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Đang xử lý</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=completed" class="px-4 py-2 <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-slate-600' ?> rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Hoàn thành</a>
                    </div>
                </div>
<div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
<tr>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">MÃ ĐƠN</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">KHÁCH HÀNG</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">EMAIL</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">TỔNG TIỀN</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">TRẠNG THÁI</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">NGÀY ĐẶT</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
<?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">Không có đơn hàng nào</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-900">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 text-sm font-semibold"><?= strtoupper(substr($order['user_name'], 0, 1)) ?></span>
                                        </div>
                                        <span class="text-slate-900"><?= htmlspecialchars($order['user_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-600"><?= htmlspecialchars($order['user_email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-900"><?= number_format($order['total'], 0, ',', '.') ?>đ</span>
                                </td>
                                <td class="py-4 px-6">
<?php
// Định nghĩa trạng thái có thể chuyển đến dựa trên trạng thái hiện tại
$currentStatus = $order['status'];
$availableStatuses = [];

switch ($currentStatus) {
    case 'pending':
        $availableStatuses = ['pending', 'processing', 'cancelled'];
        break;
    case 'processing':
        $availableStatuses = ['processing', 'shipped', 'cancelled'];
        break;
    case 'shipped':
        $availableStatuses = ['shipped', 'delivered'];
        break;
    case 'delivered':
        $availableStatuses = ['delivered', 'completed'];
        break;
    case 'completed':
        $availableStatuses = ['completed'];
        break;
    case 'cancelled':
        $availableStatuses = ['cancelled'];
        break;
}
?>
<form action="<?= BASE_URL ?>?action=admin-order-update" method="POST" class="inline" onsubmit="return confirm('Xác nhận thay đổi trạng thái đơn hàng?')">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>"/>
                                        <select name="status" onchange="this.form.submit()" class="px-3 py-1 rounded-full text-xs font-semibold border-0 focus:ring-2 focus:ring-blue-500 cursor-pointer" <?= in_array($currentStatus, ['completed', 'cancelled']) ? 'disabled' : '' ?>>
<?php if (in_array('pending', $availableStatuses)): ?>
<option value="pending" <?= $currentStatus == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
<?php endif; ?>
<?php if (in_array('processing', $availableStatuses)): ?>
<option value="processing" <?= $currentStatus == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
<?php endif; ?>
<?php if (in_array('shipped', $availableStatuses)): ?>
<option value="shipped" <?= $currentStatus == 'shipped' ? 'selected' : '' ?>>Đang giao</option>
<?php endif; ?>
<?php if (in_array('delivered', $availableStatuses)): ?>
<option value="delivered" <?= $currentStatus == 'delivered' ? 'selected' : '' ?>>Đã giao</option>
<?php endif; ?>
<?php if (in_array('completed', $availableStatuses)): ?>
<option value="completed" <?= $currentStatus == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
<?php endif; ?>
<?php if (in_array('cancelled', $availableStatuses)): ?>
<option value="cancelled" <?= $currentStatus == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
<?php endif; ?>
</select>
                                    </form>
                                </td>
                                <td class="py-4 px-6 text-slate-600"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                <td class="py-4 px-6">
                                    <button onclick="toggleOrderDetails(<?= $order['id'] ?>)" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm" id="icon-<?= $order['id'] ?>">expand_more</span>
                                        <span>Chi tiết</span>
                                    </button>
                                </td>
                            </tr>
                            <!-- Order Details Row -->
                            <tr id="details-<?= $order['id'] ?>" class="hidden bg-gray-50">
                                <td colspan="7" class="px-6 py-4">
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h4 class="font-semibold text-slate-900 mb-3">Sản phẩm trong đơn hàng</h4>
                                        <div class="space-y-3">
                                            <?php foreach ($order['items'] as $item): ?>
                                            <div class="flex items-start gap-4 p-3 bg-gray-50 rounded-lg">
                                                <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?>" 
                                                     class="w-16 h-16 object-cover rounded-lg">
                                                <div class="flex-1">
                                                    <h5 class="font-semibold text-slate-900"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></h5>
                                                    <p class="text-sm text-slate-600">Size: <?= htmlspecialchars($item['size_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-sm text-slate-600">
                                                        🧊 Đá: <?= $item['ice_level'] ?? 100 ?>% | 🍬 Đường: <?= $item['sugar_level'] ?? 100 ?>%
                                                    </p>
                                                    <?php if (!empty($item['toppings'])): ?>
                                                    <p class="text-sm text-slate-600">
                                                        Topping: <?= implode(', ', array_column($item['toppings'], 'topping_name')) ?>
                                                    </p>
                                                    <?php endif; ?>
                                                    <p class="text-sm text-slate-600">Số lượng: <?= $item['quantity'] ?></p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-slate-900"><?= number_format($item['total_price'], 0, ',', '.') ?>đ</p>
                                                    <p class="text-xs text-slate-500"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ/sp</p>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                                            <div class="text-sm text-slate-600">
                                                <p>Tạm tính: <?= number_format($order['subtotal'], 0, ',', '.') ?>đ</p>
                                                <p>Phí vận chuyển: <?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ</p>
                                                <?php if ($order['discount'] > 0): ?>
                                                <p class="text-green-600">Giảm giá: -<?= number_format($order['discount'], 0, ',', '.') ?>đ</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-slate-600">Tổng cộng</p>
                                                <p class="text-xl font-bold text-blue-600"><?= number_format($order['total'], 0, ',', '.') ?>đ</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function toggleOrderDetails(orderId) {
            const detailsRow = document.getElementById('details-' + orderId);
            const icon = document.getElementById('icon-' + orderId);
            
            if (detailsRow.classList.contains('hidden')) {
                detailsRow.classList.remove('hidden');
                icon.textContent = 'expand_less';
            } else {
                detailsRow.classList.add('hidden');
                icon.textContent = 'expand_more';
            }
        }
    </script>
</body>
</html>
