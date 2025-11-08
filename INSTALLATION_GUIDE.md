# 🚀 AgroConnect - New Database Installation Guide

## 📌 Overview
This guide will help you install the completely redesigned AgroConnect database with MySQL. The new schema includes enhanced features such as:

- **Activity Logging**: Track all user actions
- **Session Management**: Secure session tracking for farmers and admins
- **Farmer Blocking History**: Complete audit trail for blocks/unblocks
- **Search Analytics**: Track search patterns
- **Generated Fields**: Auto-calculated profit
- **Database Views**: Pre-built views for common queries
- **Stored Procedures**: Optimized database operations
- **Triggers**: Automated database actions

---

## ⚡ Quick Installation (Recommended)

### Method 1: Using Web-Based Setup (Easiest)

1. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL**

2. **Access Setup Page**
   - Open browser: `http://localhost/AgroConnect/database/setup.php`
   - Fill in database credentials (default: localhost, root, no password)
   - Click **Install Database**

3. **Done!** The setup will:
   - Create the database
   - Create all tables
   - Set up views, triggers, and procedures
   - Insert sample data
   - Display default login credentials

---

## 📋 Manual Installation

### Method 2: Using phpMyAdmin

1. **Start XAMPP**
   - Start Apache and MySQL services

2. **Open phpMyAdmin**
   - Navigate to: `http://localhost/phpmyadmin`

3. **Import SQL File**
   - Click on **Import** tab
   - Choose file: `AgroConnect/database/agroconnect_schema.sql`
   - Click **Go**
   - Wait for success message

### Method 3: Using MySQL Command Line

```bash
# Navigate to project directory
cd C:\xampp\htdocs\AgroConnect\database

# Import the SQL file
mysql -u root -p < agroconnect_schema.sql
```

(Press Enter when prompted for password if using default XAMPP settings)

### Method 4: Using MySQL Workbench

1. Open MySQL Workbench
2. Connect to Local instance (localhost:3306)
3. File → Open SQL Script
4. Select `agroconnect_schema.sql`
5. Execute (Lightning icon ⚡)

---

## 🗄️ Database Structure

### Core Tables

| Table | Description |
|-------|-------------|
| `farmers` | Farmer accounts and profiles |
| `admins` | Admin user accounts |
| `crops` | Crop posts with investment/turnover data |
| `activity_logs` | Complete activity audit trail |
| `farmer_blocks` | Farmer blocking history |
| `farmer_sessions` | Farmer login session tracking |
| `admin_sessions` | Admin login session tracking |
| `search_analytics` | Search query analytics |
| `system_settings` | Application configuration |

### Database Views

| View | Purpose |
|------|---------|
| `v_active_crops` | Active crops with farmer details |
| `v_farmer_stats` | Farmer statistics dashboard |
| `v_admin_dashboard` | Admin overview metrics |

### Stored Procedures

| Procedure | Purpose |
|-----------|---------|
| `sp_get_farmer_dashboard` | Get farmer's crop statistics |
| `sp_search_crops` | Advanced crop search |
| `sp_admin_analytics` | Complete admin analytics |

### Triggers

- `trg_farmer_last_login` - Updates last login timestamp
- `trg_admin_last_login` - Updates admin last login
- `trg_crop_soft_delete` - Sets deleted_at timestamp
- `trg_farmer_block_sync` - Syncs block status
- `trg_farmer_unblock_sync` - Syncs unblock status

---

## 🔐 Default Credentials

### Admin Access
```
Email: admin@agroconnect.com
Password: password123
```

### Sample Farmer Accounts
```
Farmer 1:
Email: rajesh.kumar@example.com
Password: Test@123

Farmer 2:
Email: priya.sharma@example.com
Password: Test@123
```

⚠️ **IMPORTANT**: Change these passwords in production!

---

## 🎯 New Features in Database

### 1. Extended Crop Fields
- `category` - Cereals, Vegetables, Fruits, etc.
- `season` - Kharif, Rabi, Zaid
- `planting_date` & `harvest_date`
- `quantity` & `quantity_unit`
- `profit` - Auto-calculated (turnover - investment)

### 2. Activity Logging
All actions are logged with:
- User type (farmer/admin)
- Action performed
- Timestamp
- IP address
- User agent

