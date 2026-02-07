<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sayul_tours";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist (First run safety)
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

// Import schema if tables don't exist
// This is a simple check; in production, use migrations.
$tableCheck = $conn->query("SHOW TABLES LIKE 'admins'");
if ($tableCheck->num_rows == 0) {
    $sqlContent = file_get_contents(__DIR__ . '/database.sql');
    if ($conn->multi_query($sqlContent)) {
        do {
            // consume all results for multi_query
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
}
?>
