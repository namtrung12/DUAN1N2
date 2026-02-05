<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Giỏ hàng - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet" />
    <style>
        * * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined' !important;
        }

        /* Ẩn mũi tên lên xuống của input number */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        input[type="number"] {
            -moz-appearance: textfield;
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
                        "display": ["Poppins"]
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

<body class="bg-background-light dark:bg-background-dark font-display">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>
    <main class="flex-1">
        <div class="px-4 sm:px-10 lg:px-20 flex justify-center py-10">
            <div class="flex flex-col w-full max-w-7xl">
                <h1 class="text-slate-900 dark:text-slate-100 text-3xl font-bold leading-tight mb-6">Giỏ hàng của bạn</h1>
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
                <?php if (empty($cartData)): ?>
                    <div class="bg-white dark:bg-slate-800/50 rounded-xl p-10 text-center shadow-sm">
                        <span class="material-symbols-outlined text-6xl text-slate-400 mb-4">shopping_cart</span>
                        <p class="text-slate-500 dark:text-slate-400 text-lg mb-4">Giỏ hàng của bạn đang trống</p>
                        <a href="<?= BASE_URL ?>?action=products" class="inline-flex items-center justify-center gap-2 h-12 px-6 bg-primary text-white rounded-lg font-bold text-base hover:bg-opacity-90 transition-colors">
                            <span>Tiếp tục mua sắm</span>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Bulk Actions Bar -->
                    <div id="bulkActions" class="mb-4 bg-white dark:bg-slate-800/50 rounded-xl p-4 shadow-sm border-2 border-primary" style="display: none;">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-slate-900 dark:text-slate-100 font-semibold">
                                    Đã chọn: <span id="selectedCount">0</span> sản phẩm
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button id="deleteSelectedBtn" onclick="deleteSelectedItems()" class="flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                    <span>Xóa đã chọn</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-4">
                            <?php foreach ($cartData as $data): ?>
                                <?php
                                $item = $data['cart_item'];
                                $sizeInfo = $data['size_info'];
                                $toppings = $data['toppings'];
                                $itemPrice = $data['item_price'];
                                $toppingCost = $data['topping_cost'];
                                $itemTotal = $data['item_total'];
                                ?>
                                <div class="bg-white dark:bg-slate-800/50 rounded-xl p-4 shadow-sm">
                                    <div class="flex gap-4">
                                        <!-- Checkbox -->
                                        <?php
                                        // Kiểm tra nếu item đã được chọn (mặc định là true nếu không có selected_items)
                                        $isSelected = isset($data['is_selected']) ? $data['is_selected'] : true;
                                        ?>
                                        <div class="flex items-start pt-2">
                                            <input type="checkbox" class="cart-item-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary cursor-pointer"
                                                value="<?= $item['id'] ?>" <?= $isSelected ? 'checked' : '' ?> onchange="updateCartSelection()" />
                                        </div>
                                        <div class="w-24 h-24 bg-cover bg-center rounded-lg flex-shrink-0" style="background-image: url('<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>');"></div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h3 class="font-bold text-lg text-slate-900 dark:text-slate-100"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                                    <p class="text-sm text-slate-500 dark:text-slate-400">Size: <?= htmlspecialchars($item['size'], ENT_QUOTES, 'UTF-8') ?> - <?= number_format($itemPrice, 0, ',', '.') ?>đ</p>
                                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                                        🧊 Đá: <?= $item['ice_level'] ?? 100 ?>% | 🍬 Đường: <?= $item['sugar_level'] ?? 100 ?>%
                                                    </p>
                                                    <?php if (!empty($toppings)): ?>
                                                        <p class="text-sm text-slate-500 dark:text-slate-400">Topping:
                                                            <?php
                                                            $toppingNames = array_map(function ($t) {
                                                                return htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8');
                                                            }, $toppings);
                                                            echo implode(', ', $toppingNames);
                                                            ?> (+<?= number_format($toppingCost, 0, ',', '.') ?>đ)
                                                        </p>
                                                    <?php endif; ?>
                                                    <?php if ($item['note']): ?>
                                                        <p class="text-sm text-slate-500 dark:text-slate-400">Ghi chú: <?= htmlspecialchars($item['note'], ENT_QUOTES, 'UTF-8') ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between mt-3">
                                                <div class="flex items-center gap-2 relative z-10">
                                                    <button type="button" 
                                                            class="qty-minus w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-bold text-xl cursor-pointer"
                                                            data-cart-id="<?= $item['id'] ?>"
                                                            data-price="<?= $itemPrice ?>"
                                                            data-topping-price="<?= $toppingCost ?>">
                                                        −
                                                    </button>
                                                    <input type="number" 
                                                           id="qty-<?= $item['id'] ?>" 
                                                           value="<?= $item['quantity'] ?>" 
                                                           min="1" 
                                                           max="99"
                                                           data-cart-id="<?= $item['id'] ?>"
                                                           data-price="<?= $itemPrice ?>"
                                                           data-topping-price="<?= $toppingCost ?>"
                                                           class="qty-input w-16 px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 text-center font-semibold" />
                                                    <button type="button" 
                                                            class="qty-plus w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-bold text-xl cursor-pointer"
                                                            data-cart-id="<?= $item['id'] ?>"
                                                            data-price="<?= $itemPrice ?>"
                                                            data-topping-price="<?= $toppingCost ?>">
                                                        +
                                                    </button>
                                                </div>
                                                <p id="total-<?= $item['id'] ?>" class="text-lg font-bold text-primary"><?= number_format($itemTotal, 0, ',', '.') ?>đ</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Select All Checkbox -->
                            <div class="bg-white dark:bg-slate-800/50 rounded-xl p-4 shadow-sm border-t-2 border-gray-200 dark:border-gray-700">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                                        class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary cursor-pointer" />
                                    <span class="text-slate-900 dark:text-slate-100 font-semibold">Chọn tất cả</span>
                                </label>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="bg-white dark:bg-slate-800/50 rounded-xl p-6 shadow-sm sticky top-24">
                                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-4">Tóm tắt đơn hàng</h2>
                                <div class="space-y-3 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between text-slate-700 dark:text-slate-300">
                                        <span>Tạm tính:</span>
                                        <span><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                                    </div>
                                    <div class="flex justify-between text-slate-700 dark:text-slate-300">
                                        <span>Topping:</span>
                                        <span><?= number_format($toppingTotal, 0, ',', '.') ?>đ</span>
                                    </div>
                                    <?php if ($discount > 0): ?>
                                        <div class="flex justify-between text-green-600 dark:text-green-400">
                                            <span>Giảm giá:</span>
                                            <span>-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex justify-between text-lg font-bold text-slate-900 dark:text-slate-100 mb-6">
                                    <span>Tổng cộng:</span>
                                    <span class="text-primary"><?= number_format($total, 0, ',', '.') ?>đ</span>
                                </div>

                                <?php 
                                // Truyền thông tin user rank vào JavaScript
                                $rankLevels = ['new' => 0, 'bronze' => 1, 'silver' => 2, 'gold' => 3, 'diamond' => 4];
                                $currentRankLevel = $rankLevels[$userRank] ?? 0;
                                $currentTotal = $subtotal + $toppingTotal;
                                ?>
                                <?php if ($currentRankLevel >= 1): ?>
                                <div id="nextCouponInfo" class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-lg"
                                     data-user-rank-level="<?= $currentRankLevel ?>"
                                     style="<?= (isset($nextCouponInfo) && $nextCouponInfo) ? '' : 'display:none;' ?>">
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl">info</span>
                                        <div class="flex-1">
                                            <p id="nextCouponText" class="font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                                <?php if (isset($nextCouponInfo) && $nextCouponInfo): ?>
                                                🎁 Mua thêm <?= number_format($nextCouponInfo['needed'], 0, ',', '.') ?>đ để nhận ưu đãi!
                                                <?php endif; ?>
                                            </p>
                                            <p id="nextCouponDetail" class="text-sm text-blue-700 dark:text-blue-300">
                                                <?php if (isset($nextCouponInfo) && $nextCouponInfo): ?>
                                                Đạt <?= number_format($nextCouponInfo['min_order'], 0, ',', '.') ?>đ → Nhận mã <strong><?= $nextCouponInfo['code'] ?></strong> giảm <strong><?= $nextCouponInfo['discount'] ?></strong>
                                                <?php endif; ?>
                                            </p>
                                            <a href="<?= BASE_URL ?>?action=products" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                                <span class="material-symbols-outlined text-sm">shopping_bag</span>
                                                Tiếp tục mua sắm
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <form action="<?= BASE_URL ?>?action=cart-apply-coupon" method="POST" class="mb-4">
                                    <label class="block text-sm font-semibold text-slate-900 dark:text-slate-100 mb-2">Mã giảm giá</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="coupon_code" id="couponInput" value="<?= isset($_SESSION['cart_coupon']) ? htmlspecialchars($_SESSION['cart_coupon'], ENT_QUOTES, 'UTF-8') : '' ?>" placeholder="Nhập mã" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/50" <?= isset($_SESSION['cart_coupon']) ? 'readonly' : '' ?> />
                                        <?php if (isset($_SESSION['cart_coupon'])): ?>
                                            <a href="<?= BASE_URL ?>?action=cart-remove-coupon" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors font-semibold">Hủy</a>
                                        <?php else: ?>
                                            <button type="submit" class="px-4 py-2 bg-primary/20 text-primary rounded-lg hover:bg-primary hover:text-white transition-colors font-semibold">Áp dụng</button>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($suggestedCoupons) && !isset($_SESSION['cart_coupon'])): ?>
                                        <div id="suggestedCouponsBox" class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                                            <p class="text-xs font-semibold text-amber-800 dark:text-amber-300 mb-2 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">local_offer</span>
                                                Mã giảm giá của bạn:
                                            </p>
                                            <div id="suggestedCouponsList" class="space-y-2">
                                                <?php foreach ($suggestedCoupons as $suggested): ?>
                                                    <div class="coupon-item flex items-center justify-between p-2 bg-white dark:bg-slate-800 rounded border border-amber-200 dark:border-amber-700"
                                                         data-min-order="<?= $suggested['min_order'] ?? 0 ?>"
                                                         data-code="<?= htmlspecialchars($suggested['code'], ENT_QUOTES, 'UTF-8') ?>">
                                                        <div class="flex-1">
                                                            <p class="font-bold text-primary text-sm"><?= htmlspecialchars($suggested['code'], ENT_QUOTES, 'UTF-8') ?></p>
                                                            <p class="text-xs text-slate-600 dark:text-slate-400">
                                                                Giảm <?= $suggested['type'] === 'percent' ? $suggested['value'] . '%' : number_format($suggested['value']) . 'đ' ?>
                                                                <?php if (isset($suggested['is_redeemed']) && $suggested['is_redeemed']): ?>
                                                                    <span class="ml-1 px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-semibold">ĐÃ ĐỔI</span>
                                                                <?php elseif ($suggested['required_rank']): ?>
                                                                    <span class="ml-1 px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[10px] font-semibold uppercase"><?= $suggested['required_rank'] ?></span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <button type="button" onclick="applyCoupon('<?= htmlspecialchars($suggested['code'], ENT_QUOTES, 'UTF-8') ?>')" class="text-xs px-3 py-1 bg-primary text-white rounded hover:bg-opacity-90 font-medium">
                                                            Dùng
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </form>

                                <script>
                                    function applyCoupon(code) {
                                        document.getElementById('couponInput').value = code;
                                        document.querySelector('form[action*="cart-apply-coupon"]').submit();
                                    }

                                    // Checkbox và Bulk Actions
                                    function toggleSelectAll() {
                                        const selectAll = document.getElementById('selectAll');
                                        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
                                        checkboxes.forEach(checkbox => {
                                            checkbox.checked = selectAll.checked;
                                        });
                                        updateCartSelection();
                                    }

                                    function updateBulkActions() {
                                        const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
                                        const bulkActions = document.getElementById('bulkActions');
                                        const selectedCount = document.getElementById('selectedCount');
                                        const selectAll = document.getElementById('selectAll');
                                        const allCheckboxes = document.querySelectorAll('.cart-item-checkbox');
                                        const checkoutBtn = document.getElementById('checkoutBtn');
                                        const checkoutBtnText = document.getElementById('checkoutBtnText');
                                        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

                                        const count = checkedBoxes.length;
                                        const total = allCheckboxes.length;

                                        if (selectedCount) {
                                            selectedCount.textContent = count;
                                        }

                                        // Hiển thị nút xóa khi có ít nhất 1 sản phẩm được chọn
                                        if (deleteSelectedBtn) {
                                            if (count > 0) {
                                                deleteSelectedBtn.style.display = 'flex';
                                            } else {
                                                deleteSelectedBtn.style.display = 'none';
                                            }
                                        }

                                        // Hiển thị bulkActions khi có ít nhất 1 sản phẩm được chọn
                                        if (bulkActions) {
                                            if (count > 0) {
                                                bulkActions.style.display = 'block';
                                            } else {
                                                bulkActions.style.display = 'none';
                                            }
                                        }

                                        // Update select all checkbox state
                                        if (selectAll) {
                                            selectAll.checked = count === total && total > 0;
                                        }

                                        // Update checkout button text and behavior
                                        if (checkoutBtn && checkoutBtnText) {
                                            if (count === 0 || count === total) {
                                                // Không chọn gì hoặc chọn tất cả -> "Tiến hành thanh toán tất cả"
                                                checkoutBtnText.textContent = 'Tiến hành thanh toán tất cả';
                                            } else {
                                                // Chọn một phần -> "Thanh toán các sản phẩm đã chọn"
                                                checkoutBtnText.textContent = 'Thanh toán các sản phẩm đã chọn';
                                            }
                                        }
                                    }

                                    function updateCartSelection() {
                                        // Cập nhật bulk actions
                                        updateBulkActions();

                                        // Lấy danh sách các checkbox đã chọn
                                        const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
                                        const ids = Array.from(checkedBoxes).map(cb => cb.value);

                                        // Lưu selected_items vào session và reload trang để tính toán lại
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = '<?= BASE_URL ?>?action=cart-set-selected';

                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'selected_items';
                                        input.value = ids.join(',');
                                        form.appendChild(input);

                                        const actionInput = document.createElement('input');
                                        actionInput.type = 'hidden';
                                        actionInput.name = 'action';
                                        actionInput.value = 'update_cart';
                                        form.appendChild(actionInput);

                                        document.body.appendChild(form);
                                        form.submit();
                                    }

                                    function deleteSelectedItems() {
                                        const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
                                        const ids = Array.from(checkedBoxes).map(cb => cb.value);

                                        if (ids.length === 0) {
                                            alert('Vui lòng chọn ít nhất một sản phẩm để xóa');
                                            return;
                                        }

                                        if (confirm(`Bạn có chắc chắn muốn xóa ${ids.length} sản phẩm đã chọn?`)) {
                                            // Tạo form và submit
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.action = '<?= BASE_URL ?>?action=cart-remove-multiple';

                                            ids.forEach(id => {
                                                const input = document.createElement('input');
                                                input.type = 'hidden';
                                                input.name = 'cart_ids[]';
                                                input.value = id;
                                                form.appendChild(input);
                                            });

                                            document.body.appendChild(form);
                                            form.submit();
                                        }
                                    }

                                    function handleCheckout() {
                                        const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
                                        const allCheckboxes = document.querySelectorAll('.cart-item-checkbox');
                                        const count = checkedBoxes.length;
                                        const total = allCheckboxes.length;

                                        // Nếu không chọn gì hoặc chọn tất cả -> thanh toán tất cả
                                        if (count === 0 || count === total) {
                                            // Xóa selected_items để thanh toán tất cả
                                            unsetSelectedItemsAndCheckout();
                                            return;
                                        }

                                        // Nếu chọn một phần -> thanh toán các sản phẩm đã chọn
                                        const ids = Array.from(checkedBoxes).map(cb => cb.value);

                                        // Tạo form và submit để lưu selected_items vào session và chuyển đến checkout
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = '<?= BASE_URL ?>?action=cart-set-selected';

                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'selected_items';
                                        input.value = ids.join(',');
                                        form.appendChild(input);

                                        const actionInput = document.createElement('input');
                                        actionInput.type = 'hidden';
                                        actionInput.name = 'action';
                                        actionInput.value = 'checkout';
                                        form.appendChild(actionInput);

                                        document.body.appendChild(form);
                                        form.submit();
                                    }

                                    function unsetSelectedItemsAndCheckout() {
                                        // Tạo form để xóa selected_items
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = '<?= BASE_URL ?>?action=cart-set-selected';

                                        const actionInput = document.createElement('input');
                                        actionInput.type = 'hidden';
                                        actionInput.name = 'action';
                                        actionInput.value = 'checkout_all';
                                        form.appendChild(actionInput);

                                        document.body.appendChild(form);
                                        form.submit();
                                    }

                                    // Khởi tạo khi DOM sẵn sàng
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Khởi tạo trạng thái ban đầu
                                        updateBulkActions();
                                    });
                                </script>
                                <button type="button" id="checkoutBtn" onclick="handleCheckout()" class="block w-full h-12 bg-primary text-white rounded-lg font-bold text-base hover:bg-opacity-90 transition-colors flex items-center justify-center">
                                    <span id="checkoutBtnText">Tiến hành thanh toán tất cả</span>
                                </button>
                                <a href="<?= BASE_URL ?>?action=products" class="block w-full h-12 mt-3 flex items-center justify-center border-2 border-primary text-primary rounded-lg font-bold text-base hover:bg-primary/10 transition-colors">Tiếp tục mua sắm</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý nút trừ
        document.querySelectorAll('.qty-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                updateQuantity(this.dataset.cartId, -1, this.dataset.price, this.dataset.toppingPrice);
            });
        });
        
        // Xử lý nút cộng
        document.querySelectorAll('.qty-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                updateQuantity(this.dataset.cartId, 1, this.dataset.price, this.dataset.toppingPrice);
            });
        });
        
        // Xử lý nhập trực tiếp vào ô số lượng
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                handleDirectInput(this);
            });
            
            input.addEventListener('blur', function() {
                handleDirectInput(this);
            });
        });
    });
    
    function handleDirectInput(input) {
        let newQty = parseInt(input.value);
        
        // Validate
        if (isNaN(newQty) || newQty < 1) {
            newQty = 1;
        }
        if (newQty > 99) {
            newQty = 99;
        }
        
        input.value = newQty;
        
        const cartId = input.dataset.cartId;
        const itemPrice = input.dataset.price;
        const toppingPrice = input.dataset.toppingPrice;
        
        // Parse giá
        const price = parseFloat(String(itemPrice).replace(/,/g, '')) || 0;
        const topping = parseFloat(String(toppingPrice).replace(/,/g, '')) || 0;
        
        // Cập nhật giá item
        const itemTotal = (price + topping) * newQty;
        const totalElement = document.getElementById('total-' + cartId);
        if (totalElement) {
            totalElement.textContent = new Intl.NumberFormat('vi-VN').format(itemTotal) + 'đ';
        }
        
        // Cập nhật tổng tiền giỏ hàng
        updateCartTotals();
        
        // Gửi request cập nhật
        const formData = new FormData();
        formData.append('cart_id', cartId);
        formData.append('quantity', newQty);
        
        fetch('<?= BASE_URL ?>?action=cart-update', {
            method: 'POST',
            body: formData
        }).catch(error => {
            console.error('Update error:', error);
            location.reload();
        });
    }
    
    function updateQuantity(cartId, change, itemPrice, toppingPrice) {
        const qtyInput = document.getElementById('qty-' + cartId);
        if (!qtyInput) return;
        
        let currentQty = parseInt(qtyInput.value);
        let newQty = currentQty + change;
        
        if (newQty < 1) newQty = 1;
        if (newQty > 99) newQty = 99;
        
        qtyInput.value = newQty;
        
        // Parse giá (loại bỏ dấu phẩy nếu có)
        const price = parseFloat(String(itemPrice).replace(/,/g, '')) || 0;
        const topping = parseFloat(String(toppingPrice).replace(/,/g, '')) || 0;
        
        // Cập nhật giá item
        const itemTotal = (price + topping) * newQty;
        const totalElement = document.getElementById('total-' + cartId);
        if (totalElement) {
            totalElement.textContent = new Intl.NumberFormat('vi-VN').format(itemTotal) + 'đ';
        }
        
        // Cập nhật tổng tiền giỏ hàng
        updateCartTotals();
        
        // Gửi request cập nhật background
        const formData = new FormData();
        formData.append('cart_id', cartId);
        formData.append('quantity', newQty);
        
        fetch('<?= BASE_URL ?>?action=cart-update', {
            method: 'POST',
            body: formData
        }).catch(error => {
            console.error('Update error:', error);
            // Nếu lỗi thì reload
            location.reload();
        });
    }
    
    // Các ngưỡng mã giảm giá theo rank
    const couponThresholds = [
        { min: 50000, code: 'BRONZE10', discount: '10%', rankLevel: 1 },
        { min: 100000, code: 'SILVER15', discount: '15%', rankLevel: 2 },
        { min: 150000, code: 'GOLD20', discount: '20%', rankLevel: 3 },
        { min: 200000, code: 'DIAMOND25', discount: '25%', rankLevel: 4 }
    ];
    
    function updateCartTotals() {
        let subtotal = 0;
        let toppingTotal = 0;
        
        // Duyệt qua tất cả các items để tính tổng
        document.querySelectorAll('.qty-input').forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(String(input.dataset.price).replace(/,/g, '')) || 0;
            const topping = parseFloat(String(input.dataset.toppingPrice).replace(/,/g, '')) || 0;
            
            subtotal += price * qty;
            toppingTotal += topping * qty;
        });
        
        const grandTotal = subtotal + toppingTotal;
        
        // Tìm và cập nhật các phần tử trong sidebar
        const summaryDiv = document.querySelector('.sticky.top-24');
        if (!summaryDiv) return;
        
        // Tìm tất cả các dòng trong phần space-y-3
        const allRows = summaryDiv.querySelectorAll('.space-y-3 .flex.justify-between');
        
        // Duyệt qua các dòng và cập nhật dựa trên text
        allRows.forEach(row => {
            const label = row.querySelector('span:first-child')?.textContent.trim();
            const valueSpan = row.querySelector('span:last-child');
            
            if (label === 'Tạm tính:' && valueSpan) {
                valueSpan.textContent = new Intl.NumberFormat('vi-VN').format(subtotal) + 'đ';
            } else if (label === 'Topping:' && valueSpan) {
                valueSpan.textContent = new Intl.NumberFormat('vi-VN').format(toppingTotal) + 'đ';
            }
        });
        
        // Cập nhật "Tổng cộng"
        const totalRow = summaryDiv.querySelector('.text-lg.font-bold .text-primary');
        if (totalRow) {
            totalRow.textContent = new Intl.NumberFormat('vi-VN').format(grandTotal) + 'đ';
        }
        
        // Cập nhật gợi ý mã giảm giá
        updateCouponSuggestion(grandTotal);
    }
    
    function updateCouponSuggestion(currentTotal) {
        const nextCouponDiv = document.getElementById('nextCouponInfo');
        
        // Cập nhật danh sách mã giảm giá gợi ý (ẩn/hiện dựa trên min_order)
        updateSuggestedCoupons(currentTotal);
        
        if (!nextCouponDiv) return;
        
        const userRankLevel = parseInt(nextCouponDiv.dataset.userRankLevel) || 0;
        
        // Nếu user là khách mới (rank level 0), không hiển thị gợi ý
        if (userRankLevel === 0) {
            nextCouponDiv.style.display = 'none';
            return;
        }
        
        // Tìm ngưỡng tiếp theo phù hợp với rank của user
        let nextCoupon = null;
        for (const threshold of couponThresholds) {
            // Chỉ hiển thị nếu: user đủ rank VÀ đơn hàng chưa đủ ngưỡng
            if (threshold.rankLevel <= userRankLevel && currentTotal < threshold.min) {
                nextCoupon = {
                    needed: threshold.min - currentTotal,
                    code: threshold.code,
                    discount: threshold.discount,
                    minOrder: threshold.min
                };
                break;
            }
        }
        
        const nextCouponText = document.getElementById('nextCouponText');
        const nextCouponDetail = document.getElementById('nextCouponDetail');
        
        if (nextCoupon && nextCoupon.needed > 0) {
            // Hiển thị gợi ý
            nextCouponDiv.style.display = 'block';
            if (nextCouponText) {
                nextCouponText.textContent = '🎁 Mua thêm ' + new Intl.NumberFormat('vi-VN').format(nextCoupon.needed) + 'đ để nhận ưu đãi!';
            }
            if (nextCouponDetail) {
                nextCouponDetail.innerHTML = 'Đạt ' + new Intl.NumberFormat('vi-VN').format(nextCoupon.minOrder) + 'đ → Nhận mã <strong>' + nextCoupon.code + '</strong> giảm <strong>' + nextCoupon.discount + '</strong>';
            }
        } else {
            // Ẩn gợi ý nếu đã đủ điều kiện tất cả các mã
            nextCouponDiv.style.display = 'none';
        }
    }
    
    // Cập nhật danh sách mã giảm giá gợi ý dựa trên tổng tiền
    function updateSuggestedCoupons(currentTotal) {
        const couponItems = document.querySelectorAll('.coupon-item');
        const suggestedBox = document.getElementById('suggestedCouponsBox');
        
        if (!couponItems.length) return;
        
        let visibleCount = 0;
        
        couponItems.forEach(item => {
            const minOrder = parseFloat(item.dataset.minOrder) || 0;
            
            // Hiển thị mã nếu tổng tiền >= min_order
            if (currentTotal >= minOrder) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Ẩn cả box nếu không có mã nào hiển thị
        if (suggestedBox) {
            suggestedBox.style.display = visibleCount > 0 ? 'block' : 'none';
        }
    }
    </script>
</body>

</html>