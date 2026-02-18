<?php
require __DIR__ . '/bootstrap.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $q = db()->prepare('INSERT INTO feedback_messages (user_id, name, email, message) VALUES (?, ?, ?, ?)');
        $q->execute([current_user()['id'] ?? null, $name, $email, $message]);
        set_flash('success', 'Спасибо! Ваше сообщение отправлено.');
    }
    redirect_to('feedback.php');
}
include __DIR__ . '/../includes/header.php';
?>
<div class="page-section mb-3"><h2>Обратная связь</h2><p class="text-muted mb-0">Напишите нам ваш вопрос, предложение или отзыв о сервисе.</p></div>
<?php if ($ok=flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
<div class="page-section"><form method="post" class="row g-3">
<div class="col-md-6"><label class="form-label">Имя</label><input name="name" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
<div class="col-12"><label class="form-label">Сообщение</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
<div class="col-12"><button class="btn btn-primary">Отправить</button></div>
</form></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
