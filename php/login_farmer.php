<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT farmer_id, name, email, password, region, soil_type, area, is_blocked FROM farmers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }
    
    $farmer = $result->fetch_assoc();
    
    if ($farmer['is_blocked']) {
        echo json_encode(['success' => false, 'message' => 'Your account has been blocked. Contact admin.']);
        exit;
    }
    
    if (password_verify($password, $farmer['password'])) {
        $session_token = bin2hex(random_bytes(32));
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $session_stmt = $conn->prepare(
            "INSERT INTO farmer_sessions (farmer_id, session_token, ip_address, user_agent) 
             VALUES (?, ?, ?, ?)"
        );
        $session_stmt->bind_param("isss", $farmer['farmer_id'], $session_token, $ip_address, $user_agent);
        $session_stmt->execute();
        $session_stmt->close();
        
        $_SESSION['farmer_id'] = $farmer['farmer_id'];
        $_SESSION['farmer_name'] = $farmer['name'];
        $_SESSION['farmer_email'] = $farmer['email'];
        $_SESSION['farmer_region'] = $farmer['region'];
        $_SESSION['farmer_soil_type'] = $farmer['soil_type'];
        $_SESSION['farmer_area'] = $farmer['area'];
        $_SESSION['session_token'] = $session_token;
        
        log_activity($conn, 'farmer', $farmer['farmer_id'], 'login', 'farmer', $farmer['farmer_id'], "Farmer logged in");
        
        echo json_encode(['success' => true, 'message' => 'Login successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

