<?php
$conn = new mysqli("localhost", "root", "", "quicknote_db");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

// Check if column exists first to avoid error spam
$check = $conn->query("SHOW COLUMNS FROM notes LIKE 'content_style'");
if ($check->num_rows == 0) {
    if ($conn->query("ALTER TABLE notes ADD COLUMN content_style TEXT DEFAULT NULL")) {
        echo "SUCCESS: Column content_style added.";
    } else {
        echo "ERROR: " . $conn->error;
    }
} else {
    echo "SKIPPED: Column content_style already exists.";
}
?>