<?php
// DATABASE CONFIGURATION
// DATABASE CONFIGURATION

$whitelist = array('127.0.0.1', '::1', 'localhost');

$remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
$server_name = $_SERVER['SERVER_NAME'] ?? '';

if (in_array($remote_addr, $whitelist) || $server_name == 'localhost') {
    // Localhost (XAMPP)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "quicknote_db";
} else {
    // InfinityFree Deployment
    $servername = "sql305.infinityfree.com";
    $username = "if0_40760361";
    $password = "v8DVyYWMWrkFJ6A";
    $dbname = "if0_40760361_quicknote_db";
}

// Enable error reporting for mysqli to throw exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // Graceful error for production
    die("Database Connection Failed: " . $e->getMessage());
}
?>