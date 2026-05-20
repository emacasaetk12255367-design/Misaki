<?php
/**
 * receipt.php — Generates a 1/8 A4 (74mm × 105mm) downloadable order receipt.
 * Access: user (own orders) or admin.
 */
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/settings.php';
require_once __DIR__.'/includes/products.php';

$orderId  = (int)($_GET['order_id'] ?? 0);
$isAdmin  = !empty($_GET['admin']) && current_admin_id();

// Security: user can only access own orders
if (!$isAdmin) {
  if (!current_user_id()) {
    header('Location: login.php'); exit;
  }
  $chk = db()->prepare('SELECT order_id FROM `order` WHERE order_id=? AND user_id=?');
  $chk->execute([$orderId, current_user_id()]);
  if (!$chk->fetch()) {
    die('<p style="font-family:sans-serif;padding:24px;color:red">Access denied or order not found.</p>');
  }
}

// Fetch order
$st = db()->prepare('SELECT o.*, u.full_name, u.email, u.phone FROM `order` o JOIN user u ON u.user_id=o.user_id WHERE o.order_id=?');
$st->execute([$orderId]);
$o = $st->fetch();
if (!$o) die('<p style="font-family:sans-serif;padding:24px;color:red">Order not found.</p>');

// Fetch items
$st = db()->prepare(
  'SELECT oi.*, p.name AS product_name, p.image
   FROM order_item oi JOIN product p ON p.product_id=oi.product_id
   WHERE oi.order_id=?'
);
$st->execute([$orderId]);
$items = $st->fetchAll();

// Fetch addons per item
$addonsPerItem = [];
foreach($items as $it){
  $st2 = db()->prepare(
    'SELECT a.name, oia.unit_price FROM order_item_addon oia
     JOIN addon a ON a.addon_id=oia.addon_id WHERE oia.order_item_id=?'
  );
  $st2->execute([$it['order_item_id']]);
  $addonsPerItem[$it['order_item_id']] = $st2->fetchAll();
}

