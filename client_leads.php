<?php
session_start();
require_once 'config.php';
require_once 'includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agent') {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
$agentId = $_SESSION['user_id'];

// Fetch leads for this agent
try {
    $stmt = $pdo->prepare("SELECT * FROM leads WHERE agent_id = ? ORDER BY created_at DESC");
    $stmt->execute([$agentId]);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $leads = [];
    error_log("Error fetching leads: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prospects - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php renderNavigation('client_leads.php', $username, $userRole); ?>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1>Prospects</h1>
            <p>Gérer vos prospects et clients potentiels</p>
        </div>
    </section>

    <section class="leads-section">
        <div class="container">
            <div class="section-header">
                <h2>Vos Prospects Actifs</h2>
                <button class="btn-primary" onclick="location.href='add_lead.php'">
                    <i class="fas fa-plus"></i> Ajouter un Prospect
                </button>
            </div>
            
            <div class="leads-table-container">
                <table class="leads-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Intérêt</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Vous n'avez pas encore de prospects.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leads as $lead): ?>
                                <tr>
                                    <td><?= htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) ?></td>
                                    <td><?= htmlspecialchars($lead['email'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($lead['phone'] ?? '') ?></td>
                                    <td><?= htmlspecialchars(ucfirst($lead['interest'])) ?></td>
                                    <td><span class="status-badge <?= $lead['status'] ?>"><?= ucfirst($lead['status']) ?></span></td>
                                    <td>
                                        <button class="btn-small btn-secondary">Voir</button>
                                        <button class="btn-small btn-danger">Archiver</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
        
        .leads-section {
            padding: 80px 0;
        }
        
        .leads-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .leads-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .leads-table th,
        .leads-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .text-center {
            text-align: center;
        }
        
        .leads-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .leads-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-badge.active {
            background: #28a745;
            color: white;
        }
        
        .status-badge.pending {
            background: #ffc107;
            color: #212529;
        }
        
        .status-badge.converted {
            background: #17a2b8;
            color: white;
        }
        
        .btn-small {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            margin-right: 5px;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        
        
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
            
            .leads-table-container {
                overflow-x: auto;
            }
            
            .leads-table {
                min-width: 600px;
            }
        }
    </style>
    
    <script>
        
    </script>
</body>
</html>