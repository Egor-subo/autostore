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
