<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['farmer_id']) && isset($_SESSION['session_token'])) {
    $farmer_id = $_SESSION['farmer_id'];
    $session_token = $_SESSION['session_token'];
    
    $stmt = $conn->prepare(
        "UPDATE farmer_sessions SET logout_at = NOW(), is_active = FALSE 
         WHERE farmer_id = ? AND session_token = ?"
    );
    $stmt->bind_param("is", $farmer_id, $session_token);
    $stmt->execute();
    $stmt->close();
    
    log_activity($conn, 'farmer', $farmer_id, 'logout', 'farmer', $farmer_id, "Farmer logged out");
} elseif (isset($_SESSION['admin_id']) && isset($_SESSION['session_token'])) {
    $admin_id = $_SESSION['admin_id'];
    $session_token = $_SESSION['session_token'];
    
    $stmt = $conn->prepare(
        "UPDATE admin_sessions SET logout_at = NOW(), is_active = FALSE 
         WHERE admin_id = ? AND session_token = ?"
    );
    $stmt->bind_param("is", $admin_id, $session_token);
    $stmt->execute();
    $stmt->close();
    
    log_activity($conn, 'admin', $admin_id, 'logout', 'admin', $admin_id, "Admin logged out");
}

$conn->close();

session_unset();
session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
exit;
?>

