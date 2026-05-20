<?php
$page        = 'shop';
$title       = 'Shop — Misaki Handcrafted';
$description = 'Browse handcrafted bouquets, ikebana arrangements, and dried botanicals.';
require __DIR__.'/includes/products.php';
require __DIR__.'/includes/settings.php';
require __DIR__.'/includes/header.php';

$shop_eyebrow    = setting('shop_eyebrow',    '店舗');
$shop_heading    = setting('shop_heading',    'Shop');
$shop_subtext    = setting('shop_subtext',    'Seasonal blooms, dried botanicals and ikebana arrangements — each made by hand.');
$shop_font_size  = setting('shop_font_size',  'clamp(2.5rem,5vw,3.75rem)');
$shop_font_color = setting('shop_font_color', '#1c1917');
$shop_font_family= setting('shop_font_family','Cormorant Garamond, serif');

$prices   = array_column($PRODUCTS, 'price');
$maxPrice = !empty($prices) ? (int)(ceil(max($prices) / 100) * 100) : 1000;
$minPrice = 0;

$activeColorSlug  = strtolower(trim($_GET['color'] ?? ''));
$activeColorLabel = '';
if ($activeColorSlug) {
  foreach($PRODUCTS as $p) {
    if (strtolower($p['color_name'] ?? '') === $activeColorSlug) {
      $activeColorLabel = $p['color_name'];
      break;
    }
  }
}

// Only show "All" and "Bouquet" — remove arrangement/dried/seasonal
$ALLOWED_TYPES = ['All', 'Bouquet'];
$FILTERED_TYPES = array_filter($TYPES, function($t) use ($ALLOWED_TYPES) {
  return in_array($t, $ALLOWED_TYPES, true);
});
$FILTERED_TYPES = array_values($FILTERED_TYPES);
// If only "All" remains after filter, show all product types
if (count($FILTERED_TYPES) <= 1) {
  $FILTERED_TYPES = $TYPES;
}
?>

<div class="page-pad" data-shop>
  <section class="container">

    <div class="shop-hero-text text-center reveal">
      <div class="eyebrow"><?= htmlspecialchars($shop_eyebrow) ?></div>
      <h1 style="font-size:<?= htmlspecialchars($shop_font_size) ?>;margin-top:8px;color:<?= htmlspecialchars($shop_font_color) ?>;font-family:<?= htmlspecialchars($shop_font_family) ?>">
        <?= htmlspecialchars($shop_heading) ?>
      </h1>
      <p class="shop-subtext"><?= htmlspecialchars($shop_subtext) ?></p>
    </div>

    <div class="shop-controls reveal">
      <label class="search-box">
        <span data-icon="eye"></span>
        <input id="search" type="text" placeholder="Search blooms…" aria-label="Search products">
      </label>
      <div class="sort-box">
        <select id="sort" aria-label="Sort by">
          <?php foreach($SORTS as $s): ?><option><?= $s ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="filter-box">
        <span id="priceLabel">All prices</span>
        <input id="price" type="range" min="<?= $minPrice ?>" max="<?= $maxPrice ?>" value="<?= $maxPrice ?>" aria-label="Max price">
      </div>
    </div>

    <div class="type-chips reveal">
      <?php foreach($FILTERED_TYPES as $i=>$t): ?>
        <button class="chip <?= $i===0?'active':'' ?>" data-type="<?= htmlspecialchars($t) ?>">
          <?= htmlspecialchars($t) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <?php if($activeColorLabel): ?>
    <div class="shop-color-banner reveal">
      <span>Showing <strong><?= htmlspecialchars($activeColorLabel) ?></strong> collection</span>
      <a href="shop.php">✕ Clear filter</a>
    </div>
    <?php endif; ?>

    <div class="product-grid" style="margin-top:48px">
      <?php foreach($PRODUCTS as $i=>$p): ?>
        <div class="reveal" style="transition-delay:<?= ($i%6)*60 ?>ms">
          <?php renderProductCard($p); ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="empty" style="display:none;text-align:center;padding:80px 0;color:var(--muted-fg)">
      No blooms match — try a different search or filter.
    </div>
  </section>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>
