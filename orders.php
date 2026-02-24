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
        <main class="flex-1 lg:ml-64 p-4 sm:p-6 lg:p-8">
<div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý đơn hàng</h1>
                <p class="text-slate-600">Xem và quản lý tất cả đơn hàng</p>
            </div>
<?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['success']); endif; ?>
            <?php if (isset($_SESSION['errors'])): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); endif; ?>

            <div class="bg-white rounded-2xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
<div class="flex flex-wrap gap-2">
                        <a href="<?= BASE_URL ?>?action=admin-orders" class="px-3 py-1.5 <?= !isset($_GET['status']) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-slate-600 hover:bg-gray-200' ?> rounded-lg text-xs font-semibold transition-colors">Tất cả</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=pending" class="px-3 py-1.5 <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' ?> rounded-lg text-xs font-semibold transition-colors">Chờ xử lý</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=processing" class="px-3 py-1.5 <?= (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' ?> rounded-lg text-xs font-semibold transition-colors">Đang xử lý</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=preparing" class="px-3 py-1.5 <?= (isset($_GET['status']) && $_GET['status'] == 'preparing') ? 'bg-orange-600 text-white' : 'bg-orange-50 text-orange-700 hover:bg-orange-100' ?> rounded-lg text-xs font-semibold transition-colors">Đang thực hiện</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=shipped" class="px-3 py-1.5 <?= (isset($_GET['status']) && $_GET['status'] == 'shipped') ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' ?> rounded-lg text-xs font-semibold transition-colors">Đã giao ĐVVC</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=delivering" class="px-3 py-1.5 <?= (isset($_GET['status']) && $_GET['status'] == 'delivering') ? 'bg-cyan-600 text-white' : 'bg-cyan-50 text-cyan-700 hover:bg-cyan-100' ?> rounded-lg text-xs font-semibold transition-colors">Đang giao</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=completed" class="px-3 py-1.5 <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'bg-green-600 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100' ?> rounded-lg text-xs font-semibold transition-colors">Hoàn thành</a>
                        <a href="<?= BASE_URL ?>?action=admin-orders&status=cancelled" class="px-3 py-1.5 <?= (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' ?> rounded-lg text-xs font-semibold transition-colors">Đã hủy</a>
                    </div>
                </div>
<div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
<tr>
                                <th class="text-left py-4 px-4 text-xs font-semibold text-slate-700 w-24">MÃ ĐƠN</th>
                                <th class="text-left py-4 px-4 text-xs font-semibold text-slate-700">KHÁCH HÀNG</th>
                                <th class="text-right py-4 px-4 text-xs font-semibold text-slate-700 w-28">TỔNG TIỀN</th>
                                <th class="text-left py-4 px-4 text-xs font-semibold text-slate-700 w-36">TRẠNG THÁI</th>
                                <th class="text-left py-4 px-4 text-xs font-semibold text-slate-700 w-32">NGÀY ĐẶT</th>
                                <th class="text-center py-4 px-4 text-xs font-semibold text-slate-700 w-24">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
