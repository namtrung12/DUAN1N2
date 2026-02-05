<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Mã giảm giá - Chill Drink Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <?php include PATH_VIEW . 'layouts/admin-sidebar.php'; ?>

        <main class="flex-1 ml-64 p-8">
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <a href="<?= BASE_URL ?>?action=admin-coupons" class="text-slate-600 hover:text-slate-900">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <h1 class="text-3xl font-bold text-slate-900">Thêm Mã giảm giá</h1>
                </div>
            </div>

            <?php if (isset($_SESSION['errors'])): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); endif; ?>

            <form action="<?= BASE_URL ?>?action=admin-coupon-store" method="POST" class="bg-white rounded-2xl shadow-sm p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Mã giảm giá *</label>
                        <input type="text" name="code" required value="<?= htmlspecialchars($_SESSION['old']['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                               placeholder="VD: SUMMER2024"/>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Loại giảm giá *</label>
                        <select name="type" id="couponType" onchange="updateValueConstraints()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="fixed">Giảm cố định (VNĐ)</option>
                            <option value="percent">Giảm phần trăm (%)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Giá trị giảm *</label>
                        <input type="number" name="value" id="couponValue" required min="1" value="<?= htmlspecialchars($_SESSION['old']['value'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="VD: 50000 hoặc 10"/>
                        <p class="text-xs text-gray-500 mt-1" id="valueHint">Nhập số tiền giảm (VNĐ)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Giảm tối đa (VNĐ)</label>
                        <input type="number" name="max_discount" min="0" value="<?= htmlspecialchars($_SESSION['old']['max_discount'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Chỉ áp dụng cho mã giảm %"/>
                        <p class="text-xs text-gray-500 mt-1">Ví dụ: Giảm 20% tối đa 50.000đ</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Đơn tối thiểu (VNĐ)</label>
                        <input type="number" name="min_order" min="0" value="<?= htmlspecialchars($_SESSION['old']['min_order'] ?? '0', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Giới hạn sử dụng</label>
                        <input type="number" name="usage_limit" min="0" value="<?= htmlspecialchars($_SESSION['old']['usage_limit'] ?? '0', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="0 = Không giới hạn"/>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Rank yêu cầu</label>
                        <select name="required_rank" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Tất cả</option>
                            <option value="bronze">Bronze</option>
                            <option value="silver">Silver</option>
                            <option value="gold">Gold</option>
                            <option value="diamond">Kim cương</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Điểm đổi</label>
                        <input type="number" name="point_cost" min="0" value="<?= htmlspecialchars($_SESSION['old']['point_cost'] ?? '0', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="0 = Miễn phí"/>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_redeemable" class="w-4 h-4 rounded border-gray-300 text-blue-600"/>
                            <span class="text-sm font-medium text-slate-900">Có thể đổi bằng điểm</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Ngày bắt đầu</label>
                        <input type="datetime-local" name="starts_at" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Ngày hết hạn</label>
                        <input type="datetime-local" name="expires_at" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Mô tả</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Mô tả chi tiết về mã giảm giá..."><?= htmlspecialchars($_SESSION['old']['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="status" checked class="w-4 h-4 rounded border-gray-300 text-blue-600"/>
                            <span class="text-sm font-medium text-slate-900">Kích hoạt mã</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4 mt-8 pt-6 border-t">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        Thêm mã
                    </button>
                    <a href="<?= BASE_URL ?>?action=admin-coupons" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                        Hủy
                    </a>
                </div>
            </form>
        </main>
    </div>
    
    <script>
        function updateValueConstraints() {
            const type = document.getElementById('couponType').value;
            const valueInput = document.getElementById('couponValue');
            const hint = document.getElementById('valueHint');
            
            if (type === 'percent') {
                valueInput.setAttribute('max', '100');
                valueInput.setAttribute('placeholder', 'VD: 10, 20, 50 (tối đa 100%)');
                hint.textContent = 'Nhập phần trăm giảm (1-100%)';
            } else {
                valueInput.removeAttribute('max');
                valueInput.setAttribute('placeholder', 'VD: 50000, 100000');
                hint.textContent = 'Nhập số tiền giảm (VNĐ)';
            }
        }
        
        // Khởi tạo khi load trang
        updateValueConstraints();
    </script>
</body>
</html>
<?php unset($_SESSION['old']); ?>
