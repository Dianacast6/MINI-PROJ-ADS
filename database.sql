CREATE DATABASE IF NOT EXISTS quicknote_db;
USE quicknote_db;
-- 1. Create the Notebooks Table
CREATE TABLE IF NOT EXISTS notebooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    space_name VARCHAR(50) DEFAULT 'Personal',
    created_by_user VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL,
    is_trashed TINYINT(1) DEFAULT 0,
    trashed_at DATETIME DEFAULT NULL
);
-- 2. Update Notes Table to support Notebooks
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content MEDIUMTEXT,
    notebook_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_trashed TINYINT(1) DEFAULT 0,
    trashed_at DATETIME DEFAULT NULL,
    FOREIGN KEY (notebook_id) REFERENCES notebooks(id) ON DELETE
    SET NULL
);
-- 3. Dummy Data (Optional)
-- INSERT INTO notebooks (name, space_name, created_by_user) VALUES ('School Work', 'Personal', 'dianacast555');
-- INSERT INTO notes (title, content, notebook_id) VALUES ('Math Homework', 'Algebra notes...', 1);