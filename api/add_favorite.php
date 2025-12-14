<?php
header('Content-Type: application/json');
session_start();
require_once dirname(__DIR__) . '/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$propertyId = isset($input['property_id']) ? (int)$input['property_id'] : 0;

if ($propertyId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid property ID']);
    exit;
}

try {
    // Check if favorite already exists
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
    $stmt->execute([$userId, $propertyId]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Already in favorites']);
        exit;
    }
    
    // Add to favorites
    $stmt = $pdo->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
    $stmt->execute([$userId, $propertyId]);
    
    echo json_encode(['success' => true, 'message' => 'Added to favorites']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>

