<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';
require_once dirname(__DIR__) . '/includes/pagination_helper.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Get user info
$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
$isLoggedIn = isset($_SESSION['user_id']);

// Check if user is a buyer - if so, redirect to buy page
if ($userRole == 'buyer') {
    header('Location: buy.php');
    exit();
}

// Pagination
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$itemsPerPage = 12;
$pagination = getPaginationValues($currentPage, $itemsPerPage);

// Get rental properties from database with pagination
$sql = "SELECT p.*, u.username as agent_name FROM properties p LEFT JOIN users u ON p.agent_id = u.id WHERE p.status = 'for_rent'";

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
    $error = "Error fetching rental properties: " . $e->getMessage();
}

// Get user's rental history if logged in
$rentalHistory = [];
if ($isLoggedIn) {
    try {
        $stmt = $pdo->prepare("SELECT o.*, p.title, p.price, p.address FROM orders o JOIN properties p ON o.property_id = p.id WHERE o.user_id = ? AND o.order_type = 'rental' ORDER BY o.created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $rentalHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $rentalHistory = [];
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('rent'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='../index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php"><?php echo t('home'); ?></a></li>
                    <li><a href="buy.php"><?php echo t('buy'); ?></a></li>
                    <li><a href="rent.php" class="active"><?php echo t('rent'); ?></a></li>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="sell.php"><?php echo t('sell'); ?></a></li>
                    <?php endif; ?>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="agents.php"><?php echo t('agents'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="financing.php"><?php echo t('financing'); ?></a></li>
                </ul>
                <div class="nav-actions">
                    <?php if ($isLoggedIn): ?>
                        <span class="user-welcome"><?php echo t('hello'); ?>, <?php echo htmlspecialchars($username); ?>!</span>
                        <a href="../auth/logout.php" class="btn-secondary"><?php echo t('logout'); ?></a>
                    <?php else: ?>
                        <button class="btn-primary" onclick="location.href='login.php'"><?php echo t('login'); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <section class="search-hero">
        <div class="container">
            <h1><?php echo t('find_rental'); ?></h1>
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
                <h2><?php echo t('properties_for_rent'); ?></h2>
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
                        <div class="property-card" data-property-id="<?php echo $property['id']; ?>">
                            <div class="property-image" style="background-image: url('<?php echo htmlspecialchars(isset($property['image_url']) ? $property['image_url'] : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=600'); ?>');">
                                <span class="property-badge"><?php echo t('for_rent'); ?></span>
                                <div class="property-favorite">
                                    <i class="far fa-heart"></i>
                                </div>
                            </div>
                            <div class="property-info">
                                <div class="property-price"><?php echo number_format($property['price'], 0, ',', ' '); ?> €/mois</div>
                                <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                <div class="property-details">
                                    <?php if (isset($property['bedrooms'])): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-bed"></i>
                                            <span><?php echo $property['bedrooms']; ?> ch</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($property['bathrooms'])): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-bath"></i>
                                            <span><?php echo $property['bathrooms']; ?> sdb</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($property['area_sqm'])): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-ruler-combined"></i>
                                            <span><?php echo $property['area_sqm']; ?> m²</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="property-address"><?php echo htmlspecialchars($property['address']); ?></div>
                                <div class="property-meta">Agent: <?php echo htmlspecialchars(isset($property['agent_name']) ? $property['agent_name'] : 'N/A'); ?></div>
                                <div class="property-actions">
                                    <button class="btn-secondary" onclick="location.href='property_detail.php?id=<?php echo $property['id']; ?>'"><?php echo t('view_details'); ?></button>
                                    <button class="btn-primary" onclick="rentProperty(<?php echo $property['id']; ?>)"><?php echo t('rent_now'); ?></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php 
                // Generate pagination
                $baseUrl = 'rent.php';
                echo generatePagination($currentPage, $totalPages, $baseUrl);
                ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-home fa-3x"></i>
                    <h3><?php echo t('no_rental_found'); ?></h3>
                    <p><?php echo t('try_later'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($isLoggedIn && count($rentalHistory) > 0): ?>
    <section class="history-section">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('rental_history'); ?></h2>
                <p><?php echo count($rentalHistory); ?> <?php echo t('past_rentals'); ?></p>
            </div>
            
            <div class="history-grid">
                <?php foreach ($rentalHistory as $order): ?>
                    <div class="history-card">
                        <div class="history-info">
                            <h3><?php echo htmlspecialchars($order['title']); ?></h3>
                            <p class="history-price"><?php echo number_format($order['price'], 0, ',', ' '); ?> €/mois</p>
                            <p class="history-address"><?php echo htmlspecialchars($order['address']); ?></p>
                            <p class="history-date"><?php echo t('date'); ?>: <?php echo date('d/m/Y', strtotime($order['created_at'])); ?></p>
                            <p class="history-status"><?php echo t('status'); ?>: <?php echo ucfirst($order['status']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-home"></i>
                        <span>ImmoHome</span>
                    </div>
                    <p><?php echo t('footer_tagline'); ?></p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('rental'); ?></h4>
                    <ul>
                        <li><a href="#">Appartements</a></li>
                        <li><a href="#">Maisons</a></li>
                        <li><a href="#">Villas</a></li>
                        <li><a href="#">Commercial</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('services'); ?></h4>
                    <ul>
                        <li><a href="#">Sélection des Locataires</a></li>
                        <li><a href="#">Contrat de Location</a></li>
                        <li><a href="#">Maintenance</a></li>
                        <li><a href="#">Assurance</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('company'); ?></h4>
                    <ul>
                        <li><a href="#">À Propos</a></li>
                        <li><a href="#">Carrières</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ImmoHome. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Function to initialize favorite buttons
        function initFavoriteButtons() {
            // Get favorites from localStorage
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            
            // Update favorite buttons based on saved favorites
            document.querySelectorAll('.property-favorite').forEach(button => {
                const propertyId = parseInt(button.closest('.property-card').dataset.propertyId);
                if (favorites.includes(propertyId)) {
                    button.classList.add('active');
                    button.innerHTML = '<i class="fas fa-heart"></i>';
                }
            });
            
            // Add click event listeners to favorite buttons
            document.querySelectorAll('.property-favorite').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleFavorite(this);
                });
            });
        }
        
        // Toggle favorite status
        function toggleFavorite(button) {
            const propertyCard = button.closest('.property-card');
            const propertyId = parseInt(propertyCard.dataset.propertyId);
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            
            if (button.classList.contains('active')) {
                // Remove from favorites
                favorites = favorites.filter(id => id !== propertyId);
                button.classList.remove('active');
                button.innerHTML = '<i class="far fa-heart"></i>';
            } else {
                // Add to favorites
                favorites.push(propertyId);
                button.classList.add('active');
                button.innerHTML = '<i class="fas fa-heart"></i>';
            }
            
            // Save to localStorage
            localStorage.setItem('favorites', JSON.stringify(favorites));
        }
        
        function viewProperty(propertyId) {
            location.href = 'property_detail.php?id=' + propertyId;
        }
        
        function rentProperty(propertyId) {
            if (confirm('<?php echo t('confirm_rent'); ?>')) {
                // In a real implementation, this would submit a rental request
                alert('<?php echo t('rental_request_submitted'); ?> ID: ' + propertyId);
            }
        }
        
        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value;
            window.location.href = 'search_properties.php?search=' + encodeURIComponent(searchTerm) + '&type=rental';
        }
        
        // Handle Enter key in search input
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        // Initialize favorite buttons when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initFavoriteButtons();
        });
    </script>
    
    <style>
        /* Fix navigation spacing */
        .nav-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .user-welcome {
            margin-right: 10px;
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