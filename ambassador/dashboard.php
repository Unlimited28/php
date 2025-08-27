<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ambassador') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $db->prepare('SELECT u.*, r.name as rank_name FROM users u LEFT JOIN ranks r ON u.rank_id = r.id WHERE u.id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch stats
$approved_exams = $db->prepare('SELECT COUNT(*) FROM exam_results WHERE user_id = ? AND status = "passed"');
$approved_exams->execute([$user_id]);
$approved_exams_count = $approved_exams->fetchColumn();

$avg_score = $db->prepare('SELECT AVG(percentage) FROM exam_results WHERE user_id = ?');
$avg_score->execute([$user_id]);
$average_score = $avg_score->fetchColumn();

// Fetch notifications
$notifications_stmt = $db->prepare("SELECT * FROM notifications WHERE (recipient_type = 'all' OR recipient_type = 'ambassador') AND created_at >= ? ORDER BY created_at DESC LIMIT 5");
$notifications_stmt->execute([$user['created_at']]);
$notifications = $notifications_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambassador Dashboard - Royal Ambassadors</title>

    <!-- Google Fonts & Remix Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Theme & Dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <img src="../assets/images/logo.png" alt="Logo" class="logo-img">
                </div>
                <div class="title">Ambassador Portal</div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active"><i class="ri-dashboard-line"></i> Dashboard</a>
                <a href="my_exams.php"><i class="ri-file-list-3-line"></i> My Exams</a>
                <a href="my_results.php"><i class="ri-trophy-line"></i> My Results</a>
                <a href="profile_settings.php"><i class="ri-user-settings-line"></i> Profile Settings</a>

                <div class="nav-title">Resources</div>
                <a href="../public/gallery.php"><i class="ri-gallery-line"></i> Gallery</a>
                <a href="../public/blog.php"><i class="ri-article-line"></i> Blog</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="ri-logout-box-r-line"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="main-header">
                <div class="header-left">
                    <button class="menu-toggle"><i class="ri-menu-2-line"></i></button>
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name">Amb. <?php echo htmlspecialchars($user['full_name']); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($user['rank_name'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Welcome Message -->
                <div class="dash-card" style="margin-bottom: 24px;">
                    <h2>Welcome, Ambassador <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
                    <p>Your unique ID is <strong style="color: var(--gold);"><?php echo htmlspecialchars($user['unique_id']); ?></strong>. Here is a summary of your journey.</p>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                    <div class="stat-card">
                        <div class="value" style="color: var(--accent-2);"><?php echo $approved_exams_count; ?></div>
                        <div class="label">Approved Exams</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--success);"><?php echo number_format($average_score ?? 0, 1); ?>%</div>
                        <div class="label">Average Score</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--gold);"><?php echo htmlspecialchars($user['rank_name'] ?? 'N/A'); ?></div>
                        <div class="label">Current Rank</div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dash-card" style="margin-top: 24px;">
                    <div class="dash-card-header">
                        <h3>Recent Notifications</h3>
                    </div>
                    <div class="dash-card-content">
                        <ul>
                            <?php foreach($notifications as $notification): ?>
                                <li><?php echo htmlspecialchars($notification['message']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer style="background: linear-gradient(180deg, #0b1530, #0b162f); border-top: 1px solid rgba(255,255,255,.06); padding: 40px 24px 20px; margin-top: 32px;">
                <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
                    <p style="color: var(--muted); font-size: 0.9rem;">&copy; 2025 Royal Ambassadors Ogun Baptist Conference. All rights reserved.</p>
                </div>
            </footer>
        </main>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
