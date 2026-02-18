<?php
require __DIR__ . '/bootstrap.php';

$status = app_health_check();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install_schema'])) {
    $result = app_install_schema();
    $message = $result;
    $status = app_health_check();
}

include __DIR__ . '/../includes/header.php';
?>
<h2>Настройка AutoStore</h2>

<?php if ($status['ok']): ?>
    <div class="alert alert-success">База данных настроена корректно. Можно перейти на <a href="<?= e(url('index.php')) ?>">главную страницу</a>.</div>
<?php else: ?>
    <div class="alert alert-warning mb-3"><?= e($status['message']) ?></div>
<?php endif; ?>

<?php if ($message): ?>
    <div class="alert alert-<?= $message['ok'] ? 'success' : 'danger' ?>"><?= e($message['message']) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h5>Что делать</h5>
        <ol class="mb-3">
            <li>Проверьте параметры MySQL в <code>config/config.php</code>.</li>
            <li>Нажмите кнопку ниже для автоустановки схемы из <code>sql/schema.sql</code> или импортируйте файл вручную.</li>
        </ol>
        <form method="post">
            <button class="btn btn-primary" name="install_schema" <?= $status['ok'] ? 'disabled' : '' ?>>Установить/обновить схему БД</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
