<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';

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
        $language = $user['language_preference'] ?? 'fr';
        $theme = $user['theme_preference'] ?? 'light';
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

// Handle user information update
if (isset($_POST['update_user_info'])) {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, city = ?, country = ? WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $email, $phone, $city, $country, $_SESSION['user_id']]);
        $message = "Informations mises à jour avec succès!";
        
        // Update session variables
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des informations.";
    }
}

// Handle language and theme update
if (isset($_POST['update_preferences'])) {
    $language = $_POST['language'] ?? 'fr';
    $theme = $_POST['theme'] ?? 'light';
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET language_preference = ?, theme_preference = ? WHERE id = ?");
        $stmt->execute([$language, $theme, $_SESSION['user_id']]);
        $message = "Préférences mises à jour avec succès!";
        
        // Update session variables
        $_SESSION['language'] = $language;
        $_SESSION['theme'] = $theme;
        
        // Also update the language preference in the database using the language handler
        setLanguage($language);
        setTheme($theme);
        
        // Update local variables to reflect changes immediately
        $GLOBALS['currentLang'] = $language;
        $GLOBALS['currentTheme'] = $theme;
        
        // Redirect to the same page to apply changes immediately
        header('Location: ' . $_SERVER['PHP_SELF'] . '?updated=1');
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des préférences.";
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(getCurrentLanguage()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('account_settings'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo getCurrentTheme(); ?>">
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='../index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
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
            <div class="hero-actions">
                <button class="btn-primary" onclick="returnToDashboard()">
                    <i class="fas fa-arrow-left"></i> Retour au Tableau de Bord
                </button>
            </div>
        </div>
    </section>

    <section class="settings-content">
        <div class="container">
            <?php if (!empty($message) || isset($_GET['updated'])): ?>
                <div class="alert success">
                    <?php echo !empty($message) ? htmlspecialchars($message) : 'Préférences mises à jour avec succès!'; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
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
                    
                    <!-- Profile Section -->
                    <div class="settings-card" id="profile" style="display: none;">
                        <div class="settings-header">
                            <h2><?php echo t('profile'); ?></h2>
                            <p><?php echo t('update_profile'); ?></p>
                        </div>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="first_name"><?php echo t('first_name'); ?></label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name"><?php echo t('last_name'); ?></label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email"><?php echo t('email'); ?></label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone"><?php echo t('phone'); ?></label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                            </div>
                            <div class="form-group">
                                <label for="city"><?php echo t('city'); ?></label>
                                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>">
                            </div>
                            <div class="form-group">
                                <label for="country"><?php echo t('country'); ?></label>
                                <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($country); ?>">
                            </div>
                            <button type="submit" name="update_user_info"><?php echo t('save_changes'); ?></button>
                        </form>
                    </div>
                    
                    <!-- Language & Theme Section -->
                    <div class="settings-card" id="language-theme" style="display: none;">
                        <div class="settings-header">
                            <h2><?php echo t('language_and_theme'); ?></h2>
                            <p>Personnalisez l'apparence et la langue</p>
                        </div>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="language"><?php echo t('language'); ?></label>
                                <select id="language" name="language">
                                    <option value="fr" <?php echo ($language == 'fr') ? 'selected' : ''; ?>>Français</option>
                                    <option value="en" <?php echo ($language == 'en') ? 'selected' : ''; ?>>English</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="theme"><?php echo t('theme'); ?></label>
                                <select id="theme" name="theme">
                                    <option value="light" <?php echo ($theme == 'light') ? 'selected' : ''; ?>><?php echo t('light'); ?></option>
                                    <option value="dark" <?php echo ($theme == 'dark') ? 'selected' : ''; ?>><?php echo t('dark'); ?></option>
                                </select>
                            </div>
                            <button type="submit" name="update_preferences"><?php echo t('save_changes'); ?></button>
                        </form>
                    </div>
                    
                    <!-- User Information Section -->
                    <div class="settings-card" id="user-info" style="display: none;">
                        <div class="settings-header">
                            <h2><?php echo t('user_information'); ?></h2>
                            <p>Gérez vos informations personnelles</p>
                        </div>
                        <p>Cette section est intégrée avec le profil ci-dessus.</p>
                    </div>
                    
                    <!-- Notifications Section -->
                    <div class="settings-card" id="notifications" style="display: none;">
                        <div class="settings-header">
                            <h2><?php echo t('notifications'); ?></h2>
                            <p>Gérez vos préférences de notification</p>
                        </div>
                        <p>Fonctionnalité de notifications à venir.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script>
        function toggleProfileDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }
        
        function returnToDashboard() {
            // Redirect to the appropriate dashboard based on user role
            const userRole = '<?php echo $userRole; ?>';
            let dashboardUrl = '../index.php'; // Default to home page
            
            switch(userRole) {
                case 'buyer':
                    dashboardUrl = '../dashboards/buyer_dashboard.php';
                    break;
                case 'seller':
                    dashboardUrl = '../dashboards/seller_dashboard.php';
                    break;
                case 'agent':
                    dashboardUrl = '../dashboards/agent_dashboard.php';
                    break;
                case 'admin':
                    dashboardUrl = '../dashboards/admin_dashboard.php';
                    break;
            }
            
            window.location.href = dashboardUrl;
        }
        
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get all menu items and content sections
            const menuItems = document.querySelectorAll('.settings-menu-item');
            const contentSections = document.querySelectorAll('.settings-card');
            
            // Add click event to each menu item
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get target section
                    const targetId = this.getAttribute('href').substring(1);
                    
                    // Hide all sections
                    contentSections.forEach(section => {
                        section.style.display = 'none';
                    });
                    
                    // Show target section
                    document.getElementById(targetId).style.display = 'block';
                    
                    // Update active menu item
                    menuItems.forEach(menuItem => {
                        menuItem.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });
            
            // Show the first section by default
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const targetSection = document.getElementById(hash);
                if (targetSection) {
                    contentSections.forEach(section => {
                        section.style.display = 'none';
                    });
                    targetSection.style.display = 'block';
                    
                    // Update active menu item
                    menuItems.forEach(menuItem => {
                        menuItem.classList.remove('active');
                        if (menuItem.getAttribute('href').substring(1) === hash) {
                            menuItem.classList.add('active');
                        }
                    });
                }
            }
        });
        
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
        
        .hero-actions {
            margin-top: 20px;
        }
        
        .settings-content {
            padding: 40px 0;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        
        .input-group, .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        
        .input-group label, .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .input-group input[type="file"], .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
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