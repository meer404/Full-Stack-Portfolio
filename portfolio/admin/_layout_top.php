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
<body class="text-slate-800">
  <div class="flex min-h-screen">
    <aside class="sidebar w-64 p-6 text-white transform transition-transform -translate-x-full lg:translate-x-0" data-sidebar>
      <div class="text-xl font-bold mb-8">Portfolio Admin</div>
      <nav class="space-y-3 text-sm">
        <a class="block px-3 py-2 rounded-lg <?= $current === 'index.php' ? 'bg-white/10' : '' ?>" href="index.php">Dashboard</a>
        <a class="block px-3 py-2 rounded-lg <?= $current === 'about.php' ? 'bg-white/10' : '' ?>" href="about.php">About</a>
        <a class="block px-3 py-2 rounded-lg <?= $current === 'resume.php' ? 'bg-white/10' : '' ?>" href="resume.php">Resume</a>
        <a class="block px-3 py-2 rounded-lg <?= $current === 'skills.php' ? 'bg-white/10' : '' ?>" href="skills.php">Skills</a>
        <a class="block px-3 py-2 rounded-lg <?= $current === 'experience.php' ? 'bg-white/10' : '' ?>" href="experience.php">Experience</a>
        <a class="block px-3 py-2 rounded-lg <?= $current === 'education.php' ? 'bg-white/10' : '' ?>" href="education.php">Education</a>
        <a class="block px-3 py-2 rounded-lg <?= $current === 'projects.php' ? 'bg-white/10' : '' ?>" href="projects.php">Projects</a>
        <a class="block px-3 py-2 rounded-lg <?= $current === 'messages.php' ? 'bg-white/10' : '' ?>" href="messages.php">Messages</a>
        <a class="block px-3 py-2 rounded-lg <?= $current === 'settings.php' ? 'bg-white/10' : '' ?>" href="settings.php">Settings</a>
        <a class="block px-3 py-2 rounded-lg text-red-200" href="logout.php">Logout</a>
      </nav>
    </aside>
    <div class="flex-1">
      <header class="flex items-center justify-between px-6 py-4 bg-white shadow-sm">
        <button class="lg:hidden" data-sidebar-toggle>☰</button>
        <h1 class="text-lg font-semibold">Admin Dashboard</h1>
        <div class="text-sm text-slate-500"><?= e($_SESSION['admin_username'] ?? 'admin') ?></div>
      </header>
      <main class="p-6 space-y-8">
