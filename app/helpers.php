<?php
declare(strict_types=1);

class HttpError extends RuntimeException {}

function e(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function base_path(): string {
    if (PHP_SAPI === 'cli-server') return '';
    return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
}
function url(string $path = '/'): string { return base_path() . '/' . ltrim($path, '/'); }

function request_path(): string {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = base_path();
    if ($base !== '' && str_starts_with($path, $base)) $path = substr($path, strlen($base));
    return '/' . trim($path, '/');
}

function redirect(string $path): never {
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}

/** Only allow redirects back into this site (prevents open-redirect). */
function safe_local_path(?string $p): ?string {
    if ($p === null || $p === '' || $p[0] !== '/' || str_starts_with($p, '//') || str_contains($p, '\\')) return null;
    return $p;
}

function abort(int $code, string $message): never { throw new HttpError($message, $code); }

/* ---------- flash messages & old input ---------- */
function flash(string $type, string $message): void { $_SESSION['flash'][$type][] = $message; }
function take_flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }
function remember_input(array $input): void { unset($input['password'], $input['_csrf']); $_SESSION['old'] = $input; }
function old(string $key, mixed $default = ''): string { return e($GLOBALS['old_input'][$key] ?? $default); }

/* ---------- CSRF ---------- */
function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">'; }
function verify_csrf(): void {
    $sent = $_POST['_csrf'] ?? '';
    $known = $_SESSION['csrf'] ?? '';
    if (!is_string($sent) || $known === '' || !hash_equals($known, $sent)) {
        abort(419, 'Your session expired. Please go back, refresh the page and try again.');
    }
}

/* ---------- views ---------- */
function render(string $view, array $data = [], int $status = 200): void {
    http_response_code($status);
    $title = $data['title'] ?? 'Wanderlust';
    try { $me = current_user(); } catch (Throwable $e) { $me = null; }   // available to every view + the layout
    extract($data, EXTR_SKIP);
    ob_start();
    require APP_ROOT . '/app/views/' . $view . '.php';
    $content = ob_get_clean();
    require APP_ROOT . '/app/views/layout.php';
}

/* ---------- formatting ---------- */
function money(int|float|string $n): string {      // Indian digit grouping: 1,50,000
    $s = (string)(int)round((float)$n);
    if (strlen($s) <= 3) return $s;
    return preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($s, 0, -3)) . ',' . substr($s, -3);
}

/* ---------- images ---------- */
function img_src(string $stored): string {
    return preg_match('#^https?://#i', $stored) ? $stored : url($stored);
}
function delete_local_image(?string $stored): void {
    if ($stored && str_starts_with($stored, 'uploads/')) {
        $file = APP_ROOT . '/public/uploads/' . basename($stored);
        if (is_file($file)) @unlink($file);
    }
}
/** Validates + stores an uploaded image. Returns 'uploads/xxxx.ext', or null if nothing (valid) was uploaded. */
function store_uploaded_image(string $field, array &$errors): ?string {
    $f = $_FILES[$field] ?? null;
    if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] !== UPLOAD_ERR_OK) { $errors[] = 'Image upload failed (error code ' . (int)$f['error'] . '). Try a smaller file.'; return null; }
    if ($f['size'] > $GLOBALS['config']['max_upload']) { $errors[] = 'Image is too large (max 5 MB).'; return null; }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset($allowed[$mime]) || @getimagesize($f['tmp_name']) === false) { $errors[] = 'Image must be a JPG, PNG or WEBP file.'; return null; }
    $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($f['tmp_name'], APP_ROOT . '/public/uploads/' . $name)) { $errors[] = 'Could not save the image on the server.'; return null; }
    return 'uploads/' . $name;
}
