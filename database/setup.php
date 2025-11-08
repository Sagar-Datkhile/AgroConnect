<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroConnect Database Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2em;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95em;
        }
        
        .info-box {
            background: #f0f7ff;
            border-left: 4px solid #0066CC;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .info-box h3 {
            color: #0066CC;
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        
        .info-box p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        
        .config-form {
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95em;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #0066CC;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }
        
        .btn {
            background: linear-gradient(135deg, #0066CC, #004C99);
            color: white;
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            font-size: 1.05em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 102, 204, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .result {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            font-size: 0.95em;
            line-height: 1.8;
        }
        
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #0066CC;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
        
        .step.success .step-icon {
            background: #28a745;
        }
        
        .step.error .step-icon {
            background: #dc3545;
        }
        
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            margin-top: 10px;
            font-size: 0.9em;
        }
        
        .credentials {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        
        .credentials h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .credentials code {
            background: #fff;
            padding: 3px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌾 AgroConnect Database Setup</h1>
        <p class="subtitle">Automated installation for MySQL database schema</p>
        
        <div class="info-box">
            <h3>📋 Prerequisites</h3>
            <p>✓ XAMPP installed with Apache and MySQL running</p>
            <p>✓ PHP 7.4 or higher</p>
            <p>✓ MySQL 5.7 or higher</p>
        </div>
        
        <form method="POST" class="config-form">
            <div class="form-group">
                <label for="host">Database Host:</label>
                <input type="text" id="host" name="host" value="localhost" required>
            </div>
            
            <div class="form-group">
                <label for="username">Database Username:</label>
                <input type="text" id="username" name="username" value="root" required>
            </div>
            
            <div class="form-group">
                <label for="password">Database Password:</label>
                <input type="password" id="password" name="password" value="" placeholder="Leave empty for default XAMPP">
            </div>
            
            <button type="submit" name="setup" class="btn">🚀 Install Database</button>
        </form>
        
        <?php
        if (isset($_POST['setup'])) {
            $host = $_POST['host'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            echo '<div class="result success">';
            echo '<h3 style="margin-bottom: 15px;">Installation Progress</h3>';
            
            try {
                // Step 1: Connect to MySQL
                echo '<div class="step success">';
                echo '<div class="step-icon">✓</div>';
                echo '<div><strong>Step 1:</strong> Connecting to MySQL server...</div>';
                echo '</div>';
                
                $conn = new mysqli($host, $username, $password);
                
                if ($conn->connect_error) {
                    throw new Exception("Connection failed: " . $conn->connect_error);
                }
                
                // Step 2: Read SQL file
                echo '<div class="step success">';
                echo '<div class="step-icon">✓</div>';
                echo '<div><strong>Step 2:</strong> Reading database schema file...</div>';
                echo '</div>';
                
                $sqlFile = __DIR__ . '/agroconnect_schema.sql';
                if (!file_exists($sqlFile)) {
                    throw new Exception("SQL file not found at: " . $sqlFile);
                }
                
                $sql = file_get_contents($sqlFile);
                
                // Step 3: Execute SQL
                echo '<div class="step success">';
                echo '<div class="step-icon">✓</div>';
                echo '<div><strong>Step 3:</strong> Executing SQL statements...</div>';
                echo '</div>';
                
                // Split SQL file by delimiters and execute
                $sql = str_replace('DELIMITER //', '', $sql);
                $sql = str_replace('DELIMITER ;', '', $sql);
                $sql = str_replace('//', ';', $sql);
                
                // Execute multi-query
                if ($conn->multi_query($sql)) {
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        }
                    } while ($conn->more_results() && $conn->next_result());
                }
                
                // Check for errors
                if ($conn->error) {
                    throw new Exception("SQL Error: " . $conn->error);
                }
                
                // Step 4: Verify installation
                echo '<div class="step success">';
                echo '<div class="step-icon">✓</div>';
                echo '<div><strong>Step 4:</strong> Verifying database structure...</div>';
                echo '</div>';
                
                $conn->select_db('agroconnect');
                $result = $conn->query("SHOW TABLES");
                $tables = [];
                while ($row = $result->fetch_array()) {
                    $tables[] = $row[0];
                }
                
                echo '<div class="step success">';
                echo '<div class="step-icon">✓</div>';
                echo '<div><strong>Step 5:</strong> Database installed successfully!</div>';
                echo '</div>';
                
                echo '<div style="margin-top: 20px; padding: 15px; background: #e8f5e9; border-radius: 8px;">';
                echo '<strong>✅ Installation Complete!</strong><br><br>';
                echo '<strong>Created Database:</strong> agroconnect<br>';
                echo '<strong>Total Tables:</strong> ' . count($tables) . '<br>';
                echo '<strong>Tables:</strong> ' . implode(', ', $tables);
                echo '</div>';
                
                echo '<div class="credentials">';
                echo '<h4>🔐 Default Login Credentials</h4>';
                echo '<p><strong>Admin Login:</strong></p>';
                echo '<p>Email: <code>admin@agroconnect.com</code></p>';
                echo '<p>Password: <code>password123</code></p>';
                echo '<br>';
                echo '<p><strong>Sample Farmer:</strong></p>';
                echo '<p>Email: <code>rajesh.kumar@example.com</code></p>';
                echo '<p>Password: <code>Test@123</code></p>';
                echo '<br>';
                echo '<p style="color: #856404; font-size: 0.9em;">⚠️ <strong>Important:</strong> Change these passwords in production!</p>';
                echo '</div>';
                
                echo '<div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 8px;">';
                echo '<strong>📁 Next Steps:</strong><br>';
                echo '1. Update <code>php/db_connect.php</code> if you used non-default credentials<br>';
                echo '2. Test the application at <code>http://localhost/AgroConnect</code><br>';
                echo '3. Login as admin to configure settings<br>';
                echo '4. Register new farmers or use sample accounts';
                echo '</div>';
                
                $conn->close();
                
            } catch (Exception $e) {
                echo '<div class="step error">';
                echo '<div class="step-icon">✗</div>';
                echo '<div><strong>Error:</strong> ' . $e->getMessage() . '</div>';
                echo '</div>';
                echo '<div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 6px;">';
                echo '<strong>💡 Troubleshooting Tips:</strong><br>';
                echo '• Make sure MySQL is running in XAMPP<br>';
                echo '• Verify your database credentials<br>';
                echo '• Check if port 3306 is not blocked<br>';
                echo '• Ensure agroconnect_schema.sql exists in database/ folder';
                echo '</div>';
            }
            
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
