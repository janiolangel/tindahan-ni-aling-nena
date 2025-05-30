<?php
// -- includes 
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- add store form handling
$name = $address = "";
$errors = [];
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_store']))) {
        break;
    }

    // --- name user input handling
    if (empty($_POST["name"])) {
        $errors["name"] = "Name is required";
        break;
    } else {
        $name = sanitize($_POST['name']);
    } 
    $address = sanitize($_POST['address']);

    // --- user data handling
    $name_exists = $conn->query(
        "SELECT *
        FROM user_tindahan ut
        JOIN tindahan t ON ut.tindahan_id = t.id
        WHERE ut.email = '".$_SESSION['email']."' AND t.name = '$name'");
    var_dump($name_exists);
    if ($name_exists->num_rows > 0) {
        $errors["name"] = "Name is already taken";
    }   
    if (strlen($name) < 5) {
        $errors["name"] = "Name must be at least 5 characters";
    }
    if (!empty($errors)) {
        break;
    }

    // --- perform add operation
    $conn->query(
        "INSERT INTO tindahan (id, name, address, expense, revenue) 
        VALUES (NULL, '$name', '$address', 0, 0)"
    );
    $last_id = $conn->insert_id;
    $email = $_SESSION["email"]; 
    $conn->query(
        "INSERT INTO user_tindahan(email, tindahan_id)
        VALUES ('$email', $last_id)"
    );
    $_SESSION["tindahan_id"] = $last_id; 
    header("Location: ../dashboard.php?view=store");
} while (0);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        input {
            display: block;
        }
    </style>
</head>
<body>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
        <div>
            <label>Store Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name);?>">
            <?php if (isset($errors["name"])): ?>
                <div><?php echo $errors["name"]; ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label>Address</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($address);?>">
        </div>
    
        <button type="submit" name="add_store">Add Store</button>
    </form>
    <a href="../dashboard.php?view=store">
        <button>Back</button>
    </a>
</body>
</html>
