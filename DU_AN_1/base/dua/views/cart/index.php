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
                                                <form action="<?= BASE_URL ?>?action=cart-update" method="POST" class="flex items-center gap-2">
                                                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>" />
                                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="99" class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 text-center focus:ring-2 focus:ring-primary/50" />
                                                    <button type="submit" class="px-4 py-2 bg-primary/20 text-primary rounded-lg hover:bg-primary hover:text-white transition-colors text-sm font-semibold">Cập nhật</button>
                                                </form>
                                                <p class="text-lg font-bold text-primary"><?= number_format($itemTotal, 0, ',', '.') ?>đ</p>
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

                                <?php if (isset($nextCouponInfo) && $nextCouponInfo): ?>
                                    <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-lg">
                                        <div class="flex items-start gap-3">
                                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl">info</span>
                                            <div class="flex-1">
                                                <p class="font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                                    🎁 Mua thêm <?= number_format($nextCouponInfo['needed'], 0, ',', '.') ?>đ để nhận ưu đãi!
                                                </p>
                                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                                    Đạt <?= number_format($nextCouponInfo['min_order'], 0, ',', '.') ?>đ → Nhận mã <strong><?= $nextCouponInfo['code'] ?></strong> giảm <strong><?= $nextCouponInfo['discount'] ?></strong>
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
                                        <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                                            <p class="text-xs font-semibold text-amber-800 dark:text-amber-300 mb-2 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">local_offer</span>
                                                Mã giảm giá của bạn:
                                            </p>
                                            <div class="space-y-2">
                                                <?php foreach ($suggestedCoupons as $suggested): ?>
                                                    <div class="flex items-center justify-between p-2 bg-white dark:bg-slate-800 rounded border border-amber-200 dark:border-amber-700">
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
</body>

</html>