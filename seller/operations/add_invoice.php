<?php
// -- includes 
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();
date_default_timezone_set('Asia/Manila');
$_SESSION['product_list'] = (isset($_GET['session']) && $_GET['session'] == 'new') ? []: $_SESSION['product_list'];
$products = $conn->query(
    "SELECT *
    FROM tindahan t
    JOIN product p ON t.id = p.tindahan_id 
    WHERE t.id = ".$_SESSION['store_id'].""
)->fetch_all(MYSQLI_ASSOC);

// -- add invoice form handling
$buyer = $payment_type = $quantity = $status = "";
$errors = [];
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_invoice']))) {
        break;
    }

    // --- name user input handling
    if (empty($_POST["buyer"])) {
        $errors["buyer"] = "Name is required";
    } else {
        $buyer = sanitize($_POST['buyer']);
    } 

    if (empty($_POST["payment_type"])) {
        $errors["payment_type"] = "Payment Type is required";
    } else {
        $payment_type = sanitize($_POST["payment_type"]);
    } 

    if (!empty($errors)) {
        break;
    }

    // --- user data handling
    foreach ($_POST['added_quantity'] as $quantity) {
        if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
            $errors["added_quantity"] = "Quantity value/s are not valid";
            break;
        }
    }
    if (!empty($errors)) {
        break;
    }

    for ($i = 0; $i < count($_POST['added_products']); $i++) {
        $product_id = intval($_POST['added_products'][$i]);
        $quantity = intval($_POST['added_quantity'][$i]);
        
        $curr_product = $conn->query("
            SELECT *
            FROM product p 
            WHERE p.product_id = $product_id
        ")->fetch_assoc();

        if ($curr_product['quantity_sold'] < $quantity) {
            $errors["added_quantity"] = "Quantity value/s exceed product quantity";
            break;
        }
    }
    if (!empty($errors)) {
        break;
    }

    
    // --- perform add operation
    $date = date('Y-m-d'); // Better SQL-compatible date format

    // Insert invoice
    $conn->query("
        INSERT INTO invoice(id, date, status, buyer, total_amount, payment_type)
        VALUES (NULL, '$date', FALSE, '$buyer', 0.00, '$payment_type')
    ");

    $invoice_id = $conn->insert_id; // Save invoice ID for later use
    $total = 0;
    for ($i = 0; $i < count($_POST['added_products']); $i++) {
        $product_id = intval($_POST['added_products'][$i]);
        $quantity = intval($_POST['added_quantity'][$i]);
            
        // Fetch product details
        $curr_product = $conn->query("
            SELECT *
            FROM product p 
            WHERE p.product_id = $product_id
        ")->fetch_assoc();

        $unit_price = floatval($curr_product['unit_price']);
        $subtotal_cost = $unit_price * $quantity;
        $total += $subtotal_cost;

        // Update product quantities 
        $conn->query(
            "UPDATE product
            SET quantity_sold = quantity_sold - $quantity
            WHERE product_id = $product_id;"
        );    
        
        // Insert invoice item
        $conn->query("
            INSERT INTO invoice_item(id, subtotal, quantity, status)
            VALUES (NULL, $subtotal_cost, $quantity, 0)
        ");

        $invoice_item_id = $conn->insert_id;

        $conn->query("
            INSERT INTO product_invoiceitem(product_id, invoice_item_id)
            VALUES (".$curr_product['product_id'].", $invoice_item_id)
        ");
        // Link invoice item to invoice
       $conn->query(
        "INSERT INTO invoiceitem_invoice(invoice_item_id, invoice_id)
            VALUES ($invoice_item_id, $invoice_id)
        ");
    }
    // Add total
    $conn->query(
        "UPDATE invoice
        SET total_amount = $total
        WHERE id = $invoice_id
    ");
    $curr_store = $_SESSION['store_id']; 
    $conn->query(
        "INSERT INTO tindahan_invoice(tindahan_id, invoice_id)
            VALUES ($curr_store, $invoice_id)
        ");

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
        #product-attribute-title {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr; 
        }
        .product-item {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr; 
            
        }
    </style>
</head>
<body>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
        <div>
            <label>Customer Name</label>
            <input type="text" name="buyer" value="<?php echo htmlspecialchars($buyer);?>">
            <?php if (isset($errors["buyer"])): ?>
                <div ><?php echo $errors["buyer"]; ?></div>
            <?php endif; ?>
        </div>
        <div>
            <label>Payment Type</label>
            <input type="radio" name="payment_type" value="Cash" <?php echo ($payment_type == 'Cash') ? 'checked' : ''; ?>> Cash<br>
            <input type="radio" name="payment_type" value="GCash" <?php echo ($payment_type == 'GCash') ? 'checked' : ''; ?>> GCash<br>
            <input type="radio" name="payment_type" value="Paymaya" <?php echo ($payment_type == 'Paymaya') ? 'checked' : ''; ?>> Paymaya<br>

            <?php if (isset($errors["payment_type"])): ?>
                <div ><?php echo $errors["payment_type"]; ?></div>
            <?php endif; ?>
        </div>
        <div>
            <label>Product</label>
            <?php foreach($products as $index => $product): ?>
                <div class='product-item'>
                    <div><?php echo $product['name']?></div>
                    <div><?php echo $product['unit_price']?></div>
                    <div><?php echo $product['quantity_sold']?></div>
                    <div>
                        <input 
                            type="checkbox" 
                            name="added_products[]" 
                            value="<?php echo $product['product_id']; ?>" 
                            class="product-checkbox" 
                            data-index="<?php echo $index; ?>"
                        >
                    </div>
                    <div>
                        <input 
                            type="text" 
                            name="added_quantity[]" 
                            class="product-quantity" 
                            data-index="<?php echo $index; ?>" 
                            disabled
                        >
                    </div>
                </div>               
            <?php endforeach; ?>
            <?php if (isset($errors["added_quantity"])): ?>
                <div ><?php echo $errors["added_quantity"]; ?></div>
            <?php endif; ?>

        </div>
        <button type="submit" name="add_invoice">Add Invoice</button>
    </form>
    <a href="../store.php?store_id=<?php echo $_SESSION['store_id']?>&view=invoice">
        <button >Back</button>
    </a>

    <script>
        
    document.addEventListener("DOMContentLoaded", function() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', function() {
                const index = this.getAttribute('data-index');
                const quantityInput = document.querySelector(`.product-quantity[data-index='${index}']`);

                if (this.checked) {
                    quantityInput.disabled = false;
                } else {
                    quantityInput.disabled = true;
                    quantityInput.value = ''; // Optional: clear value if unchecked
                }
            });
        });
    });
</script>
</body>
</html>
