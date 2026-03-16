<?php require __DIR__ . '/bootstrap.php'; include __DIR__ . '/../includes/header.php'; ?>
<section class="page-section mb-3">
    <h2>О компании AutoStore</h2>
    <p>AutoStore — современный онлайн-магазин автомобилей и комплектующих. Мы помогаем клиентам выбрать надежный автомобиль, подобрать оригинальные запчасти и удобно оформить покупку онлайн.</p>
</section>
<div class="row g-3">
    <div class="col-md-4"><div class="page-section h-100"><h5>Наша миссия</h5><p>Сделать покупку авто и запчастей простой, прозрачной и безопасной для каждого клиента.</p></div></div>
    <div class="col-md-4"><div class="page-section h-100"><h5>Почему выбирают нас</h5><ul><li>Проверенные поставщики</li><li>Удобная корзина и статусы заказов</li><li>Поддержка и обратная связь</li></ul></div></div>
    <div class="col-md-4"><div class="page-section h-100"><h5>Контакты</h5><p>Телефон: +7 (999) 123-45-67<br>Email: support@autostore.local<br>График: Пн–Сб 09:00–20:00</p></div></div>
</div>
<div class="page-section mt-3">
    <h5>Учебные HTTP-ошибки</h5>
    <p class="mb-2">Для практики можно открыть страницу демонстрации кодов 404, 301 и 302.</p>
    <a class="btn btn-outline-primary" href="<?= e(url('http-status.php')) ?>">Открыть демо HTTP-статусов</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
