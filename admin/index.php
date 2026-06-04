<?php
require_once __DIR__ . '/config.php';

if (is_admin_logged_in()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT user_pass FROM wp_users WHERE user_login=? AND ID=1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $hash = $stmt->get_result()->fetch_column();

    // Strip $wp$ prefix if present
    if (str_starts_with($hash, '$wp$')) {
        $hash = '$' . substr($hash, 4);
    }

    if ($hash && password_verify($password, $hash)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: /admin/dashboard.php');
        exit;
    }
    $error = 'Invalid credentials';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripleT Admin — Login</title>
    <link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="login-page">
<div class="login-card">
    <h1>🔐 TripleT Admin</h1>
    <p class="sub">Tung Tung Tung Sahur</p>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
