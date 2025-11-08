<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agroconnect";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please check your configuration.',
        'error' => $conn->connect_error
    ]));
}


$conn->set_charset("utf8mb4");


function log_activity($conn, $user_type, $user_id, $action, $entity_type = null, $entity_id = null, $description = null) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    try {
        $stmt = $conn->prepare(
            "INSERT INTO activity_logs (user_type, user_id, action, entity_type, entity_id, description, ip_address, user_agent) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param("sisissss", $user_type, $user_id, $action, $entity_type, $entity_id, $description, $ip_address, $user_agent);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        
        error_log("Activity logging failed: " . $e->getMessage());
    }
}
?>

