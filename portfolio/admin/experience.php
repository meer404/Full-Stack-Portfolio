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
            $stmt = $pdo->prepare('DELETE FROM resume_experience WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            $success = 'Experience deleted.';
        } elseif ($action === 'update') {
            $stmt = $pdo->prepare('UPDATE resume_experience SET title_en = ?, title_ku = ?, company_en = ?, company_ku = ?, date_range_en = ?, date_range_ku = ?, description_en = ?, description_ku = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([
                $_POST['title_en'] ?? '',
                $_POST['title_ku'] ?? '',
                $_POST['company_en'] ?? '',
                $_POST['company_ku'] ?? '',
                $_POST['date_range_en'] ?? '',
                $_POST['date_range_ku'] ?? '',
                $_POST['description_en'] ?? '',
                $_POST['description_ku'] ?? '',
                $_POST['sort_order'] ?? 0,
                $_POST['id'],
            ]);
            $success = 'Experience updated.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO resume_experience (title_en, title_ku, company_en, company_ku, date_range_en, date_range_ku, description_en, description_ku, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $_POST['title_en'] ?? '',
                $_POST['title_ku'] ?? '',
                $_POST['company_en'] ?? '',
                $_POST['company_ku'] ?? '',
                $_POST['date_range_en'] ?? '',
                $_POST['date_range_ku'] ?? '',
                $_POST['description_en'] ?? '',
                $_POST['description_ku'] ?? '',
                $_POST['sort_order'] ?? 0,
            ]);
            $success = 'Experience added.';
        }
    }
}

$items = $pdo->query('SELECT * FROM resume_experience ORDER BY sort_order ASC, id ASC')->fetchAll();
$editId = $_GET['edit'] ?? null;
$editItem = null;
if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM resume_experience WHERE id = ?');
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

require_once __DIR__ . '/_layout_top.php';
?>
<div class="grid lg:grid-cols-2 gap-6">
  <div class="bg-white rounded-2xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4"><?= $editItem ? 'Edit Experience' : 'Add Experience' ?></h2>
    <?php if ($success): ?>
      <div class="mb-3 text-green-600 text-sm"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="mb-3 text-red-600 text-sm"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-3">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <?php if ($editItem): ?>
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= e((string) $editItem['id']) ?>">
      <?php endif; ?>
      <input class="border rounded-lg px-4 py-2 w-full" name="title_en" placeholder="Title (EN)" value="<?= e($editItem['title_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="title_ku" placeholder="Title (KU)" value="<?= e($editItem['title_ku'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="company_en" placeholder="Company (EN)" value="<?= e($editItem['company_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="company_ku" placeholder="Company (KU)" value="<?= e($editItem['company_ku'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="date_range_en" placeholder="Date Range (EN)" value="<?= e($editItem['date_range_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="date_range_ku" placeholder="Date Range (KU)" value="<?= e($editItem['date_range_ku'] ?? '') ?>">
      <textarea class="border rounded-lg px-4 py-2 w-full" name="description_en" placeholder="Description (EN)"><?= e($editItem['description_en'] ?? '') ?></textarea>
      <textarea class="border rounded-lg px-4 py-2 w-full" name="description_ku" placeholder="Description (KU)"><?= e($editItem['description_ku'] ?? '') ?></textarea>
      <input class="border rounded-lg px-4 py-2 w-full" name="sort_order" type="number" placeholder="Sort Order" value="<?= e((string) ($editItem['sort_order'] ?? 0)) ?>">
      <button class="px-4 py-2 bg-teal-500 text-black rounded-lg">Save</button>
    </form>
  </div>

  <div class="bg-white rounded-2xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4">Experience List</h2>
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2">Title</th>
          <th>Company</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr class="border-t">
            <td class="py-2"><?= e($item['title_en']) ?></td>
            <td><?= e($item['company_en']) ?></td>
            <td class="flex gap-2">
              <a class="text-teal-600" href="?edit=<?= e((string) $item['id']) ?>">Edit</a>
              <form method="POST" onsubmit="return confirmDelete();">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= e((string) $item['id']) ?>">
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
