<?php
require __DIR__ . '/bootstrap.php';
$config = require __DIR__ . '/../config/config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $birthDate = $_POST['birth_date'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    if (!$login || !$email || !$phone || !$birthDate || !$password) $errors[] = 'Заполните все поля.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email.';
    if (!preg_match('/^\+?[0-9\-\s\(\)]{10,20}$/', $phone)) $errors[] = 'Некорректный телефон.';

    $age = (new DateTime($birthDate))->diff(new DateTime('today'))->y;
    if ($age < $config['app']['min_age']) $errors[] = 'Регистрация доступна с 14 лет.';
    if (!check_captcha($captcha)) $errors[] = 'Неверная капча.';

    if (!$errors) {
        $roleId = (int)db()->query("SELECT id FROM roles WHERE name='user'")->fetchColumn();
        $stmt = db()->prepare('INSERT INTO users (login, email, phone, password_hash, birth_date, role_id) VALUES (?, ?, ?, ?, ?, ?)');
        try {
            $stmt->execute([$login, $email, $phone, password_hash($password, PASSWORD_DEFAULT), $birthDate, $roleId]);
            set_flash('success', 'Регистрация успешна, войдите в аккаунт.');
            header('Location: /login.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Пользователь с такими данными уже существует.';
        }
    }
    refresh_captcha();
} else {
    refresh_captcha();
}

include __DIR__ . '/../includes/header.php';
?>
<h2>Регистрация</h2>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
    <div class="col-md-6"><label class="form-label">Логин</label><input name="login" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Телефон</label><input name="phone" class="form-control" placeholder="+7..." required></div>
    <div class="col-md-6"><label class="form-label">Дата рождения</label><input type="date" name="birth_date" max="<?= date('Y-m-d') ?>" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Пароль</label><input type="password" name="password" class="form-control" minlength="6" required></div>
    <div class="col-md-6"><label class="form-label">Капча: <?= e(captcha_question()) ?></label><input name="captcha" class="form-control" required></div>
    <div class="col-12"><button class="btn btn-primary">Зарегистрироваться</button></div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
