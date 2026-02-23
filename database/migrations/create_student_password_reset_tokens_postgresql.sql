-- Create student_password_reset_tokens table (PostgreSQL)
-- Run this if the migration hasn't been run yet

CREATE TABLE IF NOT EXISTS student_password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);