<?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">Không có đơn hàng nào</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-slate-900">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-blue-600 text-sm font-semibold"><?= strtoupper(substr($order['user_name'], 0, 1)) ?></span>
                                        </div>
                                        <span class="text-slate-900 text-sm"><?= htmlspecialchars($order['user_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <span class="font-semibold text-slate-900"><?= number_format($order['total'], 0, ',', '.') ?>đ</span>
                                </td>
                                <td class="py-3 px-4">
<?php
// Định nghĩa trạng thái có thể chuyển đến dựa trên trạng thái hiện tại
$currentStatus = $order['status'];
$availableStatuses = [];

switch ($currentStatus) {
    case 'pending':
        $availableStatuses = ['pending', 'processing'];
        $canCancel = true;
        break;
    case 'processing':
        $availableStatuses = ['processing', 'preparing'];
        $canCancel = true;
        break;
    case 'preparing':
        $availableStatuses = ['preparing', 'shipped'];
        break;
    case 'shipped':
        $availableStatuses = ['shipped', 'delivering'];
        break;
    case 'delivering':
        // Admin không thể chuyển sang hoàn thành - user tự xác nhận hoặc tự động sau 30 phút
        $availableStatuses = ['delivering'];
        $deliveringNote = true;
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
                                        <select name="status" onchange="this.form.submit()" class="px-2 py-1 rounded-full text-[11px] font-semibold border-0 focus:ring-2 focus:ring-blue-500 cursor-pointer" <?= in_array($currentStatus, ['completed', 'cancelled', 'delivering']) ? 'disabled' : '' ?>>
<?php if (in_array('pending', $availableStatuses)): ?>
<option value="pending" <?= $currentStatus == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
<?php endif; ?>
<?php if (in_array('processing', $availableStatuses)): ?>
<option value="processing" <?= $currentStatus == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
<?php endif; ?>
<?php if (in_array('preparing', $availableStatuses)): ?>
<option value="preparing" <?= $currentStatus == 'preparing' ? 'selected' : '' ?>>Đang thực hiện</option>
<?php endif; ?>
<?php if (in_array('shipped', $availableStatuses)): ?>
<option value="shipped" <?= $currentStatus == 'shipped' ? 'selected' : '' ?>>Đã giao ĐVVC</option>
<?php endif; ?>
<?php if (in_array('delivering', $availableStatuses)): ?>
<option value="delivering" <?= $currentStatus == 'delivering' ? 'selected' : '' ?>>Đang giao</option>
<?php endif; ?>
<?php if (in_array('completed', $availableStatuses)): ?>
<option value="completed" <?= $currentStatus == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
<?php endif; ?>
<?php if ($currentStatus == 'cancelled'): ?>
<option value="cancelled" selected>Đã hủy</option>
<?php endif; ?>
</select>
                                    </form>
                                    <?php if (isset($canCancel) && $canCancel): ?>
                                    <button onclick="openCancelModal(<?= $order['id'] ?>, '<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?>')" class="ml-2 px-2 py-1 bg-red-100 text-red-600 rounded text-[10px] font-semibold hover:bg-red-200">Hủy</button>
                                    <?php unset($canCancel); endif; ?>
                                    <?php if (isset($deliveringNote) && $deliveringNote): ?>
                                    <p class="text-[10px] text-cyan-600 mt-1">Chờ KH xác nhận hoặc tự động sau 30p</p>
                                    <?php unset($deliveringNote); endif; ?>
                                </td>
                                <td class="py-3 px-4 text-slate-600 text-sm">
                                    <div><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                    <div class="text-xs text-slate-400"><?= date('H:i', strtotime($order['created_at'])) ?></div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button onclick="toggleOrderDetails(<?= $order['id'] ?>)" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                            <!-- Order Details Row -->
                            <tr id="details-<?= $order['id'] ?>" class="hidden bg-gray-50">
                                <td colspan="6" class="px-4 py-4">
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
    
    <!-- Modal Hủy đơn hàng -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-900">Hủy đơn hàng #<span id="cancelOrderCode"></span></h3>
                <button onclick="closeCancelModal()" class="text-gray-500 hover:text-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>?action=admin-order-cancel" method="POST">
                <input type="hidden" name="order_id" id="cancelOrderId" />
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Lý do hủy đơn <span class="text-red-500">*</span></label>
                    <textarea name="cancel_reason" id="cancelReason" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeCancelModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">Đóng</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-semibold">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleOrderDetails(orderId) {
            const detailsRow = document.getElementById('details-' + orderId);
            const icon = document.getElementById('icon-' + orderId);
            
            if (detailsRow.classList.contains('hidden')) {
                detailsRow.classList.remove('hidden');
                if (icon) icon.textContent = 'expand_less';
            } else {
                detailsRow.classList.add('hidden');
                if (icon) icon.textContent = 'expand_more';
            }
        }

        function openCancelModal(orderId, orderCode) {
            document.getElementById('cancelOrderId').value = orderId;
            document.getElementById('cancelOrderCode').textContent = orderCode;
            document.getElementById('cancelReason').value = '';
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        // Đóng modal khi click bên ngoài
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
    </script>
</body>
</html>
