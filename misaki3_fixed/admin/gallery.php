<?php
// admin/gallery.php — Gallery Collections & Slides CRUD
require_once __DIR__ . '/../includes/db.php';

$action = $_POST['action'] ?? '';
$msg = '';
$err = '';

/* ── Image upload helper ── */
function handleGalleryUpload($fileInput, $existing = '') {
    if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] === UPLOAD_ERR_OK) {
        $ext     = strtolower(pathinfo($_FILES[$fileInput]['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $allowed)) {
            $dir = __DIR__ . '/../images/gallery/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $fname = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $dir . $fname))
                return 'images/gallery/' . $fname;
        }
    }
    return $existing;
}

/* ─────────────────────────────────────────────────────────────
   COLLECTION CRUD
───────────────────────────────────────────────────────────── */
if ($action === 'create_collection') {
    try {
        $slug = trim($_POST['key_slug']);
        if (!preg_match('/^[a-z0-9\-]+$/', $slug))
            throw new Exception('Slug must be lowercase letters, numbers, and hyphens only.');
        db()->prepare(
            'INSERT INTO gallery_collection (key_slug, name, tag, description, sort_order, is_active)
             VALUES (?,?,?,?,?,?)'
        )->execute([
            $slug,
            trim($_POST['name']),
            trim($_POST['tag']),
            trim($_POST['description']),
            (int)($_POST['sort_order'] ?? 0),
            isset($_POST['is_active']) ? 1 : 0,
        ]);
        $msg = 'Collection created.';
    } catch (Exception $e) {
        $err = $e->getMessage();
    } catch (PDOException $e) {
        $err = str_contains($e->getMessage(), '1062')
            ? "Slug '" . htmlspecialchars($_POST['key_slug']) . "' already exists."
            : 'DB error: ' . $e->getMessage();
    }

} elseif ($action === 'update_collection') {
    try {
        db()->prepare(
            'UPDATE gallery_collection
                SET name=?, tag=?, description=?, sort_order=?, is_active=?
              WHERE gallery_id=?'
        )->execute([
            trim($_POST['name']),
            trim($_POST['tag']),
            trim($_POST['description']),
            (int)($_POST['sort_order'] ?? 0),
            isset($_POST['is_active']) ? 1 : 0,
            (int)$_POST['gallery_id'],
        ]);
        $msg = 'Collection updated.';
    } catch (PDOException $e) {
        $err = 'DB error: ' . $e->getMessage();
    }

} elseif ($action === 'delete_collection') {
    // Cascade deletes slides too (FK ON DELETE CASCADE)
    db()->prepare('DELETE FROM gallery_collection WHERE gallery_id=?')
        ->execute([(int)$_POST['gallery_id']]);
    $msg = 'Collection and all its slides deleted.';

/* ─────────────────────────────────────────────────────────────
   SLIDE CRUD
───────────────────────────────────────────────────────────── */
} elseif ($action === 'add_slide') {
    $imgPath = handleGalleryUpload('slide_image_file', trim($_POST['slide_image_url'] ?? ''));
    if (!$imgPath) { $err = 'Please upload an image or provide a URL.'; }
    else {
        db()->prepare(
            'INSERT INTO gallery_slide (gallery_id, image_path, caption, sort_order)
             VALUES (?,?,?,?)'
        )->execute([
            (int)$_POST['gallery_id'],
            $imgPath,
            trim($_POST['caption']),
            (int)($_POST['sort_order'] ?? 0),
        ]);
        $msg = 'Slide added.';
    }

} elseif ($action === 'update_slide') {
    $imgPath = handleGalleryUpload('slide_image_file', $_POST['existing_image'] ?? '');
    db()->prepare(
        'UPDATE gallery_slide SET image_path=?, caption=?, sort_order=? WHERE slide_id=?'
    )->execute([
        $imgPath,
        trim($_POST['caption']),
        (int)($_POST['sort_order'] ?? 0),
        (int)$_POST['slide_id'],
    ]);
    $msg = 'Slide updated.';

} elseif ($action === 'delete_slide') {
    db()->prepare('DELETE FROM gallery_slide WHERE slide_id=?')
        ->execute([(int)$_POST['slide_id']]);
    $msg = 'Slide deleted.';
}

/* ── Load data ── */
$collections = db()->query(
    'SELECT * FROM gallery_collection ORDER BY sort_order, gallery_id'
)->fetchAll();

$slides = [];
if ($collections) {
    $ids = implode(',', array_column($collections, 'gallery_id'));
    $allSlides = db()->query(
        "SELECT * FROM gallery_slide WHERE gallery_id IN ($ids) ORDER BY gallery_id, sort_order, slide_id"
    )->fetchAll();
    foreach ($allSlides as $s) $slides[$s['gallery_id']][] = $s;
}

/* ── Active collection for slide panel ── */
$activeGid  = (int)($_GET['gid'] ?? ($collections[0]['gallery_id'] ?? 0));
?>

<?php if ($err): ?><div class="adm-flash error">✗ <?= htmlspecialchars($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="adm-flash success">✓ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="adm-section-head">
  <h2>Gallery Collections</h2>
  <span class="count"><?= count($collections) ?></span>
</div>
<p style="font-size:.82rem;color:var(--adm-muted);margin-bottom:20px">
  Each collection appears as a menu item on the Gallery page. Add slides (images) to each collection for the slideshow.
</p>

<!-- Add new collection -->
<details class="adm-expand adm-card" style="margin-bottom:20px">
  <summary>+ Add gallery collection</summary>
  <form method="post" class="adm-form" style="margin-top:20px">
    <input type="hidden" name="action" value="create_collection">
    <label class="adm-label">Slug <span style="font-size:.68rem;font-weight:400">(URL-safe, e.g. eternal-roses)</span>
      <input name="key_slug" required placeholder="eternal-roses" pattern="[a-z0-9\-]+">
    </label>
    <label class="adm-label">Collection Name
      <input name="name" required placeholder="Eternal Roses">
    </label>
    <label class="adm-label">Tag <span style="font-size:.68rem;font-weight:400">(e.g. Preserved · Forever)</span>
      <input name="tag" placeholder="Preserved · Forever">
    </label>
    <label class="adm-label span2">Description
      <textarea name="description" rows="3" placeholder="A short poetic description shown in the slideshow…"></textarea>
    </label>
    <label class="adm-label">Sort Order
      <input type="number" name="sort_order" value="<?= count($collections) ?>" min="0" style="width:80px">
    </label>
    <label class="adm-label checkbox-label">
      <input type="checkbox" name="is_active" checked> Active (visible in gallery)
    </label>
    <div><button class="adm-btn adm-btn-primary" type="submit">Create collection</button></div>
  </form>
</details>

<!-- Collections list with inline slide management -->
<?php foreach ($collections as $col): ?>
  <?php $colSlides = $slides[$col['gallery_id']] ?? []; ?>
  <div class="adm-card" style="margin-bottom:20px">

    <!-- Collection header row -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div>
        <span style="font-weight:600;font-size:1rem"><?= htmlspecialchars($col['name']) ?></span>
        <span style="font-size:.75rem;color:var(--adm-muted);margin-left:10px"><?= htmlspecialchars($col['tag']) ?></span>
        <span class="pill <?= $col['is_active'] ? 'on' : 'off' ?>" style="margin-left:8px"><?= $col['is_active'] ? 'active' : 'hidden' ?></span>
        <span style="font-size:.7rem;color:var(--adm-muted);margin-left:8px"><?= count($colSlides) ?> slide(s)</span>
      </div>
      <div style="display:flex;gap:8px">
        <a href="?tab=gallery&gid=<?= $col['gallery_id'] ?>" class="adm-btn" style="font-size:.75rem">Manage Slides</a>
        <!-- Edit collection inline -->
        <details class="adm-expand" style="display:inline">
          <summary class="adm-btn" style="font-size:.75rem;cursor:pointer">Edit</summary>
          <div style="margin-top:12px">
            <form method="post" class="adm-form">
              <input type="hidden" name="action"     value="update_collection">
              <input type="hidden" name="gallery_id" value="<?= $col['gallery_id'] ?>">
              <label class="adm-label">Name
                <input name="name" value="<?= htmlspecialchars($col['name']) ?>" required>
              </label>
              <label class="adm-label">Tag
                <input name="tag" value="<?= htmlspecialchars($col['tag']) ?>">
              </label>
              <label class="adm-label span2">Description
                <textarea name="description" rows="3"><?= htmlspecialchars($col['description']) ?></textarea>
              </label>
              <label class="adm-label">Sort Order
                <input type="number" name="sort_order" value="<?= (int)$col['sort_order'] ?>" min="0" style="width:80px">
              </label>
              <label class="adm-label checkbox-label">
                <input type="checkbox" name="is_active" <?= $col['is_active'] ? 'checked' : '' ?>> Active
              </label>
              <div><button class="adm-btn adm-btn-primary" type="submit">Save</button></div>
            </form>
          </div>
        </details>
        <form method="post" style="display:inline"
              data-confirm='Delete collection &quot;<?= addslashes($col['name']) ?>&quot; and ALL its slides?' data-danger>
          <input type="hidden" name="action"     value="delete_collection">
          <input type="hidden" name="gallery_id" value="<?= $col['gallery_id'] ?>">
          <button class="adm-btn adm-btn-danger" type="submit" style="font-size:.75rem">delete</button>
        </form>
      </div>
    </div>

    <!-- Slide panel — only expanded for the active gid -->
    <?php if ($col['gallery_id'] === $activeGid): ?>
    <div style="margin-top:20px;border-top:1px solid var(--adm-border);padding-top:16px">
      <div style="font-size:.8rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--adm-muted);margin-bottom:12px">
        Slides for <?= htmlspecialchars($col['name']) ?>
      </div>

      <!-- Add slide -->
      <details class="adm-expand" style="margin-bottom:16px">
        <summary>+ Add slide</summary>
        <form method="post" class="adm-form" style="margin-top:12px" enctype="multipart/form-data">
          <input type="hidden" name="action"     value="add_slide">
          <input type="hidden" name="gallery_id" value="<?= $col['gallery_id'] ?>">
          <label class="adm-label">Upload Image
            <input type="file" name="slide_image_file" accept="image/*" style="padding:7px">
          </label>
          <label class="adm-label">— or — Image URL <span style="font-size:.68rem;font-weight:400">(if not uploading a file)</span>
            <input type="url" name="slide_image_url" placeholder="https://…">
          </label>
          <label class="adm-label">Caption
            <input name="caption" placeholder="A short poetic line…">
          </label>
          <label class="adm-label">Sort Order
            <input type="number" name="sort_order" value="<?= count($colSlides) ?>" min="0" style="width:80px">
          </label>
          <div><button class="adm-btn adm-btn-primary" type="submit">Add slide</button></div>
        </form>
      </details>

      <!-- Slides grid -->
      <?php if ($colSlides): ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px">
        <?php foreach ($colSlides as $sl): ?>
          <div style="border:1px solid var(--adm-border);border-radius:var(--radius);overflow:hidden">
            <div style="position:relative">
              <?php
                $imgSrc = $sl['image_path'];
                // Distinguish local paths vs external URLs
                $isUrl  = str_starts_with($imgSrc, 'http://') || str_starts_with($imgSrc, 'https://');
                $src    = $isUrl ? $imgSrc : '../' . $imgSrc;
              ?>
              <img src="<?= htmlspecialchars($src) ?>" alt=""
                   style="width:100%;height:110px;object-fit:cover;display:block">
            </div>
            <div style="padding:10px">
              <div style="font-size:.75rem;color:var(--adm-muted);margin-bottom:8px;font-style:italic">
                <?= htmlspecialchars($sl['caption'] ?: '—') ?>
              </div>
              <!-- Edit slide inline -->
              <details class="adm-expand" style="margin-bottom:6px">
                <summary style="font-size:.72rem;cursor:pointer">Edit</summary>
                <form method="post" class="adm-form" style="margin-top:8px" enctype="multipart/form-data">
                  <input type="hidden" name="action"         value="update_slide">
                  <input type="hidden" name="slide_id"       value="<?= $sl['slide_id'] ?>">
                  <input type="hidden" name="existing_image" value="<?= htmlspecialchars($sl['image_path']) ?>">
                  <label class="adm-label" style="font-size:.72rem">Replace Image
                    <input type="file" name="slide_image_file" accept="image/*" style="padding:4px">
                  </label>
                  <label class="adm-label" style="font-size:.72rem">Caption
                    <input name="caption" value="<?= htmlspecialchars($sl['caption']) ?>">
                  </label>
                  <label class="adm-label" style="font-size:.72rem">Order
                    <input type="number" name="sort_order" value="<?= (int)$sl['sort_order'] ?>" min="0" style="width:60px">
                  </label>
                  <div><button class="adm-btn adm-btn-primary" type="submit" style="font-size:.72rem">Save</button></div>
                </form>
              </details>
              <form method="post" data-confirm='Delete this slide?' data-danger>
                <input type="hidden" name="action"   value="delete_slide">
                <input type="hidden" name="slide_id" value="<?= $sl['slide_id'] ?>">
                <button class="adm-btn adm-btn-danger" type="submit" style="width:100%;font-size:.72rem">delete</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <p style="font-size:.82rem;color:var(--adm-muted)">No slides yet. Add one above.</p>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div>
<?php endforeach; ?>

<?php if (!$collections): ?>
  <div class="adm-card" style="text-align:center;padding:40px;color:var(--adm-muted)">
    No gallery collections yet. Create one above.
  </div>
<?php endif; ?>
