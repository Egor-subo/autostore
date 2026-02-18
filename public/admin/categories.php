<?php
require __DIR__ . '/../bootstrap.php'; require_admin();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['add'])) db()->prepare('INSERT INTO categories(name,type) VALUES(?,?)')->execute([trim($_POST['name']), $_POST['type']]);
    if (isset($_POST['delete'])) db()->prepare('DELETE FROM categories WHERE id=?')->execute([(int)$_POST['id']]);
    redirect_to('admin/categories.php');
}
$cats = db()->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<h3>Категории</h3>
<form method="post" class="row g-2 mb-3"><div class="col-md-4"><input name="name" class="form-control" placeholder="Название" required></div><div class="col-md-3"><select name="type" class="form-select"><option value="car">Авто</option><option value="part">Комплектующие</option></select></div><div class="col-md-2"><button name="add" class="btn btn-primary">Добавить</button></div></form>
<table class="table"><tr><th>ID</th><th>Название</th><th>Тип</th><th></th></tr><?php foreach($cats as $c): ?><tr><td><?= $c['id'] ?></td><td><?= e($c['name']) ?></td><td><?= e($c['type']) ?></td><td><form method="post"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button name="delete" class="btn btn-sm btn-outline-danger" data-confirm="Удалить?">Удалить</button></form></td></tr><?php endforeach; ?></table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
