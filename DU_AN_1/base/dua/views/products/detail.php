<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .star-rating {
            color: #fbbf24;
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
                        "sans": ["-apple-system", "BlinkMacSystemFont", "Segoe UI", "Roboto", "Helvetica Neue", "Arial", "sans-serif"],
                        "display": ["-apple-system", "BlinkMacSystemFont", "Segoe UI", "Roboto", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>

<body class="bg-white font-display">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>

    <main class="flex-1">
        <div class="px-4 sm:px-6 lg:px-8 flex justify-center py-6">
            <div class="flex flex-col w-full max-w-6xl">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php unset($_SESSION['success']);
                endif; ?>
                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php unset($_SESSION['errors']);
                endif; ?>

                <!-- Main Product Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-8">
                    <!-- Product Image -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm">
                        <div class="relative w-full aspect-square bg-gray-100 max-w-md mx-auto">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full h-full object-cover"
                                onerror="this.src='https://via.placeholder.com/400x400?text=No+Image'" />
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="flex flex-col">
                        <h1 class="text-slate-900 text-2xl md:text-3xl font-bold leading-tight mb-3"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>

                        <!-- Rating -->
                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex items-center">
                                <?php
                                $fullStars = floor($avgRating);
                                $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
                                for ($i = 0; $i < 5; $i++):
                                    if ($i < $fullStars): ?>
                                        <span class="text-yellow-400 text-lg">★</span>
                                    <?php elseif ($i == $fullStars && $hasHalfStar): ?>
                                        <span class="text-yellow-400 text-lg">☆</span>
                                    <?php else: ?>
                                        <span class="text-gray-300 text-lg">★</span>
                                <?php endif;
                                endfor; ?>
                            </div>
                            <span class="text-slate-600 text-sm">(<?= number_format($reviewCount, 0, ',', '.') ?> đánh giá)</span>
                        </div>

                        <!-- Description -->
                        <p class="text-slate-700 text-sm mb-6"><?= htmlspecialchars($product['description'] ?? 'Hương vị trà sữa truyền thống kết hợp với trân châu đường đen dai giòn, ngọt ngào.', ENT_QUOTES, 'UTF-8') ?></p>

                        <form action="<?= BASE_URL ?>?action=cart-add" method="POST" class="space-y-5">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />

                            <!-- Size Selection -->
                            <div>
                                <label class="block text-slate-900 text-base font-semibold mb-3">Kích cỡ</label>
                                <div class="flex gap-2">
                                    <?php
                                    $firstSize = null;
                                    foreach ($sizes as $size):
                                        if (!$firstSize) $firstSize = $size;
                                    ?>
                                        <label class="relative flex-1">
                                            <input type="radio" name="product_size_id" value="<?= $size['id'] ?>"
                                                class="peer sr-only" required
                                                <?= $size === $firstSize ? 'checked' : '' ?>
                                                onchange="updatePrice()" />
                                            <div class="flex flex-col items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-amber-600 peer-checked:bg-amber-50 hover:border-amber-400 transition-colors">
                                                <span class="text-base font-bold text-slate-900"><?= htmlspecialchars($size['size_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Ice Level -->
                            <div>
                                <label class="block text-slate-900 text-base font-semibold mb-3">
                                    Lượng đá: <span id="ice-level-text" class="text-amber-600">100%</span>
                                </label>
                                <input type="range" name="ice_level" id="ice-level" min="0" max="100" value="100" step="25"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-amber-600"
                                    oninput="updateIceLevel(this.value)" />
                                <div class="flex justify-between text-xs text-slate-500 mt-1">
                                    <span>0%</span>
                                    <span>25%</span>
                                    <span>50%</span>
                                    <span>75%</span>
                                    <span>100%</span>
                                </div>
                            </div>

                            <!-- Sugar Level -->
                            <div>
                                <label class="block text-slate-900 text-base font-semibold mb-3">
                                    Lượng đường: <span id="sugar-level-text" class="text-amber-600">100%</span>
                                </label>
                                <input type="range" name="sugar_level" id="sugar-level" min="0" max="100" value="100" step="25"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-amber-600"
                                    oninput="updateSugarLevel(this.value)" />
                                <div class="flex justify-between text-xs text-slate-500 mt-1">
                                    <span>0%</span>
                                    <span>25%</span>
                                    <span>50%</span>
                                    <span>75%</span>
                                    <span>100%</span>
                                </div>
                            </div>

                            <!-- Toppings -->
                            <?php if (!empty($toppings)): ?>
                                <div>
                                    <label class="block text-slate-900 text-base font-semibold mb-3">Topping</label>
                                    <div class="space-y-2">
                                        <?php foreach ($toppings as $topping): ?>
                                            <label class="flex items-center justify-between p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-amber-400 transition-colors">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" name="toppings[]" value="<?= $topping['id'] ?>"
                                                        class="w-4 h-4 text-amber-600 rounded focus:ring-2 focus:ring-amber-500" />
                                                    <span class="text-slate-900 text-sm font-medium"><?= htmlspecialchars($topping['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                                </div>
                                                <span class="text-amber-600 text-sm font-semibold">+<?= number_format($topping['price'], 0, ',', '.') ?>₫</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-slate-900 text-base font-semibold mb-3">Số lượng</label>
                                <div class="flex items-center gap-3">
                                    <button type="button" onclick="decreaseQuantity()" class="w-9 h-9 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-amber-600 hover:bg-amber-50 transition-colors">
                                        <span class="material-symbols-outlined text-slate-700 text-xl">remove</span>
                                    </button>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="99"
                                        class="w-16 text-center text-base font-bold border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-600" />
                                    <button type="button" onclick="increaseQuantity()" class="w-9 h-9 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-amber-600 hover:bg-amber-50 transition-colors">
                                        <span class="material-symbols-outlined text-slate-700 text-xl">add</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Price Display -->
                            <div class="py-4">
                                <p class="text-amber-600 font-bold text-2xl" id="product-price">
                                    <?= $firstSize ? number_format($firstSize['price'], 0, ',', '.') : '0' ?>₫
                                </p>
                            </div>

                            <!-- Add to Cart Button -->
                            <button type="submit" class="w-full h-12 bg-amber-700 text-white rounded-lg font-bold text-base shadow-md hover:bg-amber-800 transition-colors">
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tabs Section -->
                <div class="mb-8">
                    <div class="flex gap-4 border-b-2 border-gray-200">
                        <button onclick="showTab('description')" id="tab-description" class="px-6 py-3 font-semibold text-slate-900 border-b-2 border-amber-600 -mb-0.5">
                            Mô tả chi tiết
                        </button>
                        <button onclick="showTab('reviews')" id="tab-reviews" class="px-6 py-3 font-semibold text-slate-500 hover:text-slate-900">
                            Đánh giá
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div id="content-description" class="tab-content">
                    <div class="bg-white rounded-xl p-8">
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Mô tả sản phẩm</h3>
                        <p class="text-slate-700 leading-relaxed"><?= nl2br(htmlspecialchars($product['description'] ?? 'Không có mô tả chi tiết.', ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                </div>

                <div id="content-reviews" class="tab-content hidden">
                    <div class="bg-white rounded-xl p-8">
                        <h3 class="text-2xl font-bold text-slate-900 mb-6">Đánh giá khách hàng</h3>
                        <?php if (empty($reviews)): ?>
                            <p class="text-slate-500 text-center py-8">Chưa có đánh giá nào cho sản phẩm này.</p>
                        <?php else: ?>
                            <div class="space-y-6">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                        <div class="flex items-start gap-4">
                                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                                <span class="text-2xl">👤</span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div>
                                                        <h4 class="font-semibold text-slate-900"><?= htmlspecialchars($review['user_name'], ENT_QUOTES, 'UTF-8') ?></h4>
                                                        <div class="flex items-center gap-1 mt-1">
                                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                                <span class="text-yellow-400 text-lg"><?= $i < $review['rating'] ? '★' : '☆' ?></span>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                    <span class="text-slate-500 text-sm"><?= date('d-m-Y', strtotime($review['created_at'])) ?></span>
                                                </div>
                                                <?php if (!empty($review['comment'])): ?>
                                                    <p class="text-slate-700 mt-2"><?= nl2br(htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8')) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <script>
        // Size and price data
        const sizes = <?= json_encode($sizes) ?>;
        const toppings = <?= json_encode($toppings ?? []) ?>;

        function updatePrice() {
            const selectedSize = document.querySelector('input[name="product_size_id"]:checked');
            if (!selectedSize) return;

            const sizeId = parseInt(selectedSize.value);
            const size = sizes.find(s => s.id == sizeId);

            if (size) {
                const basePrice = parseFloat(size.price);
                const checkedToppings = document.querySelectorAll('input[name="toppings[]"]:checked');
                let toppingPrice = 0;

                checkedToppings.forEach(topping => {
                    const toppingId = parseInt(topping.value);
                    const toppingData = toppings.find(t => t.id == toppingId);
                    if (toppingData) {
                        toppingPrice += parseFloat(toppingData.price);
                    }
                });

                const quantity = parseInt(document.getElementById('quantity').value) || 1;
                const totalPrice = (basePrice + toppingPrice) * quantity;

                document.getElementById('product-price').textContent =
                    totalPrice.toLocaleString('vi-VN') + '₫';
            }
        }

        function increaseQuantity() {
            const qtyInput = document.getElementById('quantity');
            const currentValue = parseInt(qtyInput.value) || 1;
            if (currentValue < 99) {
                qtyInput.value = currentValue + 1;
                updatePrice();
            }
        }

        function decreaseQuantity() {
            const qtyInput = document.getElementById('quantity');
            const currentValue = parseInt(qtyInput.value) || 1;
            if (currentValue > 1) {
                qtyInput.value = currentValue - 1;
                updatePrice();
            }
        }

        // Update price when quantity changes
        document.getElementById('quantity').addEventListener('change', updatePrice);
        document.getElementById('quantity').addEventListener('input', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > 99) this.value = 99;
            updatePrice();
        });

        // Update price when toppings change
        document.querySelectorAll('input[name="toppings[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updatePrice);
        });

        // Ice and Sugar level functions
        function updateIceLevel(value) {
            document.getElementById('ice-level-text').textContent = value + '%';
        }

        function updateSugarLevel(value) {
            document.getElementById('sugar-level-text').textContent = value + '%';
        }

        // Tab switching
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.classList.remove('border-amber-600', 'text-slate-900');
                tab.classList.add('text-slate-500');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.add('border-amber-600', 'text-slate-900');
            activeTab.classList.remove('text-slate-500');
        }
    </script>
</body>

</html>