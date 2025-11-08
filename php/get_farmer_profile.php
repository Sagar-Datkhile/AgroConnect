<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

$stmt = $conn->prepare(
    "SELECT name, email, phone, region, soil_type, area, registration_date, last_login 
     FROM farmers WHERE farmer_id = ?"
);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
    
    $stats_query = $conn->query("CALL sp_get_farmer_dashboard($farmer_id)");
    $stats = $stats_query->fetch_assoc();
    $stats_query->close();
    $conn->next_result(); // Clear result for next query
    
    echo json_encode([
        'success' => true, 
        'profile' => $profile,
        'statistics' => $stats
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Profile not found.']);
}

$stmt->close();
$conn->close();
?>

