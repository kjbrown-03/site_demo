<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';
require_once dirname(__DIR__) . '/includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: ../auth/login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];

// Fetch recent properties from database
try {
    $stmt = $pdo->prepare("SELECT p.*, u.username as agent_name FROM properties p LEFT JOIN users u ON p.agent_id = u.id WHERE p.status = 'for_sale' ORDER BY p.created_at DESC LIMIT 3");
    $stmt->execute();
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $properties = [];
    error_log("Error fetching properties: " . $e->getMessage());
}

// Fetch user's favorite properties
$favoritesCount = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $favoritesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch(PDOException $e) {
    error_log("Error fetching favorites count: " . $e->getMessage());
}

// Fetch user's orders
$ordersCount = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $ordersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch(PDOException $e) {
    error_log("Error fetching orders count: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('buyer_dashboard'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <header>
        <?php renderNavigation('buyer_dashboard.php', $username, $userRole); ?>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1><?php echo t('buyer_dashboard'); ?></h1>
            <p><?php echo t('find_dream_property'); ?></p>
        </div>
    </section>

    <section class="dashboard-stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo t('search_properties_title'); ?></h3>
                        <p><?php echo t('browse_listings'); ?></p>
                    </div>
                    <a href="../pages/search_properties.php" class="stat-action">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo t('favorites'); ?></h3>
                        <p><?php echo t('view_saved_properties'); ?></p>
                    </div>
                    <a href="../user/favorites.php" class="stat-action">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo t('my_orders'); ?></h3>
                        <p><?php echo t('track_purchases'); ?></p>
                    </div>
                    <a href="../user/my_orders.php" class="stat-action">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo t('account_settings'); ?></h3>
                        <p><?php echo t('manage_profile'); ?></p>
                    </div>
                    <a href="../user/account_settings.php" class="stat-action">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="recent-properties">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('recently_added_properties'); ?></h2>
                <p><?php echo t('check_latest_listings'); ?></p>
            </div>
            
            <div class="properties-grid" id="propertiesGrid">
                <!-- Properties will be loaded dynamically -->
            </div>
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
                    <p>Your trusted partner for finding the perfect home.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Buy</h4>
                    <ul>
                        <li><a href="#">Houses</a></li>
                        <li><a href="#">Apartments</a></li>
                        <li><a href="#">Villas</a></li>
                        <li><a href="#">Land</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Free Valuation</a></li>
                        <li><a href="#">Financing</a></li>
                        <li><a href="#">Insurance</a></li>
                        <li><a href="#">Moving</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ImmoHome. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function formatPrice(price) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 0
            }).format(price);
        }

        function createPropertyCard(property) {
            const card = document.createElement('div');
            card.className = 'property-card';
            
            // Use a default image if none is provided
            const imageUrl = property.image_url || 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800';
            
            card.innerHTML = `
                <div class="property-image" style="background-image: url('${imageUrl}');">
                    <span class="property-badge">${property.status ? ucfirst(str_replace('_', ' ', property.status)) : 'New'}</span>
                    <div class="property-favorite">
                        <i class="far fa-heart"></i>
                    </div>
                </div>
                <div class="property-info">
                    <div class="property-price">${formatPrice(property.price)}</div>
                    <div class="property-details">
                        ${property.bedrooms ? `
                        <div class="property-detail">
                            <i class="fas fa-bed"></i>
                            <span>${property.bedrooms} bed</span>
                        </div>` : ''}
                        ${property.bathrooms ? `
                        <div class="property-detail">
                            <i class="fas fa-bath"></i>
                            <span>${property.bathrooms} bath</span>
                        </div>` : ''}
                        ${property.area_sqm ? `
                        <div class="property-detail">
                            <i class="fas fa-ruler-combined"></i>
                            <span>${property.area_sqm} m²</span>
                        </div>` : ''}
                    </div>
                    <div class="property-address">${property.address || property.city ? (property.address + (property.city ? ', ' + property.city : '')) : 'Address not available'}</div>
                    <div class="property-meta">Agent: ${property.agent_name || 'N/A'}</div>
                </div>
            `;

            const favoriteBtn = card.querySelector('.property-favorite');
            favoriteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const icon = favoriteBtn.querySelector('i');
                icon.classList.toggle('far');
                icon.classList.toggle('fas');
                favoriteBtn.style.color = icon.classList.contains('fas') ? '#FF4757' : '';
            });

            return card;
        }

        function renderProperties() {
            const propertiesGrid = document.getElementById('propertiesGrid');
            propertiesGrid.innerHTML = '';
            
            // Get properties data from PHP
            const phpProperties = <?php echo json_encode($properties); ?>;
            
            if (phpProperties && phpProperties.length > 0) {
                phpProperties.forEach(property => {
                    propertiesGrid.appendChild(createPropertyCard(property));
                });
            } else {
                propertiesGrid.innerHTML = '<p>No properties found.</p>';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderProperties();
        });
    </script>

    <style>
        .dashboard-hero {
            margin-top: 70px;
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .dashboard-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .dashboard-hero p {
            font-size: 20px;
            opacity: 0.9;
        }
        
        .dashboard-stats {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: #006AFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-size: 24px;
        }
        
        .stat-content h3 {
            font-size: 20px;
            margin-bottom: 8px;
            color: #1A1A1A;
        }
        
        .stat-content p {
            color: #6B6B6B;
            margin: 0;
        }
        
        .stat-action {
            margin-left: auto;
            color: #006AFF;
            font-size: 20px;
            transition: all 0.3s ease;
        }
        
        .stat-action:hover {
            transform: translateX(5px);
        }
        
        .recent-properties {
            padding: 80px 0;
        }
        
        @media (max-width: 768px) {
            .dashboard-hero h1 {
                font-size: 36px;
            }
            
            .dashboard-hero p {
                font-size: 18px;
            }
            
            .stat-card {
                flex-direction: column;
                text-align: center;
            }
            
            .stat-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .stat-action {
                margin-left: 0;
                margin-top: 15px;
            }
        }
    </style>
</body>
</html>