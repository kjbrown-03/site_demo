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

// Only sellers and agents can add properties
if ($userRole != 'seller' && $userRole != 'agent') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$type = $_POST['type'] ?? '';
$bedrooms = intval($_POST['bedrooms'] ?? 0);
$bathrooms = intval($_POST['bathrooms'] ?? 0);
$area_sqm = intval($_POST['area_sqm'] ?? 0);
$status = $_POST['status'] ?? 'for_sale';

// Validate required fields
if (empty($title) || empty($price) || empty($address) || empty($city) || empty($type)) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    exit;
}

// Validate property type
$allowedTypes = ['house', 'apartment', 'villa', 'land'];
if (!in_array($type, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid property type']);
    exit;
}

// Handle image upload
$imageUrl = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (in_array($fileType, $allowedTypes)) {
        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] <= $maxSize) {
            // Create upload directory if it doesn't exist
            $uploadDir = dirname(__DIR__) . '/uploads/properties/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('prop_', true) . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $imageUrl = 'uploads/properties/' . $fileName;
            }
        }
    }
}

try {
    // Determine agent_id and seller_id
    $agentId = ($userRole == 'agent') ? $userId : null;
    $sellerId = ($userRole == 'seller') ? $userId : null;
    
    // Insert property into database
    $stmt = $pdo->prepare("
        INSERT INTO properties 
        (title, description, price, address, city, type, bedrooms, bathrooms, area_sqm, status, agent_id, seller_id, image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $title, $description, $price, $address, $city, $type, 
        $bedrooms, $bathrooms, $area_sqm, $status, $agentId, $sellerId, $imageUrl
    ]);
    
    $propertyId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Property added successfully',
        'property_id' => $propertyId
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

