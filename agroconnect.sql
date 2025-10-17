-- AgroConnect Database Schema
-- Import this file into MySQL Workbench or phpMyAdmin

CREATE DATABASE IF NOT EXISTS agroconnect;
USE agroconnect;

-- Farmers Table
CREATE TABLE IF NOT EXISTS farmers (
  farmer_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  region VARCHAR(100) NOT NULL,
  soil_type VARCHAR(100) NOT NULL,
  area FLOAT NOT NULL,
  is_blocked BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crops Table
CREATE TABLE IF NOT EXISTS crops (
  crop_id INT AUTO_INCREMENT PRIMARY KEY,
  farmer_id INT NOT NULL,
  crop_name VARCHAR(100) NOT NULL,
  investment DECIMAL(10,2) NOT NULL,
  turnover DECIMAL(10,2) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id) ON DELETE CASCADE
);

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin
-- Password: password123 (plain text for development - use hashing in production)
INSERT INTO admins (email, password) VALUES ('admin@example.com', 'password123');

-- Sample Data for Testing (Optional)
INSERT INTO farmers (name, email, password, region, soil_type, area) VALUES 
('John Doe', 'john@example.com', 'farmer123', 'Punjab', 'Alluvial', 5.5),
('Jane Smith', 'jane@example.com', 'farmer123', 'Maharashtra', 'Black Cotton', 10.0);

INSERT INTO crops (farmer_id, crop_name, investment, turnover, description) VALUES 
(1, 'Wheat', 50000.00, 75000.00, 'High quality wheat with good yield'),
(1, 'Rice', 60000.00, 90000.00, 'Basmati rice variety'),
(2, 'Cotton', 80000.00, 120000.00, 'Premium quality cotton');

