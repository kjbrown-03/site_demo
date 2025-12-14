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

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientId = $_POST['client'] ?? '';
    $appointmentDate = $_POST['date'] ?? '';
    $appointmentTime = $_POST['time'] ?? '';
    $appointmentType = $_POST['type'] ?? '';
    $propertyId = $_POST['property'] ?? null;
    $location = $_POST['location'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    // Validate required fields
    if (empty($clientId) || empty($appointmentDate) || empty($appointmentTime) || empty($appointmentType)) {
        $message = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        try {
            // Insert appointment into database
            $stmt = $pdo->prepare("INSERT INTO appointments (agent_id, client_id, property_id, appointment_date, appointment_time, appointment_type, location, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $agentId,
                $clientId,
                $propertyId ?: null,
                $appointmentDate,
                $appointmentTime,
                $appointmentType,
                $location,
                $notes
            ]);
            
            $message = 'Rendez-vous planifié avec succès!';
            // Redirect to appointments page after successful submission
            header('Location: appointments.php');
            exit();
        } catch (PDOException $e) {
            error_log("Error saving appointment: " . $e->getMessage());
            $message = 'Erreur lors de la planification du rendez-vous. Veuillez réessayer.';
        }
    }
}

// Fetch clients (users with role 'buyer') from database
try {
    $clientsStmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'buyer' ORDER BY username");
    $clientsStmt->execute();
    $clients = $clientsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clients = [];
    error_log("Error fetching clients: " . $e->getMessage());
}

// Fetch properties from database
try {
    $propertiesStmt = $pdo->prepare("SELECT id, address, city FROM properties ORDER BY city, address");
    $propertiesStmt->execute();
    $properties = $propertiesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $properties = [];
    error_log("Error fetching properties: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planifier un Rendez-vous - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php renderNavigation('schedule_appointment.php', $username, $userRole); ?>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1>Planifier un Rendez-vous</h1>
            <p>Planifier un nouveau rendez-vous avec un client</p>
        </div>
    </section>

    <section class="appointment-form-section">
        <div class="container">
            <div class="form-container">
                <h2>Détails du Rendez-vous</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <form id="appointmentForm" method="POST">
                    <div class="form-group">
                        <label for="client">Client</label>
                        <select id="client" name="client" required>
                            <option value="">Sélectionnez un client</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= htmlspecialchars($client['id']) ?>"><?= htmlspecialchars($client['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" id="date" name="date" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="time">Heure</label>
                            <input type="time" id="time" name="time" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="type">Type de Rendez-vous</label>
                        <select id="type" name="type" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="property_visit">Visite de Propriété</option>
                            <option value="contract_signing">Signature de Contrat</option>
                            <option value="project_discussion">Discussion de Projet</option>
                            <option value="follow_up">Suivi</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="property">Propriété (facultatif)</label>
                        <select id="property" name="property">
                            <option value="">Sélectionnez une propriété</option>
                            <?php foreach ($properties as $property): ?>
                                <option value="<?= htmlspecialchars($property['id']) ?>"><?= htmlspecialchars($property['address'] . ', ' . $property['city']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Lieu</label>
                        <input type="text" id="location" name="location" placeholder="Adresse du rendez-vous">
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Informations supplémentaires sur le rendez-vous..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="history.back()">Annuler</button>
                        <button type="submit" class="btn-primary">Planifier le Rendez-vous</button>
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
        
        .appointment-form-section {
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
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        // Set default date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date').value = today;
        });
        
        // Form validation is handled server-side
        // This script can be used for client-side enhancements if needed
        
        
    </script>
</body>
</html>