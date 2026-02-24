<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Chill Drink - Hương Vị Bùng Nổ Mỗi Ngày</title>
    <meta name="description" content="Thưởng thức đồ uống tươi ngon, giao hàng nhanh chóng. Tích điểm đổi quà hấp dẫn!">
    <meta name="keywords" content="trà sữa, cà phê, sinh tố, nước ép, đồ uống">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet"/>
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
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
                        "background-dark": "#111c21",
                    },
                    fontFamily: { 
                        "sans": ["-apple-system", "BlinkMacSystemFont", "Segoe UI", "Roboto", "Helvetica Neue", "Arial", "Noto Sans", "sans-serif"],
                        "display": ["-apple-system", "BlinkMacSystemFont", "Segoe UI", "Roboto", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="bg-background-light">
    <?php include PATH_VIEW . 'layouts/header.php'; ?>
    
    <?php include PATH_VIEW . 'layouts/hero.php'; ?>

    <!-- Featured Categories -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">Danh mục sản phẩm</h2>
                <p class="text-slate-600 text-sm mt-2">Khám phá các loại đồ uống yêu thích</p>
            </div>

            <div class="flex justify-center items-center gap-6 md:gap-12 lg:gap-16 flex-wrap">
                <?php 
                $categoryModel = new Category();
                $categories = $categoryModel->getAll();
                
                // Icon material symbols và màu cho từng danh mục
                $categoryData = [
                    'Trà' => ['icon' => 'eco', 'gradient' => 'from-green-400 to-emerald-500', 'color' => 'text-white'],
                    'Trà sữa' => ['icon' => 'local_cafe', 'gradient' => 'from-amber-400 to-orange-500', 'color' => 'text-white'],
                    'Cà phê' => ['icon' => 'coffee', 'gradient' => 'from-amber-700 to-yellow-800', 'color' => 'text-white'],
                    'Sinh tố' => ['icon' => 'blender', 'gradient' => 'from-pink-400 to-rose-500', 'color' => 'text-white'],
                    'Nước ép' => ['icon' => 'water_drop', 'gradient' => 'from-cyan-400 to-blue-500', 'color' => 'text-white'],
                ];
                
                foreach ($categories as $category): 
                    // Tìm data phù hợp
                    $data = ['icon' => 'local_cafe', 'gradient' => 'from-gray-400 to-gray-500', 'color' => 'text-white'];
                    foreach ($categoryData as $catName => $catData) {
                        if (stripos($category['name'], $catName) !== false) {
                            $data = $catData;
                            break;
                        }
                    }
                ?>
                <a href="<?= BASE_URL ?>?action=products-by-category&category_id=<?= $category['id'] ?>" 
                   class="flex flex-col items-center group transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br <?= $data['gradient'] ?> rounded-full flex items-center justify-center mb-2 shadow-md group-hover:shadow-lg transition-all transform group-hover:-translate-y-1 relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 group-hover:bg-white/30 transition-all"></div>
                        <span class="material-symbols-outlined <?= $data['color'] ?> text-3xl md:text-4xl relative z-10 drop-shadow-lg">
                            <?= $data['icon'] ?>
                        </span>
                    </div>
                    <h3 class="text-sm md:text-base font-semibold text-slate-900 group-hover:text-primary transition-colors">
                        <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-8 bg-gray-50" id="featured-products">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">Sản phẩm nổi bật</h2>
                <a href="<?= BASE_URL ?>?action=products" class="text-primary hover:underline text-sm font-medium">Xem tất cả →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                $productModel = new Product();
                $products = array_slice($productModel->getAll(), 0, 4);
                foreach ($products as $product): 
                    $sizes = $productModel->getSizes($product['id']);
                    $minPrice = !empty($sizes) ? min(array_column($sizes, 'price')) : 0;
                ?>
                <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $product['id'] ?>" 
                   class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 block">
                    <div class="relative h-56 bg-gray-100 overflow-hidden">
                        <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" 
                             alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                             onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'"/>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-slate-900 mb-2"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="text-primary font-bold text-xl mb-4"><?= number_format($minPrice, 0, ',', '.') ?>đ</p>
                        <div class="w-full text-center py-3 bg-cyan-100 text-cyan-600 rounded-lg font-semibold hover:bg-cyan-200 transition-colors">
                            Thêm vào giỏ
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Sale Products -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">Sản phẩm bán chạy</h2>
                <span class="text-red-500 font-semibold text-sm">🔥 Hot Deal</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                $saleProducts = array_slice($productModel->getAll(), 4, 4);
                $saleDiscounts = [20, 15, 25, 18]; // Giảm giá mẫu
                foreach ($saleProducts as $index => $product): 
                    $sizes = $productModel->getSizes($product['id']);
                    $minPrice = !empty($sizes) ? min(array_column($sizes, 'price')) : 0;
                    $discount = $saleDiscounts[$index] ?? 20;
                    $originalPrice = $minPrice * (100 / (100 - $discount));
                    $salePrice = $minPrice;
                ?>
                <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $product['id'] ?>" 
                   class="bg-gray-50 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 block">
                    <div class="relative h-56 bg-gray-200 overflow-hidden">
                        <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" 
                             alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                             onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'"/>
                        <div class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            SALE
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-slate-900 mb-2"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-gray-400 line-through text-sm"><?= number_format($originalPrice, 0, ',', '.') ?>đ</span>
                            <span class="text-primary font-bold text-xl"><?= number_format($salePrice, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="w-full text-center py-3 bg-cyan-100 text-cyan-600 rounded-lg font-semibold hover:bg-cyan-200 transition-colors">
                            Thêm vào giỏ
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-8 bg-gray-50" id="newsletter-section">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-gradient-to-r from-primary/10 to-cyan-100 rounded-2xl p-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex-1">
                        <h2 class="text-xl md:text-2xl font-bold text-slate-900 mb-1">Nhận ưu đãi đặc quyền!</h2>
                        <p class="text-slate-600 text-sm">Đăng ký để không bỏ lỡ các khuyến mãi hấp dẫn.</p>
                    </div>
                    <div class="flex-1 w-full">
                        <form class="flex gap-3" onsubmit="return handleNewsletterSubmit(event)">
                            <input 
                                type="email" 
                                placeholder="Nhập email của bạn" 
                                required
                                class="flex-1 h-12 px-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary"
                            />
                            <button 
                                type="submit"
                                class="px-8 py-3 bg-cyan-400 text-white rounded-lg font-semibold hover:bg-cyan-500 transition-colors whitespace-nowrap">
                                Đăng ký
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    function handleNewsletterSubmit(event) {
        event.preventDefault();
        const email = event.target.querySelector('input[type="email"]').value;
        alert('Cảm ơn bạn đã đăng ký! Chúng tôi sẽ gửi ưu đãi đến email: ' + email);
        event.target.reset();
        return false;
    }
    </script>

    <!-- Why Choose Us -->
    <section class="py-8 bg-gradient-to-br from-primary/5 to-primary/10">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900 mb-1">Tại Sao Chọn Chill Drink?</h2>
                <p class="text-slate-600 text-sm">Những lý do khiến khách hàng yêu thích chúng tôi</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <div class="text-center">
                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-sm">
                        <span class="material-symbols-outlined text-primary text-2xl">verified</span>
                    </div>
                    <h3 class="font-bold text-sm md:text-base text-slate-900 mb-1">Chất Lượng</h3>
                    <p class="text-slate-600 text-xs hidden md:block">Nguyên liệu tươi ngon</p>
                </div>

                <div class="text-center">
                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-sm">
                        <span class="material-symbols-outlined text-primary text-2xl">local_shipping</span>
                    </div>
                    <h3 class="font-bold text-sm md:text-base text-slate-900 mb-1">Giao Nhanh</h3>
                    <p class="text-slate-600 text-xs hidden md:block">Giao trong 30 phút</p>
                </div>

                <div class="text-center">
                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-sm">
                        <span class="material-symbols-outlined text-primary text-2xl">loyalty</span>
                    </div>
                    <h3 class="font-bold text-sm md:text-base text-slate-900 mb-1">Tích Điểm</h3>
                    <p class="text-slate-600 text-xs hidden md:block">Đổi quà hấp dẫn</p>
                </div>

                <div class="text-center">
                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-sm">
                        <span class="material-symbols-outlined text-primary text-2xl">support_agent</span>
                    </div>
                    <h3 class="font-bold text-sm md:text-base text-slate-900 mb-1">Hỗ Trợ 24/7</h3>
                    <p class="text-slate-600 text-xs hidden md:block">Luôn sẵn sàng</p>
                </div>
            </div>
        </div>
    </section>

    <?php require_once PATH_VIEW . 'layouts/footer.php'; ?>

    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
