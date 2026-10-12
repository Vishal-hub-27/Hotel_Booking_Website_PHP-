<?php
declare(strict_types=1);

function reviews_create(int $listingId): void {
    $user = require_login('You must be logged in to leave a review.');
    verify_csrf();
    find_listing($listingId);
    $rating  = filter_var($_POST['rating'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
    $comment = trim((string)($_POST['comment'] ?? ''));
    $errors = [];
    if ($rating === false) $errors[] = 'Please choose a star rating from 1 to 5.';
    if ($comment === '' || mb_strlen($comment) > 1000) $errors[] = 'Please write a comment (max 1000 characters).';
    if ($errors) {
        foreach ($errors as $m) flash('error', $m);
        remember_input($_POST);
    } else {
        db()->prepare('INSERT INTO reviews (listing_id, user_id, rating, comment) VALUES (?,?,?,?)')
            ->execute([$listingId, $user['id'], $rating, $comment]);
        flash('success', 'Review added.');
    }
    redirect("/listings/$listingId");
}

function reviews_delete(int $listingId, int $reviewId): void {
    $user = require_login('You must be logged in to delete a review.');
    verify_csrf();
    $st = db()->prepare('SELECT user_id FROM reviews WHERE id = ? AND listing_id = ?');
    $st->execute([$reviewId, $listingId]);
    $review = $st->fetch() ?: abort(404, 'Review not found.');
    if ((int)$review['user_id'] !== (int)$user['id']) {
        flash('error', 'You can only delete your own reviews.');
    } else {
        db()->prepare('DELETE FROM reviews WHERE id = ?')->execute([$reviewId]);
        flash('success', 'Review deleted.');
    }
    redirect("/listings/$listingId");
}
