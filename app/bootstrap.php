<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
$GLOBALS['config'] = require APP_ROOT . '/config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', $GLOBALS['config']['debug'] ? '1' : '0');

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
      || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_set_cookie_params([
    'lifetime' => 7 * 24 * 60 * 60,   // 7 days, like the original app
    'path'     => '/',
    'secure'   => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Old form input survives exactly one redirect
$GLOBALS['old_input'] = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

require APP_ROOT . '/app/helpers.php';
require APP_ROOT . '/app/db.php';
require APP_ROOT . '/app/auth.php';
require APP_ROOT . '/app/controllers/listings.php';
require APP_ROOT . '/app/controllers/reviews.php';
require APP_ROOT . '/app/controllers/users.php';
