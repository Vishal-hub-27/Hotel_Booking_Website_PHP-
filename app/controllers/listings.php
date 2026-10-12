<?php
declare(strict_types=1);

function find_listing(int $id): array {
    $st = db()->prepare('SELECT l.*, u.username AS owner_name FROM listings l JOIN users u ON u.id = l.user_id WHERE l.id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: abort(404, 'Listing not found.');
}

function listings_index(): void {
    $q = trim((string)($_GET['q'] ?? ''));
    if ($q !== '') {
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $st = db()->prepare('SELECT * FROM listings WHERE title LIKE ? OR location LIKE ? OR country LIKE ? ORDER BY id DESC');
        $st->execute([$like, $like, $like]);
    } else {
        $st = db()->query('SELECT * FROM listings ORDER BY id DESC');
    }
    render('listings/index', ['title' => 'Explore', 'listings' => $st->fetchAll(), 'q' => $q]);
}

function listings_new(): void {
    require_login('You must be logged in to add a property.');
    render('listings/form', ['title' => 'Add a property', 'listing' => null]);
}

/** Validates the text fields shared by create + update. */
function validate_listing_input(array $in): array {
    $d = [
        'title'       => trim((string)($in['title'] ?? '')),
        'description' => trim((string)($in['description'] ?? '')),
        'location'    => trim((string)($in['location'] ?? '')),
        'country'     => trim((string)($in['country'] ?? '')),
        'price'       => trim((string)($in['price'] ?? '')),
    ];
    $errors = [];
    if ($d['title'] === '' || mb_strlen($d['title']) > 150) $errors[] = 'Title is required (max 150 characters).';
    if ($d['description'] === '') $errors[] = 'Description is required.';
    if ($d['location'] === '' || mb_strlen($d['location']) > 120) $errors[] = 'Location is required (max 120 characters).';
    if ($d['country'] === '' || mb_strlen($d['country']) > 80) $errors[] = 'Country is required (max 80 characters).';
    if (!preg_match('/^\d{1,9}$/', $d['price'])) $errors[] = 'Price must be a whole number of 0 or more.';
    return [$d, $errors];
}

function listings_create(): void {
    $user = require_login('You must be logged in to add a property.');
    verify_csrf();
    [$d, $errors] = validate_listing_input($_POST);
    $image = store_uploaded_image('image', $errors);
    if ($image === null && !$errors) $errors[] = 'Please upload an image of the property.';
    if ($errors) {
        delete_local_image($image);
        foreach ($errors as $m) flash('error', $m);
        remember_input($_POST);
        redirect('/listings/new');
    }
    $st = db()->prepare('INSERT INTO listings (user_id, title, description, image_url, price, location, country) VALUES (?,?,?,?,?,?,?)');
    $st->execute([$user['id'], $d['title'], $d['description'], $image, (int)$d['price'], $d['location'], $d['country']]);
    flash('success', 'New listing created!');
    redirect('/listings/' . db()->lastInsertId());
}

function listings_show(int $id): void {
    $listing = find_listing($id);
    $st = db()->prepare('SELECT r.*, u.username FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.listing_id = ? ORDER BY r.created_at DESC, r.id DESC');
    $st->execute([$id]);
    render('listings/show', ['title' => $listing['title'], 'listing' => $listing, 'reviews' => $st->fetchAll()]);
}

function owned_listing_or_fail(int $id, array $user): array {
    $listing = find_listing($id);
    if ((int)$listing['user_id'] !== (int)$user['id']) {
        flash('error', 'You are not the owner of this listing.');
        redirect('/listings/' . $id);
    }
    return $listing;
}

function listings_edit(int $id): void {
    $user = require_login('You must be logged in to edit a property.');
    $listing = owned_listing_or_fail($id, $user);
    render('listings/form', ['title' => 'Edit property', 'listing' => $listing]);
}

function listings_update(int $id): void {
    $user = require_login('You must be logged in to edit a property.');
    verify_csrf();
    $listing = owned_listing_or_fail($id, $user);
    [$d, $errors] = validate_listing_input($_POST);
    $image = store_uploaded_image('image', $errors);   // optional on edit
    if ($errors) {
        delete_local_image($image);
        foreach ($errors as $m) flash('error', $m);
        remember_input($_POST);
        redirect("/listings/$id/edit");
    }
    $st = db()->prepare('UPDATE listings SET title=?, description=?, image_url=?, price=?, location=?, country=? WHERE id=?');
    $st->execute([$d['title'], $d['description'], $image ?? $listing['image_url'], (int)$d['price'], $d['location'], $d['country'], $id]);
    if ($image !== null) delete_local_image($listing['image_url']);
    flash('success', 'Listing updated.');
    redirect("/listings/$id");
}

function listings_delete(int $id): void {
    $user = require_login('You must be logged in to delete a property.');
    verify_csrf();
    $listing = owned_listing_or_fail($id, $user);
    db()->prepare('DELETE FROM listings WHERE id = ?')->execute([$id]);   // reviews are removed by ON DELETE CASCADE
    delete_local_image($listing['image_url']);
    flash('success', 'Listing deleted.');
    redirect('/listings');
}
