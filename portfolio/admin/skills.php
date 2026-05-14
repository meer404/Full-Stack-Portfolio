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
            $stmt = $pdo->prepare('DELETE FROM resume_skills WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            $success = 'Skill deleted.';
        } elseif ($action === 'update') {
            $stmt = $pdo->prepare('UPDATE resume_skills SET skill_name_en = ?, skill_name_ku = ?, skill_level = ?, category_en = ?, category_ku = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([
                $_POST['skill_name_en'] ?? '',
                $_POST['skill_name_ku'] ?? '',
                $_POST['skill_level'] ?? 0,
                $_POST['category_en'] ?? '',
                $_POST['category_ku'] ?? '',
                $_POST['sort_order'] ?? 0,
                $_POST['id'],
            ]);
            $success = 'Skill updated.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO resume_skills (skill_name_en, skill_name_ku, skill_level, category_en, category_ku, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $_POST['skill_name_en'] ?? '',
                $_POST['skill_name_ku'] ?? '',
                $_POST['skill_level'] ?? 0,
                $_POST['category_en'] ?? '',
                $_POST['category_ku'] ?? '',
                $_POST['sort_order'] ?? 0,
            ]);
            $success = 'Skill added.';
        }
    }
}

$skills = $pdo->query('SELECT * FROM resume_skills ORDER BY sort_order ASC, id ASC')->fetchAll();
$editId = $_GET['edit'] ?? null;
$editSkill = null;
if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM resume_skills WHERE id = ?');
    $stmt->execute([$editId]);
    $editSkill = $stmt->fetch();
}

require_once __DIR__ . '/_layout_top.php';
?>
<div class="grid lg:grid-cols-2 gap-6">
  <div class="bg-white rounded-2xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4"><?= $editSkill ? 'Edit Skill' : 'Add Skill' ?></h2>
    <?php if ($success): ?>
      <div class="mb-3 text-green-600 text-sm"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="mb-3 text-red-600 text-sm"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-3">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <?php if ($editSkill): ?>
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= e((string) $editSkill['id']) ?>">
      <?php endif; ?>
      <input class="border rounded-lg px-4 py-2 w-full" name="skill_name_en" placeholder="Skill Name (EN)" value="<?= e($editSkill['skill_name_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="skill_name_ku" placeholder="Skill Name (KU)" value="<?= e($editSkill['skill_name_ku'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="skill_level" type="number" min="0" max="100" placeholder="Level" value="<?= e((string) ($editSkill['skill_level'] ?? 80)) ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="category_en" placeholder="Category (EN)" value="<?= e($editSkill['category_en'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="category_ku" placeholder="Category (KU)" value="<?= e($editSkill['category_ku'] ?? '') ?>">
      <input class="border rounded-lg px-4 py-2 w-full" name="sort_order" type="number" placeholder="Sort Order" value="<?= e((string) ($editSkill['sort_order'] ?? 0)) ?>">
      <button class="px-4 py-2 bg-teal-500 text-black rounded-lg">Save</button>
    </form>
  </div>

  <div class="bg-white rounded-2xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4">Skills</h2>
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2">Name</th>
          <th>Level</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($skills as $skill): ?>
          <tr class="border-t">
            <td class="py-2"><?= e($skill['skill_name_en']) ?></td>
            <td><?= e((string) $skill['skill_level']) ?>%</td>
            <td class="flex gap-2">
              <a class="text-teal-600" href="?edit=<?= e((string) $skill['id']) ?>">Edit</a>
              <form method="POST" onsubmit="return confirmDelete();">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= e((string) $skill['id']) ?>">
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
