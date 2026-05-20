<?php
$page        = 'home';
$title       = 'Misaki Handcrafted — Floral Studio';
$description = 'Handcrafted floral arrangements with quiet ritual and seasonal bloom.';
require __DIR__.'/includes/products.php';
require __DIR__.'/includes/settings.php';

// ── Announcements ─────────────────────────────────────────────────────────
$announcementSetting    = setting('platform_announcements', '');
$platformAnnouncements  = $announcementSetting ? json_decode($announcementSetting, true) : [];
$platformAnnouncements  = is_array($platformAnnouncements) ? $platformAnnouncements : [];

$platformAnnouncements = array_map(function($item) {
  $files = [];
  if (!empty($item['files']) && is_array($item['files'])) {
    foreach ($item['files'] as $entry) {
      $files[] = [
        'url'  => 'images/announcements/' . ($entry['file'] ?? ''),
        'type' => $entry['media_type'] ?? 'image',
      ];
    }
  } elseif (!empty($item['file'])) {
    $files[] = [
      'url'  => 'images/announcements/' . $item['file'],
      'type' => $item['media_type'] ?? 'image',
    ];
  }
  return [
    'id'         => $item['id'] ?? uniqid(),
    'title'      => $item['title'] ?? '',
    'body'       => $item['body'] ?? '',
    'category'   => $item['category'] ?? 'announcements',
    'files'      => $files,
    'created_at' => $item['added'] ?? date('Y-m-d H:i:s'),
  ];
}, $platformAnnouncements);

usort($platformAnnouncements, fn($a,$b) => strtotime($b['created_at']) - strtotime($a['created_at']));

require __DIR__.'/includes/header.php';

// ── New Arrivals ───────────────────────────────────────────────────────────
$newArrivals = $PRODUCTS;
usort($newArrivals, fn($a,$b) => strtotime($b['createdAt']) - strtotime($a['createdAt']));
$featured = array_slice($newArrivals, 0, 3);

// ── Today's Bouquet: most-recently created product ─────────────────────────
$todayBouquet = !empty($newArrivals) ? $newArrivals[0] : null;

$FEATURES = [
  ['icon'=>'❀','title'=>'Seasonal', 'body'=>'Sourced weekly from local growers, every bloom chosen at its peak.'],
  ['icon'=>'✿','title'=>'Hand-tied','body'=>'Every stem placed by our florists with intention and care.'],
  ['icon'=>'❁','title'=>'Delivered','body'=>'Same-day delivery in the city for orders placed before 1pm.'],
];

$dynReviews = fetch_recent_reviews(3);
$REVIEWS = $dynReviews ?: [
  ['full_name'=>'Aiko T.',   'body'=>'The most beautiful arrangement I\'ve ever received. Quiet and considered.','rating'=>5],
  ['full_name'=>'Marcus L.', 'body'=>'Absolutely stunning. Will order again and again.','rating'=>5],
  ['full_name'=>'Sora K.',   'body'=>'Felt like a piece of art arrived at my door.','rating'=>5],
];

// ── Trending Petals: pull from announcements (images only) ─────────────────
$trendingPetals = [];
foreach ($platformAnnouncements as $ann) {
  foreach ($ann['files'] as $f) {
    if ($f['type'] === 'image') {
      $trendingPetals[] = [
        'img'   => $f['url'],
        'annId' => $ann['id'],
        'title' => $ann['title'] ?: 'Announcement',
      ];
    }
  }
  if (count($trendingPetals) >= 6) break;
}
?>

<section class="hero hero-wheel">
  <div class="bg" id="heroBg"></div>
  <div class="scrim"></div>
  <div class="tint" id="heroTint"></div>
  <div class="hero-view container">
    <div class="wheel-wrap">
      <div class="wheel" id="heroWheel">
        <div class="ring"></div>
        <div class="pointer" id="heroPointer"></div>
      </div>
    </div>
    <div class="copy">
      <div class="brand-inline">
        <span class="dot" id="heroDot"></span>
        <span class="brand-text">Misaki / 美咲</span>
      </div>
      <p class="label" id="heroLabel">Collection · Cobalt</p>
      <h1>Paper that <em id="heroEm">blooms</em><br />in every season.</h1>
      <p class="desc">
        Misaki Handicraft folds, dyes, and shapes each petal by hand in a
        small Kyoto atelier. Spin the wheel — every hue is a different
        bouquet, made to order.
      </p>
      <div class="hero-actions">
        <button class="auto-btn" id="heroAutoBtn">Auto · On</button>
      </div>
      <div class="ctas">
        <a href="shop.php" class="btn btn-cream" id="orderBtn">Order Cobalt</a>
        <a href="about.php" class="btn btn-outline">Our Story</a>
      </div>
    </div>
  </div>
  <footer class="hero-meta">
    <span>Kyoto · est. 2014</span>
    <span id="heroMeta">225° · #2b5bff</span>
  </footer>
  <div class="hero-scroll">Scroll · 下へ</div>
</section>

