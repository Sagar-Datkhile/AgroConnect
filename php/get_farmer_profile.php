<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if farmer is logged in
if (!isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

// Fetch farmer profile
$stmt = $conn->prepare("SELECT name, email, region, soil_type, area FROM farmers WHERE farmer_id = ?");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
    echo json_encode(['success' => true, 'profile' => $profile]);
} else {
    echo json_encode(['success' => false, 'message' => 'Profile not found.']);
}

$stmt->close();
$conn->close();
?>

