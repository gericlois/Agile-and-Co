<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
]);
require_once '../config/database.php';
require_once '../config/activity-log.php';
require_once '../config/csrf.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Brute force protection: max 5 attempts per 15 minutes
if (!isset($_SESSION['login_attempts'])) { $_SESSION['login_attempts'] = 0; }
if (!isset($_SESSION['login_lockout'])) { $_SESSION['login_lockout'] = 0; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Invalid security token. Please try again.';
    // Check lockout
    } elseif ($_SESSION['login_attempts'] >= 5 && time() < $_SESSION['login_lockout']) {
        $remaining = ceil(($_SESSION['login_lockout'] - time()) / 60);
        $error = 'Too many failed attempts. Please wait ' . $remaining . ' minute(s).';
    } else {
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_attempts'] = 0; // Reset after lockout expires
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['login_attempts'] = 0;
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            logActivity($pdo, 'login', 'session', 'Admin logged in');
            header('Location: dashboard.php');
            exit;
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['login_lockout'] = time() + 900; // 15 min lockout
            }
            $error = 'Invalid username or password.';
        }
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
        .password-wrapper { position: relative; }
        .password-wrapper input { width: 100%; padding-right: 60px; }
        .toggle-password { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-400); cursor: pointer; font-size: 13px; font-family: inherit; }
        .toggle-password:hover { color: var(--white); }
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
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" required>
                        <button type="button" class="toggle-password" onclick="var p=document.getElementById('password');var s=this.querySelector('span');if(p.type==='password'){p.type='text';s.textContent='Hide';}else{p.type='password';s.textContent='Show';}"><span>Show</span></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary form-submit">Log In →</button>
            </form>
        </div>
    </div>
</body>
</html>
