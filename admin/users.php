<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$currentAdminId = (int)$_SESSION['admin_id'];

// Add new admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_user') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            // Check if username exists
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $hash]);
                logActivity($pdo, 'create', 'user', 'Added admin user: ' . $username);
                $success = 'Admin user "' . htmlspecialchars($username) . '" created successfully.';
            }
        }
    }

    if ($_POST['action'] === 'change_password') {
        $targetId = (int)($_POST['user_id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';
        $confirmNew = $_POST['confirm_new_password'] ?? '';

        if (empty($newPassword)) {
            $error = 'New password is required.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($newPassword !== $confirmNew) {
            $error = 'Passwords do not match.';
        } else {
            $stmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
            $stmt->execute([$targetId]);
            $target = $stmt->fetch();
            if ($target) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $stmt->execute([$hash, $targetId]);
                logActivity($pdo, 'update', 'user', 'Changed password for: ' . $target['username']);
                $success = 'Password updated for "' . htmlspecialchars($target['username']) . '".';
            } else {
                $error = 'User not found.';
            }
        }
    }
}

// Delete admin
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $totalAdmins = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();

    if ($deleteId === $currentAdminId) {
        $error = 'You cannot delete your own account.';
    } elseif ($totalAdmins <= 1) {
        $error = 'Cannot delete the last remaining admin.';
    } else {
        $stmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
        $stmt->execute([$deleteId]);
        $target = $stmt->fetch();
        if ($target) {
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$deleteId]);
            logActivity($pdo, 'delete', 'user', 'Deleted admin user: ' . $target['username']);
            $success = 'Admin user "' . htmlspecialchars($target['username']) . '" deleted.';
        }
    }
}

$admins = $pdo->query("SELECT id, username, created_at FROM admins ORDER BY created_at ASC")->fetchAll();
$totalAdmins = count($admins);
$unreadCount = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Agile & Co Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo"><a href="dashboard.php">Agile & Co</a></div>
            <nav class="admin-nav">
                <a href="dashboard.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>
                <div class="admin-nav-divider"></div>
                <div class="admin-nav-label">Content</div>
                <a href="homepage.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Homepage
                </a>
                <a href="services.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Services
                </a>
                <a href="industries.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    Industries
                </a>
                <a href="core.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    Core
                </a>
                <a href="about.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    About
                </a>
                <a href="contact-page.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    Contact Page
                </a>
                <div class="admin-nav-divider"></div>
                <a href="contacts.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    Contacts
                    <?php if ($unreadCount > 0): ?><span class="admin-badge"><?= $unreadCount ?></span><?php endif; ?>
                </a>
                <a href="posts.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                    Blog Posts
                </a>
                <a href="history.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    History
                </a>
                <a href="users.php" class="admin-nav-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Users
                </a>
                <a href="email-settings.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Email Settings
                </a>
            </nav>
            <div class="admin-sidebar-footer">
                <a href="../index.php" class="admin-nav-item">&larr; View Site</a>
                <a href="logout.php" class="admin-nav-item" style="color: #ff6b6b;">Log Out</a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>User Management</h1>
                    <p><?= $totalAdmins ?> admin<?= $totalAdmins !== 1 ? 's' : '' ?> &middot; Logged in as <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></p>
                </div>
            </div>

            <?php if ($error): ?>
                <div style="background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 24px; color: #ff6b6b; font-size: 14px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(76, 201, 240, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 24px; color: var(--accent); font-size: 14px;"><?= $success ?></div>
            <?php endif; ?>

            <div class="admin-stats" style="grid-template-columns: repeat(2, 1fr);">
                <div class="admin-stat-card">
                    <div class="admin-stat-number"><?= $totalAdmins ?></div>
                    <div class="admin-stat-label">Total Admins</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-number"><?= htmlspecialchars($_SESSION['admin_username']) ?></div>
                    <div class="admin-stat-label">Current Account</div>
                </div>
            </div>

            <!-- Admin List -->
            <div class="admin-section">
                <div class="admin-section-header">
                    <h2>Admin Accounts</h2>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr><th>Username</th><th>Created</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($admin['username']) ?></strong>
                                    <?php if ($admin['id'] === $currentAdminId): ?>
                                        <span style="color: var(--accent); font-size: 12px; margin-left: 8px;">(you)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M j, Y', strtotime($admin['created_at'])) ?></td>
                                <td class="admin-actions">
                                    <button type="button" class="btn-small" onclick="togglePasswordForm(<?= $admin['id'] ?>)">Change Password</button>
                                    <?php if ($admin['id'] !== $currentAdminId && $totalAdmins > 1): ?>
                                        <a href="users.php?delete=<?= $admin['id'] ?>" class="btn-small btn-danger" onclick="return confirm('Delete admin &quot;<?= htmlspecialchars($admin['username']) ?>&quot;? This cannot be undone.')">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr id="pw-form-<?= $admin['id'] ?>" style="display: none;">
                                <td colspan="3" style="padding: 16px 24px; background: var(--gray-900);">
                                    <form method="POST" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                                        <input type="hidden" name="action" value="change_password">
                                        <input type="hidden" name="user_id" value="<?= $admin['id'] ?>">
                                        <div>
                                            <label style="display: block; font-size: 12px; color: var(--gray-400); margin-bottom: 4px;">New Password</label>
                                            <input type="password" name="new_password" required minlength="6" style="padding: 8px 12px; background: var(--gray-800); border: 1px solid var(--gray-700); border-radius: 6px; color: var(--white); font-size: 14px; font-family: var(--font-body);">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 12px; color: var(--gray-400); margin-bottom: 4px;">Confirm Password</label>
                                            <input type="password" name="confirm_new_password" required minlength="6" style="padding: 8px 12px; background: var(--gray-800); border: 1px solid var(--gray-700); border-radius: 6px; color: var(--white); font-size: 14px; font-family: var(--font-body);">
                                        </div>
                                        <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">Update</button>
                                        <button type="button" class="btn-small" onclick="togglePasswordForm(<?= $admin['id'] ?>)">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add New Admin -->
            <div class="admin-section">
                <div class="admin-section-header">
                    <h2>Add New Admin</h2>
                </div>
                <form method="POST" style="padding: 24px;">
                    <input type="hidden" name="action" value="add_user">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; align-items: end;">
                        <div>
                            <label style="display: block; font-size: 13px; color: var(--gray-400); margin-bottom: 6px;">Username *</label>
                            <input type="text" name="username" required placeholder="Enter username" style="width: 100%; padding: 10px 12px; background: var(--gray-900); border: 1px solid var(--gray-700); border-radius: 8px; color: var(--white); font-size: 14px; font-family: var(--font-body);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; color: var(--gray-400); margin-bottom: 6px;">Password *</label>
                            <input type="password" name="password" required minlength="6" placeholder="Min 6 characters" style="width: 100%; padding: 10px 12px; background: var(--gray-900); border: 1px solid var(--gray-700); border-radius: 8px; color: var(--white); font-size: 14px; font-family: var(--font-body);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; color: var(--gray-400); margin-bottom: 6px;">Confirm Password *</label>
                            <input type="password" name="confirm_password" required minlength="6" placeholder="Repeat password" style="width: 100%; padding: 10px 12px; background: var(--gray-900); border: 1px solid var(--gray-700); border-radius: 8px; color: var(--white); font-size: 14px; font-family: var(--font-body);">
                        </div>
                    </div>
                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;">Add Admin</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
    function togglePasswordForm(id) {
        const row = document.getElementById('pw-form-' + id);
        row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    }
    </script>
</body>
</html>
