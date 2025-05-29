<?php
// -- includes 
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

date_default_timezone_set('Asia/Manila');

$date = date('F j, Y');

var_dump($date);

// -- add product form handling
$quantity = $subtotal = $default_status = 0;

$errors = [];

$products = $conn->query(
    "SELECT *
    FROM user u
    JOIN user_stock us ON u.email = us.email
    JOIN stock s ON us.stock_id = s.id
    join product p on p.stock_id = s.id
    WHERE u.email = '".$_SESSION['email']."'"
)->fetch_all(MYSQLI_ASSOC); 

$invoice = $conn->query(
    "SELECT *
    FROM invoice it
    JOIN user_tindahan ut on ut.tindahan_id = it.id
    WHERE ut.tindahan_id = '".$_SESSION['store_id']."'")->fetch_all(MYSQLI_ASSOC); 
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_invoice']))) {
        break;
    }

    // --- invoice input handling

    if (empty($_POST["amount"])) {
        $errors["amount"] = "Quantity is required";
    } else {
        $amount = sanitize($_POST['amount']);
    } 
    
    if (!empty($errors)) {
        break;
    }

    // --- user data handling
    if ($invoice->num_rows >= 0) {
        $errors["buyer"] = "Invoice not created.";
        break;
    }

    $invoice->fetch_assoc();
    if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
        $errors["quantity"] = "Input value is not valid";
    } else {
        $quantity = intval($quantity);
        if ($quantity > $products['quantity']) {
            $errors["quantity"] = "Quantity exceeds product quantity";
        } 
    }
    if (!filter_var($amount, FILTER_VALIDATE_FLOAT)) {
        $errors["subtotal"] = "Input value is not valid";
    } else {
        $amount = floatval($amount); 
    }
    
    if (!empty($errors)) {
        break;
    }

    // --- perform add operation
    $unit_price = $conn->query("SELECT unit_price
                                FROM product
                                join stock on stock.id = product.stock_id
                                WHERE stock_id = '".$_SESSION['store_id']."'")
                                ->fetch_assoc()['unit_price'];
    $subtotal = $unit_price * $quantity;

    $conn->query(
        "INSERT INTO invoice(buyer, id, date, paid_status, amount) 
        VALUES ($buyer, ".$invoice_item['id'].", $date, $default_status, $subtotal)"
    );
    header("Location: ../store.php?store_id=".$_SESSION['store_id']."&view=invoice");
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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).'?store_id='. $_SESSION['store_id'];?>">  
        <div>
            <label>Add Invoice</label>
        </div>  
        <div>
            <label>Name</label>
            <input type="text" name="name">
            <?php if (isset($errors["buyer"])): ?>
                <div ><?php echo $errors["buyer"]; ?></div>
            <?php endif; ?>
        </div>  
        <div >
            <label>Product Name</label>
            <div id='see-stock'>See Stock</div>
            <input type="text" id="product-input" name="name">
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
                <?php foreach($products as $product):?> 
                    <div class='stock-item'>
                        <div><?php echo $product['name']?></div>
                        <div><?php echo $product['quantity_sold']?></div>
                        <div><?php echo $product['unit_price']?></div>
                        <div><?php echo $product['source']?></div>
                    </div>
                <?php endforeach;?>
                </div>
            </div>
        </div>
        <div>
            <label>Quantity</label>
            <input type="text" name="quantity">
            <?php if (isset($errors["quantity"])): ?>
                <div ><?php echo $errors["quantity"]; ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" name="add_invoice">Add Invoice Item</button>
    </form>
    <a href="../store.php?store_id=<?php echo $_SESSION['store_id']?>&view=invoice">
        <button>Back</button>
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