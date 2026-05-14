<?php
require_once __DIR__ . '/../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            redirect('index.php');
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center text-white">
  <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-2xl p-8 shadow-lg">
    <h1 class="text-2xl font-semibold mb-6">Admin Login</h1>
    <?php if ($error): ?>
      <div class="mb-4 text-red-300 text-sm"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="text" name="username" placeholder="Username" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/10">
      <input type="password" name="password" placeholder="Password" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/10">
      <button type="submit" class="w-full px-4 py-3 bg-teal-500 text-black font-semibold rounded-lg">Sign In</button>
    </form>
  </div>
</body>
</html>
