<?php
require_once __DIR__ . '/_guard.php';

$totalProjects = (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
$totalMessages = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
$unreadMessages = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
$totalSkills = (int) $pdo->query('SELECT COUNT(*) FROM resume_skills')->fetchColumn();
$lastCv = $pdo->query('SELECT updated_at FROM resume ORDER BY id ASC LIMIT 1')->fetchColumn();

$recentMessages = $pdo->query('SELECT * FROM contact_messages ORDER BY received_at DESC LIMIT 5')->fetchAll();

require_once __DIR__ . '/_layout_top.php';
?>
<div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
  <div class="bg-white rounded-2xl p-5 shadow">
    <p class="text-sm text-slate-500">Total Projects</p>
    <p class="text-2xl font-semibold"><?= e((string) $totalProjects) ?></p>
  </div>
  <div class="bg-white rounded-2xl p-5 shadow">
    <p class="text-sm text-slate-500">Messages</p>
    <p class="text-2xl font-semibold"><?= e((string) $totalMessages) ?></p>
    <p class="text-xs text-red-500">Unread: <?= e((string) $unreadMessages) ?></p>
  </div>
  <div class="bg-white rounded-2xl p-5 shadow">
    <p class="text-sm text-slate-500">Skills</p>
    <p class="text-2xl font-semibold"><?= e((string) $totalSkills) ?></p>
  </div>
  <div class="bg-white rounded-2xl p-5 shadow">
    <p class="text-sm text-slate-500">Last CV Update</p>
    <p class="text-lg font-semibold"><?= e($lastCv ?: 'N/A') ?></p>
  </div>
</div>

<div class="bg-white rounded-2xl p-6 shadow">
  <h2 class="text-lg font-semibold mb-4">Recent Messages</h2>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2">Sender</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentMessages as $message): ?>
          <tr class="border-t">
            <td class="py-2"><?= e($message['sender_name']) ?></td>
            <td><?= e($message['sender_email']) ?></td>
            <td><?= e($message['subject']) ?></td>
            <td><?= e($message['received_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
