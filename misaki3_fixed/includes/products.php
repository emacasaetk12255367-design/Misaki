<?php
date_default_timezone_set('Asia/Manila');
require_once __DIR__.'/db.php';

function fetch_products($visibleOnly=true){
  $sql = 'SELECT p.product_id AS id, p.slug, p.name, p.jp_name AS jp,
                 t.name AS type, p.price, p.image, p.badge, p.description,
                 p.sales, p.stock, p.created_at AS createdAt, p.is_visible,
                 p.color_id,
                 c.collection_name AS color_name, c.hex_code AS color_hex
          FROM product p
          JOIN product_type t ON t.type_id=p.type_id
          LEFT JOIN color_collection c ON c.color_id=p.color_id';
  if($visibleOnly) $sql .= ' WHERE p.is_visible=1';
  $sql .= ' ORDER BY p.product_id';
  $rows = db()->query($sql)->fetchAll();
  foreach($rows as &$r){
    $r['price']    = (float)$r['price'];
    $r['stock']    = (int)($r['stock'] ?? 0);
    $r['id']       = (string)$r['id'];
    $r['color_id'] = $r['color_id'] !== null ? (int)$r['color_id'] : null;
    // Badge is admin-controlled — no auto-override
  }
  return $rows;
}

function fetch_types(){
  $rows = db()->query('SELECT name FROM product_type ORDER BY type_id')->fetchAll();
  return array_merge(['All'], array_column($rows,'name'));
}

function find_product_by_slug($slug){
  foreach(fetch_products(false) as $p) if($p['slug']===$slug) return $p;
  return null;
}

function fetch_addons($activeOnly=true){
  $sql = 'SELECT addon_id AS id, name, price, is_active FROM addon';
  if($activeOnly) $sql .= ' WHERE is_active=1';
  $sql .= ' ORDER BY addon_id';
  $rows = db()->query($sql)->fetchAll();
  foreach($rows as &$r){ $r['price']=(float)$r['price']; $r['id']=(int)$r['id']; }
  return $rows;
}

function fetch_reviews_for_product($productId){
  $st = db()->prepare(
    'SELECT r.rating, r.body, r.created_at, u.full_name
       FROM review r JOIN user u ON u.user_id=r.user_id
      WHERE r.product_id=? ORDER BY r.created_at DESC');
  $st->execute([$productId]);
  return $st->fetchAll();
}

function fetch_recent_reviews($limit=3){
  $st = db()->prepare(
    'SELECT r.rating, r.body, r.created_at, u.full_name, p.name AS product_name
       FROM review r
       JOIN user u ON u.user_id=r.user_id
       JOIN product p ON p.product_id=r.product_id
       ORDER BY r.created_at DESC LIMIT '.(int)$limit);
  $st->execute();
  return $st->fetchAll();
}

/** Estimate production days: ceil(qty/5), min 1, max 14 */
function estimate_production_days($totalQty){
  return max(1, min(14, (int)ceil($totalQty / 5)));
}

/** Returns estimated completion date (Y-m-d), skipping Sundays */
function estimate_completion_date($orderDateStr, $totalQty){
  $days = estimate_production_days($totalQty);
  $ts   = strtotime($orderDateStr);
  $added = 0;
  while($added < $days){
    $ts += 86400;
    if(date('N', $ts) != 7) $added++;
  }
  return date('Y-m-d', $ts);
}

/* shared globals */
$PRODUCTS = fetch_products(true);
$TYPES    = fetch_types();
$ADDONS   = fetch_addons(true);
$SORTS    = ['Top sales','Latest','Price: Low to High','Price: High to Low'];

function findProductBySlug($slug){ return find_product_by_slug($slug); }

function renderProductCard($p){
  $json     = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
  $shareUrl = 'product.php?slug='.urlencode($p['slug']);
  $stock    = (int)($p['stock'] ?? 0);
  ?>
  <div class="product-card" data-product='<?= $json ?>'>
    <a href="product.php?slug=<?= urlencode($p['slug']) ?>" style="display:block">
      <div class="product-image">
        <img src="<?= htmlspecialchars($p['image']) ?>"
             alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" width="800" height="1024">
        <?php if(!empty($p['badge'])): ?>
          <span class="badge"><?= htmlspecialchars($p['badge']) ?></span>
        <?php endif; ?>
        <?php if($stock <= 0): ?>
          <span class="badge" style="background:#b91c1c;top:auto;bottom:10px">Out of stock</span>
        <?php elseif($stock <= 5): ?>
          <span class="badge" style="background:#d97706;top:auto;bottom:10px">Only <?= $stock ?> left</span>
        <?php endif; ?>
        <div class="product-actions">
          <button class="quick" data-quick='<?= $json ?>'>
            <span data-icon="eye"></span> Quick view
          </button>
          <?php if($stock > 0): ?>
          <button class="add" aria-label="Add to cart" data-add='<?= $json ?>'>
            <span data-icon="plus"></span>
          </button>
          <?php else: ?>
          <button class="add" aria-label="Out of stock" disabled style="opacity:.4;cursor:not-allowed">
            <span data-icon="plus"></span>
          </button>
          <?php endif; ?>
          <button class="share" aria-label="Share" data-share="<?= htmlspecialchars($shareUrl) ?>">
            <span data-icon="share"></span>
          </button>
        </div>
      </div>
    </a>
    <div class="product-meta">
      <div>
        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="product-jp"><?= htmlspecialchars($p['jp']) ?> · <?= htmlspecialchars($p['type']) ?></div>
        <div style="font-size:.72rem;color:<?= $stock<=5?'#b91c1c':'var(--muted-fg)' ?>;margin-top:3px">
          <?php if($stock<=0): ?>Out of stock<?php elseif($stock<=10): ?>⚠ <?= $stock ?> in stock<?php else: ?><?= $stock ?> in stock<?php endif; ?>
        </div>
      </div>
      <div class="product-price">₱<?= number_format($p['price'],2) ?></div>
    </div>
  </div>
  <?php
}
