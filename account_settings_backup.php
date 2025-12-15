session_start();
require_once 'config.php';

// Simple translation function for testing
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
            'customize_interface' => 'Personnalisez l\'interface',
            'manage_personal_details' => 'Gérez vos informations personnelles',
            'manage_notification_preferences' => 'Gérez vos préférences de notification',
            'language' => 'Langue',
            'theme' => 'Thème',
            'light_theme' => 'Clair',
            'dark_theme' => 'Sombre',
            'english' => 'Anglais',
            'french' => 'Français',
            'email_notifications' => 'Notifications par Email',
            'search_alerts' => 'Alertes de Recherche',
            'newsletter' => 'Newsletter',
            'receive_important_emails' => 'Recevoir des notifications importantes par email',
            'receive_property_alerts' => 'Recevoir des alertes pour les nouvelles propriétés correspondant à vos recherches',
            'receive_monthly_newsletter' => 'Recevoir notre newsletter mensuelle avec les dernières tendances immobilières',
            'cancel' => 'Annuler',
            'save_changes' => 'Enregistrer les modifications'
        ]
    ];
    
    $currentLang = 'fr'; // Default to French
    if (isset($translations[$currentLang][$key])) {
        return $translations[$currentLang][$key];
    }
    return $key;
}

// Function to check if current page is active
function isActivePage($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $page;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$username = $_SESSION['username'] ?? '';
$userRole = $_SESSION['role'] ?? '';

// Handle form submission
$message = '';
$error = '';

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

// Get user information
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
        $languagePreference = $user['language_preference'] ?? 'fr';
        $themePreference = $user['theme_preference'] ?? 'light';
        $profilePicture = $user['profile_picture'] ?? '';
        $emailNotifications = $user['email_notifications'] ?? 0;
        $searchAlerts = $user['search_alerts'] ?? 0;
        $newsletter = $user['newsletter'] ?? 0;
        // Update username and userRole from database if available
        $username = $user['username'] ?? $username;
        $userRole = $user['role'] ?? $userRole;
    } else {
        $error = "Utilisateur non trouvé.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des informations utilisateur.";
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['upload_picture'])) {
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    $languagePreference = $_POST['language'] ?? 'fr';
    $themePreference = $_POST['theme'] ?? 'light';
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, city = ?, country = ?, language_preference = ?, theme_preference = ? WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $email, $phone, $city, $country, $languagePreference, $themePreference, $_SESSION['user_id']]);
        
        // Update session variables
        $_SESSION['language'] = $languagePreference;
        $_SESSION['theme'] = $themePreference;
        
        $message = "Compte mis à jour avec succès!";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour du compte.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres du Compte - ImmoHome</title>
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
                        <?php if (!empty($profilePicture) && file_exists($profilePicture)): ?>
                            <img src="<?php echo $profilePicture; ?>" alt="Profile" class="profile-img">
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
            <?php if (!empty($message)): ?>
                <div class="alert success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
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
                    <!-- Profile Picture Section -->
                    <div class="settings-card" id="profile-picture">
                        <div class="settings-header">
                            <h2><?php echo t('profile_picture'); ?></h2>
                            <p><?php echo t('change_your_profile_picture'); ?></p>
                        </div>
                        
                        <div class="profile-picture-section">
                            <div class="current-picture">
                                <?php if (!empty($profilePicture) && file_exists($profilePicture)): ?>
                                    <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-preview">
                                <?php else: ?>
                                    <div class="profile-placeholder">
                                        <i class="fas fa-user-circle fa-5x"></i>
                                        <p><?php echo t('no_profile_picture'); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <form class="picture-upload-form" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="profile_picture"><?php echo t('select_new_picture'); ?></label>
                                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                                    <p class="help-text"><?php echo t('supported_formats'); ?></p>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="upload_picture" class="btn-primary"><?php echo t('upload_picture'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Profile Information Section -->
                    <div class="settings-card" id="profile">
                        <div class="settings-header">
                            <h2><?php echo t('update_profile'); ?></h2>
                            <p><?php echo t('personalize_experience'); ?></p>
                        </div>
                        
                        <form class="settings-form" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName"><?php echo t('first_name'); ?></label>
                                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" placeholder="<?php echo t('first_name'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName"><?php echo t('last_name'); ?></label>
                                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" placeholder="<?php echo t('last_name'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email"><?php echo t('email'); ?></label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="<?php echo t('email'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone"><?php echo t('phone'); ?></label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="<?php echo t('phone'); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city"><?php echo t('city'); ?></label>
                                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" placeholder="<?php echo t('city'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="country"><?php echo t('country'); ?></label>
                                    <select id="country" name="country">
                                        <option value=""><?php echo t('country'); ?></option>
                                        <option value="fr" <?php echo $country === 'fr' ? 'selected' : ''; ?>>France</option>
                                        <option value="be" <?php echo $country === 'be' ? 'selected' : ''; ?>>Belgique</option>
                                        <option value="ch" <?php echo $country === 'ch' ? 'selected' : ''; ?>>Suisse</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="reset" class="btn-secondary"><?php echo t('cancel'); ?></button>
                                <button type="submit" class="btn-primary"><?php echo t('save_changes'); ?></button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Language and Theme Section -->
                    <div class="settings-card" id="language-theme">
                        <div class="settings-header">
                            <h2><?php echo t('language_and_theme'); ?></h2>
                            <p><?php echo t('customize_interface'); ?></p>
                        </div>
                        
                        <form class="settings-form" method="POST">
                            <div class="form-group">
                                <label for="language"><?php echo t('language'); ?></label>
                                <select id="language" name="language">
                                    <option value="fr" <?php echo $languagePreference === 'fr' ? 'selected' : ''; ?>><?php echo t('french'); ?></option>
                                    <option value="en" <?php echo $languagePreference === 'en' ? 'selected' : ''; ?>><?php echo t('english'); ?></option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="theme"><?php echo t('theme'); ?></label>
                                <select id="theme" name="theme">
                                    <option value="light" <?php echo $themePreference === 'light' ? 'selected' : ''; ?>><?php echo t('light_theme'); ?></option>
                                    <option value="dark" <?php echo $themePreference === 'dark' ? 'selected' : ''; ?>><?php echo t('dark_theme'); ?></option>
                                </select>
                            </div>
                            
                            <div class="form-actions">
                                <button type="reset" class="btn-secondary"><?php echo t('cancel'); ?></button>
                                <button type="submit" class="btn-primary"><?php echo t('save_changes'); ?></button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- User Information Section -->
                    <div class="settings-card" id="user-info">
                        <div class="settings-header">
                            <h2><?php echo t('user_information'); ?></h2>
                            <p><?php echo t('manage_personal_details'); ?></p>
                        </div>
                        
                        <form class="settings-form" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName-info"><?php echo t('first_name'); ?></label>
                                    <input type="text" id="firstName-info" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" placeholder="<?php echo t('first_name'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName-info"><?php echo t('last_name'); ?></label>
                                    <input type="text" id="lastName-info" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" placeholder="<?php echo t('last_name'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email-info"><?php echo t('email'); ?></label>
                                <input type="email" id="email-info" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="<?php echo t('email'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone-info"><?php echo t('phone'); ?></label>
                                <input type="tel" id="phone-info" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="<?php echo t('phone'); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city-info"><?php echo t('city'); ?></label>
                                    <input type="text" id="city-info" name="city" value="<?php echo htmlspecialchars($city); ?>" placeholder="<?php echo t('city'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="country-info"><?php echo t('country'); ?></label>
                                    <select id="country-info" name="country">
                                        <option value=""><?php echo t('country'); ?></option>
                                        <option value="fr" <?php echo $country === 'fr' ? 'selected' : ''; ?>>France</option>
                                        <option value="be" <?php echo $country === 'be' ? 'selected' : ''; ?>>Belgique</option>
                                        <option value="ch" <?php echo $country === 'ch' ? 'selected' : ''; ?>>Suisse</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="reset" class="btn-secondary"><?php echo t('cancel'); ?></button>
                                <button type="submit" class="btn-primary"><?php echo t('save_changes'); ?></button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Notifications Section -->
                    <div class="settings-card" id="notifications">
                        <div class="settings-header">
                            <h2><?php echo t('notifications'); ?></h2>
                            <p><?php echo t('manage_notification_preferences'); ?></p>
                        </div>
                        
                        <div class="preferences-list">
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3><?php echo t('email_notifications'); ?></h3>
                                    <p><?php echo t('receive_important_emails'); ?></p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="email-notifications" name="email-notifications" <?php echo $emailNotifications ? 'checked' : ''; ?>>
                                    <label for="email-notifications" class="switch-label"></label>
                                </div>
                            </div>
                            
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3><?php echo t('search_alerts'); ?></h3>
                                    <p><?php echo t('receive_property_alerts'); ?></p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="search-alerts" name="search-alerts" <?php echo $searchAlerts ? 'checked' : ''; ?>>
                                    <label for="search-alerts" class="switch-label"></label>
                                </div>
                            </div>
                            
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3><?php echo t('newsletter'); ?></h3>
                                    <p><?php echo t('receive_monthly_newsletter'); ?></p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="newsletter" name="newsletter" <?php echo $newsletter ? 'checked' : ''; ?>>
                                    <label for="newsletter" class="switch-label"></label>
                                </div>
                            </div>
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
                    <p>Votre partenaire de confiance pour trouver la maison parfaite.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Achat</h4>
                    <ul>
                        <li><a href="buy.php">Maisons</a></li>
                        <li><a href="buy.php">Appartements</a></li>
                        <li><a href="buy.php">Villas</a></li>
                        <li><a href="buy.php">Terrains</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Évaluation Gratuite</a></li>
                        <li><a href="financing.php">Financement</a></li>
                        <li><a href="#">Assurance</a></li>
                        <li><a href="#">Déménagement</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Entreprise</h4>
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

    <style>
        .settings-hero {
            margin-top: 70px;
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .settings-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .settings-hero p {
            font-size: 20px;
            opacity: 0.9;
        }
        
        .settings-content {
            padding: 80px 0;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }
        
        .settings-sidebar {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 25px;
            height: fit-content;
        }
        
        .settings-menu-item {
            display: flex;
            align-items: center;
            padding: 15px;
            color: #6B6B6B;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .settings-menu-item:hover,
        .settings-menu-item.active {
            background: #006AFF;
            color: white;
        }
        
        .settings-menu-item i {
            margin-right: 15px;
            font-size: 18px;
        }
        
        .settings-main {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .settings-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }
        
        .settings-header {
            margin-bottom: 30px;
        }
        
        .settings-header h2 {
            font-size: 28px;
            color: #1A1A1A;
            margin-bottom: 10px;
        }
        
        .settings-header p {
            color: #6B6B6B;
            margin: 0;
        }
        
        .settings-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #1A1A1A;
        }
        
        .form-group input,
        .form-group select {
            padding: 15px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #006AFF;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .preferences-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .preference-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #E0E0E0;
        }
        
        .preference-item:last-child {
            border-bottom: none;
        }
        
        .preference-info h3 {
            font-size: 18px;
            color: #1A1A1A;
            margin-bottom: 5px;
        }
        
        .preference-info p {
            color: #6B6B6B;
            margin: 0;
        }
        
        .toggle-switch {
            position: relative;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .switch-label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
            width: 60px;
            height: 34px;
        }
        
        .switch-label:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .switch-label {
            background-color: #006AFF;
        }
        
        input:checked + .switch-label:before {
            transform: translateX(26px);
        }
        
        /* Profile picture styles */
        .profile-picture-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }
        
        .current-picture {
            text-align: center;
        }
        
        .profile-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #006AFF;
        }
        
        .profile-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            color: #666;
        }
        
        .profile-placeholder i {
            margin-bottom: 10px;
        }
        
        .picture-upload-form {
            width: 100%;
            max-width: 500px;
        }
        
        .help-text {
            font-size: 14px;
            color: #6B6B6B;
            margin-top: 5px;
        }
        
        /* User avatar styles */
        .user-avatar {
            cursor: pointer;
            position: relative;
        }
        
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        @media (max-width: 768px) {
            .settings-hero h1 {
                font-size: 36px;
            }
            
            .settings-hero p {
                font-size: 18px;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .preference-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Update active menu item
                    document.querySelectorAll('.settings-menu-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });
        
        // Set active menu item based on scroll position
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.settings-card');
            const menuItems = document.querySelectorAll('.settings-menu-item');
            
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (window.scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });
            
            menuItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href') === '#' + current) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>