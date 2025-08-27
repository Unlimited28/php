<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'president') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt'])) {
    $purpose = $_POST['purpose'];
    $amount = $_POST['amount'];

    $upload_dir = '../uploads/receipts/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_name = $user_id . '_' . time() . '_' . basename($_FILES['receipt']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['receipt']['tmp_name'], $target_file)) {
        $stmt = $db->prepare('INSERT INTO payments (user_id, type, amount, receipt_path) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user_id, $purpose, $amount, 'uploads/receipts/' . $file_name]);
    }
    header('Location: payments_upload.php');
    exit;
}

$history_stmt = $db->prepare('SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC');
$history_stmt->execute([$user_id]);
$history = $history_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Payments - Royal Ambassadors</title>

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
                <a href="payments_upload.php" class="active"><i class="ri-upload-cloud-2-line"></i> Upload Payments</a>
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
                    <h1 class="page-title">Upload Payments</h1>
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
                <!-- Upload Card -->
                <div class="dash-card" style="margin-bottom: 24px;">
                    <div class="dash-card-header">
                        <h3>Submit a New Payment</h3>
                    </div>
                    <div class="dash-card-content">
                        <form class="dash-form" method="POST" action="payments_upload.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="purpose">Payment Purpose</label>
                                <select id="purpose" name="purpose" class="form-control">
                                    <option value="dues">Annual Dues</option>
                                    <option value="camp">Camp Fees</option>
                                    <option value="exam">Exam Fees</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount (NGN)</label>
                                <input type="number" id="amount" name="amount" class="form-control" placeholder="e.g., 50000" required>
                            </div>
                            <div class="form-group">
                                <label for="receipt">Upload Receipt (Image)</label>
                                <input type="file" id="receipt" name="receipt" class="form-control" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="ri-upload-cloud-line"></i> Upload Receipt</button>
                        </form>
                    </div>
                </div>

                <!-- History Card -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Payment History</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Purpose</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($history as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(ucfirst($payment['type'])); ?></td>
                                    <td>₦<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($payment['created_at'])); ?></td>
                                    <td><span class="status status-<?php echo strtolower($payment['status']); ?>"><?php echo ucfirst($payment['status']); ?></span></td>
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
