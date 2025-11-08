<?php
/**
 * Database Verification Script
 * This script checks and reports on the database structure
 */

require_once 'db_connect.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>AgroConnect Database Verification</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #0066CC; border-bottom: 3px solid #0066CC; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; }
        .success { color: #059669; font-weight: bold; }
        .error { color: #DC2626; font-weight: bold; }
        .warning { color: #F59E0B; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #0066CC; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .info-box { background: #E0F2FE; padding: 15px; border-left: 4px solid #0066CC; margin: 15px 0; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 4px; color: white; font-size: 12px; }
        .status.ok { background: #059669; }
        .status.missing { background: #DC2626; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🌾 AgroConnect Database Verification</h1>";

// Check connection
if ($conn->connect_error) {
    echo "<p class='error'>❌ Connection Failed: " . $conn->connect_error . "</p>";
    echo "</div></body></html>";
    exit;
}

echo "<p class='success'>✅ Database Connection Successful</p>";
echo "<p><strong>Server:</strong> " . htmlspecialchars($conn->host_info) . "</p>";
echo "<p><strong>Database:</strong> agroconnect</p>";

// Check if database exists
$db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'agroconnect'");
if ($db_check && $db_check->num_rows > 0) {
    echo "<p class='success'>✅ Database 'agroconnect' exists</p>";
} else {
    echo "<p class='error'>❌ Database 'agroconnect' not found. Please import agroconnect.sql</p>";
}

// Required tables
$required_tables = ['farmers', 'crops', 'admins'];
$existing_tables = [];

echo "<h2>📊 Table Structure</h2>";

foreach ($required_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p class='success'>✅ Table '$table' exists</p>";
        $existing_tables[] = $table;
        
        // Get column info
        $columns = $conn->query("DESCRIBE $table");
        if ($columns) {
            echo "<table>";
            echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($col = $columns->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($col['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p class='error'>❌ Table '$table' missing</p>";
    }
}

// Check for soft delete columns in crops table
echo "<h2>🔍 Feature Verification</h2>";

if (in_array('crops', $existing_tables)) {
    $is_deleted_check = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
    $deleted_at_check = $conn->query("SHOW COLUMNS FROM crops LIKE 'deleted_at'");
    
    if ($is_deleted_check && $is_deleted_check->num_rows > 0) {
        echo "<p class='success'>✅ Soft delete feature enabled (is_deleted column exists)</p>";
    } else {
        echo "<p class='warning'>⚠️ Soft delete feature disabled (is_deleted column missing)</p>";
        echo "<div class='info-box'>Run migrate_database.php to add soft delete columns</div>";
    }
    
    if ($deleted_at_check && $deleted_at_check->num_rows > 0) {
        echo "<p class='success'>✅ Delete timestamp tracking enabled (deleted_at column exists)</p>";
    } else {
        echo "<p class='warning'>⚠️ Delete timestamp tracking disabled (deleted_at column missing)</p>";
    }
}

// Check sample data
echo "<h2>📈 Data Statistics</h2>";

if (in_array('farmers', $existing_tables)) {
    $farmer_count = $conn->query("SELECT COUNT(*) as count FROM farmers");
    $farmer_row = $farmer_count->fetch_assoc();
    echo "<p><strong>Total Farmers:</strong> " . $farmer_row['count'] . "</p>";
    
    $blocked_count = $conn->query("SELECT COUNT(*) as count FROM farmers WHERE is_blocked = TRUE");
    $blocked_row = $blocked_count->fetch_assoc();
    echo "<p><strong>Blocked Farmers:</strong> " . $blocked_row['count'] . "</p>";
}

if (in_array('crops', $existing_tables)) {
    $crop_count = $conn->query("SELECT COUNT(*) as count FROM crops");
    $crop_row = $crop_count->fetch_assoc();
    echo "<p><strong>Total Crops:</strong> " . $crop_row['count'] . "</p>";
    
    // Check if soft delete exists
    $deleted_check = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
    if ($deleted_check && $deleted_check->num_rows > 0) {
        $deleted_count = $conn->query("SELECT COUNT(*) as count FROM crops WHERE is_deleted = TRUE");
        $deleted_row = $deleted_count->fetch_assoc();
        echo "<p><strong>Deleted Crops:</strong> " . $deleted_row['count'] . "</p>";
    }
}

if (in_array('admins', $existing_tables)) {
    $admin_count = $conn->query("SELECT COUNT(*) as count FROM admins");
    $admin_row = $admin_count->fetch_assoc();
    echo "<p><strong>Total Admins:</strong> " . $admin_row['count'] . "</p>";
}

// Foreign key verification
echo "<h2>🔗 Relationships & Constraints</h2>";

$fk_query = "SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'agroconnect' 
AND REFERENCED_TABLE_NAME IS NOT NULL";

$fk_result = $conn->query($fk_query);
if ($fk_result && $fk_result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Constraint</th><th>Table</th><th>Column</th><th>References</th></tr>";
    while ($fk = $fk_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($fk['CONSTRAINT_NAME']) . "</td>";
        echo "<td>" . htmlspecialchars($fk['TABLE_NAME']) . "</td>";
        echo "<td>" . htmlspecialchars($fk['COLUMN_NAME']) . "</td>";
        echo "<td>" . htmlspecialchars($fk['REFERENCED_TABLE_NAME']) . "." . htmlspecialchars($fk['REFERENCED_COLUMN_NAME']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>⚠️ No foreign key constraints found</p>";
}

// Recommendations
echo "<h2>💡 Recommendations</h2>";
echo "<div class='info-box'>";

$recommendations = [];

if (!in_array('farmers', $existing_tables) || !in_array('crops', $existing_tables) || !in_array('admins', $existing_tables)) {
    $recommendations[] = "Import the agroconnect.sql file to create missing tables";
}

$is_deleted_exists = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
if (!$is_deleted_exists || $is_deleted_exists->num_rows == 0) {
    $recommendations[] = "Run migrate_database.php to enable soft delete functionality";
}

if (in_array('admins', $existing_tables)) {
    $admin_check = $conn->query("SELECT COUNT(*) as count FROM admins WHERE email = 'admin@example.com'");
    $admin_data = $admin_check->fetch_assoc();
    if ($admin_data['count'] == 0) {
        $recommendations[] = "Create default admin account (run SQL: INSERT INTO admins (email, password) VALUES ('admin@example.com', 'password123'))";
    }
}

if (empty($recommendations)) {
    echo "<p class='success'>✅ Database is properly configured!</p>";
} else {
    echo "<ul>";
    foreach ($recommendations as $rec) {
        echo "<li>" . htmlspecialchars($rec) . "</li>";
    }
    echo "</ul>";
}

echo "</div>";

echo "<h2>✅ Summary</h2>";
echo "<p>Database verification completed successfully. The AgroConnect database is ";
if (count($existing_tables) == 3) {
    echo "<span class='success'>ready to use!</span>";
} else {
    echo "<span class='error'>incomplete and requires setup.</span>";
}
echo "</p>";

echo "<div class='info-box'>";
echo "<strong>Next Steps:</strong><br>";
echo "1. If tables are missing, import <code>agroconnect.sql</code> via phpMyAdmin<br>";
echo "2. Test the application at <code>http://localhost/agroconnect</code><br>";
echo "3. Login with default admin credentials: admin@example.com / password123";
echo "</div>";

echo "</div></body></html>";

$conn->close();
?>