### 3. Session Tracking
- Secure session tokens
- Login/logout timestamps
- IP and user agent tracking
- Active session management

### 4. Farmer Blocking System
- Block/unblock history
- Admin who performed action
- Reason for blocking
- Timestamps for all actions

### 5. Search Analytics
- Track search queries
- Results count
- Filters used
- User behavior analysis

---

## 🔄 Migrating from Old Database

If you have existing data:

### Option 1: Fresh Install (Recommended)
1. Backup your old data
2. Run new installation
3. Register farmers again
4. Re-enter crop data

### Option 2: Data Migration
Create custom migration script:
```sql
-- Example migration queries
INSERT INTO agroconnect.farmers (name, email, password, region, soil_type, area)
SELECT name, email, password, region, soil_type, area 
FROM old_agroconnect.farmers;

INSERT INTO agroconnect.crops (farmer_id, crop_name, investment, turnover, description)
SELECT farmer_id, crop_name, investment, turnover, description
FROM old_agroconnect.crops;
```

---

## ✅ Verification Steps

### 1. Check Database Creation
```sql
SHOW DATABASES LIKE 'agroconnect';
```

### 2. Verify Tables
```sql
USE agroconnect;
SHOW TABLES;
```
Should show 9 tables.

### 3. Check Sample Data
```sql
SELECT COUNT(*) FROM farmers;
SELECT COUNT(*) FROM crops;
SELECT COUNT(*) FROM admins;
```

### 4. Test Views
```sql
SELECT * FROM v_admin_dashboard;
SELECT * FROM v_farmer_stats LIMIT 5;
```

### 5. Test Stored Procedure
```sql
CALL sp_get_farmer_dashboard(1);
```

---

## 🐛 Troubleshooting

### Issue: "Database already exists"
**Solution**: Drop existing database first
```sql
DROP DATABASE IF EXISTS agroconnect;
```
Then re-run installation.

### Issue: "Cannot create trigger"
**Solution**: Ensure you have proper MySQL privileges
```sql
GRANT ALL PRIVILEGES ON agroconnect.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Issue: "Stored procedure error"
**Solution**: Check MySQL version (requires 5.7+)
```sql
SELECT VERSION();
```

### Issue: "Connection failed"
**Solution**: 
1. Verify MySQL is running in XAMPP
2. Check credentials in `php/db_connect.php`
3. Test connection:
```php
php -r "new mysqli('localhost', 'root', '', 'agroconnect');"
```

### Issue: "Character set problems"
**Solution**: Database uses utf8mb4
```sql
ALTER DATABASE agroconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 🧪 Testing the Installation

### Test 1: Admin Login
1. Go to: `http://localhost/AgroConnect/admin_login.html`
2. Use admin credentials
3. Should see admin dashboard

### Test 2: Farmer Registration
1. Go to: `http://localhost/AgroConnect/farmer_register.html`
2. Register new farmer
3. Check database:
```sql
SELECT * FROM farmers ORDER BY farmer_id DESC LIMIT 1;
SELECT * FROM activity_logs ORDER BY log_id DESC LIMIT 1;
```

### Test 3: Add Crop
1. Login as farmer
2. Add new crop
3. Verify:
```sql
SELECT * FROM crops WHERE is_deleted = FALSE ORDER BY crop_id DESC LIMIT 1;
SELECT * FROM activity_logs WHERE action = 'add_crop' ORDER BY log_id DESC LIMIT 1;
```

### Test 4: Search Functionality
1. Go to: `http://localhost/AgroConnect/search.html`
2. Search for crops
3. Check analytics:
```sql
SELECT * FROM search_analytics ORDER BY search_id DESC LIMIT 5;
```

---

## 📊 Database Performance

### Indexes
All tables have optimized indexes for:
- Primary keys (auto-indexed)
- Foreign keys (auto-indexed)
- Frequently queried columns
- Composite indexes for common filter combinations
- Fulltext index for crop search

### Query Optimization
- Use views for complex queries
- Use stored procedures for repeated operations
- Profit is a generated column (auto-calculated)

---

## 🔒 Security Features

