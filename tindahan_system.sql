-- Create the database
CREATE DATABASE IF NOT EXISTS tindahan_system;

-- Use the database
USE tindahan_system;

-- Drop existing tables if they exist to avoid errors
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users for testing
-- Note: Passwords are stored as plaintext here for simplicity, but they will be hashed by the PHP application
INSERT INTO users (username, email, password) VALUES
('Aling Nena', 'alingnena@example.com', 'password123'),
('Account 2', 'account2@example.com', 'password123'),
('Account 3', 'account3@example.com', 'password123');

-- Select all users to confirm data was inserted
SELECT * FROM users;