<?php
require_once __DIR__.'/includes/auth.php';

$step    = 'request'; // request | sent | reset | done
$err     = '';
$success = '';
$token   = $_GET['token'] ?? '';

// ── Step: validate reset token & show reset form ──────────────────────────────
if ($token) {
  $st = db()->prepare('SELECT * FROM password_reset WHERE token=? AND expires_at > NOW() AND used=0');
  $st->execute([$token]);
  $row = $st->fetch();
  if (!$row) {
    $err  = 'This reset link is invalid or has expired. Please request a new one.';
  } else {
    $step = 'reset';
  }

  // Handle new password submission
  if ($step === 'reset' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw1 = $_POST['password']  ?? '';
    $pw2 = $_POST['password2'] ?? '';
    if (strlen($pw1) < 8) {
      $err = 'Password must be at least 8 characters.';
    } elseif ($pw1 !== $pw2) {
      $err = 'Passwords do not match.';
    } else {
      $hash = password_hash($pw1, PASSWORD_BCRYPT);
      db()->prepare('UPDATE user SET password_hash=? WHERE user_id=?')
          ->execute([$hash, $row['user_id']]);
      db()->prepare('UPDATE password_reset SET used=1 WHERE token=?')
          ->execute([$token]);
      $step = 'done';
    }
  }

// ── Step: send reset email ─────────────────────────────────────────────────────
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $st = db()->prepare('SELECT user_id FROM user WHERE email=?');
  $st->execute([$email]);
  $user = $st->fetch();

  if ($user) {
    $tok = bin2hex(random_bytes(32));
    $exp = date('Y-m-d H:i:s', strtotime('+1 hour'));
    // Ensure table exists
    db()->exec("CREATE TABLE IF NOT EXISTS password_reset (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      token VARCHAR(64) NOT NULL UNIQUE,
      expires_at DATETIME NOT NULL,
      used TINYINT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    db()->prepare('INSERT INTO password_reset (user_id, token, expires_at) VALUES (?,?,?)')
        ->execute([$user['user_id'], $tok, $exp]);

    $resetLink = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']
               . dirname($_SERVER['REQUEST_URI'])
               . '/forgot_password.php?token='.$tok;

    // Send email (uses PHP mail(); configure SMTP in php.ini for production)
    $subject = 'Reset your Misaki password';
    $body    = "Hello,\n\nYou requested a password reset for your Misaki account.\n\nClick this link to reset your password (valid for 1 hour):\n$resetLink\n\nIf you didn't request this, you can safely ignore this email.\n\n— Misaki Handcrafted ❀";
    @mail($email, $subject, $body, "From: noreply@misaki.ph\r\nContent-Type: text/plain; charset=UTF-8");
  }
  // Always show "sent" to prevent email enumeration
  $step = 'sent';
}

$page='auth'; $title='Reset Password — Misaki';
require __DIR__.'/includes/header.php';
?>
<style>
/* Reuse the auth card style from login.php */
.auth-page-wrap {
  min-height: calc(100vh - var(--nav-h));
  display: flex; align-items: center; justify-content: center;
  padding: 48px var(--gutter);
  background: linear-gradient(160deg, var(--cream) 0%, var(--cream-dk) 100%);
}
.auth-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius-xl);
  padding: 52px 48px;
  width: 100%; max-width: 440px;
  box-shadow: 0 8px 48px rgba(28,25,23,.08), 0 2px 8px rgba(28,25,23,.04);
  position: relative; overflow: hidden;
}
.auth-card::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
  background: linear-gradient(90deg, var(--sage-deep), var(--sage), var(--sage-lt));
}
.auth-card h2 { font-family: var(--ff-display); font-size: 1.6rem; font-weight: 400; color: var(--ink); margin-bottom: 8px; }
.auth-card .subtitle { font-size: .85rem; color: var(--muted-fg); margin-bottom: 28px; line-height: 1.6; }
.auth-field { display: flex; flex-direction: column; gap: 7px; margin-bottom: 18px; }
.auth-field-label { font-size: .68rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-fg); }
.auth-field-input {
  padding: 13px 16px; border: 1.5px solid var(--border); border-radius: 8px;
  font-family: var(--ff-body); font-size: .9rem; color: var(--ink);
  background: var(--cream); transition: border-color .2s, box-shadow .2s, background .2s;
  width: 100%; box-sizing: border-box;
}
.auth-field-input:focus { outline: none; border-color: var(--sage); background: var(--white); box-shadow: 0 0 0 3px rgba(107,143,108,.15); }
.auth-submit-btn {
  width: 100%; padding: 14px; background: var(--ink); color: var(--white);
  border: none; border-radius: 8px; font-family: var(--ff-body); font-size: .78rem;
  font-weight: 600; letter-spacing: .14em; text-transform: uppercase; cursor: pointer;
  transition: background .2s, box-shadow .2s; margin-top: 8px;
}
.auth-submit-btn:hover { background: var(--ink-lt); box-shadow: 0 4px 20px rgba(28,25,23,.22); }
.auth-error-msg { background: var(--error-bg); border: 1px solid var(--error-bd); color: var(--error-fg); padding: 12px 16px; border-radius: 8px; font-size: .83rem; margin-bottom: 20px; }
.auth-success-msg { background: var(--success-bg); border: 1px solid var(--success-bd); color: var(--success-fg); padding: 14px 16px; border-radius: 8px; font-size: .88rem; margin-bottom: 20px; line-height: 1.6; }
.auth-back { display: block; text-align: center; margin-top: 24px; font-size: .82rem; color: var(--muted-fg); }
.auth-back a { color: var(--sage-deep); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
.done-icon { font-size: 3rem; text-align: center; display: block; margin-bottom: 16px; }
@media (max-width: 520px) { .auth-card { padding: 40px 28px; } }
</style>

<div class="auth-page-wrap">
  <div class="auth-card">

    <?php if ($step === 'request'): ?>
      <span style="font-size:1.6rem;display:block;margin-bottom:12px">🔑</span>
      <h2>Forgot password?</h2>
      <p class="subtitle">Enter your account email and we'll send you a reset link.</p>

      <?php if ($err): ?><div class="auth-error-msg"><?= htmlspecialchars($err) ?></div><?php endif; ?>

      <form method="post">
        <div class="auth-field">
          <span class="auth-field-label">Email address</span>
          <input name="email" type="email" class="auth-field-input" required
            placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email']??'') ?>">
        </div>
        <button class="auth-submit-btn" type="submit">Send reset link →</button>
      </form>

    <?php elseif ($step === 'sent'): ?>
      <span class="done-icon">📬</span>
      <h2>Check your email</h2>
      <div class="auth-success-msg">
        If an account exists for that email address, we've sent a password reset link.<br>
        Please check your inbox (and spam folder) — the link expires in <strong>1 hour</strong>.
      </div>

    <?php elseif ($step === 'reset'): ?>
      <span style="font-size:1.6rem;display:block;margin-bottom:12px">🔒</span>
      <h2>Set new password</h2>
      <p class="subtitle">Choose a strong password with at least 8 characters.</p>

      <?php if ($err): ?><div class="auth-error-msg"><?= htmlspecialchars($err) ?></div><?php endif; ?>

      <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="auth-field">
          <span class="auth-field-label">New Password</span>
          <input name="password" type="password" class="auth-field-input" required
            placeholder="Min. 8 characters" minlength="8">
        </div>
        <div class="auth-field">
          <span class="auth-field-label">Confirm New Password</span>
          <input name="password2" type="password" class="auth-field-input" required
            placeholder="Repeat password" minlength="8">
        </div>
        <button class="auth-submit-btn" type="submit">Update password →</button>
      </form>

    <?php elseif ($step === 'done'): ?>
      <span class="done-icon">✅</span>
      <h2>Password updated!</h2>
      <div class="auth-success-msg">Your password has been changed successfully. You can now sign in with your new password.</div>
      <a class="auth-submit-btn" href="login.php" style="text-decoration:none;display:block;text-align:center">Sign in →</a>

    <?php endif; ?>

    <p class="auth-back"><a href="login.php">← Back to Sign in</a></p>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>
