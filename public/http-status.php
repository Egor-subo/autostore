<?php
require __DIR__ . '/bootstrap.php';

$mode = $_GET['mode'] ?? '';
if ($mode === '301') {
    header('Location: ' . url('index.php'), true, 301);
    exit;
}
if ($mode === '302') {
    header('Location: ' . url('index.php'), true, 302);
    exit;
}
if ($mode === '404') {
    http_response_code(404);
}

include __DIR__ . '/../includes/header.php';
?>
<section class="page-section mb-3">
    <h2>Демо HTTP-статусов (301 / 302 / 404)</h2>
    <p class="text-muted mb-0">Страница добавлена для практики: можно проверить коды ответа сервера без поломки остального сайта.</p>
</section>

<div class="row g-3">
    <div class="col-md-4">
        <div class="page-section h-100">
            <h5>404 Not Found</h5>
            <p>Показывает страницу с кодом 404.</p>
            <a class="btn btn-danger" href="<?= e(url('http-status.php?mode=404')) ?>">Открыть 404</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="page-section h-100">
            <h5>301 Moved Permanently</h5>
            <p>Постоянный редирект на главную страницу.</p>
            <a class="btn btn-primary" href="<?= e(url('http-status.php?mode=301')) ?>">Проверить 301</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="page-section h-100">
            <h5>302 Found</h5>
            <p>Временный редирект на главную страницу.</p>
            <a class="btn btn-success" href="<?= e(url('http-status.php?mode=302')) ?>">Проверить 302</a>
        </div>
    </div>
</div>

<?php if ($mode === '404'): ?>
    <div class="alert alert-danger mt-3 mb-0">
        <strong>Ошибка 404:</strong> учебная демонстрация страницы «Не найдено». Остальные разделы сайта продолжают работать в обычном режиме.
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
