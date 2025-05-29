<?php
// -- includes 
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- add product form handling
$name = $quantity = $price = "";
$errors = [];
$stocks = $conn->query(
    "SELECT s.name, s.quantity, s.price, s.source
    FROM user u
    JOIN user_stock us ON u.email = us.email
    JOIN stock s ON us.stock_id = s.id
    WHERE u.email = '".$_SESSION['email']."'"
)->fetch_all(MYSQLI_ASSOC); 
var_dump($stocks);
var_dump($_SESSION['store_id']);
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product']))) {
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
    
    if (empty($_POST["price"])) {
        $errors["price"] = "Price is required";
    } else {
        $price = sanitize($_POST['price']);
    } 
    
    if (!empty($errors)) {
        break;
    }

    // --- user data handling
    $stock_exists = $conn->query(
       $sql = "SELECT *
        FROM stock s
        JOIN user_stock us ON us.stock_id = s.id
        WHERE us.email = '" . $_SESSION['email'] . "' AND s.name = '$name'"
    );
    $product_exists = $conn->query(
        "SELECT *
        FROM product p
        JOIN stock s ON p.stock_id = s.id 
        JOIN tindahan t ON t.id = p.tindahan_id
        WHERE t.id ='".$_SESSION['store_id']."' and s.name = '$name'"
    );
    if ($stock_exists->num_rows == 0) {
        $errors["name"] = "Product is not in stock";
        break;
    }
    if ($product_exists->num_rows > 0) {
        $errors["name"] = "Product is already in store";     
    }

    $stock = $stock_exists->fetch_assoc();
    if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
        $errors["quantity"] = "Input value is not valid";
    } else {
        $quantity = intval($quantity);
        if ($quantity > $stock['quantity']) {
            $errors["quantity"] = "Quantity exceeds stock quantity";
        } 
    }
    if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
        $errors["price"] = "Input value is not valid";
    } else {
        $price = floatval($price); 
    }
    
    if (!empty($errors)) {
        break;
    }

    // --- perform add operation
    $conn->query(
        "INSERT INTO product(tindahan_id, stock_id, quantity_sold, unit_price) 
        VALUES (".$_SESSION['store_id'].", ".$stock['id'].", $quantity, $price)"
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
    "?stock_id=".$_GET['stock_id'];?>">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).'?store_id='. $_SESSION['store_id'];?>">
        <div >
            <label>Product Name</label>
            <div id='see-stock'>See Stock</div>
            <input type="text" id="product-input" name="name"  value="<?php echo htmlspecialchars($name);?>">
            <?php if (isset($errors["name"])): ?>
                <div ><?php echo $errors["name"]; ?></div>
            <?php endif; ?>
            <div id='product-search'>
                <div id='product-attribute-title'>
                    <div>Name</div>
                    <div>Quantity</div>
                    <div>Price</div>
                    <div>Source</div>
                </div>
                <div id='all-products'>   
                <?php foreach($stocks as $stock):?> 
                    <div class='stock-item'>
                        <div><?php echo $stock['name']?></div>
                        <div><?php echo $stock['quantity']?></div>
                        <div><?php echo $stock['price']?></div>
                        <div><?php echo $stock['source']?></div>
                    </div>
                <?php endforeach;?>
                </div>
            </div>
        </div>

        <div >
            <label>Quantity</label>
            <input type="text" name="quantity"  value="<?php echo htmlspecialchars($quantity);?>">
            <?php if (isset($errors["quantity"])): ?>
                <div ><?php echo $errors["quantity"]; ?></div>
            <?php endif; ?>
        </div>

        <div >
            <label>Price</label>
            <input type="text" name="price"  value="<?php echo htmlspecialchars($price);?>">
            <?php if (isset($errors["price"])): ?>
                <div ><?php echo $errors["price"]; ?></div>
            <?php endif; ?>
        </div>
        
        <button type="submit" name="add_product" >Add Product</button>
    </form>
    <a href="../store.php?store_id=<?php echo $_SESSION['store_id']?>&view=products">
        <button >Back</button>
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
