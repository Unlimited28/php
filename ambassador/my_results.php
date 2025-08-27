<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ambassador') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare('
    SELECT er.*, e.title as exam_title
    FROM exam_results er
    JOIN exams e ON er.exam_id = e.id
    WHERE er.user_id = ?
    ORDER BY er.taken_at DESC
');
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Results - Royal Ambassadors</title>

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
                <div class="title">Ambassador Portal</div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
                <a href="my_exams.php"><i class="ri-file-list-3-line"></i> My Exams</a>
                <a href="my_results.php" class="active"><i class="ri-trophy-line"></i> My Results</a>
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
                    <h1 class="page-title">My Exam Results</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/team-1.jpg" alt="User Avatar">
                        <div class="user-info">
                            <span class="user-name">Amb. <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <span class="user-role"></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Your Performance History</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Exam Title</th>
                                    <th>Date Taken</th>
                                    <th>Score</th>
                                    <th>Result</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $result): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($result['exam_title']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($result['taken_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($result['percentage']); ?>%</td>
                                    <td><span class="status status-<?php echo $result['status'] === 'passed' ? 'approved' : 'rejected'; ?>"><?php echo ucfirst($result['status']); ?></span></td>
                                    <td><a href="result_details.php?id=<?php echo $result['id']; ?>" class="btn btn-secondary btn-sm">View Details</a></td>
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
