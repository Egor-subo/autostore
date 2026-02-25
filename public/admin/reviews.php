<?php
require __DIR__ . '/../bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reply'])) {
        db()->prepare('UPDATE reviews SET admin_reply=? WHERE id=?')->execute([trim((string)($_POST['admin_reply'] ?? '')), (int)$_POST['id']]);
        set_flash('success', 'Ответ на отзыв сохранён.');
    }

    if (isset($_POST['delete'])) {
        db()->prepare('DELETE FROM reviews WHERE id=?')->execute([(int)$_POST['id']]);
        set_flash('success', 'Отзыв удалён.');
    }

    redirect_to('admin/reviews.php');
}

$reviews = db()->query('SELECT r.*,u.login,p.title FROM reviews r JOIN users u ON u.id=r.user_id JOIN products p ON p.id=r.product_id ORDER BY r.id DESC')->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<h3>Отзывы</h3>
<?php if ($ok = flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
<?php foreach ($reviews as $r): ?>
<div class="card mb-3">
    <div class="card-body">
        <div><strong><?= e($r['login']) ?></strong> → <?= e($r['title']) ?> (<?= (int)$r['rating'] ?>/5)</div>
        <p class="mb-3"><?= e($r['comment']) ?></p>
        <form method="post" class="row g-2">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <div class="col-md-8"><input name="admin_reply" value="<?= e((string)($r['admin_reply'] ?? '')) ?>" class="form-control" placeholder="Ответ администратора"></div>
            <div class="col-md-4 d-flex gap-2">
                <button name="reply" class="btn btn-primary w-100">Сохранить ответ</button>
                <button name="delete" class="btn btn-outline-danger" data-confirm="Удалить отзыв?">Удалить отзыв</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>
<?php if (!$reviews): ?><div class="alert alert-light border">Отзывов пока нет.</div><?php endif; ?>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
