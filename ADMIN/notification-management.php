<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $recipient_type = $_POST['target_role'];
    $sender_id = $_SESSION['user_id'];

    $stmt = $db->prepare('INSERT INTO notifications (title, message, recipient_type, sender_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $message, $recipient_type, $sender_id]);

    header('Location: notification-management.php');
    exit;
}

$notifications = $db->query('SELECT * FROM notifications ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Management - Super Admin</title>

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
                <div class="title">Super Admin</div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>

                <div class="nav-title">User Management</div>
                <a href="user-management.php"><i class="ri-team-line"></i> Manage Users</a>
                <a href="finance_dashboard.php"><i class="ri-bank-card-line"></i> Finance Dashboard</a>
                <a href="voucher-management.php"><i class="ri-coupon-3-line"></i> Voucher Management</a>
                <a href="camp_files.php"><i class="ri-file-excel-2-line"></i> Camp Files</a>

                <div class="nav-title">Content</div>
                <a href="exam-management.php"><i class="ri-file-list-3-line"></i> Exam Management</a>
                <a href="blog-management.php"><i class="ri-article-line"></i> Blog Management</a>
                <a href="gallery-management.php"><i class="ri-gallery-line"></i> Gallery Management</a>

                <div class="nav-title">Marketing</div>
                <a href="ads-management.php"><i class="ri-advertisement-line"></i> Ads Management</a>

                <div class="nav-title">System</div>
                <a href="notification-management.php" class="active"><i class="ri-notification-3-line"></i> Notifications</a>
                <a href="system_settings.php"><i class="ri-settings-3-line"></i> System Settings</a>
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
                    <h1 class="page-title">Notification Management</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/pro.jpg" alt="User Avatar">
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Compose Card -->
                <div class="dash-card" style="margin-bottom: 24px;">
                    <div class="dash-card-header">
                        <h3>Send a System-Wide Notification</h3>
                    </div>
                    <div class="dash-card-content">
                        <form class="dash-form" method="POST" action="notification-management.php">
                            <div class="form-group">
                                <label for="target_role">Target Audience</label>
                                <select id="target_role" name="target_role" class="form-control">
                                    <option value="all">All Users</option>
                                    <option value="ambassador">Ambassadors Only</option>
                                    <option value="president">Association Presidents Only</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">Notification Title</label>
                                <input type="text" id="title" name="title" class="form-control" placeholder="e.g., System Maintenance" required>
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
                                    <th>Audience</th>
                                    <th>Date Sent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($notifications as $notification): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($notification['title']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $notification['recipient_type']))); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($notification['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
