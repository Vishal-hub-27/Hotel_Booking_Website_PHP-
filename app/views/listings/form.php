<?php
$editing = $listing !== null;
$action  = $editing ? '/listings/' . $listing['id'] . '/update' : '/listings';
$v = fn(string $k) => $editing ? old($k, $listing[$k]) : old($k);
?>
<br>
<h3 class="col-md-8 offset-md-2"><?= $editing ? 'Edit the information' : 'Add a new property' ?></h3>
<form method="post" action="<?= e(url($action)) ?>" novalidate class="needs-validation" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="row mt-3"><div class="col-md-8 offset-md-2">
    <div class="mb-3">
      <label for="title" class="form-label">Title</label>
      <input id="title" type="text" name="title" value="<?= $v('title') ?>" placeholder="Enter the title" class="form-control" required maxlength="150">
      <div class="invalid-feedback">Please enter a title.</div>
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea id="description" name="description" rows="4" placeholder="Enter the description" class="form-control" required><?= $v('description') ?></textarea>
      <div class="invalid-feedback">Please enter a description.</div>
    </div>
    <?php if ($editing): ?>
      <div class="mb-3">
        <p class="mb-1">Current image</p>
        <img src="<?= e(img_src($listing['image_url'])) ?>" alt="current image" style="max-height:150px;border-radius:.75rem">
      </div>
    <?php endif; ?>
    <div class="mb-3">
      <label for="image" class="form-label"><?= $editing ? 'Upload a new image (optional)' : 'Upload listing image' ?></label>
      <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp" class="form-control" <?= $editing ? '' : 'required' ?>>
      <div class="invalid-feedback">Please choose an image.</div>
    </div>
    <div class="mb-3">
      <label for="price" class="form-label">Price per night (&#x20b9;)</label>
      <input id="price" type="number" min="0" step="1" name="price" value="<?= $v('price') ?>" placeholder="Enter the price" class="form-control" required>
      <div class="invalid-feedback">Please enter a valid price.</div>
    </div>
    <div class="row">
      <div class="mb-3 col-md-4">
        <label for="location" class="form-label">Location</label>
        <input id="location" type="text" name="location" value="<?= $v('location') ?>" placeholder="Enter the location" class="form-control" required>
        <div class="invalid-feedback">Please enter a location.</div>
      </div>
      <div class="mb-3 col-md-8">
        <label for="country" class="form-label">Country</label>
        <input id="country" type="text" name="country" value="<?= $v('country') ?>" placeholder="Enter the country" class="form-control" required>
        <div class="invalid-feedback">Please enter a country.</div>
      </div>
    </div>
    <button class="btn btn-dark add-btn mt-3"><?= $editing ? 'Save changes' : 'Add' ?></button>
    <br><br>
  </div></div>
</form>
