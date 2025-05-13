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
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // For simplicity, we're not hashing passwords in this example
            // In a real application, you should use password_hash() and password_verify()
            if ($password == $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
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
            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    }
}

// Get recent logins (limited to 3)
$recent_logins = [];
$login_query = "SELECT id, username FROM users ORDER BY last_login DESC LIMIT 3";
// $login_result = $conn->query($login_query);

/* if ($login_result && $login_result->num_rows > 0) {
    while ($row = $login_result->fetch_assoc()) {
        $recent_logins[] = $row;
    }
}*/

// Handle language selection
$languages = ['English (US)', 'Filipino', 'Hiligaynon'];
$selected_lang = isset($_GET['lang']) ? $_GET['lang'] : 'English (US)';

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tindahan ni Aling Nena</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body { 
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        
        .main-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            background-color: transparent;
        }
        
        .left-panel {
            flex: 1;
            padding: 30px;
        }
        
        .right-panel {
            flex: 1;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }
        
        .logo-image {
            width: 60px;
            height: 60px;
            background-color: #8B4513;
            border-radius: 5px;
            margin-right: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .logo-image img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .logo-text {
            color: #8B4513;
            font-size: 24px;
            font-weight: bold;
            line-height: 1.2;
        }
        
        .recent-logins h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
        }
        
        .login-profiles {
            display: flex;
            gap: 20px;
        }
        
        .profile-item {
            text-align: center;
            cursor: pointer;
        }
        
        .profile-box {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
            position: relative;
        }
        
        .profile-box::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 48%, #ddd 49%, #ddd 51%, transparent 52%);
        }
        
        .profile-name {
            color: #333;
            font-size: 14px;
        }
        
        .login-form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #8B4513;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #6e3209;
        }
        
        .form-divider {
            height: 1px;
            background-color: #ddd;
            margin: 20px 0;
        }
        
        .new-account-btn {
            width: 100%;
            padding: 12px;
            background-color: #fff;
            color: #8B4513;
            border: 1px solid #8B4513;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .new-account-btn:hover {
            background-color: #f9f9f9;
        }
        
        .language-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 15px 0;
            text-align: center;
            border-top: 1px solid #ddd;
        }
        
        .language-bar a {
            margin: 0 15px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }
        
        .language-bar a.active {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .error-message {
            color: #e74c3c;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            
            .left-panel, .right-panel {
                width: 100%;
            }
            
            .right-panel {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="left-panel">
            <div class="logo-section">
                <div class="logo-image">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIj48cGF0aCBkPSJNMTAgMTJINnYzMGgzNlYxMmgtNE02IDEyaDM2TTEwIDZoMjhhNCA0IDAgMTEwIDhoLTI4YTQgNCAwIDExMC04eiIvPjxwYXRoIGQ9Ik0xNiAyNGg0bTgtOGg0bS04IDhoNG00LThoNG0tMTYgMTZoNG0tOCAwaDRtOCAwaDRtLTQtOGg0Ii8+PC9zdmc+" alt="Store Icon">
                </div>
                <div class="logo-text">Tindahan ni<br>Aling Nena</div>
            </div>
            
            <div class="recent-logins">
                <h2>Recent Logins</h2>
                <div class="login-profiles">
                    <?php foreach ($recent_logins as $login): ?>
                    <div class="profile-item" onclick="fillEmail('<?php echo htmlspecialchars($login['username']); ?>')">
                        <div class="profile-box"></div>
                        <div class="profile-name"><?php echo htmlspecialchars($login['username']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="right-panel">
            <div class="login-form-container">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <?php if (isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <input type="email" name="email" placeholder="Email" class="form-input" required>
                    <input type="password" name="password" placeholder="Password" class="form-input" required>
                    <button type="submit" name="login" class="btn">Log In</button>
                    
                    <div class="form-divider"></div>
                    
                    <a href="register.php">
                        <button type="button" class="new-account-btn">New Account</button>
                    </a>
                </form>
            </div>
        </div>
    </div>
    
    <div class="language-bar">
        <?php foreach ($languages as $lang): ?>
        <a href="?lang=<?php echo urlencode($lang); ?>" class="<?php echo ($selected_lang == $lang) ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($lang); ?>
        </a>
        <?php endforeach; ?>
    </div>
    
    <script>
        function fillEmail(username) {
            // This is a simple implementation
            // In a real application, you might want to use the actual email instead
            document.querySelector('input[name="email"]').value = username.toLowerCase().replace(' ', '') + "@example.com";
            document.querySelector('input[name="password"]').focus();
        }
    </script>
</body>
</html>