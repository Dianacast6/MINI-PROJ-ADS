CREATE DATABASE IF NOT EXISTS quicknote_db;
USE quicknote_db;

-- 1. Create the Notebooks Table
CREATE TABLE IF NOT EXISTS notebooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Update Notes Table to support Notebooks
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    notebook_id INT DEFAULT NULL,  -- NULL means it's a loose note
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notebook_id) REFERENCES notebooks(id) ON DELETE SET NULL
);

-- -- 3. Dummy Data
-- INSERT INTO notebooks (name) VALUES ('School Work'), ('Personal');
-- INSERT INTO notes (title, content, notebook_id) VALUES ('Math Homework', 'Algebra notes...', 1);
-- INSERT INTO notes (title, content, notebook_id) VALUES ('Grocery List', 'Milk, Eggs...', NULL);

ALTER TABLE notes ADD COLUMN is_trashed TINYINT(1) DEFAULT 0;
ALTER TABLE notes ADD COLUMN trashed_at DATETIME DEFAULT NULL;