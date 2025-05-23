<?php
// Start the session to track user login state
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Database connection configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "tindahan_system";

// Connect to database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    
    // Validate email and password
    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        // Query to check user credentials
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Check if password matches (supports both hashed and plain text for compatibility)
            $password_match = false;
            
            // First try to verify as hashed password
            if (password_verify($password, $user['password'])) {
                $password_match = true;
            } 
            // If that fails, check as plain text (for existing plain text passwords)
            elseif ($password === $user['password']) {
                $password_match = true;
                
                // Optionally upgrade to hashed password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_pwd_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_pwd_stmt->bind_param("si", $hashed_password, $user['id']);
                $update_pwd_stmt->execute();
                $update_pwd_stmt->close();
            }
            
            if ($password_match) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Update last login timestamp
                $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "No account found with this email address.";
        }
        $stmt->close();
    }
}

// Get recent logins (limited to 3)
$recent_logins = [];
$login_query = "SELECT id, username, email FROM users WHERE last_login IS NOT NULL ORDER BY last_login DESC LIMIT 3";
$login_result = $conn->query($login_query);

if ($login_result && $login_result->num_rows > 0) {
    while ($row = $login_result->fetch_assoc()) {
        $recent_logins[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tindahan ni Aling Nena - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family:"Quicksand", sans-serif !important;
            background: linear-gradient(135deg,#455A64 0%, #2D3561 50%, #1A1F3A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1200px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 24px 48px rgba(28, 35, 64, 0.15);
            overflow: hidden;
            min-height: 600px;
        }

        .left-section {
            background: linear-gradient(135deg, #1C2340 0%, #2D3561 50%, #1A1F3A 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.08)"/></svg>') repeat;
            animation: float 30s linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-100px) rotate(360deg); }
        }

        .logo {
            background: rgba(255, 255, 255, 0.1);
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            z-index: 1;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4A90E2 0%, #5BA0F2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 16px rgba(74, 144, 226, 0.25);
        }

        .brand-text {
            color:rgb(146, 144, 167);
            margin-top: 25px;
            margin-bottom: 13px;
        }

        .recent-logins {
            width: 100%;
            max-width: 300px;
            z-index: 1;
        }

        .recent-logins h3 {
            color: rgba(255, 255, 255, 0.9);
            font-size: 4rem;
            margin-bottom: 20px;
            text-align: left;
            font-weight: 500;
        }

        .login-avatars {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            justify-content: center;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: rgba(74, 144, 226, 0.15);
            border: 1px solid rgba(74, 144, 226, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4A90E2;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            overflow: hidden;
            position: relative;
        }

        .avatar:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(74, 144, 226, 0.2);
            border-color: #4A90E2;
            background: rgba(74, 144, 226, 0.2);
        }

        .right-section {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FAFBFC;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 2rem;
            color: #1C2340;
            margin-bottom: 8px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #6B7280;
            font-size: 1rem;
            font-weight: 400;
        }

        .right-section .login-header .tindahan-font {
            font-family: 'Rajdhani', sans-serif;
            font-size: 30px;
            color: #1C2340;
            letter-spacing: 1.3px; 
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: white;
            color: #1C2340;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1C2340;
            box-shadow: 0 0 0 3px rgba(28, 35, 64, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6B7280;
            font-size: 1.1rem;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #1C2340;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 32px;
        }

        .forgot-password a {
            color: #1C2340;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-password a:hover {
            color: #2D3561;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0.25px;
        }

        .btn-primary {
            background: #1C2340;
            color: white;
            box-shadow: 0 4px 12px rgba(28, 35, 64, 0.15);
        }

        .btn-primary:hover {
            background: #2D3561;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(28, 35, 64, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: transparent;
            color: #1C2340;
            border: 1px solid #1C2340;
        }

        .btn-secondary:hover {
            background: #1C2340;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(28, 35, 64, 0.15);
        }

        .divider {
            text-align: center;
            margin: 32px 0;
            position: relative;
            color: #9CA3AF;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #E5E7EB;
        }

        .divider span {
            background: #FAFBFC;
            padding: 0 20px;
        }

        .loading {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                margin: 10px;
            }
            
            .left-section {
                padding: 40px 30px;
                min-height: 300px;
            }
            
            .right-section {
                padding: 40px 30px;
            }

            }
            .brand-text h1 {
                font-size: 1rem;
            }
            
            .recent-logins h3 {
                font-size: 15px; 
                font-weight: bold;
                font-display: center;
            }

            .avatar {
                width: 125px;
                height: 150px;
                font-size: 15px;
                margin: 0 8px; 
            }

        .error-message {
            color: #DC2626;
            font-size: 0.875rem;
            margin: 12px 0;
            padding: 12px;
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            font-weight: 500;
        }

        .success-message {
            color: #059669;
            font-size: 0.875rem;
            margin-top: 8px;
            display: none;
        }

        .input-error {
            border-color: #DC2626 !important;
            background-color: #FEF2F2 !important;
        }

        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: #1C2340;
        }

        .checkbox-wrapper label {
            margin: 0;
            font-size: 0.9rem;
            cursor: pointer;
            color: #374151;
            font-weight: 400;

        .left-section h1 {
            color: white;
        }


</style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo-icon">
                <img src="logo.png" alt="Logo">
            </div>
            <br>
            <br>
            <br>
            <br>
            <div class="brand-text">
                <h1>RECENT LOGINS</h1>
            </div>
            <div class="recent-logins">
                <div class="login-avatars">
                    <?php if (count($recent_logins) > 0): ?>
                        <?php foreach ($recent_logins as $index => $login): ?>
                        <div class="avatar" title="<?php echo htmlspecialchars($login['username']); ?>" onclick="fillUserEmail('<?php echo htmlspecialchars($login['email']); ?>')">
                            <img src="bluePerson.png" alt="Avatar" class="avatar-img">    
                            <?php echo strtoupper(substr($login['username'], 0, 3)); ?>
                        </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="avatar">AD</div>
                        <div class="avatar">MG</div>
                        <div class="avatar">ST</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="right-section">
            <div class="login-header">
                <h2 class="tindahan-font">Hello!</h2>
                <p>Sign in to access your dashboard.</p>
            </div>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="loginForm">
                <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <span class="password-toggle" onclick="togglePassword()">👁️</span>
                    </div>
                </div>

                <div class="remember-me">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="#" onclick="showForgotPassword()">Forgot Password?</a>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-primary" id="loginBtn">
                    <span id="loginText">Sign In</span>
                    <div class="loading" id="loginLoading"></div>
                </button>

                <div class="divider">
                    <span>or</span>
                </div>

                <a href="register.php">
                    <button type="button" class="btn btn-secondary">Create New Account</button>
                </a>
            </form>
        </div>
    </div>

    <script>
        let isPasswordVisible = false;

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            
            if (isPasswordVisible) {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            } else {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            }
            isPasswordVisible = !isPasswordVisible;
        }

        function fillUserEmail(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').focus();
        }

        function showForgotPassword() {
            alert('Redirect to forgot password page');
        }

        function changeLanguage(lang) {
            // Update active state
            document.querySelectorAll('.language-selector a').forEach(a => a.classList.remove('active'));
            event.target.classList.add('active');
            console.log('Language changed to:', lang);
        }

        // Add loading animation on form submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loginLoading = document.getElementById('loginLoading');
            
            loginBtn.disabled = true;
            loginText.style.display = 'none';
            loginLoading.style.display = 'block';
        });

        // Add click effect to avatars
        document.querySelectorAll('.avatar').forEach(avatar => {
            avatar.addEventListener('click', function() {
                // Visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

        // Add subtle animations on page load
        window.addEventListener('load', function() {
            document.querySelector('.container').style.opacity = '0';
            document.querySelector('.container').style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                document.querySelector('.container').style.transition = 'all 0.5s ease';
                document.querySelector('.container').style.opacity = '1';
                document.querySelector('.container').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>