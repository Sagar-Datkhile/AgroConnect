<?php
/**
 * Public Crop Search Handler
 * Backward compatible with old and new database schema
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    require_once 'db_connect.php';
    $crop_name = isset($_GET['crop_name']) ? trim($_GET['crop_name']) : '';
    $region = isset($_GET['region']) ? trim($_GET['region']) : '';
    $min_area = isset($_GET['min_area']) ? floatval($_GET['min_area']) : 0;
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    
    // Check which columns exist in crops table
    $checkColumns = $conn->query("SHOW COLUMNS FROM crops");
    $columns = [];
    while ($col = $checkColumns->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    
    $hasNewSchema = in_array('category', $columns) && in_array('profit', $columns);
    $hasIsDeleted = in_array('is_deleted', $columns);
    
    // Build query based on available columns
    if ($hasNewSchema) {
        // New schema with extended fields
        $query = "SELECT c.crop_id, c.crop_name, c.category, c.investment, c.turnover, c.profit, 
                         c.description, c.season, c.quantity, c.quantity_unit, c.created_at,
                         f.name as farmer_name, f.email as farmer_email, f.region, f.soil_type, f.area 
                  FROM crops c 
                  INNER JOIN farmers f ON c.farmer_id = f.farmer_id";
    } else {
        // Old schema - basic fields
        $query = "SELECT c.crop_id, c.crop_name, c.investment, c.turnover, c.description, c.created_at,
                         f.name as farmer_name, f.region, f.soil_type, f.area 
                  FROM crops c 
                  INNER JOIN farmers f ON c.farmer_id = f.farmer_id";
    }
    
    // Add WHERE conditions
    $whereConditions = [];
    
    if ($hasIsDeleted) {
        $whereConditions[] = "c.is_deleted = FALSE";
    }
    
    $whereConditions[] = "f.is_blocked = FALSE";
    
    $query .= " WHERE " . implode(" AND ", $whereConditions);
    
    $params = [];
    $types = "";
    
    if (!empty($crop_name)) {
        $query .= " AND c.crop_name LIKE ?";
        $params[] = "%$crop_name%";
        $types .= "s";
    }
    
    if (!empty($region)) {
        $query .= " AND f.region LIKE ?";
        $params[] = "%$region%";
        $types .= "s";
    }
    
    if ($min_area > 0) {
        $query .= " AND f.area >= ?";
        $params[] = $min_area;
        $types .= "d";
    }
    
    if ($hasNewSchema && !empty($category)) {
        $query .= " AND c.category = ?";
        $params[] = $category;
        $types .= "s";
    }
    
    $query .= " ORDER BY c.created_at DESC LIMIT 100";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $crops = [];
    while ($row = $result->fetch_assoc()) {
        $crops[] = $row;
    }
    
    // Log search analytics if table exists (new schema only)
    $checkAnalytics = $conn->query("SHOW TABLES LIKE 'search_analytics'");
    if ($checkAnalytics && $checkAnalytics->num_rows > 0) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $log_stmt = $conn->prepare(
            "INSERT INTO search_analytics (search_query, region_filter, min_area_filter, results_count, ip_address) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $results_count = count($crops);
        $log_stmt->bind_param("ssdis", $crop_name, $region, $min_area, $results_count, $ip_address);
        $log_stmt->execute();
        $log_stmt->close();
    }
    
    echo json_encode(['success' => true, 'crops' => $crops, 'count' => count($crops)]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while searching.', 'error' => $e->getMessage()]);
}
?>

