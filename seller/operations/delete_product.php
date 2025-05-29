<?php
// -- includes
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- delete store form handling
$name = $address = "";
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product']))) {
        $product = $conn->query(
            "SELECT * 
            FROM product
            join stock on product.stock_id = stock.id
            WHERE stock_id = ".$_GET['stock_id'].""
        )->fetch_assoc();
        $name = $product['name'];
        break;
    }   
     
    // --- perform delete operation
    $conn->query(
        "DELETE FROM product 
        WHERE stock_id = ".$_GET['stock_id'].";"
    );
    header("Location: ../store.php?store_id=".$_SESSION['store_id']."&view=products");
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
        <div>
            <label>Delete <?php echo htmlspecialchars($name)?>?</label>
            <button type="submit" name="delete_product">Delete</button>
        </div>
    </form>
    <a href="../store.php?store_id=<?php echo $_SESSION['store_id']?>&view=products">
        <button>Back</button>
    </a>
</body>
</html>