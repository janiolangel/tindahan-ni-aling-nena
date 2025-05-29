<?php
// -- includes 
include 'includes/db_connector.php';
include 'includes/functions.php';

// -- starts and gets session data
session_start();

// -- register form handling
$username = $email = $password = $confirm_password = "";
$errors = [];

do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register']))) {
        break;
    }

    // --- user input handling
    if (empty($_POST["username"])) {
        $errors["username"] = "Username is required";
    } else {
        $username = sanitize($_POST["username"]);
    }
    
    if (empty($_POST["email"])) {
        $errors["email"] = "Email is required";
    } else {
        $email = sanitize($_POST["email"]);
    }

    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
    } else {
        $password = sanitize($_POST["password"]);
    }

    if (empty($_POST["confirm_password"])) {
        $errors["confirm_password"] = "Please confirm your password";
    } else {
        $confirm_password = sanitize($_POST["confirm_password"]);
    }

    if (!empty($errors)) {
        break;
    }

    // --- user data handling
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format";
    }
    $email_exists = $conn->query(
        "SELECT *
        FROM user 
        WHERE email = '$email'");
    if ($email_exists->num_rows > 0) {
        $errors["email"] = "Email is already registered";
    }

    if (strlen($password) < 6) {
        $errors["password"] = "Password must be at least 6 characters";
    }

    if ($password != $confirm_password) {
        $errors["confirm_password"] = "Passwords do not match";
    }
    if (!empty($errors)) {
        break;
    }

    // --- perform add operation
    $conn->query(
        "INSERT INTO user (username, email, password) 
        VALUES ('$username', '$email', '$password')"
    );
    $_SESSION["email"] = $email; 
    $_SESSION["username"] = $username; 
    header("Location: seller/dashboard.php");
} while (0);
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            overflow: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            display: grid;
            max-width: 600px;
            width: 100%;
            height: 95vh;
            max-height: 560px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2);
            overflow: hidden;
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

        .right-section {
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            height: 100%;
            overflow: hidden;
        }

        .register-header {
            margin-bottom: 25px;
            text-align: center;
        }

        .register-header h2 {
            font-size: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 3px;
            font-weight: 700;
            letter-spacing: -0.5px;
            font-family: "Poppins", sans-serif;
        }

        .register-header p {
            color: #6B7280;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
            color: #374151;
            backdrop-filter: blur(10px);
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
            font-size: 0.8rem;
            margin-top: 4px;
            padding: 6px 10px;
            background: linear-gradient(135deg, #FEF2F2 0%, #FDE8E8 100%);
            border: 1px solid #FECACA;
            border-radius: 6px;
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
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9CA3AF;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .password-toggle:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 12px;
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
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.25);
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
            box-shadow: 0 10px 28px rgba(102, 126, 234, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
            color: #9CA3AF;
            font-size: 0.85rem;
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
            padding: 0 20px;
            backdrop-filter: blur(10px);
        }

        .login-link {
            text-align: center;
            color: #6B7280;
            font-size: 0.9rem;
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

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .container {
                max-width: 100%;
                height: 100vh;
                max-height: none;
                border-radius: 15px;
            }
            
            .right-section {
                padding: 25px 25px;
            }

            .register-header h2 {
                font-size: 1.75rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-group {
                margin-bottom: 14px;
            }

            .register-header {
                margin-bottom: 20px;
            }
        }

        @media (max-height: 700px) {
            .register-header {
                margin-bottom: 15px;
            }
            
            .register-header h2 {
                font-size: 1.6rem;
            }
            
            .register-header p {
                font-size: 0.85rem;
            }
            
            .form-group {
                margin-bottom: 12px;
            }
            
            .form-group input {
                padding: 12px 16px;
            }
            
            .btn {
                padding: 12px;
            }
            
            .divider {
                margin: 15px 0;
            }
        }

        /* Ripple effect for buttons */
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
            <div class="form-container">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
                                    <span style="margin-left: 6px;"><?php echo $errors["username"]; ?></span>
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
                                    <span style="margin-left: 6px;"><?php echo $errors["email"]; ?></span>
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
                        <?php if (isset($errors["password"])): ?>
                            <div class="error-text">
                                <span>⚠️</span>
                                <span style="margin-left: 6px;"><?php echo $errors["password"]; ?></span>
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
                                <span style="margin-left: 6px;"><?php echo $errors["confirm_password"]; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary" name="register">
                        Create Account
                    </button>

                    <div class="divider">
                        <span>or</span>
                    </div>

                    <div class="login-link">
                        Already have an account? <a href="index.php">Sign in here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = fieldId === 'password' ? document.getElementById('eyeIcon1') : document.getElementById('eyeIcon2');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414l-7.071 7.071m2.829-2.829l4.243 4.243m0 0L12 12m-4.242 4.243L12 12m-4.242 4.243L4.929 19.07" />
                `;
            } else {
                field.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>
</html>