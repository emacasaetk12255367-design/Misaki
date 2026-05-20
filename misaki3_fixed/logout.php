<?php
require_once __DIR__.'/includes/auth.php';
logout_user();
?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><title>Signing out…</title></head>
<body>
<script>
// FIX: Clear cart from localStorage on logout so guest visitors
// do not see the previous logged-in user's cart items.
localStorage.removeItem('misaki_cart');
window.location.replace('index.php');
</script>
</body></html>
