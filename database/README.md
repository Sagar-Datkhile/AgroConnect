# 🌾 AgroConnect Database - Quick Start

## 🚀 Installation (Choose One Method)

### Method 1: Web Setup (Recommended - Easiest!)
1. Start Apache + MySQL in XAMPP
2. Open: `http://localhost/AgroConnect/database/setup.php`
3. Click "Install Database"
4. Done! ✅

### Method 2: phpMyAdmin
1. Go to `http://localhost/phpmyadmin`
2. Import → Choose `agroconnect_schema.sql`
3. Click Go

### Method 3: Command Line
```bash
mysql -u root -p < agroconnect_schema.sql
```

## 📁 Files in This Directory

- **agroconnect_schema.sql** - Complete database schema with sample data
- **setup.php** - Web-based installer (easiest method)
- **README.md** - This file

## 🔐 Default Login Credentials

**Admin:**
- Email: `admin@agroconnect.com`
- Password: `password123`

**Sample Farmer:**
- Email: `rajesh.kumar@example.com`
- Password: `Test@123` (hashed in database)

## 📊 What Gets Created

- **9 Tables**: farmers, admins, crops, activity_logs, farmer_blocks, farmer_sessions, admin_sessions, search_analytics, system_settings
- **3 Views**: v_active_crops, v_farmer_stats, v_admin_dashboard
- **3 Stored Procedures**: sp_get_farmer_dashboard, sp_search_crops, sp_admin_analytics
- **5 Triggers**: Automated actions for login, deletion, blocking
- **Sample Data**: 2 admins, 5 farmers, 10 crops

## ✅ Verification

Test if installation worked:

```sql
USE agroconnect;
SHOW TABLES;
SELECT * FROM v_admin_dashboard;
```

Should show 9 tables and statistics.

## 🆘 Troubleshooting

**Problem: Database exists**
```sql
DROP DATABASE IF EXISTS agroconnect;
```
Then re-run installation.

**Problem: MySQL not running**
- Open XAMPP Control Panel
- Start MySQL service

**Problem: Permission denied**
```sql
GRANT ALL PRIVILEGES ON agroconnect.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

## 📖 Full Documentation

For complete documentation, see:
- `../INSTALLATION_GUIDE.md` - Comprehensive installation guide
- `../README.md` - Project documentation

## 🎯 New Features

✨ **Enhanced from Previous Version:**
- Activity logging for all actions
- Session tracking with tokens
- Farmer blocking history
- Search analytics
- Auto-calculated profit
- Extended crop fields (category, season, dates, quantity)
- Database views for common queries
- Stored procedures for optimization
- Automated triggers

## 📞 Need Help?

1. Check `../INSTALLATION_GUIDE.md` for detailed help
2. Test connection: `php -r "new mysqli('localhost', 'root', '', 'agroconnect');"`
3. Check logs: `C:\xampp\mysql\data\mysql_error.log`

---

**Quick Links:**
- Test Admin Panel: `http://localhost/AgroConnect/admin_login.html`
- Test Farmer Registration: `http://localhost/AgroConnect/farmer_register.html`
- Test Search: `http://localhost/AgroConnect/search.html`

---

*Database Version: 2.0 | MySQL 5.7+ | PHP 7.4+*
