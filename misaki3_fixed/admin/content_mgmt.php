<?php
// admin/content_mgmt.php — Content Management (replaces old Settings)
// Edit all text content for Home, Shop, Gallery, About, Header, Footer
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/settings.php';

$msg = '';
$err = '';

/* ── Save handler ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_content') {
  try {
    $pdo  = db();
    $upsert = $pdo->prepare(
      "INSERT INTO site_settings (`key`,`value`,`label`,`group`,`type`,`sort_order`)
       VALUES (?,?,?,?,?,?)
       ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)"
    );
    foreach ($_POST['content'] as $key => $value) {
      // Fetch existing meta so we only update value
      $meta = $pdo->prepare('SELECT `label`,`group`,`type`,`sort_order` FROM site_settings WHERE `key`=?');
      $meta->execute([$key]);
      $row = $meta->fetch();
      if ($row) {
        $upsert->execute([$key, $value, $row['label'], $row['group'], $row['type'], $row['sort_order']]);
      } else {
        // New key (e.g. custom style overrides)
        $upsert->execute([$key, $value, ucwords(str_replace('_',' ',$key)), 'content', 'textarea', 99]);
      }
    }
    header('Location: ?tab=content_mgmt&saved=1');
    exit;
  } catch (Throwable $e) {
    $err = 'Save failed: ' . $e->getMessage();
  }
}

if (isset($_GET['saved'])) $msg = 'Content saved successfully.';

// ── Load all settings ──────────────────────────────────────
$pdo  = db();
$rows = $pdo->query('SELECT * FROM site_settings ORDER BY `group`, sort_order')->fetchAll();
$allSettings = [];
foreach ($rows as $r) $allSettings[$r['key']] = $r['value'];

// Helper
function cs($key, $default = '') {
  global $allSettings;
  return $allSettings[$key] ?? $default;
}

$activeSection = $_GET['section'] ?? 'home';
$sections = [
  'home'    => 'Home Page',
  'header'  => 'Header',
  'footer'  => 'Footer',
  'shop'    => 'Shop Page',
  'gallery' => 'Gallery Page',
  'about'   => 'About Page',
  'contact' => 'Contact & Social',
  'seo'     => 'SEO / Meta',
];
?>

<?php if ($err): ?><div class="adm-flash error">✗ <?= htmlspecialchars($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="adm-flash success">✓ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<p style="font-size:.875rem;color:var(--adm-muted);max-width:620px;margin-bottom:24px">
  Edit all text content displayed on the public storefront. Use the rich text controls to customize font size, color, and family.
  Changes take effect immediately after saving.
</p>

<!-- Section tabs -->
<div class="adm-stabs" style="margin-bottom:0">
  <?php foreach ($sections as $sk => $sl): ?>
    <button class="adm-stab <?= $activeSection === $sk ? 'active' : '' ?>"
            onclick="switchSection('<?= $sk ?>')" data-section="<?= $sk ?>">
      <?= htmlspecialchars($sl) ?>
    </button>
  <?php endforeach; ?>
</div>

<form method="post" id="content-form">
  <input type="hidden" name="action" value="save_content">

  <!-- ══ HOME ══ -->
  <div class="adm-card content-section" id="sec-home" style="margin-top:20px;<?= $activeSection!=='home'?'display:none':'' ?>">
    <h3 class="sec-title">Home Page</h3>
    <div class="adm-form">
      <?php renderField('hero_eyebrow',   'Hero Eyebrow (Top label)', 'text') ?>
      <?php renderField('hero_heading',   'Hero Heading (HTML allowed)', 'textarea', true) ?>
      <?php renderField('hero_subtext',   'Hero Sub-text', 'textarea') ?>
      <?php renderField('hero_cta_primary',   'Primary CTA Button Text', 'text') ?>
      <?php renderField('hero_cta_secondary', 'Secondary CTA Button Text', 'text') ?>
    </div>
    <!-- Style controls for hero -->
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--adm-border)">
      <div style="font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);margin-bottom:14px">Hero Text Style Overrides</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
        <?php renderStyleField('hero_font_size',   'Font Size', '2.8rem', 'e.g. 2.8rem or 48px') ?>
        <?php renderStyleField('hero_font_color',  'Font Color', '#f7f2ea', '', 'color') ?>
        <?php renderStyleField('hero_font_family', 'Font Family', 'Cormorant Garamond, serif', 'e.g. Inter, sans-serif') ?>
      </div>
    </div>
  </div>

  <!-- ══ HEADER ══ -->
  <div class="adm-card content-section" id="sec-header" style="margin-top:20px;<?= $activeSection!=='header'?'display:none':'' ?>">
    <h3 class="sec-title">Header</h3>
    <div class="adm-form">
      <?php renderField('brand_name', 'Brand / Logo Name', 'text') ?>
      <?php renderField('brand_jp',   'Brand Japanese Subtitle', 'text') ?>
    </div>
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--adm-border)">
      <div style="font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);margin-bottom:14px">Header Text Style</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
        <?php renderStyleField('header_font_size',   'Brand Font Size', '1.15rem') ?>
        <?php renderStyleField('header_font_color',  'Brand Color', '#1c1917', '', 'color') ?>
        <?php renderStyleField('header_font_family', 'Brand Font Family', 'Cormorant Garamond, serif') ?>
      </div>
    </div>
  </div>

  <!-- ══ FOOTER ══ -->
  <div class="adm-card content-section" id="sec-footer" style="margin-top:20px;<?= $activeSection!=='footer'?'display:none':'' ?>">
    <h3 class="sec-title">Footer</h3>
    <div class="adm-form">
      <?php renderField('brand_tagline',    'Footer Tagline / Description', 'textarea') ?>
      <?php renderField('brand_quote_jp',   'Footer Japanese Quote (bottom bar)', 'text') ?>
      <?php renderField('footer_link_1_text','Legal Link 1 Text (e.g. Privacy Policy)', 'text') ?>
      <?php renderField('footer_link_2_text','Legal Link 2 Text (e.g. Terms of Service)', 'text') ?>
      <?php renderField('footer_link_2_url', 'Legal Link 2 URL', 'text') ?>
    </div>
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--adm-border)">
      <div style="font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);margin-bottom:14px">Footer Text Style</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
        <?php renderStyleField('footer_font_size',   'Font Size',   '0.875rem') ?>
        <?php renderStyleField('footer_font_color',  'Font Color',  '#f7f2ea', '', 'color') ?>
        <?php renderStyleField('footer_font_family', 'Font Family', 'Inter, sans-serif') ?>
      </div>
    </div>
    <div style="margin-top:16px;padding:14px;background:#fef9c3;border-radius:var(--radius);border:1px solid #fde047;font-size:.8rem;color:#78350f">
      ℹ <strong>Explore links</strong> (Shop, Gallery, About, Cart) and <strong>Contact details</strong> are editable in the Contact & Social section.
    </div>
  </div>

  <!-- ══ SHOP ══ -->
  <div class="adm-card content-section" id="sec-shop" style="margin-top:20px;<?= $activeSection!=='shop'?'display:none':'' ?>">
    <h3 class="sec-title">Shop Page</h3>
    <div class="adm-form">
      <?php renderField('shop_eyebrow', 'Eyebrow Label', 'text', false, 'e.g. お花屋さん') ?>
      <?php renderField('shop_heading', 'Page Heading',  'text', false, 'e.g. Our Collection') ?>
      <?php renderField('shop_subtext', 'Sub-text / Description', 'textarea') ?>
    </div>
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--adm-border)">
      <div style="font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);margin-bottom:14px">Shop Heading Style</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
        <?php renderStyleField('shop_font_size',   'Font Size',   '2.4rem') ?>
        <?php renderStyleField('shop_font_color',  'Font Color',  '#1c1917', '', 'color') ?>
        <?php renderStyleField('shop_font_family', 'Font Family', 'Cormorant Garamond, serif') ?>
      </div>
    </div>
  </div>

  <!-- ══ GALLERY ══ -->
  <div class="adm-card content-section" id="sec-gallery" style="margin-top:20px;<?= $activeSection!=='gallery'?'display:none':'' ?>">
    <h3 class="sec-title">Gallery Page</h3>
    <div class="adm-form">
      <?php renderField('gallery_eyebrow', 'Eyebrow Label', 'text', false, 'e.g. Misaki Atelier') ?>
      <?php renderField('gallery_tagline', 'Tagline (italic line under MISAKI title)', 'text', false, 'e.g. — A quiet study of bloom, thread, and patience.') ?>
      <?php renderField('gallery_subtext', 'Sub-text / Description', 'textarea') ?>
    </div>
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--adm-border)">
      <div style="font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);margin-bottom:14px">Gallery Heading Style</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
        <?php renderStyleField('gallery_font_size',   'Font Size',   '2.4rem') ?>
        <?php renderStyleField('gallery_font_color',  'Font Color',  '#1c1917', '', 'color') ?>
        <?php renderStyleField('gallery_font_family', 'Font Family', 'Cormorant Garamond, serif') ?>
      </div>
    </div>
  </div>

  <!-- ══ ABOUT ══ -->
  <div class="adm-card content-section" id="sec-about" style="margin-top:20px;<?= $activeSection!=='about'?'display:none':'' ?>">
    <h3 class="sec-title">About Page</h3>
    <div class="adm-form">
      <?php renderField('about_eyebrow', 'Eyebrow Label', 'text') ?>
      <?php renderField('about_heading', 'Page Heading',  'text') ?>
      <?php renderField('about_body',    'Body Text',     'textarea') ?>
    </div>
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--adm-border)">
      <div style="font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);margin-bottom:14px">About Text Style</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
        <?php renderStyleField('about_font_size',   'Body Font Size',   '1rem') ?>
        <?php renderStyleField('about_font_color',  'Body Font Color',  '#1c1917', '', 'color') ?>
        <?php renderStyleField('about_font_family', 'Body Font Family', 'Inter, sans-serif') ?>
      </div>
    </div>
  </div>

  <!-- ══ CONTACT ══ -->
  <div class="adm-card content-section" id="sec-contact" style="margin-top:20px;<?= $activeSection!=='contact'?'display:none':'' ?>">
    <h3 class="sec-title">Contact & Social</h3>
    <div class="adm-form">
      <?php renderField('contact_email',     'Contact Email',    'text') ?>
      <?php renderField('contact_phone',     'Contact Phone',    'text') ?>
      <?php renderField('contact_instagram', 'Instagram Handle', 'text') ?>
      <?php renderField('gcash_number',      'GCash Number',     'text') ?>
      <?php renderField('gcash_name',        'GCash Account Name','text') ?>
    </div>
  </div>

  <!-- ══ SEO ══ -->
  <div class="adm-card content-section" id="sec-seo" style="margin-top:20px;<?= $activeSection!=='seo'?'display:none':'' ?>">
    <h3 class="sec-title">SEO / Meta</h3>
    <div class="adm-form">
      <?php renderField('meta_description', 'Default Meta Description', 'textarea') ?>
      <?php renderField('meta_og_title',    'Open Graph Title',         'text') ?>
    </div>
  </div>

  <div style="margin-top:24px;display:flex;gap:12px;align-items:center">
    <button type="submit" class="adm-btn adm-btn-primary" style="padding:11px 28px;font-size:.85rem">
      ✓ Save Content
    </button>
    <span style="font-size:.75rem;color:var(--adm-muted)">Changes apply immediately to the storefront.</span>
  </div>
</form>

<style>
  .sec-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.3rem;
    font-weight: 400;
    color: var(--adm-ink);
    margin-bottom: 20px;
  }
  .style-field-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .style-field-group label {
    font-size: .68rem;
    font-weight: 600;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--adm-muted);
  }
  .style-field-group input {
    font-family: 'Inter', sans-serif;
    font-size: .875rem;
    color: var(--adm-ink);
    border: 1px solid var(--adm-border);
    border-radius: var(--radius);
    padding: 9px 12px;
    background: var(--adm-white);
    width: 100%;
    outline: none;
  }
  .style-field-group input[type="color"] {
    padding: 4px 6px;
    height: 40px;
    cursor: pointer;
  }
  .style-field-group input:focus {
    border-color: var(--adm-sage);
    box-shadow: 0 0 0 3px rgba(61,90,62,.1);
  }
</style>

<?php
function renderField($key, $label, $type = 'text', $allowHtml = false, $placeholder = '') {
  global $allSettings;
  $val = $allSettings[$key] ?? '';
  $spanClass = ($type === 'textarea') ? 'span2' : '';
  echo '<label class="adm-label '.$spanClass.'">'
       . htmlspecialchars($label);
  if ($allowHtml) {
    echo ' <span style="font-size:.68rem;font-weight:400;text-transform:none;letter-spacing:0;color:#3d5a3e">(HTML tags like &lt;em&gt; are allowed)</span>';
  }
  if ($type === 'textarea') {
    echo '<textarea name="content['.htmlspecialchars($key).']" rows="4"'
         . ($placeholder ? ' placeholder="'.htmlspecialchars($placeholder).'"' : '') . '>'
         . htmlspecialchars($val) . '</textarea>';
  } else {
    echo '<input type="text" name="content['.htmlspecialchars($key).']" value="'.htmlspecialchars($val).'"'
         . ($placeholder ? ' placeholder="'.htmlspecialchars($placeholder).'"' : '') . '>';
  }
  echo '</label>';
}

function renderStyleField($key, $label, $default = '', $placeholder = '', $type = 'text') {
  global $allSettings;
  $val = $allSettings[$key] ?? $default;
  echo '<div class="style-field-group"><label>'.htmlspecialchars($label).'</label>';
  if ($type === 'color') {
    echo '<input type="color" name="content['.htmlspecialchars($key).']" value="'.htmlspecialchars($val).'">';
  } else {
    echo '<input type="text" name="content['.htmlspecialchars($key).']" value="'.htmlspecialchars($val).'"'
         . ($placeholder ? ' placeholder="'.htmlspecialchars($placeholder).'"' : '') . '>';
  }
  echo '</div>';
}
?>

<script>
function switchSection(key) {
  document.querySelectorAll('.content-section').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.adm-stab').forEach(el => el.classList.remove('active'));
  const sec = document.getElementById('sec-' + key);
  if (sec) sec.style.display = '';
  const btn = document.querySelector('[data-section="' + key + '"]');
  if (btn) btn.classList.add('active');
  const url = new URL(window.location);
  url.searchParams.set('section', key);
  history.replaceState({}, '', url);
}
</script>
