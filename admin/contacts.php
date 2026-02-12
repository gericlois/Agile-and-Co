<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Send reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply') {
    $contactId = (int)($_POST['contact_id'] ?? 0);
    $replyTo = trim($_POST['reply_to'] ?? '');
    $replySubject = trim($_POST['reply_subject'] ?? '');
    $replyBody = trim($_POST['reply_body'] ?? '');

    if (empty($replyTo) || empty($replySubject) || empty($replyBody)) {
        $error = 'All reply fields are required.';
    } else {
        $adminEmail = 'noreply@agileandco.com';
        try {
            $adminEmail = $pdo->query("SELECT email FROM notification_emails WHERE is_active = 1 LIMIT 1")->fetchColumn() ?: $adminEmail;
        } catch (Exception $e) {}

        $headers = "From: Agile & Co <" . $adminEmail . ">\r\n";
        $headers .= "Reply-To: " . $adminEmail . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $sent = @mail($replyTo, $replySubject, $replyBody, $headers);
        $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?")->execute([$contactId]);
        logActivity($pdo, 'reply', 'contact', 'Replied to: ' . $replyTo);
        $success = $sent ? 'Reply sent to ' . htmlspecialchars($replyTo) : 'Reply queued (check mail config if not delivered).';
    }
}

// Mark as read
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $nameVal = $stmt->fetchColumn();
    $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?")->execute([$id]);
    logActivity($pdo, 'update', 'contact', 'Marked contact as read: ' . ($nameVal ?: '#'.$id));
    header('Location: contacts.php');
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $nameVal = $stmt->fetchColumn();
    $pdo->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
    logActivity($pdo, 'delete', 'contact', 'Deleted contact: ' . ($nameVal ?: '#'.$id));
    header('Location: contacts.php');
    exit;
}

// View single contact
$viewContact = null;
if (isset($_GET['view'])) {
    $viewId = (int)$_GET['view'];
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$viewId]);
    $viewContact = $stmt->fetch();
    if ($viewContact && !$viewContact['is_read']) {
        $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?")->execute([$viewId]);
        $viewContact['is_read'] = 1;
    }
}

// Search, sort, pagination, score filter
$search = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$scoreFilter = $_GET['score'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];
if ($search !== '') {
    $where = "WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR company LIKE ? OR message LIKE ?)";
    $like = '%' . $search . '%';
    $params = [$like, $like, $like, $like, $like];
}
if ($scoreFilter === 'unscored') {
    $where .= ($where ? ' AND' : 'WHERE') . ' lead_score IS NULL';
} elseif (in_array($scoreFilter, ['hot', 'warm', 'cold'])) {
    $where .= ($where ? ' AND' : 'WHERE') . ' lead_score = ?';
    $params[] = $scoreFilter;
}

$orderBy = $sort === 'oldest' ? 'created_at ASC' : 'created_at DESC';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM contacts $where");
$countStmt->execute($params);
$totalContacts = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalContacts / $perPage));

