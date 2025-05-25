<?php
// Start the session
session_start();

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

// Initialize variables
$username = $email = $password = $confirm_password = "";
$errors = [];

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $errors["username"] = "Username is required";
    } else {
        $username = sanitize($_POST["username"]);
        // Check if username is already taken
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors["username"] = "Username already exists";
        }
        $stmt->close();
    }
    
    // Validate email
    if (empty($_POST["email"])) {
        $errors["email"] = "Email is required";
    } else {
        $email = sanitize($_POST["email"]);
        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email format";
        } else {
            // Check if email is already registered
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors["email"] = "Email is already registered";
            }
            $stmt->close();
        }
    }
    
    // Validate password
    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
    } else {
        $password = sanitize($_POST["password"]);
        // Password must be at least 6 characters
        if (strlen($password) < 6) {
            $errors["password"] = "Password must be at least 6 characters";
        }
    }
    
    // Validate confirm password
    if (empty($_POST["confirm_password"])) {
        $errors["confirm_password"] = "Please confirm your password";
    } else {
        $confirm_password = sanitize($_POST["confirm_password"]);
        // Check if passwords match
        if ($password != $confirm_password) {
            $errors["confirm_password"] = "Passwords do not match";
        }
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        // Execute the query
        if ($stmt->execute()) {
            // Get the new user ID
            $user_id = $stmt->insert_id;
            
            // Set session variables
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $errors["general"] = "Registration failed. Please try again.";
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Tindahan ni Aling Nena</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            display: grid;
            max-width: 700px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2);
            overflow: hidden;
            min-height: 750px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            opacity: 0;
            transform: translateY(30px);
            animation: pageLoad 0.8s ease-out forwards;
        }

        @keyframes pageLoad {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-60px) rotate(360deg); }
        }

        .logo {
            background: rgba(255, 255, 255, 0.15);
            padding: 32px;
            border-radius: 20px;
            margin-bottom: 40px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: logoFloat 6s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            font-weight: bold;
            box-shadow: 0 8px 24px rgba(79, 172, 254, 0.3);
            font-family: "Poppins", sans-serif;
        }

        .brand-text {
            color: rgba(255, 255, 255, 0.95);
            margin-top: 30px;
            margin-bottom: 20px;
            z-index: 1;
        }

        .brand-text h1 {
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            font-family: "Poppins", sans-serif;
        }

        .brand-text p {
            font-size: 1.1rem;
            margin-top: 15px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .features {
            width: 100%;
            max-width: 320px;
            z-index: 1;
            margin-top: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            opacity: 0;
            transform: translateX(-20px);
            animation: slideIn 0.6s ease-out forwards;
        }

        .feature-item:nth-child(1) { animation-delay: 0.2s; }
        .feature-item:nth-child(2) { animation-delay: 0.4s; }
        .feature-item:nth-child(3) { animation-delay: 0.6s; }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .right-section {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
        }

        .register-header {
            margin-bottom: 40px;
        }

        .register-header h2 {
            font-size: 2.4rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
            font-family: "Poppins", sans-serif;
        }

        .register-header p {
            color: #6B7280;
            font-size: 1.1rem;
            font-weight: 400;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #374151;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 18px 24px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
            color: #374151;
            backdrop-filter: blur(10px);
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .form-group input:hover {
            border-color: #9CA3AF;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .input-error {
            border-color: #DC2626 !important;
            background: linear-gradient(135deg, #FEF2F2 0%, #FDE8E8 100%) !important;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-text {
            color: #DC2626;
            font-size: 0.875rem;
            margin-top: 8px;
            padding: 8px 12px;
            background: linear-gradient(135deg, #FEF2F2 0%, #FDE8E8 100%);
            border: 1px solid #FECACA;
            border-radius: 8px;
            font-weight: 500;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9CA3AF;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .password-toggle:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .password-strength {
            margin-top: 8px;
            display: none;
        }

        .strength-bar {
            height: 4px;
            background: #E5E7EB;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .strength-fill {
            height: 100%;
            background: linear-gradient(90deg, #DC2626, #F59E0B, #10B981);
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .strength-text {
            font-size: 0.875rem;
            color: #6B7280;
        }

        .btn {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0.5px;
            font-family: "Inter", sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
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

        .divider {
            text-align: center;
            margin: 32px 0;
            position: relative;
            color: #9CA3AF;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #E5E7EB, transparent);
        }

        .divider span {
            background: rgba(255, 255, 255, 0.9);
            padding: 0 24px;
            backdrop-filter: blur(10px);
        }

        .login-link {
            text-align: center;
            color: #6B7280;
            font-size: 0.95rem;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            position: relative;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .general-error {
            background: linear-gradient(135deg, #FEF2F2 0%, #FDE8E8 100%);
            color: #DC2626;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            border: 1px solid #FECACA;
            font-weight: 500;
            display: flex;
            align-items: center;
            animation: slideDown 0.3s ease-out;
        }

        .success-message {
            background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);
            color: #059669;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            border: 1px solid #A7F3D0;
            font-weight: 500;
            display: flex;
            align-items: center;
            animation: slideDown 0.3s ease-out;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                margin: 10px;
                border-radius: 20px;
                min-height: auto;
            }
            
            .right-section {
                padding: 40px 30px;
            }

            .brand-text h1 {
                font-size: 1.4rem;
            }

            .register-header h2 {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .features {
                max-width: 280px;
            }

            .feature-item {
                font-size: 0.9rem;
            }
        }

        /* Enhanced input animations */
        .form-group input:focus + .form-label {
            transform: translateY(-8px) scale(0.9);
            color: #667eea;
        }

        /* Ripple effect for buttons */
        .btn {
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:active::after {
            width: 300px;
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="right-section">
            <div class="register-header">
                <h2>Create Account</h2>
                <p>Fill in your details to get started with your tindahan management.</p>
            </div>

            <?php if (isset($errors["general"])): ?>
                <div class="general-error">
                    <span>⚠️</span>
                    <span style="margin-left: 8px;"><?php echo $errors["general"]; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="form-control <?php echo isset($errors['username']) ? 'input-error' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($username); ?>"
                                   placeholder="Enter your username"
                                   required>
                        </div>
                        <?php if (isset($errors["username"])): ?>
                            <div class="error-text">
                                <span>⚠️</span>
                                <span style="margin-left: 8px;"><?php echo $errors["username"]; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control <?php echo isset($errors['email']) ? 'input-error' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($email); ?>"
                                   placeholder="Enter your email"
                                   required>
                        </div>
                        <?php if (isset($errors["email"])): ?>
                            <div class="error-text">
                                <span>⚠️</span>
                                <span style="margin-left: 8px;"><?php echo $errors["email"]; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control <?php echo isset($errors['password']) ? 'input-error' : ''; ?>"
                               placeholder="Create a strong password"
                               required>
                        <span class="password-toggle" onclick="togglePassword('password')">
                            <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Password strength</div>
                    </div>
                    <?php if (isset($errors["password"])): ?>
                        <div class="error-text">
                            <span>⚠️</span>
                            <span style="margin-left: 8px;"><?php echo $errors["password"]; ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="form-control <?php echo isset($errors['confirm_password']) ? 'input-error' : ''; ?>"
                               placeholder="Confirm your password"
                               required>
                        <span class="password-toggle" onclick="togglePassword('confirm_password')">
                            <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </div>
                    <?php if (isset($errors["confirm_password"])): ?>
                        <div class="error-text">
                            <span>⚠️</span>
                            <span style="margin-left: 8px;"><?php echo $errors["confirm_password"]; ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary" id="registerBtn">
                    <span id="registerText">Create Account</span>
                    <div class="loading" id="registerLoading"></div>
                </button>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="login-link">
                    Already have an account? <a href="index2.php">Sign in here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let passwordVisible = false;
        let confirmPasswordVisible = false;

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = fieldId === 'password'