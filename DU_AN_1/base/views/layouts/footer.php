<?php
// Load settings từ database (key-value format)
$siteName = 'Chill Drink';
$siteEmail = 'support@chilldrink.vn';
$sitePhone = '1900 xxxx';
$siteAddress = 'Hà Nội, Việt Nam';
$siteLogo = null;

try {
    $pdo = new PDO("mysql:host=localhost;dbname=du_an1;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT k, v FROM settings");
    $settingsData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $siteName = $settingsData['site_name'] ?? $siteName;
    $siteEmail = $settingsData['contact_email'] ?? $siteEmail;
    $sitePhone = $settingsData['contact_phone'] ?? $sitePhone;
    $siteAddress = $settingsData['site_address'] ?? $siteAddress;
    $siteLogo = $settingsData['site_logo'] ?? $siteLogo;
} catch (Exception $e) {
    // Sử dụng giá trị mặc định nếu có lỗi
}
?>

<footer class="bg-slate-900 text-white py-12 mt-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Logo & Slogan -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <?php if (!empty($siteLogo)): ?>
                        <img src="<?= BASE_URL . $siteLogo ?>" alt="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>" class="h-10 w-auto object-contain">
                    <?php else: ?>
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-white">local_bar</span>
                        </div>
                    <?php endif; ?>
                    <span class="text-xl font-bold"><?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <p class="text-slate-400">Hương vị bùng nổ mỗi ngày</p>
            </div>

            <!-- Liên Kết -->
            <div>
                <h4 class="font-bold text-lg mb-4">Liên Kết</h4>
                <ul class="space-y-2">
                    <li><a href="<?= BASE_URL ?>" class="text-slate-400 hover:text-white transition-colors">Trang chủ</a></li>
                    <li><a href="<?= BASE_URL ?>?action=products" class="text-slate-400 hover:text-white transition-colors">Sản phẩm</a></li>
                    <li><a href="<?= BASE_URL ?>?action=orders" class="text-slate-400 hover:text-white transition-colors">Đơn hàng</a></li>
                    <li><a href="<?= BASE_URL ?>?action=loyalty" class="text-slate-400 hover:text-white transition-colors">Ưu đãi</a></li>
                </ul>
            </div>

            <!-- Hỗ Trợ -->
            <div>
                <h4 class="font-bold text-lg mb-4">Hỗ Trợ</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Chính sách đổi trả</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Điều khoản sử dụng</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Chính sách bảo mật</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Liên Hệ -->
            <div>
                <h4 class="font-bold text-lg mb-4">Liên Hệ</h4>
                <ul class="space-y-2 text-slate-400">
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">call</span>
                        <span><?= htmlspecialchars($sitePhone, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">mail</span>
                        <span><?= htmlspecialchars($siteEmail, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        <span><?= htmlspecialchars($siteAddress, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 text-center text-slate-400">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
// Prevent Google Translate from translating Material Icons
window.addEventListener('load', function() {
    document.querySelectorAll('.material-symbols-outlined').forEach(function(el) {
        el.setAttribute('translate', 'no');
        el.classList.add('notranslate');
    });
});
</script>
