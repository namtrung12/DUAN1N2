<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Chill Drink Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <?php require_once PATH_VIEW . 'layouts/admin-sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 p-4 sm:p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Quản lý Người dùng</h1>
                <p class="text-slate-600">Xem và quản lý tất cả người dùng.</p>
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
                                <input type="text" id="searchInput" placeholder="Tìm theo tên, email, hoặc SĐT..." 
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <!-- Role Filter -->
                            <select id="roleFilter" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Vai trò: Tất cả</option>
                                <option value="1">Customer</option>
                                <option value="2">Admin</option>
                            </select>
                            
                            <!-- Status Filter -->
                            <select id="statusFilter" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Trạng thái: Tất cả</option>
                                <option value="1">Hoạt động</option>
                                <option value="0">Bị khóa</option>
                            </select>
                        </div>

                        <!-- Bulk Actions -->
                        <div id="bulkActions" class="hidden flex gap-2">
                            <button onclick="lockSelectedUsers()" class="flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <span class="material-symbols-outlined text-xl">lock</span>
                                <span>Khóa</span>
                            </button>
                            <button onclick="unlockSelectedUsers()" class="flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <span class="material-symbols-outlined text-xl">lock_open</span>
                                <span>Mở khóa</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID NGƯỜI DÙNG</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">TÊN NGƯỜI DÙNG</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">EMAIL</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SỐ ĐIỆN THOẠI</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">VAI TRÒ</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">TRẠNG THÁI</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="divide-y divide-gray-200">
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">Không có người dùng nào</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" class="user-checkbox rounded border-gray-300" value="<?= $user['id'] ?>">
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-slate-900"><?= $user['id'] ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <?php 
                                                $avatarUrl = !empty($user['avatar']) 
                                                    ? BASE_URL . $user['avatar'] 
                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=40&background=A0DDE6&color=fff';
                                                ?>
                                                <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-10 h-10 min-w-[40px] rounded-full object-cover border-2 border-gray-200">
                                                <div>
                                                    <div class="font-semibold text-slate-900"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <?php if ($user['id'] == $_SESSION['user']['id']): ?>
                                                        <small class="text-gray-500">(Tài khoản hiện tại)</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($user['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $roleColors = [
                                                1 => 'bg-gray-100 text-gray-700',
                                                2 => 'bg-red-100 text-red-700',
                                                3 => 'bg-blue-100 text-blue-700'
                                            ];
                                            $roleNames = [
                                                1 => 'Customer',
                                                2 => 'Admin'
                                            ];
                                            $colorClass = $roleColors[$user['role_id']] ?? 'bg-gray-100 text-gray-700';
                                            ?>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $colorClass ?>">
                                                <?= $roleNames[$user['role_id']] ?? 'User' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap <?= $user['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                                <?= $user['is_active'] ? 'Hoạt động' : 'Bị khóa' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button onclick="openEditModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>', <?= $user['role_id'] ?>)" 
                                                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Sửa vai trò">
                                                <span class="material-symbols-outlined text-xl">edit</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <?php
                $startItem = ($page - 1) * $perPage + 1;
                $endItem = min($page * $perPage, $totalUsers);
                ?>
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Hiển thị <?= $startItem ?>-<?= $endItem ?> của <?= $totalUsers ?> người dùng
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if ($page > 1): ?>
                        <a href="<?= BASE_URL ?>?action=admin-users&page=<?= $page - 1 ?>" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Trước
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                            <span class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                                <?= $i ?>
                            </span>
                            <?php elseif ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                            <a href="<?= BASE_URL ?>?action=admin-users&page=<?= $i ?>" 
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                <?= $i ?>
                            </a>
                            <?php elseif (abs($i - $page) == 3): ?>
                            <span class="px-2 text-gray-500">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="<?= BASE_URL ?>?action=admin-users&page=<?= $page + 1 ?>" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Sau
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Edit Role Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-slate-900">Thay đổi vai trò</h3>
            </div>
            <form id="editRoleForm" method="POST" action="<?= BASE_URL ?>?action=admin-user-update-role">
                <div class="p-6">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Người dùng</label>
                        <p id="editUserName" class="text-slate-900 font-semibold"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò mới</label>
                        <select name="role_id" id="editRoleId" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="1">Customer</option>
                            <option value="2">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Hủy
                    </button>
                    <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', filterUsers);
        document.getElementById('roleFilter').addEventListener('change', filterUsers);
        document.getElementById('statusFilter').addEventListener('change', filterUsers);

        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#usersTableBody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const roleMatch = !roleFilter || row.textContent.includes(getRoleName(roleFilter));
                const statusMatch = !statusFilter || (statusFilter === '1' ? row.textContent.includes('Hoạt động') : row.textContent.includes('Bị khóa'));
                
                if (text.includes(searchTerm) && roleMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function getRoleName(roleId) {
            const roles = { '1': 'Customer', '2': 'Admin' };
            return roles[roleId] || '';
        }

        // Select all checkbox
        document.getElementById('selectAll')?.addEventListener('change', function() {
            document.querySelectorAll('.user-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            toggleBulkActions();
        });

        // Show/hide bulk actions when checkboxes are selected
        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkActions);
        });

        function toggleBulkActions() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            
            if (checkedBoxes.length > 0) {
                bulkActions.classList.remove('hidden');
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        // Lock selected users
        function lockSelectedUsers() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const userIds = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (userIds.length === 0) {
                alert('Vui lòng chọn ít nhất một người dùng');
                return;
            }

            if (confirm(`Bạn có chắc muốn khóa ${userIds.length} tài khoản đã chọn?\n\nLưu ý: Bạn không thể khóa tài khoản của chính mình.`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>?action=admin-users-lock-multiple';
                
                userIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Unlock selected users
        function unlockSelectedUsers() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const userIds = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (userIds.length === 0) {
                alert('Vui lòng chọn ít nhất một người dùng');
                return;
            }

            if (confirm(`Bạn có chắc muốn mở khóa ${userIds.length} tài khoản đã chọn?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>?action=admin-users-unlock-multiple';
                
                userIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Edit role modal functions
        function openEditModal(userId, userName, roleId) {
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUserName').textContent = userName;
            document.getElementById('editRoleId').value = roleId;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
