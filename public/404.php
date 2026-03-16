<?php
require __DIR__ . '/bootstrap.php';
http_response_code(404);
include __DIR__ . '/../includes/header.php';
?>
<div class="page-section text-center py-5">
    <h1 class="mb-3">404</h1>
    <h4 class="mb-3">Страница не найдена</h4>
    <p class="text-muted mb-4">Похоже, такой страницы не существует или она была перемещена.</p>
    <a class="btn btn-primary" href="<?= e(url('index.php')) ?>">Вернуться на главную</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
