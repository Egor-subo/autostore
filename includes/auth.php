<?php

require_once __DIR__ . '/db.php';

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user) {
        return $user;
    }

    try {
        $stmt = db()->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE u.id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;

        if ($user && (int)($user['is_blocked'] ?? 0) === 1) {
            unset($_SESSION['user_id']);
            $user = null;
        }
    } catch (PDOException $e) {
        return null;
    }

    return $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user && $user['role_name'] === 'admin';
}

function require_auth(): void
{
    if (!is_logged_in()) {
        redirect_to('login.php');
        exit;
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Доступ запрещён');
    }
}
