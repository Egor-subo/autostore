<?php
require __DIR__ . '/../bootstrap.php'; require_admin();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    db()->prepare('UPDATE orders SET status_id=? WHERE id=?')->execute([(int)$_POST['status_id'], (int)$_POST['id']]);
    header('Location: /admin/orders.php'); exit;
}
$statuses=db()->query('SELECT * FROM order_statuses')->fetchAll();
$orders=db()->query('SELECT o.*,u.login,s.name status_name FROM orders o JOIN users u ON u.id=o.user_id JOIN order_statuses s ON s.id=o.status_id ORDER BY o.id DESC')->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<h3>Управление заказами</h3>
<table class="table table-striped"><tr><th>ID</th><th>Пользователь</th><th>Сумма</th><th>Адрес</th><th>Статус</th><th></th></tr>
<?php foreach($orders as $o): ?><tr><td><?= $o['id'] ?></td><td><?= e($o['login']) ?></td><td><?= number_format((float)$o['total_amount'],0,',',' ') ?> ₽</td><td><?= e($o['address']) ?></td><td><?= e($o['status_name']) ?></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="id" value="<?= $o['id'] ?>"><select name="status_id" class="form-select form-select-sm"><?php foreach($statuses as $s): ?><option value="<?= $s['id'] ?>" <?= $s['id']==$o['status_id']?'selected':'' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-primary">OK</button></form></td></tr><?php endforeach; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
