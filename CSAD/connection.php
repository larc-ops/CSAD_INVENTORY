<?php
// ============================
// DATABASE CONNECTION FILE
// ============================

// Database credentials
$host    = "localhost";        // XAMPP default
$db_user = "root";             // XAMPP default
$db_pass = "";                 // XAMPP default (blank)
$db_name = "inventory_system"; // Database name from your SQL script

// Create connection using MySQLi
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8 (recommended)
$conn->set_charset("utf8mb4");

// Optional: uncomment to confirm connection works (for testing only)
// echo "✅ Connected successfully to database: " . $db_name;
?>