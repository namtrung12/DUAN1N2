<!-- Header Navigation -->
<?php
$siteSettings = get_site_settings();
$siteName = $siteSettings['site_name'] ?? 'Chill Drink';
$siteLogo = $siteSettings['site_logo'] ?? '';
?>
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="<?= BASE_URL ?>" class="flex items-center gap-3 mr-12" style="text-decoration: none; cursor: pointer !important; position: relative; z-index: 100;">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?= BASE_URL . $siteLogo ?>" alt="<?= htmlspecialchars($siteName) ?>" style="width: 60px; height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-2xl">local_bar</span>
                    </div>
                <?php endif; ?>
                <span class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($siteName) ?></span>
            </a>

            <!-- Navigation Menu -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="<?= BASE_URL ?>" class="text-slate-700 hover:text-primary font-medium transition-colors">Trang Chủ</a>
                <a href="<?= BASE_URL ?>?action=products" class="text-slate-700 hover:text-primary font-medium transition-colors">Sản Phẩm</a>
                <a href="<?= BASE_URL ?>?action=orders" class="text-slate-700 hover:text-primary font-medium transition-colors">Đơn Hàng</a>
                <a href="<?= BASE_URL ?>?action=loyalty" class="text-slate-700 hover:text-primary font-medium transition-colors">Ưu Đãi</a>
            </nav>

            <!-- Search Bar -->
            <div class="hidden md:flex items-center flex-1 max-w-md mx-8">
                <form action="<?= BASE_URL ?>" method="GET" class="relative w-full" id="searchForm">
                    <input type="hidden" name="action" value="products" />
                    <input 
                        type="text"
                        name="search"
                        id="searchInput"
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>"
                        placeholder="Tìm kiếm đồ uống..." 
                        class="w-full h-10 pl-10 pr-10 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary"
                        autocomplete="off"
                        oninput="handleSearchInput(this.value)"
                    />
                    <button type="submit" class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary cursor-pointer">search</button>
                    <button type="button" id="clearSearch" onclick="clearSearchInput()" class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 cursor-pointer hidden">close</button>
                    
                    <!-- Search Suggestions Dropdown -->
                    <div id="searchSuggestions" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-lg border border-gray-200 hidden max-h-96 overflow-y-auto z-50">
                        <!-- Suggestions will be inserted here -->
                    </div>
                </form>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- Cart -->
                <a href="<?= BASE_URL ?>?action=cart" class="relative p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-slate-700">shopping_cart</span>
                    <?php if (isset($_SESSION['user'])): ?>
                        <?php 
                        $cartModel = new Cart();
                        $cartCount = $cartModel->countItems($_SESSION['user']['id']);
                        if ($cartCount > 0):
                        ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                            <?= $cartCount > 9 ? '9+' : $cartCount ?>
                        </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </a>

                <!-- User Account -->
                <?php if (isset($_SESSION['user'])): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-slate-700">account_circle</span>
                        <span class="hidden lg:block text-slate-700 font-medium">Tài khoản</span>
                        <span class="material-symbols-outlined text-slate-700 text-sm">expand_more</span>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <div class="p-4 border-b border-gray-200">
                            <p class="font-semibold text-slate-900"><?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="py-2">
                            <a href="<?= BASE_URL ?>?action=profile" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-slate-600">person</span>
                                <span class="text-slate-700">Hồ Sơ</span>
                            </a>
                            <a href="<?= BASE_URL ?>?action=orders" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-slate-600">receipt_long</span>
                                <span class="text-slate-700">Đơn hàng của tôi</span>
                            </a>
                            <a href="<?= BASE_URL ?>?action=loyalty" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-slate-600">stars</span>
                                <span class="text-slate-700">Điểm thưởng</span>
                            </a>
                            <a href="<?= BASE_URL ?>?action=wallet" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-slate-600">account_balance_wallet</span>
                                <span class="text-slate-700">Ví của tôi</span>
                            </a>
                            <?php if ($_SESSION['user']['role_id'] == 2): ?>
                            <a href="<?= BASE_URL ?>?action=admin" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-slate-600">admin_panel_settings</span>
                                <span class="text-slate-700">Quản trị</span>
                            </a>
                            <?php elseif ($_SESSION['user']['role_id'] == 3): ?>
                            <a href="<?= BASE_URL ?>?action=staff" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-slate-600">work</span>
                                <span class="text-slate-700">Staff Dashboard</span>
                            </a>
                            <?php endif; ?>
                        </div>
                        <div class="border-t border-gray-200 py-2">
                            <a href="<?= BASE_URL ?>?action=logout" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors text-red-600">
                                <span class="material-symbols-outlined">logout</span>
                                <span>Đăng xuất</span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="flex items-center gap-2">
                    <a href="<?= BASE_URL ?>?action=login" class="px-4 py-2 text-slate-700 hover:text-primary font-medium transition-colors">
                        Đăng nhập
                    </a>
                    <a href="<?= BASE_URL ?>?action=register" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-medium transition-colors">
                        Đăng ký
                    </a>
                </div>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button class="lg:hidden p-2 hover:bg-gray-100 rounded-lg" onclick="toggleMobileMenu()">
                    <span class="material-symbols-outlined text-slate-700">menu</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden border-t border-gray-200">
        <div class="px-4 py-4 space-y-2">
            <!-- Mobile Search -->
            <form action="<?= BASE_URL ?>" method="GET" class="relative mb-4">
                <input type="hidden" name="action" value="products" />
                <input 
                    type="text"
                    name="search"
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>"
                    placeholder="Tìm kiếm đồ uống..." 
                    class="w-full h-10 pl-10 pr-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/50"
                />
                <button type="submit" class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary cursor-pointer">search</button>
            </form>
            
            <a href="<?= BASE_URL ?>" class="block px-4 py-2 text-slate-700 hover:bg-gray-50 rounded-lg">Trang Chủ</a>
            <a href="<?= BASE_URL ?>?action=products" class="block px-4 py-2 text-slate-700 hover:bg-gray-50 rounded-lg">Sản Phẩm</a>
            <a href="<?= BASE_URL ?>?action=orders" class="block px-4 py-2 text-slate-700 hover:bg-gray-50 rounded-lg">Đơn Hàng</a>
            <a href="<?= BASE_URL ?>?action=loyalty" class="block px-4 py-2 text-slate-700 hover:bg-gray-50 rounded-lg">Ưu Đãi</a>
        </div>
    </div>
