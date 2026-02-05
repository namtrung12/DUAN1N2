<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Topping - Chill Drink Admin</title>
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
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý Topping</h1>
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
                        <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên topping..." 
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
                            <span>Thêm topping mới</span>
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full" id="toppingTable">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700 w-12">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"/>
                                </th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">ID TOPPING</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">TÊN TOPPING</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">GIÁ TOPPING</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-slate-700">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($toppings)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Không có topping nào</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($toppings as $topping): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6">
                                    <input type="checkbox" class="topping-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                                           value="<?= $topping['id'] ?>" onchange="updateDeleteButton()"/>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-600 font-medium">TP<?= str_pad($topping['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-900"><?= htmlspecialchars($topping['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-900"><?= number_format($topping['price'], 0, ',', '.') ?>đ</span>
                                </td>
                                <td class="py-4 px-6">
                                    <button onclick='openEditModal(<?= json_encode($topping) ?>)' 
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
                    <p class="text-sm text-slate-600">Hiển thị 1-<?= count($toppings) ?> trên <?= count($toppings) ?> kết quả</p>
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
    <div id="toppingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h2 id="modalTitle" class="text-2xl font-bold text-slate-900">Thêm topping mới</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="toppingForm" method="POST">
                <input type="hidden" id="toppingId" name="id"/>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Tên topping</label>
                    <input type="text" name="name" id="toppingName" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nhập tên topping"/>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Giá (VNĐ)</label>
                    <input type="number" name="price" id="toppingPrice" required min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nhập giá"/>
                </div>

                <div class="mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="status" id="toppingStatus" checked
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"/>
                        <span class="text-sm font-medium text-slate-900">Hiển thị</span>
                    </label>
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
            document.getElementById('modalTitle').textContent = 'Thêm topping mới';
            document.getElementById('toppingForm').action = '<?= BASE_URL ?>?action=admin-topping-create';
            document.getElementById('toppingId').value = '';
            document.getElementById('toppingName').value = '';
            document.getElementById('toppingPrice').value = '';
            document.getElementById('toppingStatus').checked = true;
            document.getElementById('toppingModal').classList.remove('hidden');
        }

        function openEditModal(topping) {
            document.getElementById('modalTitle').textContent = 'Chỉnh sửa topping';
            document.getElementById('toppingForm').action = '<?= BASE_URL ?>?action=admin-topping-update';
            document.getElementById('toppingId').value = topping.id;
            document.getElementById('toppingName').value = topping.name;
            document.getElementById('toppingPrice').value = topping.price;
            document.getElementById('toppingStatus').checked = topping.status == 1;
            document.getElementById('toppingModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('toppingModal').classList.add('hidden');
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.topping-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.topping-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            const selectAll = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.topping-checkbox');
            
            if (checkboxes.length > 0) {
                deleteBtn.classList.remove('hidden');
                deleteBtn.classList.add('flex');
            } else {
                deleteBtn.classList.add('hidden');
                deleteBtn.classList.remove('flex');
            }

            // Update select all checkbox state
            selectAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.topping-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                alert('Vui lòng chọn ít nhất một topping để xóa');
                return;
            }

            if (confirm(`Bạn có chắc chắn muốn xóa ${ids.length} topping đã chọn?`)) {
                window.location.href = '<?= BASE_URL ?>?action=admin-topping-delete-multiple&ids=' + ids.join(',');
            }
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('toppingTable');
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
        document.getElementById('toppingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
