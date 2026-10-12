<div id="filters">
  <div class="tag-toggle">
    <div class="form-check-reverse form-switch">
      <input class="form-check-input" type="checkbox" role="switch" id="taxSwitch">
      <label class="form-check-label" for="taxSwitch">Display total after taxes</label>
    </div>
  </div>
  <?php if ($q !== ''): ?>
    <span class="ms-4 mt-3">Results for <b><?= e($q) ?></b> &middot; <a href="<?= e(url('/listings')) ?>">clear</a></span>
  <?php endif; ?>
</div>

<?php if (!$listings): ?>
  <p class="mt-5 text-center text-muted">No properties found.</p>
<?php endif; ?>

<div class="row row-cols-lg-3 row-cols-md-3 row-cols-sm-1 mt-3">
<?php foreach ($listings as $l): ?>
  <a href="<?= e(url('/listings/' . $l['id'])) ?>" class="listing-link">
    <div class="card col listing-card">
      <img src="<?= e(img_src($l['image_url'])) ?>" class="card-img-top" alt="<?= e($l['title']) ?>" style="height:20rem" loading="lazy">
      <div class="card-img-overlay"></div>
      <div class="card-body">
        <p class="card-text">
          <b><?= e($l['title']) ?></b><br>
          &#x20b9;<?= e(money($l['price'])) ?>/night <i class="tax-info">&nbsp;&nbsp;+18% GST</i>
        </p>
      </div>
    </div>
  </a>
<?php endforeach; ?>
</div>

<script>
  document.getElementById('taxSwitch').addEventListener('change', function () {
    document.querySelectorAll('.tax-info').forEach(function (el) { el.style.display = this.checked ? 'inline' : 'none'; }, this);
  });
</script>
