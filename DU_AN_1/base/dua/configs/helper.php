<?php

if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

if (!function_exists('upload_file')) {
    function upload_file($folder, $file)
    {
        // Tạo thư mục nếu chưa có
        $uploadDir = PATH_ASSETS_UPLOADS . $folder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Tạo tên file unique
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $filename = time() . '-' . uniqid() . '.' . $extension;
        $targetFile = $folder . '/' . $filename;
        $fullPath = PATH_ASSETS_UPLOADS . $targetFile;

        // Upload file
        if (move_uploaded_file($file["tmp_name"], $fullPath)) {
            return $targetFile;
        }

        throw new Exception('Upload file không thành công! Path: ' . $fullPath);
    }
}

if (!function_exists('get_site_settings')) {
    function get_site_settings()
    {
        if (!isset($GLOBALS['site_settings_loaded'])) {
            try {
                $pdo = new PDO(
                    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME),
                    DB_USERNAME,
                    DB_PASSWORD,
                    DB_OPTIONS
                );
                $stmt = $pdo->query("SELECT * FROM settings");
                $settingsData = $stmt->fetchAll();
                $GLOBALS['siteSettings'] = [];
                foreach ($settingsData as $row) {
                    $GLOBALS['siteSettings'][$row['k']] = $row['v'];
                }
                $GLOBALS['site_settings_loaded'] = true;
            } catch (Exception $e) {
                $GLOBALS['siteSettings'] = [];
                $GLOBALS['site_settings_loaded'] = true;
            }
        }
        return $GLOBALS['siteSettings'];
    }
}