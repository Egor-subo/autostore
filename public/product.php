<?php
require __DIR__ . '/bootstrap.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT p.*, c.name category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id=?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { http_response_code(404); exit('Товар не найден'); }

$maxReviewLength = 1500;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (isset($_POST['add_to_cart'])) {
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        $q = db()->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)');
        $q->execute([current_user()['id'], $id, $qty]);
        set_flash('success', 'Товар добавлен в корзину.');
        redirect_to('cart.php');
        exit;
    }
    if (isset($_POST['add_review'])) {
        $rating = max(1, min(5, (int)$_POST['rating']));
        $comment = trim((string)($_POST['comment'] ?? ''));

        if ($comment === '') {
            set_flash('error', 'Отзыв не может быть пустым.');
        } elseif (mb_strlen($comment) > $maxReviewLength) {
            set_flash('error', 'Отзыв слишком длинный. Максимум 1500 символов.');
        } else {
            $q = db()->prepare('INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)');
            $q->execute([current_user()['id'], $id, $rating, $comment]);
            set_flash('success', 'Отзыв отправлен.');
        }

        redirect_to('product.php?id=' . $id);
        exit;
    }
}

$reviews = db()->prepare('SELECT r.*, u.login FROM reviews r JOIN users u ON u.id=r.user_id WHERE product_id=? ORDER BY r.created_at DESC');
$reviews->execute([$id]);
$reviews = $reviews->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="row g-4">
  <div class="col-md-6"><img class="img-fluid rounded" src="<?= e($product['image_url'] ?: 'https://via.placeholder.com/800x500') ?>"></div>
  <div class="col-md-6"><h2><?= e($product['title']) ?></h2><p class="text-muted"><?= e($product['category_name']) ?></p><p><?= e($product['description']) ?></p><div class="price mb-3"><?= number_format((float)$product['price'], 0, ',', ' ') ?> ₽</div>
  <?php if(is_logged_in()): ?><form method="post" class="d-flex gap-2"><input type="number" name="quantity" min="1" value="1" class="form-control" style="max-width:100px"><button name="add_to_cart" class="btn btn-primary">В корзину</button></form><?php else: ?><a href="<?= e(url('login.php')) ?>" class="btn btn-outline-primary">Войдите для заказа</a><?php endif; ?>
  </div>
</div>
<hr>
<h4>Отзывы</h4>
<?php if ($ok = flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
<?php if ($err = flash('error')): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<?php if(is_logged_in()): ?><form method="post" class="mb-3"><div class="row g-2"><div class="col-md-2"><select name="rating" class="form-select"><?php for($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select></div><div class="col-md-8"><input name="comment" class="form-control" placeholder="Ваш отзыв" maxlength="1500" required></div><div class="col-md-2"><button name="add_review" class="btn btn-success w-100">Отправить</button></div></div></form><?php endif; ?>
<?php foreach($reviews as $r): ?><div class="card mb-2"><div class="card-body"><div class="d-flex justify-content-between"><strong><?= e($r['login']) ?></strong><span class="rating">★ <?= (int)$r['rating'] ?>/5</span></div><p class="mb-1"><?= e($r['comment']) ?></p><?php if($r['admin_reply']): ?><div class="alert alert-secondary py-2 mb-0"><strong>Ответ администратора:</strong> <?= e($r['admin_reply']) ?></div><?php endif; ?></div></div><?php endforeach; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
