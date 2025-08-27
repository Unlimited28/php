<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

$exam_id = $_GET['exam_id'] ?? null;
if (!$exam_id) {
    header('Location: exam-management.php');
    exit;
}

$db = get_db_connection();

// Fetch exam details
$stmt = $db->prepare('SELECT * FROM exams WHERE id = ?');
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

if (!$exam) {
    header('Location: exam-management.php');
    exit;
}

// Handle form submission for new question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    $stmt = $db->prepare('INSERT INTO exam_questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$exam_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option]);

    header('Location: exam-questions.php?exam_id=' . $exam_id);
    exit;
}

// Fetch existing questions
$stmt = $db->prepare('SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY id');
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Questions - <?php echo htmlspecialchars($exam['title']); ?></title>

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
                    <h1 class="page-title">Exam Questions: <?php echo htmlspecialchars($exam['title']); ?></h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/pro.jpg" alt="User Avatar">
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Add Question Card -->
                <div class="dash-card" style="margin-bottom: 24px;">
                    <div class="dash-card-header">
                        <h3>Add New Question</h3>
                    </div>
                    <div class="dash-card-content">
                        <form class="dash-form" method="POST" action="exam-questions.php?exam_id=<?php echo $exam_id; ?>">
                            <div class="form-group">
                                <label for="question_text">Question Text</label>
                                <textarea id="question_text" name="question_text" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="option_a">Option A</label>
                                <input type="text" id="option_a" name="option_a" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="option_b">Option B</label>
                                <input type="text" id="option_b" name="option_b" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="option_c">Option C</label>
                                <input type="text" id="option_c" name="option_c" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="option_d">Option D</label>
                                <input type="text" id="option_d" name="option_d" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="correct_option">Correct Option</label>
                                <select id="correct_option" name="correct_option" class="form-control" required>
                                    <option value="A">Option A</option>
                                    <option value="B">Option B</option>
                                    <option value="C">Option C</option>
                                    <option value="D">Option D</option>
                                </select>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <button type="submit" name="add_question" class="btn btn-primary">Save Question</button>
                                <a href="exam-management.php" class="btn btn-gold">Finish & Back to Exams</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Existing Questions -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Existing Questions (<?php echo count($questions); ?> of <?php echo $exam['total_questions']; ?>)</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Correct Answer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $index => $question): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                                    <td><?php echo htmlspecialchars($question['correct_option']); ?></td>
                                    <td>
                                        <a href="edit_question.php?id=<?php echo $question['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <a href="delete_question.php?id=<?php echo $question['id']; ?>" class="btn btn-secondary btn-sm" style="background: var(--danger); color: white;" onclick="return confirm('Are you sure?')">Delete</a>
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
