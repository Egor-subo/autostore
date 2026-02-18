<?php
require __DIR__ . '/bootstrap.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    if (!check_captcha($captcha)) $errors[] = 'Неверная капча.';

    if (!$errors) {
        $stmt = db()->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE login = ? OR phone = ? LIMIT 1');
        $stmt->execute([$identity, $identity]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            redirect_to('index.php');
        }
        $errors[] = 'Неверные данные для входа.';
    }
    refresh_captcha();
} else {
    refresh_captcha();
}

include __DIR__ . '/../includes/header.php';
?>
<div class="auth-wrap">
  <div class="card auth-card">
    <div class="row g-0">
      <div class="col-md-5 auth-side d-flex flex-column justify-content-center">
        <span class="badge badge-soft mb-3">AutoStore Account</span>
        <h3>С возвращением!</h3>
        <p class="mb-0">Войдите, чтобы оформлять заказы, отслеживать статусы и оставлять отзывы о товарах.</p>
      </div>
      <div class="col-md-7 auth-form">
        <h2 class="mb-3">Вход в аккаунт</h2>
        <?php if ($ok = flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
        <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
        <form method="post" class="row g-3">
            <div class="col-12"><label class="form-label">Логин или телефон</label><input name="identity" class="form-control form-control-lg" required></div>
            <div class="col-12"><label class="form-label">Пароль</label><input type="password" name="password" class="form-control form-control-lg" required></div>
            <div class="col-12"><label class="form-label">Капча: <?= e(captcha_question()) ?></label><input name="captcha" class="form-control" required></div>
            <div class="col-12 d-grid"><button class="btn btn-primary btn-lg">Войти</button></div>
            <div class="col-12 text-muted">Нет аккаунта? <a href="<?= e(url('register.php')) ?>">Зарегистрироваться</a></div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
