<?php
// admin/products.php — loaded by admin/index.php
// Category management has been moved to admin/categories.php
require_once __DIR__.'/../includes/db.php';
$action = $_POST['action'] ?? '';
$msg    = '';
$err    = '';

/* ── Product CRUD only ── */
if ($action === 'create') {
  try {
    $imagePath = handleImageUpload('image_file', 'images/default-placeholder.jpg');
    $stock = max(0, (int)($_POST['stock'] ?? 0));
    $colorId = !empty($_POST['color_id']) ? (int)$_POST['color_id'] : null;
    db()->prepare('INSERT INTO product (slug,name,jp_name,type_id,color_id,price,image,badge,description,stock,is_visible) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
       ->execute([trim($_POST['slug']),trim($_POST['name']),trim($_POST['jp_name']),(int)$_POST['type_id'],$colorId,(float)$_POST['price'],$imagePath,trim($_POST['badge']??'')?:null,trim($_POST['description']),$stock,isset($_POST['is_visible'])?1:0]);
    $msg = 'Product created.';
  } catch (PDOException $e) {
    $err = ($e->getCode()==23000 && str_contains($e->getMessage(),'1062'))
      ? "Slug '".htmlspecialchars($_POST['slug'])."' is already in use."
      : "Database error: ".$e->getMessage();
  }
} elseif ($action === 'update') {
  try {
    $imagePath = handleImageUpload('image_file', $_POST['existing_image'] ?? '');
    $stock = max(0, (int)($_POST['stock'] ?? 0));
    $colorId = !empty($_POST['color_id']) ? (int)$_POST['color_id'] : null;
    db()->prepare('UPDATE product SET slug=?,name=?,jp_name=?,type_id=?,color_id=?,price=?,image=?,badge=?,description=?,stock=?,is_visible=? WHERE product_id=?')
       ->execute([trim($_POST['slug']),trim($_POST['name']),trim($_POST['jp_name']),(int)$_POST['type_id'],$colorId,(float)$_POST['price'],$imagePath,trim($_POST['badge']??'')?:null,trim($_POST['description']),$stock,isset($_POST['is_visible'])?1:0,(int)$_POST['product_id']]);
    $msg = 'Product updated.';
  } catch (PDOException $e) {
    $err = ($e->getCode()==23000 && str_contains($e->getMessage(),'1062'))
      ? "Slug '".htmlspecialchars($_POST['slug'])."' is already used by another product."
      : "Database error: ".$e->getMessage();
  }
} elseif ($action === 'update_stock') {
  $productId = (int)$_POST['product_id'];
  $stock     = max(0, (int)$_POST['stock']);
  db()->prepare('UPDATE product SET stock=? WHERE product_id=?')->execute([$stock, $productId]);
  $msg = 'Stock updated.';
} elseif ($action === 'delete') {
  try {
    db()->prepare('DELETE FROM product WHERE product_id=?')->execute([(int)$_POST['product_id']]);
    $msg = 'Product deleted.';
  } catch (PDOException $e) {
    $err = ($e->getCode()==23000 && str_contains($e->getMessage(),'1451'))
      ? "Cannot delete — product is part of existing orders. Hide it instead by unchecking 'Visible'."
      : "Database error: ".$e->getMessage();
  }
}

function handleImageUpload($fileInputName, $existingImagePath = '') {
  if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
    $ext     = strtolower(pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (in_array($ext, $allowed)) {
      $uploadDir = __DIR__.'/../images/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
      $filename = 'prod_'.time().'_'.uniqid().'.'.$ext;
      $target   = $uploadDir.$filename;
      if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $target))
        return 'images/'.$filename;
    }
  }
  return $existingImagePath;
}

