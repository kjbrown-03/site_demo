<?php
require_once 'config.php';

// Test the specific search query that's failing
$searchTerm = 'yaounde';
$propertyType = 'house';

// Build SQL query
$sql = "SELECT p.*, u.username as agent_name FROM properties p LEFT JOIN users u ON p.agent_id = u.id WHERE 1=1";
$params = [];

if (!empty($searchTerm)) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.address LIKE ? OR p.city LIKE ?)";
    $params = array_merge($params, ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"]);
}

if (!empty($propertyType) && $propertyType != 'all') {
    $sql .= " AND p.type = ?";
    $params[] = $propertyType;
}

echo "SQL Query: " . $sql . "\n";
echo "Parameters: " . print_r($params, true) . "\n";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($properties) . " properties\n";
    
    foreach ($properties as $property) {
        echo "- " . $property['title'] . " in " . $property['city'] . "\n";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>