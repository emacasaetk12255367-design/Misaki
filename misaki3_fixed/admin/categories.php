<?php
// admin/categories.php — full CRUD for product_type categories
require_once __DIR__.'/../includes/db.php';

$action = $_POST['action'] ?? '';
$msg    = '';
$err    = '';

if ($action === 'create') {
  $name = trim($_POST['name'] ?? '');
  if ($name) {
    try {
      db()->prepare('INSERT INTO product_type (name) VALUES (?)')->execute([$name]);
      $msg = "Category '$name' added.";
    } catch (PDOException $e) {
      $err = "Category already exists.";
    }
  } else { $err = 'Name is required.'; }
} elseif ($action === 'update') {
  $id   = (int)$_POST['type_id'];
  $name = trim($_POST['name'] ?? '');
  if ($name && $id) {
    try {
      db()->prepare('UPDATE product_type SET name=? WHERE type_id=?')->execute([$name, $id]);
      $msg = "Category updated.";
    } catch (PDOException $e) {
      $err = "Name already in use.";
    }
  }
} elseif ($action === 'delete') {
  $id = (int)$_POST['type_id'];
  try {
    db()->prepare('DELETE FROM product_type WHERE type_id=?')->execute([$id]);
    $msg = "Category deleted.";
  } catch (PDOException $e) {
    $err = "Cannot delete — products are assigned to this category. Re-assign them first in Products.";
  }
}

$types = db()->query(
  'SELECT t.*, COUNT(p.product_id) AS product_count
   FROM product_type t LEFT JOIN product p ON p.type_id=t.type_id
   GROUP BY t.type_id ORDER BY t.type_id'
)->fetchAll();
?>

<?php if ($err): ?><div class="adm-flash error">✗ <?= htmlspecialchars($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="adm-flash success">✓ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="adm-section-head">
  <h2>Categories</h2>
  <span class="count"><?= count($types) ?></span>
</div>
<p style="font-size:.82rem;color:var(--adm-muted);margin-bottom:20px">
  These are the category filters displayed on the Shop page. Full CRUD: create, rename, or delete categories here.
</p>

<!-- Add new -->
<details class="adm-expand adm-card" style="margin-bottom:20px">
  <summary>+ Add new category</summary>
  <form method="post" class="adm-form" style="margin-top:16px;max-width:360px">
    <input type="hidden" name="action" value="create">
    <label class="adm-label">Category name
      <input name="name" required placeholder="e.g. Seasonal">
    </label>
    <div><button class="adm-btn adm-btn-primary" type="submit">Create category</button></div>
  </form>
</details>

<!-- Table -->
<div class="adm-card">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr><th>ID</th><th>Name</th><th>Products</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach($types as $t): ?>
        <tr>
          <td style="color:var(--adm-muted);font-size:.75rem"><?= $t['type_id'] ?></td>
          <td style="font-weight:500"><?= htmlspecialchars($t['name']) ?></td>
          <td><span class="count" style="font-size:.72rem"><?= $t['product_count'] ?></span></td>
          <td>
            <details class="adm-expand" style="display:inline">
              <summary>Rename</summary>
              <div class="adm-card" style="margin-top:10px;max-width:300px">
                <form method="post" class="adm-form">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="type_id" value="<?= $t['type_id'] ?>">
                  <label class="adm-label">New name
                    <input name="name" value="<?= htmlspecialchars($t['name']) ?>" required>
                  </label>
                  <div><button class="adm-btn adm-btn-primary" type="submit">Save</button></div>
                </form>
              </div>
            </details>
            <form method="post" style="display:inline;margin-left:8px"
                  data-confirm='Delete category \'<?= htmlspecialchars(addslashes($t['name'])) ?>\'? Products must be re-assigned first.' data-danger>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="type_id" value="<?= $t['type_id'] ?>">
              <button class="adm-btn adm-btn-danger" type="submit"
                      <?= $t['product_count']>0?'title="Has products — re-assign first"':'' ?>>
                delete
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
