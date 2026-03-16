<?php
require __DIR__ . '/bootstrap.php';

$scriptName = basename($_SERVER['SCRIPT_NAME'] ?? '');
$isCarsPage = $scriptName === 'cars.php';
$isPartsPage = $scriptName === 'parts.php';
$isTypedPage = $isCarsPage || $isPartsPage;

$type = $_GET['type'] ?? '';
if ($isCarsPage) {
    $type = 'car';
} elseif ($isPartsPage) {
    $type = 'part';
}

$allowedTypes = ['car', 'part'];
if (!in_array($type, $allowedTypes, true)) {
    $type = '';
}

$category = (int)($_GET['category'] ?? 0);
$priceMinRaw = trim((string)($_GET['price_min'] ?? ''));
$priceMaxRaw = trim((string)($_GET['price_max'] ?? ''));
$priceMin = $priceMinRaw !== '' ? (float)$priceMinRaw : null;
$priceMax = $priceMaxRaw !== '' ? (float)$priceMaxRaw : null;
$search = trim((string)($_GET['q'] ?? ''));
$search = preg_replace('/\s+/u', ' ', $search) ?? '';

$categorySql = 'SELECT * FROM categories';
$categoryParams = [];
if ($type !== '') {
    $categorySql .= ' WHERE type=?';
    $categoryParams[] = $type;
}
$categorySql .= ' ORDER BY name';
$catStmt = db()->prepare($categorySql);
$catStmt->execute($categoryParams);
$categories = $catStmt->fetchAll();
$availableCategoryIds = array_map(static fn(array $cat): int => (int)$cat['id'], $categories);

if ($category > 0 && !in_array($category, $availableCategoryIds, true)) {
    $category = 0;
}

if ($priceMin !== null && $priceMin < 0) {
    $priceMin = 0;
}
if ($priceMax !== null && $priceMax < 0) {
    $priceMax = 0;
}
if ($priceMin !== null && $priceMax !== null && $priceMax < $priceMin) {
    [$priceMin, $priceMax] = [$priceMax, $priceMin];
}

$sql = 'SELECT p.*, c.name category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE 1=1';
$params = [];
if ($type !== '') {
    $sql .= ' AND p.type=?';
    $params[] = $type;
}
if ($category > 0) {
    $sql .= ' AND p.category_id=?';
    $params[] = $category;
}
if ($priceMin !== null) {
    $sql .= ' AND p.price >= ?';
    $params[] = $priceMin;
}
if ($priceMax !== null) {
    $sql .= ' AND p.price <= ?';
    $params[] = $priceMax;
}
if ($search !== '') {
    $sql .= ' AND (LOWER(p.title) LIKE LOWER(?) OR LOWER(p.short_description) LIKE LOWER(?) OR LOWER(p.description) LIKE LOWER(?) OR LOWER(c.name) LIKE LOWER(?))';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= ' ORDER BY p.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = 'Каталог товаров';
$pageDescription = 'Ищите автомобили и комплектующие по категории и цене.';
if ($type === 'car') {
    $pageTitle = 'Автомобили';
    $pageDescription = 'Каталог автомобилей с фильтрацией по категориям, цене и поиском.';
} elseif ($type === 'part') {
    $pageTitle = 'Комплектующие';
    $pageDescription = 'Каталог комплектующих с фильтрацией по категориям, цене и поиском.';
}

include __DIR__ . '/../includes/header.php';
?>
<div class="page-section mb-3">
    <h2><?= e($pageTitle) ?></h2>
    <p class="text-muted mb-0"><?= e($pageDescription) ?></p>
</div>

<div class="page-section mb-3">
    <form method="get" class="row g-2">
        <?php if ($isTypedPage): ?>
            <input type="hidden" name="type" value="<?= e($type) ?>">
            <div class="col-md-3">
                <input class="form-control" value="<?= $type === 'car' ? 'Автомобили' : 'Комплектующие' ?>" disabled>
            </div>
        <?php else: ?>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">Все типы</option>
                    <option value="car" <?= $type === 'car' ? 'selected' : '' ?>>Автомобили</option>
                    <option value="part" <?= $type === 'part' ? 'selected' : '' ?>>Комплектующие</option>
                </select>
            </div>
        <?php endif; ?>

        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="0">Все категории</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= $category === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><input type="number" min="0" step="1" name="price_min" class="form-control" placeholder="Цена от" value="<?= e($priceMinRaw) ?>"></div>
        <div class="col-md-2"><input type="number" min="0" step="1" name="price_max" class="form-control" placeholder="Цена до" value="<?= e($priceMaxRaw) ?>"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Фильтр</button></div>
        <?php if ($isTypedPage): ?>
            <div class="col-12"><input name="q" class="form-control" placeholder="Поиск по названию и описанию" value="<?= e($search) ?>"></div>
        <?php endif; ?>
    </form>
</div>

<div class="row g-3">
    <?php foreach ($products as $product): ?>
        <div class="col-md-4"><div class="card h-100 product-card"><img src="<?= e($product['image_url'] ?: 'https://via.placeholder.com/800x500') ?>" class="card-img-top"><div class="card-body d-flex flex-column"><small class="text-muted"><?= e($product['category_name']) ?></small><h5><?= e($product['title']) ?></h5><p><?= e($product['short_description']) ?></p><div class="price mb-2"><?= number_format((float)$product['price'], 0, ',', ' ') ?> ₽</div><a class="btn btn-outline-primary mt-auto" href="<?= e(url('product.php?id=' . (int)$product['id'])) ?>">Подробнее</a></div></div></div>
    <?php endforeach; ?>
    <?php if (!$products): ?>
        <div class="col-12"><div class="alert alert-light border">По вашему запросу ничего не найдено.</div></div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
