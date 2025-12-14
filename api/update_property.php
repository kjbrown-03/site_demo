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

// Get property ID
$propertyId = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;

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

// Get form data
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$type = $_POST['type'] ?? '';
$bedrooms = isset($_POST['bedrooms']) && $_POST['bedrooms'] !== '' ? intval($_POST['bedrooms']) : null;
$bathrooms = isset($_POST['bathrooms']) && $_POST['bathrooms'] !== '' ? intval($_POST['bathrooms']) : null;
$area_sqm = isset($_POST['area_sqm']) && $_POST['area_sqm'] !== '' ? intval($_POST['area_sqm']) : null;
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

// Handle image upload if new image is provided
$imageUrl = $property['image_url']; // Keep existing image by default
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
            
            // Delete old image if exists
            if (!empty($property['image_url']) && file_exists(dirname(__DIR__) . '/' . $property['image_url'])) {
                @unlink(dirname(__DIR__) . '/' . $property['image_url']);
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
    // Update property in database
    $stmt = $pdo->prepare("
        UPDATE properties 
        SET title = ?, description = ?, price = ?, address = ?, city = ?, type = ?, 
            bedrooms = ?, bathrooms = ?, area_sqm = ?, status = ?, image_url = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $title, $description, $price, $address, $city, $type, 
        $bedrooms, $bathrooms, $area_sqm, $status, $imageUrl, $propertyId
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Property updated successfully'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

