<div class="row mt-3">
  <h2 class="col-md-8 offset-md-2">Sign up on Wanderlust</h2>
  <div class="col-md-8 offset-md-2">
    <form action="<?= e(url('/signup')) ?>" method="post" novalidate class="needs-validation">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input name="username" id="username" type="text" class="form-control" value="<?= old('username') ?>" required minlength="3" maxlength="30" pattern="[A-Za-z0-9_.\-]+">
        <div class="invalid-feedback">3-30 characters: letters, numbers, . - _</div>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input name="email" id="email" type="email" class="form-control" value="<?= old('email') ?>" required>
        <div class="invalid-feedback">Please enter a valid email.</div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input name="password" id="password" type="password" class="form-control" required minlength="6">
        <div class="invalid-feedback">At least 6 characters.</div>
      </div>
      <button class="btn btn-success">Sign up</button>
    </form>
  </div>
</div>
