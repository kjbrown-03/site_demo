<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/config.php';

try {
    // Fetch properties from database
    $stmt = $pdo->query("
        SELECT p.*, u.username as agent_name 
        FROM properties p 
        LEFT JOIN users u ON p.agent_id = u.id 
        WHERE p.status IN ('for_sale', 'for_rent') 
        ORDER BY p.created_at DESC 
        LIMIT 8
    ");
    
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Transform data to match frontend format
    $formattedProperties = [];
    foreach ($properties as $property) {
        $formattedProperties[] = [
            'id' => $property['id'],
            'price' => $property['price'],
            'beds' => isset($property['bedrooms']) ? $property['bedrooms'] : 0,
            'baths' => isset($property['bathrooms']) ? $property['bathrooms'] : 0,
            'sqft' => isset($property['area_sqm']) ? $property['area_sqm'] : 0,
            'address' => $property['address'],
            'image' => !empty($property['image_url']) ? '../' . $property['image_url'] : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800',
            'status' => $property['status'] == 'for_rent' ? 'À louer' : 'À vendre',
            'agent' => isset($property['agent_name']) ? $property['agent_name'] : 'Agent ImmoHome'
        ];
    }
    
    echo json_encode($formattedProperties);
    
} catch (PDOException $e) {
    // Fallback to static data if database connection fails
    $staticProperties = [
        [
            'id' => 1,
            'price' => 485000,
            'beds' => 4,
            'baths' => 2,
            'sqft' => 1883,
            'address' => "123 Avenue des Champs, Paris 8ème",
            'image' => "https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800",
            'status' => "Nouveau",
            'agent' => "Sophie Martin"
        ],
        [
            'id' => 2,
            'price' => 325000,
            'beds' => 3,
            'baths' => 2,
            'sqft' => 1440,
            'address' => "45 Rue de la République, Lyon 2ème",
            'image' => "https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800",
            'status' => "Prix réduit",
            'agent' => "Jean Dupont"
        ],
        [
            'id' => 3,
            'price' => 629000,
            'beds' => 5,
            'baths' => 3,
            'sqft' => 2819,
            'address' => "78 Boulevard Haussmann, Paris 9ème",
            'image' => "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800",
            'status' => "Nouveau",
            'agent' => "Marie Leclerc"
        ],
        [
            'id' => 4,
            'price' => 389900,
            'beds' => 3,
            'baths' => 2,
            'sqft' => 2000,
            'address' => "12 Rue Victor Hugo, Bordeaux",
            'image' => "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800",
            'status' => "À vendre",
            'agent' => "Pierre Dubois"
        ]
    ];
    
    echo json_encode($staticProperties);
}
?>