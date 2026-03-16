<?php
require __DIR__ . '/bootstrap.php';
$products = db()->query('SELECT * FROM products ORDER BY created_at DESC LIMIT 6')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<section class="hero mb-4">
    <h1>Магазин автомобилей и комплектующих</h1>
    <p>Выбирайте автомобили, детали, оформляйте заказы, оставляйте отзывы и получайте обратную связь.</p>
    <a href="<?= e(url('catalog.php')) ?>" class="btn btn-light">Перейти в каталог</a>
</section>
<div class="page-section mb-3"><h3 class="mb-3">Популярные предложения</h3><div class="row g-3">
    <?php foreach ($products as $product): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="<?= e($product['image_url'] ?: 'https://via.placeholder.com/800x500') ?>" class="card-img-top" alt="">
                <div class="card-body d-flex flex-column">
                    <h5><?= e($product['title']) ?></h5>
                    <p><?= e($product['short_description']) ?></p>
                    <div class="price mb-2"><?= number_format((float)$product['price'], 0, ',', ' ') ?> ₽</div>
                    <a class="btn btn-outline-primary mt-auto" href="<?= e(url('product.php?id=' . (int)$product['id'])) ?>">Подробнее</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
