<?php
require __DIR__ . '/bootstrap.php';
require_auth();
$userId = current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['qty'] ?? [] as $itemId => $qty) {
            $q = db()->prepare('UPDATE cart_items SET quantity=? WHERE id=? AND user_id=?');
            $q->execute([max(1, (int)$qty), (int)$itemId, $userId]);
        }
    }
    if (isset($_POST['remove'])) {
        $q = db()->prepare('DELETE FROM cart_items WHERE id=? AND user_id=?');
        $q->execute([(int)$_POST['item_id'], $userId]);
    }
    if (isset($_POST['checkout'])) {
        $address = trim($_POST['address'] ?? '');
        $items = db()->prepare('SELECT ci.*, p.price FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.user_id=?');
        $items->execute([$userId]);
        $items = $items->fetchAll();
        if ($items && $address) {
            $total = 0; foreach ($items as $i) $total += $i['price'] * $i['quantity'];
            db()->beginTransaction();
            $newStatusId = (int)db()->query("SELECT id FROM order_statuses WHERE name='Новый'")->fetchColumn();
            $q = db()->prepare('INSERT INTO orders (user_id, status_id, total_amount, address) VALUES (?, ?, ?, ?)');
            $q->execute([$userId, $newStatusId, $total, $address]);
            $orderId = (int)db()->lastInsertId();
            $oi = db()->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
            foreach ($items as $i) $oi->execute([$orderId, $i['product_id'], $i['quantity'], $i['price']]);
            db()->prepare('DELETE FROM cart_items WHERE user_id=?')->execute([$userId]);
            db()->commit();
        }
        redirect_to('orders.php');
    }
    redirect_to('cart.php');
}

$items = db()->prepare('SELECT ci.id, ci.quantity, p.title, p.price FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.user_id=?');
$items->execute([$userId]);
$items = $items->fetchAll();
$total = 0; foreach ($items as $i) $total += $i['price'] * $i['quantity'];
include __DIR__ . '/../includes/header.php';
?>
<h2>Корзина</h2>
<form method="post">
<table class="table"><tr><th>Товар</th><th>Цена</th><th>Количество</th><th>Сумма</th><th></th></tr>
<?php foreach($items as $item): ?><tr><td><?= e($item['title']) ?></td><td><?= number_format((float)$item['price'],0,',',' ') ?> ₽</td><td><input class="form-control" style="width:100px" type="number" min="1" name="qty[<?= (int)$item['id'] ?>]" value="<?= (int)$item['quantity'] ?>"></td><td><?= number_format($item['price']*$item['quantity'],0,',',' ') ?> ₽</td><td><button name="remove" class="btn btn-sm btn-outline-danger" data-confirm="Удалить товар?" value="1">Удалить</button><input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>"></td></tr><?php endforeach; ?>
</table>
<div class="d-flex justify-content-between align-items-center"><strong>Итого: <?= number_format($total,0,',',' ') ?> ₽</strong><button name="update" class="btn btn-outline-primary">Обновить корзину</button></div>
<div class="mt-3"><label class="form-label">Адрес доставки</label><input name="address" class="form-control" required></div>
<button name="checkout" class="btn btn-success mt-3" <?= empty($items)?'disabled':'' ?>>Оформить заказ</button>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
