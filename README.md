# 🌾 AgroConnect – Farmer Portal

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)

A comprehensive web portal that connects farmers with agricultural data management. Farmers can register, manage their profiles, post crop details, and track investments. Users can search for crops by various filters, while admins can moderate content and manage users.

---

## 📋 Table of Contents

- [Project Overview](#-project-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Database Schema](#-database-schema)
- [Installation & Setup](#-installation--setup)
- [Usage Guide](#-usage-guide)
- [Project Structure](#-project-structure)
- [Screenshots](#-screenshots)
- [Default Credentials](#-default-credentials)
- [Future Enhancements](#-future-enhancements)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 Project Overview

**AgroConnect** is a full-stack web application built with vanilla PHP, MySQL, HTML, CSS, and JavaScript. It provides a complete ecosystem for:

- **Farmers**: Register, login, manage profiles, post crop details with investment/turnover data
- **Users/Visitors**: Search and discover crops by name, region, and farm area
- **Admins**: Comprehensive moderation panel to manage farmers and crop posts

The application features a modern, professional UI with a clean blue and white color scheme, rounded components, and responsive design.

---

## ✨ Features

### 👨‍🌾 Farmer Portal

- **Registration System**
  - Complete farmer profile creation
  - Fields: Name, Email, Password, Region, Soil Type (15 options), Farm Area
  - Email uniqueness validation
  - Secure password hashing (bcrypt)
  - Real-time form validation
  - **15 Soil Type Options**: Alluvial, Black, Red, Laterite, Desert, Mountain, Forest, Saline, Peaty, Yellow, Marshy, Skeletal, Calcareous, Alkaline, Mixed

- **Authentication**
  - Secure login with PHP sessions
  - Session-based access control
  - Blocked user prevention

- **Farmer Dashboard**
  - Welcome banner with personalized information
  - Dashboard statistics (total posts, investment, turnover)
  - Navigation: Dashboard, Add Crop, My Posts, Profile, Logout

- **Crop Management**
  - Add new crop posts (Crop Name, Investment, Turnover, Description)
  - View all personal crop posts in organized table
  - Edit existing crop details
  - Delete crop posts with confirmation

- **Profile Management**
  - Update personal information
  - Edit region, soil type, and farm area
  - Real-time session updates

### 🔍 User Portal (Public Search)

- **Home Page**
  - Professional landing page
  - Feature showcase
  - Quick access to registration and search

- **Advanced Crop Search**
  - Filter by crop name (partial match)
  - Filter by region
  - Filter by minimum farm area
  - Real-time search with debouncing
  - Detailed crop cards showing:
    - Crop name and description
    - Farmer details
    - Soil type and region
    - Investment and turnover data
    - Farm area

### 🧑‍💼 Admin Panel

- **Admin Authentication**
  - Secure admin login
  - Separate session management

- **Dashboard Overview**
  - Total farmers count
  - Total crop posts count
  - Blocked farmers count

- **Farmer Management**
  - View all registered farmers in organized table
  - Search/filter farmers by name, email, or region
  - Block farmers (prevents login and access)
  - View farmer details (name, email, region, soil type, farm area, status)
  - Real-time status updates (Active/Blocked badges)

- **Crop Post Moderation**
  - View all crop posts across platform in sortable table
  - Search/filter posts by crop name, farmer, or region
  - Delete inappropriate content with confirmation
  - View detailed farmer information for each post
  - Right-aligned monetary values for better readability

- **Blocked Users Management**
  - Dedicated section displaying all blocked farmers
  - Comprehensive blocked user information table
  - One-click unblock functionality with confirmation
  - Real-time updates when users are blocked/unblocked
  - Empty state when no users are blocked

- **Recently Deleted Posts**
  - View posts deleted in last 30 days
  - Track admin moderation actions
  - Audit trail for content management

---

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

## 🗄️ Database Schema

### Database Name: `agroconnect`

The database uses **UTF-8MB4** character encoding for proper support of all characters.

#### Core Tables

**1. `farmers`** - Stores farmer profiles and authentication
- Primary Key: `farmer_id`
- Includes: name, email, hashed password, region, soil type, farm area
- Security: `is_blocked` flag for admin moderation
- Timestamps: `registration_date`, `last_login`, `updated_at`

**2. `crops`** - Manages crop posts by farmers
- Primary Key: `crop_id`
- Foreign Key: `farmer_id` (cascading delete)
- Fields: crop_name, investment, turnover, description
- Soft delete support: `is_deleted`, `deleted_at`, `deleted_by`
- Auto-calculated: `profit` (generated column)

**3. `admins`** - Admin user accounts
- Primary Key: `admin_id`
- Roles: super_admin, admin, moderator
- Fields: name, email, hashed password, role, is_active

**4. `farmer_blocks`** - Tracks blocking/unblocking history
- Records admin actions with reasons
- Supports block history and unblock tracking

**5. `activity_logs`** - Comprehensive audit trail
- Logs all user actions (farmer and admin)
- Includes IP address and user agent
- Enables security monitoring and analytics

**6. `farmer_sessions` & `admin_sessions`** - Session management
- Tracks active logins
- Security monitoring with IP and user agent

**7. `search_analytics`** - Search query tracking
- Analyzes user search patterns
- Helps improve crop discovery

Full schema available in `database/agroconnect_schema.sql`

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

**Option A: Using MySQL Workbench (Recommended)**
1. Open MySQL Workbench
2. Connect to local instance (localhost:3306, user: root, no password)
3. Click **File** → **Open SQL Script**
4. Navigate to `AgroConnect/database/` folder and open `agroconnect_schema.sql`
5. Click the **Execute** button (lightning icon)
6. Verify all tables are created successfully

**Option B: Using phpMyAdmin**
1. Open browser and go to `http://localhost/phpmyadmin`
2. Click **New** to create a database
3. Name it `agroconnect` and select `utf8mb4_unicode_ci` collation
4. Click **Create**
5. Click on the `agroconnect` database
6. Go to **Import** tab
7. Choose `database/agroconnect_schema.sql` file
8. Click **Go** and wait for completion

**Option C: Using Command Line**
```bash
mysql -u root -p < database/agroconnect_schema.sql
```
(Press Enter when prompted for password if you haven't set one)

**Note**: The schema file includes:
- All 8 database tables with proper relationships
- Indexes for performance optimization
- Database triggers for automatic updates
- UTF-8MB4 character set for full Unicode support
- Sample admin account (admin@agroconnect.com / Admin@123)

#### 4. Deploy Project Files

1. Copy the entire `agroconnect` project folder
2. Paste it into `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux)
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

## 📖 Usage Guide

### For Farmers

1. **Registration**
   - Go to `http://localhost/agroconnect/farmer_register.html`
   - Fill in all required fields
   - Submit to create account
   - You'll be redirected to login page

2. **Login**
   - Navigate to Farmer Login
   - Enter registered email and password
   - Access your dashboard

3. **Dashboard Features**
   - View statistics of your posts
   - Add new crop posts
   - Edit or delete existing posts
   - Update your profile information

### For Users/Visitors

1. **Home Page**
   - Explore features
   - Quick navigation to search

2. **Search Crops**
   - Go to Search page
   - Use filters (crop name, region, area)
   - View detailed crop information
   - Search updates automatically as you type

### For Admins

1. **Admin Login**
   - Navigate to Admin Login page
   - Use default credentials (see below)
   - Access admin dashboard

2. **Admin Functions**
   - View overview statistics
   - Manage farmers (block/unblock)
   - Delete inappropriate crop posts
   - Monitor blocked users

---

## 📁 Project Structure

```
agroconnect/
│
├── index.html                  # Home page
├── farmer_register.html        # Farmer registration
├── farmer_login.html           # Farmer login
├── farmer_dashboard.html       # Farmer dashboard (protected)
├── search.html                 # Public crop search
├── admin_login.html            # Admin login
├── admin_dashboard.html        # Admin panel (protected)
├── agroconnect.sql            # Database schema & sample data
├── README.md                   # Project documentation
│
├── css/
│   └── style.css              # Global styles (blue & white theme)
│
├── js/
│   ├── script.js              # Main JavaScript (search, UI logic)
│   └── validation.js          # Form validation functions
│
├── database/
│   └── agroconnect_schema.sql # Complete database schema with all tables
│
└── php/
    ├── db_connect.php              # MySQL connection with UTF-8MB4 support
    ├── register_farmer.php         # Farmer registration handler
    ├── login_farmer.php            # Farmer login handler
    ├── logout.php                  # Session destroy & logout
    ├── add_crop.php                # Add new crop post
    ├── edit_crop.php               # Edit existing crop
    ├── delete_crop.php             # Delete crop (soft delete)
    ├── fetch_crops.php             # Get farmer's crops
    ├── get_farmer_profile.php      # Get profile data
    ├── update_profile.php          # Update farmer profile
    ├── search_crops.php            # Public crop search with filters
    ├── check_session.php           # Verify farmer session
    ├── admin_login.php             # Admin authentication
    ├── admin_get_farmers.php       # Get all farmers (with block status)
    ├── admin_get_crops.php         # Get all crops across platform
    ├── admin_get_deleted_crops.php # Get recently deleted posts
    ├── admin_delete_crop.php       # Admin delete crop with logging
    ├── admin_block_farmer.php      # Block farmer with reason tracking
    ├── admin_unblock_farmer.php    # Unblock farmer with logging
    ├── check_admin_session.php     # Verify admin session
    ├── migrate_database.php        # Database migration utility
    └── verify_database.php         # Database verification script
```

---

## 🖼️ Screenshots

### Home Page
- Professional landing page with hero section
- Feature cards showcasing portal capabilities
- Clean navigation with blue and white theme

### Farmer Dashboard
- Personalized welcome banner
- Statistics cards (total posts, investment, turnover)
- Sidebar navigation
- Tabbed sections for different features

### Search Interface
- Advanced filters (crop name, region, area)
- Real-time search results
- Detailed crop cards with all information
- Responsive grid layout

### Admin Panel
- Overview dashboard with key metrics
- Comprehensive farmer management table
- Crop moderation interface
- Blocked users section

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

**⚠️ Security Note**: These are development credentials. In production:
- Use strong, unique passwords
- Implement password hashing (already included for farmer registration)
- Use HTTPS
- Add CSRF protection
- Implement rate limiting

---

## 🔮 Future Enhancements

### Phase 1 - Enhanced Features
- [ ] Password reset functionality via email
- [ ] Profile picture upload for farmers
- [ ] Crop image uploads
- [ ] Advanced analytics dashboard
- [ ] Export data to PDF/Excel

### Phase 2 - Data Integration
- [ ] Weather API integration for regional forecasts
- [ ] Market price data integration
- [ ] Crop yield predictions using historical data
- [ ] Soil health recommendations

### Phase 3 - Community Features
- [ ] Farmer-to-farmer messaging
- [ ] Community forum
- [ ] Success stories showcase
- [ ] Expert Q&A section
- [ ] Rating system for crops

### Phase 4 - Advanced Technology
- [ ] Mobile app (React Native / Flutter)
- [ ] AI-powered crop recommendations
- [ ] IoT sensor data integration
- [ ] Blockchain for supply chain tracking
- [ ] Multi-language support

### Phase 5 - Business Features
- [ ] Marketplace for direct crop sales
- [ ] Subscription plans for premium features
- [ ] Government scheme integration
- [ ] Loan and insurance information
- [ ] Logistics and transportation

---

## 🎨 Design System

### Color Palette
- **Primary Blue**: `#0066CC`
- **Hover Blue**: `#0052A3`
- **Dark Blue**: `#004C99`
- **Background**: `#F9FAFB`
- **Success Green**: `#059669`
- **Error Red**: `#DC2626`
- **Text Dark**: `#333`
- **Text Gray**: `#666`
- **Border**: `#E5E7EB`

### Typography
- **Font Family**: 'Poppins', sans-serif
- **Headings**: 600-700 weight
- **Body Text**: 400 weight
- **Small Text**: 300 weight

### UI Components
- **Border Radius**: 8px - 12px
- **Box Shadow**: Soft shadows (0 2px 12px rgba(0,0,0,0.06))
- **Transitions**: 0.3s ease
- **Hover Effects**: Translate Y and enhanced shadows

---

## 🛠️ Development Notes

### Browser Compatibility
- Chrome 90+
- Firefox 88+
- Edge 90+
- Safari 14+

### PHP Requirements
- PHP 7.4 or higher
- MySQLi extension enabled
- Session support enabled

### Security Features Implemented
- Password hashing (PHP `password_hash()`)
- SQL injection prevention (prepared statements)
- Session-based authentication
- Input validation (client & server-side)
- XSS prevention (input sanitization)

### Performance Optimizations
- Debounced search (500ms delay)
- Efficient SQL queries with proper indexing
- Minimal external dependencies
- Optimized CSS with minimal reflows

---

## 🐛 Troubleshooting

### Common Issues

**Issue**: Database connection failed
- **Solution**: Check XAMPP MySQL is running, verify `db_connect.php` credentials

**Issue**: 404 Error when accessing pages
- **Solution**: Ensure project is in `htdocs/agroconnect` folder, check Apache is running

**Issue**: PHP errors displayed
- **Solution**: Check PHP error logs in `xampp/php/logs/`, ensure all PHP files have proper syntax

**Issue**: Session not persisting
- **Solution**: Check PHP session support, clear browser cookies, verify session_start() calls

**Issue**: Database tables not created
- **Solution**: Re-import `agroconnect.sql`, check for SQL errors in import log

---

## 📞 Support

For issues, questions, or contributions:
- Create an issue in the repository
- Contact: [Your contact information]
- Documentation: This README file

---

## 👏 Acknowledgments

- Google Fonts for Poppins typography
- XAMPP team for the development environment
- PHP and MySQL communities for excellent documentation

---

## 📄 License

This project is created for educational purposes. Feel free to use, modify, and distribute as needed.

---

## 🚀 Quick Start Commands

```bash
# Start XAMPP services
# Open XAMPP Control Panel → Start Apache & MySQL

# Access application
http://localhost/agroconnect

# Access phpMyAdmin
http://localhost/phpmyadmin

# Test connection
http://localhost/agroconnect/php/db_connect.php
```

---

## 📊 Project Statistics

- **Total Files**: 30+
- **Lines of Code**: ~5000+
- **Database Tables**: 8 (farmers, crops, admins, farmer_blocks, activity_logs, farmer_sessions, admin_sessions, search_analytics)
- **PHP Scripts**: 24
- **HTML Pages**: 7
- **JavaScript Functions**: 50+
- **CSS Classes**: 150+
- **Soil Types Supported**: 15
- **Admin Features**: 5 (Dashboard, Manage Farmers, Manage Crops, Blocked Users, Deleted Posts)

---

**Built with ❤️ for the farming community**

*AgroConnect - Empowering Farmers Through Technology*
