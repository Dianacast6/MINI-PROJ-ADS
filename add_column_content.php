<?php
include 'config/db.php';
$conn->query("ALTER TABLE notes ADD COLUMN content_style TEXT DEFAULT NULL");
echo "Column content_style added successfully (or already exists/error: " . $conn->error . ")";
?>