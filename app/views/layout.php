<?php
$flashes = take_flashes();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> | Wanderlust</title>
    <link rel="stylesheet" href="<?= e(url('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('css/rating.css')) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md bg-body-light border-bottom sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= e(url('/listings')) ?>" aria-label="Wanderlust home"><i class="fa-solid fa-location-arrow"></i></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <div class="navbar-nav">
        <a class="nav-link" href="<?= e(url('/listings')) ?>">Explore</a>
      </div>
      <div class="navbar-nav ms-auto">
        <form class="d-flex" role="search" method="get" action="<?= e(url('/listings')) ?>">
          <input class="form-control me-2 search-inp" type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search destination" aria-label="Search">
          <button class="btn btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i>Search</button>
        </form>
      </div>
      <div class="navbar-nav ms-auto align-items-md-center">
        <a class="nav-link" href="<?= e(url('/listings/new')) ?>">List your property</a>
        <?php if (!$me): ?>
          <a class="nav-link" href="<?= e(url('/signup')) ?>"><b>Sign up</b></a>
          <a class="nav-link" href="<?= e(url('/login')) ?>"><b>Log in</b></a>
        <?php else: ?>
          <span class="nav-link text-muted"><i class="fa-regular fa-user"></i> <?= e($me['username']) ?></span>
          <form method="post" action="<?= e(url('/logout')) ?>" class="m-0"><?= csrf_field() ?>
            <button class="nav-link btn btn-link logout-btn"><b>Log out</b></button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<div class="container">
  <?php foreach (['success' => 'success', 'error' => 'danger'] as $type => $cls): ?>
    <?php foreach ($flashes[$type] ?? [] as $msg): ?>
      <div class="alert alert-<?= $cls ?> alert-dismissible fade show col-md-6 offset-md-3 mt-3" role="alert">
        <?= e($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endforeach; ?>
  <?php endforeach; ?>
  <?= $content ?>
</div>

<footer>
  <div class="f-info">
    <div class="f-info-socials">
      <i class="fa-brands fa-square-facebook"></i>
      <i class="fa-brands fa-square-instagram"></i>
      <i class="fa-brands fa-square-linkedin"></i>
    </div>
    <div class="f-info-brand">&copy; Wanderlust Private Limited</div>
    <div class="f-info-links">
      <a href="<?= e(url('/privacy')) ?>">Privacy</a>
      <a href="<?= e(url('/terms')) ?>">Terms</a>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="<?= e(url('js/script.js')) ?>"></script>
</body>
</html>
