<?php
session_start();
require_once '../includes/database.php';

// Check if user is logged in and is a super_admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();

// Fetch dashboard stats
$total_users = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$total_payments = $db->query('SELECT SUM(amount) FROM payments WHERE status = "approved"')->fetchColumn();
$pending_approvals = $db->query('SELECT COUNT(*) FROM payments WHERE status = "pending"')->fetchColumn();
$total_associations = $db->query('SELECT COUNT(*) FROM associations')->fetchColumn();

// Fetch recent activity (this is a simplified example)
$recent_activities = $db->query('SELECT u.full_name, "Payment Upload" as action, p.description, p.created_at FROM payments p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 3')->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Royal Ambassadors</title>

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
                <a href="dashboard.php" class="active"><i class="ri-dashboard-line"></i> Dashboard</a>

                <div class="nav-title">Exam Management</div>
                <a href="exam-management.php"><i class="ri-file-list-3-line"></i> All Exams</a>
                <a href="create_exam.php"><i class="ri-add-box-line"></i> Exam Creation</a>
                <a href="exam-questions.php"><i class="ri-question-answer-line"></i> Question Bank</a>

                <div class="nav-title">Finance & Ledger</div>
                <a href="finance_dashboard.php"><i class="ri-bank-card-line"></i> Finance Dashboard</a>
                <a href="finance_oversight.php"><i class="ri-pie-chart-2-line"></i> Finance Oversight</a>
                <a href="voucher-management.php"><i class="ri-coupon-3-line"></i> Voucher Management</a>

                <div class="nav-title">User Management</div>
                <a href="user-management.php"><i class="ri-team-line"></i> All Users</a>
                <a href="camp_files.php"><i class="ri-file-excel-2-line"></i> Camp Registrations</a>

                <div class="nav-title">Content Management</div>
                <a href="blog-management.php"><i class="ri-article-line"></i> Blog</a>
                <a href="gallery-management.php"><i class="ri-gallery-line"></i> Gallery</a>

                <div class="nav-title">System</div>
                <a href="notification-management.php"><i class="ri-notification-3-line"></i> Notifications</a>
                 <a href="ads-management.php"><i class="ri-advertisement-line"></i> Ads Management</a>
                <a href="system_settings.php"><i class="ri-settings-3-line"></i> System Settings</a>
                <a href="settings.php"><i class="ri-user-settings-line"></i> Profile Settings</a>
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
                    <h1 class="page-title">System Overview</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/pro.jpg" alt="User Avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <span class="user-role">System Control</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Stats Grid -->
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                    <div class="stat-card">
                        <div class="value" style="color: var(--accent-2);"><?php echo $total_users; ?></div>
                        <div class="label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--success);">₦<?php echo number_format($total_payments, 2); ?></div>
                        <div class="label">Total Approved Payments</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--danger);"><?php echo $pending_approvals; ?></div>
                        <div class="label">Pending Approvals</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--gold);"><?php echo $total_associations; ?></div>
                        <div class="label">Total Associations</div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dash-card" style="margin-top: 24px;">
                    <div class="dash-card-header">
                        <h3>Recent System Activity</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activities as $activity): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($activity['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['description']); ?></td>
                                    <td><?php echo date('Y-m-d h:i A', strtotime($activity['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
