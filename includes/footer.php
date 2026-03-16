</main>
<footer class="bg-light border-top py-4">
    <div class="container d-flex justify-content-between flex-wrap gap-3">
        <div>
            <strong>AutoStore</strong><br>
            Магазин автомобилей и комплектующих
        </div>
        <div>
            <div>Email: support@autostore.local</div>
            <div>Телефон: +7 (999) 123-45-67</div>
        </div>
        <div>
            <div class="small text-muted mb-1">Учебные HTTP-страницы:</div>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-sm btn-outline-danger" href="<?= e(url('404.php')) ?>">404</a>
                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('http-status.php?mode=301')) ?>">301</a>
                <a class="btn btn-sm btn-outline-success" href="<?= e(url('http-status.php?mode=302')) ?>">302</a>
            </div>
        </div>
        <div class="text-muted">© <?= date('Y') ?> Все права защищены</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(url('assets/app.js')) ?>"></script>
</body>
</html>
