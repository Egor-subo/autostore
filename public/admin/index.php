<?php
require __DIR__ . '/../bootstrap.php';
require_admin();
$stats = [
    'users' => db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'orders' => db()->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'products' => db()->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'reviews' => db()->query('SELECT COUNT(*) FROM reviews')->fetchColumn(),
];
include __DIR__ . '/../../includes/header.php';
?>
<h2>Админ-панель</h2>
<div class="row g-3 mb-4"><?php foreach($stats as $k=>$v): ?><div class="col-md-3"><div class="card"><div class="card-body"><small><?= e($k) ?></small><h3><?= (int)$v ?></h3></div></div></div><?php endforeach; ?></div>
<div class="list-group">
    <a class="list-group-item" href="/admin/products.php">Управление товарами</a>
    <a class="list-group-item" href="/admin/categories.php">Категории</a>
    <a class="list-group-item" href="/admin/orders.php">Заказы и статусы</a>
    <a class="list-group-item" href="/admin/reviews.php">Отзывы и ответы</a>
    <a class="list-group-item" href="/admin/feedback.php">Сообщения обратной связи</a>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
