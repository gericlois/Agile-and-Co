<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$unreadCount = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();

// Stats
$totalActivities = $pdo->query("SELECT COUNT(*) FROM activity_log")->fetchColumn();
$todayActivities = $pdo->query("SELECT COUNT(*) FROM activity_log WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$uniqueAdmins = $pdo->query("SELECT COUNT(DISTINCT admin_username) FROM activity_log")->fetchColumn();
$latestAction = $pdo->query("SELECT created_at FROM activity_log ORDER BY created_at DESC LIMIT 1")->fetchColumn();

// Handle clear history
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_history'])) {
    $pdo->exec("DELETE FROM activity_log");
    logActivity($pdo, 'clear', 'history', 'Cleared all activity history');
    header('Location: history.php');
    exit;
}

// Filters
$filterAction = trim($_GET['action'] ?? '');
$filterEntity = trim($_GET['entity'] ?? '');
$filterAdmin = trim($_GET['admin'] ?? '');
$filterDate = trim($_GET['date'] ?? '');

// Build query
$where = [];
$params = [];

if ($filterAction) {
    $where[] = "action = ?";
    $params[] = $filterAction;
}
if ($filterEntity) {
    $where[] = "entity_type = ?";
    $params[] = $filterEntity;
}
if ($filterAdmin) {
    $where[] = "admin_username = ?";
    $params[] = $filterAdmin;
}
if ($filterDate) {
    $where[] = "DATE(created_at) = ?";
    $params[] = $filterDate;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Pagination
$perPage = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM activity_log $whereSQL");
$totalStmt->execute($params);
$totalRows = $totalStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM activity_log $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Get distinct values for filter dropdowns
$actions = $pdo->query("SELECT DISTINCT action FROM activity_log ORDER BY action")->fetchAll(PDO::FETCH_COLUMN);
$entities = $pdo->query("SELECT DISTINCT entity_type FROM activity_log ORDER BY entity_type")->fetchAll(PDO::FETCH_COLUMN);
$admins = $pdo->query("SELECT DISTINCT admin_username FROM activity_log ORDER BY admin_username")->fetchAll(PDO::FETCH_COLUMN);

// Build current query string for pagination links
function buildQuery($overrides = []) {
    $params = array_merge($_GET, $overrides);
    unset($params['']);
    return http_build_query($params);
}

// Action label colors
function actionColor($action) {
    switch ($action) {
        case 'login': case 'create': case 'publish': return 'status-unread';
        case 'delete': case 'clear': return 'status-draft';
        case 'logout': return 'status-read';
        default: return 'status-read';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity History | Agile & Co Admin</title>
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
                <a href="history.php" class="admin-nav-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    History
                </a>
                <a href="users.php" class="admin-nav-item">
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
                <h1>Activity History</h1>
                <p>Track all admin actions and changes</p>
            </div>

            <div class="admin-stats">
                <div class="admin-stat-card">
                    <div class="admin-stat-number"><?= $totalActivities ?></div>
                    <div class="admin-stat-label">Total Activities</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-number"><?= $todayActivities ?></div>
                    <div class="admin-stat-label">Today</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-number"><?= $uniqueAdmins ?></div>
                    <div class="admin-stat-label">Active Admins</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-number" style="font-size: 16px; line-height: 2.2;"><?= $latestAction ? date('M j, g:i A', strtotime($latestAction)) : '—' ?></div>
                    <div class="admin-stat-label">Last Activity</div>
                </div>
            </div>

            <div class="admin-section">
                <div class="admin-section-header">
                    <h2>Activity Log</h2>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <?php if ($totalActivities > 0): ?>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to clear all activity history? This cannot be undone.');" style="margin: 0;">
                            <button type="submit" name="clear_history" class="btn-small btn-danger" style="cursor: pointer; border: 1px solid rgba(255,107,107,0.3);">Clear All</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Filters -->
                <div style="padding: 16px 24px; border-bottom: 1px solid var(--gray-700);">
                    <form method="GET" class="history-filter-form">
                        <select name="action" class="admin-select" onchange="this.form.submit()">
                            <option value="">All Actions</option>
                            <?php foreach ($actions as $a): ?>
                                <option value="<?= htmlspecialchars($a) ?>" <?= $filterAction === $a ? 'selected' : '' ?>><?= ucfirst(htmlspecialchars($a)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="entity" class="admin-select" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <?php foreach ($entities as $e): ?>
                                <option value="<?= htmlspecialchars($e) ?>" <?= $filterEntity === $e ? 'selected' : '' ?>><?= ucfirst(htmlspecialchars($e)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="admin" class="admin-select" onchange="this.form.submit()">
                            <option value="">All Admins</option>
                            <?php foreach ($admins as $adm): ?>
                                <option value="<?= htmlspecialchars($adm) ?>" <?= $filterAdmin === $adm ? 'selected' : '' ?>><?= htmlspecialchars($adm) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="date" name="date" class="admin-select" value="<?= htmlspecialchars($filterDate) ?>" onchange="this.form.submit()" style="color-scheme: dark;">
                        <?php if ($filterAction || $filterEntity || $filterAdmin || $filterDate): ?>
                            <a href="history.php" class="btn-small">Clear Filters</a>
                        <?php endif; ?>
                    </form>
                </div>

                <?php if (empty($logs)): ?>
                    <div class="admin-empty">
                        <p>No activity logged yet.</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td style="white-space: nowrap;"><?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></td>
                                    <td><strong><?= htmlspecialchars($log['admin_username']) ?></strong></td>
                                    <td><span class="<?= actionColor($log['action']) ?>"><?= ucfirst(htmlspecialchars($log['action'])) ?></span></td>
                                    <td><span class="admin-tag"><?= ucfirst(htmlspecialchars($log['entity_type'])) ?></span></td>
                                    <td style="color: var(--gray-300);"><?= htmlspecialchars($log['entity_label']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="admin-pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?= buildQuery(['page' => $page - 1]) ?>" class="btn-small">&larr; Prev</a>
                <?php endif; ?>
                <span class="admin-pagination-info">Page <?= $page ?> of <?= $totalPages ?></span>
                <?php if ($page < $totalPages): ?>
                    <a href="?<?= buildQuery(['page' => $page + 1]) ?>" class="btn-small">Next &rarr;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
