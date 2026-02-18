<?php
session_start();

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/captcha.php';
require_once __DIR__ . '/../includes/app.php';

$appHealth = app_health_check();
if (!$appHealth['ok']) {
    $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($currentScript !== 'setup.php') {
        redirect_to('setup.php');
        exit;
    }
}
