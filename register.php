<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered';
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashedPassword, $role]);
                
                $success = 'Account created successfully! You can now log in.';
            }
        } catch(PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .auth-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .auth-header h2 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #1A1A1A;
        }
        
        .auth-header p {
            color: #6B6B6B;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1A1A1A;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6B6B6B;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .input-with-icon input:focus {
            outline: none;
            border-color: #006AFF;
        }
        
        .form-select {
            width: 100%;
            padding: 15px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 16px;
            background: white;
            cursor: pointer;
        }
        
        .form-select:focus {
            outline: none;
            border-color: #006AFF;
        }
        
        .auth-btn {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 25px;
            color: #6B6B6B;
        }
        
        .auth-footer a {
            color: #006AFF;
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert.error {
            background: #FFEAEA;
            color: #FF4757;
            border: 1px solid #FFD1D1;
        }
        
        .alert.success {
            background: #E8F5E9;
            color: #4CAF50;
            border: 1px solid #C8E6C9;
        }
        
        /* Role selection styles */
        .role-selection {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .role-card {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .role-card:hover {
            border-color: #006AFF;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 106, 255, 0.1);
        }
        
        .role-card.selected {
            border-color: #006AFF;
            background: rgba(0, 106, 255, 0.05);
        }
        
        .role-icon {
            width: 50px;
            height: 50px;
            background: #f0f8ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #006AFF;
            font-size: 20px;
        }
        
        .role-content {
            flex: 1;
        }
        
        .role-content h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #1A1A1A;
        }
        
        .role-content p {
            margin: 0;
            font-size: 14px;
            color: #6B6B6B;
        }
        
        .role-check {
            width: 24px;
            height: 24px;
            border: 2px solid #E0E0E0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            color: white;
            font-size: 12px;
        }
        
        .role-card.selected .role-check {
            background: #006AFF;
            border-color: #006AFF;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h2>Create Account</h2>
                <p>Join our community today</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" required placeholder="Choose a username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" required placeholder="Enter your email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required placeholder="Create a password">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="role">I want to:</label>
                    <div class="role-selection">
                        <input type="hidden" id="role" name="role" value="" required>
                        
                        <div class="role-card" onclick="selectRole('buyer')">
                            <div class="role-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="role-content">
                                <h3>Buy Properties</h3>
                                <p>Find your dream home or investment property</p>
                            </div>
                            <div class="role-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <div class="role-card" onclick="selectRole('seller')">
                            <div class="role-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="role-content">
                                <h3>Sell Properties</h3>
                                <p>List your property and reach potential buyers</p>
                            </div>
                            <div class="role-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <div class="role-card" onclick="selectRole('agent')">
                            <div class="role-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="role-content">
                                <h3>Work as an Agent</h3>
                                <p>Help clients buy and sell properties</p>
                            </div>
                            <div class="role-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary auth-btn">Create Account</button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
    
    <script>
        function selectRole(role) {
            // Remove selected class from all role cards
            var cards = document.querySelectorAll('.role-card');
            for (var i = 0; i < cards.length; i++) {
                cards[i].classList.remove('selected');
            }
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Set the hidden input value
            document.getElementById('role').value = role;
        }
    </script>
</body>
</html>