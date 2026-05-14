<?php
require_once __DIR__ . '/_guard.php';

$about = $pdo->query('SELECT * FROM about ORDER BY id ASC LIMIT 1')->fetch();
if (!$about) {
    $pdo->exec('INSERT INTO about (name_en, name_ku) VALUES ("", "")');
    $about = $pdo->query('SELECT * FROM about ORDER BY id ASC LIMIT 1')->fetch();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        $profilePath = $about['profile_image'] ?? '';
        if (!empty($_FILES['profile_image']['name'])) {
            $file = $_FILES['profile_image'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                $error = 'Invalid image type.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('profile_', true) . '.' . $ext;
                $destination = UPLOADS_DIR . '/profile/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $profilePath = '/portfolio/uploads/profile/' . $filename;
                }
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare('UPDATE about SET name_en = ?, name_ku = ?, bio_en = ?, bio_ku = ?, profile_image = ?, university_en = ?, university_ku = ?, graduation_year = ?, email = ?, phone = ?, github_url = ?, linkedin_url = ? WHERE id = ?');
            $stmt->execute([
                $_POST['name_en'] ?? '',
                $_POST['name_ku'] ?? '',
                $_POST['bio_en'] ?? '',
                $_POST['bio_ku'] ?? '',
                $profilePath,
                $_POST['university_en'] ?? '',
                $_POST['university_ku'] ?? '',
                $_POST['graduation_year'] ?? '',
                $_POST['email'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['github_url'] ?? '',
                $_POST['linkedin_url'] ?? '',
                $about['id'],
            ]);
            $success = 'About section updated.';
            $about = $pdo->query('SELECT * FROM about ORDER BY id ASC LIMIT 1')->fetch();
        }
    }
}

require_once __DIR__ . '/_layout_top.php';
?>
<div class="bg-white rounded-2xl p-6 shadow">
  <h2 class="text-lg font-semibold mb-4">About Section</h2>
  <?php if ($success): ?>
    <div class="mb-4 text-green-600 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="mb-4 text-red-600 text-sm"><?= e($error) ?></div>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input class="border rounded-lg px-4 py-2" name="name_en" placeholder="Name (EN)" value="<?= e($about['name_en'] ?? '') ?>">
    <input class="border rounded-lg px-4 py-2" name="name_ku" placeholder="Name (KU)" value="<?= e($about['name_ku'] ?? '') ?>">
    <textarea class="border rounded-lg px-4 py-2 md:col-span-2" name="bio_en" placeholder="Bio (EN)"><?= e($about['bio_en'] ?? '') ?></textarea>
    <textarea class="border rounded-lg px-4 py-2 md:col-span-2" name="bio_ku" placeholder="Bio (KU)"><?= e($about['bio_ku'] ?? '') ?></textarea>
    <input class="border rounded-lg px-4 py-2" name="university_en" placeholder="University (EN)" value="<?= e($about['university_en'] ?? '') ?>">
    <input class="border rounded-lg px-4 py-2" name="university_ku" placeholder="University (KU)" value="<?= e($about['university_ku'] ?? '') ?>">
    <input class="border rounded-lg px-4 py-2" name="graduation_year" placeholder="Graduation Year" value="<?= e($about['graduation_year'] ?? '') ?>">
    <input class="border rounded-lg px-4 py-2" name="email" placeholder="Email" value="<?= e($about['email'] ?? '') ?>">
    <input class="border rounded-lg px-4 py-2" name="phone" placeholder="Phone" value="<?= e($about['phone'] ?? '') ?>">
    <input class="border rounded-lg px-4 py-2" name="github_url" placeholder="GitHub URL" value="<?= e($about['github_url'] ?? '') ?>">
    <input class="border rounded-lg px-4 py-2" name="linkedin_url" placeholder="LinkedIn URL" value="<?= e($about['linkedin_url'] ?? '') ?>">
    <div class="md:col-span-2">
      <label class="block text-sm text-slate-500 mb-2">Profile Image</label>
      <input type="file" name="profile_image" class="border rounded-lg px-4 py-2 w-full">
      <?php if (!empty($about['profile_image'])): ?>
        <img src="<?= e($about['profile_image']) ?>" alt="Profile" class="mt-4 w-24 h-24 rounded-full object-cover">
      <?php endif; ?>
    </div>
    <div class="md:col-span-2">
      <button class="px-4 py-2 bg-teal-500 text-black rounded-lg">Save</button>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
