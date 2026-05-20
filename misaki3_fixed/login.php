<?php
require_once __DIR__.'/includes/auth.php';
$err  = '';
$next = $_GET['next'] ?? 'index.php';
if(!preg_match('/^[a-z0-9_.\\/\\-?=%]+$/i',$next)) $next='index.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  if(login_user($email,$pass)){ header('Location: '.$next); exit; }
  $err = 'Invalid email or password.';
}
$page='auth'; $title='Sign in — Misaki';
require __DIR__.'/includes/header.php';
?>
<style>
.auth-page-wrap {
  min-height: calc(100vh - var(--nav-h));
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px var(--gutter);
  background: linear-gradient(160deg, var(--cream) 0%, var(--cream-dk) 100%);
}
.auth-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius-xl);
  padding: 52px 48px;
  width: 100%;
  max-width: 440px;
  box-shadow: 0 8px 48px rgba(28,25,23,.08), 0 2px 8px rgba(28,25,23,.04);
  position: relative;
  overflow: hidden;
}
.auth-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--sage-deep), var(--sage), var(--sage-lt));
}
.auth-brand-mark {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-bottom: 36px;
}
.auth-brand-mark .flower {
  font-size: 2rem;
  margin-bottom: 10px;
  display: inline-block;
  animation: bloom 2.4s ease-in-out infinite alternate;
}
@keyframes bloom {
  from { transform: scale(1) rotate(-3deg); }
  to   { transform: scale(1.12) rotate(3deg); }
}
.auth-brand-name {
  font-family: var(--ff-display);
  font-size: 1.8rem;
  font-weight: 500;
  letter-spacing: .22em;
  color: var(--ink);
}
.auth-brand-sub {
  font-size: .6rem;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: var(--muted-fg);
  margin-top: 4px;
}
.auth-card h2 {
  font-family: var(--ff-display);
  font-size: 1.4rem;
  font-weight: 400;
  color: var(--ink);
  margin-bottom: 6px;
}
.auth-card .subtitle {
  font-size: .82rem;
  color: var(--muted-fg);
  margin-bottom: 28px;
}
.auth-field {
  display: flex;
  flex-direction: column;
  gap: 7px;
  margin-bottom: 18px;
}
.auth-field-label {
  font-size: .68rem;
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--muted-fg);
}
.auth-field-input {
  padding: 13px 16px;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  font-family: var(--ff-body);
  font-size: .9rem;
  color: var(--ink);
  background: var(--cream);
  transition: border-color .2s, box-shadow .2s, background .2s;
  width: 100%;
  box-sizing: border-box;
}
.auth-field-input:focus {
  outline: none;
  border-color: var(--sage);
  background: var(--white);
  box-shadow: 0 0 0 3px rgba(107,143,108,.15);
}
.auth-submit-btn {
  width: 100%;
  padding: 14px;
  background: var(--ink);
  color: var(--white);
  border: none;
  border-radius: 8px;
  font-family: var(--ff-body);
  font-size: .78rem;
  font-weight: 600;
  letter-spacing: .14em;
  text-transform: uppercase;
  cursor: pointer;
  transition: background .2s, transform .15s, box-shadow .2s;
  margin-top: 8px;
}
.auth-submit-btn:hover {
  background: var(--ink-lt);
  box-shadow: 0 4px 20px rgba(28,25,23,.22);
}
.auth-submit-btn:active { transform: scale(.99); }
.auth-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 24px 0;
  color: var(--muted-fg);
  font-size: .72rem;
  letter-spacing: .06em;
}
.auth-divider::before, .auth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}
.auth-alt-link {
  display: block;
  text-align: center;
  font-size: .82rem;
  color: var(--muted-fg);
}
.auth-alt-link a {
  color: var(--sage-deep);
  font-weight: 600;
  text-decoration: underline;
  text-decoration-color: var(--sage-lt);
  text-underline-offset: 3px;
  transition: color .15s;
}
.auth-alt-link a:hover { color: var(--ink); }
.auth-error-msg {
  background: var(--error-bg);
  border: 1px solid var(--error-bd);
  color: var(--error-fg);
  padding: 12px 16px;
  border-radius: 8px;
  font-size: .83rem;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.auth-forgot {
  text-align: right;
  margin-top: -10px;
  margin-bottom: 18px;
}
.auth-forgot a {
  font-size: .75rem;
  color: var(--muted-fg);
  text-decoration: underline;
  text-decoration-color: var(--border);
  text-underline-offset: 3px;
  transition: color .15s;
}
.auth-forgot a:hover { color: var(--sage-deep); }
@media (max-width: 520px) {
  .auth-card { padding: 40px 28px; }
}
</style>

<div class="auth-page-wrap">
  <div class="auth-card">
    <div class="auth-brand-mark">
      <span class="flower">❀</span>
      <div class="auth-brand-name">MISAKI</div>
      <div class="auth-brand-sub">handcrafted · 美咲</div>
    </div>

    <h2>Welcome back</h2>
    <p class="subtitle">Sign in to your account to continue.</p>

    <?php if($err): ?>
    <div class="auth-error-msg">
      <span>✕</span> <?= htmlspecialchars($err) ?>
    </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="auth-field">
        <span class="auth-field-label">Email address</span>
        <input name="email" type="email" class="auth-field-input" required
          autocomplete="email" placeholder="you@example.com"
          value="<?= htmlspecialchars($_POST['email']??'') ?>">
      </div>
      <div class="auth-field">
        <span class="auth-field-label">Password</span>
        <input name="password" type="password" class="auth-field-input" required
          autocomplete="current-password" placeholder="••••••••">
      </div>
      <div class="auth-forgot">
        <a href="forgot_password.php">Forgot password?</a>
      </div>
      <button class="auth-submit-btn" type="submit">Sign in →</button>
    </form>

    <div class="auth-divider">or</div>

    <p class="auth-alt-link">
      No account yet? <a href="register.php?next=<?= urlencode($next) ?>">Create one</a>
    </p>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>
