<?php
$page        = 'about';
$title       = 'About — Misaki Handcrafted';
$description = 'About Misaki, a handcrafted floral studio rooted in ikebana and wabi-sabi tradition.';
require __DIR__.'/includes/settings.php';
require __DIR__.'/includes/header.php';

$eyebrow = setting('about_eyebrow',  '店について');
$heading = setting('about_heading',  'About the studio');
$body    = setting('about_body',
  "Misaki was founded on the quiet belief that flowers are most beautiful when they are most themselves — a philosophy borrowed from ikebana and the wabi-sabi tradition.\n\nEvery arrangement is hand-tied in our small studio with seasonal blooms sourced weekly from local growers. We do not chase trends; we follow the bloom calendar."
);
$quote_jp = setting('brand_quote_jp', '花のように静かに');
?>

<div class="about-page">

  <!-- Hero Banner -->
  <div class="about-hero">
    <div class="about-hero-scrim"></div>
    <div class="about-hero-content container">
      <div class="eyebrow" style="color:rgba(255,255,255,.7)"><?= htmlspecialchars($eyebrow) ?></div>
      <h1 class="about-hero-title"><?= htmlspecialchars($heading) ?></h1>
    </div>
  </div>

  <!-- Philosophy Split -->
  <section class="about-split container reveal">
    <div class="about-split-text">
      <?php foreach(explode("\n\n", trim($body)) as $i => $para): ?>
        <p class="about-para <?= $i===0 ? 'about-para-lead' : '' ?>"><?= nl2br(htmlspecialchars($para)) ?></p>
      <?php endforeach; ?>
      <blockquote class="about-quote">
        <span class="font-jp"><?= htmlspecialchars($quote_jp) ?></span>
        <cite>— Quietly, like a flower</cite>
      </blockquote>
    </div>
    <div class="about-split-image">
      <div class="about-img-frame">
        <img src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=800&q=80"
             alt="Floral arrangement" loading="lazy">
        <div class="about-img-badge">
          <span class="font-jp">美咲</span>
          <small>est. 2014</small>
        </div>
      </div>
    </div>
  </section>

  <!-- Values Strip -->
  <section class="about-values">
    <div class="container about-values-grid">
      <div class="about-value reveal">
        <div class="about-value-icon">❀</div>
        <h3>Seasonal</h3>
        <p>Every bloom is sourced at its seasonal peak from trusted local growers.</p>
      </div>
      <div class="about-value reveal" style="transition-delay:100ms">
        <div class="about-value-icon">✿</div>
        <h3>Handcrafted</h3>
        <p>Each stem is placed with intention. No assembly lines, only quiet hands.</p>
      </div>
      <div class="about-value reveal" style="transition-delay:200ms">
        <div class="about-value-icon">❁</div>
        <h3>Wabi-Sabi</h3>
        <p>Beauty in imperfection. We celebrate the natural form of every petal.</p>
      </div>
      <div class="about-value reveal" style="transition-delay:300ms">
        <div class="about-value-icon">✦</div>
        <h3>Ikebana</h3>
        <p>Rooted in the Japanese art of flower arrangement — space is as important as bloom.</p>
      </div>
    </div>
  </section>

  <!-- Studio Story -->
  <section class="about-story container reveal">
    <div class="about-story-eyebrow eyebrow">Our Story</div>
    <h2 class="about-story-heading">A small studio, a quiet practice</h2>
    <div class="about-story-body">
      <p>What began as a personal ritual — arranging flowers each morning as a meditative practice — grew into something shared. Misaki Handcrafted was born in a small corner studio, where the rhythm of the seasons dictates everything: what we source, what we create, what we offer.</p>
      <p>We believe that a well-made bouquet is not decoration. It is a brief conversation between the maker and the receiver, conducted entirely in the language of bloom and stem.</p>
    </div>
  </section>

  <!-- CTA -->
  <section class="about-cta reveal">
    <div class="container about-cta-inner">
      <h2>Ready to bring bloom into your space?</h2>
      <p>Browse our seasonal collection, or visit us in the studio.</p>
      <div class="about-cta-btns">
        <a href="shop.php" class="btn-primary-pill">Shop blooms</a>
        <a href="gallery.php" class="btn-outline-pill">View gallery</a>
      </div>
    </div>
  </section>

</div>

<?php require __DIR__.'/includes/footer.php'; ?>
