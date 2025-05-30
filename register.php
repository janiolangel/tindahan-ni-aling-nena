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
<html>
<head>
    <style>
        input {
            display: block;
        }
        * {
            margin: 0px;
            padding: 0px;
            box-sizing: 0px;
        }
        
    </style>
</head>
<body>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label>Username</label>
            <input type="text" name="username"  value="<?php echo htmlspecialchars($username);?>">
            <?php if (isset($errors["username"])): ?>
                <div><?php echo $errors["username"]; ?></div>
            <?php endif; ?>
        </div>
        
        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <?php if (isset($errors["email"])): ?>
                <div><?php echo $errors["email"]; ?></div>
            <?php endif; ?>
        </div>
        
        <div>
            <label>Password</label>
            <input type="password" name="password">
            <?php if (isset($errors["password"])): ?>
                <div><?php echo $errors["password"]; ?></div>
            <?php endif; ?>
        </div>
        
        <div>
            <label>Confirm Password</label>
            <input type="password" name="confirm_password">
            <?php if (isset($errors["confirm_password"])): ?>
                <div><?php echo $errors["confirm_password"]; ?></div>
            <?php endif; ?>
        </div>
        
        <button type="submit" name="register">Register</button>
        
        <div>
            Already have an account? <a href="index.php">Login</a>
        </div>
    </form>
</body>
</html>
