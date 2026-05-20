<?php
// admin/images.php — Announcement Manager for Floralgram updates and platform posts
// Loaded by admin/index.php

$msg = '';
$err = '';
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$announceDir = __DIR__.'/../images/announcements/';

if (!is_dir($announceDir)) mkdir($announceDir, 0777, true);

ini_set('max_file_uploads', '20');
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '100M');
$allowedExts = ['jpg','jpeg','png','webp','gif','mp4','webm','mov','avi'];

/* ── Upload Announcement ── */
if ($action === 'upload_announcement') {
  $category = trim($_POST['category'] ?? 'announcements');
  $allowedCategories = ['announcements', 'updates', 'showcases'];
  if (!in_array($category, $allowedCategories, true)) {
    $category = 'announcements';
  }
  $title = trim($_POST['title'] ?? '');
  $body  = trim($_POST['body'] ?? '');
  if ($title === '') {
    $title = ucfirst($category);
  }

  $files = $_FILES['announcement_files'] ?? null;
  if (!$files || !isset($files['name'])) {
    $err = 'No files selected or upload error.';
  } else {
    if (!is_array($files['name'])) {
      $files = [
        'name' => [$files['name']],
        'type' => [$files['type']],
        'tmp_name' => [$files['tmp_name']],
        'error' => [$files['error']],
        'size' => [$files['size']],
      ];
    }

    $count = count($files['name']);
    if ($count === 0) {
      $err = 'No files selected or upload error.';
    } elseif ($count > 20) {
      $err = 'Please upload no more than 20 files at once.';
    } else {
      $pdo = db();
      $cur = $pdo->prepare("SELECT `value` FROM site_settings WHERE `key`='platform_announcements'");
      $cur->execute();
      $existing = $cur->fetchColumn();
      $list = $existing ? json_decode($existing, true) : [];
      $mediaItems = [];
      $errors = [];

      for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
          $errors[] = 'Upload failed for ' . ($files['name'][$i] ?? 'file #' . ($i + 1)) . '.';
          continue;
        }
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
          $errors[] = 'Invalid file type for ' . ($files['name'][$i] ?? 'file #' . ($i + 1)) . '.';
          continue;
        }

        $fname = 'announce_' . time() . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($files['tmp_name'][$i], $announceDir . $fname)) {
          $mediaType = in_array($ext, ['mp4', 'webm', 'mov', 'avi'], true) ? 'video' : 'image';
          $mediaItems[] = [
            'file' => $fname,
            'media_type' => $mediaType,
          ];
        } else {
          $errors[] = 'Upload failed for ' . ($files['name'][$i] ?? 'file #' . ($i + 1)) . '.';
        }
      }

      if (!empty($mediaItems)) {
        $list[] = [
          'id' => time() . '_' . uniqid(),
          'title' => $title,
          'category' => $category,
          'body' => $body,
          'files' => $mediaItems,
          'added' => date('Y-m-d H:i:s'),
        ];
        $value = json_encode($list);
        $pdo->prepare("INSERT INTO site_settings (`key`,`value`,`label`,`group`,`type`,`sort_order`)
                       VALUES ('platform_announcements',?,'Platform Announcements JSON','platform','textarea',25)
                       ON DUPLICATE KEY UPDATE `value`=?")->execute([$value, $value]);
        $msg = count($mediaItems) === 1 ? 'Announcement posted successfully.' : count($mediaItems) . ' files uploaded in one announcement.';
      }
      if (!empty($errors)) {
        $err = implode(' ', $errors);
      }
    }
  }
}

/* ── Delete Announcement ── */
if ($action === 'delete_announcement' && !empty($_POST['id'])) {
  $id = $_POST['id'];
  $pdo = db();
  $cur = $pdo->prepare("SELECT `value` FROM site_settings WHERE `key`='platform_announcements'");
  $cur->execute();
  $existing = $cur->fetchColumn();
  $list = $existing ? json_decode($existing, true) : [];
  $updated = [];
  foreach ($list as $item) {
    if ($item['id'] === $id) {
      if (!empty($item['files']) && is_array($item['files'])) {
        foreach ($item['files'] as $fileEntry) {
          $path = $announceDir . basename($fileEntry['file']);
          if (file_exists($path)) unlink($path);
        }
      } elseif (!empty($item['file'])) {
        $path = $announceDir . basename($item['file']);
        if (file_exists($path)) unlink($path);
      }
      continue;
    }
    $updated[] = $item;
  }
  $pdo->prepare("UPDATE site_settings SET `value`=? WHERE `key`='platform_announcements'")->execute([json_encode($updated)]);
  $msg = 'Announcement deleted.';
}

// ── Read current announcements ──────────────────────────────────────
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/settings.php';

$announcementSetting = setting('platform_announcements', '');
$announcements = $announcementSetting ? json_decode($announcementSetting, true) : [];
$announcements = array_reverse($announcements);
?>

