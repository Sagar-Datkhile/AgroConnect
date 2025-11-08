<?php
header('Content-Type: application/json');

$response = [
    'php_working' => true,
    'php_version' => phpversion(),
    'mysql_extension' => extension_loaded('mysqli'),
    'database_status' => 'unknown',
    'tables_exist' => false,
    'error' => null
];

try {
    // Try to connect to MySQL
    $conn = new mysqli('localhost', 'root', '');
    
    if ($conn->connect_error) {
        $response['database_status'] = 'connection_failed';
        $response['error'] = $conn->connect_error;
        echo json_encode($response);
        exit;
    }
    
    $response['database_status'] = 'connected';
    
    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE 'agroconnect'");
    if ($result && $result->num_rows > 0) {
        $response['database_exists'] = true;
        
        // Select database
        $conn->select_db('agroconnect');
        
        // Check if tables exist
        $tables = $conn->query("SHOW TABLES");
        if ($tables && $tables->num_rows > 0) {
            $response['tables_exist'] = true;
            $response['table_count'] = $tables->num_rows;
            
            $tableList = [];
            while ($row = $tables->fetch_array()) {
                $tableList[] = $row[0];
            }
            $response['tables'] = $tableList;
            
            // Check if crops table has data
            $cropCount = $conn->query("SELECT COUNT(*) as count FROM crops");
            if ($cropCount) {
                $row = $cropCount->fetch_assoc();
                $response['crop_count'] = $row['count'];
            }
            
            // Check if farmers table has data
            $farmerCount = $conn->query("SELECT COUNT(*) as count FROM farmers");
            if ($farmerCount) {
                $row = $farmerCount->fetch_assoc();
                $response['farmer_count'] = $row['count'];
            }
        } else {
            $response['database_exists'] = true;
            $response['tables_exist'] = false;
        }
    } else {
        $response['database_exists'] = false;
    }
    
    $conn->close();
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
