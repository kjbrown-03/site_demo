<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';
require_once dirname(__DIR__) . '/includes/pagination_helper.php';
require_once dirname(__DIR__) . '/includes/navigation.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';

// Pagination
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$itemsPerPage = 12;
$pagination = getPaginationValues($currentPage, $itemsPerPage);

// Get properties for sale from database with pagination
$sql = "SELECT p.*, u.username as agent_name FROM properties p LEFT JOIN users u ON p.agent_id = u.id WHERE p.status = 'for_sale'";

// Get total count
$countSql = getCountQuery($sql);
try {
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute();
    $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = getTotalPages($totalItems, $itemsPerPage);
} catch(PDOException $e) {
    $totalItems = 0;
    $totalPages = 1;
}

// Get paginated results
$sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pagination['limit'], $pagination['offset']]);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $properties = [];
    $error = "Error fetching properties: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('buy'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <?php renderNavigation('buy', $username, $userRole); ?>

    <section class="search-hero">
        <div class="container">
            <h1><?php echo t('find_property'); ?></h1>
            <p><?php echo t('thousands_properties'); ?></p>
            
            <div class="search-box">
                <div class="search-form">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="<?php echo t('search_placeholder'); ?>" class="search-input" id="searchInput">
                    </div>
                    <button class="search-btn" onclick="performSearch()"><i class="fas fa-search"></i> <?php echo t('search'); ?></button>
                </div>
            </div>
        </div>
    </section>

    <section class="properties-results">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('properties_for_sale'); ?></h2>
                <p>
                    <?php 
                    $startItem = ($currentPage - 1) * $itemsPerPage + 1;
                    $endItem = min($currentPage * $itemsPerPage, $totalItems);
                    echo t('showing') . ' ' . $startItem . ' ' . t('to') . ' ' . $endItem . ' ' . t('of') . ' ' . $totalItems . ' ' . t('results');
                    ?>
                </p>
            </div>
            
            <?php if (!empty($properties)): ?>
                <div class="properties-grid">
                    <?php foreach ($properties as $property): ?>
                        <div class="property-card" data-property-id="<?php echo $property['id']; ?>" onclick="location.href='property_detail.php?id=<?php echo $property['id']; ?>'">
                            <div class="property-image" style="background-image: url('<?php echo htmlspecialchars(isset($property['image_url']) && !empty($property['image_url']) ? '../' . $property['image_url'] : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=600'); ?>');">
                                <span class="property-badge"><?php echo t('for_sale'); ?></span>
                                <div class="property-favorite" onclick="event.stopPropagation(); toggleFavorite(<?php echo $property['id']; ?>, this)">
                                    <i class="far fa-heart"></i>
                                </div>
                            </div>
                            <div class="property-info">
                                <div class="property-price"><?php echo number_format($property['price'], 0, ',', ' '); ?> €</div>
                                <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                <div class="property-details">
                                    <?php if (isset($property['bedrooms']) && $property['bedrooms'] > 0): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-bed"></i>
                                            <span><?php echo $property['bedrooms']; ?> ch</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($property['bathrooms']) && $property['bathrooms'] > 0): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-bath"></i>
                                            <span><?php echo $property['bathrooms']; ?> sdb</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($property['area_sqm']) && $property['area_sqm'] > 0): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-ruler-combined"></i>
                                            <span><?php echo $property['area_sqm']; ?> m²</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="property-address"><?php echo htmlspecialchars($property['address']); ?></div>
                                <div class="property-meta"><?php echo t('agent'); ?>: <?php echo htmlspecialchars(isset($property['agent_name']) ? $property['agent_name'] : 'N/A'); ?></div>
                                <div class="property-actions">
                                    <button class="btn-secondary" onclick="event.stopPropagation(); location.href='property_detail.php?id=<?php echo $property['id']; ?>'"><?php echo t('view_details'); ?></button>
                                    <?php if ($isLoggedIn): ?>
                                        <button class="btn-primary" onclick="event.stopPropagation(); placeOrder(<?php echo $property['id']; ?>)"><?php echo t('buy_now'); ?></button>
                                    <?php else: ?>
                                        <button class="btn-primary" onclick="event.stopPropagation(); location.href='../auth/login.php'"><?php echo t('login_to_buy'); ?></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php 
                // Generate pagination
                $baseUrl = 'buy.php';
                echo generatePagination($currentPage, $totalPages, $baseUrl);
                ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-home fa-3x"></i>
                    <h3><?php echo t('no_properties_found'); ?></h3>
                    <p><?php echo t('try_later'); ?></p>
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

    <script>
        function toggleFavorite(propertyId, element) {
            <?php if (!$isLoggedIn): ?>
                location.href = '../auth/login.php';
                return;
            <?php endif; ?>

            const icon = element.querySelector('i');
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
                    element.style.color = icon.classList.contains('fas') ? '#FF4757' : '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function placeOrder(propertyId) {
            if (confirm('<?php echo t('confirm_order'); ?>')) {
                // In a real implementation, this would submit an order
                alert('<?php echo t('order_submitted'); ?>');
            }
        }

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value;
            window.location.href = 'search_properties.php?search=' + encodeURIComponent(searchTerm) + '&type=house';
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    </script>

    <style>
        .search-hero {
            margin-top: 70px;
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }

        .search-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .search-hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            margin: 0 auto;
        }

        .search-form {
            display: flex;
            gap: 15px;
        }

        .search-input-group {
            flex: 1;
            position: relative;
        }

        .search-input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6B6B6B;
        }

        .search-input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 16px;
        }

        .search-btn {
            padding: 15px 30px;
            background: #006AFF;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-btn:hover {
            background: #0052CC;
        }

        .properties-results {
            padding: 80px 0;
        }

        .property-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .property-actions .btn-secondary,
        .property-actions .btn-primary {
            flex: 1;
            padding: 10px;
            font-size: 14px;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6B6B6B;
        }

        .no-results i {
            margin-bottom: 20px;
            color: #006AFF;
        }

        .no-results h3 {
            margin-bottom: 10px;
            color: #1A1A1A;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
            padding: 20px 0;
        }

        .pagination-btn {
            padding: 10px 20px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            background: white;
            color: #1A1A1A;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pagination-btn:hover:not(.disabled) {
            border-color: #006AFF;
            color: #006AFF;
            background: #F0F7FF;
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-numbers {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .pagination-number {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            background: white;
            color: #1A1A1A;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .pagination-number:hover:not(.active) {
            border-color: #006AFF;
            color: #006AFF;
            background: #F0F7FF;
        }

        .pagination-number.active {
            background: #006AFF;
            border-color: #006AFF;
            color: white;
        }

        .pagination-ellipsis {
            padding: 0 5px;
            color: #6B6B6B;
        }

        @media (max-width: 768px) {
            .search-hero {
                padding: 60px 0;
            }

            .search-hero h1 {
                font-size: 36px;
            }

            .search-hero p {
                font-size: 18px;
            }

            .search-form {
                flex-direction: column;
            }

            .search-box {
                padding: 20px;
            }

            .property-actions {
                flex-direction: column;
            }

            .pagination {
                flex-wrap: wrap;
            }

            .pagination-numbers {
                flex-wrap: wrap;
            }
        }
    </style>
</body>
</html>

