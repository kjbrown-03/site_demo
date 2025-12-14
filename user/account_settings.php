<?php
session_start();
require_once dirname(__DIR__) . '/config.php';

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
            'notifications' => 'Notifications',
            'change_your_profile_picture' => 'Changez votre photo de profil',
            'no_profile_picture' => 'Aucune photo de profil',
            'select_new_picture' => 'Sélectionner une nouvelle photo',
            'supported_formats' => 'Formats supportés: JPG, JPEG, PNG, GIF',
            'upload_picture' => 'Télécharger la photo',
            'update_profile' => 'Mettre à jour le profil',
            'first_name' => 'Prénom',
            'last_name' => 'Nom',
            'email' => 'Adresse Email',
            'phone' => 'Numéro de Téléphone',
            'city' => 'Ville',
            'country' => 'Pays',
            'save_changes' => 'Enregistrer les modifications',
            'cancel' => 'Annuler'
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
    header('Location: ../auth/login.php');
    exit();
}

// Initialize variables
$username = $_SESSION['username'] ?? 'Unknown';
$userRole = $_SESSION['role'] ?? 'user';

// Handle form submission
$message = '';
$error = '';

// Get user information from database
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $firstName = $user['first_name'] ?? '';
        $lastName = $user['last_name'] ?? '';
        $email = $user['email'] ?? '';
        $phone = $user['phone'] ?? '';
        $city = $user['city'] ?? '';
        $country = $user['country'] ?? '';
        $profilePicture = $user['profile_picture'] ?? '';
    } else {
        $error = "Utilisateur non trouvé.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des informations utilisateur.";
}

// Handle profile picture upload
if (isset($_POST['upload_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/profile_pictures/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        
        // Check if file extension is allowed
        if (in_array($file_extension, $allowed_extensions)) {
            // Generate unique filename
            $filename = uniqid() . '_' . $_SESSION['user_id'] . '.' . $file_extension;
            $target_file = $target_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Update database with new profile picture path
                try {
                    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                    $stmt->execute([$target_file, $_SESSION['user_id']]);
                    $profilePicture = $target_file; // Update the variable for immediate display
                    $message = "Photo de profil mise à jour avec succès!";
                } catch (PDOException $e) {
                    $error = "Erreur lors de la mise à jour de la photo de profil.";
                }
            } else {
                $error = "Erreur lors du téléchargement de l'image.";
            }
        } else {
            $error = "Format de fichier non autorisé. Veuillez utiliser JPG, JPEG, PNG ou GIF.";
        }
    } else {
        $error = "Veuillez sélectionner une image à télécharger.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('account_settings'); ?> - ImmoHome</title>
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
                    <?php if ($userRole === 'seller'): ?>
                        <li><a href="seller_dashboard.php">Dashboard</a></li>
                        <li><a href="my_properties.php">Mes Propriétés</a></li>
                        <li><a href="add_property.php">Ajouter</a></li>
                        <li><a href="my_sales.php">Mes Ventes</a></li>
                    <?php elseif ($userRole === 'agent'): ?>
                        <li><a href="agent_dashboard.php">Dashboard</a></li>
                        <li><a href="my_listings.php">Mes Annonces</a></li>
                        <li><a href="client_leads.php">Prospects</a></li>
                        <li><a href="appointments.php">Rendez-vous</a></li>
                    <?php elseif ($userRole === 'buyer'): ?>
                        <li><a href="buyer_dashboard.php">Dashboard</a></li>
                        <li><a href="search_properties.php">Rechercher</a></li>
                        <li><a href="my_orders.php">Mes Commandes</a></li>
                    <?php endif; ?>
                    <li><a href="favorites.php">Favoris</a></li>
                </ul>
                <div class="nav-actions">
                    <div class="user-avatar" onclick="toggleProfileDropdown()">
                        <?php if (!empty($profilePicture) && file_exists($profilePicture)): ?>
                            <img src="<?php echo $profilePicture; ?>" alt="Profile" class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-2x"></i>
                        <?php endif; ?>
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
                            <p><?php echo t('change_your_profile_picture'); ?></p>
                        </div>
                        <div class="profile-picture-section">
                            <?php if (!empty($profilePicture) && file_exists($profilePicture)): ?>
                                <div class="current-profile-picture">
                                    <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #667eea;">
                                    <p>Photo de profil actuelle</p>
                                </div>
                            <?php else: ?>
                                <div class="profile-placeholder">
                                    <i class="fas fa-user-circle fa-5x"></i>
                                    <p><?php echo t('no_profile_picture'); ?></p>
                                </div>
                            <?php endif; ?>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="input-group">
                                    <label for="profile_picture"><?php echo t('select_new_picture'); ?></label>
                                    <input type="file" name="profile_picture" id="profile_picture">
                                </div>
                                <p><?php echo t('supported_formats'); ?></p>
                                <button type="submit" name="upload_picture"><?php echo t('upload_picture'); ?></button>
                            </form>
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
    
    <style>
        .settings-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-top: 70px;
        }
        
        .settings-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .settings-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .settings-content {
            padding: 40px 0;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }
        
        .settings-sidebar {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .settings-menu-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .settings-menu-item:hover,
        .settings-menu-item.active {
            background: #667eea;
            color: white;
        }
        
        .settings-menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .settings-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .settings-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .settings-header h2 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .profile-picture-section {
            text-align: center;
        }
        
        .profile-placeholder, .current-profile-picture {
            margin-bottom: 20px;
        }
        
        .profile-placeholder i, .current-profile-picture img {
            margin-bottom: 15px;
        }
        
        .profile-placeholder i {
            color: #ddd;
        }
        
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .input-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }
        
        button:hover {
            background: #5a6fd8;
        }
        
        @media (max-width: 768px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .settings-hero h1 {
                font-size: 2rem;
            }
        }
    </style>
</body>
</html>