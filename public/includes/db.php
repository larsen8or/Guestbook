<?php
// Database configuration
$db_file = dirname(__DIR__) . '/../guestbook.sqlite';

// Create database directory if it doesn't exist
$db_dir = dirname($db_file);
if (!is_dir($db_dir)) {
    mkdir($db_dir, 0755, true);
}

try {
    // Connect to SQLite database
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS entries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            message TEXT NOT NULL,
            ip_address TEXT,
            user_agent TEXT,
            is_approved INTEGER DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
} catch (PDOException $e) {
    // Log error and show user-friendly message
    error_log('Database Error: ' . $e->getMessage());
    die('Der opstod en databasefejl. Prøv venligst igen senere.');
}

// Set character encoding
$pdo->exec('PRAGMA encoding = "UTF-8"');
?>
