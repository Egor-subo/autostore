<?php
require __DIR__ . '/bootstrap.php';
require_auth();
$stmt = db()->prepare('SELECT o.*, s.name status_name FROM orders o JOIN order_statuses s ON s.id=o.status_id WHERE user_id=? ORDER BY o.created_at DESC');
$stmt->execute([current_user()['id']]);
$orders = $stmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2>Мои заказы</h2>
<table class="table table-striped"><tr><th>#</th><th>Дата</th><th>Статус</th><th>Сумма</th><th>Адрес</th></tr>
<?php foreach($orders as $o): ?><tr><td><?= (int)$o['id'] ?></td><td><?= e($o['created_at']) ?></td><td><?= e($o['status_name']) ?></td><td><?= number_format((float)$o['total_amount'],0,',',' ') ?> ₽</td><td><?= e($o['address']) ?></td></tr><?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
