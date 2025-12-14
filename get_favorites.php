<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Get favorite IDs from POST data
    $input = json_decode(file_get_contents('php://input'), true);
    $favoriteIds = isset($input['ids']) ? $input['ids'] : [];
    
    if (empty($favoriteIds)) {
        echo json_encode([]);
        exit;
    }
    
    // Create placeholders for the prepared statement
    $placeholders = str_repeat('?,', count($favoriteIds) - 1) . '?';
    
    // Fetch properties from database
    $stmt = $pdo->prepare("
        SELECT p.*, u.username as agent_name 
        FROM properties p 
        LEFT JOIN users u ON p.agent_id = u.id 
        WHERE p.id IN ($placeholders)
        ORDER BY p.created_at DESC
    ");
    
    $stmt->execute($favoriteIds);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Transform data to match frontend format
    $formattedProperties = [];
    foreach ($properties as $property) {
        $formattedProperties[] = [
            'id' => $property['id'],
            'price' => $property['price'],
            'bedrooms' => isset($property['bedrooms']) ? $property['bedrooms'] : 0,
            'bathrooms' => isset($property['bathrooms']) ? $property['bathrooms'] : 0,
            'area_sqm' => isset($property['area_sqm']) ? $property['area_sqm'] : 0,
            'address' => $property['address'],
            'image_url' => isset($property['image_url']) ? $property['image_url'] : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800',
            'status' => $property['status'],
            'agent_name' => isset($property['agent_name']) ? $property['agent_name'] : 'Agent ImmoHome'
        ];
    }
    
    echo json_encode($formattedProperties);
    
} catch (PDOException $e) {
    echo json_encode([]);
}
?>