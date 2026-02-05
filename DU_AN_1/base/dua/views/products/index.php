<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Thực Đơn - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet"/>
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
        body { 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#47b4eb",
                        "background-light": "#f6f7f8",
                    },
                    fontFamily: { 
                        "sans": ["-apple-system", "BlinkMacSystemFont", "Segoe UI", "Roboto", "Helvetica Neue", "Arial", "sans-serif"],
                        "display": ["-apple-system", "BlinkMacSystemFont", "Segoe UI", "Roboto", "sans-serif"]
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-white">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="<?= BASE_URL ?>" class="hover:text-primary">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-900 font-medium">Menu</span>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filter -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-gray-50 rounded-2xl p-6 sticky top-24">
                    <!-- Category Filter -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Bộ lọc</h3>
                        
                        <h4 class="font-semibold text-slate-900 mb-3">Danh mục</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="category" value="" <?= !isset($_GET['category_id']) ? 'checked' : '' ?> 
                                       onchange="window.location.href='<?= BASE_URL ?>?action=products'"
                                       class="w-5 h-5 text-cyan-400 focus:ring-cyan-400"/>
                                <span class="text-slate-700 group-hover:text-primary">Tất cả sản phẩm</span>
                            </label>
                            <?php foreach ($categories as $cat): ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="category" value="<?= $cat['id'] ?>" 
                                       <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'checked' : '' ?>
                                       onchange="window.location.href='<?= BASE_URL ?>?action=products-by-category&category_id=<?= $cat['id'] ?>'"
                                       class="w-5 h-5 text-cyan-400 focus:ring-cyan-400"/>
                                <span class="text-slate-700 group-hover:text-primary"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-8">
                        <h4 class="font-semibold text-slate-900 mb-3">Mức giá</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm text-slate-600">
                                <span id="minPriceLabel">0đ</span>
                                <span id="maxPriceLabel">100.000đ</span>
                            </div>
                            <div class="relative">
                                <input type="range" id="minPrice" min="0" max="100000" value="0" step="5000"
                                       class="absolute w-full h-2 bg-transparent appearance-none cursor-pointer z-10"
                                       style="pointer-events: none;"
                                       oninput="updatePriceFilter(event)"/>
                                <input type="range" id="maxPrice" min="0" max="100000" value="100000" step="5000"
                                       class="absolute w-full h-2 bg-transparent appearance-none cursor-pointer z-20"
                                       oninput="updatePriceFilter(event)"/>
                                <div class="relative w-full h-2 bg-gray-200 rounded-lg">
                                    <div id="priceRange" class="absolute h-2 bg-cyan-400 rounded-lg"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500 mt-2">
                                <span> <strong id="minPriceValue">0đ</strong></span>
                                <span> <strong id="maxPriceValue">100.000đ</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Flavor Filter -->
                    <div class="mb-8">
                        <h4 class="font-semibold text-slate-900 mb-3">Hương vị</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="flavor" value="tran-chau" class="w-5 h-5 text-cyan-400 focus:ring-cyan-400"/>
                                <span class="text-slate-700 group-hover:text-primary">Trân châu</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="flavor" value="pudding" class="w-5 h-5 text-cyan-400 focus:ring-cyan-400"/>
                                <span class="text-slate-700 group-hover:text-primary">Pudding</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="flavor" value="trai-cay" class="w-5 h-5 text-cyan-400 focus:ring-cyan-400"/>
                                <span class="text-slate-700 group-hover:text-primary">Trái cây</span>
                            </label>
                        </div>
                    </div>

                    <!-- Apply Button -->
                    <button class="w-full py-3 bg-cyan-400 text-white rounded-lg font-semibold hover:bg-cyan-500 transition-colors">
                        Áp dụng
                    </button>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="flex-1">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900">
                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                Kết quả tìm kiếm: "<?= htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') ?>"
                            <?php else: ?>
                                Thực Đơn
                            <?php endif; ?>
                        </h1>
                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <p class="text-slate-600 text-sm mt-1">
                                Tìm thấy <?= count($products) ?> sản phẩm
                                <a href="<?= BASE_URL ?>?action=products" class="text-primary hover:underline ml-2">← Xem tất cả</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                <div class="text-center py-20">
                    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">search_off</span>
                    <p class="text-slate-500 text-lg">Không tìm thấy sản phẩm nào</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($products as $product): ?>
                    <?php 
                    $sizes = (new Product())->getSizes($product['id']);
                    $minPrice = !empty($sizes) ? min(array_column($sizes, 'price')) : 0;
                    ?>
                    <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $product['id'] ?>" 
                       class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group block"
                       data-price="<?= $minPrice ?>">
                        <div class="relative h-56 bg-gray-100 overflow-hidden">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" 
                                 alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                 onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'"/>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-lg text-slate-900 mb-2 line-clamp-1"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="product-price text-primary font-bold text-xl mb-1">Từ <?= number_format($minPrice, 0, ',', '.') ?>đ</p>
                            <p class="text-gray-400 text-sm mb-4">S/M/L</p>
                            <div class="w-full text-center py-3 bg-cyan-100 text-cyan-600 rounded-lg font-semibold group-hover:bg-cyan-200 transition-colors">
                                Thêm vào giỏ
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-center gap-2 mt-12">
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
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php require_once PATH_VIEW . 'layouts/footer.php'; ?>

    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
    <script>
       
const minPrice = document.getElementById('minPrice');
const maxPrice = document.getElementById('maxPrice');
const minPriceLabel = document.getElementById('minPriceLabel');
const maxPriceLabel = document.getElementById('maxPriceLabel');
const minPriceValue = document.getElementById('minPriceValue');
const maxPriceValue = document.getElementById('maxPriceValue');
const priceRange = document.getElementById('priceRange');

// Hàm format tiền tệ
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
}

