<?php
// -- includes
include 'includes/db_connector.php';
include 'includes/functions.php';

// -- starts and gets session data
session_start();

// -- login form handling
$email = $password = "";
$error_message = NULL;
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']))) {
        break;
    }
    
    // -- handle input handling 
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);   
    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
        break;
    }   

    // -- user data handling
    $get_user = $conn->query(
        "SELECT * 
        FROM user
        WHERE email = '$email' and password = '$password'"
    );
    if ($get_user->num_rows == 0) {
        $error_message = "Invalid email or password.";
        break;
    }

    // --- perform login operation
    $user = $get_user->fetch_assoc();
    $_SESSION['email'] = $user['email'];
    $_SESSION['username'] = $user['username'];       
    header("Location: seller/dashboard.php");
} while (0);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
        }
        body * {
            border: solid 1px red;
        }
        form {
            width: 600px;
        }


        input {
            display: block;
        }
        a {
            text-decoration: none;
            color: black;
        }
        .active {
            color: red !important;
        }
    </style>
</head>
<body>
    <header>
        Tindahan ni Aling Nena
    </header>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
        <?php if (isset($error_message)): ?>
            <div><?php echo $error_message;?></div>
        <?php endif; ?>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $email; ?>">
        </div>
        
        <div>
            <label>Password</label>
            <input type="password" name="password">
        </div>
        
        <button type="submit" name="login">Log In</button>
        <div></div>
        
        <a href="register.php">
            <button type="button">New Account</button>
        </a>
    </form>
</body>
</html>



