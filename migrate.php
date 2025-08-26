<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/database/DatabaseMigration.php';

use App\Database\DatabaseMigration;

// Check command line arguments
$command = $argv[1] ?? 'migrate';

switch ($command) {
    case 'migrate':
        DatabaseMigration::migrate();
        break;
        
    case 'reset':
        DatabaseMigration::reset();
        break;
        
    case 'fresh':
        DatabaseMigration::reset();
        DatabaseMigration::migrate();
        break;
        
    case 'seed':
        DatabaseMigration::seed();
        break;
        
    default:
        echo "Usage: php migrate.php [migrate|reset|fresh|seed]\n";
        echo "  migrate - Run migrations\n";
        echo "  reset   - Drop all tables\n";
        echo "  fresh   - Reset and migrate\n";
        echo "  seed    - Seed data only\n";
        break;
}