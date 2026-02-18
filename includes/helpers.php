<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function flash(?string $key = null): ?string
{
    if ($key === null) {
        return null;
    }

    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function set_flash(string $key, string $msg): void
{
    $_SESSION['flash'][$key] = $msg;
}

function base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $config = require __DIR__ . '/../config/config.php';
    $raw = trim((string)($config['app']['base_url'] ?? ''));
    if ($raw === '' || $raw === '/') {
        $base = '';
        return $base;
    }

    $base = '/' . trim($raw, '/');
    return $base;
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $base = base_url();
    if ($path === '') {
        return $base !== '' ? $base . '/' : '/';
    }

    return ($base !== '' ? $base : '') . '/' . $path;
}

function redirect_to(string $path): void
{
    header('Location: ' . url($path));
    exit;
}
