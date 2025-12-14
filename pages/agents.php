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

// Check if user is a buyer - if so, redirect to buy page
if ($userRole == 'buyer') {
    header('Location: buy.php');
    exit();
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
                <div class="logo" onclick="location.href='index.html'">
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
                    <a href="logout.php" class="btn-secondary">Logout</a>
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
            alert('Contacting agent with ID: ' + agentId + '. In a real implementation, this would open a contact form.');
        }
    </script>

    <style>
        .agents-hero {
            margin-top: 70px;
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .agents-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .agents-hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .agents-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .agents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .agent-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }
        
        .agent-card:hover {
            transform: translateY(-5px);
        }
        
        .agent-image {
            height: 200px;
            background: #006AFF;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .agent-image img {
            width: 128px;
            height: 128px;
            border-radius: 50%;
            border: 5px solid white;
        }
        
        .agent-info {
            padding: 25px;
            text-align: center;
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