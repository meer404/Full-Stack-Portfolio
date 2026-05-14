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
            $stmt = $pdo->prepare('DELETE FROM resume_education WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            $success = 'Education deleted.';
        } elseif ($action === 'update') {
            $stmt = $pdo->prepare('UPDATE resume_education SET degree_en = ?, degree_ku = ?, institution_en = ?, institution_ku = ?, year_range = ?, description_en = ?, description_ku = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([
                $_POST['degree_en'] ?? '',
                $_POST['degree_ku'] ?? '',
                $_POST['institution_en'] ?? '',
                $_POST['institution_ku'] ?? '',
                $_POST['year_range'] ?? '',
                $_POST['description_en'] ?? '',
                $_POST['description_ku'] ?? '',
                $_POST['sort_order'] ?? 0,
                $_POST['id'],
            ]);
            $success = 'Education updated.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO resume_education (degree_en, degree_ku, institution_en, institution_ku, year_range, description_en, description_ku, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $_POST['degree_en'] ?? '',
                $_POST['degree_ku'] ?? '',
                $_POST['institution_en'] ?? '',
                $_POST['institution_ku'] ?? '',
                $_POST['year_range'] ?? '',
                $_POST['description_en'] ?? '',
                $_POST['description_ku'] ?? '',
                $_POST['sort_order'] ?? 0,
            ]);
            $success = 'Education added.';
        }
    }
}

$items = $pdo->query('SELECT * FROM resume_education ORDER BY sort_order ASC, id ASC')->fetchAll();
$editId = $_GET['edit'] ?? null;
$editItem = null;
if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM resume_education WHERE id = ?');
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

require_once __DIR__ . '/_layout_top.php';
?>
<div class="grid lg:grid-cols-2 gap-6">
  <div class="bg-white rounded-2xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4"><?= $editItem ? 'Edit Education' : 'Add Education' ?></h2>
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
      <input class="border rounded-lg px-4 py-2 w-full" name="degree_en" placeholder="Degree (EN)" value="<?= e($editItem['degree_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="degree_ku" placeholder="Degree (KU)" value="<?= e($editItem['degree_ku'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="institution_en" placeholder="Institution (EN)" value="<?= e($editItem['institution_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="institution_ku" placeholder="Institution (KU)" value="<?= e($editItem['institution_ku'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="year_range" placeholder="Year Range" value="<?= e($editItem['year_range'] ?? '') ?>">
      <textarea class="border rounded-lg px-4 py-2 w-full" name="description_en" placeholder="Description (EN)"><?= e($editItem['description_en'] ?? '') ?></textarea>
      <textarea class="border rounded-lg px-4 py-2 w-full" name="description_ku" placeholder="Description (KU)"><?= e($editItem['description_ku'] ?? '') ?></textarea>
      <input class="border rounded-lg px-4 py-2 w-full" name="sort_order" type="number" placeholder="Sort Order" value="<?= e((string) ($editItem['sort_order'] ?? 0)) ?>">
      <button class="px-4 py-2 bg-teal-500 text-black rounded-lg">Save</button>
    </form>
  </div>

  <div class="bg-white rounded-2xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4">Education List</h2>
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2">Degree</th>
          <th>Institution</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr class="border-t">
            <td class="py-2"><?= e($item['degree_en']) ?></td>
            <td><?= e($item['institution_en']) ?></td>
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
