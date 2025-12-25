<?php
// DATABASE CONFIGURATION
// DATABASE CONFIGURATION

$whitelist = array('127.0.0.1', '::1', 'localhost');

if (in_array($_SERVER['REMOTE_ADDR'], $whitelist) || $_SERVER['SERVER_NAME'] == 'localhost') {
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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>