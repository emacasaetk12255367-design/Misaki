<?php
require_once __DIR__.'/../includes/auth.php';
if (current_admin_id()) { header('Location: index.php'); exit; }

$brand = 'MISAKI';
try {
  require_once __DIR__.'/../includes/settings.php';
  $brand = setting('brand_name', 'MISAKI');
} catch (Throwable $e) {}

$step  = 'request';
$err   = '';
$token = $_GET['token'] ?? '';

if ($token) {
  db()->exec("CREATE TABLE IF NOT EXISTS admin_password_reset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )");
  $st = db()->prepare('SELECT * FROM admin_password_reset WHERE token=? AND expires_at > NOW() AND used=0');
  $st->execute([$token]);
  $row = $st->fetch();
  if (!$row) {
    $err  = 'This reset link is invalid or has expired.';
  } else {
    $step = 'reset';
  }

  if ($step === 'reset' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw1 = $_POST['password'] ?? '';
    $pw2 = $_POST['password2'] ?? '';
    if (strlen($pw1) < 8) {
      $err = 'Password must be at least 8 characters.';
    } elseif ($pw1 !== $pw2) {
      $err = 'Passwords do not match.';
    } else {
      $hash = password_hash($pw1, PASSWORD_BCRYPT);
      db()->prepare('UPDATE admin_user SET password_hash=? WHERE admin_id=?')
          ->execute([$hash, $row['admin_id']]);
      db()->prepare('UPDATE admin_password_reset SET used=1 WHERE token=?')
          ->execute([$token]);
      $step = 'done';
    }
  }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $st = db()->prepare('SELECT admin_id, email FROM admin_user WHERE username=?');
  $st->execute([$username]);
  $admin = $st->fetch();

  if ($admin && !empty($admin['email'])) {
    db()->exec("CREATE TABLE IF NOT EXISTS admin_password_reset (
      id INT AUTO_INCREMENT PRIMARY KEY,
      admin_id INT NOT NULL,
      token VARCHAR(64) NOT NULL UNIQUE,
      expires_at DATETIME NOT NULL,
      used TINYINT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $tok = bin2hex(random_bytes(32));
    $exp = date('Y-m-d H:i:s', strtotime('+1 hour'));
    db()->prepare('INSERT INTO admin_password_reset (admin_id, token, expires_at) VALUES (?,?,?)')
        ->execute([$admin['admin_id'], $tok, $exp]);

    $resetLink = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']
               . dirname($_SERVER['REQUEST_URI'])
               . '/forgot_password.php?token='.$tok;

    $subject = "[$brand Admin] Password Reset";
    $body    = "Hello,\n\nA password reset was requested for admin account: $username\n\nReset link (valid 1 hour):\n$resetLink\n\n— $brand Admin";
    @mail($admin['email'], $subject, $body, "From: noreply@misaki.ph\r\nContent-Type: text/plain; charset=UTF-8");
  }
  $step = 'sent';
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Reset — <?= htmlspecialchars($brand) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', system-ui, sans-serif; background: #17211a; min-height: 100vh; display: grid; place-items: center; padding: 24px; -webkit-font-smoothing: antialiased; }
    .card { background: #1f2e24; border: 1px solid rgba(255,255,255,.07); border-radius: 16px; padding: 48px 40px; width: 100%; max-width: 380px; box-shadow: 0 24px 64px rgba(0,0,0,.4); }
    .brand { font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; font-weight: 500; letter-spacing: .24em; color: #e8e0d4; margin-bottom: 4px; }
    .sub { font-size: .65rem; letter-spacing: .18em; text-transform: uppercase; color: rgba(255,255,255,.3); margin-bottom: 36px; }
    h2 { font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: #e8e0d4; margin-bottom: 8px; font-weight: 400; }
    .desc { font-size: .78rem; color: rgba(255,255,255,.4); margin-bottom: 24px; line-height: 1.6; }
    .lbl { display: flex; flex-direction: column; gap: 7px; font-size: .68rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: rgba(255,255,255,.35); margin-bottom: 16px; }
    .lbl input { font-family: 'Inter', sans-serif; font-size: .875rem; color: #e8e0d4; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); border-radius: 6px; padding: 11px 14px; width: 100%; outline: none; transition: border-color .2s; }
    .lbl input:focus { border-color: #6b8f6c; }
    .err { background: rgba(176,40,40,.2); border: 1px solid rgba(176,40,40,.4); color: #f87171; font-size: .8rem; padding: 10px 14px; border-radius: 6px; margin-bottom: 20px; }
    .ok { background: rgba(61,90,62,.3); border: 1px solid rgba(107,143,108,.4); color: #86efac; font-size: .8rem; padding: 10px 14px; border-radius: 6px; margin-bottom: 20px; line-height: 1.6; }
    .btn { width: 100%; padding: 12px; background: #3d5a3e; color: #c4d9c4; border: none; border-radius: 6px; font-family: 'Inter', sans-serif; font-size: .82rem; font-weight: 500; letter-spacing: .08em; cursor: pointer; margin-top: 8px; transition: background .2s; text-decoration: none; display: block; text-align: center; }
    .btn:hover { background: #4a6e4b; }
    .back { display: block; text-align: center; margin-top: 20px; font-size: .75rem; color: rgba(255,255,255,.25); text-decoration: none; transition: color .2s; }
    .back:hover { color: rgba(255,255,255,.5); }
  </style>
</head>
<body>
  <div class="card">
    <div class="brand"><?= htmlspecialchars($brand) ?></div>
    <div class="sub">Admin Panel</div>

    <?php if ($step === 'request'): ?>
      <h2>Reset Admin Password</h2>
      <p class="desc">Enter your admin username. If it has an email on file, we'll send a reset link.</p>
      <?php if ($err): ?><div class="err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
      <form method="post">
        <label class="lbl">Admin Username
          <input name="username" required autocomplete="username" placeholder="admin">
        </label>
        <button class="btn" type="submit">Send reset link →</button>
      </form>

    <?php elseif ($step === 'sent'): ?>
      <h2>📬 Check email</h2>
      <div class="ok">If that admin account has an email address on file, a reset link has been sent. Check your inbox — valid for 1 hour.</div>

    <?php elseif ($step === 'reset'): ?>
      <h2>🔒 New password</h2>
      <p class="desc">Choose a secure password (min. 8 characters).</p>
      <?php if ($err): ?><div class="err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
      <form method="post">
        <label class="lbl">New Password
          <input name="password" type="password" required minlength="8" placeholder="Min. 8 characters">
        </label>
        <label class="lbl">Confirm Password
          <input name="password2" type="password" required minlength="8" placeholder="Repeat password">
        </label>
        <button class="btn" type="submit">Update password →</button>
      </form>

    <?php elseif ($step === 'done'): ?>
      <h2>✅ Password updated</h2>
      <div class="ok">Admin password changed successfully.</div>
      <a href="login.php" class="btn">Sign in →</a>
    <?php endif; ?>

    <a href="login.php" class="back">← Back to Admin Login</a>
  </div>
</body>
</html>
