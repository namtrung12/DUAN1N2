<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn hàng - Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <?php require_once PATH_VIEW . 'layouts/staff-sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý Đơn hàng</h1>
                <p class="text-slate-600">Xem, lọc và cập nhật trạng thái các đơn hàng tại đây.</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg flex items-center justify-between">
                    <span><?= $_SESSION['success'] ?></span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <div><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm">
                <!-- Header with Search and Filters -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <!-- Search -->
                        <div class="relative flex-1 max-w-md">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input type="text" id="searchInput" placeholder="Tìm kiếm theo ID, tên khách hàng..." 
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Status Filter -->
                        <select id="statusFilter" onchange="filterByStatus(this.value)" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Trạng thái đơn hàng: Tất cả</option>
                            <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="processing" <?= (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'selected' : '' ?>>Đang xử lý</option>
                            <option value="shipped" <?= (isset($_GET['status']) && $_GET['status'] == 'shipped') ? 'selected' : '' ?>>Đang giao</option>
                            <option value="delivered" <?= (isset($_GET['status']) && $_GET['status'] == 'delivered') ? 'selected' : '' ?>>Đã giao</option>
                            <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                            <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID ĐƠN HÀNG</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">KHÁCH HÀNG</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">TỔNG TIỀN</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PHƯƠNG THỨC TT</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">TRẠNG THÁI</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">THỜI GIAN TẠO</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Không có đơn hàng nào</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                    // Định nghĩa màu nền và viền cho từng trạng thái
                                    $statusBgColors = [
                                        'pending' => 'bg-yellow-50 hover:bg-yellow-100',
                                        'processing' => 'bg-blue-50 hover:bg-blue-100',
                                        'shipped' => 'bg-purple-50 hover:bg-purple-100',
                                        'delivered' => 'bg-indigo-50 hover:bg-indigo-100',
                                        'completed' => 'bg-green-50 hover:bg-green-100',
                                        'cancelled' => 'bg-red-50 hover:bg-red-100'
                                    ];
                                    $statusBorderColors = [
                                        'pending' => 'border-yellow-400',
                                        'processing' => 'border-blue-400',
                                        'shipped' => 'border-purple-400',
                                        'delivered' => 'border-indigo-400',
                                        'completed' => 'border-green-400',
                                        'cancelled' => 'border-red-400'
                                    ];
                                    $rowBgClass = $statusBgColors[$order['status']] ?? 'bg-gray-50 hover:bg-gray-100';
                                    $borderClass = $statusBorderColors[$order['status']] ?? 'border-gray-400';
                                    ?>
                                    <tr class="transition-colors <?= $rowBgClass ?> border-l-4 <?= $borderClass ?> border-t-2 border-r-2 border-b-0">
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-slate-900">#CD<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($order['user_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-6 py-4 text-slate-900 font-semibold"><?= number_format($order['total'], 0, ',', '.') ?>đ</td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $paymentLabels = [
                                                'cod' => 'Tiền mặt',
                                                'vnpay' => 'VNPay',
                                                'wallet' => 'Ví'
                                            ];
                                            ?>
                                            <span class="text-slate-600"><?= $paymentLabels[$order['payment_method']] ?? $order['payment_method'] ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'processing' => 'bg-blue-100 text-blue-700',
                                                'shipped' => 'bg-purple-100 text-purple-700',
                                                'delivered' => 'bg-indigo-100 text-indigo-700',
                                                'completed' => 'bg-green-100 text-green-700',
                                                'cancelled' => 'bg-red-100 text-red-700'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Chờ xử lý',
                                                'processing' => 'Đang xử lý',
                                                'shipped' => 'Đang giao',
                                                'delivered' => 'Đã giao',
                                                'completed' => 'Hoàn thành',
                                                'cancelled' => 'Đã hủy'
                                            ];
                                            $colorClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700';
                                            ?>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap <?= $colorClass ?>">
                                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600"><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></td>
                                        <td class="px-6 py-4">
                                            <?php
                                            // Xác định trạng thái tiếp theo
                                            $nextStatus = null;
                                            $nextLabel = '';
                                            $buttonColor = '';
                                            
                                            switch($order['status']) {
                                                case 'pending':
                                                    $nextStatus = 'processing';
                                                    $nextLabel = 'Bắt đầu xử lý';
                                                    $buttonColor = 'bg-blue-600 hover:bg-blue-700';
                                                    break;
                                                case 'processing':
                                                    $nextStatus = 'shipped';
                                                    $nextLabel = 'Giao hàng';
                                                    $buttonColor = 'bg-purple-600 hover:bg-purple-700';
                                                    break;
                                                case 'shipped':
                                                    $nextStatus = 'delivered';
                                                    $nextLabel = 'Đã giao';
                                                    $buttonColor = 'bg-indigo-600 hover:bg-indigo-700';
                                                    break;
                                                case 'delivered':
                                                    $nextStatus = 'completed';
                                                    $nextLabel = 'Hoàn thành';
                                                    $buttonColor = 'bg-green-600 hover:bg-green-700';
                                                    break;
                                                case 'completed':
                                                    $nextLabel = 'Đã hoàn thành';
                                                    $buttonColor = 'bg-gray-400 cursor-not-allowed';
                                                    break;
                                                case 'cancelled':
                                                    $nextLabel = 'Đã hủy';
                                                    $buttonColor = 'bg-gray-400 cursor-not-allowed';
                                                    break;
                                            }
                                            
                                            if ($nextStatus): ?>
                                                <form action="<?= BASE_URL ?>?action=staff-order-update-status" method="POST" class="inline">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <input type="hidden" name="status" value="<?= $nextStatus ?>">
                                                    <button type="submit" 
                                                            class="px-4 py-2 text-white text-sm font-semibold rounded-lg transition-colors <?= $buttonColor ?> flex items-center gap-2"
                                                            onclick="return confirm('Chuyển đơn hàng sang trạng thái: <?= $nextLabel ?>?')">
                                                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                                        <span><?= $nextLabel ?></span>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button disabled class="px-4 py-2 text-white text-sm font-semibold rounded-lg <?= $buttonColor ?> flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-lg">check_circle</span>
                                                    <span><?= $nextLabel ?></span>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <!-- Order Details Row - Always Visible -->
                                    <tr class="<?= $rowBgClass ?> border-l-4 <?= $borderClass ?> border-b-2 border-r-2 border-t-0">
                                        <td colspan="7" class="px-6 py-4 pb-6">
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
                                    <!-- Spacer between orders -->
                                    <tr class="h-3 bg-transparent">
                                        <td colspan="7" class="p-0 border-0"></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Update Status Modal -->
    <div id="updateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-slate-900">Cập nhật trạng thái đơn hàng</h3>
            </div>
            <form id="updateForm" method="POST" action="<?= BASE_URL ?>?action=staff-update-order">
                <div class="p-6">
                    <input type="hidden" name="order_id" id="updateOrderId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái mới</label>
                        <select name="status" id="updateStatus" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="processing">Đang xử lý</option>
                            <option value="shipped">Đang giao</option>
                            <option value="delivered">Đã giao</option>
                            <option value="completed">Hoàn thành</option>
                        </select>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Hủy
                    </button>
                    <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterByStatus(status) {
            window.location.href = '<?= BASE_URL ?>?action=staff-orders' + (status ? '&status=' + status : '');
        }

        function openUpdateModal(orderId, currentStatus) {
            document.getElementById('updateOrderId').value = orderId;
            
            // Định nghĩa trạng thái có thể chuyển đến
            const statusOptions = {
                'pending': [
                    {value: 'pending', label: 'Chờ xử lý'},
                    {value: 'processing', label: 'Đang xử lý'}
                ],
                'processing': [
                    {value: 'processing', label: 'Đang xử lý'},
                    {value: 'shipped', label: 'Đang giao'}
                ],
                'shipped': [
                    {value: 'shipped', label: 'Đang giao'},
                    {value: 'delivered', label: 'Đã giao'}
                ],
                'delivered': [
                    {value: 'delivered', label: 'Đã giao'},
                    {value: 'completed', label: 'Hoàn thành'}
                ],
                'completed': [
                    {value: 'completed', label: 'Hoàn thành'}
                ],
                'cancelled': [
                    {value: 'cancelled', label: 'Đã hủy'}
                ]
            };
            
            // Cập nhật dropdown
            const select = document.getElementById('updateStatus');
            select.innerHTML = '';
            
            const options = statusOptions[currentStatus] || [];
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                option.selected = opt.value === currentStatus;
                select.appendChild(option);
            });
            
            // Disable nếu đã hoàn thành hoặc hủy
            select.disabled = currentStatus === 'completed' || currentStatus === 'cancelled';
            
            document.getElementById('updateModal').classList.remove('hidden');
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('updateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUpdateModal();
            }
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
