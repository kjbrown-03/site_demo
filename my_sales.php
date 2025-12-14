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
    <title>Mes Ventes - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php renderNavigation('my_sales.php', $username, $userRole); ?>
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
                        <h3>€1,245,000</h3>
                        <p>Revenus Totaux</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-content">
                        <h3>12</h3>
                        <p>Propriétés Vendues</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>15.3%</h3>
                        <p>Taux de Conversion</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3>4.8/5</h3>
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
                        <tr>
                            <td>12 Déc 2024</td>
                            <td>123 Main Street, Paris</td>
                            <td>€485,000</td>
                            <td>€24,250</td>
                            <td><span class="status-badge completed">Complétée</span></td>
                        </tr>
                        <tr>
                            <td>28 Nov 2024</td>
                            <td>45 City Avenue, Lyon</td>
                            <td>€325,000</td>
                            <td>€16,250</td>
                            <td><span class="status-badge completed">Complétée</span></td>
                        </tr>
                        <tr>
                            <td>15 Nov 2024</td>
                            <td>78 Hill Road, Nice</td>
                            <td>€629,000</td>
                            <td>€31,450</td>
                            <td><span class="status-badge completed">Complétée</span></td>
                        </tr>
                        <tr>
                            <td>3 Nov 2024</td>
                            <td>102 Park Lane, Marseille</td>
                            <td>€295,000</td>
                            <td>€14,750</td>
                            <td><span class="status-badge completed">Complétée</span></td>
                        </tr>
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