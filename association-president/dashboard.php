<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'president') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare('SELECT u.*, a.name as association_name FROM users u JOIN associations a ON u.association_id = a.id WHERE u.id = ?');
$stmt->execute([$user_id]);
$president = $stmt->fetch();
$association_id = $president['association_id'];

// Stats
$total_ambassadors = $db->prepare('SELECT COUNT(*) FROM users WHERE association_id = ? AND role = "ambassador"');
$total_ambassadors->execute([$association_id]);
$total_ambassadors_count = $total_ambassadors->fetchColumn();

// This is a placeholder for a more complex query
$pending_approvals = 0;
$pending_payments = $db->prepare('SELECT COUNT(*) FROM payments p JOIN users u ON p.user_id = u.id WHERE u.association_id = ? AND p.status = "pending"');
$pending_payments->execute([$association_id]);
$pending_payments_count = $pending_payments->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>President Dashboard - Royal Ambassadors</title>

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
                <a href="dashboard.php" class="active"><i class="ri-dashboard-line"></i> Dashboard</a>
                <a href="manage_ambassadors.php"><i class="ri-team-line"></i> Manage Ambassadors</a>
                <a href="exam_approvals.php"><i class="ri-checkbox-multiple-line"></i> Exam Approvals</a>
                <a href="camp_registrations.php"><i class="ri-quill-pen-line"></i> Camp Registrations</a>
                <a href="payments_upload.php"><i class="ri-upload-cloud-2-line"></i> Upload Payments</a>
                <a href="notifications.php"><i class="ri-notification-3-line"></i> Notifications</a>
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
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/director.jpg" alt="User Avatar">
                        <div class="user-info">
                            <span class="user-name">Pres. <?php echo htmlspecialchars($president['full_name']); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($president['association_name']); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Welcome Message -->
                <div class="dash-card" style="margin-bottom: 24px;">
                    <h2>Welcome, President <?php echo htmlspecialchars($president['full_name']); ?>!</h2>
                    <p>Here's an overview of your association's activities.</p>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                    <div class="stat-card">
                        <div class="value" style="color: var(--accent-2);"><?php echo $total_ambassadors_count; ?></div>
                        <div class="label">Total Ambassadors</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--danger);"><?php echo $pending_approvals; ?></div>
                        <div class="label">Pending Approvals</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--gold);"><?php echo $pending_payments_count; ?></div>
                        <div class="label">Pending Payments</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--success);">0</div>
                        <div class="label">Camp Registered</div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dash-card" style="margin-top: 24px;">
                    <div class="dash-card-header">
                        <h3>Pending Actions</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Ambassador Name</th>
                                    <th>Action Required</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Samuel Adekunle</td>
                                    <td>Exam Approval Request</td>
                                    <td>2025-08-19</td>
                                    <td><a href="exam_approvals.php" class="btn btn-secondary btn-sm">View</a></td>
                                </tr>
                                <tr>
                                    <td>David Ojo</td>
                                    <td>New Registration</td>
                                    <td>2025-08-18</td>
                                    <td><a href="manage_ambassadors.php" class="btn btn-secondary btn-sm">View</a></td>
                                </tr>
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
