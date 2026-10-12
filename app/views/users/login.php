<div class="row mt-3">
  <h2 class="col-md-8 offset-md-2">Log in</h2>
  <div class="col-md-8 offset-md-2">
    <form action="<?= e(url('/login')) ?>" method="post" novalidate class="needs-validation">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input name="username" id="username" type="text" class="form-control" value="<?= old('username') ?>" required autofocus>
        <div class="invalid-feedback">Please enter your username.</div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input name="password" id="password" type="password" class="form-control" required>
        <div class="invalid-feedback">Please enter your password.</div>
      </div>
      <button class="btn btn-success">Log in</button>
    </form>
  </div>
</div>
