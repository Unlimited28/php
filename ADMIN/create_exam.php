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
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $total_questions = $_POST['total_questions'];
    $pass_mark = $_POST['pass_score'];
    $created_by = $_SESSION['user_id'];

    $stmt = $db->prepare('INSERT INTO exams (title, description, duration, total_questions, pass_mark, created_by) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$title, $description, $duration, $total_questions, $pass_mark, $created_by]);

    $exam_id = $db->lastInsertId();
    header('Location: exam-questions.php?exam_id=' . $exam_id);
    exit;
}

$ranks = $db->query('SELECT * FROM ranks ORDER BY level')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam - Super Admin</title>

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
                <a href="exam-management.php" class="active"><i class="ri-file-list-3-line"></i> Exam Management</a>
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
                    <h1 class="page-title">Create New Exam</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/pro.jpg" alt="User Avatar">
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Exam Details</h3>
                    </div>
                    <div class="dash-card-content">
                        <form class="dash-form" method="POST" action="create_exam.php">
                            <div class="form-group">
                                <label for="title">Exam Title</label>
                                <input type="text" id="title" name="title" class="form-control" placeholder="e.g., The Book of Romans" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="3" placeholder="A brief description of the exam..."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="rank">Rank Requirement</label>
                                <select id="rank" name="rank_id" class="form-control" required>
                                    <option value="">-- Select Rank --</option>
                                    <?php foreach ($ranks as $rank): ?>
                                    <option value="<?php echo $rank['id']; ?>"><?php echo htmlspecialchars($rank['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="duration">Duration (in minutes)</label>
                                <input type="number" id="duration" name="duration" class="form-control" placeholder="e.g., 30" required>
                            </div>
                            <div class="form-group">
                                <label for="total_questions">Total Questions</label>
                                <input type="number" id="total_questions" name="total_questions" class="form-control" placeholder="e.g., 50" required>
                            </div>
                             <div class="form-group">
                                <label for="pass_score">Pass Score (%)</label>
                                <input type="number" id="pass_score" name="pass_score" class="form-control" placeholder="e.g., 70" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Exam & Add Questions</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
