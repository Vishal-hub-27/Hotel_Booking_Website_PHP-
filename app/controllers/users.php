<?php
declare(strict_types=1);

function users_signup_form(): void {
    if (current_user()) redirect('/listings');
    render('users/signup', ['title' => 'Sign up']);
}

function users_signup(): void {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $email    = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $errors = [];
    if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) $errors[] = 'Username must be 3-30 characters: letters, numbers, dot, dash or underscore.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if (!$errors) {
        $st = db()->prepare('SELECT username, email FROM users WHERE username = ? OR email = ?');
        $st->execute([$username, $email]);
        foreach ($st->fetchAll() as $row) {
            if (strcasecmp($row['username'], $username) === 0) $errors[] = 'That username is already taken.';
            if (strcasecmp($row['email'], $email) === 0) $errors[] = 'That email is already registered.';
        }
    }
    if ($errors) {
        foreach (array_unique($errors) as $m) flash('error', $m);
        remember_input($_POST);
        redirect('/signup');
    }
    db()->prepare('INSERT INTO users (username, email, password_hash) VALUES (?,?,?)')
        ->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
    login_user((int)db()->lastInsertId());
    flash('success', 'Welcome to Wanderlust!');
    redirect('/listings');
}

function users_login_form(): void {
    if (current_user()) redirect('/listings');
    render('users/login', ['title' => 'Log in']);
}

function users_login(): void {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $st = db()->prepare('SELECT id, password_hash FROM users WHERE username = ?');
    $st->execute([$username]);
    $row = $st->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        flash('error', 'Invalid username or password.');
        remember_input(['username' => $username]);
        redirect('/login');
    }
    $next = safe_local_path($_SESSION['redirect_after_login'] ?? null);
    login_user((int)$row['id']);                       // regenerates the session id
    flash('success', 'Welcome back, ' . $username . '!');
    if ($next !== null) { header('Location: ' . $next); exit; }
    redirect('/listings');
}

function users_logout(): void {
    verify_csrf();
    $_SESSION = [];
    session_regenerate_id(true);
    flash('success', 'You are logged out.');
    redirect('/listings');
}
