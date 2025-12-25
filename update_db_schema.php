<?php
include 'config/db.php';

$sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) DEFAULT NULL";
if ($conn->query($sql) === TRUE) {
    echo "Column 'reset_token' added successfully or already exists.<br>";
} else {
    echo "Error adding column 'reset_token': " . $conn->error . "<br>";
}

$sql2 = "ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_expiry DATETIME DEFAULT NULL";
if ($conn->query($sql2) === TRUE) {
    echo "Column 'reset_expiry' added successfully or already exists.<br>";
} else {
    echo "Error adding column 'reset_expiry': " . $conn->error . "<br>";
}

$conn->close();
?>