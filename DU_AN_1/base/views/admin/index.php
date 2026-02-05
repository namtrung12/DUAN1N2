<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Chill Drink Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <?php include PATH_VIEW . 'layouts/admin-sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Tổng quan</h1>
                <p class="text-slate-600">Chào mừng trở lại, Admin!</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Orders -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-slate-500 text-sm mb-2">Tổng đơn hàng</p>
                    <h3 class="text-3xl font-bold text-slate-900 mb-2"><?= number_format($totalOrders) ?></h3>
                    <p class="text-green-600 text-sm font-semibold">+12.5%</p>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-slate-500 text-sm mb-2">Tổng doanh thu</p>
                    <h3 class="text-3xl font-bold text-slate-900 mb-2"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</h3>
                    <p class="text-green-600 text-sm font-semibold">+8.2%</p>
                </div>

                <!-- Total Users -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-slate-500 text-sm mb-2">Tổng người dùng</p>
                    <h3 class="text-3xl font-bold text-slate-900 mb-2"><?= number_format($totalUsers) ?></h3>
                    <p class="text-green-600 text-sm font-semibold">+5.1%</p>
                </div>

                <!-- Best Seller -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-slate-500 text-sm mb-2">Sản phẩm bán chạy</p>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Trà sữa tr...</h3>
                    <p class="text-green-600 text-sm font-semibold">+2.0%</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Doanh thu theo tháng</h3>
                        <p class="text-2xl font-bold text-slate-900">95.000.000đ</p>
                        <p class="text-green-600 text-sm font-semibold">Tháng này +8.2%</p>
                    </div>
                    <div class="relative h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Products Chart -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Sản phẩm bán chạy</h3>
                        <p class="text-2xl font-bold text-slate-900">850 sản phẩm</p>
                        <p class="text-green-600 text-sm font-semibold">Tháng này +15.0%</p>
                    </div>
                    <div class="relative h-64">
                        <canvas id="productsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-2xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900">Đơn hàng gần đây</h3>
                        <div class="relative">
                            <input type="text" placeholder="Tìm kiếm đơn hàng..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">MÃ ĐƠN HÀNG</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">KHÁCH HÀNG</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">NGÀY ĐẶT</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">TỔNG TIỀN</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">TRẠNG THÁI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentOrders as $order): ?>
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
                                <td class="py-4 px-6 text-slate-600">
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-900"><?= number_format($order['total'], 0, ',', '.') ?>đ</span>
                                </td>
                                <td class="py-4 px-6">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'preparing' => 'bg-orange-100 text-orange-700',
                                        'shipped' => 'bg-purple-100 text-purple-700',
                                        'delivering' => 'bg-cyan-100 text-cyan-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Chờ xử lý',
                                        'processing' => 'Đang xử lý',
                                        'preparing' => 'Đang thực hiện',
                                        'shipped' => 'Đã giao ĐVVC',
                                        'delivering' => 'Đang giao',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    $colorClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700';
                                    $label = $statusLabels[$order['status']] ?? $order['status'];
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $colorClass ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCanvas = document.getElementById('revenueChart');
            if (revenueCanvas) {
                const revenueCtx = revenueCanvas.getContext('2d');
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                        datasets: [{
                            label: 'Doanh thu',
                            data: [65, 75, 70, 80, 75, 85, 80, 90, 85, 95, 90, 95],
                            borderColor: 'rgb(96, 165, 250)',
                            backgroundColor: 'rgba(96, 165, 250, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }

            // Products Chart
            const productsCanvas = document.getElementById('productsChart');
            if (productsCanvas) {
                const productsCtx = productsCanvas.getContext('2d');
                new Chart(productsCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Trà sữa', 'Cà phê', 'Nước ép', 'Trà trái cây', 'Sinh tố'],
                        datasets: [{
                            label: 'Số lượng bán',
                            data: [180, 220, 190, 160, 280],
                            backgroundColor: [
                                'rgba(191, 219, 254, 0.8)',
                                'rgba(191, 219, 254, 0.8)',
                                'rgba(191, 219, 254, 0.8)',
                                'rgba(191, 219, 254, 0.8)',
                                'rgba(59, 130, 246, 0.8)'
                            ],
                            borderRadius: 8,
                            borderSkipped: false,
                            barThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
