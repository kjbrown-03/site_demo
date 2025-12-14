<?php
session_start();
require_once 'config.php';

// Simple translation function
function t($key) {
    $translations = [
        'fr' => [
            'account_settings' => 'Paramètres du Compte',
            'personalize_experience' => 'Personnalisez votre expérience',
            'profile_picture' => 'Photo de Profil',
            'profile' => 'Profil',
            'language_and_theme' => 'Langue & Thème',
            'user_information' => 'Informations Utilisateur',
            'notifications' => 'Notifications'
        ]
    ];
    
    $currentLang = 'fr';
    if (isset($translations[$currentLang][$key])) {
        return $translations[$currentLang][$key];
    }
    return $key;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables with default values
$username = $_SESSION['username'] ?? 'Unknown';
$userRole = $_SESSION['role'] ?? 'user';

// Simple test data
$firstName = 'John';
$lastName = 'Doe';
$email = 'john.doe@example.com';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('account_settings'); ?> - ImmoHome</title>
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
                    <li><a href="my_properties.php">Mes Propriétés</a></li>
                    <li><a href="add_property.php">Ajouter</a></li>
                    <li><a href="my_sales.php">Mes Ventes</a></li>
                    <li><a href="favorites.php">Favoris</a></li>
                </ul>
                <div class="nav-actions">
                    <div class="user-avatar" onclick="toggleProfileDropdown()">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                    <div id="profileDropdown" class="profile-dropdown-content">
                        <div class="profile-info">
                            <div class="profile-name"><?php echo htmlspecialchars($username); ?></div>
                            <div class="profile-role"><?php echo ucfirst($userRole); ?></div>
                        </div>
                        <a href="account_settings.php"><i class="fas fa-cog"></i> Paramètres</a>
                        <a href="account_settings.php#language-theme"><i class="fas fa-paint-brush"></i> Langue & Thème</a>
                        <a href="account_settings.php#user-info"><i class="fas fa-address-card"></i> Informations Utilisateur</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section class="settings-hero">
        <div class="container">
            <h1><?php echo t('account_settings'); ?></h1>
            <p><?php echo t('personalize_experience'); ?></p>
        </div>
    </section>

    <section class="settings-content">
        <div class="container">
            <div class="settings-grid">
                <div class="settings-sidebar">
                    <div class="settings-menu">
                        <a href="#profile-picture" class="settings-menu-item active">
                            <i class="fas fa-camera"></i>
                            <span><?php echo t('profile_picture'); ?></span>
                        </a>
                        <a href="#profile" class="settings-menu-item">
                            <i class="fas fa-user"></i>
                            <span><?php echo t('profile'); ?></span>
                        </a>
                        <a href="#language-theme" class="settings-menu-item">
                            <i class="fas fa-paint-brush"></i>
                            <span><?php echo t('language_and_theme'); ?></span>
                        </a>
                        <a href="#user-info" class="settings-menu-item">
                            <i class="fas fa-address-card"></i>
                            <span><?php echo t('user_information'); ?></span>
                        </a>
                        <a href="#notifications" class="settings-menu-item">
                            <i class="fas fa-bell"></i>
                            <span><?php echo t('notifications'); ?></span>
                        </a>
                    </div>
                </div>
                
                <div class="settings-main">
                    <div class="settings-card" id="profile-picture">
                        <div class="settings-header">
                            <h2><?php echo t('profile_picture'); ?></h2>
                            <p>Changez votre photo de profil</p>
                        </div>
                        <div class="profile-picture-section">
                            <div class="profile-placeholder">
                                <i class="fas fa-user-circle fa-5x"></i>
                                <p>Aucune photo de profil</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
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