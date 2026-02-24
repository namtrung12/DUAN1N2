<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt chung - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .settings-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .settings-card {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .settings-title {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 2rem;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem;
        }
        .form-control:focus {
            border-color: #7cb342;
            box-shadow: 0 0 0 0.2rem rgba(124, 179, 66, 0.25);
        }
        .logo-preview {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #dee2e6;
            overflow: hidden;
        }
        .logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .logo-preview-text {
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .upload-btn {
            background: #e9ecef;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: #495057;
            cursor: pointer;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .upload-btn:hover {
            background: #dee2e6;
        }
        .banner-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .banner-item {
            position: relative;
            aspect-ratio: 16/9;
            border-radius: 8px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .banner-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .banner-item.current {
            border-color: #7cb342;
            background: #f1f8e9;
        }
        .banner-item.current::after {
            content: 'CURRENT';
            position: absolute;
            top: 8px;
            left: 8px;
            background: #7cb342;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .banner-placeholder {
            color: #adb5bd;
            font-size: 3rem;
        }
        .add-banner-btn {
            border: 2px dashed #dee2e6;
            background: transparent;
            color: #6c757d;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .add-banner-btn:hover {
            border-color: #7cb342;
            color: #7cb342;
            background: #f1f8e9;
        }
        .save-btn {
            background: #7cb342;
            border: none;
            padding: 0.75rem 2.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }
        .save-btn:hover {
            background: #689f38;
        }
        .back-btn {
            background: #6c757d;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .back-btn:hover {
            background: #5a6268;
            color: white;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <h1 class="settings-title">Cài đặt chung</h1>

        <div class="settings-card">
            <form action="<?= BASE_URL ?>?action=admin-settings-update" method="POST" enctype="multipart/form-data">
                        
                        <!-- Thông tin cửa hàng -->
                        <div class="section-title">Thông tin cửa hàng</div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="site_name" class="form-label">Tên cửa hàng</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" 
                                       value="<?= htmlspecialchars($settings['site_name'] ?? 'Chill Drink') ?>" 
                                       placeholder="Chill Drink">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Logo</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="logo-preview" id="logoPreview">
                                        <?php if (!empty($settings['site_logo'])): ?>
                                            <img src="<?= BASE_URL . $settings['site_logo'] ?>" alt="Logo">
                                        <?php else: ?>
                                            <span class="logo-preview-text">CURRENT</span>
                                        <?php endif; ?>
                                    </div>
                                    <label for="logo" class="upload-btn">
                                        <i class="fas fa-upload"></i> Tải ảnh lên
                                    </label>
                                    <input type="file" class="d-none" id="logo" name="logo" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">Email liên hệ</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                       value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>" 
                                       placeholder="contact@chilldrink.com">
                            </div>

                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label">Số điện thoại hotline</label>
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                                       value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>" 
                                       placeholder="1900 1234">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="site_address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="site_address" name="site_address" rows="3" 
                                      placeholder="123 Đường ABC, Phường XYZ, Quận 1, TP. Hồ Chí Minh"><?= htmlspecialchars($settings['site_address'] ?? '') ?></textarea>
                        </div>

                        <!-- Ảnh Banner Slideshow -->
                        <div class="section-title">Ảnh Banner Slideshow</div>
                        
                        <div class="banner-grid" id="bannerGrid">
                            <?php 
                            $banners = [];
                            for ($i = 1; $i <= 3; $i++) {
                                if (!empty($settings["banner_$i"])) {
                                    $banners[] = $settings["banner_$i"];
                                }
                            }
                            
                            if (empty($banners)) {
                                $banners = [null, null, null];
                            }
                            
                            foreach ($banners as $index => $banner): 
                            ?>
                                <div class="banner-item <?= $index === 0 ? 'current' : '' ?>">
                                    <?php if ($banner): ?>
                                        <img src="<?= BASE_URL . $banner ?>" alt="Banner <?= $index + 1 ?>">
                                    <?php else: ?>
                                        <i class="fas fa-image banner-placeholder"></i>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            
                            <label for="banners" class="banner-item add-banner-btn">
                                <i class="fas fa-plus"></i>
                                <span>Thêm ảnh mới</span>
                            </label>
                        </div>
                        <input type="file" class="d-none" id="banners" name="banners[]" accept="image/*" multiple>

                <div class="mt-4 button-group">
                    <a href="<?= BASE_URL ?>?action=admin" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-btn">
                        <i class="fas fa-save"></i> Lưu Cài đặt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logo preview
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoPreview').innerHTML = '<img src="' + e.target.result + '" alt="Logo">';
                }
                reader.readAsDataURL(file);
            }
        });

        // Banner preview
        document.getElementById('banners').addEventListener('change', function(e) {
            const files = e.target.files;
            const grid = document.getElementById('bannerGrid');
            
            // Remove old previews except the add button
            const items = grid.querySelectorAll('.banner-item:not(.add-banner-btn)');
            items.forEach(item => item.remove());
            
            // Add new previews
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'banner-item' + (index === 0 ? ' current' : '');
                    div.innerHTML = '<img src="' + e.target.result + '" alt="Banner">';
                    grid.insertBefore(div, grid.querySelector('.add-banner-btn'));
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>
</html>
