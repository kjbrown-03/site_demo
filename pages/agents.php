<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Get user info
$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
$isLoggedIn = true;
$clientId = $_SESSION['user_id'];

// Check if user is a buyer - if so, redirect to buy page
if ($userRole == 'buyer') {
    header('Location: buy.php');
    exit();
}

// Handle contact form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_agent'])) {
    $agentId = intval($_POST['agent_id']);
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $interest = trim($_POST['interest']);
    $budget = !empty($_POST['budget']) ? floatval($_POST['budget']) : null;
    $locationPreference = trim($_POST['location_preference']);
    $notes = trim($_POST['notes']);
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($interest)) {
        $message = "Please fill in all required fields.";
    } else {
        try {
            // Insert lead into database
            $stmt = $pdo->prepare("INSERT INTO leads (agent_id, first_name, last_name, email, phone, interest, budget, location_preference, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $agentId,
                $firstName,
                $lastName,
                $email,
                $phone,
                $interest,
                $budget,
                $locationPreference,
                $notes
            ]);
            
            $message = "Your request has been sent successfully! The agent will contact you soon.";
        } catch (PDOException $e) {
            error_log("Error saving lead: " . $e->getMessage());
            $message = "Error sending your request. Please try again.";
        }
    }
}

// Fetch agents from database
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'agent' ORDER BY created_at DESC");
    $stmt->execute();
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $agents = [];
    $error = "Error fetching agents: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Agents - ImmoHome</title>
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
                    <li><a href="index.php"><?php echo t('home'); ?></a></li>
                    <li><a href="buy.php"><?php echo t('buy'); ?></a></li>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="rent.php"><?php echo t('rent'); ?></a></li>
                    <?php endif; ?>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="sell.php"><?php echo t('sell'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="agents.php" class="active"><?php echo t('agents'); ?></a></li>
                    <li><a href="financing.php"><?php echo t('financing'); ?></a></li>
                </ul>
                <div class="nav-actions">
                    <span class="user-welcome">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                    <a href="../auth/logout.php" class="btn-secondary">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <section class="agents-hero">
        <div class="container">
            <h1>Meet Our Expert Agents</h1>
            <p>Our professional team is ready to help you find your dream property</p>
        </div>
    </section>

    <section class="agents-section">
        <div class="container">
            <?php if (!empty($message)): ?>
                <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="section-header">
                <h2>Our Real Estate Professionals</h2>
                <p><?php echo count($agents); ?> experienced agents ready to assist you</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (count($agents) > 0): ?>
                <div class="agents-grid">
                    <?php foreach ($agents as $agent): ?>
                        <div class="agent-card">
                            <div class="agent-image">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($agent['username']); ?>&size=128&background=random" alt="<?php echo htmlspecialchars($agent['username']); ?>">
                            </div>
                            <div class="agent-info">
                                <h3><?php echo htmlspecialchars($agent['username']); ?></h3>
                                <p class="agent-email"><?php echo htmlspecialchars($agent['email']); ?></p>
                                <p class="agent-role">Real Estate Agent</p>
                                <div class="agent-stats">
                                    <div class="stat">
                                        <span class="stat-number">50+</span>
                                        <span class="stat-label">Properties Sold</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-number">10+</span>
                                        <span class="stat-label">Years Experience</span>
                                    </div>
                                </div>
                                <button class="btn-primary contact-agent" onclick="contactAgent(<?php echo $agent['id']; ?>)">Contact Agent</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-user-tie fa-3x"></i>
                    <h3>No agents found</h3>
                    <p>Check back later for our professional agents</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Looking for a Specific Agent?</h2>
                <p>Contact our support team to connect with the perfect agent for your needs</p>
                <button class="btn-primary" onclick="location.href='mailto:support@immohome.com'">Contact Support</button>
            </div>
        </div>
    </section>

    <!-- Contact Agent Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Contact Agent</h2>
            <p class="modal-subtitle">Fill in your details and we'll connect you with the right agent</p>
            <div id="modalMessage"></div>
            <form id="contactForm" method="POST">
                <input type="hidden" name="contact_agent" value="1">
                <input type="hidden" id="agentId" name="agent_id" value="">
                <div class="form-row">
                    <div class="form-group required">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="first_name" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group required">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="last_name" placeholder="Enter your last name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group required">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" placeholder="Enter your email address" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
                    </div>
                </div>
                <div class="form-group required">
                    <label for="interest">Interest</label>
                    <select id="interest" name="interest" required>
                        <option value="">Select your interest</option>
                        <option value="house">Buying a House</option>
                        <option value="apartment">Buying an Apartment</option>
                        <option value="rent">Renting</option>
                        <option value="investment">Investment</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="budget">Budget (€)</label>
                        <input type="number" id="budget" name="budget" placeholder="Your budget range">
                    </div>
                    <div class="form-group">
                        <label for="location_preference">Preferred Location</label>
                        <input type="text" id="location_preference" name="location_preference" placeholder="City or region">
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Any additional information..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Send Request</button>
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
                    <p>Your trusted partner for finding the perfect home.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Agents</h4>
                    <ul>
                        <li><a href="#">Find an Agent</a></li>
                        <li><a href="#">Become an Agent</a></li>
                        <li><a href="#">Agent Resources</a></li>
                        <li><a href="#">Agent Login</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Property Search</a></li>
                        <li><a href="#">Market Analysis</a></li>
                        <li><a href="#">Investment Advice</a></li>
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

    <script>
        function contactAgent(agentId) {
            // Set the agent ID in the hidden field
            document.getElementById('agentId').value = agentId;
            
            // Clear any previous messages
            document.getElementById('modalMessage').innerHTML = '';
            
            // Show the modal
            document.getElementById('contactModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('contactModal').style.display = 'none';
            
            // Reset the form
            document.getElementById('contactForm').reset();
            document.getElementById('agentId').value = '';
            document.getElementById('modalMessage').innerHTML = '';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('contactModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // Handle form submission with AJAX
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // Reload the page to show the message
                location.reload();
            })
            .catch(error => {
                document.getElementById('modalMessage').innerHTML = '<div class="alert error">Error sending request. Please try again.</div>';
            });
        });
    </script>

    <style>
        .agents-hero {
            margin-top: 70px;
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .agents-hero:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        }
        
        .agents-hero h1 {
            font-size: 52px;
            margin-bottom: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .agents-hero p {
            font-size: 22px;
            margin-bottom: 40px;
            opacity: 0.95;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        .agents-section {
            padding: 100px 0;
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }
        
        .agents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 35px;
            margin-top: 50px;
        }
        
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 50px;
        }
        
        .section-header h2 {
            font-size: 36px;
            margin-bottom: 15px;
            color: #1A1A1A;
            font-weight: 800;
        }
        
        .section-header p {
            font-size: 18px;
            color: #6B6B6B;
            line-height: 1.6;
        }
        
        .agent-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .agent-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }
        
        .agent-image {
            height: 220px;
            background: linear-gradient(135deg, #006AFF, #0056cc);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .agent-image:before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        
        .agent-image img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }
        
        .agent-info {
            padding: 30px 25px;
            text-align: center;
        }
        
        .agent-info h3 {
            margin: 0 0 8px 0;
            color: #1A1A1A;
            font-size: 22px;
            font-weight: 700;
        }
        
        .agent-email {
            color: #006AFF;
            font-weight: 500;
            margin: 0 0 5px 0;
            font-size: 15px;
        }
        
        .agent-role {
            color: #6B6B6B;
            font-size: 14px;
            margin: 0 0 20px 0;
        }
        
        .agent-stats {
            display: flex;
            justify-content: space-around;
            margin: 25px 0;
            padding: 0 10px;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            display: block;
            font-size: 20px;
            font-weight: 700;
            color: #1A1A1A;
        }
        
        .stat-label {
            font-size: 13px;
            color: #6B6B6B;
        }
        
        .contact-agent {
            background: linear-gradient(135deg, #006AFF, #0056cc);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(0, 106, 255, 0.3);
        }
        
        .contact-agent:hover {
            background: linear-gradient(135deg, #0056cc, #0044aa);
            box-shadow: 0 6px 20px rgba(0, 106, 255, 0.4);
            transform: translateY(-2px);
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }
        
        .modal-content {
            background: linear-gradient(to bottom right, #ffffff, #f8f9fa);
            margin: 5% auto;
            padding: 40px;
            border-radius: 16px;
            width: 90%;
            max-width: 650px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease-out;
        }
        
        .close {
            color: #777;
            float: right;
            font-size: 32px;
            font-weight: 300;
            cursor: pointer;
            position: absolute;
            right: 25px;
            top: 20px;
            transition: all 0.2s ease;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .close:hover,
        .close:focus {
            color: #000;
            background-color: #f0f0f0;
            text-decoration: none;
            transform: rotate(90deg);
        }
        
        .modal-content h2 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #1A1A1A;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
        }
        
        .modal-subtitle {
            text-align: center;
            color: #6B6B6B;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }
        
        .form-group.required label:after {
            content: " *";
            color: #e74c3c;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #fff;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #006AFF;
            box-shadow: 0 0 0 4px rgba(0, 106, 255, 0.15);
            background-color: #fff;
        }
        
        .form-group input::placeholder {
            color: #aaa;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 22px;
        }
        
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }
        
        .btn-primary, .btn-secondary {
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #006AFF, #0056cc);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 106, 255, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0056cc, #0044aa);
            box-shadow: 0 6px 20px rgba(0, 106, 255, 0.4);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #f1f3f5;
            color: #495057;
            border: 1px solid #e1e5eb;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        /* CTA Section Styles */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #006AFF 0%, #0056cc 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        }
        
        .cta-content h2 {
            font-size: 40px;
            margin-bottom: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .cta-content p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
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
        
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                padding: 25px;
                margin: 10% auto;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .form-actions button {
                width: 100%;
            }
            
            .close {
                right: 15px;
                top: 15px;
                font-size: 28px;
            }
        }
        
        @media (max-width: 480px) {
            .modal-content {
                padding: 20px 15px;
            }
            
            .modal-content h2 {
                font-size: 24px;
            }
            
            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 12px 14px;
                font-size: 15px;
            }
            
            .btn-primary, .btn-secondary {
                padding: 12px 20px;
                font-size: 15px;
            }
        }
    </style>
    
    <style>
        /* Fix navigation spacing */
        .nav-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .user-welcome {
            margin-right: 10px;
        }

        @media (max-width: 968px) {
            .agents-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .agents-hero {
                padding: 60px 0;
            }

            .agents-hero h1 {
                font-size: 36px;
            }

            .agents-hero p {
                font-size: 18px;
            }

            .agents-grid {
                grid-template-columns: 1fr;
            }

            .nav-links, .nav-actions {
                display: none;
            }
        }
    </style>
</body>
</html>