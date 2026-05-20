<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/products.php';
require_once __DIR__.'/settings.php';

if(!isset($page))        $page        = '';
if(!isset($title))       $title       = setting('meta_og_title', 'Misaki Handcrafted — Floral Studio');
if(!isset($description)) $description = setting('meta_description', 'Handcrafted floral arrangements with quiet ritual.');

$me = current_user();

$brand_name = setting('brand_name', 'MISAKI');
$brand_jp   = setting('brand_jp',   'handcrafted · 美咲');
$header_font_size   = setting('header_font_size',   '');
$header_font_color  = setting('header_font_color',  '');
$header_font_family = setting('header_font_family', '');
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Inter:wght@300;400;500&family=Shippori+Mincho:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <?= $extra_css ?? '' ?>
  <?php render_color_vars(); // inject dynamic CSS variables from DB ?>
</head>
<body data-page="<?= htmlspecialchars($page) ?>"<?= $me?' data-auth="1"':'' ?>>
<!-- Modal JS loaded early so confirm() patches work everywhere -->
<script src="js/modal.js"></script>
</head>
<body data-page="<?= htmlspecialchars($page) ?>"<?= $me?' data-auth="1"':'' ?>>

<?php if($me): ?>
<script>
(function() {
  const base = <?= json_encode((isset($page) && $page === 'legal') ? '../' : '') ?>;
  function fetchUserNotifs() {
    fetch(base + 'api/notifications.php?action=user_unread')
      .then(r => r.json())
      .then(d => {
        const badge = document.getElementById('user-notif-badge');
        if (!badge) return;
        if (d.count > 0) {
          badge.style.display = 'block';
          badge.textContent = d.count > 9 ? '9+' : d.count;
        } else {
          badge.style.display = 'none';
        }
        const list = document.getElementById('user-notif-list');
        if (d.notifications && d.notifications.length) {
          list.innerHTML = d.notifications.map(n => `
            <div style="padding:10px 16px;border-bottom:1px solid #f3ede6">
              <div>${n.message}</div>
              <div style="margin-top:3px;font-size:.65rem;color:#78716c">${n.created_at}</div>
              ${n.order_id ? `<a href="${base}account.php" style="font-size:.65rem;color:#3d5a3e">View my orders →</a>` : ''}
            </div>`).join('');
        } else {
          list.innerHTML = '<div style="padding:20px;text-align:center;color:#78716c">No new notifications</div>';
        }
      }).catch(() => {});
  }
  window.toggleUserNotif = function(e) {
    e.stopPropagation();
    const dd = document.getElementById('user-notif-dropdown');
    const open = dd.style.display === 'block';
    dd.style.display = open ? 'none' : 'block';
    if (!open) fetchUserNotifs();
  };
  window.markUserRead = function() {
    fetch(base + 'api/notifications.php?action=user_mark_read')
      .then(() => {
        document.getElementById('user-notif-badge').style.display = 'none';
        document.getElementById('user-notif-list').innerHTML =
          '<div style="padding:20px;text-align:center;color:#78716c">All caught up ✓</div>';
      });
  };
  document.addEventListener('click', e => {
    const wrap = document.getElementById('user-notif-wrap');
    if (wrap && !wrap.contains(e.target)) {
      const dd = document.getElementById('user-notif-dropdown');
      if (dd) dd.style.display = 'none';
    }
  });
  fetchUserNotifs();
  setInterval(fetchUserNotifs, 30000);
})();
</script>
<?php endif; ?>

<div class="page-loader"><div class="petal">美咲</div></div>

<header class="navbar">
  <div class="nav-inner">
    <a href="index.php" class="brand">
      <span class="brand-logo" style="<?= $header_font_size   ? 'font-size:'.htmlspecialchars($header_font_size).';'   : '' ?><?= $header_font_color  ? 'color:'.htmlspecialchars($header_font_color).';'       : '' ?><?= $header_font_family ? 'font-family:'.htmlspecialchars($header_font_family).';': '' ?>"><?= htmlspecialchars($brand_name) ?></span>
      <span class="brand-jp"><?= htmlspecialchars($brand_jp) ?></span>
    </a>
    <nav class="nav-links">
      <a href="index.php"   class="<?= $page==='home'?'active':'' ?>">Home</a>
      <a href="shop.php"    class="<?= $page==='shop'?'active':'' ?>">Shop</a>
      <a href="gallery.php" class="<?= $page==='gallery'?'active':'' ?>">Gallery</a>
      <a href="about.php"   class="<?= $page==='about'?'active':'' ?>">About</a>
    </nav>
    <div class="nav-actions">
      <button class="icon-btn open-faq" aria-label="FAQ" title="FAQ">
        <span data-icon="help"></span>
      </button>
      <?php if($me): ?>
        <!-- User Notification Bell -->
        <div style="position:relative" id="user-notif-wrap">
          <button id="user-notif-btn" class="icon-btn" aria-label="Notifications" onclick="toggleUserNotif(event)"
                  style="position:relative">
            <span data-icon="bell" style="font-size:1.1rem">🔔</span>
            <span id="user-notif-badge" style="display:none;position:absolute;top:2px;right:2px;background:#ef4444;color:#fff;border-radius:99px;font-size:.5rem;font-weight:700;padding:1px 4px;min-width:14px;text-align:center;line-height:1.4"></span>
          </button>
          <div id="user-notif-dropdown" style="display:none;position:absolute;right:0;top:44px;width:300px;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.12);z-index:9999;font-size:.82rem">
            <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
              <strong>Notifications</strong>
              <button onclick="markUserRead()" style="font-size:.7rem;color:var(--sage-deep);background:none;border:none;cursor:pointer">Mark read</button>
            </div>
            <div id="user-notif-list" style="max-height:260px;overflow-y:auto;padding:8px 0">
              <div style="padding:20px;text-align:center;color:var(--muted-fg)">No new notifications</div>
            </div>
          </div>
        </div>
        <a href="account.php" class="icon-btn" aria-label="Account">
          <span data-icon="user"></span>
        </a>
      <?php else: ?>
        <a href="login.php" class="icon-btn" aria-label="Sign in">
          <span data-icon="user"></span>
        </a>
      <?php endif; ?>
      <a href="cart.php" class="icon-btn cart-link" aria-label="Cart">
        <span data-icon="bag"></span>
        <span class="cart-badge" style="display:none">0</span>
      </a>
      <button class="icon-btn menu-btn" aria-label="Menu" aria-expanded="false">
        <span data-icon="menu"></span>
      </button>
    </div>
  </div>
  <div class="mobile-nav">
    <nav>
      <a href="index.php">Home</a>
      <a href="shop.php">Shop</a>
      <a href="gallery.php">Gallery</a>
      <a href="about.php">About</a>
      <a href="#" class="open-faq">FAQ</a>
      <?php if($me): ?>
        <a href="account.php">Account</a>
        <a href="logout.php">Sign out</a>
      <?php else: ?>
        <a href="login.php">Sign in</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>