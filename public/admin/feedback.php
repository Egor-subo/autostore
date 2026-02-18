<?php
require __DIR__ . '/../bootstrap.php'; require_admin();
$rows=db()->query('SELECT * FROM feedback_messages ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<h3>Обратная связь</h3>
<table class="table"><tr><th>Дата</th><th>Имя</th><th>Email</th><th>Сообщение</th></tr><?php foreach($rows as $r): ?><tr><td><?= e($r['created_at']) ?></td><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['message']) ?></td></tr><?php endforeach; ?></table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
