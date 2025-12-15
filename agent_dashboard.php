<?php
session_start();
require_once 'config.php';
require_once 'includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agent') {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];

// Fetch recent properties for this agent
try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE agent_id = ? ORDER BY created_at DESC LIMIT 3");
    $stmt->execute([$_SESSION['user_id']]);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $properties = [];
    error_log("Error fetching properties: " . $e->getMessage());
}

// Fetch statistics
try {
    // Count listings
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties WHERE agent_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $listingsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count leads
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM leads WHERE agent_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $leadsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count appointments
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE agent_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $appointmentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch(PDOException $e) {
    $listingsCount = 0;
    $leadsCount = 0;
    $appointmentsCount = 0;
    error_log("Error fetching statistics: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php renderNavigation('agent_dashboard.php', $username, $userRole); ?>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1>Agent Dashboard</h1>
            <p>Manage your listings and client relationships</p>
        </div>
    </section>

    <section class="dashboard-stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Mes Annonces</h3>
                        <p>Gérer vos annonces immobilières</p>
                    </div>
                    <a href="my_listings.php" class="stat-action">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Prospects</h3>
                        <p>Gérer vos prospects et clients potentiels</p>
                    </div>
                    <a href="client_leads.php" class="stat-action">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Rendez-vous</h3>
                        <p>Gérer vos rendez-vous avec les clients</p>
                    </div>
                    <a href="appointments.php" class="stat-action">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Performance</h3>
                        <p>Track your sales metrics</p>
                    </div>
                    <a href="#" class="stat-action">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="recent-properties">
        <div class="container">
            <div class="section-header">
                <h2>Recent Listings</h2>
                <p>Newly added properties</p>
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
                    <p>Your trusted platform for real estate professionals.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Agent Tools</h4>
                    <ul>
                        <li><a href="#">Lead Management</a></li>
                        <li><a href="#">CRM Integration</a></li>
                        <li><a href="#">Marketing Materials</a></li>
                        <li><a href="#">Training Resources</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Listing Promotion</a></li>
                        <li><a href="#">Professional Photography</a></li>
                        <li><a href="#">Virtual Tours</a></li>
                        <li><a href="#">Legal Support</a></li>
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
                    <span class="property-badge">${property.status ? ucfirst(str_replace('_', ' ', property.status)) : 'Active'}</span>
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
                    <div class="property-meta">ID: ${property.id}</div>
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