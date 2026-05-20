<?php
// api/colors.php — returns active color_collection rows as JSON
// Called by main.js initHeroWheel() on page load.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300'); // 5-min browser cache

require_once __DIR__ . '/../includes/db.php';

try {
    $rows = db()->query(
        'SELECT color_id, collection_name, hex_code, hero_word, bg_image
           FROM color_collection
          WHERE is_active = 1
          ORDER BY sort_order, color_id'
    )->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB error']);
}
