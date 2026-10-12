<?php $isOwner = $me && (int)$me['id'] === (int)$listing['user_id']; ?>
<div class="row mt-3">
  <div class="col-md-8 offset-md-2">
    <h3><b><?= e($listing['title']) ?></b></h3>
    <div class="card listing-card">
      <img src="<?= e(img_src($listing['image_url'])) ?>" class="card-img-top show-img" alt="<?= e($listing['title']) ?>">
      <div class="card-body mt-2">
        <p class="card-text">Owned by: <i><?= e($listing['owner_name']) ?></i></p>
        <p class="card-text"><?= nl2br(e($listing['description'])) ?></p>
        <p class="card-text">&#x20b9; <?= e(money($listing['price'])) ?> / night</p>
        <p class="card-text"><?= e($listing['location']) ?>, <?= e($listing['country']) ?></p>
      </div>
    </div>

    <?php if ($isOwner): ?>
      <div class="d-flex gap-3 mb-3">
        <a href="<?= e(url('/listings/' . $listing['id'] . '/edit')) ?>" class="btn btn-dark add-btn">Edit</a>
        <form method="post" action="<?= e(url('/listings/' . $listing['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this listing and all its reviews?');">
          <?= csrf_field() ?>
          <button class="btn btn-dark">Delete</button>
        </form>
      </div>
    <?php endif; ?>

    <hr id="reviews">
    <?php if ($me): ?>
      <h4>Leave a review</h4>
      <form action="<?= e(url('/listings/' . $listing['id'] . '/reviews')) ?>" method="post" novalidate class="needs-validation mb-4">
        <?= csrf_field() ?>
        <div class="mb-3 mt-3">
          <label class="form-label">Rating</label>
          <fieldset class="starability-slot">
            <input type="radio" id="no-rate" class="input-no-rate" name="rating" value="0" checked aria-label="No rating.">
            <?php foreach ([1 => 'Terrible', 2 => 'Not good', 3 => 'Average', 4 => 'Very good', 5 => 'Amazing'] as $n => $label): ?>
              <input type="radio" id="first-rate<?= $n ?>" name="rating" value="<?= $n ?>">
              <label for="first-rate<?= $n ?>" title="<?= $label ?>"><?= $n ?> star<?= $n > 1 ? 's' : '' ?></label>
            <?php endforeach; ?>
          </fieldset>
        </div>
        <div class="mb-3">
          <label for="comment" class="form-label">Comments</label>
          <textarea name="comment" id="comment" rows="5" class="form-control" required maxlength="1000"><?= old('comment') ?></textarea>
          <div class="invalid-feedback">Please add a comment for your review.</div>
        </div>
        <button class="btn btn-outline-dark">Submit</button>
      </form>
    <?php else: ?>
      <p><a href="<?= e(url('/login')) ?>">Log in</a> to leave a review.</p>
    <?php endif; ?>

    <hr>
    <p><b>All reviews</b></p>
    <?php if (!$reviews): ?><p class="text-muted">No reviews yet.</p><?php endif; ?>
    <div class="row">
      <?php foreach ($reviews as $r): ?>
        <div class="card col-md-5 ms-3 mb-3">
          <div class="card-body">
            <h5 class="card-title">@<?= e($r['username']) ?></h5>
            <p class="starability-result" data-rating="<?= (int)$r['rating'] ?>"></p>
            <p class="card-text"><?= nl2br(e($r['comment'])) ?></p>
          </div>
          <?php if ($me && (int)$me['id'] === (int)$r['user_id']): ?>
            <form class="mb-3 ms-3" method="post" action="<?= e(url('/listings/' . $listing['id'] . '/reviews/' . $r['id'] . '/delete')) ?>">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-dark">Delete</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
