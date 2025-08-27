<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'president') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare('SELECT association_id FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$association_id = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];

    $insert_stmt = $db->prepare('INSERT INTO notifications (sender_id, title, message, recipient_type, recipient_id) VALUES (?, ?, ?, ?, ?)');
    $insert_stmt->execute([$user_id, $title, $message, 'association', $association_id]);

    header('Location: notifications.php');
    exit;
}

$history_stmt = $db->prepare("SELECT * FROM notifications WHERE sender_id = ? ORDER BY created_at DESC");
$history_stmt->execute([$user_id]);
$history = $history_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Royal Ambassadors</title>

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
                <div class="logo-container"><img src="../assets/images/logo.png" alt="Logo" class="logo-img"></div>
                <div class="title">President Portal</div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
                <a href="manage_ambassadors.php"><i class="ri-team-line"></i> Manage Ambassadors</a>
                <a href="exam_approvals.php"><i class="ri-checkbox-multiple-line"></i> Exam Approvals</a>
                <a href="camp_registrations.php"><i class="ri-quill-pen-line"></i> Camp Registrations</a>
                <a href="payments_upload.php"><i class="ri-upload-cloud-2-line"></i> Upload Payments</a>
                <a href="notifications.php" class="active"><i class="ri-notification-3-line"></i> Notifications</a>
                <a href="profile_settings.php"><i class="ri-user-settings-line"></i> Profile Settings</a>
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
                    <h1 class="page-title">Send Notifications</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/director.jpg" alt="User Avatar">
                        <div class="user-info">
                            <span class="user-name">Pres. <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <span class="user-role"></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Compose Card -->
                <div class="dash-card" style="margin-bottom: 24px;">
                    <div class="dash-card-header">
                        <h3>Compose a New Notification</h3>
                    </div>
                    <div class="dash-card-content">
                        <p style="color: var(--muted); margin-bottom: 16px;">This message will be sent to all ambassadors in your association.</p>
                        <form class="dash-form" method="POST" action="notifications.php">
                            <div class="form-group">
                                <label for="title">Notification Title</label>
                                <input type="text" id="title" name="title" class="form-control" placeholder="e.g., Upcoming Meeting" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" class="form-control" rows="5" placeholder="Enter your message here..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="ri-send-plane-2-line"></i> Send Notification</button>
                        </form>
                    </div>
                </div>

                <!-- History Card -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Sent Notifications History</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date Sent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($history as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($item['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