// Hàm xử lý chính
function updatePriceFilter(e) {
    let minVal = parseInt(minPrice.value);
    let maxVal = parseInt(maxPrice.value);
    const priceGap = 5000; // Khoảng cách tối thiểu giữa 2 nút

    // --- LOGIC SỬA LỖI ---
    // Kiểm tra nếu khoảng cách nhỏ hơn 5000
    if ((maxVal - minVal) < priceGap) {
        
        // Kiểm tra xem người dùng đang kéo nút nào (dựa vào e.target.id)
        if (e.target.id === "minPrice") {
            // Nếu đang kéo nút Min lên quá cao -> Giữ nút Min lại
            minPrice.value = maxVal - priceGap;
            minVal = parseInt(minPrice.value);
        } else {
            // Nếu đang kéo nút Max xuống quá thấp -> Giữ nút Max lại
            // (Thay vì trừ Min đi gây ra số âm, ta cộng Max lên)
            maxPrice.value = minVal + priceGap;
            maxVal = parseInt(maxPrice.value);
        }
    }
    // ---------------------

    // Cập nhật text hiển thị
    minPriceLabel.textContent = formatPrice(minVal);
    maxPriceLabel.textContent = formatPrice(maxVal);
    minPriceValue.textContent = formatPrice(minVal);
    maxPriceValue.textContent = formatPrice(maxVal);

    // Cập nhật thanh màu xanh (Progress bar)
    const maxRange = parseInt(maxPrice.max); // 100000
    const percentMin = (minVal / maxRange) * 100;
    const percentMax = (maxVal / maxRange) * 100;

    priceRange.style.left = percentMin + '%';
    priceRange.style.width = (percentMax - percentMin) + '%';

            // Filter products
            filterProducts();
        }

        function filterProducts() {
            const minVal = parseInt(minPrice.value);
            const maxVal = parseInt(maxPrice.value);
            const products = document.querySelectorAll('.product-card');

            products.forEach(product => {
                const priceText = product.querySelector('.product-price').textContent;
                const price = parseInt(priceText.replace(/[^\d]/g, ''));

                if (price >= minVal && price <= maxVal) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        // Initialize
        updatePriceFilter();

     const style = document.createElement('style');
style.textContent = `
    /* Style cho Chrome/Safari/Edge */
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none; /* Quan trọng */
        pointer-events: auto; /* Quan trọng: chỉ nút tròn mới bắt sự kiện chuột */
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #22d3ee;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        position: relative;
        z-index: 30; /* Đưa nút lên cao nhất */
    }

    /* Style cho Firefox */
    input[type="range"]::-moz-range-thumb {
        pointer-events: auto;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #22d3ee;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        border: none;
        z-index: 30;
    }
    
`;
document.head.appendChild(style);
    </script>
</body>
</html>
