<?php
declare(strict_types=1);

function current_user(): ?array {
    static $user = false;
    if ($user === false) {
        $user = null;
        if (!empty($_SESSION['uid'])) {
            $st = db()->prepare('SELECT id, username, email FROM users WHERE id = ?');
            $st->execute([$_SESSION['uid']]);
            $user = $st->fetch() ?: null;
            if (!$user) unset($_SESSION['uid']);
        }
    }
    return $user;
}

function require_login(string $message = 'You must be logged in to do that.'): array {
    $u = current_user();
    if (!$u) {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            $_SESSION['redirect_after_login'] = ($_SERVER['REQUEST_URI'] ?? '/');
        }
        flash('error', $message);
        redirect('/login');
    }
    return $u;
}

function login_user(int $id): void {
    session_regenerate_id(true);
    $_SESSION['uid'] = $id;
}
