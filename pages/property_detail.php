<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';
require_once dirname(__DIR__) . '/includes/navigation.php';

// Check if property ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$propertyId = (int)$_GET['id'];
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Fetch property details
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username as agent_name, u.email as agent_email, u.phone as agent_phone,
               u.first_name as agent_first_name, u.last_name as agent_last_name
        FROM properties p 
        LEFT JOIN users u ON p.agent_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$propertyId]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$property) {
        header('Location: ../index.php');
        exit();
    }
} catch(PDOException $e) {
    header('Location: ../index.php');
    exit();
}

// Check if property is in user's favorites
$isFavorite = false;
if ($isLoggedIn) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
        $stmt->execute([$userId, $propertyId]);
        $isFavorite = $stmt->fetch() !== false;
    } catch(PDOException $e) {
        $isFavorite = false;
    }
}

// Get related properties (same city, different property)
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username as agent_name 
        FROM properties p 
        LEFT JOIN users u ON p.agent_id = u.id 
        WHERE p.city = ? AND p.id != ? AND p.status IN ('for_sale', 'for_rent')
        ORDER BY p.created_at DESC 
        LIMIT 4
    ");
    $stmt->execute([$property['city'], $propertyId]);
    $relatedProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $relatedProperties = [];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <?php renderNavigation('property_detail', $isLoggedIn ? $_SESSION['username'] : '', $isLoggedIn ? $_SESSION['role'] : ''); ?>

    <section class="property-detail-hero">
        <div class="container">
            <div class="breadcrumb">
                <a href="../index.php"><?php echo t('home'); ?></a>
                <span>/</span>
                <a href="search_properties.php"><?php echo t('properties'); ?></a>
                <span>/</span>
                <span><?php echo htmlspecialchars($property['title']); ?></span>
            </div>
        </div>
    </section>

    <section class="property-detail-content">
        <div class="container">
            <div class="property-detail-grid">
                <!-- Main Content -->
                <div class="property-detail-main">
                    <!-- Image Gallery -->
                    <div class="property-image-gallery">
                        <div class="main-image">
                            <img src="<?php echo !empty($property['image_url']) ? '../' . htmlspecialchars($property['image_url']) : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=1200'; ?>" 
                                 alt="<?php echo htmlspecialchars($property['title']); ?>">
                        </div>
                    </div>

                    <!-- Property Info -->
                    <div class="property-info-section">
                        <div class="property-header">
                            <h1><?php echo htmlspecialchars($property['title']); ?></h1>
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($property['address'] . ', ' . $property['city']); ?></span>
                            </div>
                        </div>

                        <div class="property-price-section">
                            <div class="property-price-large">
                                <?php echo number_format($property['price'], 0, ',', ' '); ?> €
                                <?php if ($property['status'] == 'for_rent'): ?>
                                    <span class="price-period">/<?php echo t('month'); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="property-badge-large"><?php echo t($property['status']); ?></span>
                        </div>

                        <div class="property-features">
                            <?php if ($property['bedrooms']): ?>
                                <div class="feature-item">
                                    <i class="fas fa-bed"></i>
                                    <span><?php echo $property['bedrooms']; ?> <?php echo t('bedrooms'); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($property['bathrooms']): ?>
                                <div class="feature-item">
                                    <i class="fas fa-bath"></i>
                                    <span><?php echo $property['bathrooms']; ?> <?php echo t('bathrooms'); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($property['area_sqm']): ?>
                                <div class="feature-item">
                                    <i class="fas fa-ruler-combined"></i>
                                    <span><?php echo $property['area_sqm']; ?> m²</span>
                                </div>
                            <?php endif; ?>
                            <div class="feature-item">
                                <i class="fas fa-home"></i>
                                <span><?php echo t($property['type']); ?></span>
                            </div>
                        </div>

                        <div class="property-description">
                            <h2><?php echo t('description'); ?></h2>
                            <p><?php echo nl2br(htmlspecialchars($property['description'] ?: t('no_description'))); ?></p>
                        </div>

                        <div class="property-details-grid">
                            <div class="detail-item">
                                <span class="detail-label"><?php echo t('property_type'); ?></span>
                                <span class="detail-value"><?php echo t($property['type']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><?php echo t('status'); ?></span>
                                <span class="detail-value"><?php echo t($property['status']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><?php echo t('city'); ?></span>
                                <span class="detail-value"><?php echo htmlspecialchars($property['city']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><?php echo t('address'); ?></span>
                                <span class="detail-value"><?php echo htmlspecialchars($property['address']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="property-detail-sidebar">
                    <div class="contact-card">
                        <h3><?php echo t('contact_agent'); ?></h3>
                        <?php if ($property['agent_name']): ?>
                            <div class="agent-info">
                                <div class="agent-name">
                                    <?php if ($property['agent_first_name'] || $property['agent_last_name']): ?>
                                        <?php echo htmlspecialchars(trim(($property['agent_first_name'] ?? '') . ' ' . ($property['agent_last_name'] ?? ''))); ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($property['agent_name']); ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($property['agent_email']): ?>
                                    <div class="agent-email">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo htmlspecialchars($property['agent_email']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($property['agent_phone']): ?>
                                    <div class="agent-phone">
                                        <i class="fas fa-phone"></i>
                                        <?php echo htmlspecialchars($property['agent_phone']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p><?php echo t('no_agent_assigned'); ?></p>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <?php if ($isLoggedIn): ?>
                                <button class="btn-primary btn-full" onclick="placeOrder(<?php echo $propertyId; ?>, '<?php echo $property['status']; ?>')">
                                    <?php echo $property['status'] == 'for_rent' ? t('rent_now') : t('buy_now'); ?>
                                </button>
                                <button class="btn-secondary btn-full" id="favoriteBtn" onclick="toggleFavorite(<?php echo $propertyId; ?>)">
                                    <i class="fas fa-heart <?php echo $isFavorite ? '' : 'far'; ?>"></i>
                                    <?php echo $isFavorite ? t('remove_favorite') : t('add_favorite'); ?>
                                </button>
                            <?php else: ?>
                                <button class="btn-primary btn-full" onclick="location.href='../auth/login.php'">
                                    <?php echo t('login_to_contact'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Properties -->
            <?php if (!empty($relatedProperties)): ?>
                <div class="related-properties">
                    <h2><?php echo t('related_properties'); ?></h2>
                    <div class="properties-grid">
                        <?php foreach ($relatedProperties as $related): ?>
                            <div class="property-card" onclick="location.href='property_detail.php?id=<?php echo $related['id']; ?>'">
                                <div class="property-image" style="background-image: url('<?php echo !empty($related['image_url']) ? '../' . htmlspecialchars($related['image_url']) : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800'; ?>');">
                                    <span class="property-badge"><?php echo t($related['status']); ?></span>
                                </div>
                                <div class="property-info">
                                    <div class="property-price"><?php echo number_format($related['price'], 0, ',', ' '); ?> €</div>
                                    <div class="property-details">
                                        <?php if ($related['bedrooms']): ?>
                                            <div class="property-detail">
                                                <i class="fas fa-bed"></i>
                                                <span><?php echo $related['bedrooms']; ?> ch</span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($related['bathrooms']): ?>
                                            <div class="property-detail">
                                                <i class="fas fa-bath"></i>
                                                <span><?php echo $related['bathrooms']; ?> sdb</span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($related['area_sqm']): ?>
                                            <div class="property-detail">
                                                <i class="fas fa-ruler-combined"></i>
                                                <span><?php echo $related['area_sqm']; ?> m²</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="property-address"><?php echo htmlspecialchars($related['address']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-home"></i>
                        <span>ImmoHome</span>
                    </div>
                    <p><?php echo t('footer_tagline'); ?></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ImmoHome. <?php echo t('all_rights_reserved'); ?></p>
            </div>
        </div>
    </footer>

    <style>
        .property-detail-hero {
            margin-top: 70px;
            padding: 20px 0;
            background: #f8f9fa;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #006AFF;
            text-decoration: none;
        }

        .breadcrumb span {
            color: #6B6B6B;
        }

        .property-detail-content {
            padding: 40px 0 80px;
        }

        .property-detail-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            margin-bottom: 60px;
        }

        .property-image-gallery {
            margin-bottom: 30px;
        }

        .main-image {
            width: 100%;
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-info-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .property-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #1A1A1A;
        }

        .property-location {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6B6B6B;
            margin-bottom: 20px;
        }

        .property-price-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-top: 1px solid #E0E0E0;
            border-bottom: 1px solid #E0E0E0;
            margin-bottom: 30px;
        }

        .property-price-large {
            font-size: 36px;
            font-weight: 700;
            color: #006AFF;
        }

        .price-period {
            font-size: 20px;
            font-weight: 400;
        }

        .property-badge-large {
            padding: 8px 16px;
            background: #006AFF;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .property-features {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .feature-item i {
            color: #006AFF;
            font-size: 20px;
        }

        .property-description {
            margin-bottom: 30px;
        }

        .property-description h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #1A1A1A;
        }

        .property-description p {
            line-height: 1.8;
            color: #495057;
        }

        .property-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding-top: 30px;
            border-top: 1px solid #E0E0E0;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            font-size: 14px;
            color: #6B6B6B;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #1A1A1A;
        }

        .property-detail-sidebar {
            position: sticky;
            top: 90px;
            height: fit-content;
        }

        .contact-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .contact-card h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #1A1A1A;
        }

        .agent-info {
            margin-bottom: 25px;
        }

        .agent-name {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1A1A1A;
        }

        .agent-email,
        .agent-phone {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #495057;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-full {
            width: 100%;
        }

        .related-properties {
            margin-top: 60px;
        }

        .related-properties h2 {
            font-size: 28px;
            margin-bottom: 30px;
            color: #1A1A1A;
        }

        @media (max-width: 968px) {
            .property-detail-grid {
                grid-template-columns: 1fr;
            }

            .property-detail-sidebar {
                position: static;
            }

            .property-features {
                grid-template-columns: repeat(2, 1fr);
            }

            .property-details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .property-header h1 {
                font-size: 24px;
            }

            .property-price-large {
                font-size: 28px;
            }

            .main-image {
                height: 300px;
            }

            .property-features {
                grid-template-columns: 1fr;
            }

            .property-info-section {
                padding: 20px;
            }

            .contact-card {
                padding: 20px;
            }
        }
    </style>

    <script>
        function toggleFavorite(propertyId) {
            <?php if (!$isLoggedIn): ?>
                location.href = '../auth/login.php';
                return;
            <?php endif; ?>

            const btn = document.getElementById('favoriteBtn');
            const icon = btn.querySelector('i');
            const isCurrentlyFavorite = icon.classList.contains('fas');

            fetch('../api/' + (isCurrentlyFavorite ? 'remove_favorite.php' : 'add_favorite.php'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ property_id: propertyId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    icon.classList.toggle('fas');
                    icon.classList.toggle('far');
                    btn.innerHTML = '<i class="fas fa-heart ' + (icon.classList.contains('fas') ? '' : 'far') + '"></i> ' + 
                                   (icon.classList.contains('fas') ? '<?php echo t('remove_favorite'); ?>' : '<?php echo t('add_favorite'); ?>');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function placeOrder(propertyId, status) {
            if (confirm('<?php echo t('confirm_order'); ?>')) {
                // In a real implementation, this would submit an order
                alert('<?php echo t('order_submitted'); ?>');
            }
        }
    </script>
</body>
</html>

