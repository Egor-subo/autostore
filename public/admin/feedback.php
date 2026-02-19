<?php
require __DIR__ . '/../bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $id = (int)($_POST['id'] ?? 0);
    $reply = trim((string)($_POST['admin_reply'] ?? ''));

    $stmt = db()->prepare('UPDATE feedback_messages SET admin_reply=? WHERE id=?');
    $stmt->execute([$reply !== '' ? $reply : null, $id]);
    redirect_to('admin/feedback.php');
}

$rows = db()->query('SELECT * FROM feedback_messages ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<h3>Обратная связь</h3>
<table class="table align-middle">
    <tr><th>Дата</th><th>Имя</th><th>Email</th><th>Сообщение</th><th>Ответ администратора</th></tr>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['created_at']) ?></td>
            <td><?= e($r['name']) ?></td>
            <td><?= e($r['email']) ?></td>
            <td><?= e($r['message']) ?></td>
            <td style="min-width: 280px;">
                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input name="admin_reply" class="form-control" value="<?= e((string)($r['admin_reply'] ?? '')) ?>" placeholder="Ответ пользователю">
                    <button name="reply" class="btn btn-primary btn-sm">Сохранить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
