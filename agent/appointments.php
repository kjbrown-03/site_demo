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
    <title>Rendez-vous - ImmoHome</title>
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
                    <li><a href="appointments.php" class="active">Rendez-vous</a></li>
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
            <h1>Rendez-vous</h1>
            <p>Gérer vos rendez-vous avec les clients</p>
        </div>
    </section>

    <section class="appointments-section">
        <div class="container">
            <div class="section-header">
                <h2>Vos Rendez-vous à Venir</h2>
                <button class="btn-primary" onclick="location.href='schedule_appointment.php'">
                    <i class="fas fa-plus"></i> Planifier un Rendez-vous
                </button>
            </div>
            
            <div class="appointments-list">
                <div class="appointment-card">
                    <div class="appointment-date">
                        <div class="date-day">15</div>
                        <div class="date-month">DEC</div>
                    </div>
                    <div class="appointment-details">
                        <h3>Visite de Propriété</h3>
                        <p class="appointment-property">123 Main Street, Paris</p>
                        <p class="appointment-client">Avec Marie Dubois</p>
                        <p class="appointment-time"><i class="fas fa-clock"></i> 14:30 - 15:30</p>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn-small btn-secondary">Modifier</button>
                        <button class="btn-small btn-danger">Annuler</button>
                    </div>
                </div>
                
                <div class="appointment-card">
                    <div class="appointment-date">
                        <div class="date-day">18</div>
                        <div class="date-month">DEC</div>
                    </div>
                    <div class="appointment-details">
                        <h3>Signature de Contrat</h3>
                        <p class="appointment-property">45 City Avenue, Lyon</p>
                        <p class="appointment-client">Avec Jean Martin</p>
                        <p class="appointment-time"><i class="fas fa-clock"></i> 10:00 - 11:00</p>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn-small btn-secondary">Modifier</button>
                        <button class="btn-small btn-danger">Annuler</button>
                    </div>
                </div>
                
                <div class="appointment-card">
                    <div class="appointment-date">
                        <div class="date-day">22</div>
                        <div class="date-month">DEC</div>
                    </div>
                    <div class="appointment-details">
                        <h3>Discussion de Projet</h3>
                        <p class="appointment-property">Consultation en ligne</p>
                        <p class="appointment-client">Avec Pierre Lambert</p>
                        <p class="appointment-time"><i class="fas fa-clock"></i> 16:00 - 17:00</p>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn-small btn-secondary">Modifier</button>
                        <button class="btn-small btn-danger">Annuler</button>
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
        
        .appointments-section {
            padding: 80px 0;
        }
        
        .appointments-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .appointment-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .appointment-date {
            background: #006AFF;
            color: white;
            border-radius: 8px;
            width: 70px;
            height: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .date-day {
            font-size: 24px;
            font-weight: 700;
        }
        
        .date-month {
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .appointment-details {
            flex: 1;
        }
        
        .appointment-details h3 {
            margin: 0 0 10px 0;
            color: #1A1A1A;
        }
        
        .appointment-property {
            margin: 0 0 5px 0;
            color: #495057;
            font-weight: 500;
        }
        
        .appointment-client {
            margin: 0 0 5px 0;
            color: #6B6B6B;
        }
        
        .appointment-time {
            margin: 0;
            color: #6B6B6B;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .appointment-actions {
            display: flex;
            flex-direction: column;
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
        
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
            
            .appointment-card {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .appointment-actions {
                flex-direction: row;
                width: 100%;
                justify-content: flex-end;
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