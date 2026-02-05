<?php

function base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = rtrim(str_replace('\\', '/', dirname($script)), '/');
    $basePath = $basePath === '.' ? '' : $basePath;
    return $scheme . '://' . $host . ($basePath ? $basePath . '/' : '/');
}

function redirect(string $actionOrUrl, array $params = []): void
{
    if (strpos($actionOrUrl, 'http') === 0 || strpos($actionOrUrl, '/') === 0) {
        $url = $actionOrUrl;
    } else {
        $query = array_merge(['action' => $actionOrUrl], $params);
        $url = BASE_URL . '?' . http_build_query($query);
    }
    header('Location: ' . $url);
    exit;
}

function slugify(string $text): string
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text === '' ? 'n-a' : $text;
}

function get_site_settings(): array
{
    $settings = new Settings(get_store());
    return $settings->all();
}

function set_flash(string $key, $value): void
{
    $_SESSION[$key] = $value;
}

function get_store(): DataStore
{
    static $store;
    if (!$store) {
        $store = new DataStore(STORAGE_PATH);
    }
    return $store;
}

function ensure_user_session(): void
{
    if (!isset($_SESSION['user'])) {
        return;
    }
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('errors', ['Please login to continue.']);
        redirect('login');
    }
}

function require_admin(): void
{
    require_login();
    if ((int)$_SESSION['user']['role_id'] !== 2) {
        set_flash('errors', ['Access denied.']);
        redirect('home');
    }
}
