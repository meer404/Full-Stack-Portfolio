<?php
require_once __DIR__ . '/_guard.php';

$resume = $pdo->query('SELECT * FROM resume ORDER BY id ASC LIMIT 1')->fetch();
if (!$resume) {
    $pdo->exec('INSERT INTO resume (cv_file, cv_filename_display) VALUES ("", "")');
    $resume = $pdo->query('SELECT * FROM resume ORDER BY id ASC LIMIT 1')->fetch();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        if (!empty($_FILES['cv_file']['name'])) {
            $file = $_FILES['cv_file'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            if ($mime !== 'application/pdf') {
                $error = 'Only PDF files are allowed.';
            } else {
                $filename = uniqid('cv_', true) . '.pdf';
                $destination = UPLOADS_DIR . '/cv/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $stmt = $pdo->prepare('UPDATE resume SET cv_file = ?, cv_filename_display = ? WHERE id = ?');
                    $stmt->execute([
                        '/portfolio/uploads/cv/' . $filename,
                        $_POST['cv_filename_display'] ?? $file['name'],
                        $resume['id'],
                    ]);
                    $success = 'CV uploaded.';
                }
            }
        } else {
            $stmt = $pdo->prepare('UPDATE resume SET cv_filename_display = ?, section_title_en = ?, section_title_ku = ? WHERE id = ?');
            $stmt->execute([
                $_POST['cv_filename_display'] ?? '',
                $_POST['section_title_en'] ?? 'Resume',
                $_POST['section_title_ku'] ?? 'ڕیزومێ',
                $resume['id'],
            ]);
            $success = 'Resume settings saved.';
        }
        $resume = $pdo->query('SELECT * FROM resume ORDER BY id ASC LIMIT 1')->fetch();
    }
}

require_once __DIR__ . '/_layout_top.php';
?>
<div class="bg-white rounded-2xl p-6 shadow">
  <h2 class="text-lg font-semibold mb-4">Resume Settings</h2>
  <?php if ($success): ?>
    <div class="mb-4 text-green-600 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="mb-4 text-red-600 text-sm"><?= e($error) ?></div>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="grid md:grid-cols-2 gap-4">
      <input class="border rounded-lg px-4 py-2" name="section_title_en" placeholder="Section title (EN)" value="<?= e($resume['section_title_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2" name="section_title_ku" placeholder="Section title (KU)" value="<?= e($resume['section_title_ku'] ?? '') ?>">
    </div>
    <input class="border rounded-lg px-4 py-2 w-full" name="cv_filename_display" placeholder="CV display filename" value="<?= e($resume['cv_filename_display'] ?? '') ?>">
    <div>
      <label class="block text-sm text-slate-500 mb-2">Upload PDF</label>
      <input type="file" name="cv_file" accept="application/pdf" class="border rounded-lg px-4 py-2 w-full">
    </div>
    <?php if (!empty($resume['cv_file'])): ?>
      <a href="<?= e($resume['cv_file']) ?>" target="_blank" class="text-teal-600 underline">Download current CV</a>
    <?php endif; ?>
    <button class="px-4 py-2 bg-teal-500 text-black rounded-lg">Save</button>
  </form>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
