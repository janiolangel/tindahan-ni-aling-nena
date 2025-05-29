<?php
// -- includes 
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- edit stock form handling
$name = $quantity = $unit = $price = $source = "";
$errors = [];

do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_stock']))) {
        $stock = $conn->query(
            "SELECT * 
            FROM stock
            WHERE id = ".$_GET['stock_id'].""
        )->fetch_assoc();
        $name = $stock['name'];
        $quantity = $stock['quantity'];
        $unit = $stock['unit'];
        $price = $stock['price'];
        $source = $stock['source'];
        break;
    }

    // --- name user input handling
    if (empty($_POST["name"])) {
        $errors["name"] = "Name is required";
    } else {
        $name = sanitize($_POST['name']);
    } 

    if (empty($_POST["quantity"])) {
        $errors["quantity"] = "Quantity is required";
    } else {
        $quantity = sanitize($_POST['quantity']);
    } 
    
    if (empty($_POST["unit"])) {
        $errors["unit"] = "Unit is required";
    } else {
        $unit = sanitize($_POST['unit']);
    } 

    if (empty($_POST["price"])) {
        $errors["price"] = "Price is required";
    } else {
        $price = sanitize($_POST['price']);
    } 
    
    if (empty($_POST["source"])) {
        $errors["source"] = "Source is required";
    } else {
        $source = sanitize($_POST['source']);
    } 

    if (!empty($errors)) {
        break;
    }

    // --- user data handling
    $name_exists = $conn->query(
        "SELECT *
        FROM user_stock us
        JOIN stock s ON us.stock_id = s.id
        WHERE us.email = '".$_SESSION['email']."' AND s.name = '$name'");
    if ($name_exists->num_rows > 0) {
        $errors["name"] = "Name is already in stock";
    }   


    if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
        $errors["quantity"] = "Input value is not valid";
    } else {
       $quantity = intval($quantity); 
    }

    if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
        $errors["price"] = "Input value is not valid";
    } else {
        $price = floatval($price); 
    }

    if (!empty($errors)) {
        break;
    }

    // --- perform edit operation
     $conn->query(
        "UPDATE stock
        SET name = '$name',
            price = '$price',
            quantity = '$quantity',
            unit = '$unit',
            source = '$source'
        WHERE id = ".$_GET['stock_id'].";"    
    );
    header("Location: ../dashboard.php?view=stock");
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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?stock_id=".$_GET['stock_id'];?>">
        <div >
            <label>Stock Name</label>
            <input type="text" name="name"  value="<?php echo htmlspecialchars($name);?>">
            <?php if (isset($errors["name"])): ?>
                <div ><?php echo $errors["name"]; ?></div>
            <?php endif; ?>
        </div>
        <div >
            <label>Quantity</label>
            <input type="text" name="quantity"  value="<?php echo htmlspecialchars($quantity);?>">
            <?php if (isset($errors["quantity"])): ?>
                <div ><?php echo $errors["quantity"]; ?></div>
            <?php endif; ?>
        </div>
        <div >
            <label>Unit</label>
            <input type="text" name="unit"  value="<?php echo htmlspecialchars($unit);?>">
            <?php if (isset($errors["unit"])): ?>
                <div ><?php echo $errors["unit"]; ?></div>
            <?php endif; ?>
        </div>
        <div >
            <label>Price</label>
            <input type="" name="price"  value="<?php echo htmlspecialchars($price);?>">
            <?php if (isset($errors["price"])): ?>
                <div ><?php echo $errors["price"]; ?></div>
            <?php endif; ?>
        </div>
        <div >
            <label>Source</label>
            <input type="text" name="source"  value="<?php echo htmlspecialchars($source);?>">
            <?php if (isset($errors["source"])): ?>
                <div ><?php echo $errors["source"]; ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" name="edit_stock" >Edit Stock</button>
    </form>
    <a href="../dashboard.php?view=stock">
        <button >Back</button>
    </a>
</body>
</html>
