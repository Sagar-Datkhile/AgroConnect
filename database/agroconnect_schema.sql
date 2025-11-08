-- ============================================================================
-- AgroConnect - Complete Database Schema (MySQL)
-- Redesigned for XAMPP/MySQL
-- ============================================================================
-- Version: 2.0
-- Database: agroconnect
-- Description: Complete agricultural portal database with farmers, crops, 
--              admin management, activity logging, and analytics
-- ============================================================================

-- Drop existing database and create fresh
DROP DATABASE IF EXISTS agroconnect;
CREATE DATABASE agroconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agroconnect;

-- ============================================================================
-- TABLE: farmers
-- Description: Stores farmer registration and profile information
-- ============================================================================
CREATE TABLE farmers (
    farmer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password using PHP password_hash()',
    phone VARCHAR(20) DEFAULT NULL,
    region VARCHAR(100) NOT NULL,
    soil_type VARCHAR(100) NOT NULL,
    area DECIMAL(10,2) NOT NULL COMMENT 'Farm area in acres/hectares',
    is_blocked BOOLEAN DEFAULT FALSE COMMENT 'Admin can block farmers',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_region (region),
    INDEX idx_is_blocked (is_blocked),
    INDEX idx_registration_date (registration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: admins
-- Description: Admin user accounts for portal management
-- ============================================================================
CREATE TABLE admins (
    admin_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: crops
-- Description: Crop posts created by farmers with investment and turnover data
-- ============================================================================
CREATE TABLE crops (
    crop_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT UNSIGNED NOT NULL,
    crop_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) DEFAULT NULL COMMENT 'e.g., Cereals, Vegetables, Fruits, Pulses',
    investment DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Total investment amount',
    turnover DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Total turnover/revenue',
    profit DECIMAL(12,2) GENERATED ALWAYS AS (turnover - investment) STORED COMMENT 'Auto-calculated profit',
    description TEXT DEFAULT NULL,
    season VARCHAR(50) DEFAULT NULL COMMENT 'e.g., Kharif, Rabi, Zaid',
    planting_date DATE DEFAULT NULL,
    harvest_date DATE DEFAULT NULL,
    quantity DECIMAL(10,2) DEFAULT NULL COMMENT 'Yield quantity',
    quantity_unit VARCHAR(20) DEFAULT NULL COMMENT 'e.g., Quintals, Tons, KG',
    is_deleted BOOLEAN DEFAULT FALSE COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    deleted_by INT UNSIGNED DEFAULT NULL COMMENT 'Admin ID or NULL if self-deleted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (deleted_by) REFERENCES admins(admin_id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_farmer_id (farmer_id),
    INDEX idx_crop_name (crop_name),
    INDEX idx_category (category),
    INDEX idx_is_deleted (is_deleted),
    INDEX idx_created_at (created_at),
    INDEX idx_season (season),
    FULLTEXT idx_search (crop_name, description, category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: activity_logs
-- Description: Comprehensive activity logging for audit trail
-- ============================================================================
CREATE TABLE activity_logs (
    log_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('farmer', 'admin') NOT NULL,
    user_id INT UNSIGNED NOT NULL COMMENT 'farmer_id or admin_id',
    action VARCHAR(100) NOT NULL COMMENT 'e.g., login, logout, add_crop, delete_crop',
    entity_type VARCHAR(50) DEFAULT NULL COMMENT 'e.g., crop, farmer, profile',
    entity_id INT UNSIGNED DEFAULT NULL COMMENT 'ID of affected entity',
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_type_id (user_type, user_id),
    INDEX idx_action (action),
    INDEX idx_entity_type (entity_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: farmer_blocks
-- Description: Track blocking history of farmers
-- ============================================================================
CREATE TABLE farmer_blocks (
    block_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT UNSIGNED NOT NULL,
    admin_id INT UNSIGNED NOT NULL,
    reason TEXT DEFAULT NULL,
    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unblocked_at TIMESTAMP NULL DEFAULT NULL,
    unblocked_by INT UNSIGNED DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE COMMENT 'TRUE if currently blocked',
    
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(admin_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (unblocked_by) REFERENCES admins(admin_id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_farmer_id (farmer_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_is_active (is_active),
    INDEX idx_blocked_at (blocked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: farmer_sessions
-- Description: Track farmer login sessions for security
-- ============================================================================
CREATE TABLE farmer_sessions (
    session_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT UNSIGNED NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    logout_at TIMESTAMP NULL DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    INDEX idx_farmer_id (farmer_id),
    INDEX idx_session_token (session_token),
    INDEX idx_is_active (is_active),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: admin_sessions
-- Description: Track admin login sessions for security
-- ============================================================================
CREATE TABLE admin_sessions (
    session_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    logout_at TIMESTAMP NULL DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (admin_id) REFERENCES admins(admin_id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    INDEX idx_admin_id (admin_id),
    INDEX idx_session_token (session_token),
    INDEX idx_is_active (is_active),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: search_analytics
-- Description: Track search queries for analytics and improvements
-- ============================================================================
CREATE TABLE search_analytics (
    search_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    search_query VARCHAR(255) DEFAULT NULL,
    region_filter VARCHAR(100) DEFAULT NULL,
    min_area_filter DECIMAL(10,2) DEFAULT NULL,
    results_count INT DEFAULT 0,
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL,
    
    INDEX idx_search_query (search_query),
    INDEX idx_searched_at (searched_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: system_settings
-- Description: Application-wide settings and configurations
-- ============================================================================
CREATE TABLE system_settings (
    setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT UNSIGNED DEFAULT NULL,
    
    FOREIGN KEY (updated_by) REFERENCES admins(admin_id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- VIEWS: Pre-built views for common queries
-- ============================================================================

-- View: Active Crops with Farmer Details
CREATE VIEW v_active_crops AS
SELECT 
    c.crop_id,
    c.crop_name,
    c.category,
    c.investment,
    c.turnover,
    c.profit,
    c.description,
    c.season,
    c.quantity,
    c.quantity_unit,
    c.created_at,
    f.farmer_id,
    f.name AS farmer_name,
    f.email AS farmer_email,
    f.region,
    f.soil_type,
    f.area AS farm_area
FROM crops c
INNER JOIN farmers f ON c.farmer_id = f.farmer_id
WHERE c.is_deleted = FALSE AND f.is_blocked = FALSE;

-- View: Farmer Statistics Dashboard
CREATE VIEW v_farmer_stats AS
SELECT 
    f.farmer_id,
    f.name,
    f.email,
    f.region,
    f.soil_type,
    f.area,
    COUNT(c.crop_id) AS total_crops,
    COALESCE(SUM(c.investment), 0) AS total_investment,
    COALESCE(SUM(c.turnover), 0) AS total_turnover,
    COALESCE(SUM(c.profit), 0) AS total_profit,
    f.registration_date
FROM farmers f
LEFT JOIN crops c ON f.farmer_id = c.farmer_id AND c.is_deleted = FALSE
WHERE f.is_blocked = FALSE
GROUP BY f.farmer_id, f.name, f.email, f.region, f.soil_type, f.area, f.registration_date;

-- View: Admin Dashboard Overview
CREATE VIEW v_admin_dashboard AS
SELECT 
    (SELECT COUNT(*) FROM farmers WHERE is_blocked = FALSE) AS total_active_farmers,
    (SELECT COUNT(*) FROM farmers WHERE is_blocked = TRUE) AS total_blocked_farmers,
    (SELECT COUNT(*) FROM crops WHERE is_deleted = FALSE) AS total_active_crops,
    (SELECT COUNT(*) FROM crops WHERE is_deleted = TRUE) AS total_deleted_crops,
    (SELECT COALESCE(SUM(investment), 0) FROM crops WHERE is_deleted = FALSE) AS total_platform_investment,
    (SELECT COALESCE(SUM(turnover), 0) FROM crops WHERE is_deleted = FALSE) AS total_platform_turnover,
    (SELECT COALESCE(SUM(profit), 0) FROM crops WHERE is_deleted = FALSE) AS total_platform_profit;

-- ============================================================================
-- TRIGGERS: Automated actions
-- ============================================================================

-- Trigger: Update last_login on farmer login
DELIMITER //
CREATE TRIGGER trg_farmer_last_login
AFTER INSERT ON farmer_sessions
FOR EACH ROW
BEGIN
    UPDATE farmers 
    SET last_login = NEW.login_at 
    WHERE farmer_id = NEW.farmer_id;
END//
DELIMITER ;

-- Trigger: Update last_login on admin login
DELIMITER //
CREATE TRIGGER trg_admin_last_login
AFTER INSERT ON admin_sessions
FOR EACH ROW
BEGIN
    UPDATE admins 
    SET last_login = NEW.login_at 
    WHERE admin_id = NEW.admin_id;
END//
DELIMITER ;

-- Trigger: Soft delete timestamp
DELIMITER //
CREATE TRIGGER trg_crop_soft_delete
BEFORE UPDATE ON crops
FOR EACH ROW
BEGIN
    IF NEW.is_deleted = TRUE AND OLD.is_deleted = FALSE THEN
        SET NEW.deleted_at = CURRENT_TIMESTAMP;
    END IF;
END//
DELIMITER ;

-- Trigger: Sync farmer block status
DELIMITER //
CREATE TRIGGER trg_farmer_block_sync
AFTER INSERT ON farmer_blocks
FOR EACH ROW
BEGIN
    IF NEW.is_active = TRUE THEN
        UPDATE farmers SET is_blocked = TRUE WHERE farmer_id = NEW.farmer_id;
    END IF;
END//
DELIMITER ;

-- Trigger: Sync farmer unblock status
DELIMITER //
CREATE TRIGGER trg_farmer_unblock_sync
AFTER UPDATE ON farmer_blocks
FOR EACH ROW
BEGIN
    IF NEW.is_active = FALSE AND OLD.is_active = TRUE THEN
        UPDATE farmers SET is_blocked = FALSE WHERE farmer_id = NEW.farmer_id;
    END IF;
END//
DELIMITER ;

-- ============================================================================
-- STORED PROCEDURES: Common operations
-- ============================================================================

-- Procedure: Get Farmer Dashboard Stats
DELIMITER //
CREATE PROCEDURE sp_get_farmer_dashboard(IN p_farmer_id INT UNSIGNED)
BEGIN
    SELECT 
        COUNT(crop_id) AS total_crops,
        COALESCE(SUM(investment), 0) AS total_investment,
        COALESCE(SUM(turnover), 0) AS total_turnover,
        COALESCE(SUM(profit), 0) AS total_profit
    FROM crops
    WHERE farmer_id = p_farmer_id AND is_deleted = FALSE;
END//
DELIMITER ;

-- Procedure: Search Crops with Filters
DELIMITER //
CREATE PROCEDURE sp_search_crops(
    IN p_crop_name VARCHAR(100),
    IN p_region VARCHAR(100),
    IN p_min_area DECIMAL(10,2),
    IN p_category VARCHAR(50)
)
BEGIN
    SELECT 
        c.crop_id,
        c.crop_name,
        c.category,
        c.investment,
        c.turnover,
        c.profit,
        c.description,
        c.season,
        c.quantity,
        c.quantity_unit,
        c.created_at,
        f.farmer_id,
        f.name AS farmer_name,
        f.region,
        f.soil_type,
        f.area AS farm_area
    FROM crops c
    INNER JOIN farmers f ON c.farmer_id = f.farmer_id
    WHERE c.is_deleted = FALSE 
        AND f.is_blocked = FALSE
        AND (p_crop_name IS NULL OR c.crop_name LIKE CONCAT('%', p_crop_name, '%'))
        AND (p_region IS NULL OR f.region LIKE CONCAT('%', p_region, '%'))
        AND (p_min_area IS NULL OR f.area >= p_min_area)
        AND (p_category IS NULL OR c.category = p_category)
    ORDER BY c.created_at DESC;
END//
DELIMITER ;

-- Procedure: Get Admin Analytics
DELIMITER //
CREATE PROCEDURE sp_admin_analytics()
BEGIN
    SELECT * FROM v_admin_dashboard;
    
    SELECT 
        region,
        COUNT(farmer_id) AS farmer_count,
        SUM(area) AS total_area
    FROM farmers
    WHERE is_blocked = FALSE
    GROUP BY region
    ORDER BY farmer_count DESC;
    
    SELECT 
        category,
        COUNT(crop_id) AS crop_count,
        SUM(investment) AS total_investment,
        SUM(turnover) AS total_turnover,
        SUM(profit) AS total_profit
    FROM crops
    WHERE is_deleted = FALSE
    GROUP BY category
    ORDER BY crop_count DESC;
END//
DELIMITER ;

-- ============================================================================
-- SAMPLE DATA: For testing and development
-- ============================================================================

-- Insert Default Admin
INSERT INTO admins (name, email, password, role, is_active) VALUES 
('Super Admin', 'admin@agroconnect.com', 'password123', 'super_admin', TRUE),
('Moderator', 'moderator@agroconnect.com', 'password123', 'moderator', TRUE);

-- Insert Sample Farmers
INSERT INTO farmers (name, email, password, phone, region, soil_type, area) VALUES 
('Rajesh Kumar', 'rajesh.kumar@example.com', '$2y$10$E1XlPBo3Qw6YmQr8VRlwE.5JK3K8zFxZxHZDQH5K6Q7vZYmQr8VRl', '9876543210', 'Punjab', 'Alluvial', 15.50),
('Priya Sharma', 'priya.sharma@example.com', '$2y$10$E1XlPBo3Qw6YmQr8VRlwE.5JK3K8zFxZxHZDQH5K6Q7vZYmQr8VRl', '9876543211', 'Maharashtra', 'Black Cotton', 25.00),
('Amit Patel', 'amit.patel@example.com', '$2y$10$E1XlPBo3Qw6YmQr8VRlwE.5JK3K8zFxZxHZDQH5K6Q7vZYmQr8VRl', '9876543212', 'Gujarat', 'Sandy Loam', 30.75),
('Sunita Devi', 'sunita.devi@example.com', '$2y$10$E1XlPBo3Qw6YmQr8VRlwE.5JK3K8zFxZxHZDQH5K6Q7vZYmQr8VRl', '9876543213', 'Uttar Pradesh', 'Loamy', 12.00),
('Vijay Singh', 'vijay.singh@example.com', '$2y$10$E1XlPBo3Qw6YmQr8VRlwE.5JK3K8zFxZxHZDQH5K6Q7vZYmQr8VRl', '9876543214', 'Haryana', 'Alluvial', 20.00);

-- Insert Sample Crops
INSERT INTO crops (farmer_id, crop_name, category, investment, turnover, description, season, planting_date, harvest_date, quantity, quantity_unit) VALUES 
(1, 'Wheat', 'Cereals', 75000.00, 115000.00, 'High-quality wheat variety HD-2967, excellent yield', 'Rabi', '2024-11-01', '2025-04-15', 45.50, 'Quintals'),
(1, 'Rice', 'Cereals', 90000.00, 135000.00, 'Basmati rice - aromatic and premium quality', 'Kharif', '2024-06-15', '2024-11-20', 60.00, 'Quintals'),
(2, 'Cotton', 'Cash Crops', 120000.00, 180000.00, 'High-grade cotton suitable for textile industry', 'Kharif', '2024-05-20', '2024-12-10', 35.00, 'Quintals'),
(2, 'Sugarcane', 'Cash Crops', 150000.00, 210000.00, 'High sugar content variety, good market demand', 'Kharif', '2024-03-01', '2025-02-15', 450.00, 'Quintals'),
(3, 'Groundnut', 'Oilseeds', 60000.00, 92000.00, 'Oil content 48%, good for oil extraction', 'Kharif', '2024-06-01', '2024-10-30', 28.00, 'Quintals'),
(3, 'Bajra', 'Millets', 45000.00, 68000.00, 'Drought-resistant variety, excellent nutrition', 'Kharif', '2024-06-10', '2024-10-15', 32.00, 'Quintals'),
(4, 'Potato', 'Vegetables', 85000.00, 125000.00, 'Kufri Jyoti variety, high yield potential', 'Rabi', '2024-10-15', '2025-02-20', 180.00, 'Quintals'),
(4, 'Tomato', 'Vegetables', 65000.00, 95000.00, 'Hybrid variety resistant to diseases', 'Rabi', '2024-10-20', '2025-02-10', 120.00, 'Quintals'),
(5, 'Mustard', 'Oilseeds', 55000.00, 82000.00, 'High oil content, good market price', 'Rabi', '2024-10-25', '2025-03-15', 22.00, 'Quintals'),
(5, 'Chickpea', 'Pulses', 70000.00, 105000.00, 'Desi variety, high protein content', 'Rabi', '2024-11-05', '2025-04-10', 18.00, 'Quintals');

-- Insert Sample System Settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES 
('site_name', 'AgroConnect', 'string', 'Application name'),
('maintenance_mode', '0', 'boolean', 'Enable/disable maintenance mode'),
('max_crops_per_farmer', '100', 'number', 'Maximum crops a farmer can post'),
('session_timeout', '3600', 'number', 'Session timeout in seconds');

-- ============================================================================
-- INDEXES OPTIMIZATION SUMMARY
-- ============================================================================
-- All foreign keys are indexed automatically
-- Additional indexes on frequently queried columns
-- Composite indexes for common filter combinations
-- Fulltext index for search functionality

-- ============================================================================
-- DATABASE SCHEMA COMPLETED SUCCESSFULLY
-- ============================================================================
-- Next Steps:
-- 1. Update PHP files to use new schema
-- 2. Update connection settings in db_connect.php
-- 3. Test all CRUD operations
-- 4. Implement session tracking features
-- 5. Add activity logging in PHP files
-- ============================================================================
