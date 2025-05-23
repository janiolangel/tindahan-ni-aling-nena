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
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            background-color: white;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-title {
            color: #8B4513;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo {
            width: 60px;
            margin-right: 10px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        
        .error-text {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .btn {
            background-color: #8B4513;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
        }
        
        .btn:hover {
            background-color: #6e3209;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #8B4513;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .general-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <div class="logo-title">
                <img src="tindahan-logo.png" alt="Logo" class="logo">
                <span>Tindahan ni Aling Nena</span>
            </div>
        </div>
        
        <h2>Create New Account</h2>
        
        <?php if (isset($errors["general"])): ?>
            <div class="general-error"><?php echo $errors["general"]; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo $username; ?>">
                <?php if (isset($errors["username"])): ?>
                    <div class="error-text"><?php echo $errors["username"]; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo $email; ?>">
                <?php if (isset($errors["email"])): ?>
                    <div class="error-text"><?php echo $errors["email"]; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control">
                <?php if (isset($errors["password"])): ?>
                    <div class="error-text"><?php echo $errors["password"]; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                <?php if (isset($errors["confirm_password"])): ?>
                    <div class="error-text"><?php echo $errors["confirm_password"]; ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Register</button>
            
            <div class="login-link">
                Already have an account? <a href="index.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>