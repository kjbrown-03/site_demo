<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];

// Fetch user orders
try {
    $stmt = $pdo->prepare("
        SELECT o.*, p.title as property_title, p.price as property_price, p.address as property_address 
        FROM orders o 
        JOIN properties p ON o.property_id = p.id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $orders = [];
    $error = "An error occurred while fetching orders.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
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
                    <?php if ($userRole == 'buyer'): ?>
                        <li><a href="buyer_dashboard.php">Dashboard</a></li>
                    <?php elseif ($userRole == 'seller'): ?>
                        <li><a href="seller_dashboard.php">Dashboard</a></li>
                    <?php elseif ($userRole == 'agent'): ?>
                        <li><a href="agent_dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="search_properties.php">Search</a></li>
                    <li><a href="my_orders.php" class="active">My Orders</a></li>
                </ul>
                <div class="nav-actions">
                    <span class="user-welcome">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                    <a href="logout.php" class="btn-secondary">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <section class="orders-hero">
        <div class="container">
            <h1>My Orders</h1>
            <p>Manage your property transactions</p>
        </div>
    </section>

    <section class="orders-section">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (count($orders) > 0): ?>
                <div class="orders-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Property</th>
                                <th>Price</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td>
                                        <div class="property-info-small">
                                            <h4><?php echo htmlspecialchars($order['property_title']); ?></h4>
                                            <p><?php echo htmlspecialchars($order['property_address']); ?></p>
                                        </div>
                                    </td>
                                    <td>€<?php echo number_format($order['property_price'], 0, ',', ' '); ?></td>
                                    <td>
                                        <span class="order-type <?php echo $order['order_type']; ?>">
                                            <?php echo ucfirst($order['order_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="order-status <?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <button class="btn-secondary btn-sm" onclick="viewOrder(<?php echo $order['id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-file-contract fa-3x"></i>
                    <h3>No orders yet</h3>
                    <p>You haven't placed any orders yet.</p>
                    <a href="search_properties.php" class="btn-primary">Browse Properties</a>
                </div>
            <?php endif; ?>
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
                    <h4>Buy</h4>
                    <ul>
                        <li><a href="#">Houses</a></li>
                        <li><a href="#">Apartments</a></li>
                        <li><a href="#">Villas</a></li>
                        <li><a href="#">Land</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Free Valuation</a></li>
                        <li><a href="#">Financing</a></li>
                        <li><a href="#">Insurance</a></li>
                        <li><a href="#">Moving</a></li>
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
        function viewOrder(orderId) {
            alert('Viewing details for order #' + orderId);
            // In a real implementation, this would show order details
        }
    </script>

    <style>
        .orders-hero {
            margin-top: 70px;
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .orders-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .orders-hero p {
            font-size: 20px;
            opacity: 0.9;
        }
        
        .orders-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .orders-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #006AFF;
            color: white;
        }
        
        th, td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        tbody tr:hover {
            background: #f5f9ff;
        }
        
        .property-info-small h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #1A1A1A;
        }
        
        .property-info-small p {
            margin: 0;
            color: #6B6B6B;
            font-size: 14px;
        }
        
        .order-type {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .order-type.purchase {
            background: #E8F5E9;
            color: #4CAF50;
        }
        
        .order-type.rental {
            background: #E3F2FD;
            color: #2196F3;
        }
        
        .order-type.sale {
            background: #FFF3E0;
            color: #FF9800;
        }
        
        .order-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .order-status.pending {
            background: #FFF8E1;
            color: #FFC107;
        }
        
        .order-status.confirmed {
            background: #E8F5E9;
            color: #4CAF50;
        }
        
        .order-status.completed {
            background: #E8F5E9;
            color: #4CAF50;
        }
        
        .order-status.cancelled {
            background: #FFEBEE;
            color: #F44336;
        }
        
        .btn-sm {
            padding: 8px 12px;
            font-size: 14px;
        }
        
        .no-orders {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .no-orders i {
            margin-bottom: 20px;
            color: #006AFF;
        }
        
        .no-orders h3 {
            margin-bottom: 10px;
            color: #1A1A1A;
        }
        
        .no-orders p {
            color: #6B6B6B;
            margin-bottom: 30px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .alert.error {
            background: #FFEAEA;
            color: #FF4757;
            border: 1px solid #FFD1D1;
        }
        
        @media (max-width: 768px) {
            .orders-hero h1 {
                font-size: 36px;
            }
            
            .orders-hero p {
                font-size: 18px;
            }
            
            th, td {
                padding: 15px 10px;
                font-size: 14px;
            }
            
            .property-info-small h4 {
                font-size: 14px;
            }
        }
    </style>
</body>
</html>