<?php if ($err): ?><div class="adm-flash error">✗ <?= htmlspecialchars($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="adm-flash success">✓ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<p style="font-size:.82rem;color:var(--adm-muted);margin-bottom:24px;max-width:620px">
  Publish platform announcements and updates for the Floralgram experience.
  Upload a media file directly from your computer instead of entering a URL.
</p>

<div class="adm-section-head" style="margin-bottom:12px">
  <h2>Publish Announcement</h2>
  <span class="count"><?= count($announcements) ?></span>
</div>

<details class="adm-expand adm-card" style="margin-bottom:20px">
  <summary>+ New platform announcement</summary>
  <form method="post" enctype="multipart/form-data" style="margin-top:20px;display:flex;flex-direction:column;gap:14px;max-width:560px">
    <input type="hidden" name="action" value="upload_announcement">
    <label class="adm-label">Category
      <select name="category" required style="padding:10px">
        <option value="announcements">Announcements</option>
        <option value="updates">Updates</option>
        <option value="showcases">Showcases</option>
      </select>
    </label>
    <label class="adm-label">Title (optional)
      <input type="text" name="title" placeholder="Optional title for this announcement" style="padding:10px">
    </label>
    <label class="adm-label">Media Files
      <input type="file" id="announcementFiles" name="announcement_files[]" accept="image/*,video/*" multiple required style="padding:7px">
      <small style="font-size:.82rem;color:var(--adm-muted);margin-top:6px;display:block">Select up to 20 files total. Choosing files again adds to the current selection.</small>
      <div id="selectedFiles" style="font-size:.82rem;color:var(--adm-muted);margin-top:10px;line-height:1.5"></div>
    </label>
    <div><button class="adm-btn adm-btn-primary" type="submit">Publish announcement</button></div>
  </form>
  <script>
    (function() {
      const input = document.getElementById('announcementFiles');
      const preview = document.getElementById('selectedFiles');
      const form = input.closest('form');
      const selectedFiles = [];

      function updateInputFiles() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;
      }

      function updatePreview() {
        if (selectedFiles.length === 0) {
          preview.innerHTML = '<strong>Selected files:</strong> none';
          return;
        }
        const summary = `<strong>Selected ${selectedFiles.length} file${selectedFiles.length === 1 ? '' : 's'}:</strong>`;
        const list = selectedFiles.map((file, idx) => `<li style="margin-bottom:4px">${idx + 1}. ${file.name}</li>`);
        preview.innerHTML = `${summary}<ul style="margin:8px 0 0 18px;padding:0;list-style:none;">${list.join('')}</ul>`;
      }

      input.addEventListener('change', (event) => {
        const newFiles = Array.from(event.target.files);
        newFiles.forEach(file => {
          if (selectedFiles.length < 20) {
            const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size && f.type === file.type);
            if (!exists) selectedFiles.push(file);
          }
        });
        if (selectedFiles.length > 20) {
          selectedFiles.splice(20);
        }
        updateInputFiles();
        updatePreview();
      });

      form.addEventListener('submit', () => {
        updateInputFiles();
      });

      updatePreview();
    })();
  </script>
</details>

<?php if (empty($announcements)): ?>
  <div class="adm-card" style="text-align:center;padding:36px;color:var(--adm-muted)">
    No announcements yet. Create one above to share updates on the platform.
  </div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px">
  <?php foreach ($announcements as $item): ?>
    <div class="adm-card" style="padding:0;overflow:hidden">
      <?php
        $mediaItems = $item['files'] ?? [];
        if (empty($mediaItems) && !empty($item['file'])) {
          $mediaItems = [['file' => $item['file'], 'media_type' => $item['media_type'] ?? 'image']];
        }
      ?>
      <?php foreach ($mediaItems as $media): ?>
        <?php if ($media['media_type'] === 'video'): ?>
          <video controls style="width:100%;height:auto;max-height:420px;object-fit:contain;display:block;margin-bottom:12px" preload="metadata">
            <source src="../images/announcements/<?= htmlspecialchars($media['file']) ?>" type="video/<?= htmlspecialchars(pathinfo($media['file'], PATHINFO_EXTENSION)) ?>">
            Your browser does not support video playback.
          </video>
        <?php else: ?>
          <img src="../images/announcements/<?= htmlspecialchars($media['file']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="width:100%;height:auto;max-height:420px;object-fit:contain;display:block;margin-bottom:12px">
        <?php endif; ?>
      <?php endforeach; ?>
      <div style="padding:18px;display:flex;flex-direction:column;gap:10px">
        <div style="font-size:.95rem;font-weight:700;color:var(--adm-ink)"><?= htmlspecialchars($item['title']) ?></div>
        <div style="font-size:.82rem;color:var(--adm-muted);line-height:1.5;white-space:pre-wrap"><?= htmlspecialchars($item['body']) ?></div>
        <div style="font-size:.72rem;color:var(--adm-muted);">Posted: <?= htmlspecialchars($item['added']) ?></div>
        <form method="post" data-confirm='Delete this announcement?' data-danger style="margin-top:8px">
          <input type="hidden" name="action" value="delete_announcement">
          <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
          <button class="adm-btn adm-btn-danger" type="submit">Delete announcement</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
