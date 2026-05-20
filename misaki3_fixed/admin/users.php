<?php
// admin/users.php — loaded by admin/index.php
require_once __DIR__.'/../includes/db.php';

$pdo = db();

// ── Counts ────────────────────────────────────────────────────
$totalUsers    = (int)$pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
$newThisMonth  = (int)$pdo->query("SELECT COUNT(*) FROM user WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn();
$usersWithOrder= (int)$pdo->query("SELECT COUNT(DISTINCT user_id) FROM `order`")->fetchColumn();

// ── Search ────────────────────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$sql    = "SELECT u.user_id, u.full_name, u.email, u.phone, u.created_at,
                  COUNT(o.order_id) AS order_count,
                  COALESCE(SUM(o.total),0) AS total_spent
           FROM user u
           LEFT JOIN `order` o ON o.user_id=u.user_id";
$params = [];
if ($search) {
    $sql .= " WHERE u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}
$sql .= " GROUP BY u.user_id ORDER BY u.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<!-- ── Stats row ─────────────────────────────────────────── -->
<div class="adm-stats" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px">
  <div class="adm-stat">
    <div class="adm-stat-label">Total Users</div>
    <div class="adm-stat-value"><?= $totalUsers ?></div>
    <div class="adm-stat-sub">Registered accounts</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-label">New This Month</div>
    <div class="adm-stat-value"><?= $newThisMonth ?></div>
    <div class="adm-stat-sub">Joined in <?= date('F Y') ?></div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-label">Active Buyers</div>
    <div class="adm-stat-value"><?= $usersWithOrder ?></div>
    <div class="adm-stat-sub">Users with ≥1 order</div>
  </div>
</div>

<!-- ── Section header + search ───────────────────────────── -->
<div class="adm-section-head" style="flex-wrap:wrap;gap:12px;margin-bottom:16px">
  <h2>Registered Users</h2>
  <form method="get" style="display:flex;gap:8px;align-items:center">
    <input type="hidden" name="tab" value="users">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
           placeholder="Search by name, email, or phone…"
           style="padding:8px 14px;border:1px solid var(--adm-border);border-radius:var(--radius);font-size:.82rem;min-width:240px">
    <button class="adm-btn adm-btn-primary" type="submit">Search</button>
    <?php if($search): ?>
      <a href="?tab=users" class="adm-btn">✕ Clear</a>
    <?php endif; ?>
  </form>
</div>

<!-- ── User table ────────────────────────────────────────── -->
<div class="adm-card" style="padding:0;overflow:hidden">
  <div class="adm-table-wrap">
    <table class="adm-table" style="min-width:700px">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Orders</th>
          <th>Total Spent</th>
          <th>Joined</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$users): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--adm-muted);padding:40px">
            <?= $search ? "No users found matching \"".htmlspecialchars($search)."\"." : "No registered users yet." ?>
          </td></tr>
        <?php endif; ?>
        <?php foreach ($users as $u): ?>
        <tr style="vertical-align:middle">
          <td style="color:var(--adm-muted);font-weight:500"><?= $u['user_id'] ?></td>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($u['full_name']) ?></div>
          </td>
          <td style="color:var(--adm-muted);font-size:.82rem"><?= htmlspecialchars($u['email']) ?></td>
          <td style="color:var(--adm-muted);font-size:.82rem"><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
          <td>
            <?php if ($u['order_count'] > 0): ?>
              <a href="?tab=orders" style="color:var(--adm-sage);font-weight:600;text-decoration:none"><?= $u['order_count'] ?></a>
            <?php else: ?>
              <span style="color:var(--adm-muted)">0</span>
            <?php endif; ?>
          </td>
          <td style="font-weight:500">
            <?= $u['total_spent'] > 0 ? '₱'.number_format($u['total_spent'], 2) : '<span style="color:var(--adm-muted)">—</span>' ?>
          </td>
          <td style="color:var(--adm-muted);font-size:.78rem;white-space:nowrap">
            <?= date('M j, Y', strtotime($u['created_at'])) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($users): ?>
  <div style="padding:12px 20px;border-top:1px solid var(--adm-border);font-size:.75rem;color:var(--adm-muted)">
    Showing <?= count($users) ?> of <?= $totalUsers ?> user<?= $totalUsers !== 1 ? 's' : '' ?>
    <?= $search ? " — filtered by \"".htmlspecialchars($search)."\"" : '' ?>
  </div>
  <?php endif; ?>
</div>
