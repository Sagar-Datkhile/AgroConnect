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
    
    // Fetch farmer details
    $stmt = $conn->prepare("SELECT farmer_id, name, email, password, region, is_blocked FROM farmers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }
    
    $farmer = $result->fetch_assoc();
    
    // Check if farmer is blocked
    if ($farmer['is_blocked']) {
        echo json_encode(['success' => false, 'message' => 'Your account has been blocked. Contact admin.']);
        exit;
    }
    
    // Verify password
    if (password_verify($password, $farmer['password'])) {
        // Set session variables
        $_SESSION['farmer_id'] = $farmer['farmer_id'];
        $_SESSION['farmer_name'] = $farmer['name'];
        $_SESSION['farmer_email'] = $farmer['email'];
        $_SESSION['farmer_region'] = $farmer['region'];
        
        echo json_encode(['success' => true, 'message' => 'Login successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

