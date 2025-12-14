<?php
header('Content-Type: application/json');
session_start();
require_once dirname(__DIR__) . '/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Get user's favorites from database
    $stmt = $pdo->prepare("
        SELECT p.*, u.username as agent_name 
        FROM favorites f
        INNER JOIN properties p ON f.property_id = p.id
        LEFT JOIN users u ON p.agent_id = u.id 
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ");
    
    $stmt->execute([$userId]);
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
            'image_url' => !empty($property['image_url']) ? '../' . $property['image_url'] : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800',
            'status' => $property['status'],
            'agent_name' => isset($property['agent_name']) ? $property['agent_name'] : 'Agent ImmoHome'
        ];
    }
    
    echo json_encode($formattedProperties);
    
} catch (PDOException $e) {
    echo json_encode([]);
}
?>