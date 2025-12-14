<?php
session_start();
require_once 'config.php';
require_once 'includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Propriétés - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
                    <li><a href="seller_dashboard.php">Dashboard</a></li>
                    <li><a href="my_properties.php" class="active">Mes Propriétés</a></li>
                    <li><a href="add_property.php">Ajouter</a></li>
                    <li><a href="my_sales.php">Mes Ventes</a></li>
                    <li><a href="favorites.php">Favoris</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropbtn">Paramètres <i class="fas fa-caret-down"></i></a>
                        <div class="dropdown-content">
                            <a href="account_settings.php">Langue & Thème</a>
                            <a href="account_settings.php">Informations Utilisateur</a>
                        </div>
                    </li>
                </ul>
                <div class="nav-actions">
                    <div class="user-profile-dropdown">
                        <div class="user-avatar" onclick="toggleProfileDropdown()">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </div>
                        <div class="profile-dropdown-content" id="profileDropdown">
                            <div class="profile-info">
                                <p><?php echo htmlspecialchars($username); ?></p>
                            </div>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1>Mes Propriétés</h1>
            <p>Gérer vos propriétés immobilières</p>
        </div>
    </section>

    <section class="properties-section">
        <div class="container">
            <div class="section-header">
                <h2>Vos Propriétés Enregistrées</h2>
                <button class="btn-primary" onclick="location.href='add_property.php'">
                    <i class="fas fa-plus"></i> Ajouter une Propriété
                </button>
            </div>
            
            <div class="properties-grid" id="propertiesGrid">
                <!-- Properties will be loaded dynamically -->
                <div class="property-card">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800');">
                        <span class="property-badge active">En Vente</span>
                        <div class="property-favorite">
                            <i class="fas fa-heart"></i>
                        </div>
                    </div>
                    <div class="property-info">
                        <div class="property-price">€485,000</div>
                        <div class="property-address">123 Main Street, Paris</div>
                        <div class="property-details">
                            <div class="property-detail">
                                <i class="fas fa-bed"></i>
                                <span>4 chambres</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-bath"></i>
                                <span>2 salles de bain</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-ruler-combined"></i>
                                <span>1883 m²</span>
                            </div>
                        </div>
                        <div class="property-actions">
                            <button class="btn-small btn-secondary">Modifier</button>
                            <button class="btn-small btn-danger">Retirer</button>
                        </div>
                    </div>
                </div>
                
                <div class="property-card">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800');">
                        <span class="property-badge sold">Vendue</span>
                        <div class="property-favorite">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="property-info">
                        <div class="property-price">€325,000</div>
                        <div class="property-address">45 City Avenue, Lyon</div>
                        <div class="property-details">
                            <div class="property-detail">
                                <i class="fas fa-bed"></i>
                                <span>3 chambres</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-bath"></i>
                                <span>2 salles de bain</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-ruler-combined"></i>
                                <span>1440 m²</span>
                            </div>
                        </div>
                        <div class="property-actions">
                            <button class="btn-small btn-secondary" disabled>Modifier</button>
                            <button class="btn-small btn-danger">Archiver</button>
                        </div>
                    </div>
                </div>
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
                    <p>Your trusted partner for selling properties.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Sell</h4>
                    <ul>
                        <li><a href="#">List Property</a></li>
                        <li><a href="#">Pricing Guide</a></li>
                        <li><a href="#">Marketing Options</a></li>
                        <li><a href="#">Success Stories</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Professional Photography</a></li>
                        <li><a href="#">Virtual Tours</a></li>
                        <li><a href="#">Legal Support</a></li>
                        <li><a href="#">Moving Assistance</a></li>
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
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .section-header h2 {
            margin: 0;
        }
        
        .properties-section {
            padding: 80px 0;
        }
        
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .property-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .property-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .property-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .property-badge.active {
            background: #28a745;
            color: white;
        }
        
        .property-badge.sold {
            background: #6c757d;
            color: white;
        }
        
        .property-favorite {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        .property-info {
            padding: 20px;
        }
        
        .property-price {
            font-size: 24px;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 10px;
        }
        
        .property-address {
            color: #6B6B6B;
            margin-bottom: 15px;
        }
        
        .property-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .property-detail {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #6B6B6B;
        }
        
        .property-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:disabled {
            background: #adb5bd;
            cursor: not-allowed;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        /* User profile dropdown */
        .user-profile-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .user-avatar {
            cursor: pointer;
            color: #006AFF;
        }
        
        .profile-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 8px;
            top: 100%;
        }
        
        .profile-dropdown-content.show {
            display: block;
        }
        
        .profile-info {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-weight: 500;
        }
        
        .profile-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .profile-dropdown-content a:hover {
            background-color: #f1f1f1;
            border-radius: 4px;
            margin: 0 5px;
        }
        
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
            
            .properties-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <script>
        function toggleProfileDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.user-avatar') && !event.target.matches('.user-avatar *')) {
                var dropdowns = document.getElementsByClassName("profile-dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>