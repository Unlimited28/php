<?php
/**
 * Database Setup Script for Royal Ambassadors OGBC Portal
 * Run this through a web browser to set up the database
 */

// Include bootstrap
require_once __DIR__ . '/app/bootstrap.php';

// Check if this is a POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'migrate':
                $result = runMigration();
                $message = $result['success'] ? 'Database migration completed successfully!' : 'Migration failed: ' . $result['error'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
                
            case 'reset':
                $result = resetDatabase();
                $message = $result['success'] ? 'Database reset completed!' : 'Reset failed: ' . $result['error'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
                
            case 'fresh':
                $resetResult = resetDatabase();
                if ($resetResult['success']) {
                    $migrateResult = runMigration();
                    $message = $migrateResult['success'] ? 'Database fresh migration completed!' : 'Fresh migration failed: ' . $migrateResult['error'];
                    $messageType = $migrateResult['success'] ? 'success' : 'error';
                } else {
                    $message = 'Fresh migration failed during reset: ' . $resetResult['error'];
                    $messageType = 'error';
                }
                break;
                
            default:
                $message = 'Unknown action';
                $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

function executeSqlFile($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("SQL file not found: $filePath");
    }
    
    $sql = file_get_contents($filePath);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );
    
    $conn = \App\Core\DB::conn();
    $results = [];
    
    try {
        $conn->beginTransaction();
        
        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                $conn->exec($statement);
                $results[] = "✓ Executed: " . substr(trim($statement), 0, 50) . "...";
            }
        }
        
        $conn->commit();
        return ['success' => true, 'results' => $results];
        
    } catch (Exception $e) {
        $conn->rollBack();
        throw new Exception("Migration failed: " . $e->getMessage());
    }
}

function runMigration() {
    try {
        $results = [];
        
        // Run core tables migration
        $coreResult = executeSqlFile(__DIR__ . '/database/migrations/001_create_core_tables.sql');
        $results = array_merge($results, $coreResult['results']);
        
        // Seed associations and ranks
        $seedResult1 = executeSqlFile(__DIR__ . '/database/seeds/001_seed_associations_and_ranks.sql');
        $results = array_merge($results, $seedResult1['results']);
        
        // Seed sample data
        $seedResult2 = executeSqlFile(__DIR__ . '/database/seeds/002_seed_sample_data.sql');
        $results = array_merge($results, $seedResult2['results']);
        
        return ['success' => true, 'results' => $results];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function resetDatabase() {
    try {
        $conn = \App\Core\DB::conn();
        $results = [];
        
        // Get all tables
        $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        // Disable foreign key checks
        $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
        $results[] = "✓ Disabled foreign key checks";
        
        // Drop all tables
        foreach ($tables as $table) {
            $conn->exec("DROP TABLE IF EXISTS `$table`");
            $results[] = "✓ Dropped table: $table";
        }
        
        // Re-enable foreign key checks
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        $results[] = "✓ Re-enabled foreign key checks";
        
        return ['success' => true, 'results' => $results];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Check database connection
try {
    $conn = \App\Core\DB::conn();
    $dbConnected = true;
    $dbInfo = $conn->query("SELECT DATABASE() as db_name, VERSION() as version")->fetch();
} catch (Exception $e) {
    $dbConnected = false;
    $dbError = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royal Ambassadors OGBC - Database Setup</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .status-box {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid;
        }
        .status-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .status-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .status-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .results {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .info-card {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Royal Ambassadors OGBC</h1>
            <h2>Database Setup & Migration</h2>
        </div>
        
        <!-- Database Connection Status -->
        <?php if ($dbConnected): ?>
            <div class="status-box status-success">
                <strong>✓ Database Connected Successfully</strong><br>
                Database: <?= $dbInfo['db_name'] ?><br>
                MySQL Version: <?= $dbInfo['version'] ?>
            </div>
        <?php else: ?>
            <div class="status-box status-error">
                <strong>✗ Database Connection Failed</strong><br>
                Error: <?= $dbError ?><br>
                Please check your database configuration in app/config/database.php
            </div>
        <?php endif; ?>
        
        <!-- Action Results -->
        <?php if (isset($message)): ?>
            <div class="status-box status-<?= $messageType ?>">
                <strong><?= htmlspecialchars($message) ?></strong>
                <?php if (isset($result['results'])): ?>
                    <div class="results"><?= implode("\n", $result['results']) ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Migration Actions -->
        <?php if ($dbConnected): ?>
            <div class="info-grid">
                <div class="info-card">
                    <h3>Migration Actions</h3>
                    <p>Choose an action to set up or modify your database:</p>
                    
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="migrate">
                        <button type="submit" class="btn">Run Migration</button>
                    </form>
                    
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="fresh">
                        <button type="submit" class="btn btn-warning">Fresh Migration</button>
                    </form>
                    
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="reset">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('This will delete all data. Are you sure?')">Reset Database</button>
                    </form>
                </div>
                
                <div class="info-card">
                    <h3>Database Schema</h3>
                    <p><strong>Phase 3: Database Integration</strong></p>
                    <ul>
                        <li>✓ 25 Official Associations</li>
                        <li>✓ 11 Royal Ambassador Ranks</li>
                        <li>✓ User Management System</li>
                        <li>✓ Exam System with Auto-grading</li>
                        <li>✓ Payment Tracking & Verification</li>
                        <li>✓ Notification System</li>
                        <li>✓ Content Management (Blogs/Gallery)</li>
                        <li>✓ Camp Registration System</li>
                    </ul>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Action Descriptions</h3>
                <p><strong>Run Migration:</strong> Creates tables and seeds initial data (safe for existing data)</p>
                <p><strong>Fresh Migration:</strong> Drops all tables and recreates everything with sample data</p>
                <p><strong>Reset Database:</strong> Drops all tables (WARNING: Deletes all data)</p>
            </div>
        <?php endif; ?>
        
        <div class="status-box status-info">
            <strong>Next Steps After Migration:</strong><br>
            1. Test the portal functionality<br>
            2. Login with sample accounts (password: 'password123'):<br>
            &nbsp;&nbsp;- Ambassador: john.doe@example.com<br>
            &nbsp;&nbsp;- President: jane.smith@example.com<br>
            &nbsp;&nbsp;- Super Admin: admin@ogbc.org<br>
            3. Configure file upload directories<br>
            4. Set up production security settings
        </div>
    </div>
</body>
</html>