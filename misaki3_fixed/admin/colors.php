<?php
// admin/colors.php — Color Wheel & Collection CRUD
require_once __DIR__ . '/../includes/db.php';

$action = $_POST['action'] ?? '';
$msg = '';
$err = '';

/* ── Image upload helper (reuse pattern from products.php) ── */
function handleColorBgUpload($fileInput, $existing = '') {
    if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] === UPLOAD_ERR_OK) {
        $ext     = strtolower(pathinfo($_FILES[$fileInput]['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $allowed)) {
            $dir = __DIR__ . '/../images/home-background/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $fname = 'homebg_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $dir . $fname))
                return 'images/home-background/' . $fname;
        }
    }
    return $existing;
}

/* ── CRUD handlers ── */
if ($action === 'create_color') {
    $bgPath = handleColorBgUpload('bg_image_file', '');
    db()->prepare(
        'INSERT INTO color_collection (collection_name, hex_code, hero_word, bg_image, sort_order, is_active)
         VALUES (?,?,?,?,?,?)'
    )->execute([
        trim($_POST['collection_name']),
        trim($_POST['hex_code']),
        trim($_POST['hero_word'] ?: 'blooms'),
        $bgPath ?: null,
        (int)($_POST['sort_order'] ?? 0),
        isset($_POST['is_active']) ? 1 : 0,
    ]);
    $msg = 'Color collection created.';

} elseif ($action === 'update_color') {
    $bgPath = handleColorBgUpload('bg_image_file', $_POST['existing_bg'] ?? '');
    db()->prepare(
        'UPDATE color_collection
            SET collection_name=?, hex_code=?, hero_word=?, bg_image=?, sort_order=?, is_active=?
          WHERE color_id=?'
    )->execute([
        trim($_POST['collection_name']),
        trim($_POST['hex_code']),
        trim($_POST['hero_word'] ?: 'blooms'),
        $bgPath ?: null,
        (int)($_POST['sort_order'] ?? 0),
        isset($_POST['is_active']) ? 1 : 0,
        (int)$_POST['color_id'],
    ]);
    $msg = 'Color collection updated.';

} elseif ($action === 'delete_color') {
    db()->prepare('DELETE FROM color_collection WHERE color_id=?')
        ->execute([(int)$_POST['color_id']]);
    $msg = 'Color collection deleted.';
}

$colors = db()->query(
    'SELECT * FROM color_collection ORDER BY sort_order, color_id'
)->fetchAll();
?>

