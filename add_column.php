<?php
include 'config/db.php';
$conn->query("ALTER TABLE notes ADD COLUMN title_style TEXT DEFAULT NULL");
echo "Column added successfully";
?>