$brand    = setting('brand_name', 'MISAKI');
$brandJp  = setting('brand_jp', 'handcrafted · 美咲');
$phone    = setting('contact_phone', '');
$email    = setting('contact_email', '');
$ig       = setting('contact_instagram', '');
$rn       = $o['receipt_number'] ?? 'MSK-'.date('Ymd', strtotime($o['created_at'])).'-'.str_pad($orderId,4,'0',STR_PAD_LEFT);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Receipt <?= htmlspecialchars($rn) ?> — <?= htmlspecialchars($brand) ?></title>
<style>
/* 1/8 of A4 = 74mm × 105mm */
@page {
  size: 74mm 105mm;
  margin: 0;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  width: 74mm;
  min-height: 105mm;
  font-family: 'Courier New', Courier, monospace;
  font-size: 7.5pt;
  color: #1c1917;
  background: #fff;
  padding: 5mm 4mm 4mm;
}
.header { text-align: center; border-bottom: 1px dashed #999; padding-bottom: 3mm; margin-bottom: 3mm; }
.brand  { font-family: Georgia, serif; font-size: 14pt; letter-spacing: .25em; font-weight: bold; }
.sub    { font-size: 6pt; letter-spacing: .15em; color: #555; margin-top: 1mm; }
.rn     { font-size: 6.5pt; margin-top: 2mm; color: #333; }
.meta   { font-size: 6.5pt; margin-bottom: 3mm; }
.meta div { margin-bottom: .8mm; }
.items-head { font-size: 6pt; letter-spacing: .08em; text-transform: uppercase; color: #666;
              border-bottom: 1px solid #ccc; padding-bottom: 1mm; margin-bottom: 1mm;
              display: flex; justify-content: space-between; }
.item   { display: flex; justify-content: space-between; font-size: 7pt; margin-bottom: 1mm; gap: 2mm; }
.item-name { flex: 1; }
.item-price { text-align: right; white-space: nowrap; }
.addon  { font-size: 6pt; color: #666; margin-left: 2mm; margin-bottom: .5mm; }
.divider { border: none; border-top: 1px dashed #999; margin: 2mm 0; }
.total-line { display: flex; justify-content: space-between; font-size: 7.5pt; margin-bottom: .8mm; }
.grand-total { font-size: 9pt; font-weight: bold; border-top: 2px solid #333; padding-top: 1.5mm; margin-top: 1mm; display: flex; justify-content: space-between; }
.footer { text-align: center; font-size: 5.5pt; color: #666; margin-top: 3mm; border-top: 1px dashed #999; padding-top: 2mm; }
.status-badge { display: inline-block; padding: 1mm 3mm; border-radius: 2mm;
                font-size: 6pt; font-weight: bold; letter-spacing: .06em; text-transform: uppercase;
                background: #dcfce7; color: #166534; margin-top: 1mm; }
.est { font-size: 6pt; margin-top: 1.5mm; color: #92400e; background: #fef9c3; padding: 1mm 2mm; border-radius: 1mm; }
/* Print button — hidden on print */
.print-btn { display: flex; justify-content: center; gap: 8px; padding: 4mm 0 2mm; }
@media print { .print-btn { display: none; } }
.print-btn button {
  padding: 6px 16px; border: 1px solid #3d5a3e; border-radius: 4px;
  background: #3d5a3e; color: white; font-size: 8pt; cursor: pointer;
}
</style>
</head>
<body>

<div class="print-btn">
  <button onclick="window.print()" style="background:#3d5a3e;border-color:#3d5a3e;">🖨 Print / Save PDF</button>
  <button onclick="window.close()" style="background:#78716c;border-color:#78716c;">✕ Close</button>
</div>

<div class="header">
  <div class="brand"><?= htmlspecialchars($brand) ?></div>
  <div class="sub"><?= htmlspecialchars($brandJp) ?></div>
  <div class="rn">Receipt #<?= htmlspecialchars($rn) ?></div>
</div>

<div class="meta">
  <div><strong>Date:</strong> <?= date('M j, Y · g:ia', strtotime($o['created_at'])) ?></div>
  <div><strong>Customer:</strong> <?= htmlspecialchars($o['full_name']) ?></div>
  <?php if($o['delivery_address']): ?>
  <div><strong>Deliver to:</strong> <?= htmlspecialchars($o['delivery_address']) ?></div>
  <?php endif; ?>
  <div><strong>Payment:</strong> <?= strtoupper(htmlspecialchars($o['payment_method'] ?? 'CASH')) ?></div>
  <span class="status-badge"><?= htmlspecialchars($o['status']) ?></span>
  <?php if(!empty($o['estimated_completion'])): ?>
  <div class="est">⏱ Est. completion: <?= date('M j, Y', strtotime($o['estimated_completion'])) ?></div>
  <?php endif; ?>
</div>

<div class="items-head"><span>Item</span><span>Total</span></div>

<?php foreach($items as $it): ?>
  <div class="item">
    <div class="item-name"><?= htmlspecialchars($it['product_name']) ?> ×<?= $it['qty'] ?></div>
    <div class="item-price">₱<?= number_format($it['line_total'],2) ?></div>
  </div>
  <?php foreach(($addonsPerItem[$it['order_item_id']] ?? []) as $ad): ?>
    <div class="addon">+ <?= htmlspecialchars($ad['name']) ?> ₱<?= number_format($ad['unit_price'],2) ?>/ea</div>
  <?php endforeach; ?>
<?php endforeach; ?>

<hr class="divider">
<?php
// Back-calculate subtotal (total - 125 delivery)
$deliveryFee = 125;
$subtotal = max(0, (float)$o['total'] - $deliveryFee);
?>
<div class="total-line">
  <span>Subtotal</span>
  <span>₱<?= number_format($subtotal,2) ?></span>
</div>
<div class="total-line">
  <span>Delivery</span>
  <span>₱<?= number_format($deliveryFee,2) ?></span>
</div>
<hr class="divider">
<div class="grand-total">
  <span>TOTAL</span>
  <span>₱<?= number_format($o['total'],2) ?></span>
</div>

<div class="footer">
  <?php if($phone): ?><?= htmlspecialchars($phone) ?><?php endif; ?>
  <?php if($email): ?> · <?= htmlspecialchars($email) ?><?php endif; ?><br>
  <?php if($ig): ?><?= htmlspecialchars($ig) ?><br><?php endif; ?>
  Thank you for your order! 花のように静かに
</div>
</body>
</html>
