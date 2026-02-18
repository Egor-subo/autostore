<?php
require __DIR__ . '/bootstrap.php';
require_auth();
$user = current_user();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $q = db()->prepare('UPDATE users SET email=?, phone=? WHERE id=?');
    $q->execute([$email, $phone, $user['id']]);
    set_flash('success', 'Профиль обновлен.');
    header('Location: /profile.php'); exit;
}
include __DIR__ . '/../includes/header.php';
?>
<h2>Профиль</h2>
<?php if ($ok = flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
<form method="post" class="row g-3">
<div class="col-md-6"><label class="form-label">Логин</label><input class="form-control" value="<?= e($user['login']) ?>" disabled></div>
<div class="col-md-6"><label class="form-label">Email</label><input name="email" class="form-control" value="<?= e($user['email']) ?>" required></div>
<div class="col-md-6"><label class="form-label">Телефон</label><input name="phone" class="form-control" value="<?= e($user['phone']) ?>" required></div>
<div class="col-12"><button class="btn btn-primary">Сохранить</button></div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
