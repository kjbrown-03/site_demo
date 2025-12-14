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
$agentId = $_SESSION['user_id'];

// Fetch appointments for this agent
try {
    $stmt = $pdo->prepare("SELECT a.*, u.username as client_name, p.address as property_address, p.city as property_city FROM appointments a LEFT JOIN users u ON a.client_id = u.id LEFT JOIN properties p ON a.property_id = p.id WHERE a.agent_id = ? ORDER BY a.appointment_date ASC, a.appointment_time ASC");
    $stmt->execute([$agentId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $appointments = [];
    error_log("Error fetching appointments: " . $e->getMessage());
}
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
        <?php renderNavigation('appointments.php', $username, $userRole); ?>
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
                <?php if (empty($appointments)): ?>
                    <p>Vous n'avez pas encore de rendez-vous programmés.</p>
                <?php else: ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-date">
                                <div class="date-day"><?= date('d', strtotime($appointment['appointment_date'])) ?></div>
                                <div class="date-month"><?= strtoupper(date('M', strtotime($appointment['appointment_date']))) ?></div>
                            </div>
                            <div class="appointment-details">
                                <h3><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $appointment['appointment_type']))) ?></h3>
                                <?php if (!empty($appointment['property_address'])): ?>
                                    <p class="appointment-property"><?= htmlspecialchars($appointment['property_address'] . ', ' . $appointment['property_city']) ?></p>
                                <?php else: ?>
                                    <p class="appointment-property">Consultation en ligne</p>
                                <?php endif; ?>
                                <p class="appointment-client">Avec <?= htmlspecialchars($appointment['client_name']) ?></p>
                                <p class="appointment-time"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($appointment['appointment_time'])) ?> - <?= date('H:i', strtotime($appointment['appointment_time']) + 3600) ?></p>
                            </div>
                            <div class="appointment-actions">
                                <button class="btn-small btn-secondary">Modifier</button>
                                <button class="btn-small btn-danger">Annuler</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
        
    </script>
</body>
</html>