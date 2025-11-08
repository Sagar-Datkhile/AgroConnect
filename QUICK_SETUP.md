# 🚀 AgroConnect - Quick Setup Guide

## ✅ Database Issue Fixed!

The invalid MySQL syntax has been removed from `agroconnect.sql`. You can now proceed with setup.

---

## 📋 Setup Steps (5 Minutes)

### 1️⃣ Start XAMPP
- Open XAMPP Control Panel
- Click **Start** next to Apache
- Click **Start** next to MySQL
- Wait for both to show green "Running" status

### 2️⃣ Create Database
Open your browser and choose ONE method:

**Method A: phpMyAdmin (Recommended)**
1. Go to: `http://localhost/phpmyadmin`
2. Click **"New"** in the left sidebar
3. Enter database name: `agroconnect`
4. Click **"Create"**
5. Select the `agroconnect` database (click on it)
6. Click **"Import"** tab at the top
7. Click **"Choose File"** and select `agroconnect.sql`
8. Scroll down and click **"Go"**
9. Wait for "Import has been successfully finished" message

**Method B: MySQL Command Line**
```bash
mysql -u root -p
# Press Enter (no password by default)

CREATE DATABASE agroconnect;
USE agroconnect;
SOURCE G:/Coding Projects/GitHub Desktop/AgroConnect/agroconnect.sql;
EXIT;
```

### 3️⃣ Verify Database Setup
Open browser and visit:
```
http://localhost/agroconnect/php/verify_database.php
```

You should see:
- ✅ Database Connection Successful
- ✅ All 3 tables exist (farmers, crops, admins)
- ✅ Soft delete features enabled

### 4️⃣ Copy Project to XAMPP (If Not Already)
If you haven't already, copy the project folder:

**Windows:**
- Copy the entire `AgroConnect` folder
- Paste into: `C:\xampp\htdocs\`
- Final path: `C:\xampp\htdocs\AgroConnect\`

### 5️⃣ Access Application
Open browser and go to:
```
http://localhost/agroconnect
```

---

## 🔐 Default Login Credentials

### Admin Login
```
Email: admin@example.com
Password: password123
```
Access at: `http://localhost/agroconnect/admin_login.html`

### Sample Farmer Accounts
```
Farmer 1:
Email: john@example.com
Password: farmer123

Farmer 2:
Email: jane@example.com
Password: farmer123
```
Access at: `http://localhost/agroconnect/farmer_login.html`

---

## 🧪 Test the Application

### Test as Farmer:
1. Login with `john@example.com` / `farmer123`
2. View Dashboard (should show 2 crop posts)
3. Click "Add Crop" and create a new crop
4. Go to "My Posts" and try editing/deleting a crop
5. Update your profile

### Test as Admin:
1. Login with `admin@example.com` / `password123`
2. View all farmers (should show 2 farmers)
3. View all crops (should show 3 sample crops)
4. Try blocking a farmer
5. Try deleting a crop

### Test Public Search:
1. Go to: `http://localhost/agroconnect/search.html`
2. Try searching for "Wheat"
3. Try filtering by region "Punjab"
4. Try filtering by minimum area

---

## ❓ Troubleshooting

### Database Connection Failed
**Problem:** "Connection failed" error
**Solution:**
- Check XAMPP MySQL is running (green status)
- Verify database name is `agroconnect` (all lowercase)
- Check `php/db_connect.php` has correct credentials:
  - Host: `localhost`
  - Username: `root`
  - Password: (empty)
  - Database: `agroconnect`

### 404 Not Found
**Problem:** Page not found error
**Solution:**
- Ensure project is in `C:\xampp\htdocs\AgroConnect\`
- Check Apache is running in XAMPP
- Use correct URL: `http://localhost/agroconnect/index.html`

### Import Failed
**Problem:** SQL import errors in phpMyAdmin
**Solution:**
- Make sure you're using the **fixed** `agroconnect.sql` file
- Drop existing database first if reimporting:
  ```sql
  DROP DATABASE IF EXISTS agroconnect;
  CREATE DATABASE agroconnect;
  ```
- Then import again

### Blank Page or PHP Errors
**Problem:** White screen or PHP error messages
**Solution:**
- Check Apache is running
- Verify PHP is enabled in XAMPP
- Check browser console (F12) for JavaScript errors
- Check `C:\xampp\apache\logs\error.log` for PHP errors

---

## 📊 What Was Fixed?

The database had **invalid MySQL syntax** that prevented proper setup:

**Before (❌ Broken):**
```sql
ALTER TABLE crops ADD COLUMN IF NOT EXISTS is_deleted BOOLEAN DEFAULT FALSE;
ALTER TABLE crops ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL;
```

**After (✅ Fixed):**
These redundant statements were removed since the columns are already created in the main table definition.

**Result:** Database now imports successfully without errors! 🎉

---

## 📁 Project Structure

```
AgroConnect/
├── agroconnect.sql              ← Fixed database schema
├── DATABASE_FIX_REPORT.md       ← Detailed fix documentation
├── QUICK_SETUP.md               ← This file
├── README.md                    ← Full project documentation
├── php/
│   ├── verify_database.php      ← NEW: Database health check
│   ├── db_connect.php           ← Database connection
│   └── [other PHP files]
├── index.html                   ← Home page
├── farmer_login.html
├── farmer_register.html
├── farmer_dashboard.html
├── admin_login.html
├── admin_dashboard.html
└── search.html
```

---

## 🎯 Next Steps

1. ✅ **Setup Complete?** Start building features!
2. 📝 **Want Details?** Read `DATABASE_FIX_REPORT.md`
3. 📚 **Need Full Docs?** Check `README.md`
4. 🔍 **Verify Database?** Run `verify_database.php`

---

## 🌟 Features Available

### For Farmers:
- ✅ Registration & Login
- ✅ Dashboard with statistics
- ✅ Add/Edit/Delete crop posts
- ✅ Profile management
- ✅ Investment & turnover tracking

### For Admins:
- ✅ Admin dashboard
- ✅ View all farmers
- ✅ Block/Unblock farmers
- ✅ Delete inappropriate crops
- ✅ View deleted crops history

### For Public:
- ✅ Search crops by name
- ✅ Filter by region
- ✅ Filter by farm area
- ✅ View farmer details

---

## 💡 Tips

1. **Soft Delete Enabled**: Deleted crops are kept in database for 30 days (not permanently removed)
2. **Password Security**: Farmer passwords are hashed, admin password is plain text (change for production)
3. **Sample Data**: The SQL file includes 2 farmers and 3 crops for testing
4. **No Framework**: Pure PHP, MySQL, HTML, CSS, JavaScript (no dependencies)

---

## ✅ Setup Checklist

- [ ] XAMPP installed and running
- [ ] Database `agroconnect` created
- [ ] `agroconnect.sql` imported successfully
- [ ] Verification script shows all green ✅
- [ ] Can access home page: `http://localhost/agroconnect`
- [ ] Can login as farmer
- [ ] Can login as admin
- [ ] Can search crops publicly

---

**🎉 You're all set! Start exploring AgroConnect!**

Need help? Check `DATABASE_FIX_REPORT.md` for detailed technical information.
