<?php
require __DIR__ . '/bootstrap.php';
require_auth();

$user = current_user();
$maxFeedbackLength = 1500;

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

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        set_flash('error', 'Проверьте корректность заполнения полей.');
    } elseif (mb_strlen($message) > $maxFeedbackLength) {
        set_flash('error', 'Сообщение слишком длинное. Максимум 1500 символов.');
    } else {
        $q = db()->prepare('INSERT INTO feedback_messages (user_id, name, email, message) VALUES (?, ?, ?, ?)');
        $q->execute([$user['id'], $name, $email, $message]);
        set_flash('success', 'Спасибо! Ваше обращение отправлено. Номер обращения появится ниже.');
        refresh_captcha();
    }

    redirect_to('feedback.php');
}

$myMessagesStmt = db()->prepare('SELECT * FROM feedback_messages WHERE user_id=? ORDER BY id DESC');
$myMessagesStmt->execute([$user['id']]);
$myMessages = $myMessagesStmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="page-section mb-3">
    <h2>Обратная связь</h2>
    <p class="text-muted mb-0">Вы видите только свои обращения. Администратор отвечает на них в панели управления.</p>
</div>
<?php if ($ok = flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
<?php if ($err = flash('error')): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>

<div class="page-section">
    <h5 class="mb-3">Новое обращение</h5>
    <form method="post" class="row g-3">
        <div class="col-md-6"><label class="form-label">Имя</label><input name="name" class="form-control" required value="<?= e($user['login']) ?>"></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= e($user['email']) ?>"></div>
        <div class="col-12"><label class="form-label">Сообщение</label><textarea name="message" class="form-control" rows="5" placeholder="Опишите ваш вопрос подробно" maxlength="1500" required></textarea><div class="form-text">Максимум 1500 символов.</div></div>
        <div class="col-md-6"><label class="form-label">Капча: <?= e(captcha_question()) ?></label><input name="captcha" class="form-control" required></div>
        <div class="col-12"><button class="btn btn-primary">Отправить</button></div>
    </form>
</div>

<div class="page-section mt-3">
    <h5 class="mb-3">Мои обращения</h5>
    <?php if (!$myMessages): ?>
        <div class="alert alert-light border mb-0">У вас пока нет обращений.</div>
    <?php else: ?>
        <?php foreach ($myMessages as $item): ?>
            <?php $isAnswered = !empty($item['admin_reply']); ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                        <div><strong>Обращение #<?= (int)$item['id'] ?></strong> <span class="text-muted ms-2"><?= e($item['created_at']) ?></span></div>
                        <span class="badge <?= $isAnswered ? 'text-bg-success' : 'text-bg-warning' ?>"><?= $isAnswered ? 'Есть ответ' : 'Ожидает ответа' ?></span>
                    </div>
                    <div class="p-3 bg-light rounded mb-2" style="white-space: pre-wrap"><?= e($item['message']) ?></div>
                    <div>
                        <strong>Ответ администратора:</strong>
                        <div class="mt-1">
                            <?php if ($isAnswered): ?>
                                <?= e($item['admin_reply']) ?>
                            <?php else: ?>
                                <span class="text-muted">Пока нет ответа</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
