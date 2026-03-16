<?php
require __DIR__ . '/bootstrap.php';
require_auth();

$userId = (int)current_user()['id'];

$stmt = db()->prepare('SELECT o.*, s.name status_name FROM orders o JOIN order_statuses s ON s.id=o.status_id WHERE o.user_id=? ORDER BY o.created_at DESC');
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

$orderItemsByOrder = [];
if ($orders) {
    $ids = array_map(static fn(array $o): int => (int)$o['id'], $orders);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $itemsStmt = db()->prepare("SELECT oi.order_id, oi.quantity, oi.unit_price, p.title, p.type FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id IN ($placeholders) ORDER BY oi.order_id DESC, oi.id ASC");
    $itemsStmt->execute($ids);
    foreach ($itemsStmt->fetchAll() as $item) {
        $orderId = (int)$item['order_id'];
        $orderItemsByOrder[$orderId][] = $item;
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="page-section mb-3">
    <h2>Мои заказы</h2>
    <p class="text-muted mb-0">Здесь отображаются состав заказа, статус, адрес и итоговая сумма.</p>
</div>

<?php if (!$orders): ?>
    <div class="alert alert-light border">У вас пока нет заказов.</div>
<?php endif; ?>

<?php foreach ($orders as $o): ?>
    <?php $orderId = (int)$o['id']; $items = $orderItemsByOrder[$orderId] ?? []; ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                <div><strong>Заказ #<?= $orderId ?></strong> <span class="text-muted ms-2"><?= e($o['created_at']) ?></span></div>
                <span class="badge text-bg-primary"><?= e($o['status_name']) ?></span>
            </div>

            <div class="mb-2"><strong>Адрес доставки:</strong> <?= e($o['address']) ?></div>

            <div class="table-responsive mb-2">
                <table class="table table-sm align-middle mb-0">
                    <tr><th>Товар</th><th>Тип</th><th>Цена</th><th>Кол-во</th><th>Сумма</th></tr>
                    <?php foreach ($items as $i): ?>
                        <tr>
                            <td><?= e($i['title']) ?></td>
                            <td><?= e($i['type']) ?></td>
                            <td><?= number_format((float)$i['unit_price'], 0, ',', ' ') ?> ₽</td>
                            <td><?= (int)$i['quantity'] ?></td>
                            <td><?= number_format((float)$i['unit_price'] * (int)$i['quantity'], 0, ',', ' ') ?> ₽</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div><strong>Итого:</strong> <?= number_format((float)$o['total_amount'], 0, ',', ' ') ?> ₽</div>
        </div>
    </div>
<?php endforeach; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
