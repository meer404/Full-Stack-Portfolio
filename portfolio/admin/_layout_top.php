<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/portfolio/assets/css/admin.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="admin-body text-slate-900">
  <div class="flex min-h-screen">
    <aside class="sidebar w-64 p-5 text-white transform transition-transform -translate-x-full lg:translate-x-0" data-sidebar>
      <div class="mb-8">
        <div class="text-lg font-bold text-white">Portfolio Admin</div>
        <div class="text-xs text-white/40 mt-0.5 font-mono">v1.0</div>
      </div>
      <nav class="space-y-1 text-sm">
        <?php
        $navItems = [
          'index.php'      => ['📊', 'Dashboard'],
          'about.php'      => ['👤', 'About'],
          'resume.php'     => ['📄', 'Resume'],
          'skills.php'     => ['⚡', 'Skills'],
          'experience.php' => ['💼', 'Experience'],
          'education.php'  => ['🎓', 'Education'],
          'projects.php'   => ['🚀', 'Projects'],
          'messages.php'   => ['✉️', 'Messages'],
          'settings.php'   => ['⚙️', 'Settings'],
        ];
        foreach ($navItems as $file => [$icon, $label]):
        ?>
          <a class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-white/70 <?= $current === $file ? 'bg-white/10 !text-white' : '' ?>"
             href="<?= $file ?>">
            <span class="text-base"><?= $icon ?></span>
            <?= $label ?>
          </a>
        <?php endforeach; ?>
        <div class="pt-2 border-t border-white/10 mt-2">
          <a class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-red-300/80 hover:text-red-200" href="logout.php">
            <span class="text-base">🚪</span>
            Logout
          </a>
        </div>
      </nav>
    </aside>
    <div class="flex-1 min-w-0">
      <header class="admin-header flex items-center justify-between px-6 py-3.5">
        <button class="lg:hidden p-2 rounded-lg hover:bg-black/10 transition" data-sidebar-toggle>☰</button>
        <h1 class="text-base font-semibold text-slate-800">
          <?= $navItems[$current][1] ?? 'Admin Dashboard' ?>
        </h1>
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-full bg-gradient-to-br from-green-400 to-yellow-400 flex items-center justify-center text-xs font-bold text-black">
            <?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?>
          </div>
          <span class="text-sm text-slate-500 hidden sm:block"><?= e($_SESSION['admin_username'] ?? 'admin') ?></span>
        </div>
      </header>
      <main class="p-6 space-y-8">
