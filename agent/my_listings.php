<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agent') {
    header('Location: ../auth/login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete' && isset($_POST['property_id'])) {
            // In a real application, you would delete the property from the database
            $message = "Annonce supprimée avec succès!";
        } elseif ($_POST['action'] === 'edit' && isset($_POST['property_id'])) {
            // In a real application, you would update the property in the database
            $message = "Annonce mise à jour avec succès!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('my_listings'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='../index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
                    <li><a href="agent_dashboard.php">Dashboard</a></li>
                    <li><a href="my_listings.php" class="active">Mes Annonces</a></li>
                    <li><a href="client_leads.php">Prospects</a></li>
                    <li><a href="appointments.php">Rendez-vous</a></li>
                    <li><a href="favorites.php">Favoris</a></li>
                </ul>
                <div class="nav-actions">
                    <div class="user-profile-dropdown">
                        <div class="user-avatar" onclick="toggleProfileDropdown()">
                            <?php
                            // Fetch user profile picture
                            try {
                                $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                $profilePicture = isset($user['profile_picture']) ? $user['profile_picture'] : '';
                                
                                if (!empty($profilePicture) && file_exists($profilePicture)) {
                                    echo '<img src="' . $profilePicture . '" alt="Profile" class="profile-img">';
                                } else {
                                    echo '<i class="fas fa-user-circle fa-2x"></i>';
                                }
                            } catch(PDOException $e) {
                                echo '<i class="fas fa-user-circle fa-2x"></i>';
                            }
                            ?>
                        </div>
                        <div class="profile-dropdown-content" id="profileDropdown">
                            <div class="profile-info">
                                <p><?php echo htmlspecialchars($username); ?></p>
                            </div>
                            <a href="account_settings.php"><i class="fas fa-cog"></i> Paramètres</a>
                            <a href="account_settings.php#language-theme"><i class="fas fa-language"></i> Langue & Thème</a>
                            <a href="account_settings.php#user-info"><i class="fas fa-user-edit"></i> Informations Utilisateur</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1>Mes Annonces</h1>
            <p>Gérer vos annonces immobilières</p>
        </div>
    </section>

    <?php if (isset($message)): ?>
    <div class="container">
        <div class="alert alert-success"><?php echo $message; ?></div>
    </div>
    <?php endif; ?>

    <section class="properties-section">
        <div class="container">
            <div class="section-header">
                <h2>Vos Annonces Actives</h2>
                <button class="btn-primary" onclick="location.href='add_listing.php'">
                    <i class="fas fa-plus"></i> Ajouter une Annonce
                </button>
            </div>
            
            <div class="properties-grid" id="propertiesGrid">
                <?php if (!empty($properties)): ?>
                    <?php foreach ($properties as $property): ?>
                        <div class="property-card">
                            <div class="property-image" style="background-image: url('<?php echo !empty($property['image_url']) ? '../' . htmlspecialchars($property['image_url']) : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800'; ?>');">
                                <span class="property-badge"><?php echo t($property['status']); ?></span>
                            </div>
                            <div class="property-info">
                                <div class="property-price"><?php echo number_format($property['price'], 0, ',', ' '); ?> €</div>
                                <div class="property-address"><?php echo htmlspecialchars($property['address']); ?></div>
                                <div class="property-details">
                                    <?php if ($property['bedrooms']): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-bed"></i>
                                            <span><?php echo $property['bedrooms']; ?> ch</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($property['bathrooms']): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-bath"></i>
                                            <span><?php echo $property['bathrooms']; ?> sdb</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($property['area_sqm']): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-ruler-combined"></i>
                                            <span><?php echo $property['area_sqm']; ?> m²</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="property-actions">
                                    <button class="btn-small btn-secondary" onclick="location.href='../pages/edit_property.php?id=<?php echo $property['id']; ?>'"><?php echo t('edit'); ?></button>
                                    <button class="btn-small btn-danger" onclick="deleteProperty(<?php echo $property['id']; ?>)"><?php echo t('delete'); ?></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-properties">
                        <p><?php echo t('no_properties_found'); ?></p>
                        <button class="btn-primary" onclick="location.href='../pages/add_listing.php'"><?php echo t('add_listing'); ?></button>
                    </div>
                <?php endif; ?>
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
        
        .property-badge.pending {
            background: #ffc107;
            color: #212529;
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
        
        /* Alert styles */
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
            
            .property-details {
                flex-direction: column;
                gap: 10px;
            }
            
            .property-actions {
                flex-direction: column;
            }
            
            .form-row {
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

        function deleteProperty(propertyId) {
            if (confirm('<?php echo t('confirm_delete_property'); ?>')) {
                fetch('../api/delete_property.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ property_id: propertyId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('<?php echo t('property_deleted_success'); ?>');
                        location.reload();
                    } else {
                        alert('<?php echo t('error_deleting_property'); ?>: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('<?php echo t('error_deleting_property'); ?>');
                });
            }
        }
    </script>
</body>
</html>