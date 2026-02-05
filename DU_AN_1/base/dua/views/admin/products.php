<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sản phẩm - Chill Drink Admin</title>
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
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý Sản Phẩm</h1>
                <p class="text-slate-600">Xem, thêm, sửa, và xóa các sản phẩm của bạn.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm">
                <!-- Header with Search, Filter and Buttons -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="relative flex-1 max-w-md">
                                <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên sản phẩm..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       onkeyup="searchTable()"/>
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                            </div>
                            <select id="categoryFilter" onchange="filterByCategory()" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Lọc theo danh mục</option>
                                <?php 
                                $categoryModel = new Category();
                                $categories = $categoryModel->getAll();
                                foreach ($categories as $cat): 
                                ?>
                                <option value="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="<?= BASE_URL ?>?action=admin-product-create" class="flex items-center gap-2 px-6 py-2 bg-cyan-400 text-white rounded-lg hover:bg-cyan-500 transition-colors font-semibold">
                                <span class="material-symbols-outlined">add</span>
                                <span>Thêm sản phẩm</span>
                            </a>
                            <button id="deleteSelectedBtn" onclick="deleteSelected()" class="hidden items-center gap-2 px-6 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors font-semibold">
                                <span class="material-symbols-outlined">delete</span>
                                <span>Xóa</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full" id="productTable">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700 w-12">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"/>
                                </th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">ID SẢN PHẨM</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">HÌNH ẢNH</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">TÊN SẢN PHẨM</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">DANH MỤC</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">GIÁ</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">SIZE</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">TOPPING</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-500">Không có sản phẩm nào</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($products as $product): ?>
                            <?php 
                            $productModel = new Product();
                            $sizes = $productModel->getSizes($product['id']);
                            $toppings = $productModel->getToppings($product['id']);
                            $minPrice = !empty($sizes) ? min(array_column($sizes, 'price')) : 0;
                            $sizeNames = !empty($sizes) ? array_unique(array_column($sizes, 'size_name')) : [];
                            $toppingNames = !empty($toppings) ? array_slice(array_column($toppings, 'name'), 0, 2) : [];
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6">
                                    <input type="checkbox" class="product-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                                           value="<?= $product['id'] ?>" onchange="updateDeleteButton()"/>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-600 font-medium">CD-<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <img class="w-12 h-12 object-cover rounded-lg shadow-sm" 
                                         src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>"
                                         onerror="this.src='https://via.placeholder.com/48x48?text=No+Image'"/>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-900"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-600"><?= htmlspecialchars($product['category_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-900 font-medium"><?= number_format($minPrice, 0, ',', '.') ?>đ</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-600"><?= !empty($sizeNames) ? implode(', ', $sizeNames) : 'N/A' ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-600"><?= !empty($toppingNames) ? implode(', ', $toppingNames) : 'Không' ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <a href="<?= BASE_URL ?>?action=admin-product-edit&id=<?= $product['id'] ?>" 
                                       class="flex items-center gap-1 px-3 py-1.5 text-slate-600 hover:bg-gray-100 rounded-lg transition-colors font-medium">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                        <span>Sửa</span>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-slate-600">Hiển thị 1-<?= count($products) ?> trên <?= count($products) ?> kết quả</p>
                    <div class="flex items-center gap-2">
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-slate-600">chevron_left</span>
                        </button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-cyan-400 text-white font-semibold">1</button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 text-slate-600 font-semibold transition-colors">2</button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 text-slate-600 font-semibold transition-colors">3</button>
                        <span class="px-2 text-slate-400">...</span>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 text-slate-600 font-semibold transition-colors">10</button>
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-slate-600">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            const selectAll = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.product-checkbox');
            
            if (checkboxes.length > 0) {
                deleteBtn.classList.remove('hidden');
                deleteBtn.classList.add('flex');
            } else {
                deleteBtn.classList.add('hidden');
                deleteBtn.classList.remove('flex');
            }

            selectAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm để xóa');
                return;
            }

            if (confirm(`Bạn có chắc chắn muốn xóa ${ids.length} sản phẩm đã chọn?`)) {
                window.location.href = '<?= BASE_URL ?>?action=admin-product-delete-multiple&ids=' + ids.join(',');
            }
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('productTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[3];
                if (nameCell) {
                    const txtValue = nameCell.textContent || nameCell.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }

        function filterByCategory() {
            const select = document.getElementById('categoryFilter');
            const filter = select.value.toLowerCase();
            const table = document.getElementById('productTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const categoryCell = rows[i].getElementsByTagName('td')[4];
                if (categoryCell) {
                    const txtValue = categoryCell.textContent || categoryCell.innerText;
                    if (filter === '' || txtValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>
