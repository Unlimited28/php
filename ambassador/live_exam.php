<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ambassador') {
    header('Location: ../public/login.php');
    exit;
}

$exam_id = $_GET['exam_id'] ?? 0;
$db = get_db_connection();

// Fetch exam details
$stmt = $db->prepare('SELECT * FROM exams WHERE id = ? AND status = "published"');
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

if (!$exam) {
    die("Exam not found or not available.");
}

// Fetch questions
$stmt = $db->prepare('SELECT * FROM exam_questions WHERE exam_id = ?');
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Exam: <?php echo htmlspecialchars($exam['title']); ?> - Royal Ambassadors</title>

    <!-- Google Fonts & Remix Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Theme & Dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <style>
        .exam-card {
            background-color: var(--panel);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: var(--radius);
            padding: 32px;
        }
        .exam-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }
        .exam-timer {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--danger);
        }
        .question-meta {
            font-weight: 600;
            color: var(--muted);
        }
        .question-text {
            font-size: 1.2rem;
            margin: 24px 0;
            line-height: 1.6;
        }
        .options-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .options-list li label {
            display: block;
            padding: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
        }
        .options-list li label:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: var(--accent-2);
        }
        .options-list li input[type="radio"] {
            display: none;
        }
        .options-list li input[type="radio"]:checked + label {
            background-color: var(--accent-1);
            border-color: var(--accent-2);
            color: white;
            box-shadow: var(--glow);
        }
        .exam-navigation {
            margin-top: 32px;
            display: flex;
            justify-content: space-between;
        }
    </style>
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
                <a href="my_exams.php" class="active"><i class="ri-file-list-3-line"></i> My Exams</a>
                <a href="my_results.php"><i class="ri-trophy-line"></i> My Results</a>
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
                    <h1 class="page-title">Exam: <?php echo htmlspecialchars($exam['title']); ?></h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/team-1.jpg" alt="User Avatar">
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <form action="submit_exam.php" method="POST">
                    <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                    <div class="exam-card">
                        <div class="exam-header">
                            <div class="question-meta">Question <span id="current-question-number">1</span> of <?php echo count($questions); ?></div>
                            <div class="exam-timer" id="timer"><i class="ri-time-line"></i> <?php echo $exam['duration']; ?>:00</div>
                        </div>

                        <?php foreach ($questions as $index => $question): ?>
                        <div class="question-body" id="question-<?php echo $index; ?>" style="<?php echo $index > 0 ? 'display: none;' : ''; ?>">
                            <p class="question-text">
                                <?php echo htmlspecialchars($question['question_text']); ?>
                            </p>
                            <ul class="options-list">
                                <li>
                                    <input type="radio" id="q<?php echo $question['id']; ?>_optA" name="answers[<?php echo $question['id']; ?>]" value="A">
                                    <label for="q<?php echo $question['id']; ?>_optA">A) <?php echo htmlspecialchars($question['option_a']); ?></label>
                                </li>
                                <li>
                                    <input type="radio" id="q<?php echo $question['id']; ?>_optB" name="answers[<?php echo $question['id']; ?>]" value="B">
                                    <label for="q<?php echo $question['id']; ?>_optB">B) <?php echo htmlspecialchars($question['option_b']); ?></label>
                                </li>
                                <li>
                                    <input type="radio" id="q<?php echo $question['id']; ?>_optC" name="answers[<?php echo $question['id']; ?>]" value="C">
                                    <label for="q<?php echo $question['id']; ?>_optC">C) <?php echo htmlspecialchars($question['option_c']); ?></label>
                                </li>
                                <li>
                                    <input type="radio" id="q<?php echo $question['id']; ?>_optD" name="answers[<?php echo $question['id']; ?>]" value="D">
                                    <label for="q<?php echo $question['id']; ?>_optD">D) <?php echo htmlspecialchars($question['option_d']); ?></label>
                                </li>
                            </ul>
                        </div>
                        <?php endforeach; ?>

                        <div class="exam-navigation">
                            <button type="button" class="btn btn-secondary" id="prev-btn" style="display: none;">Previous</button>
                            <button type="button" class="btn btn-primary" id="next-btn">Next</button>
                            <button type="submit" class="btn btn-gold" id="submit-btn" style="display: none;">Submit Exam</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="../assets/js/dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questions = document.querySelectorAll('.question-body');
            const nextBtn = document.getElementById('next-btn');
            const prevBtn = document.getElementById('prev-btn');
            const submitBtn = document.getElementById('submit-btn');
            const questionNumberSpan = document.getElementById('current-question-number');
            const timerDisplay = document.getElementById('timer');

            let currentQuestionIndex = 0;
            const totalQuestions = questions.length;

            function showQuestion(index) {
                questions.forEach((q, i) => {
                    q.style.display = i === index ? 'block' : 'none';
                });
                questionNumberSpan.textContent = index + 1;
                prevBtn.style.display = index > 0 ? 'inline-block' : 'none';
                nextBtn.style.display = index < totalQuestions - 1 ? 'inline-block' : 'none';
                submitBtn.style.display = index === totalQuestions - 1 ? 'inline-block' : 'none';
            }

            nextBtn.addEventListener('click', () => {
                if (currentQuestionIndex < totalQuestions - 1) {
                    currentQuestionIndex++;
                    showQuestion(currentQuestionIndex);
                }
            });

            prevBtn.addEventListener('click', () => {
                if (currentQuestionIndex > 0) {
                    currentQuestionIndex--;
                    showQuestion(currentQuestionIndex);
                }
            });

            // Timer
            let timeRemaining = <?php echo $exam['duration'] * 60; ?>;
            const timerInterval = setInterval(() => {
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                timerDisplay.innerHTML = `<i class="ri-time-line"></i> ${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    document.querySelector('form').submit();
                }
                timeRemaining--;
            }, 1000);

            showQuestion(0);
        });
    </script>
</body>
</html>
