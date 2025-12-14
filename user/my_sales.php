<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header('Location: ../auth/login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];

// Fetch sales data from database
try {
    // Fetch sales records
    $stmt = $pdo->prepare("SELECT o.*, p.title, p.address, p.city FROM orders o JOIN properties p ON o.property_id = p.id WHERE p.agent_id = ? ORDER BY o.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate statistics
    $totalRevenue = 0;
    $totalSales = count($sales);
    
    foreach ($sales as $sale) {
        $totalRevenue += $sale['total_amount'];
    }
    
    // Commission rate (you can adjust this as needed)
    $commissionRate = 0.05; // 5%
    $totalCommission = $totalRevenue * $commissionRate;
    
} catch(PDOException $e) {
    $sales = [];
    $totalRevenue = 0;
    $totalSales = 0;
    $totalCommission = 0;
    error_log("Error fetching sales data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Ventes - ImmoHome</title>
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
                    <li><a href="seller_dashboard.php">Dashboard</a></li>
                    <li><a href="my_properties.php">Mes Propriétés</a></li>
                    <li><a href="add_property.php">Ajouter</a></li>
                    <li><a href="my_sales.php" class="active">Mes Ventes</a></li>
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
            <h1>Mes Ventes</h1>
            <p>Suivre vos ventes et performances</p>
        </div>
    </section>

    <section class="sales-overview-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>€<?php echo number_format($totalRevenue, 0, ',', ' '); ?></h3>
                        <p>Revenus Totaux</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalSales; ?></h3>
                        <p>Propriétés Vendues</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>€<?php echo number_format($totalCommission, 0, ',', ' '); ?></h3>
                        <p>Commission Totale</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3>4.5/5</h3>
                        <p>Note Moyenne</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="sales-history-section">
        <div class="container">
            <div class="section-header">
                <h2>Historique des Ventes</h2>
            </div>
            
            <div class="sales-table-container">
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Propriété</th>
                            <th>Prix de Vente</th>
                            <th>Commission</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sales)): ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($sale['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($sale['title'] . ', ' . $sale['city']); ?></td>
                                    <td>€<?php echo number_format($sale['total_amount'], 0, ',', ' '); ?></td>
                                    <td>€<?php echo number_format($sale['total_amount'] * 0.05, 0, ',', ' '); ?></td>
                                    <td><span class="status-badge completed">Complétée</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Aucune vente enregistrée</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
        
        .sales-overview-section {
            padding: 40px 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
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
            font-size: 28px;
            margin-bottom: 5px;
            color: #1A1A1A;
        }
        
        .stat-content p {
            color: #6B6B6B;
            margin: 0;
            font-size: 16px;
        }
        
        .section-header {
            margin: 40px 0 20px 0;
        }
        
        .section-header h2 {
            margin: 0;
        }
        
        .sales-history-section {
            padding-bottom: 80px;
        }
        
        .sales-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .sales-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .sales-table th,
        .sales-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .sales-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .sales-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-badge.completed {
            background: #28a745;
            color: white;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .sales-table-container {
                overflow-x: auto;
            }
            
            .sales-table {
                min-width: 600px;
            }
        }
    </style>
</body>
</html>