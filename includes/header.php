<?php $user = current_user(); ?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/index.php">AutoStore</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/catalog.php">Каталог</a></li>
                <li class="nav-item"><a class="nav-link" href="/about.php">О нас</a></li>
                <li class="nav-item"><a class="nav-link" href="/feedback.php">Обратная связь</a></li>
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="/cart.php">Корзина</a></li>
                    <li class="nav-item"><a class="nav-link" href="/orders.php">Мои заказы</a></li>
                    <li class="nav-item"><a class="nav-link" href="/profile.php">Профиль</a></li>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                    <li class="nav-item"><a class="nav-link text-warning" href="/admin/index.php">Админка</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex gap-2">
                <?php if (!$user): ?>
                    <a class="btn btn-outline-light btn-sm" href="/login.php">Вход</a>
                    <a class="btn btn-primary btn-sm" href="/register.php">Регистрация</a>
                <?php else: ?>
                    <span class="text-light me-2 align-self-center">Привет, <?= e($user['login']) ?></span>
                    <a class="btn btn-danger btn-sm" href="/logout.php">Выход</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="container mb-5">
