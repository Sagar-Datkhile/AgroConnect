<?php
/**
 * Admin Login Handler
 * Updated for new database schema with session tracking
 */
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
    
    // Fetch admin details
    $stmt = $conn->prepare("SELECT admin_id, name, email, password, role, is_active FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid admin credentials.']);
        exit;
    }
    
    $admin = $result->fetch_assoc();
    
    // Check if admin is active
    if (!$admin['is_active']) {
        echo json_encode(['success' => false, 'message' => 'Your admin account is inactive.']);
        exit;
    }
    
    // Direct password comparison (for sample data - use password_hash in production)
    if ($password === $admin['password']) {
        // Generate session token
        $session_token = bin2hex(random_bytes(32));
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        // Insert session record
        $session_stmt = $conn->prepare(
            "INSERT INTO admin_sessions (admin_id, session_token, ip_address, user_agent) 
             VALUES (?, ?, ?, ?)"
        );
        $session_stmt->bind_param("isss", $admin['admin_id'], $session_token, $ip_address, $user_agent);
        $session_stmt->execute();
        $session_stmt->close();
        
        // Set session variables
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['is_admin'] = true;
        $_SESSION['session_token'] = $session_token;
        
        // Log login activity
        log_activity($conn, 'admin', $admin['admin_id'], 'login', 'admin', $admin['admin_id'], "Admin logged in");
        
        echo json_encode(['success' => true, 'message' => 'Admin login successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid admin credentials.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

