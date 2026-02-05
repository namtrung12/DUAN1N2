<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đánh giá - Chill Drink Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <?php require_once PATH_VIEW . 'layouts/admin-sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 p-4 sm:p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý Đánh giá & Bình luận</h1>
                <p class="text-slate-600">Xem và quản lý tất cả đánh giá và bình luận của khách hàng.</p>
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
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg flex items-center justify-between">
                    <div>
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <div><?= $error ?></div>
                        <?php endforeach; ?>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm">
                <!-- Header with Search and Filters -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1">
                            <!-- Search -->
                            <div class="relative flex-1 max-w-md">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                                <input type="text" id="searchInput" placeholder="Tìm theo tên sản phẩm, người dùng..." 
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <!-- Status Filter -->
                            <a href="<?= BASE_URL ?>?action=admin-reviews" 
                               class="px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors <?= (!isset($_GET['status']) || $_GET['status'] === '') ? 'bg-blue-50 text-blue-600 border-blue-300' : 'text-gray-700' ?>">
                                Tất cả
                            </a>
                            <a href="<?= BASE_URL ?>?action=admin-reviews&status=1" 
                               class="px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'bg-green-50 text-green-600 border-green-300' : 'text-gray-700' ?>">
                                Đang hiển thị
                            </a>
                            <a href="<?= BASE_URL ?>?action=admin-reviews&status=0" 
                               class="px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'bg-red-50 text-red-600 border-red-300' : 'text-gray-700' ?>">
                                Đã ẩn
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">SẢN PHẨM</th>
                                <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">NGƯỜI DÙNG</th>
                                <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">ĐÁNH GIÁ</th>
                                <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">BÌNH LUẬN</th>
                                <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">NGÀY TẠO</th>
                                <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">TRẠNG THÁI</th>
                                <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody id="reviewsTableBody" class="divide-y divide-gray-200">
                            <?php if (empty($reviews)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">Không có đánh giá nào</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reviews as $review): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <span class="font-semibold text-slate-900 text-sm">#<?= $review['id'] ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-900 text-sm max-w-[150px] truncate"><?= htmlspecialchars($review['product_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="font-medium text-slate-900 text-sm"><?= htmlspecialchars($review['user_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
                                                <div class="text-xs text-gray-500 truncate max-w-[180px]"><?= htmlspecialchars($review['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-0.5">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="material-symbols-outlined text-lg <?= $i <= $review['rating'] ? 'text-yellow-500' : 'text-gray-300' ?>">
                                                        star
                                                    </span>
                                                <?php endfor; ?>
                                                <span class="ml-1 text-xs text-gray-600">(<?= $review['rating'] ?>/5)</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="max-w-[200px]">
                                                <p class="text-slate-600 text-xs line-clamp-2"><?= htmlspecialchars($review['comment'] ?? 'Không có bình luận', ENT_QUOTES, 'UTF-8') ?></p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">
                                            <?= date('d/m/Y', strtotime($review['created_at'])) ?><br/>
                                            <span class="text-gray-400"><?= date('H:i', strtotime($review['created_at'])) ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded whitespace-nowrap <?= $review['status'] == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                                <?= $review['status'] == 1 ? 'Đang hiện' : 'Đã ẩn' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <?php if ($review['status'] == 1): ?>
                                                    <form method="POST" action="<?= BASE_URL ?>?action=admin-review-update-status" class="inline">
                                                        <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                        <input type="hidden" name="status" value="0">
                                                        <button type="submit" 
                                                                onclick="return confirm('Bạn có chắc muốn ẩn đánh giá này?')"
                                                                class="p-2 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors" 
                                                                title="Ẩn đánh giá">
                                                            <span class="material-symbols-outlined text-xl">visibility_off</span>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="<?= BASE_URL ?>?action=admin-review-update-status" class="inline">
                                                        <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                        <input type="hidden" name="status" value="1">
                                                        <button type="submit" 
                                                                onclick="return confirm('Bạn có chắc muốn hiển thị đánh giá này?')"
                                                                class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" 
                                                                title="Hiển thị đánh giá">
                                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Hiển thị 1-<?= count($reviews) ?> của <?= count($reviews) ?> đánh giá
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#reviewsTableBody tr');

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


