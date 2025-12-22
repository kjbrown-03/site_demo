<?php
require_once 'config.php';
require_once 'includes/pagination_helper.php';

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

echo "Original SQL Query: " . $sql . "\n";

// Get count query
$countSql = getCountQuery($sql);
echo "Count SQL Query: " . $countSql . "\n";
echo "Parameters: " . print_r($params, true) . "\n";

try {
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total items: " . $totalItems . "\n";
} catch(PDOException $e) {
    echo "Error counting properties: " . $e->getMessage() . "\n";
}

try {
    // Add pagination to main query
    $sql .= " ORDER BY p.created_at DESC LIMIT 12 OFFSET 0";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($properties) . " properties\n";
} catch(PDOException $e) {
    echo "Error fetching properties: " . $e->getMessage() . "\n";
}
?>