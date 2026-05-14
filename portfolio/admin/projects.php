<?php
require_once __DIR__ . '/_guard.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            $success = 'Project deleted.';
        } elseif ($action === 'toggle') {
            $stmt = $pdo->prepare('UPDATE projects SET is_featured = ? WHERE id = ?');
            $stmt->execute([$_POST['is_featured'], $_POST['id']]);
            $success = 'Featured status updated.';
        } elseif ($action === 'update' || $action === 'create') {
            $thumbnail = $_POST['current_thumbnail'] ?? '';
            if (!empty($_FILES['thumbnail']['name'])) {
                $file = $_FILES['thumbnail'];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    $error = 'Invalid image type.';
                } else {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('project_', true) . '.' . $ext;
                    $destination = UPLOADS_DIR . '/projects/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $thumbnail = '/portfolio/uploads/projects/' . $filename;
                    }
                }
            }

            if ($error === '') {
                if ($action === 'update') {
                    $stmt = $pdo->prepare('UPDATE projects SET title_en = ?, title_ku = ?, description_en = ?, description_ku = ?, thumbnail = ?, demo_url = ?, github_url = ?, tags = ?, is_featured = ?, sort_order = ? WHERE id = ?');
                    $stmt->execute([
                        $_POST['title_en'] ?? '',
                        $_POST['title_ku'] ?? '',
                        $_POST['description_en'] ?? '',
                        $_POST['description_ku'] ?? '',
                        $thumbnail,
                        $_POST['demo_url'] ?? '',
                        $_POST['github_url'] ?? '',
                        $_POST['tags'] ?? '',
                        isset($_POST['is_featured']) ? 1 : 0,
                        $_POST['sort_order'] ?? 0,
                        $_POST['id'],
                    ]);
                    $success = 'Project updated.';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO projects (title_en, title_ku, description_en, description_ku, thumbnail, demo_url, github_url, tags, is_featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([
                        $_POST['title_en'] ?? '',
                        $_POST['title_ku'] ?? '',
                        $_POST['description_en'] ?? '',
                        $_POST['description_ku'] ?? '',
                        $thumbnail,
                        $_POST['demo_url'] ?? '',
                        $_POST['github_url'] ?? '',
                        $_POST['tags'] ?? '',
                        isset($_POST['is_featured']) ? 1 : 0,
                        $_POST['sort_order'] ?? 0,
                    ]);
                    $success = 'Project added.';
                }
            }
        }
    }
}

$projects = $pdo->query('SELECT * FROM projects ORDER BY sort_order ASC, id DESC')->fetchAll();
$editId = $_GET['edit'] ?? null;
$editProject = null;
if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$editId]);
    $editProject = $stmt->fetch();
}

require_once __DIR__ . '/_layout_top.php';
?>
<div class="grid lg:grid-cols-2 gap-6">
  <div class="bg-white rounded-2xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4"><?= $editProject ? 'Edit Project' : 'Add Project' ?></h2>
    <?php if ($success): ?>
      <div class="mb-3 text-green-600 text-sm"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="mb-3 text-red-600 text-sm"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-3">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="<?= $editProject ? 'update' : 'create' ?>">
      <?php if ($editProject): ?>
        <input type="hidden" name="id" value="<?= e((string) $editProject['id']) ?>">
        <input type="hidden" name="current_thumbnail" value="<?= e($editProject['thumbnail'] ?? '') ?>">
      <?php endif; ?>
      <input class="border rounded-lg px-4 py-2 w-full" name="title_en" placeholder="Title (EN)" value="<?= e($editProject['title_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="title_ku" placeholder="Title (KU)" value="<?= e($editProject['title_ku'] ?? '') ?>">
      <textarea class="border rounded-lg px-4 py-2 w-full" name="description_en" placeholder="Description (EN)"><?= e($editProject['description_en'] ?? '') ?></textarea>
      <textarea class="border rounded-lg px-4 py-2 w-full" name="description_ku" placeholder="Description (KU)"><?= e($editProject['description_ku'] ?? '') ?></textarea>
      <input class="border rounded-lg px-4 py-2 w-full" name="demo_url" placeholder="Demo URL" value="<?= e($editProject['demo_url'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="github_url" placeholder="GitHub URL" value="<?= e($editProject['github_url'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="tags" placeholder="Tags (comma-separated)" value="<?= e($editProject['tags'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="sort_order" type="number" placeholder="Sort Order" value="<?= e((string) ($editProject['sort_order'] ?? 0)) ?>">
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_featured" <?= !empty($editProject['is_featured']) ? 'checked' : '' ?>>
        Featured
      </label>
      <div>
        <label class="block text-sm text-slate-500 mb-2">Thumbnail</label>
        <input type="file" name="thumbnail" class="border rounded-lg px-4 py-2 w-full">
        <?php if (!empty($editProject['thumbnail'])): ?>
          <img src="<?= e($editProject['thumbnail']) ?>" alt="Thumbnail" class="mt-3 w-32 h-20 object-cover rounded">
        <?php endif; ?>
      </div>
      <button class="px-4 py-2 bg-teal-500 text-black rounded-lg">Save</button>
    </form>
  </div>

  <div class="bg-white rounded-2xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4">Projects</h2>
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2">Title</th>
          <th>Tags</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($projects as $project): ?>
          <tr class="border-t">
            <td class="py-2"><?= e($project['title_en']) ?></td>
            <td><?= e($project['tags']) ?></td>
            <td>
              <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= e((string) $project['id']) ?>">
                <input type="hidden" name="is_featured" value="<?= $project['is_featured'] ? '0' : '1' ?>">
                <button class="text-teal-600" type="submit"><?= $project['is_featured'] ? 'Yes' : 'No' ?></button>
              </form>
            </td>
            <td class="flex gap-2">
              <a class="text-teal-600" href="?edit=<?= e((string) $project['id']) ?>">Edit</a>
              <form method="POST" onsubmit="return confirmDelete();">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= e((string) $project['id']) ?>">
                <button class="text-red-500" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
