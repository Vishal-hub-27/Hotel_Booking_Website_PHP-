<?php
declare(strict_types=1);

// php -S localhost:8000 -t public public/index.php  -> let the built-in server serve real files itself
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file) && basename($file) !== 'index.php') return false;
}

require dirname(__DIR__) . '/app/bootstrap.php';

$routes = [
    ['GET',  '/',                               fn() => redirect('/listings')],
    ['GET',  '/listings',                       'listings_index'],
    ['POST', '/listings',                       'listings_create'],
    ['GET',  '/listings/new',                   'listings_new'],
    ['GET',  '/listings/(\d+)',                 'listings_show'],
    ['GET',  '/listings/(\d+)/edit',            'listings_edit'],
    ['POST', '/listings/(\d+)/update',          'listings_update'],
    ['POST', '/listings/(\d+)/delete',          'listings_delete'],
    ['POST', '/listings/(\d+)/reviews',         'reviews_create'],
    ['POST', '/listings/(\d+)/reviews/(\d+)/delete', 'reviews_delete'],
    ['GET',  '/signup',                         'users_signup_form'],
    ['POST', '/signup',                         'users_signup'],
    ['GET',  '/login',                          'users_login_form'],
    ['POST', '/login',                          'users_login'],
    ['POST', '/logout',                         'users_logout'],
    ['GET',  '/privacy',                        fn() => render('pages/privacy', ['title' => 'Privacy'])],
    ['GET',  '/terms',                          fn() => render('pages/terms', ['title' => 'Terms'])],
];

try {
    $method = $_SERVER['REQUEST_METHOD'] === 'HEAD' ? 'GET' : $_SERVER['REQUEST_METHOD'];
    $path = request_path();
    foreach ($routes as [$m, $pattern, $handler]) {
        if ($m === $method && preg_match('#^' . $pattern . '$#', $path, $matches)) {
            array_shift($matches);
            $handler(...array_map('intval', $matches));
            exit;
        }
    }
    abort(404, 'Page not found.');
} catch (HttpError $e) {
    render('error', ['title' => 'Error', 'message' => $e->getMessage(), 'code' => $e->getCode() ?: 500], $e->getCode() ?: 500);
} catch (Throwable $e) {
    error_log((string)$e);
    $detail = $GLOBALS['config']['debug'] ? $e->getMessage() : 'Something went wrong. Please try again.';
    render('error', ['title' => 'Error', 'message' => $detail, 'code' => 500], 500);
}