<?php if ($err): ?><div class="adm-flash error">✗ <?= htmlspecialchars($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="adm-flash success">✓ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="adm-section-head">
  <h2>Color Wheel Collections</h2>
  <span class="count"><?= count($colors) ?></span>
</div>
<p style="font-size:.82rem;color:var(--adm-muted);margin-bottom:20px">
  Each active entry appears as a petal on the homepage color wheel. The wheel is evenly spaced — colors are ordered by <em>Sort Order</em>.
  The <em>Hero Word</em> replaces the italic word in the hero heading when that color is selected.
</p>

<!-- Add new color -->
<details class="adm-expand adm-card" style="margin-bottom:20px">
  <summary>+ Add color collection</summary>
  <form method="post" class="adm-form" style="margin-top:20px" enctype="multipart/form-data">
    <input type="hidden" name="action" value="create_color">
    <label class="adm-label">
      Collection Name
      <input name="collection_name" required placeholder="e.g. Sakura">
    </label>
    <label class="adm-label">
      Hex Color Code
      <div style="display:flex;gap:8px;align-items:center">
        <input type="color" name="_hex_picker" value="#ff3aa1" style="width:44px;height:36px;padding:2px;border:1px solid var(--adm-border);border-radius:var(--radius);cursor:pointer">
        <input type="text"  name="hex_code" value="#ff3aa1" placeholder="#rrggbb" maxlength="7" style="width:100px"
               oninput="this.previousElementSibling.value=this.value">
      </div>
    </label>
    <label class="adm-label">
      Hero Word <span style="font-size:.68rem;font-weight:400">(the italic word in "Paper that <em>blooms</em>")</span>
      <input name="hero_word" value="blooms" placeholder="blooms">
    </label>
    <label class="adm-label">
      Background Image <span style="font-size:.68rem;font-weight:400">(optional — shown in hero when this color is active)</span>
      <input type="file" name="bg_image_file" accept="image/*" style="padding:7px">
    </label>
    <label class="adm-label">
      Sort Order <span style="font-size:.68rem;font-weight:400">(lower = earlier on wheel)</span>
      <input type="number" name="sort_order" value="0" min="0" style="width:80px">
    </label>
    <label class="adm-label checkbox-label">
      <input type="checkbox" name="is_active" checked> Active (show on wheel)
    </label>
    <div><button class="adm-btn adm-btn-primary" type="submit">Add collection</button></div>
  </form>
</details>

<!-- Color table -->
<div class="adm-card">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>Swatch</th>
          <th>Name</th>
          <th>Hex</th>
          <th>Hero Word</th>
          <th>BG Image</th>
          <th>Order</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($colors as $c): ?>
        <tr>
          <td>
            <div style="width:36px;height:36px;border-radius:50%;background:<?= htmlspecialchars($c['hex_code']) ?>;
                        box-shadow:0 0 10px <?= htmlspecialchars($c['hex_code']) ?>88;border:2px solid rgba(255,255,255,.15)"></div>
          </td>
          <td style="font-weight:500"><?= htmlspecialchars($c['collection_name']) ?></td>
          <td style="font-family:monospace"><?= htmlspecialchars($c['hex_code']) ?></td>
          <td style="font-style:italic"><?= htmlspecialchars($c['hero_word']) ?></td>
          <td>
            <?php if ($c['bg_image']): ?>
              <img src="../<?= htmlspecialchars($c['bg_image']) ?>" alt=""
                   style="width:48px;height:32px;object-fit:cover;border-radius:4px">
            <?php else: ?>
              <span style="color:var(--adm-muted);font-size:.75rem">none</span>
            <?php endif; ?>
          </td>
          <td><?= (int)$c['sort_order'] ?></td>
          <td><span class="pill <?= $c['is_active'] ? 'on' : 'off' ?>"><?= $c['is_active'] ? 'active' : 'hidden' ?></span></td>
          <td>
            <!-- Edit -->
            <details class="adm-expand" style="display:inline">
              <summary>Edit</summary>
              <div class="adm-card" style="margin-top:12px">
                <form method="post" class="adm-form" enctype="multipart/form-data">
                  <input type="hidden" name="action"      value="update_color">
                  <input type="hidden" name="color_id"    value="<?= $c['color_id'] ?>">
                  <input type="hidden" name="existing_bg" value="<?= htmlspecialchars($c['bg_image'] ?? '') ?>">
                  <label class="adm-label">Name
                    <input name="collection_name" value="<?= htmlspecialchars($c['collection_name']) ?>" required>
                  </label>
                  <label class="adm-label">Hex Color
                    <div style="display:flex;gap:8px;align-items:center">
                      <input type="color" name="_hex_picker" value="<?= htmlspecialchars($c['hex_code']) ?>"
                             style="width:44px;height:36px;padding:2px;border:1px solid var(--adm-border);border-radius:var(--radius);cursor:pointer">
                      <input type="text"  name="hex_code" value="<?= htmlspecialchars($c['hex_code']) ?>" maxlength="7" style="width:100px"
                             oninput="this.previousElementSibling.value=this.value">
                    </div>
                  </label>
                  <label class="adm-label">Hero Word
                    <input name="hero_word" value="<?= htmlspecialchars($c['hero_word']) ?>">
                  </label>
                  <label class="adm-label">Replace BG Image
                    <input type="file" name="bg_image_file" accept="image/*" style="padding:7px">
                  </label>
                  <label class="adm-label">Sort Order
                    <input type="number" name="sort_order" value="<?= (int)$c['sort_order'] ?>" min="0" style="width:80px">
                  </label>
                  <label class="adm-label checkbox-label">
                    <input type="checkbox" name="is_active" <?= $c['is_active'] ? 'checked' : '' ?>> Active
                  </label>
                  <div><button class="adm-btn adm-btn-primary" type="submit">Save</button></div>
                </form>
              </div>
            </details>
            <!-- Delete -->
            <form method="post" style="display:inline;margin-left:8px"
                  data-confirm='Delete this color collection?' data-danger>
              <input type="hidden" name="action"   value="delete_color">
              <input type="hidden" name="color_id" value="<?= $c['color_id'] ?>">
              <button class="adm-btn adm-btn-danger" type="submit">delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$colors): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--adm-muted);padding:32px">No colors yet.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
/* Keep color picker and text input in sync */
document.querySelectorAll('input[type="color"]').forEach(picker => {
  picker.addEventListener('input', function () {
    const txt = this.nextElementSibling;
    if (txt && txt.type === 'text') txt.value = this.value;
  });
});
</script>
