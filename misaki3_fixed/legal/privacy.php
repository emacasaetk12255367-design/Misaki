<?php
$page  = 'legal';
$title = 'Privacy Policy — Misaki Handcrafted';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/products.php';
require __DIR__.'/../includes/header.php';
?>

<style>
/* ── Privacy Page Styles ─────────────────────────── */
.privacy-hero {
  background: linear-gradient(135deg, var(--cream-dk, #ede7dc) 0%, var(--cream, #f7f2ea) 100%);
  padding: clamp(48px, 8vw, 96px) 24px clamp(32px, 5vw, 56px);
  text-align: center;
  border-bottom: 1px solid var(--border, #ddd6cc);
}
.privacy-hero .eyebrow {
  font-family: 'Shippori Mincho', serif;
  font-size: .85rem;
  letter-spacing: .18em;
  color: var(--sage-deep, #3d5a3e);
  margin-bottom: 12px;
}
.privacy-hero h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2.2rem, 5vw, 3.5rem);
  font-weight: 400;
  color: var(--ink, #1c1917);
  margin: 0 0 16px;
  line-height: 1.1;
}
.privacy-hero .subtitle {
  font-size: .875rem;
  color: var(--muted-fg, #78716c);
  max-width: 480px;
  margin: 0 auto;
  line-height: 1.7;
}
.privacy-body {
  max-width: 780px;
  margin: 0 auto;
  padding: clamp(40px, 6vw, 80px) 24px;
}
.privacy-section {
  margin-bottom: 48px;
}
.privacy-section-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: var(--sage-lt, #c4d9c4);
  color: var(--sage-deep, #3d5a3e);
  border-radius: 50%;
  font-size: .75rem;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  flex-shrink: 0;
  margin-right: 14px;
}
.privacy-section-heading {
  display: flex;
  align-items: center;
  margin-bottom: 16px;
}
.privacy-section-heading h2 {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.55rem;
  font-weight: 500;
  color: var(--ink, #1c1917);
  margin: 0;
}
.privacy-section p {
  font-size: .925rem;
  color: var(--ink-lt, #44403c);
  line-height: 1.85;
  margin: 0 0 12px;
}
.privacy-section ul {
  list-style: none;
  padding: 0;
  margin: 0 0 12px;
}
.privacy-section ul li {
  font-size: .925rem;
  color: var(--ink-lt, #44403c);
  line-height: 1.85;
  padding-left: 1.2em;
  position: relative;
  margin-bottom: 6px;
}
.privacy-section ul li::before {
  content: '❁';
  position: absolute;
  left: 0;
  top: 0;
  font-size: .65rem;
  color: var(--sage, #6b8f6c);
  top: .35em;
}
.privacy-divider {
  height: 1px;
  background: var(--border, #ddd6cc);
  margin: 0 0 48px;
}
.privacy-last-updated {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--card-bg, #fbf8f3);
  border: 1px solid var(--border, #ddd6cc);
  border-radius: 8px;
  padding: 10px 16px;
  font-size: .8rem;
  color: var(--muted-fg, #78716c);
  margin-bottom: 40px;
}
.privacy-footer-cta {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  padding-top: 40px;
  border-top: 1px solid var(--border, #ddd6cc);
}
</style>

<!-- Hero -->
<div class="privacy-hero">
  <div class="privacy-hero eyebrow">プライバシーポリシー</div>
  <h1>Privacy Policy</h1>
  <p class="subtitle">We believe trust is as important as beauty. Here is how we care for your information.</p>
</div>

<!-- Body -->
<div class="privacy-body">
  <div class="privacy-last-updated">
    🌿 Last updated: <strong>May 12, 2026</strong>
  </div>

  <!-- 1 -->
  <div class="privacy-section reveal">
    <div class="privacy-section-heading">
      <span class="privacy-section-num">1</span>
      <h2>Introduction</h2>
    </div>
    <p>Welcome to Misaki Handcrafted. We are committed to protecting and respecting your privacy. This policy sets out the basis on which we process any personal information we collect from you, guided by international data protection standards and the Philippine Data Privacy Act of 2012 (RA 10173).</p>
    <p>By using our website and placing orders, you agree to the practices described in this policy.</p>
  </div>
  <div class="privacy-divider"></div>

  <!-- 2 -->
  <div class="privacy-section reveal">
    <div class="privacy-section-heading">
      <span class="privacy-section-num">2</span>
      <h2>Information We Collect</h2>
    </div>
    <p>To fulfill your orders and provide a seamless experience, we collect:</p>
    <ul>
      <li>Your full name, email address, and phone number</li>
      <li>Delivery address and city</li>
      <li>Payment method and GCash receipt (when applicable)</li>
      <li>Order history and preferences</li>
    </ul>
    <p>We do not collect payment card numbers. All GCash transactions are handled by the platform's own infrastructure.</p>
  </div>
  <div class="privacy-divider"></div>

  <!-- 3 -->
  <div class="privacy-section reveal">
    <div class="privacy-section-heading">
      <span class="privacy-section-num">3</span>
      <h2>Recipient Data</h2>
    </div>
    <p>When you select <strong>"Someone Else"</strong> as a delivery label, we process the recipient's name and address solely for the purpose of fulfilling that specific delivery. This information is never shared with third parties for marketing.</p>
    <p>To protect the recipient's surprise and our payment process, orders sent to others require prepaid GCash payment.</p>
  </div>
  <div class="privacy-divider"></div>

  <!-- 4 -->
  <div class="privacy-section reveal">
    <div class="privacy-section-heading">
      <span class="privacy-section-num">4</span>
      <h2>How We Use Your Data</h2>
    </div>
    <p>Your information is used exclusively to:</p>
    <ul>
      <li>Process and fulfill your floral orders</li>
      <li>Send you order status updates and pickup notifications</li>
      <li>Respond to your inquiries and support requests</li>
      <li>Improve the quality and relevance of our offerings</li>
    </ul>
    <p>We do not sell, rent, or trade your personal data to any third party for commercial purposes.</p>
  </div>
  <div class="privacy-divider"></div>

  <!-- 5 -->
  <div class="privacy-section reveal">
    <div class="privacy-section-heading">
      <span class="privacy-section-num">5</span>
      <h2>Your Rights</h2>
    </div>
    <p>As a user in the Philippines, you have rights under the Data Privacy Act to:</p>
    <ul>
      <li>Access the personal data we hold about you</li>
      <li>Request correction of inaccurate information</li>
      <li>Request erasure of your account and associated data</li>
      <li>Withdraw consent at any time by contacting us</li>
    </ul>
    <p>You may update your profile details at any time from your <a href="../account.php" style="color:var(--sage-deep);text-decoration:underline">Account page</a>. For deletion requests, please contact us at the email below.</p>
  </div>
  <div class="privacy-divider"></div>

  <!-- 6 -->
  <div class="privacy-section reveal">
    <div class="privacy-section-heading">
      <span class="privacy-section-num">6</span>
      <h2>Contact Us</h2>
    </div>
    <p>If you have any questions about this Privacy Policy or how we handle your data, please reach out:</p>
    <ul>
      <li>Email: <?= htmlspecialchars(\setting('contact_email','hello@misaki.lorem')) ?></li>
      <li>Phone: <?= htmlspecialchars(\setting('contact_phone','+00 000 0000')) ?></li>
      <li>Instagram: <?= htmlspecialchars(\setting('contact_instagram','@misaki.handcrafted')) ?></li>
    </ul>
  </div>

  <!-- Footer CTA -->
  <div class="privacy-footer-cta reveal">
    <a href="../shop.php" class="btn btn-ink">Shop Our Blooms</a>
    <a href="../account.php" class="btn" style="border:1px solid var(--border)">My Account</a>
  </div>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>
