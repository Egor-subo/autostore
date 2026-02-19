<?php
require __DIR__ . '/../bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $id = (int)($_POST['id'] ?? 0);
        $type = in_array($_POST['type'] ?? '', ['car', 'part'], true) ? $_POST['type'] : 'car';
        $categoryId = (int)($_POST['category_id'] ?? 0);

        $catStmt = db()->prepare('SELECT id FROM categories WHERE id=? AND type=?');
        $catStmt->execute([$categoryId, $type]);
        $validCategoryId = (int)$catStmt->fetchColumn();

        if ($validCategoryId <= 0) {
            set_flash('error', 'Категория не подходит для выбранного типа товара.');
            redirect_to('admin/products.php');
        }

        $data = [
            $validCategoryId,
            $type,
            trim((string)($_POST['title'] ?? '')),
            trim((string)($_POST['short_description'] ?? '')),
            trim((string)($_POST['description'] ?? '')),
            max(0, (float)($_POST['price'] ?? 0)),
            max(0, (int)($_POST['stock'] ?? 0)),
            trim((string)($_POST['image_url'] ?? '')),
        ];

        if ($id > 0) {
            $data[] = $id;
            db()->prepare('UPDATE products SET category_id=?,type=?,title=?,short_description=?,description=?,price=?,stock=?,image_url=? WHERE id=?')->execute($data);
        } else {
            db()->prepare('INSERT INTO products(category_id,type,title,short_description,description,price,stock,image_url) VALUES(?,?,?,?,?,?,?,?)')->execute($data);
        }
    }

    if (isset($_POST['delete'])) {
        db()->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_POST['id']]);
    }

    redirect_to('admin/products.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $s = db()->prepare('SELECT * FROM products WHERE id=?');
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}

$products = db()->query('SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll();
$categories = db()->query('SELECT * FROM categories ORDER BY type, name')->fetchAll();
$selectedType = $edit['type'] ?? 'car';

include __DIR__ . '/../../includes/header.php';
?>
<h3>Товары</h3>
<?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="row g-2 mb-4">
<input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<div class="col-md-3"><input name="title" class="form-control" placeholder="Название" value="<?= e($edit['title'] ?? '') ?>" required></div>
<div class="col-md-2">
    <select id="productType" name="type" class="form-select">
        <option value="car" <?= $selectedType === 'car' ? 'selected' : '' ?>>Авто</option>
        <option value="part" <?= $selectedType === 'part' ? 'selected' : '' ?>>Комплектующие</option>
    </select>
</div>
<div class="col-md-2">
    <select id="productCategory" name="category_id" class="form-select">
        <?php foreach($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" data-type="<?= e($c['type']) ?>" <?= ((int)($edit['category_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="col-md-2"><input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="Цена" value="<?= e($edit['price'] ?? '') ?>" required></div>
<div class="col-md-1"><input type="number" min="0" name="stock" class="form-control" placeholder="Склад" value="<?= e($edit['stock'] ?? '0') ?>"></div>
<div class="col-md-2"><input name="image_url" class="form-control" placeholder="URL фото" value="<?= e($edit['image_url'] ?? '') ?>"></div>
<div class="col-12"><input name="short_description" class="form-control" placeholder="Кратко" value="<?= e($edit['short_description'] ?? '') ?>" required></div>
<div class="col-12"><textarea name="description" class="form-control" placeholder="Описание" required><?= e($edit['description'] ?? '') ?></textarea></div>
<div class="col-12"><button name="save" class="btn btn-primary">Сохранить</button> <a href="<?= e(url('admin/products.php')) ?>" class="btn btn-secondary">Очистить</a></div>
</form>
<table class="table table-sm"><tr><th>ID</th><th>Название</th><th>Тип</th><th>Категория</th><th>Цена</th><th></th></tr><?php foreach($products as $p): ?><tr><td><?= $p['id'] ?></td><td><?= e($p['title']) ?></td><td><?= e($p['type']) ?></td><td><?= e($p['category_name']) ?></td><td><?= number_format((float)$p['price'],0,',',' ') ?></td><td><a href="<?= e(url('admin/products.php?edit=' . $p['id'])) ?>" class="btn btn-sm btn-outline-primary">Ред.</a> <form method="post" class="d-inline"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button name="delete" class="btn btn-sm btn-outline-danger" data-confirm="Удалить?">Удалить</button></form></td></tr><?php endforeach; ?></table>
<script>
(() => {
    const typeSelect = document.getElementById('productType');
    const categorySelect = document.getElementById('productCategory');
    if (!typeSelect || !categorySelect) return;

    const applyFilter = () => {
        const chosenType = typeSelect.value;
        let firstVisible = null;

        for (const option of categorySelect.options) {
            const visible = option.dataset.type === chosenType;
            option.hidden = !visible;
            if (visible && firstVisible === null) firstVisible = option.value;
        }

        const current = categorySelect.selectedOptions[0];
        if (!current || current.hidden) {
            if (firstVisible !== null) categorySelect.value = firstVisible;
        }
    };

    typeSelect.addEventListener('change', applyFilter);
    applyFilter();
})();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
