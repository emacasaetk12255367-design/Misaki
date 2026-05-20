-- Migration: Add password reset tables for user and admin
-- Run this once against your misaki database

CREATE TABLE IF NOT EXISTS password_reset (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  token      VARCHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used       TINYINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_token (token),
  INDEX idx_user (user_id)
);

CREATE TABLE IF NOT EXISTS admin_password_reset (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  admin_id   INT NOT NULL,
  token      VARCHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used       TINYINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_token (token),
  INDEX idx_admin (admin_id)
);

-- Optional: add email column to admin_user if not exists
-- ALTER TABLE admin_user ADD COLUMN IF NOT EXISTS email VARCHAR(255) DEFAULT NULL;
