<!-- Staff Sidebar -->
<?php
// Load settings for sidebar
if (!isset($siteSettings)) {
    $siteSettings = get_site_settings();
}

$siteName = $siteSettings['site_name'] ?? 'Chill Drink';
$siteLogo = $siteSettings['site_logo'] ?? '';
?>
<aside class="w-64 bg-white border-r border-gray-200 min-h-screen fixed left-0 top-0 z-40">
    <!-- Logo & Brand -->
    <div class="p-6 border-b border-gray-200">
        <a href="<?= BASE_URL ?>" class="flex items-center gap-3 mb-1" style="text-decoration: none; cursor: pointer !important; position: relative; z-index: 100;">
            <?php if (!empty($siteLogo)): ?>
                <img src="<?= BASE_URL . $siteLogo ?>" alt="<?= htmlspecialchars($siteName) ?>" style="width: 50px; height: 50px; object-fit: contain; pointer-events: none;">
            <?php else: ?>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center" style="pointer-events: none;">
                    <span class="material-symbols-outlined text-amber-600">local_bar</span>
                </div>
            <?php endif; ?>
            <div style="pointer-events: none;">
                <h2 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($siteName) ?></h2>
                <p class="text-xs text-slate-500">Staff Dashboard</p>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4">
        <ul class="space-y-1">
            <li>
                <a href="<?= BASE_URL ?>?action=staff" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (!isset($_GET['action']) || $_GET['action'] == 'staff') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?action=staff-orders" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg <?= (isset($_GET['action']) && $_GET['action'] == 'staff-orders') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-gray-50' ?> transition-colors">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                    <span class="font-medium">Quản lý Đơn hàng</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Info & Logout -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-white">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600">person</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-900 truncate"><?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-xs text-slate-500">Staff</p>
            </div>
        </div>
        <a href="<?= BASE_URL ?>?action=logout" 
           class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
            <span class="material-symbols-outlined text-lg">logout</span>
            <span class="font-medium">Đăng xuất</span>
        </a>
    </div>
</aside>
