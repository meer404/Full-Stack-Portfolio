<?php
require_once __DIR__ . '/_guard.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'read') {
            $stmt = $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            $success = 'Message marked as read.';
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            $success = 'Message deleted.';
        } elseif ($action === 'mark_all') {
            $pdo->exec('UPDATE contact_messages SET is_read = 1');
            $success = 'All messages marked as read.';
        }
    }
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY received_at DESC')->fetchAll();

require_once __DIR__ . '/_layout_top.php';
?>
<div class="bg-white rounded-2xl p-6 shadow">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Messages</h2>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="mark_all">
      <button class="text-sm text-teal-600" type="submit">Mark all read</button>
    </form>
  </div>
  <?php if ($success): ?>
    <div class="mb-3 text-green-600 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="mb-3 text-red-600 text-sm"><?= e($error) ?></div>
  <?php endif; ?>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2">Sender</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($messages as $message): ?>
          <tr class="border-t <?= $message['is_read'] ? '' : 'bg-teal-50' ?>">
            <td class="py-2"><?= e($message['sender_name']) ?></td>
            <td><?= e($message['sender_email']) ?></td>
            <td><?= e($message['subject']) ?></td>
            <td><?= e($message['received_at']) ?></td>
            <td>
              <?= $message['is_read'] ? 'Read' : 'Unread' ?>
            </td>
            <td class="flex gap-2">
              <?php if (!$message['is_read']): ?>
                <form method="POST">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="action" value="read">
                  <input type="hidden" name="id" value="<?= e((string) $message['id']) ?>">
                  <button class="text-teal-600" type="submit">Mark Read</button>
                </form>
              <?php endif; ?>
              <form method="POST" onsubmit="return confirmDelete();">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= e((string) $message['id']) ?>">
                <button class="text-red-500" type="submit">Delete</button>
              </form>
            </td>
          </tr>
          <tr class="border-b">
            <td colspan="6" class="py-2 text-slate-500 text-sm"><?= e($message['message']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
