<?php
// api/notifications.php
// Handles: admin poll (unread count), mark-read, send "Ready for Pick up" to user
require_once __DIR__.'/../includes/auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

/* ── Admin: poll unread count ──────────────────────────────── */
if ($action === 'admin_unread') {
    if (!current_admin_id()) { echo json_encode(['error'=>'forbidden']); exit; }
    $count = db()->query("SELECT COUNT(*) FROM admin_notification WHERE is_read=0")->fetchColumn();
    // Also fetch recent unread notifications
    $notifs = db()->query(
        "SELECT n.notif_id, n.message, n.created_at, n.order_id
         FROM admin_notification n
         WHERE n.is_read=0
         ORDER BY n.created_at DESC
         LIMIT 10"
    )->fetchAll();
    echo json_encode(['count'=>(int)$count, 'notifications'=>$notifs]);
    exit;
}

/* ── Admin: mark all admin notifications read ──────────────── */
if ($action === 'admin_mark_read') {
    if (!current_admin_id()) { echo json_encode(['error'=>'forbidden']); exit; }
    db()->exec("UPDATE admin_notification SET is_read=1 WHERE is_read=0");
    echo json_encode(['ok'=>true]);
    exit;
}

/* ── Admin: send "Ready for Pick up" notification to user ──── */
if ($action === 'notify_ready' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!current_admin_id()) { echo json_encode(['error'=>'forbidden']); exit; }
    $orderId = (int)($_POST['order_id'] ?? 0);
    if (!$orderId) { echo json_encode(['error'=>'missing order_id']); exit; }

    // Get order + user info
    $order = db()->prepare(
        "SELECT o.order_id, o.user_id, o.status, o.ready_notified, u.full_name
         FROM `order` o JOIN user u ON u.user_id=o.user_id
         WHERE o.order_id=?"
    );
    $order->execute([$orderId]);
    $o = $order->fetch();
    if (!$o) { echo json_encode(['error'=>'order not found']); exit; }

    // Insert user notification
    $msg = "🌸 Your order #$orderId is Ready for Pick up! Please come collect it at your convenience.";
    db()->prepare(
        "INSERT INTO user_notification (user_id, order_id, message) VALUES (?,?,?)"
    )->execute([$o['user_id'], $orderId, $msg]);

    // Mark order as notified
    db()->prepare("UPDATE `order` SET ready_notified=1 WHERE order_id=?")->execute([$orderId]);

    echo json_encode(['ok'=>true, 'message'=>$msg]);
    exit;
}

/* ── User: poll own unread count ───────────────────────────── */
if ($action === 'user_unread') {
    $uid = current_user_id();
    if (!$uid) { echo json_encode(['count'=>0, 'notifications'=>[]]); exit; }
    $count = db()->prepare("SELECT COUNT(*) FROM user_notification WHERE user_id=? AND is_read=0");
    $count->execute([$uid]);
    $notifs = db()->prepare(
        "SELECT notif_id, message, order_id, created_at
         FROM user_notification
         WHERE user_id=? AND is_read=0
         ORDER BY created_at DESC LIMIT 10"
    );
    $notifs->execute([$uid]);
    echo json_encode(['count'=>(int)$count->fetchColumn(), 'notifications'=>$notifs->fetchAll()]);
    exit;
}

/* ── User: mark own notifications read ────────────────────── */
if ($action === 'user_mark_read') {
    $uid = current_user_id();
    if (!$uid) { echo json_encode(['error'=>'not logged in']); exit; }
    db()->prepare("UPDATE user_notification SET is_read=1 WHERE user_id=? AND is_read=0")->execute([$uid]);
    echo json_encode(['ok'=>true]);
    exit;
}

echo json_encode(['error'=>'unknown action']);
