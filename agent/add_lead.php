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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Prospect - ImmoHome</title>
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
                    <li><a href="my_listings.php">Mes Annonces</a></li>
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
            <h1>Ajouter un Prospect</h1>
            <p>Ajouter un nouveau prospect/client potentiel</p>
        </div>
    </section>

    <section class="lead-form-section">
        <div class="container">
            <div class="form-container">
                <h2>Informations sur le Prospect</h2>
                <form id="leadForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">Prénom</label>
                            <input type="text" id="firstName" name="firstName" placeholder="Prénom" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lastName">Nom</label>
                            <input type="text" id="lastName" name="lastName" placeholder="Nom" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="adresse@email.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <input type="tel" id="phone" name="phone" placeholder="+33 1 23 45 67 89">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="interest">Intérêt</label>
                        <select id="interest" name="interest" required>
                            <option value="">Sélectionnez un intérêt</option>
                            <option value="house">Achat Maison</option>
                            <option value="apartment">Achat Appartement</option>
                            <option value="rent">Location</option>
                            <option value="investment">Investissement</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="budget">Budget (€)</label>
                        <input type="number" id="budget" name="budget" placeholder="Budget maximal">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Localisation Souhaitée</label>
                        <input type="text" id="location" name="location" placeholder="Ville ou région">
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Informations supplémentaires sur le prospect..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="history.back()">Annuler</button>
                        <button type="submit" class="btn-primary">Ajouter le Prospect</button>
                    </div>
                </form>
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
        
        .lead-form-section {
            padding: 80px 0;
        }
        
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-container h2 {
            margin-top: 0;
            margin-bottom: 30px;
            color: #1A1A1A;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #006AFF;
            box-shadow: 0 0 0 3px rgba(0, 106, 255, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
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
            .form-container {
                padding: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .form-actions button {
                width: 100%;
            }
        }
    </style>
    
    <script>
        document.getElementById('leadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // In a real application, this would submit the form data
            alert('Prospect ajouté avec succès!');
            window.location.href = 'client_leads.php';
        });
        
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