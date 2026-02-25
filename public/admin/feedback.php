<?php
require __DIR__ . '/../bootstrap.php';
require_admin();

$filter = $_GET['status'] ?? 'all';
if (!in_array($filter, ['all', 'pending', 'answered'], true)) {
    $filter = 'all';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $id = (int)($_POST['id'] ?? 0);
    $reply = trim((string)($_POST['admin_reply'] ?? ''));

    $stmt = db()->prepare('UPDATE feedback_messages SET admin_reply=? WHERE id=?');
    $stmt->execute([$reply !== '' ? $reply : null, $id]);
    set_flash('success', 'Ответ сохранён.');
    redirect_to('admin/feedback.php?status=' . $filter);
}

$sql = 'SELECT fm.*, u.login AS user_login, u.phone AS user_phone
        FROM feedback_messages fm
        LEFT JOIN users u ON u.id = fm.user_id';
$params = [];
if ($filter === 'pending') {
    $sql .= ' WHERE fm.admin_reply IS NULL OR fm.admin_reply = ""';
} elseif ($filter === 'answered') {
    $sql .= ' WHERE fm.admin_reply IS NOT NULL AND fm.admin_reply <> ""';
}
$sql .= ' ORDER BY (fm.admin_reply IS NULL OR fm.admin_reply = "") DESC, fm.id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>
<div class="page-section mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h3 class="mb-1">Обратная связь</h3>
        <p class="text-muted mb-0">Здесь видно, кто написал обращение и на какое сообщение вы отвечаете.</p>
    </div>
    <div class="btn-group">
        <a class="btn btn-outline-secondary <?= $filter === 'all' ? 'active' : '' ?>" href="<?= e(url('admin/feedback.php?status=all')) ?>">Все</a>
        <a class="btn btn-outline-secondary <?= $filter === 'pending' ? 'active' : '' ?>" href="<?= e(url('admin/feedback.php?status=pending')) ?>">Без ответа</a>
        <a class="btn btn-outline-secondary <?= $filter === 'answered' ? 'active' : '' ?>" href="<?= e(url('admin/feedback.php?status=answered')) ?>">С ответом</a>
    </div>
</div>

<?php if ($ok = flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

<?php if (!$rows): ?>
    <div class="alert alert-light border">Обращений по выбранному фильтру пока нет.</div>
<?php endif; ?>

<?php foreach ($rows as $r): ?>
    <?php $isAnswered = !empty($r['admin_reply']); ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                <div>
                    <strong>Обращение #<?= (int)$r['id'] ?></strong>
                    <span class="text-muted ms-2"><?= e($r['created_at']) ?></span>
                </div>
                <span class="badge <?= $isAnswered ? 'text-bg-success' : 'text-bg-warning' ?>">
                    <?= $isAnswered ? 'Есть ответ' : 'Ждёт ответа' ?>
                </span>
            </div>

            <div class="mb-2">
                <div><strong>Пользователь:</strong> <?= e($r['user_login'] ?: $r['name']) ?></div>
                <div><strong>Email:</strong> <?= e($r['email']) ?></div>
                <?php if (!empty($r['user_phone'])): ?><div><strong>Телефон:</strong> <?= e($r['user_phone']) ?></div><?php endif; ?>
            </div>

            <div class="p-3 bg-light rounded mb-3">
                <strong>Текст обращения:</strong>
                <div class="mt-1" style="white-space: pre-wrap"><?= e($r['message']) ?></div>
            </div>

            <form method="post" class="row g-2">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <div class="col-12">
                    <label class="form-label">Ответ администратора</label>
                    <textarea name="admin_reply" class="form-control" rows="3" placeholder="Введите ответ пользователю..."><?= e((string)($r['admin_reply'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <button name="reply" class="btn btn-primary">Сохранить ответ</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
