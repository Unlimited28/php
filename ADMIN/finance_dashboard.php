<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();

// Stats
$pending_payments = $db->query('SELECT SUM(amount) FROM payments WHERE status = "pending"')->fetchColumn();
$approved_this_month = $db->query('SELECT SUM(amount) FROM payments WHERE status = "approved" AND MONTH(verified_at) = MONTH(CURRENT_DATE()) AND YEAR(verified_at) = YEAR(CURRENT_DATE())')->fetchColumn();
$total_approved = $db->query('SELECT SUM(amount) FROM payments WHERE status = "approved"')->fetchColumn();

// Pending payments list
$pending_list = $db->query('SELECT p.*, u.full_name, a.name as association_name FROM payments p JOIN users u ON p.user_id = u.id LEFT JOIN associations a ON u.association_id = a.id WHERE p.status = "pending" ORDER BY p.created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard - Super Admin</title>

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
                <a href="finance_dashboard.php" class="active"><i class="ri-bank-card-line"></i> Finance Dashboard</a>
                <a href="voucher-management.php"><i class="ri-coupon-3-line"></i> Voucher Management</a>
                <a href="camp_files.php"><i class="ri-file-excel-2-line"></i> Camp Files</a>

                <div class="nav-title">Content</div>
                <a href="exam-management.php"><i class="ri-file-list-3-line"></i> Exam Management</a>
                <a href="blog-management.php"><i class="ri-article-line"></i> Blog Management</a>
                <a href="gallery-management.php"><i class="ri-gallery-line"></i> Gallery Management</a>

                <div class="nav-title">Marketing</div>
                <a href="ads-management.php"><i class="ri-advertisement-line"></i> Ads Management</a>

                <div class="nav-title">System</div>
                <a href="notification-management.php"><i class="ri-notification-3-line"></i> Notifications</a>
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
                    <h1 class="page-title">Finance Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/pro.jpg" alt="User Avatar">
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Stats Grid -->
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-bottom: 24px;">
                    <div class="stat-card">
                        <div class="value" style="color: var(--danger);">₦<?php echo number_format($pending_payments ?? 0, 2); ?></div>
                        <div class="label">Pending Payments</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--success);">₦<?php echo number_format($approved_this_month ?? 0, 2); ?></div>
                        <div class="label">Approved This Month</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" style="color: var(--gold);">₦<?php echo number_format($total_approved ?? 0, 2); ?></div>
                        <div class="label">Total Approved (All Time)</div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Pending Payment Verifications</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Association</th>
                                    <th>Purpose</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_list as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['association_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($payment['type'])); ?></td>
                                    <td>₦<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($payment['created_at'])); ?></td>
                                    <td>
                                        <a href="../<?php echo htmlspecialchars($payment['receipt_path']); ?>" target="_blank" class="btn btn-secondary btn-sm">View Receipt</a>
                                        <a href="approve_payment.php?id=<?php echo $payment['id']; ?>" class="btn btn-primary btn-sm" style="background: var(--success);">Approve</a>
                                        <a href="reject_payment.php?id=<?php echo $payment['id']; ?>" class="btn btn-secondary btn-sm" style="background: var(--danger); color: white;">Reject</a>
                                    </td>
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
