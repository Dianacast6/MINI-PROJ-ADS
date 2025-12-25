<?php
include 'config/db.php';

// 1. Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'users' created/verified.<br>";
} else {
    echo "Error creating table users: " . $conn->error . "<br>";
}

// 2. Add user_id to notebooks
// Check if column exists
$res = $conn->query("SHOW COLUMNS FROM notebooks LIKE 'user_id'");
if ($res->num_rows == 0) {
    $conn->query("ALTER TABLE notebooks ADD COLUMN user_id INT DEFAULT NULL"); // Nullable for now to not break existing
    $conn->query("ALTER TABLE notebooks ADD CONSTRAINT fk_nb_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
    echo "Added user_id to notebooks.<br>";
} else {
    echo "Column user_id already exists in notebooks.<br>";
}

// 3. Add user_id to notes
$res = $conn->query("SHOW COLUMNS FROM notes LIKE 'user_id'");
if ($res->num_rows == 0) {
    $conn->query("ALTER TABLE notes ADD COLUMN user_id INT DEFAULT NULL");
    $conn->query("ALTER TABLE notes ADD CONSTRAINT fk_note_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
    echo "Added user_id to notes.<br>";
} else {
    echo "Column user_id already exists in notes.<br>";
}

echo "Auth setup complete. Delete this file.";
?>