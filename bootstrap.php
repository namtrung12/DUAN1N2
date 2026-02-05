<?php

session_start();

date_default_timezone_set('Asia/Ho_Chi_Minh');

define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('STORAGE_PATH', BASE_PATH . 'storage' . DIRECTORY_SEPARATOR . 'data');

// Database constants (for compatibility with existing layout code)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'du_an1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . 'app' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $class . '.php',
        BASE_PATH . 'app' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . $class . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once BASE_PATH . 'app' . DIRECTORY_SEPARATOR . 'helpers.php';

define('BASE_URL', base_url());
define('PATH_VIEW', BASE_PATH);
define('BASE_ASSETS', BASE_URL . 'assets/');
define('BASE_ASSETS_UPLOADS', BASE_URL . 'assets/uploads/');
