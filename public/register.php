<?php
require_once '../includes/database.php';
require_once '../config/config.php';

$db = get_db_connection();
$associations = $db->query('SELECT * FROM associations ORDER BY name')->fetchAll();
$ranks = $db->query('SELECT * FROM ranks ORDER BY level')->fetchAll();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $passcode = $_POST['passcode'] ?? '';
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $church = $_POST['church'];
    $age = $_POST['age'];
    $association_id = $_POST['association_id'];
    $rank_id = $_POST['rank_id'];
    $password = $_POST['password'];

    // Validation
    if (($role === 'president' && $passcode !== PRESIDENT_PASSCODE) || ($role === 'super_admin' && $passcode !== SUPER_ADMIN_PASSCODE)) {
        $error_message = 'Invalid passcode for the selected role.';
    } else {
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error_message = 'An account with this email already exists.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $unique_id = 'OGBC/RA/' . mt_rand(1000, 9999);

            $insert_stmt = $db->prepare('INSERT INTO users (unique_id, full_name, email, phone, password, role, association_id, rank_id, church, age) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $insert_stmt->execute([$unique_id, $fullname, $email, $phone, $password_hash, $role, $association_id, $rank_id, $church, $age]);

            $success_message = "Registration successful! You can now login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Royal Ambassadors Portal</title>

    <!-- Google Fonts & Remix Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Premium Theme CSS -->
    <link rel="stylesheet" href="../assets/css/theme.css">

    <style>
        .auth-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 24px;
        }
        .auth-card {
            background: var(--panel);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 40px;
            width: 100%;
            max-width: 550px;
            text-align: center;
        }
        .auth-card .brand {
            justify-content: center;
            margin-bottom: 24px;
        }
        .auth-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .auth-card p {
            color: var(--muted);
            margin-bottom: 32px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            display: block;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.1);
            background-color: var(--panel-2);
            color: var(--text);
            font-size: 1rem;
            transition: var(--transition);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent-1);
            box-shadow: 0 0 0 3px rgba(25,140,255,.2);
        }
        select.form-control {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23a8b2d1' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
        .btn-full {
            width: 100%;
            padding: 16px;
            font-size: 1rem;
        }
        .auth-footer {
            margin-top: 24px;
            color: var(--muted);
        }
        .auth-footer a {
            color: var(--accent-2);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Auth Section -->
    <section class="auth-section">
        <div class="auth-card">
            <div class="brand">
                <div class="logo-container">
                    <img src="../assets/images/logo.png" alt="Logo" style="height: 45px;">
                </div>
                <div class="title">Royal Ambassadors</div>
            </div>
            <h2>Create Your Account</h2>
            <p>Join the digital portal and start your journey.</p>

            <?php if ($error_message): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="role">Select Your Role</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="ambassador">Ambassador</option>
                            <option value="president">Association President</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="form-group full-width" id="passcodeField" style="display: none;">
                        <label for="passcode">Passcode</label>
                        <input type="text" id="passcode" name="passcode" class="form-control">
                    </div>
                    <div class="form-group full-width">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="church">Church</label>
                        <input type="text" id="church" name="church" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" class="form-control" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="association">Association</label>
                        <select id="association" name="association_id" class="form-control" required>
                            <option value="">-- Select Association --</option>
                            <?php foreach ($associations as $association): ?>
                                <option value="<?php echo $association['id']; ?>"><?php echo htmlspecialchars($association['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label for="rank">Current Rank</label>
                        <select id="rank" name="rank_id" class="form-control" required>
                            <option value="">-- Select Rank --</option>
                            <?php foreach ($ranks as $rank): ?>
                                <option value="<?php echo $rank['id']; ?>"><?php echo htmlspecialchars($rank['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="ri-user-add-line"></i>
                    Register Now
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </section>
    <footer style="position: fixed; bottom: 0; width: 100%; background: var(--bg); text-align: center; padding: 10px 0; font-size: 0.9rem; color: var(--muted);">
        <p>&copy; 2025 Royal Ambassadors Ogun Baptist Conference. All rights reserved.</p>
    </footer>
    <script src="../assets/js/public.js"></script>
</body>
</html>
