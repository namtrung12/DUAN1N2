<?php

class SettingsController
{
    public function __construct()
    {
        $this->checkAuth();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if ($_SESSION['user']['role_id'] != 2) {
            $_SESSION['errors'] = ['auth' => 'Chỉ Admin mới có quyền truy cập'];
            header('Location: ' . BASE_URL . '?action=admin');
            exit;
        }
    }

    public function index()
    {
        $settings = $this->getSettings();
        require_once PATH_VIEW . 'admin/settings.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-settings');
            exit;
        }

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME),
                DB_USERNAME,
                DB_PASSWORD,
                DB_OPTIONS
            );

            // Update text settings first
            $siteName = $_POST['site_name'] ?? '';
            $siteAddress = $_POST['site_address'] ?? '';
            $contactPhone = $_POST['contact_phone'] ?? '';
            $contactEmail = $_POST['contact_email'] ?? '';

            if ($siteName) {
                $this->updateSetting($pdo, 'site_name', $siteName);
            }
            if ($siteAddress) {
                $this->updateSetting($pdo, 'site_address', $siteAddress);
            }
            if ($contactPhone) {
                $this->updateSetting($pdo, 'contact_phone', $contactPhone);
            }
            if ($contactEmail) {
                $this->updateSetting($pdo, 'contact_email', $contactEmail);
            }

            // Update logo
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $logoPath = upload_file('settings', $_FILES['logo']);
                    $this->updateSetting($pdo, 'site_logo', 'assets/uploads/' . $logoPath);
                } catch (Exception $e) {
                    throw new Exception('Lỗi upload logo: ' . $e->getMessage());
                }
            }

            // Update banners (multiple)
            if (isset($_FILES['banners']) && is_array($_FILES['banners']['name'])) {
                $hasValidBanner = false;
                foreach ($_FILES['banners']['name'] as $name) {
                    if (!empty($name)) {
                        $hasValidBanner = true;
                        break;
                    }
                }
                
                if ($hasValidBanner) {
                    // Xóa các banner cũ trước
                    $stmt = $pdo->prepare("DELETE FROM settings WHERE k LIKE 'banner_%'");
                    $stmt->execute();
                    
                    $bannerIndex = 1;
                    foreach ($_FILES['banners']['name'] as $index => $name) {
                        if (!empty($name) && $_FILES['banners']['error'][$index] === UPLOAD_ERR_OK) {
                            try {
                                $file = [
                                    'name' => $_FILES['banners']['name'][$index],
                                    'type' => $_FILES['banners']['type'][$index],
                                    'tmp_name' => $_FILES['banners']['tmp_name'][$index],
                                    'error' => $_FILES['banners']['error'][$index],
                                    'size' => $_FILES['banners']['size'][$index]
                                ];
                                $bannerPath = upload_file('banners', $file);
                                $fullPath = 'assets/uploads/' . $bannerPath;
                                $this->updateSetting($pdo, 'banner_' . $bannerIndex, $fullPath);
                                $bannerIndex++;
                            } catch (Exception $e) {
                                throw new Exception('Lỗi upload banner ' . ($index + 1) . ': ' . $e->getMessage());
                            }
                        }
                    }
                }
            }

            $_SESSION['success'] = 'Cập nhật cài đặt thành công! Đang chuyển về trang chủ...';
            
            // Redirect về trang chủ sau 2 giây
            header('Location: ' . BASE_URL);
            exit;
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => $e->getMessage()];
            header('Location: ' . BASE_URL . '?action=admin-settings');
            exit;
        }
    }

    private function getSettings()
    {
        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME),
                DB_USERNAME,
                DB_PASSWORD,
                DB_OPTIONS
            );

            $stmt = $pdo->query("SELECT * FROM settings");
            $results = $stmt->fetchAll();
            
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['k']] = $row['v'];
            }

            return $settings;
        } catch (Exception $e) {
            return [];
        }
    }

    private function updateSetting($pdo, $key, $value)
    {
        $sql = "INSERT INTO settings (`k`, `v`) VALUES (:k, :v) 
                ON DUPLICATE KEY UPDATE `v` = VALUES(`v`)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':k' => $key,
            ':v' => $value
        ]);
    }
}
