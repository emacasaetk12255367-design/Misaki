<?php
// api/gallery.php — returns gallery_collection + slides as JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');

require_once __DIR__ . '/../includes/db.php';

try {
    $collections = db()->query(
        'SELECT gallery_id, key_slug, name, tag, description
           FROM gallery_collection
          WHERE is_active = 1
          ORDER BY sort_order, gallery_id'
    )->fetchAll(PDO::FETCH_ASSOC);

    if (empty($collections)) {
        echo json_encode([]);
        exit;
    }

    $ids = implode(',', array_column($collections, 'gallery_id'));
    $slides = db()->query(
        "SELECT gallery_id, image_path, caption
           FROM gallery_slide
          WHERE gallery_id IN ($ids)
          ORDER BY gallery_id, sort_order, slide_id"
    )->fetchAll(PDO::FETCH_ASSOC);

    // Group slides under their parent collection
    $slideMap = [];
    foreach ($slides as $s) {
        $slideMap[$s['gallery_id']][] = [
            'img' => $s['image_path'],
            'cap' => $s['caption'],
        ];
    }

    $result = [];
    foreach ($collections as $c) {
        $result[] = [
            'key'    => $c['key_slug'],
            'name'   => $c['name'],
            'tag'    => $c['tag'],
            'desc'   => $c['description'],
            'slides' => $slideMap[$c['gallery_id']] ?? [],
        ];
    }

    echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB error']);
}
