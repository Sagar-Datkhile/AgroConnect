<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Search Crops</h2>";

// Test if search_crops.php works
echo "<h3>1. Testing Search Endpoint</h3>";
$url = "http://localhost/AgroConnect/php/search_crops.php";
$context = stream_context_create(['http' => ['ignore_errors' => true]]);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "<p style='color: red;'>❌ Failed to fetch: " . error_get_last()['message'] . "</p>";
} else {
    echo "<p style='color: green;'>✅ Response received</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "<p>Decoded JSON successfully</p>";
        echo "<p>Success: " . ($data['success'] ? 'true' : 'false') . "</p>";
        if (isset($data['crops'])) {
            echo "<p>Crops found: " . count($data['crops']) . "</p>";
        }
        if (isset($data['error'])) {
            echo "<p style='color: red;'>Error: " . htmlspecialchars($data['error']) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Failed to decode JSON</p>";
    }
}

echo "<hr>";
echo "<h3>2. Direct Database Test</h3>";

require_once 'php/db_connect.php';

try {
    $query = "SELECT c.crop_id, c.crop_name, c.category, c.investment, c.turnover, c.profit, 
                     c.description, c.season, c.created_at,
                     f.name as farmer_name, f.region, f.soil_type, f.area 
              FROM crops c 
              INNER JOIN farmers f ON c.farmer_id = f.farmer_id
              WHERE c.is_deleted = FALSE AND f.is_blocked = FALSE
              ORDER BY c.created_at DESC LIMIT 5";
    
    $result = $conn->query($query);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Query executed successfully</p>";
        echo "<p>Rows found: " . $result->num_rows . "</p>";
        
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Crop Name</th><th>Farmer</th><th>Region</th><th>Investment</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['crop_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['farmer_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['region']) . "</td>";
                echo "<td>₹" . number_format($row['investment'], 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ Query failed: " . $conn->error . "</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}
?>