$stmt = $pdo->prepare("SELECT * FROM contacts $where ORDER BY $orderBy LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$contacts = $stmt->fetchAll();

$unreadCount = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();
$totalAll = (int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$hotCount = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE lead_score = 'hot'")->fetchColumn();

function buildUrl($overrides = []) {
    $params = $_GET;
    foreach ($overrides as $k => $v) { $params[$k] = $v; }
    unset($params['view']);
    return 'contacts.php?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts | Agile & Co Admin</title>
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
                <a href="contacts.php" class="admin-nav-item active">
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

        <?php if ($viewContact): ?>
            <!-- SINGLE CONTACT VIEW -->
            <div class="admin-header">
                <div>
                    <a href="contacts.php?<?= http_build_query(array_diff_key($_GET, ['view' => ''])) ?>" style="color: var(--gray-400); font-size: 13px; text-decoration: none;">&larr; Back to contacts</a>
                    <h1 style="margin-top: 8px;"><?= htmlspecialchars($viewContact['first_name'] . ' ' . $viewContact['last_name']) ?></h1>
                    <p><?= htmlspecialchars($viewContact['email']) ?> &middot; <?= date('M j, Y g:i A', strtotime($viewContact['created_at'])) ?></p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="contacts.php?delete=<?= $viewContact['id'] ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px; color: #ff6b6b; border-color: rgba(255,107,107,0.3);" onclick="return confirm('Delete this contact?')">Delete</a>
                </div>
            </div>

            <?php if ($success): ?>
                <div style="background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(76, 201, 240, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 24px; color: var(--accent); font-size: 14px;"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 24px; color: #ff6b6b; font-size: 14px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="admin-section">
                <div class="admin-section-header"><h2>Contact Details</h2></div>
                <div style="padding: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-bottom: 4px;">Name</div>
                        <div style="font-size: 15px; color: var(--white);"><?= htmlspecialchars($viewContact['first_name'] . ' ' . $viewContact['last_name']) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-bottom: 4px;">Email</div>
                        <div style="font-size: 15px;"><a href="mailto:<?= htmlspecialchars($viewContact['email']) ?>" style="color: var(--accent);"><?= htmlspecialchars($viewContact['email']) ?></a></div>
                    </div>
                    <?php if ($viewContact['phone']): ?>
                    <div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-bottom: 4px;">Phone</div>
                        <div style="font-size: 15px; color: var(--white);"><?= htmlspecialchars($viewContact['phone']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($viewContact['company']): ?>
                    <div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-bottom: 4px;">Company</div>
                        <div style="font-size: 15px; color: var(--white);"><?= htmlspecialchars($viewContact['company']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($viewContact['industry']): ?>
                    <div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-bottom: 4px;">Industry</div>
                        <div style="font-size: 15px; color: var(--white);"><?= htmlspecialchars($viewContact['industry']) ?></div>
                    </div>
                    <?php endif; ?>
                    <div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-bottom: 4px;">Submitted</div>
                        <div style="font-size: 15px; color: var(--white);"><?= date('F j, Y \a\t g:i A', strtotime($viewContact['created_at'])) ?></div>
                    </div>
                    <?php
                    if (!empty($viewContact['custom_fields'])):
                        $cf = json_decode($viewContact['custom_fields'], true);
                        if ($cf):
                            foreach ($cf as $cfKey => $cfVal):
                                if ($cfVal !== ''):
                    ?>
                    <div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-bottom: 4px;"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $cfKey))) ?></div>
                        <div style="font-size: 15px; color: var(--white);"><?= htmlspecialchars($cfVal) ?></div>
                    </div>
                    <?php endif; endforeach; endif; endif; ?>
                </div>
                <?php if ($viewContact['message']): ?>
                <div style="padding: 0 24px 24px;">
                    <div style="font-size: 12px; color: var(--gray-500); margin-bottom: 8px;">Message</div>
                    <div style="background: var(--gray-900); border-radius: 8px; padding: 16px; color: var(--gray-300); font-size: 14px; line-height: 1.7;"><?= nl2br(htmlspecialchars($viewContact['message'])) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Lead Score -->
            <div class="admin-section">
                <div class="admin-section-header">
                    <h2>Lead Score</h2>
                    <button type="button" id="rescore-btn" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;" data-contact-id="<?= $viewContact['id'] ?>">
                        <?= $viewContact['lead_score'] ? 'Re-Score' : 'Score Now' ?>
                    </button>
                </div>
                <div style="padding: 24px;" id="lead-score-display">
                    <?php if ($viewContact['lead_score']): ?>
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                            <span class="status-score-<?= $viewContact['lead_score'] ?>" style="font-size: 14px; padding: 6px 16px;">
                                <?= ucfirst($viewContact['lead_score']) ?>
                            </span>
                            <?php if ($viewContact['lead_scored_at']): ?>
                                <span style="font-size: 12px; color: var(--gray-500);">Scored <?= date('M j, Y g:i A', strtotime($viewContact['lead_scored_at'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($viewContact['lead_score_reason']): ?>
                            <div style="font-size: 14px; color: var(--gray-300); line-height: 1.7; background: var(--gray-900); border-radius: 8px; padding: 16px;"><?= htmlspecialchars($viewContact['lead_score_reason']) ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style="color: var(--gray-500); font-size: 14px;" id="no-score-msg">This contact has not been scored yet. Click "Score Now" to analyze with AI.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reply Form -->
            <div class="admin-section">
                <div class="admin-section-header"><h2>Reply</h2></div>
                <form method="POST" action="contacts.php?view=<?= $viewContact['id'] ?>" style="padding: 24px;">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="contact_id" value="<?= $viewContact['id'] ?>">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; color: var(--gray-400); margin-bottom: 6px;">To</label>
                        <input type="email" name="reply_to" value="<?= htmlspecialchars($viewContact['email']) ?>" readonly style="width: 100%; padding: 10px 12px; background: var(--gray-900); border: 1px solid var(--gray-700); border-radius: 8px; color: var(--gray-400); font-size: 14px; font-family: var(--font-body);">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; color: var(--gray-400); margin-bottom: 6px;">Subject</label>
                        <input type="text" name="reply_subject" required value="Re: Your inquiry to Agile & Co" style="width: 100%; padding: 10px 12px; background: var(--gray-900); border: 1px solid var(--gray-700); border-radius: 8px; color: var(--white); font-size: 14px; font-family: var(--font-body);">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; color: var(--gray-400); margin-bottom: 6px;">Message</label>
                        <textarea name="reply_body" required rows="8" style="width: 100%; padding: 12px; background: var(--gray-900); border: 1px solid var(--gray-700); border-radius: 8px; color: var(--white); font-size: 14px; font-family: var(--font-body); resize: vertical;">Hi <?= htmlspecialchars($viewContact['first_name']) ?>,

Thank you for reaching out to Agile & Co.



Best regards,
Agile & Co Team</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;">Send Reply</button>
                </form>
            </div>

        <?php else: ?>
            <!-- CONTACTS LIST VIEW -->
            <div class="admin-header">
                <div>
                    <h1>Contact Submissions</h1>
                    <p><?= $totalAll ?> total &middot; <?= $unreadCount ?> unread</p>
                </div>
            </div>

            <div class="admin-stats">
                <div class="admin-stat-card">
                    <div class="admin-stat-number"><?= $totalAll ?></div>
                    <div class="admin-stat-label">Total Contacts</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-number" style="color: #ff6b6b;"><?= $unreadCount ?></div>
                    <div class="admin-stat-label">Unread</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-number" style="color: #ff8c42;"><?= $hotCount ?></div>
                    <div class="admin-stat-label">Hot Leads</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-number" style="color: #51cf66;"><?= $totalAll - $unreadCount ?></div>
                    <div class="admin-stat-label">Read</div>
                </div>
            </div>

            <div class="admin-section">
                <div class="admin-section-header">
                    <h2>All Contacts</h2>
                    <form method="GET" style="display: flex; gap: 8px; align-items: center;">
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search name, email..." style="padding: 8px 12px; background: var(--gray-900); border: 1px solid var(--gray-700); border-radius: 8px; color: var(--white); font-size: 13px; font-family: var(--font-body); width: 200px;">
                        <select name="score" class="admin-select" onchange="this.form.submit()">
                            <option value="" <?= $scoreFilter === '' ? 'selected' : '' ?>>All Scores</option>
                            <option value="hot" <?= $scoreFilter === 'hot' ? 'selected' : '' ?>>Hot</option>
                            <option value="warm" <?= $scoreFilter === 'warm' ? 'selected' : '' ?>>Warm</option>
                            <option value="cold" <?= $scoreFilter === 'cold' ? 'selected' : '' ?>>Cold</option>
                            <option value="unscored" <?= $scoreFilter === 'unscored' ? 'selected' : '' ?>>Unscored</option>
                        </select>
                        <select name="sort" class="admin-select" onchange="this.form.submit()">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest first</option>
                            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest first</option>
                        </select>
                        <button type="submit" class="btn btn-secondary" style="padding: 8px 14px; font-size: 13px;">Search</button>
                        <?php if ($search): ?>
                            <a href="contacts.php" class="btn-small">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <?php if (empty($contacts)): ?>
                    <p style="color: var(--gray-400); padding: 24px;"><?= $search ? 'No contacts found for "' . htmlspecialchars($search) . '".' : 'No contact submissions yet.' ?></p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 30px;"></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Industry</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $c): ?>
                                <tr style="<?= !$c['is_read'] ? 'background: rgba(76, 201, 240, 0.03);' : '' ?>">
                                    <td>
                                        <?php if (!$c['is_read']): ?>
                                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--accent);"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong style="<?= !$c['is_read'] ? 'color: var(--white);' : '' ?>"><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></strong></td>
                                    <td style="color: var(--gray-400); font-size: 13px;"><?= htmlspecialchars($c['email']) ?></td>
                                    <td style="color: var(--gray-400); font-size: 13px;"><?= htmlspecialchars($c['industry'] ?: '-') ?></td>
                                    <td style="color: var(--gray-400); font-size: 13px; white-space: nowrap;"><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
                                    <td>
                                        <?= $c['is_read']
                                            ? '<span class="status-read">Read</span>'
                                            : '<span class="status-unread">New</span>' ?>
                                    </td>
                                    <td>
                                        <?php if ($c['lead_score']): ?>
                                            <span class="status-score-<?= $c['lead_score'] ?>"><?= ucfirst($c['lead_score']) ?></span>
                                        <?php else: ?>
                                            <span style="color: var(--gray-500); font-size: 12px;">--</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;" class="admin-actions">
                                        <a href="contacts.php?view=<?= $c['id'] ?><?= $search ? '&q=' . urlencode($search) : '' ?><?= $sort !== 'newest' ? '&sort=' . $sort : '' ?><?= $scoreFilter ? '&score=' . urlencode($scoreFilter) : '' ?><?= $page > 1 ? '&page=' . $page : '' ?>" class="btn-small" style="background: var(--accent); color: #000;">View</a>
                                        <a href="contacts.php?delete=<?= $c['id'] ?>" class="btn-small btn-danger" onclick="return confirm('Delete this contact?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($totalPages > 1): ?>
                    <div class="admin-pagination">
                        <?php if ($page > 1): ?>
                            <a href="<?= buildUrl(['page' => $page - 1]) ?>" class="btn-small">&laquo; Prev</a>
                        <?php endif; ?>
                        <span class="admin-pagination-info">Page <?= $page ?> of <?= $totalPages ?> (<?= $totalContacts ?> result<?= $totalContacts !== 1 ? 's' : '' ?>)</span>
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= buildUrl(['page' => $page + 1]) ?>" class="btn-small">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </main>
    </div>
<script>
document.getElementById('rescore-btn')?.addEventListener('click', function() {
    const btn = this;
    const contactId = btn.dataset.contactId;
    const display = document.getElementById('lead-score-display');

    btn.disabled = true;
    btn.textContent = 'Scoring...';

    fetch('../api/lead-score.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ contact_id: parseInt(contactId) })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            alert('Scoring failed: ' + data.error);
            btn.disabled = false;
            btn.textContent = 'Retry';
            return;
        }

        const scoreColors = { hot: '#ff8c42', warm: '#ffd43b', cold: '#4cc9f0' };
        const bgColors = { hot: 'rgba(255, 140, 66, 0.15)', warm: 'rgba(255, 212, 59, 0.15)', cold: 'rgba(76, 201, 240, 0.15)' };

        display.innerHTML = '<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">'
            + '<span style="font-size: 14px; padding: 6px 16px; border-radius: 100px; font-weight: 600; background: ' + bgColors[data.score] + '; color: ' + scoreColors[data.score] + ';">' + data.score.charAt(0).toUpperCase() + data.score.slice(1) + '</span>'
            + '<span style="font-size: 12px; color: var(--gray-500);">Just now</span>'
            + '</div>'
            + (data.reason ? '<div style="font-size: 14px; color: var(--gray-300); line-height: 1.7; background: var(--gray-900); border-radius: 8px; padding: 16px;">' + data.reason + '</div>' : '');

        btn.disabled = false;
        btn.textContent = 'Re-Score';
    })
    .catch(() => {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Retry';
    });
});
</script>
</body>
</html>
