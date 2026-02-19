<?php
require __DIR__ . '/bootstrap.php';
require_auth();

$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $captcha = trim($_POST['captcha'] ?? '');

    if (!check_captcha($captcha)) {
        set_flash('error', 'Неверно решена капча. Попробуйте снова.');
        refresh_captcha();
        redirect_to('feedback.php');
    }

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $q = db()->prepare('INSERT INTO feedback_messages (user_id, name, email, message) VALUES (?, ?, ?, ?)');
        $q->execute([$user['id'], $name, $email, $message]);
        set_flash('success', 'Спасибо! Ваше сообщение отправлено.');
        refresh_captcha();
    } else {
        set_flash('error', 'Проверьте корректность заполнения полей.');
    }

    redirect_to('feedback.php');
}

$myMessagesStmt = db()->prepare('SELECT * FROM feedback_messages WHERE user_id=? ORDER BY id DESC');
$myMessagesStmt->execute([$user['id']]);
$myMessages = $myMessagesStmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="page-section mb-3"><h2>Обратная связь</h2><p class="text-muted mb-0">Сообщения могут отправлять только зарегистрированные пользователи.</p></div>
<?php if ($ok = flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
<?php if ($err = flash('error')): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<div class="page-section"><form method="post" class="row g-3">
<div class="col-md-6"><label class="form-label">Имя</label><input name="name" class="form-control" required value="<?= e($user['login']) ?>"></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= e($user['email']) ?>"></div>
<div class="col-12"><label class="form-label">Сообщение</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
<div class="col-md-6"><label class="form-label">Капча: <?= e(captcha_question()) ?></label><input name="captcha" class="form-control" required></div>
<div class="col-12"><button class="btn btn-primary">Отправить</button></div>
</form></div>

<?php if ($myMessages): ?>
<div class="page-section mt-3">
    <h5 class="mb-3">Мои обращения</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <tr><th>Дата</th><th>Сообщение</th><th>Ответ администратора</th></tr>
            <?php foreach ($myMessages as $item): ?>
                <tr>
                    <td><?= e($item['created_at']) ?></td>
                    <td><?= e($item['message']) ?></td>
                    <td>
                        <?php if (!empty($item['admin_reply'])): ?>
                            <?= e($item['admin_reply']) ?>
                        <?php else: ?>
                            <span class="text-muted">Пока нет ответа</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
