<?php
// -- includes
include '../includes/db_connector.php';
include '../includes/functions.php';

// -- starts and gets session data
session_start();
date_default_timezone_set('Asia/Manila');
$_SESSION['store_id'] = $_GET['store_id']; 
$_SESSION['store_view'] = isset($_GET['view']) ? $_GET['view']: 'summary'; 

// -- data
// --- tindahan data
$store = $conn->query(
    "SELECT * 
    FROM tindahan 
    WHERE id = ".$_SESSION['store_id'].""
)->fetch_assoc();   

// --- product data
$products = $conn->query(
    "SELECT *
    FROM product p
    WHERE p.tindahan_id = ".$_SESSION['store_id'].""
)->fetch_all(MYSQLI_ASSOC);
var_dump($store);
var_dump($products);

$invoices = $conn->query(
    "SELECT *
    FROM invoice i
    JOIN tindahan_invoice ti on i.id = ti.invoice_id
    WHERE ti.tindahan_id = ".$_SESSION['store_id'].""
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        input {
            display: block;
        }
        .active {
            color: red !important;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <!-- <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIGZpbGw9IiM1ZTFhMTIiLz48dGV4dCB4PSIyMCIgeT0iMjUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0id2hpdGUiPkxOPC90ZXh0Pjwvc3ZnPg==" alt="Logo" class="logo"> -->
            <div>TINDAHAN NI<br>ALING NENA</div>
        </div>
    </header>

    <div>
        <h2><?php echo $store['name']?></h2>
        <h2><?php echo date('F j, Y')?></h2>        
    </div>
   
    <aside>
        <a href='?store_id=<?php echo $_SESSION['store_id']?>&view=summary' class=<?php echo ($_SESSION['store_view'] == 'summary') ? 'active': ''?>>Summary</a>
        <a href='?store_id=<?php echo $_SESSION['store_id']?>&view=invoice' class=<?php echo ($_SESSION['store_view'] == 'invoice') ? 'active': ''?>>Invoice</a>
        <a href='?store_id=<?php echo $_SESSION['store_id']?>&view=products' class=<?php echo ($_SESSION['store_view'] == 'products') ? 'active': ''?>>Products</a>
        <a href='?store_id=<?php echo $_SESSION['store_id']?>&view=tags' class=<?php echo ($_SESSION['store_view'] == 'tags') ? 'active': ''?>>Tags</a>
        <a href='?store_id=<?php echo $_SESSION['store_id']?>&view=suki' class=<?php echo ($_SESSION['store_view'] == 'suki') ? 'active': ''?>>Suki</a>
    </aside>
    
    <?php if ($_SESSION['store_view'] == 'summary'): ?>
        <p>Current Most-Selling Item: TO BE MADE</p>
        <p>Current Income: <?php echo $store['revenue'] - $store['expense'];?></p>
    <?php elseif ($_SESSION['store_view'] == 'products'): ?>
        <aside>
            <?php foreach ($products as $product): ?>
                <a class="product-desc" style="text-decoration: none; color: inherit;">
                    <?php echo htmlspecialchars($product['name']); ?>
                    <?php echo htmlspecialchars($product['unit_price']); ?>
                    <?php echo htmlspecialchars($product['quantity_sold']); ?>
                </a>
                <a href="operations/edit_product.php?product_id=<?php echo $product['product_id'];?>">Edit</a>
                <a href="operations/delete_product.php?product_id=<?php echo $product['product_id'];?>">Delete</a>
                <br>
            <?php endforeach;?>
            <a href="operations/add_product.php?store_id=<?php echo $_SESSION['store_id']?>">Add Product +</a>
        </aside>
    <?php elseif ($_SESSION['store_view'] == 'invoice'): ?>
        <label>Invoice List</label>
        <aside>
            <a href="operations/add_invoice.php?store_id=<?php echo $_SESSION['store_id']?>&session=new">Add Invoice +</a>
        </aside>
        <?php foreach ($invoices as $invoice): ?>
            <div class="invoice-container">
                <a href="operations/edit_invoice.php?invoice_id=<?php echo $invoice['id']?>" class="invoice-desc" style="text-decoration: none; color: inherit;">
                    <?php echo htmlspecialchars($invoice['buyer']); ?>
                    <?php echo htmlspecialchars($invoice['total_amount']); ?>
                    <?php echo htmlspecialchars($invoice['date']); ?>
                    <?php echo htmlspecialchars($invoice['payment_type']); ?>
                    <?php echo htmlspecialchars($invoice['status']); ?>
                </a>         
                <a href="operations/delete_invoice.php?invoice_id=<?php echo $invoice['id']; ?>">Delete</a>
            </div>
        <?php endforeach; ?>
        </aside>
    <?php endif;?>
    <div>
        <a href='dashboard.php'>Go Back to Stores</a>
    </div>
</body>
</html>

