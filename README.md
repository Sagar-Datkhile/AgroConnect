# 🌾 AgroConnect – Farmer Portal

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)

AgroConnect is a lightweight web portal that helps farmers manage crop information and track investments, while users can search crops by region, name, and farm area. Admins can moderate posts and manage farmers through a dedicated dashboard.

## 🎯 Project Overview

**AgroConnect** is a full-stack web application built with vanilla PHP, MySQL, HTML, CSS, and JavaScript. It provides a complete ecosystem for:

- **Farmers**: Register, login, manage profiles, post crop details with investment/turnover data
- **Users/Visitors**: Search and discover crops by name, region, and farm area
- **Admins**: Comprehensive moderation panel to manage farmers and crop posts

The application features a modern, professional UI with a clean blue and white color scheme, rounded components, and responsive design.

---

## ✅ Features

### 👨‍🌾 Farmer
- Register and login with secure password hashing
- Add, edit, and delete crop posts
- Update profile details (region, soil type, farm area)
- Dashboard showing total posts, investment, and turnover

### 🔍 Public Search
- Search crops by name, region, and minimum farm area
- Real-time filtering
- Detailed crop cards with farmer info

### 🧑‍💼 Admin
- Admin login & session control
- View all farmers
- Block/unblock accounts
- Delete crop posts
- View recently deleted posts

## 🧰 Tech Stack

| Component | Technology |
|-----------|-----------|
| **Frontend** | HTML5, CSS3, Vanilla JavaScript (ES6+) |
| **Backend** | PHP 7.4+ |
| **Database** | MySQL 8.0+ |
| **Server** | Apache (via XAMPP) |
| **Font** | Google Fonts (Poppins) |
| **Design** | Flexbox, CSS Grid, Responsive Design |

**No frameworks used** – Pure vanilla implementation as per requirements.

---


## 🚀 Installation & Setup

### Prerequisites

- **XAMPP** (includes Apache and MySQL)
- **MySQL Workbench** (optional, for database management)
- Modern web browser (Chrome, Firefox, Edge)

### Step-by-Step Setup

#### 1. Install XAMPP

- Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
- Install XAMPP to the default location (usually `C:\xampp` on Windows)
- Launch XAMPP Control Panel

#### 2. Start Services

- Open XAMPP Control Panel
- Click **Start** for **Apache**
- Click **Start** for **MySQL**
- Ensure both services show green "Running" status

#### 3. Create Database

**Using phpMyAdmin**
1. Open browser and go to `http://localhost/phpmyadmin`
2. Click **New** to create a database
3. Name it `agroconnect` and select `utf8mb4_unicode_ci` collation
4. Click **Create**
5. Click on the `agroconnect` database
6. Go to **Import** tab
7. Choose `database/agroconnect_schema.sql` file
8. Click **Go** and wait for completion

#### 4. Deploy Project Files

1. Copy the entire `agroconnect` project folder
2. Paste it into `C:\xampp\htdocs\` (Windows)
3. Final structure should be: `C:\xampp\htdocs\agroconnect\`

#### 5. Configure Database Connection (if needed)

The project is pre-configured for XAMPP defaults:
- **Host**: localhost
- **Username**: root
- **Password**: (empty)
- **Database**: agroconnect

If your setup differs, edit `php/db_connect.php`:
```php
$servername = "localhost";
$username = "root";
$password = ""; // Add your password if set
$dbname = "agroconnect";
```

#### 6. Access the Application

Open your web browser and navigate to:
```
http://localhost/agroconnect
```

---


## 🔐 Default Credentials

### Admin Access
```
Email: admin@agroconnect.com
Password: Admin@123
```

### Test Farmer Accounts
Create your own farmer accounts through the registration page.
All passwords are securely hashed using bcrypt.

**To create a test farmer:**
1. Go to Farmer Registration page
2. Fill in the form with test data
3. Use any email (e.g., test@example.com)
4. Set a password (minimum 6 characters)
5. Select region, soil type, and farm area
6. Submit to create account

## 📞 Support

For issues, questions, or contributions:
- Create an issue in the repository
- Contact: sagardatkhile.official@gmail.com
- Documentation: This README file

---

## 📄 License

This project is created for educational purposes. Feel free to use, modify, and distribute as needed.

---

**Built with ❤️ for the farming community** 