1. **Password Hashing**: Using PHP `password_hash()`
2. **Prepared Statements**: All queries use parameterized statements
3. **SQL Injection Prevention**: Bound parameters throughout
4. **Session Management**: Secure token-based sessions
5. **Activity Logging**: Complete audit trail
6. **Input Validation**: Server-side validation on all inputs

---

## 📝 Database Maintenance

### Regular Tasks

#### Weekly
```sql
-- Cleanup old search analytics (older than 30 days)
DELETE FROM search_analytics WHERE searched_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

#### Monthly
```sql
-- Analyze table performance
ANALYZE TABLE farmers, crops, activity_logs;

-- Optimize tables
OPTIMIZE TABLE farmers, crops, activity_logs;
```

#### Quarterly
```sql
-- Archive old activity logs
CREATE TABLE activity_logs_archive LIKE activity_logs;
INSERT INTO activity_logs_archive 
SELECT * FROM activity_logs 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

DELETE FROM activity_logs 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Backup
```bash
# Full backup
mysqldump -u root -p agroconnect > agroconnect_backup_$(date +%Y%m%d).sql

# Tables only (no data)
mysqldump -u root -p --no-data agroconnect > agroconnect_schema_only.sql

# Data only (no structure)
mysqldump -u root -p --no-create-info agroconnect > agroconnect_data_only.sql
```

---

## 🎓 Database Query Examples

### Get Farmer's Total Statistics
```sql
SELECT * FROM v_farmer_stats WHERE farmer_id = 1;
```

### Get All Active Crops with Profit
```sql
SELECT 
    crop_name, 
    category,
    investment, 
    turnover, 
    profit,
    (profit / investment * 100) AS profit_percentage
FROM crops 
WHERE is_deleted = FALSE 
ORDER BY profit DESC;
```

### Get Top Performing Farmers
```sql
SELECT 
    farmer_id,
    name,
    region,
    total_crops,
    total_profit
FROM v_farmer_stats
ORDER BY total_profit DESC
LIMIT 10;
```

### Get Recent Activity Logs
```sql
SELECT 
    user_type,
    user_id,
    action,
    description,
    created_at
FROM activity_logs
ORDER BY created_at DESC
LIMIT 50;
```

### Get Search Trends
```sql
SELECT 
    search_query,
    COUNT(*) AS search_count,
    AVG(results_count) AS avg_results
FROM search_analytics
WHERE searched_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY search_query
ORDER BY search_count DESC
LIMIT 10;
```

---

## 🌐 API Endpoints (PHP Files)

### Farmer Endpoints
- `register_farmer.php` - Register new farmer
- `login_farmer.php` - Farmer login
- `add_crop.php` - Add new crop
- `edit_crop.php` - Update crop
- `delete_crop.php` - Delete crop (soft delete)
- `fetch_crops.php` - Get farmer's crops
- `get_farmer_profile.php` - Get profile + stats
- `update_profile.php` - Update profile
- `logout.php` - Logout

### Admin Endpoints
- `admin_login.php` - Admin login
- `admin_get_farmers.php` - Get all farmers
- `admin_get_crops.php` - Get all crops
- `admin_delete_crop.php` - Delete crop
- `admin_block_farmer.php` - Block farmer
- `admin_unblock_farmer.php` - Unblock farmer
- `logout.php` - Logout

### Public Endpoints
- `search_crops.php` - Search crops (public)

---

## 📞 Support & Next Steps

### After Installation:

1. ✅ Test all functionalities
2. ✅ Change default passwords
3. ✅ Configure system settings
4. ✅ Add your own data
5. ✅ Customize as needed

### Need Help?

- Check troubleshooting section
- Review PHP error logs: `C:\xampp\php\logs\php_error_log`
- Check MySQL error logs: `C:\xampp\mysql\data\mysql_error.log`
- Verify Apache is running on port 80
- Verify MySQL is running on port 3306

---

## 🎉 Congratulations!

Your AgroConnect database is now set up with:
- ✅ 9 tables with proper relationships
- ✅ 3 optimized views
- ✅ 3 stored procedures
- ✅ 5 automated triggers
- ✅ Complete activity logging
- ✅ Session management
- ✅ Sample data for testing

**Ready to use at**: `http://localhost/AgroConnect`

---

**Built with ❤️ for the farming community**
*AgroConnect - Empowering Farmers Through Technology*
