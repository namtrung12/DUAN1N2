<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Mã giảm giá - Chill Drink Admin</title>
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

        <main class="flex-1 ml-64 p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý Mã giảm giá</h1>
                <p class="text-slate-600">Quản lý mã theo ranking và mã đổi điểm</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
            </div>
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
                    <div class="flex items-center justify-between">
                        <div class="relative flex-1 max-w-md">
                            <input type="text" id="searchInput" placeholder="Tìm kiếm mã..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   onkeyup="searchTable()"/>
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="<?= BASE_URL ?>?action=admin-coupon-create" class="flex items-center gap-2 px-6 py-2 bg-cyan-400 text-white rounded-lg hover:bg-cyan-500 font-semibold">
                                <span class="material-symbols-outlined">add</span>
                                <span>Thêm mã</span>
                            </a>
                            <button id="deleteSelectedBtn" onclick="deleteSelected()" class="hidden items-center gap-2 px-6 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 font-semibold">
                                <span class="material-symbols-outlined">delete</span>
                                <span>Xóa</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full" id="couponTable">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 rounded border-gray-300"/>
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">MÃ</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">MÔ TẢ</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">GIÁ TRỊ</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">GIẢM TỐI ĐA</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">RANK</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">ĐIỂM ĐỔI</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">SỬ DỤNG</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">TRẠNG THÁI</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($coupons as $coupon): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="coupon-checkbox w-4 h-4 rounded border-gray-300" value="<?= $coupon['id'] ?>" onchange="updateDeleteButton()"/>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-blue-600"><?= htmlspecialchars($coupon['code'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-600"><?= !empty($coupon['description']) ? htmlspecialchars(substr($coupon['description'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($coupon['description']) > 40 ? '...' : '') : '-' ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($coupon['type'] === 'percent'): ?>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-semibold"><?= $coupon['value'] ?>%</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold"><?= number_format($coupon['value']) ?>đ</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?php if ($coupon['type'] === 'percent' && !empty($coupon['max_discount']) && $coupon['max_discount'] > 0): ?>
                                        <span class="font-semibold text-amber-600"><?= number_format($coupon['max_discount']) ?>đ</span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($coupon['required_rank']): ?>
                                        <?php
                                        $rankColors = [
                                            'bronze' => 'bg-orange-100 text-orange-700',
                                            'silver' => 'bg-gray-100 text-gray-700',
                                            'gold' => 'bg-yellow-100 text-yellow-700',
                                            'diamond' => 'bg-blue-100 text-blue-700'
                                        ];
                                        $rankNames = [
                                            'bronze' => 'Bronze',
                                            'silver' => 'Silver',
                                            'gold' => 'Gold',
                                            'diamond' => 'Kim cương'
                                        ];
                                        ?>
                                        <span class="px-2 py-1 <?= $rankColors[$coupon['required_rank']] ?> rounded text-xs font-semibold">
                                            <?= $rankNames[$coupon['required_rank']] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">Tất cả</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($coupon['is_redeemable']): ?>
                                        <span class="font-semibold text-amber-600"><?= $coupon['point_cost'] ?> điểm</span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?= $coupon['used_count'] ?>/<?= $coupon['usage_limit'] ?: '∞' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($coupon['status']): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-semibold">Tắt</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="<?= BASE_URL ?>?action=admin-coupon-edit&id=<?= $coupon['id'] ?>" 
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </a>
                                        <button onclick="deleteCoupon(<?= $coupon['id'] ?>)" 
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-xl">delete</span>
                                        </button>
                                    </div>
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
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('couponTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[1];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
                }
            }
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.coupon-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.coupon-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            deleteBtn.classList.toggle('hidden', checkboxes.length === 0);
            deleteBtn.classList.toggle('flex', checkboxes.length > 0);
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.coupon-checkbox:checked');
            if (checkboxes.length === 0) return;

            if (confirm(`Bạn có chắc muốn xóa ${checkboxes.length} mã giảm giá?`)) {
                const ids = Array.from(checkboxes).map(cb => cb.value).join(',');
                window.location.href = `<?= BASE_URL ?>?action=admin-coupon-delete-multiple&ids=${ids}`;
            }
        }

        function deleteCoupon(id) {
            if (confirm('Bạn có chắc muốn xóa mã giảm giá này?')) {
                window.location.href = `<?= BASE_URL ?>?action=admin-coupon-delete&id=${id}`;
            }
        }
    </script>
</body>
</html>
