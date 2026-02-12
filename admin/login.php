<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        logActivity($pdo, 'login', 'session', 'Admin logged in');
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Agile & Co</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .admin-login { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--gray-900); }
        .login-box { background: var(--gray-800); border: 1px solid var(--gray-700); border-radius: 20px; padding: 48px; width: 100%; max-width: 420px; }
        .login-box h1 { font-size: 28px; margin-bottom: 8px; text-align: center; }
        .login-box .subtitle { color: var(--gray-400); text-align: center; margin-bottom: 32px; }
        .login-error { background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; color: #ff6b6b; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
    <div class="admin-login">
        <div class="login-box">
            <h1>Agile & Co</h1>
            <p class="subtitle">Admin Panel</p>
            <?php if ($error): ?>
                <div class="login-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary form-submit">Log In →</button>
            </form>
        </div>
    </div>
</body>
</html>
