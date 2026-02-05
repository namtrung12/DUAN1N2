<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục - Chill Drink Admin</title>
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
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý Danh mục</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg flex items-center justify-between">
                <span><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></span>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                    <span class="material-symbols-outlined">close</span>
                </button>
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
                <!-- Header with Search and Buttons -->
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div class="relative flex-1 max-w-md">
                        <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên danh mục..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               onkeyup="searchTable()"/>
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="deleteSelectedBtn" onclick="deleteSelected()" class="hidden items-center gap-2 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                            <span class="material-symbols-outlined">delete</span>
                            <span>Xóa đã chọn</span>
                        </button>
                        <button onclick="openAddModal()" class="flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            <span class="material-symbols-outlined">add</span>
                            <span>Thêm danh mục mới</span>
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full" id="categoryTable">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700 w-12">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"/>
                                </th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">ID DANH MỤC</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">TÊN DANH MỤC</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">SLUG</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Không có danh mục nào</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6">
                                    <input type="checkbox" class="category-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                                           value="<?= $category['id'] ?>" onchange="updateDeleteButton()"/>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-600 font-medium">CAT<?= str_pad($category['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-900"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-600"><?= htmlspecialchars($category['slug'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <button onclick='openEditModal(<?= json_encode($category) ?>)' 
                                            class="flex items-center gap-1 px-3 py-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                        <span>Sửa</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-slate-600">Hiển thị 1-<?= count($categories) ?> trên <?= count($categories) ?> kết quả</p>
                    <div class="flex items-center gap-2">
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-slate-600">chevron_left</span>
                        </button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-600 text-white font-semibold">1</button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 text-slate-600 font-semibold transition-colors">2</button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 text-slate-600 font-semibold transition-colors">3</button>
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-slate-600">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Modal -->
    <div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h2 id="modalTitle" class="text-2xl font-bold text-slate-900">Thêm danh mục mới</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="categoryForm" method="POST">
                <input type="hidden" id="categoryId" name="id"/>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Tên danh mục</label>
                    <input type="text" name="name" id="categoryName" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nhập tên danh mục"/>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Slug (tùy chọn)</label>
                    <input type="text" name="slug" id="categorySlug"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Tự động tạo nếu để trống"/>
                    <p class="text-xs text-slate-500 mt-1">VD: tra-sua, ca-phe, nuoc-ep</p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 px-6 py-3 border border-gray-300 text-slate-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm danh mục mới';
            document.getElementById('categoryForm').action = '<?= BASE_URL ?>?action=admin-category-create';
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryName').value = '';
            document.getElementById('categorySlug').value = '';
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function openEditModal(category) {
            document.getElementById('modalTitle').textContent = 'Chỉnh sửa danh mục';
            document.getElementById('categoryForm').action = '<?= BASE_URL ?>?action=admin-category-update';
            document.getElementById('categoryId').value = category.id;
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categorySlug').value = category.slug;
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.category-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.category-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            const selectAll = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.category-checkbox');
            
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
            const checkboxes = document.querySelectorAll('.category-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                alert('Vui lòng chọn ít nhất một danh mục để xóa');
                return;
            }

            if (confirm(`Bạn có chắc chắn muốn xóa ${ids.length} danh mục đã chọn?`)) {
                window.location.href = '<?= BASE_URL ?>?action=admin-category-delete-multiple&ids=' + ids.join(',');
            }
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('categoryTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[2];
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

        // Close modal when clicking outside
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
