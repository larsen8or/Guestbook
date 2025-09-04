<?php
// Database configuration
$db_file = __DIR__ . '/guestbook.sqlite';

try {
    // Connect to SQLite database
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop existing table if it exists
    $pdo->exec("DROP TABLE IF EXISTS entries");
    
    // Create new table with the correct schema
    $pdo->exec("
        CREATE TABLE entries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            message TEXT NOT NULL,
            ip_address TEXT,
            user_agent TEXT,
            is_approved INTEGER DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    echo "Database setup completed successfully!\n";
    echo "You can now access the guestbook at: http://localhost/guestbook/public\n";
    
} catch (PDOException $e) {
    die("Error setting up database: " . $e->getMessage() . "\n");
}
?>
