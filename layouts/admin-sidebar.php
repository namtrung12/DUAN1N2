<!-- Admin Sidebar -->
<?php
// Load settings for sidebar
if (!isset($siteSettings)) {
    $siteSettings = get_site_settings();
}

$siteName = $siteSettings['site_name'] ?? 'Chill Drink';
$siteLogo = $siteSettings['site_logo'] ?? '';
?>
<!-- Mobile Menu Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden" onclick="toggleSidebar()"></div>

<aside id="adminSidebar" class="w-64 bg-white border-r border-gray-200 h-screen fixed left-0 top-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col overflow-hidden">
    <!-- Logo & Brand -->
    <div class="p-6 border-b border-gray-200 flex-shrink-0">
        <a href="<?= BASE_URL ?>" class="flex items-center gap-3 mb-1" style="text-decoration: none; cursor: pointer;">
            <?php if (!empty($siteLogo)): ?>
                <img src="<?= BASE_URL . $siteLogo ?>" alt="<?= htmlspecialchars($siteName) ?>" style="width: 50px; height: 50px; object-fit: contain;">
            <?php else: ?>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600">local_bar</span>
                </div>
            <?php endif; ?>
            <div>
                <h2 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($siteName) ?></h2>
                <p class="text-xs text-slate-500">Admin Panel</p>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4 flex-1 overflow-y-auto">
        <ul class="space-y-1">
            <li>
                <a href="<?= BASE_URL ?>?action=admin" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (!isset($_GET['action']) || $_GET['action'] == 'admin') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    <span class="font-medium">Thống kê</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?action=admin-products" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && $_GET['action'] == 'admin-products') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">inventory_2</span>
                    <span class="font-medium">Quản lý sản phẩm</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?action=admin-categories" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && strpos($_GET['action'], 'admin-categor') !== false) ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">category</span>
                    <span class="font-medium">Danh mục</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?action=admin-toppings" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && strpos($_GET['action'], 'admin-topping') !== false) ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">cake</span>
                    <span class="font-medium">Topping</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?action=admin-orders" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && $_GET['action'] == 'admin-orders') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                    <span class="font-medium">Đơn hàng</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?action=admin-users" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && $_GET['action'] == 'admin-users') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">group</span>
                    <span class="font-medium">Người dùng</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?action=admin-reviews" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && strpos($_GET['action'], 'admin-review') !== false) ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">rate_review</span>
                    <span class="font-medium">Đánh giá & Bình luận</span>
                </a>
            </li>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2): ?>
            <li>
                <a href="<?= BASE_URL ?>?action=admin-coupons" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && strpos($_GET['action'], 'admin-coupon') === 0) ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">confirmation_number</span>
                    <span class="font-medium">Mã giảm giá</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Settings & Logout -->
    <div class="border-t border-gray-200 flex-shrink-0">
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2): ?>
        <div class="p-4 border-b border-gray-200">
            <a href="<?= BASE_URL ?>?action=admin-settings" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && $_GET['action'] == 'admin-settings') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                <span class="material-symbols-outlined text-xl">settings</span>
                <span class="font-medium">Cài đặt</span>
            </a>
        </div>
        <?php endif; ?>
        <div class="p-4">
            <a href="<?= BASE_URL ?>?action=logout" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-colors">
                <span class="material-symbols-outlined text-xl">logout</span>
                <span class="font-medium">Đăng xuất</span>
            </a>
        </div>
    </div>
</aside>

<!-- Mobile Menu Toggle Button -->
<button onclick="toggleSidebar()" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-lg shadow-lg border border-gray-200">
    <span class="material-symbols-outlined text-slate-700">menu</span>
</button>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>
