<?php
require __DIR__ . '/../bootstrap.php';
require_admin();

$roles = db()->query('SELECT * FROM roles ORDER BY name')->fetchAll();
$adminId = (int)(current_user()['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $login = trim((string)($_POST['login'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $birthDate = (string)($_POST['birth_date'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 0);
        $password = (string)($_POST['password'] ?? '');

        if ($login && $email && $phone && $birthDate && $roleId > 0 && $password !== '') {
            try {
                $stmt = db()->prepare('INSERT INTO users (login, email, phone, password_hash, birth_date, role_id, is_blocked) VALUES (?, ?, ?, ?, ?, ?, 0)');
                $stmt->execute([$login, $email, $phone, password_hash($password, PASSWORD_DEFAULT), $birthDate, $roleId]);
                set_flash('success', 'Пользователь создан.');
            } catch (PDOException $e) {
                set_flash('error', 'Не удалось создать пользователя (проверьте уникальность логина/email/телефона).');
            }
        } else {
            set_flash('error', 'Заполните все поля для создания пользователя.');
        }
    }

    if (isset($_POST['update'])) {
        $id = (int)($_POST['id'] ?? 0);
        $login = trim((string)($_POST['login'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $birthDate = (string)($_POST['birth_date'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 0);
        $newPassword = (string)($_POST['new_password'] ?? '');

        if ($id > 0 && $login && $email && $phone && $birthDate && $roleId > 0) {
            try {
                if ($newPassword !== '') {
                    $stmt = db()->prepare('UPDATE users SET login=?, email=?, phone=?, birth_date=?, role_id=?, password_hash=? WHERE id=?');
                    $stmt->execute([$login, $email, $phone, $birthDate, $roleId, password_hash($newPassword, PASSWORD_DEFAULT), $id]);
                } else {
                    $stmt = db()->prepare('UPDATE users SET login=?, email=?, phone=?, birth_date=?, role_id=? WHERE id=?');
                    $stmt->execute([$login, $email, $phone, $birthDate, $roleId, $id]);
                }
                set_flash('success', 'Данные пользователя обновлены.');
            } catch (PDOException $e) {
                set_flash('error', 'Не удалось обновить пользователя (проверьте уникальность логина/email/телефона).');
            }
        }
    }

    if (isset($_POST['toggle_block'])) {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $id !== $adminId) {
            db()->prepare('UPDATE users SET is_blocked = IF(is_blocked=1,0,1) WHERE id=?')->execute([$id]);
            set_flash('success', 'Статус блокировки обновлён.');
        }
    }

    if (isset($_POST['delete'])) {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $id !== $adminId) {
            db()->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
            set_flash('success', 'Пользователь удалён навсегда.');
        } else {
            set_flash('error', 'Нельзя удалить собственную учётную запись.');
        }
    }

    redirect_to('admin/users.php');
}

$users = db()->query('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.id DESC')->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<h3 class="mb-3">Управление пользователями</h3>
<?php if ($ok = flash('success')): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
<?php if ($err = flash('error')): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>

<div class="page-section mb-4">
    <h5 class="mb-3">Добавить пользователя</h5>
    <form method="post" class="row g-2">
        <div class="col-md-2"><input name="login" class="form-control" placeholder="Логин" required></div>
        <div class="col-md-2"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
        <div class="col-md-2"><input name="phone" class="form-control" placeholder="Телефон" required></div>
        <div class="col-md-2"><input type="date" name="birth_date" class="form-control" required></div>
        <div class="col-md-2"><select name="role_id" class="form-select"><?php foreach($roles as $role): ?><option value="<?= (int)$role['id'] ?>"><?= e($role['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Пароль" required></div>
        <div class="col-12"><button name="create" class="btn btn-success">Добавить</button></div>
    </form>
</div>

<?php foreach ($users as $u): ?>
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div><strong>#<?= (int)$u['id'] ?> <?= e($u['login']) ?></strong> <span class="text-muted">(<?= e($u['role_name']) ?>)</span></div>
            <span class="badge <?= (int)$u['is_blocked'] === 1 ? 'text-bg-danger' : 'text-bg-success' ?>"><?= (int)$u['is_blocked'] === 1 ? 'Заблокирован' : 'Активен' ?></span>
        </div>

        <form method="post" class="row g-2 align-items-end">
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <div class="col-md-2"><label class="form-label">Логин</label><input name="login" class="form-control" value="<?= e($u['login']) ?>" required></div>
            <div class="col-md-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($u['email']) ?>" required></div>
            <div class="col-md-2"><label class="form-label">Телефон</label><input name="phone" class="form-control" value="<?= e($u['phone']) ?>" required></div>
            <div class="col-md-2"><label class="form-label">Дата рождения</label><input type="date" name="birth_date" class="form-control" value="<?= e($u['birth_date']) ?>" required></div>
            <div class="col-md-2"><label class="form-label">Роль</label><select name="role_id" class="form-select"><?php foreach($roles as $role): ?><option value="<?= (int)$role['id'] ?>" <?= (int)$u['role_id']===(int)$role['id'] ? 'selected' : '' ?>><?= e($role['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label">Новый пароль</label><input type="password" name="new_password" class="form-control" placeholder="Не менять"></div>
            <div class="col-12 d-flex gap-2 flex-wrap">
                <button name="update" class="btn btn-primary">Сохранить изменения</button>
                <?php if ((int)$u['id'] !== $adminId): ?>
                    <button name="toggle_block" class="btn <?= (int)$u['is_blocked'] === 1 ? 'btn-success' : 'btn-warning' ?>"><?= (int)$u['is_blocked'] === 1 ? 'Разблокировать' : 'Заблокировать' ?></button>
                    <button name="delete" class="btn btn-danger" data-confirm="Удалить пользователя навсегда?">Удалить навсегда</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
