<?php
// admin/orders.php — loaded by admin/index.php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/products.php';

/* ── Status update with stock restore on cancel ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'status') {
  $orderId   = (int)($_POST['order_id'] ?? 0);
  $newStatus = $_POST['status'] ?? '';
  $validSt   = ['pending','paid','fulfilled','cancelled'];
  if (!$orderId || !in_array($newStatus, $validSt)) {
    header('Location: ?tab=orders');
    exit;
  }

  // Fetch current status
  $cur = db()->prepare('SELECT status FROM `order` WHERE order_id=?');
  $cur->execute([$orderId]);
  $oldStatus = $cur->fetchColumn();

  $pdo = db();
  $pdo->beginTransaction();
  try {
    $pdo->prepare('UPDATE `order` SET status=? WHERE order_id=?')->execute([$newStatus, $orderId]);

    // If admin CANCELS an order → restore stock
    if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
      $items = $pdo->prepare('SELECT product_id, qty FROM order_item WHERE order_id=?');
      $items->execute([$orderId]);
      foreach ($items->fetchAll() as $it) {
        $pdo->prepare('UPDATE product SET stock = stock + ? WHERE product_id=?')
            ->execute([(int)$it['qty'], (int)$it['product_id']]);
      }
    }
    // If reversing a cancellation → deduct stock again
    if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
      $items = $pdo->prepare('SELECT product_id, qty FROM order_item WHERE order_id=?');
      $items->execute([$orderId]);
      foreach ($items->fetchAll() as $it) {
        $pdo->prepare('UPDATE product SET stock = GREATEST(0, stock - ?) WHERE product_id=?')
            ->execute([(int)$it['qty'], (int)$it['product_id']]);
      }
    }

    // If status → 'fulfilled', mark as ready (Lalamove notification trigger)
    if ($newStatus === 'fulfilled' && $oldStatus !== 'fulfilled') {
      $pdo->prepare('UPDATE `order` SET ready_notified=1 WHERE order_id=?')->execute([$orderId]);
    }

    $pdo->commit();
  } catch (Throwable $e) {
    $pdo->rollBack();
  }
  $returnStatus = $_GET['status'] ?? 'all';
  header('Location: ?tab=orders&status=' . urlencode($returnStatus));
  exit;
}

$statusFilter = $_GET['status'] ?? 'all';
$validStatuses = ['all','pending','paid','fulfilled','cancelled'];
if (!in_array($statusFilter, $validStatuses)) $statusFilter = 'all';

// Receipt search
$searchReceipt = trim($_GET['receipt'] ?? '');

$sql = 'SELECT o.*, u.email, u.full_name
        FROM `order` o JOIN user u ON u.user_id=o.user_id';
$params = [];
if ($searchReceipt) {
  $sql .= ' WHERE o.receipt_number LIKE ?';
  $params[] = '%'.$searchReceipt.'%';
} elseif ($statusFilter !== 'all') {
  $sql .= " WHERE o.status = ?";
  $params[] = $statusFilter;
}
$sql .= ' ORDER BY o.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Count by status for filter badges
$counts = db()->query(
  "SELECT status, COUNT(*) as n FROM `order` GROUP BY status"
)->fetchAll(PDO::FETCH_KEY_PAIR);
$allCount = array_sum($counts);
?>

<div class="adm-section-head">
  <h2>Orders</h2>
  <span class="count"><?= count($orders) ?> shown</span>
</div>

<!-- Receipt search -->
<form method="get" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
  <input type="hidden" name="tab" value="orders">
  <input type="text" name="receipt" value="<?= htmlspecialchars($searchReceipt) ?>"
         placeholder="Search by receipt number (e.g. MSK-20260516-0012)…"
         style="flex:1;min-width:240px;padding:9px 14px;border:1px solid var(--adm-border);border-radius:var(--radius);font-size:.82rem">
  <button class="adm-btn adm-btn-primary" type="submit">Search</button>
  <?php if($searchReceipt): ?>
    <a href="?tab=orders" class="adm-btn">✕ Clear</a>
  <?php endif; ?>
</form>

<!-- Status filter pills -->
<?php if(!$searchReceipt): ?>
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px">
  <?php
  $filters = ['all'=>'All','pending'=>'Pending','paid'=>'Paid','fulfilled'=>'Fulfilled','cancelled'=>'Cancelled'];
  foreach ($filters as $fk => $fl):
    $n = ($fk === 'all') ? $allCount : ($counts[$fk] ?? 0);
  ?>
    <a href="?tab=orders&status=<?= $fk ?>"
       style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:99px;font-size:.72rem;font-weight:500;letter-spacing:.04em;text-decoration:none;border:1px solid <?= $statusFilter===$fk ? 'var(--adm-sage)' : 'var(--adm-border)' ?>;background:<?= $statusFilter===$fk ? 'var(--adm-sage)' : 'transparent' ?>;color:<?= $statusFilter===$fk ? '#fff' : 'var(--adm-muted)' ?>">
      <?= $fl ?>
      <span style="background:rgba(255,255,255,.2);border-radius:99px;padding:1px 6px;font-size:.65rem"><?= $n ?></span>
    </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!$orders): ?>
  <div class="adm-card" style="text-align:center;padding:48px;color:var(--adm-muted)">
    No orders<?= $searchReceipt ? " matching receipt '$searchReceipt'" : ($statusFilter!=='all' ? " with status $statusFilter" : '') ?> yet.
  </div>
<?php endif; ?>

<?php foreach($orders as $o):
  $st = db()->prepare(
    'SELECT oi.*, p.name, p.stock FROM order_item oi
     JOIN product p ON p.product_id=oi.product_id WHERE order_id=?'
  );
  $st->execute([$o['order_id']]);
  $items = $st->fetchAll();

  $totalQty = array_sum(array_column($items, 'qty'));
  $estComp  = $o['estimated_completion'] ?? null;
  if (!$estComp && $totalQty > 0) {
    $estComp = estimate_completion_date($o['created_at'], $totalQty);
  }
  $isReady  = ($o['status'] === 'fulfilled');
?>
  <div class="adm-card" style="margin-bottom:16px<?= $isReady ? ';border-left:4px solid #166534' : '' ?>">
    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;align-items:flex-start">
      <div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
          <strong style="font-size:1rem">Order #<?= $o['order_id'] ?></strong>
          <?php if(!empty($o['receipt_number'])): ?>
            <span style="font-size:.72rem;background:var(--adm-cream);border:1px solid var(--adm-border);border-radius:4px;padding:2px 7px;font-family:monospace">
              <?= htmlspecialchars($o['receipt_number']) ?>
            </span>
          <?php endif; ?>
          <span class="pill <?= $o['status'] ?>"><?= $o['status'] ?></span>
          <?php if($isReady): ?>
            <?php if(!$o['ready_notified']): ?>
              <button
                onclick="sendReadyNotification(<?= $o['order_id'] ?>, this)"
                style="font-size:.7rem;background:#dcfce7;color:#166534;border-radius:99px;padding:4px 14px;font-weight:600;border:1px solid #86efac;cursor:pointer">
                🌸 Notify Customer — Ready for Pick up
              </button>
            <?php else: ?>
              <span style="font-size:.7rem;background:#dcfce7;color:#166534;border-radius:99px;padding:2px 10px;font-weight:600">
                ✓ Customer Notified — Ready for Pick up
              </span>
            <?php endif; ?>
          <?php endif; ?>
          <span style="font-size:.75rem;color:var(--adm-muted)"><?= date('M j, Y · g:ia', strtotime($o['created_at'])) ?></span>
        </div>
        <div style="margin-top:6px;font-size:.82rem;color:var(--adm-muted)">
          <?= htmlspecialchars($o['full_name']) ?> · <?= htmlspecialchars($o['email']) ?>
        </div>
        <?php if($o['delivery_address']): ?>
        <div style="margin-top:4px;font-size:.78rem;color:var(--adm-muted)">
          📍 <?= htmlspecialchars($o['delivery_name']) ?> · <?= htmlspecialchars($o['delivery_phone'] ?? '') ?><br>
          <?= htmlspecialchars($o['delivery_address']) ?>
        </div>
        <?php endif; ?>
        <div style="margin-top:6px;font-size:.82rem">
          Payment: <strong style="text-transform:uppercase"><?= htmlspecialchars($o['payment_method'] ?? 'cash') ?></strong>
          <?php if (!empty($o['payment_proof'])): ?>
            · <a href="../<?= htmlspecialchars($o['payment_proof']) ?>" target="_blank"
                 style="color:var(--adm-sage);text-decoration:underline">View GCash receipt ↗</a>
          <?php endif; ?>
        </div>
        <!-- Production Time Estimate -->
        <?php if($estComp && $o['status'] !== 'cancelled'): ?>
        <div style="margin-top:8px;font-size:.8rem;display:inline-flex;align-items:center;gap:6px;background:#fef9c3;border:1px solid #fde047;border-radius:6px;padding:5px 12px">
          ⏱ Est. completion: <strong><?= date('M j, Y (D)', strtotime($estComp)) ?></strong>
          &nbsp;·&nbsp; <?= estimate_production_days($totalQty) ?> working day<?= estimate_production_days($totalQty)>1?'s':'' ?> · <?= $totalQty ?> pcs
        </div>
        <?php endif; ?>
        <!-- Admin receipt download -->
        <?php if(!empty($o['receipt_number'])): ?>
        <div style="margin-top:6px">
          <a href="../receipt.php?order_id=<?= $o['order_id'] ?>&admin=1" target="_blank"
             style="font-size:.75rem;color:var(--adm-sage);text-decoration:underline">
            📄 View / Download Receipt
          </a>
        </div>
        <?php endif; ?>
      </div>

      <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <strong style="font-size:1.2rem;font-family:'Cormorant Garamond',serif">₱<?= number_format($o['total'],2) ?></strong>
        <form method="post" style="display:inline-flex;align-items:center;gap:8px">
          <input type="hidden" name="action" value="status">
          <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
          <select name="status" onchange="this.form.submit()"
                  style="font-size:.78rem;padding:6px 10px;border:1px solid var(--adm-border);border-radius:var(--radius);background:var(--adm-white);color:var(--adm-ink);cursor:pointer">
            <?php foreach(['pending','paid','fulfilled','cancelled'] as $s): ?>
              <option <?= $o['status']===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>
    </div>

    <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--adm-border)">
      <div style="font-size:.65rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);margin-bottom:8px">Items</div>
      <?php foreach($items as $it): ?>
        <div style="display:flex;justify-content:space-between;font-size:.82rem;padding:4px 0;color:var(--adm-muted)">
          <span>
            <?= htmlspecialchars($it['name']) ?> × <?= $it['qty'] ?>
            <?php
              $st_now = (int)$it['stock'];
              if($o['status'] !== 'cancelled' && $st_now <= 10):
            ?>
              <span style="color:#ef4444;font-size:.7rem;font-weight:600"> [Stock: <?= $st_now ?>]</span>
            <?php endif; ?>
          </span>
          <span>₱<?= number_format($it['line_total'],2) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endforeach; ?>

<script>
function sendReadyNotification(orderId, btn) {
  if (!confirm('Send "Ready for Pick up" notification to this customer?')) return;
  btn.disabled = true;
  btn.textContent = 'Sending…';
  const fd = new FormData();
  fd.append('action', 'notify_ready');
  fd.append('order_id', orderId);
  fetch('../api/notifications.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.ok) {
        btn.textContent = '✓ Customer Notified — Ready for Pick up';
        btn.style.cursor = 'default';
        btn.style.background = '#dcfce7';
      } else {
        btn.disabled = false;
        btn.textContent = '🌸 Notify Customer — Ready for Pick up';
        alert('Error: ' + (d.error || 'unknown'));
      }
    })
    .catch(() => { btn.disabled = false; btn.textContent = '🌸 Notify Customer — Ready for Pick up'; alert('Network error.'); });
}
</script>