<section class="container section">
  <div class="features-grid">
    <?php foreach($FEATURES as $i=>$f): ?>
      <div class="feature reveal" style="transition-delay:<?= $i*100 ?>ms">
        <div style="font-size:1.5rem;color:var(--sage-deep)"><?= $f['icon'] ?></div>
        <h3><?= $f['title'] ?></h3>
        <p><?= $f['body'] ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="container section-pad">
  <div class="row-head reveal">
    <div>
      <div class="eyebrow">新着商品</div>
      <h2>New Arrivals</h2>
    </div>
    <a class="view-all" href="shop.php">View all <span data-icon="arrow"></span></a>
  </div>
  <div class="product-grid">
    <?php foreach($featured as $i=>$p): ?>
      <div class="reveal" style="transition-delay:<?= $i*80 ?>ms">
        <?php renderProductCard($p); ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="reviews">
  <div class="container section">
    <div class="text-center reveal">
      <div class="eyebrow">お客様の声</div>
      <h2>Words from our garden</h2>
    </div>
    <div class="reviews-grid">
      <?php foreach($REVIEWS as $i=>$r): ?>
        <figure class="review reveal" style="transition-delay:<?= $i*80 ?>ms">
          <div class="stars"><?= str_repeat('★',(int)($r['rating']??5)) ?></div>
          <blockquote>"<?= htmlspecialchars($r['body']) ?>"</blockquote>
          <figcaption>— <?= htmlspecialchars($r['full_name']) ?></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Floralgram Feed ─────────────────────────────────────────────────── -->
<section class="container section floralgram-section">
  <div class="floralgram-layout">

    <!-- LEFT: Trending Petals -->
    <aside class="fg-sidebar glass reveal">
      <h3 class="fg-sidebar-title">Trending Petals</h3>
      <?php if (!empty($trendingPetals)): ?>
        <div class="trending-grid">
          <?php foreach($trendingPetals as $tp): ?>
            <a href="#ann-<?= htmlspecialchars($tp['annId']) ?>" class="trending-petal" title="<?= htmlspecialchars($tp['title']) ?>">
              <img src="<?= htmlspecialchars($tp['img']) ?>" alt="<?= htmlspecialchars($tp['title']) ?>" loading="lazy">
            </a>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="fg-empty">No trending petals yet.</p>
      <?php endif; ?>
    </aside>

    <!-- CENTER: Announcement Feed -->
    <section class="fg-feed">
      <?php if (empty($platformAnnouncements)): ?>
        <div class="fg-no-posts glass">
          <span style="font-size:2rem">❀</span>
          <p>No announcements yet. Check back soon.</p>
        </div>
      <?php else: ?>
        <?php foreach($platformAnnouncements as $ann): ?>
          <article class="fg-post glass reveal" id="ann-<?= htmlspecialchars($ann['id']) ?>">
            <header class="fg-post-head">
              <div class="fg-avatar">M</div>
              <div class="fg-post-meta">
                <strong>Misaki Handcrafted</strong>
                <small><?= htmlspecialchars(date('M j, Y', strtotime($ann['created_at']))) ?></small>
              </div>
              <?php if(!empty($ann['category'])): ?>
                <span class="fg-badge"><?= htmlspecialchars(ucfirst($ann['category'])) ?></span>
              <?php endif; ?>
            </header>

            <?php if(!empty($ann['files'])): ?>
              <div class="fg-media <?= count($ann['files']) > 1 ? 'fg-media-multi' : '' ?>">
                <?php foreach($ann['files'] as $fi): ?>
                  <?php if($fi['type'] === 'video'): ?>
                    <video controls preload="metadata" class="fg-media-item">
                      <source src="<?= htmlspecialchars($fi['url']) ?>">
                    </video>
                  <?php else: ?>
                    <img src="<?= htmlspecialchars($fi['url']) ?>"
                         alt="<?= htmlspecialchars($ann['title']) ?>"
                         loading="lazy" class="fg-media-item"
                         onerror="this.closest('.fg-media-item, img').style.display='none'">
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if(!empty($ann['title'])): ?>
              <h4 class="fg-post-title"><?= htmlspecialchars($ann['title']) ?></h4>
            <?php endif; ?>
            <?php if(!empty($ann['body'])): ?>
              <p class="fg-post-body"><?= nl2br(htmlspecialchars($ann['body'])) ?></p>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <!-- RIGHT: Today's Bouquet -->
    <aside class="fg-sidebar glass reveal">
      <h3 class="fg-sidebar-title">Today's Bouquet</h3>
      <?php if($todayBouquet): ?>
        <a href="product.php?slug=<?= urlencode($todayBouquet['slug']) ?>" class="daily-bouquet-card">
          <div class="daily-bouquet-img">
            <img src="<?= htmlspecialchars($todayBouquet['image']) ?>"
                 alt="<?= htmlspecialchars($todayBouquet['name']) ?>"
                 loading="lazy">
          </div>
          <div class="daily-bouquet-meta">
            <p class="daily-bouquet-name"><?= htmlspecialchars($todayBouquet['name']) ?></p>
            <p class="daily-bouquet-jp"><?= htmlspecialchars($todayBouquet['jp']) ?></p>
            <p class="daily-bouquet-price">₱<?= number_format($todayBouquet['price'],2) ?></p>
            <span class="daily-bouquet-cta">View →</span>
          </div>
        </a>
      <?php else: ?>
        <p class="fg-empty">No products yet.</p>
      <?php endif; ?>
    </aside>

  </div>
</section>

<?php require __DIR__.'/includes/footer.php'; ?>
