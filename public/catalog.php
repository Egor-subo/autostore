<?php
require __DIR__ . '/bootstrap.php';
$type = $_GET['type'] ?? '';
$category = (int)($_GET['category'] ?? 0);
$sql = 'SELECT p.*, c.name category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE 1=1';
$params = [];
if (in_array($type, ['car', 'part'], true)) { $sql .= ' AND p.type=?'; $params[] = $type; }
if ($category > 0) { $sql .= ' AND p.category_id=?'; $params[] = $category; }
$sql .= ' ORDER BY p.created_at DESC';
$stmt = db()->prepare($sql); $stmt->execute($params); $products = $stmt->fetchAll();
$categories = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-section mb-3"><h2>Каталог товаров</h2><p class="text-muted mb-0">Выберите автомобили или комплектующие через фильтры ниже.</p></div>
<div class="page-section mb-3"><form class="row g-2">
    <div class="col-md-3"><select name="type" class="form-select"><option value="">Все типы</option><option value="car" <?= $type==='car'?'selected':'' ?>>Автомобили</option><option value="part" <?= $type==='part'?'selected':'' ?>>Комплектующие</option></select></div>
    <div class="col-md-4"><select name="category" class="form-select"><option value="0">Все категории</option><?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $category===$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Фильтр</button></div>
</form></div>
<div class="row g-3"><?php foreach ($products as $product): ?><div class="col-md-4"><div class="card h-100"><img src="<?= e($product['image_url'] ?: 'https://via.placeholder.com/800x500') ?>" class="card-img-top"><div class="card-body d-flex flex-column"><small class="text-muted"><?= e($product['category_name']) ?></small><h5><?= e($product['title']) ?></h5><p><?= e($product['short_description']) ?></p><div class="price mb-2"><?= number_format((float)$product['price'], 0, ',', ' ') ?> ₽</div><a class="btn btn-outline-primary mt-auto" href="<?= e(url('product.php?id=' . (int)$product['id'])) ?>">Подробнее</a></div></div></div><?php endforeach; ?></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
