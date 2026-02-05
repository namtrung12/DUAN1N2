<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản phẩm - Chill Drink Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .required-star { color: #ef4444; }
        .error-text { color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <?php include PATH_VIEW . 'layouts/admin-sidebar.php'; ?>

        <main class="flex-1 lg:ml-64 p-4 sm:p-6 lg:p-8">
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <a href="<?= BASE_URL ?>?action=admin-products" class="text-slate-600 hover:text-slate-900">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <h1 class="text-3xl font-bold text-slate-900">Thêm Sản phẩm mới</h1>
                </div>
            </div>

            <?php 
            $errors = $_SESSION['errors'] ?? []; 
            unset($_SESSION['errors']); 
            $tempImage = $_SESSION['old']['temp_image'] ?? '';
            ?>

            <form action="<?= BASE_URL ?>?action=admin-product-store" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Hình ảnh -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Hình ảnh sản phẩm <span class="required-star">*</span></label>
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <img id="imagePreview" 
                                     src="<?= !empty($tempImage) ? BASE_URL . 'assets/uploads/' . $tempImage : 'https://via.placeholder.com/128x128?text=No+Image' ?>" 
                                     alt="Preview"
                                     class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200"/>
                            </div>
                            
                            <div class="flex-1">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <span class="material-symbols-outlined text-gray-400 text-4xl mb-2">cloud_upload</span>
                                        <p class="text-sm text-gray-600"><span class="font-semibold">Click để upload</span> hoặc kéo thả</p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(event)"/>
                                </label>
                            </div>
                        </div>
                        <?php if (isset($errors['image'])): ?>
                        <p class="error-text"><?= htmlspecialchars($errors['image'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Tên sản phẩm -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Tên sản phẩm <span class="required-star">*</span></label>
                        <input type="text" name="name" required maxlength="255"
                               value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Nhập tên sản phẩm (tối đa 255 ký tự)"/>
                        <?php if (isset($errors['name'])): ?>
                        <p class="error-text"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Danh mục -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Danh mục <span class="required-star">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category'])): ?>
                        <p class="error-text"><?= htmlspecialchars($errors['category'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Trạng thái -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Trạng thái</label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="status" checked class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"/>
                            <span class="text-sm font-medium text-slate-900">Hiển thị sản phẩm</span>
                        </label>
                    </div>

                    <!-- Mô tả -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Mô tả</label>
                        <textarea name="description" rows="4" maxlength="1000"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Nhập mô tả sản phẩm (tối đa 1000 ký tự)"><?= htmlspecialchars($_SESSION['old']['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <!-- Kích cỡ & Giá -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-3">Kích cỡ & Giá <span class="required-star">*</span></label>
                        <div class="space-y-3">
                            <?php foreach ($allSizes as $size): ?>
                            <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                                <label class="flex items-center gap-2 cursor-pointer flex-1">
                                    <input type="checkbox" name="sizes[]" value="<?= $size['id'] ?>" class="w-4 h-4 rounded border-gray-300 text-blue-600"/>
                                    <span class="font-medium"><?= htmlspecialchars($size['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="prices[<?= $size['id'] ?>]" min="0" max="99999999" step="1000"
                                           class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Giá"/>
                                    <span class="text-sm text-gray-600">đ</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (isset($errors['sizes'])): ?>
                        <p class="error-text"><?= htmlspecialchars($errors['sizes'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Topping -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-3">Topping</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <?php foreach ($allToppings as $topping): ?>
                            <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="toppings[]" value="<?= $topping['id'] ?>" class="w-4 h-4 rounded border-gray-300 text-blue-600"/>
                                <span class="text-sm"><?= htmlspecialchars($topping['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 mt-8 pt-6 border-t">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        Thêm sản phẩm
                    </button>
                    <a href="<?= BASE_URL ?>?action=admin-products" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                        Hủy
                    </a>
                </div>
            </form>
        </main>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
<?php unset($_SESSION['old']); ?>
