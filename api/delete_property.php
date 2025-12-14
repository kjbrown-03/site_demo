<?php
session_start();
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$propertyId = isset($input['property_id']) ? (int)$input['property_id'] : 0;

if ($propertyId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid property ID']);
    exit;
}

// Verify ownership
try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND (seller_id = ? OR agent_id = ?)");
    $stmt->execute([$propertyId, $userId, $userId]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$property) {
        echo json_encode(['success' => false, 'message' => 'Property not found or unauthorized']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

try {
    // Delete associated image if exists
    if (!empty($property['image_url']) && file_exists(dirname(__DIR__) . '/' . $property['image_url'])) {
        @unlink(dirname(__DIR__) . '/' . $property['image_url']);
    }
    
    // Delete property from database
    $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
    $stmt->execute([$propertyId]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Property deleted successfully'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

