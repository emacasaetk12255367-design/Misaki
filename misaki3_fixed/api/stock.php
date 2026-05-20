<?php
// api/stock.php — returns current stock levels for all products as JSON
// Used by the frontend for real-time cart stock sync
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/../includes/db.php';

try {
    $rows = db()->query(
        'SELECT product_id, stock FROM product WHERE is_visible = 1'
    )->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($rows as $r) {
        $result[(string)$r['product_id']] = (int)$r['stock'];
    }

    echo json_encode($result, JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB error']);
}