$products = db()->query('SELECT p.*, t.name as type_name, c.collection_name as color_name, c.hex_code FROM product p JOIN product_type t ON p.type_id=t.type_id LEFT JOIN color_collection c ON c.color_id=p.color_id ORDER BY p.product_id DESC')->fetchAll();
$types    = db()->query('SELECT * FROM product_type ORDER BY type_id')->fetchAll();
$colors   = db()->query('SELECT color_id, collection_name, hex_code FROM color_collection WHERE is_active=1 ORDER BY sort_order')->fetchAll();
?>

<?php if ($err): ?><div class="adm-flash error">✗ <?= htmlspecialchars($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="adm-flash success">✓ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<!-- ═══════════════════════════════════════════════════════
     PRODUCTS
════════════════════════════════════════════════════════ -->
<div class="adm-section-head">
  <h2>Products</h2>
  <span class="count"><?= count($products) ?></span>
</div>

<!-- New product -->
<details class="adm-expand adm-card" style="margin-bottom:20px">
  <summary>+ Add new product</summary>
  <form method="post" class="adm-form" style="margin-top:20px" enctype="multipart/form-data">
    <input type="hidden" name="action" value="create">
    <label class="adm-label">Slug<input name="slug" required placeholder="e.g. lorem-blush"></label>
    <label class="adm-label">Name<input name="name" required placeholder="e.g. Lorem Blush"></label>
    <label class="adm-label">Japanese Name<input name="jp_name" placeholder="e.g. 桃の夢"></label>
    <label class="adm-label">Category
      <select name="type_id">
        <?php foreach($types as $t): ?>
          <option value="<?= $t['type_id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="adm-label">Color Collection <span style="font-size:.68rem;font-weight:400">(links product to a color wheel collection)</span>
      <select name="color_id">
        <option value="">— None —</option>
        <?php foreach($colors as $c): ?>
          <option value="<?= $c['color_id'] ?>" style="padding-left:4px">
            <?= htmlspecialchars($c['collection_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="adm-label">Price (₱)<input type="number" step="0.01" min="0" name="price" required></label>
    <label class="adm-label">Stock Qty<input type="number" min="0" name="stock" value="50" required></label>
    <label class="adm-label">Badge <span style="font-size:.68rem;font-weight:400">(e.g. Bestseller, New, Limited)</span><input name="badge" placeholder="optional"></label>
    <label class="adm-label">Image<input type="file" name="image_file" accept="image/*" required style="padding:7px"></label>
    <label class="adm-label span2">Description<textarea name="description" rows="3" required></textarea></label>
    <label class="adm-label checkbox-label"><input type="checkbox" name="is_visible" checked> Visible on store</label>
    <div><button class="adm-btn adm-btn-primary" type="submit">Create product</button></div>
  </form>
</details>

<!-- Table -->
<div class="adm-card">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>Image</th><th>Name</th><th>Type</th><th>Color</th><th>Price</th>
          <th>Stock</th><th>Visibility</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($products as $p):
        $lowStock = (int)$p['stock'] <= 10;
      ?>
        <tr>
          <td><img src="../<?= htmlspecialchars($p['image']) ?>" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:6px"></td>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($p['name']) ?></div>
            <div style="font-size:.7rem;color:var(--adm-muted)"><?= htmlspecialchars($p['slug']) ?></div>
          </td>
          <td><?= htmlspecialchars($p['type_name']) ?></td>
          <td>
            <?php if(!empty($p['color_name'])): ?>
              <span style="display:inline-flex;align-items:center;gap:5px;font-size:.8rem">
                <span style="width:10px;height:10px;border-radius:50%;background:<?= htmlspecialchars($p['hex_code'] ?? '#ccc') ?>;flex-shrink:0"></span>
                <?= htmlspecialchars($p['color_name']) ?>
              </span>
            <?php else: ?>
              <span style="color:var(--adm-muted);font-size:.78rem">—</span>
            <?php endif; ?>
          </td>
          <td style="font-weight:500">₱<?= number_format($p['price'],2) ?></td>
          <td>
            <!-- Inline stock quick-update -->
            <form method="post" style="display:flex;gap:6px;align-items:center">
              <input type="hidden" name="action" value="update_stock">
              <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
              <input type="number" name="stock" value="<?= (int)$p['stock'] ?>" min="0"
                     style="width:64px;padding:4px 6px;border:1px solid <?= $lowStock?'#ef4444':'var(--adm-border)' ?>;border-radius:var(--radius);font-size:.82rem;color:<?= $lowStock?'#ef4444':'inherit' ?>;font-weight:<?= $lowStock?'700':'400' ?>">
              <button class="adm-btn" type="submit" style="padding:4px 10px;font-size:.7rem">✓</button>
            </form>
            <?php if($lowStock && (int)$p['stock'] > 0): ?>
              <div style="font-size:.65rem;color:#ef4444;margin-top:2px">⚠ Low stock!</div>
            <?php elseif((int)$p['stock'] === 0): ?>
              <div style="font-size:.65rem;color:#b91c1c;margin-top:2px;font-weight:700">✗ Out of stock</div>
            <?php endif; ?>
          </td>
          <td><span class="pill <?= $p['is_visible']?'on':'off' ?>"><?= $p['is_visible']?'visible':'hidden' ?></span></td>
          <td>
            <details class="adm-expand" style="display:inline">
              <summary>Edit</summary>
              <div class="adm-card" style="margin-top:12px">
                <form method="post" class="adm-form" enctype="multipart/form-data">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                  <input type="hidden" name="existing_image" value="<?= htmlspecialchars($p['image']) ?>">
                  <label class="adm-label">Slug<input name="slug" value="<?= htmlspecialchars($p['slug']) ?>" required></label>
                  <label class="adm-label">Name<input name="name" value="<?= htmlspecialchars($p['name']) ?>" required></label>
                  <label class="adm-label">Japanese Name<input name="jp_name" value="<?= htmlspecialchars($p['jp_name']) ?>"></label>
                  <label class="adm-label">Category
                    <select name="type_id">
                      <?php foreach($types as $t): ?>
                        <option value="<?= $t['type_id'] ?>" <?= $t['type_id']==$p['type_id']?'selected':'' ?>><?= htmlspecialchars($t['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </label>
                  <label class="adm-label">Color Collection <span style="font-size:.68rem;font-weight:400">(color wheel linkage)</span>
                    <select name="color_id">
                      <option value="">— None —</option>
                      <?php foreach($colors as $c): ?>
                        <option value="<?= $c['color_id'] ?>" <?= $c['color_id']==$p['color_id']?'selected':'' ?>>
                          <?= htmlspecialchars($c['collection_name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </label>
                  <label class="adm-label">Price (₱)<input type="number" step="0.01" min="0" name="price" value="<?= $p['price'] ?>"></label>
                  <label class="adm-label">Stock Qty<input type="number" min="0" name="stock" value="<?= (int)$p['stock'] ?>" required></label>
                  <label class="adm-label">Badge<input name="badge" value="<?= htmlspecialchars($p['badge'] ?? '') ?>" placeholder="optional"></label>
                  <label class="adm-label">Change Image <span style="font-size:.68rem;font-weight:400;text-transform:none">(leave blank to keep current)</span>
                    <input type="file" name="image_file" accept="image/*" style="padding:7px">
                  </label>
                  <label class="adm-label span2">Description<textarea name="description" rows="3"><?= htmlspecialchars($p['description']) ?></textarea></label>
                  <label class="adm-label checkbox-label"><input type="checkbox" name="is_visible" <?= $p['is_visible']?'checked':'' ?>> Visible</label>
                  <div><button class="adm-btn adm-btn-primary" type="submit">Save changes</button></div>
                </form>
              </div>
            </details>
            <form method="post" style="display:inline;margin-left:8px" data-confirm='Delete this product?' data-danger>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
              <button class="adm-btn adm-btn-danger" type="submit">delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
