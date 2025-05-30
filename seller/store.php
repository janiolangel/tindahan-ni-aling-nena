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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
            background-size: 400% 400%;
            animation: gradientShift 12s ease infinite;
            min-height: 100vh;
            color: #374151;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
            padding: 20px;
            min-height: 50vh;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 25px 35px;
            margin-bottom: 25px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            font-weight: bold;
            box-shadow: 0 6px 18px rgba(102, 126, 234, 0.3);==
        }

        .logo-text {
            font-family: "Poppins", sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            color: #6B7280;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .nav-tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 8px;
            margin-bottom: 25px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out 0.2s both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .nav-tab {
            flex: 1;
            padding: 14px 20px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #6B7280;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .nav-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 6px 18px rgba(102, 126, 234, 0.3);
            transform: translateY(-2px);
        }

        .nav-tab:not(.active):hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            transform: translateY(-1px);
        }

        .search-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out 0.4s both;
        }

        .search-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .content-area {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out 0.6s both;
            min-height: 500px;
        }

        .item-card {
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid #E5E7EB;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .item-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .item-card:hover::before {
            transform: scaleY(1);
        }

        .item-info {
            flex: 1;
            margin-bottom: 16px;
        }

        .item-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 8px;
            font-family: "Poppins", sans-serif;
        }

        .item-subtitle {
            color: #6B7280;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .item-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-edit {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(239, 68, 68, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(102, 126, 234, 0.4);
        }

        .sidebar {
            position: fixed;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 16px;
            z-index: 100;
        }

        .sidebar .btn, .add-product, .add-invoice,  .menu{
            padding: 14px 20px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            color: #667eea;
            border: 2px solid #667eea;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            min-width: 140px;
            text-align: center;
        }

        .sidebar .btn:hover {
            background: #667eea;
            color: white;
        }

        .checkbox-item {
            margin-left: 16px;
            accent-color: #667eea;
            transform: scale(1.2);
        }

        .delete-mode .item-card {
            border-color: #EF4444;
            background: linear-gradient(135deg, #FEF2F2 0%, #FDE8E8 100%);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6B7280;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .stock-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 16px;
            margin: 12px 0;
        }

        .stock-detail {
            background: rgba(102, 126, 234, 0.1);
            padding: 12px;
            border-radius: 8px;
            text-align: center;
        }

        .stock-detail-label {
            font-size: 0.8rem;
            color: #6B7280;
            margin-bottom: 4px;
        }

        .stock-detail-value {
            font-weight: 600;
            color: #1F2937;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header {
                padding: 20px;
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .nav-tabs {
                flex-direction: column;
                gap: 8px;
            }

            .sidebar {
                position: static;
                transform: none;
                flex-direction: row;
                justify-content: center;
                margin: 20px 0;
            }

            .item-card {
                padding: 20px;
            }

            .item-actions {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <!-- <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIGZpbGw9IiM1ZTFhMTIiLz48dGV4dCB4PSIyMCIgeT0iMjUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0id2hpdGUiPkxOPC90ZXh0Pjwvc3ZnPg==" alt="Logo" class="logo"> -->
            <div>TINDAHAN NI<br>ALING NENA</div>
        </div>
        <div>Current User: <span class="user-name"><?php echo htmlspecialchars($_SESSION["username"]); ?></span></div>
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
        <p>Current Income: TO BE MADE</p>
    <?php elseif ($_SESSION['store_view'] == 'products'): ?>
        <aside>
            <?php foreach ($products as $product): ?>
                <a class="product-desc" style="text-decoration: none; color: inherit;">
                    <?php echo htmlspecialchars($product['name']); ?>
                    <?php echo htmlspecialchars($product['unit_price']); ?>
                    <?php echo htmlspecialchars($product['quantity_sold']); ?>
                </a>
                <a href="operations/edit_product.php?stock_id=<?php echo $product['stock_id'];?>">Edit</a>
                <a href="operations/delete_product.php?stock_id=<?php echo $product['stock_id'];?>">Delete</a>
                <br>
            <?php endforeach;?>
            <a href="operations/add_product.php?store_id=<?php echo $_SESSION['store_id']?>" class="add-product">Add Product +</a>
        </aside>
    <?php elseif ($_SESSION['store_view'] == 'invoice'): ?>
        <label>Invoice List</label>
        <aside>
            <?php foreach ($invoices as $invoice): ?>
                <a class="invoice-desc" style="text-decoration: none; color: inherit;">
                    <?php echo htmlspecialchars($invoice['name']); ?>
                    <?php echo htmlspecialchars($invoice['status']); ?>
                    <?php echo htmlspecialchars($invoice['quantity']); ?>
                    <?php echo htmlspecialchars($invoice['subtotal']); ?>
                </a>
                <a href="operations/edit_invoice.php?stock_id=<?php echo $product['stock_id'];?>">Edit</a>
                <a href="operations/delete_invoice.php?stock_id=<?php echo $product['stock_id'];?>">Delete</a>
                <br>
            <?php endforeach;?>
            <a href="operations/add_invoice.php?store_id=<?php echo $_SESSION['store_id']?>" class="add-product">Add Invoice +</a>
        </aside>
    <?php endif;?>
    <div>
        <a href='dashboard.php'>Go Back to Stores</a>
    </div>
</body>
</html>

