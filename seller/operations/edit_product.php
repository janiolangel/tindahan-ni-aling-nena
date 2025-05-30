<?php
// -- includes 
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- edit form handling
$quantity = $price = "";
$product = $conn->query(
    "SELECT * 
    FROM product
    WHERE product_id = ".$_GET['product_id']."")->fetch_assoc();
$errors = [];
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product']))) {
        $price = $product['unit_price'];
        $quantity = $product['quantity_sold'];
        break;
    }

    // --- name user input handling
    if (empty($_POST["price"])) {
        $errors["price"] = "Price is required";
    } else {
        $price = sanitize($_POST['price']);
    } 
    
    if (!empty($errors)) {
        break;
    }

    // --- user data handling
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
        "UPDATE product
            SET unit_price = $price
            WHERE product_id = ".$_GET['product_id'].";"    
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
        #product-attribute-title, .stock-item {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr; 
        }
    </style>
</head>
<body>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?product_id=".$_GET['product_id'];?>">
        <div class="form-group">
            <label>Product Name: <?php echo $product['name']?></label>
        </div>

        <div class="form-group">
            <label>Price</label>
            <input type="text" name="price" class="form-control" value="<?php echo htmlspecialchars($price);?>">
            <?php if (isset($errors["price"])): ?>
                <div class="error-text"><?php echo $errors["price"]; ?></div>
            <?php endif; ?>
        </div>
        
        <button type="submit" name="edit_product" class="btn">Edit Product</button>
    </form>
    <a href="../store.php?store_id=<?php echo $_SESSION['store_id']?>&view=products">
        <button class="btn">Back</button>
    </a>
    <script>
        // For stock display
        const stockVisibilityToggler = document.querySelector('#see-stock'); 
        const stockList = document.querySelector('#product-search'); 
        stockVisibilityToggler.addEventListener('click', () => {
            if (stockList.style.display === 'none') {
                stockList.style.display = 'block';
            } else {
                stockList.style.display = 'none';
            }
        });

        // For stock filter
        const searchInput = document.querySelector('#product-input');
        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('.stock-item');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

    </script>
</body>
</html>
