<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Chill Drink</title>
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
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Dashboard</h1>
                <p class="text-slate-600">Chào mừng trở lại, <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8') ?>!</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Orders -->
                <a href="<?= BASE_URL ?>?action=staff-orders" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all cursor-pointer transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600 text-2xl">receipt_long</span>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-1"><?= $totalOrders ?></h3>
                    <p class="text-slate-600 text-sm">Tổng đơn hàng</p>
                </a>

                <!-- Pending Orders -->
                <a href="<?= BASE_URL ?>?action=staff-orders&status=pending" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all cursor-pointer transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-yellow-600 text-2xl">pending</span>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-1"><?= $pendingOrders ?></h3>
                    <p class="text-slate-600 text-sm">Đơn chờ xử lý</p>
                </a>

                <!-- Processing Orders -->
                <a href="<?= BASE_URL ?>?action=staff-orders&status=processing" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all cursor-pointer transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-orange-600 text-2xl">local_shipping</span>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-1"><?= $processingOrders ?></h3>
                    <p class="text-slate-600 text-sm">Đang xử lý</p>
                </a>

                <!-- Completed Orders -->
                <a href="<?= BASE_URL ?>?action=staff-orders&status=completed" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all cursor-pointer transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-1"><?= $completedOrders ?></h3>
                    <p class="text-slate-600 text-sm">Hoàn thành</p>
                </a>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-slate-900">Đơn hàng gần đây</h2>
                    <a href="<?= BASE_URL ?>?action=staff-orders" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Xem tất cả →
                    </a>
                </div>

                <?php if (empty($recentOrders)): ?>
                    <p class="text-slate-600 text-center py-8">Chưa có đơn hàng nào</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700">Mã đơn</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700">Khách hàng</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700">Tổng tiền</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700">Trạng thái</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700">Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): 
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'shipped' => 'bg-purple-100 text-purple-700',
                                        'delivered' => 'bg-green-100 text-green-700',
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
                                ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <a href="<?= BASE_URL ?>?action=staff-orders" class="text-blue-600 hover:text-blue-700 font-medium">
                                            #<?= $order['id'] ?>
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-slate-700"><?= htmlspecialchars($order['user_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="py-3 px-4 text-sm font-semibold text-slate-900"><?= number_format($order['total'], 0, ',', '.') ?>₫</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700' ?>">
                                            <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-slate-600"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="flex items-center justify-center gap-2 mt-6">
                        <?php if ($page > 1): ?>
                            <a href="<?= BASE_URL ?>?action=staff&page=<?= $page - 1 ?>" 
                               class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                ← Trước
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= BASE_URL ?>?action=staff&page=<?= $i ?>" 
                               class="px-3 py-2 border rounded-lg text-sm <?= $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= BASE_URL ?>?action=staff&page=<?= $page + 1 ?>" 
                               class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                Sau →
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
