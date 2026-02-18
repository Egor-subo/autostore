<?php

require_once __DIR__ . '/db.php';

function app_health_check(): array
{
    try {
        $pdo = db();
    } catch (PDOException $e) {
        return [
            'ok' => false,
            'message' => 'Нет подключения к БД. Проверьте config/config.php и доступ к MySQL.',
        ];
    }

    try {
        $required = ['roles', 'users', 'categories', 'products', 'orders', 'reviews'];
        $schema = $pdo->query('SELECT DATABASE()')->fetchColumn();
        if (!$schema) {
            return [
                'ok' => false,
                'message' => 'База данных не выбрана. Проверьте имя БД в config/config.php.',
            ];
        }

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?');
        foreach ($required as $table) {
            $stmt->execute([$schema, $table]);
            if ((int)$stmt->fetchColumn() === 0) {
                return [
                    'ok' => false,
                    'message' => "Не найдена таблица '{$table}'. Выполните sql/schema.sql или запустите /setup.php.",
                ];
            }
        }

        return ['ok' => true, 'message' => 'OK'];
    } catch (PDOException $e) {
        return [
            'ok' => false,
            'message' => 'Ошибка проверки структуры БД: ' . $e->getMessage(),
        ];
    }
}

function app_install_schema(): array
{
    $schemaFile = __DIR__ . '/../sql/schema.sql';
    if (!file_exists($schemaFile)) {
        return ['ok' => false, 'message' => 'Файл sql/schema.sql не найден.'];
    }

    try {
        $sql = file_get_contents($schemaFile);
        if ($sql === false) {
            return ['ok' => false, 'message' => 'Не удалось прочитать sql/schema.sql.'];
        }

        db()->exec($sql);
        return ['ok' => true, 'message' => 'Схема БД успешно применена.'];
    } catch (PDOException $e) {
        return ['ok' => false, 'message' => 'Ошибка применения схемы: ' . $e->getMessage()];
    }
}