</header>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}
</script>


<script>
// Search Autocomplete
let searchTimeout;
const searchInput = document.getElementById('searchInput');
const clearBtn = document.getElementById('clearSearch');
const suggestionsBox = document.getElementById('searchSuggestions');

function handleSearchInput(value) {
    // Show/hide clear button
    if (value.length > 0) {
        clearBtn?.classList.remove('hidden');
    } else {
        clearBtn?.classList.add('hidden');
        suggestionsBox?.classList.add('hidden');
        return;
    }

    // Debounce search
    clearTimeout(searchTimeout);
    
    if (value.length < 2) {
        suggestionsBox?.classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(() => {
        fetchSuggestions(value);
    }, 300);
}

async function fetchSuggestions(keyword) {
    try {
        const response = await fetch(`<?= BASE_URL ?>api/search-suggestions.php?q=${encodeURIComponent(keyword)}`);
        const products = await response.json();
        
        if (products.length > 0) {
            displaySuggestions(products);
        } else {
            suggestionsBox.innerHTML = '<div class="p-4 text-center text-gray-500">Không tìm thấy sản phẩm</div>';
            suggestionsBox.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Search error:', error);
    }
}

function displaySuggestions(products) {
    const html = products.map(product => `
        <a href="<?= BASE_URL ?>?action=product-detail&id=${product.id}" 
           class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
            <img src="<?= BASE_URL ?>assets/uploads/${product.image}" 
                 alt="${product.name}"
                 class="w-12 h-12 object-cover rounded-lg"
                 onerror="this.src='https://via.placeholder.com/48'"/>
            <div class="flex-1">
                <h4 class="font-semibold text-sm text-slate-900">${product.name}</h4>
                <p class="text-xs text-gray-500">${product.category_name || ''}</p>
            </div>
            <span class="text-sm font-bold text-primary">${formatPrice(product.min_price)}đ</span>
        </a>
    `).join('');
    
    suggestionsBox.innerHTML = html;
    suggestionsBox.classList.remove('hidden');
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

function clearSearchInput() {
    if (searchInput) {
        searchInput.value = '';
        clearBtn?.classList.add('hidden');
        suggestionsBox?.classList.add('hidden');
        searchInput.focus();
    }
}

// Close suggestions when clicking outside
document.addEventListener('click', (e) => {
    if (!document.getElementById('searchForm')?.contains(e.target)) {
        suggestionsBox?.classList.add('hidden');
    }
});

// Show clear button on page load if there's value
if (searchInput?.value.length > 0) {
    clearBtn?.classList.remove('hidden');
}
</script>
