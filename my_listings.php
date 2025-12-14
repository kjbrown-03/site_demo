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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete' && isset($_POST['property_id'])) {
            try {
                $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ? AND agent_id = ?");
                $stmt->execute([$_POST['property_id'], $agentId]);
                $message = "Annonce supprimée avec succès!";
            } catch (PDOException $e) {
                $message = "Erreur lors de la suppression de l'annonce.";
                error_log("Error deleting property: " . $e->getMessage());
            }
        } elseif ($_POST['action'] === 'edit' && isset($_POST['property_id'])) {
            // In a real application, you would update the property in the database
            $message = "Annonce mise à jour avec succès!";
        }
    }
}

// Fetch properties for this agent
try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE agent_id = ? ORDER BY created_at DESC");
    $stmt->execute([$agentId]);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Mes Annonces - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php renderNavigation('my_listings.php', $username, $userRole); ?>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1>Mes Annonces</h1>
            <p>Gérer vos annonces immobilières</p>
        </div>
    </section>

    <?php if (isset($message)): ?>
    <div class="container">
        <div class="alert alert-success"><?php echo $message; ?></div>
    </div>
    <?php endif; ?>

    <section class="properties-section">
        <div class="container">
            <div class="section-header">
                <h2>Vos Annonces Actives</h2>
                <button class="btn-primary" onclick="location.href='add_listing.php'">
                    <i class="fas fa-plus"></i> Ajouter une Annonce
                </button>
            </div>
            
            <div class="properties-grid" id="propertiesGrid">
                <?php if (empty($properties)): ?>
                    <p>Vous n'avez pas encore d'annonces.</p>
                <?php else: ?>
                    <?php foreach ($properties as $property): ?>
                        <div class="property-card">
                            <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800');">
                                <span class="property-badge <?= htmlspecialchars($property['status']) ?>"><?= ucfirst(str_replace('_', ' ', $property['status'])) ?></span>
                                <div class="property-favorite">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </div>
                            <div class="property-info">
                                <div class="property-price">€<?= number_format($property['price'], 0, ',', ' ') ?></div>
                                <div class="property-address"><?= htmlspecialchars($property['address'] . ', ' . $property['city']) ?></div>
                                <div class="property-details">
                                    <div class="property-detail">
                                        <i class="fas fa-bed"></i>
                                        <span><?= $property['bedrooms'] ?> chambres</span>
                                    </div>
                                    <div class="property-detail">
                                        <i class="fas fa-bath"></i>
                                        <span><?= $property['bathrooms'] ?> salles de bain</span>
                                    </div>
                                    <div class="property-detail">
                                        <i class="fas fa-ruler-combined"></i>
                                        <span><?= $property['area_sqm'] ?> m²</span>
                                    </div>
                                </div>
                                <div class="property-actions">
                                    <button class="btn-small btn-secondary" onclick="editProperty(<?= $property['id'] ?>)">Modifier</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                        <button type="submit" class="btn-small btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Edit Property Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Modifier l'Annonce</h2>
            <form id="editPropertyForm">
                <input type="hidden" id="editPropertyId" name="property_id">
                <div class="form-group">
                    <label for="editPrice">Prix (€)</label>
                    <input type="number" id="editPrice" name="price" placeholder="Entrez le prix" required>
                </div>
                
                <div class="form-group">
                    <label for="editAddress">Adresse</label>
                    <input type="text" id="editAddress" name="address" placeholder="Adresse complète" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editBedrooms">Chambres</label>
                        <input type="number" id="editBedrooms" name="bedrooms" min="0" placeholder="Nombre de chambres">
                    </div>
                    
                    <div class="form-group">
                        <label for="editBathrooms">Salles de bain</label>
                        <input type="number" id="editBathrooms" name="bathrooms" min="0" placeholder="Nombre de salles de bain">
                    </div>
                    
                    <div class="form-group">
                        <label for="editArea">Surface (m²)</label>
                        <input type="number" id="editArea" name="area" min="0" placeholder="Surface totale">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="editStatus">Statut</label>
                    <select id="editStatus" name="status">
                        <option value="active">Active</option>
                        <option value="pending">En Attente</option>
                        <option value="sold">Vendue</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn-primary">Mettre à Jour</button>
                </div>
            </form>
        </div>
    </div>

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
        
        .properties-section {
            padding: 80px 0;
        }
        
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .property-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .property-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .property-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .property-badge.active {
            background: #28a745;
            color: white;
        }
        
        .property-badge.pending {
            background: #ffc107;
            color: #212529;
        }
        
        .property-favorite {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        .property-info {
            padding: 20px;
        }
        
        .property-price {
            font-size: 24px;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 10px;
        }
        
        .property-address {
            color: #6B6B6B;
            margin-bottom: 15px;
        }
        
        .property-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .property-detail {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #6B6B6B;
        }
        
        .property-actions {
            display: flex;
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
        
        
        
        /* Alert styles */
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 600px;
            position: relative;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 15px;
        }
        
        .close:hover {
            color: #000;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
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
        
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
            
            .properties-grid {
                grid-template-columns: 1fr;
            }
            
            .property-details {
                flex-direction: column;
                gap: 10px;
            }
            
            .property-actions {
                flex-direction: column;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <script>
        // Close modal when clicking outside
        window.onclick = function(event) {
            // Close modal when clicking outside
            if (event.target.classList.contains('modal')) {
                closeModal();
            }
        }
        
        function editProperty(propertyId) {
            // In a real application, you would fetch the property details from the server
            // For now, we'll just populate with sample data
            const modal = document.getElementById('editModal');
            document.getElementById('editPropertyId').value = propertyId;
            
            // Populate form fields with existing data based on propertyId
            if (propertyId == 1) {
                document.getElementById('editPrice').value = '485000';
                document.getElementById('editAddress').value = '123 Main Street, Paris';
                document.getElementById('editBedrooms').value = '4';
                document.getElementById('editBathrooms').value = '2';
                document.getElementById('editArea').value = '1883';
                document.getElementById('editStatus').value = 'active';
            } else if (propertyId == 2) {
                document.getElementById('editPrice').value = '325000';
                document.getElementById('editAddress').value = '45 City Avenue, Lyon';
                document.getElementById('editBedrooms').value = '3';
                document.getElementById('editBathrooms').value = '2';
                document.getElementById('editArea').value = '1440';
                document.getElementById('editStatus').value = 'pending';
            }
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        document.getElementById('editPropertyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // In a real application, this would submit the form data to update the property
            alert('Annonce mise à jour avec succès!');
            closeModal();
        });
    </script>
</body>
</html>