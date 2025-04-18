<?php
require_once 'config/database.php';

try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST,
        DB_USER,
        DB_PASSWORD
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $conn->exec("USE " . DB_NAME);

    // Create users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id VARCHAR(36) PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create scan_results table
    $conn->exec("CREATE TABLE IF NOT EXISTS scan_results (
        id VARCHAR(36) PRIMARY KEY,
        tool VARCHAR(50) NOT NULL,
        target VARCHAR(255) NOT NULL,
        output TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id VARCHAR(36) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Create default admin user
    $adminPassword = password_hash('admin', PASSWORD_DEFAULT);
    $adminId = uniqid();
    $stmt = $conn->prepare("INSERT IGNORE INTO users (id, email, password) VALUES (?, 'admin', ?)");
    $stmt->execute([$adminId, $adminPassword]);

    echo "Database initialized successfully\n";
} catch(PDOException $e) {
    die("Database initialization failed: " . $e->getMessage() . "\n");
}