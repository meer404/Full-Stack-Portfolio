<?php
require_once __DIR__ . '/_guard.php';

$keys = ['site_name', 'meta_description', 'hero_greeting', 'hero_tagline'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        foreach ($keys as $key) {
            $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value_en, setting_value_ku) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value_en = VALUES(setting_value_en), setting_value_ku = VALUES(setting_value_ku)');
            $stmt->execute([
                $key,
                $_POST[$key . '_en'] ?? '',
                $_POST[$key . '_ku'] ?? '',
            ]);
        }
        $success = 'Settings updated.';
    }
}

$settings = fetch_settings($pdo);

require_once __DIR__ . '/_layout_top.php';
?>
<div class="bg-white rounded-2xl p-6 shadow">
  <h2 class="text-lg font-semibold mb-4">Site Settings</h2>
  <?php if ($success): ?>
    <div class="mb-3 text-green-600 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="mb-3 text-red-600 text-sm"><?= e($error) ?></div>
  <?php endif; ?>
  <form method="POST" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <?php foreach ($keys as $key): ?>
      <div class="grid md:grid-cols-2 gap-4">
        <input class="border rounded-lg px-4 py-2" name="<?= e($key) ?>_en" placeholder="<?= e($key) ?> (EN)" value="<?= e($settings[$key]['en'] ?? '') ?>">
        <input class="border rounded-lg px-4 py-2" name="<?= e($key) ?>_ku" placeholder="<?= e($key) ?> (KU)" value="<?= e($settings[$key]['ku'] ?? '') ?>">
      </div>
    <?php endforeach; ?>
    <button class="px-4 py-2 bg-teal-500 text-black rounded-lg">Save</button>
  </form>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
