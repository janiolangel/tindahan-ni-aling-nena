<?php
// -- includes
include '../includes/db_connector.php';
include '../includes/functions.php';

// -- starts and gets session data
session_start();

// -- operation
// --- delete stocks
$delete_stock_enabled = isset($_GET['operation']) && $_GET['operation'] == 'delete_stock';

// -- data
// --- view data
$_SESSION['dashboard_view'] = isset($_GET['view']) ? $_GET['view'] : 'store'; 
// --- tindahans managed data
$stores = $conn->query(
    "SELECT t.id, t.name, t.address 
    FROM user as u
    JOIN user_tindahan ut ON u.email = ut.email
    JOIN tindahan t ON ut.tindahan_id = t.id
    WHERE u.email = '".$_SESSION['email']."';"
)->fetch_all(MYSQLI_ASSOC);
// --- stocks managed data
$stocks = $conn->query(
    "SELECT *
    FROM user as u
    JOIN user_stock us ON u.email = us.email
    JOIN stock s ON us.stock_id = s.id
    WHERE u.email = '".$_SESSION['email']."';"
)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        input {
            display: block;
        }
        a {
            color: inherit;
        }
        .active {
            color: red;
        }
    </style>
</head>
<body>
    <header>
        <div>
            <div>TINDAHAN NI<br>ALING NENA</div>
        </div>
        <div>Current User: <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span></div>
    </header>
    <nav>
        <a href='?view=store' class="<?php echo ($_SESSION['dashboard_view'] == 'store')? 'active':''?>">Stores</a>
        <a href='?view=stock' class="<?php echo ($_SESSION['dashboard_view'] == 'stock')? 'active':''?>">Stocks</a>
    </nav>

    <div class="search-container">
        <input type="text" class="search-input" placeholder="Search">
    </div>
    
    <?php if ($_SESSION['dashboard_view'] == "store"):?>
    <div>
        <?php foreach ($stores as $store): ?>
        <div class="store-item">
            <a href="store.php?store_id=<?php echo $store['id']; ?>" style="text-decoration: none; color: inherit;">
                <?php echo htmlspecialchars($store['name']); ?>
                <?php echo htmlspecialchars($store['address']); ?>
            </a>
            <a href="operations/edit_store.php?store_id=<?php echo $store['id'];?>">Edit</a>
            <a href="operations/delete_store.php?store_id=<?php echo $store['id'];?>">Delete</a>
        </div>
        <?php endforeach; ?>
        <aside>
            <a href='operations/add_store.php?'>Add Store +</a>
            <a href="../logout.php">Log Out</a>
        </aside>
    </div>

    <?php elseif ($_SESSION['dashboard_view'] == "stock"):?>        
        <form action='operations/delete_stock.php' method='post'>
            <div>
            <?php foreach ($stocks as $stock): ?>
                <div class="stock-item">
                    <a>
                        <?php echo htmlspecialchars($stock['name']); ?>
                        <?php echo htmlspecialchars($stock['price']); ?>
                        <?php echo htmlspecialchars($stock['source']); ?>
                    </a>
                    <a href="operations/edit_stock.php?stock_id=<?php echo $stock['id'];?>">Edit</a>
                    <input type="checkbox" name="deleted_stocks[]" value="<?php echo $stock['id'];?>" style="display: <?php echo ($delete_stock_enabled)?'inline-block': 'none'?>">
                </div>
            <?php endforeach; ?>
            </div>
            <aside>
                <a href='operations/add_stock.php?'>Add Stock +</a>
                <?php if ($delete_stock_enabled): ?>
                    <input type='submit' value='Confirm Delete'></a>
                    <a href='?view=stock'>Cancel Delete</a>
                <?php else: ?>
                    <a href='?view=stock&operation=delete_stock'>Delete Stock </a>
                <?php endif;?>
                <a href="../logout.php">Log Out</a>
            </aside>    
        </form>
    </div>
    <?php endif;?>

<script>
    const searchInput = document.querySelector('.search-input');
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('.<?php echo $_SESSION['dashboard_view']?>-item');
        console.log(rows);
        rows.forEach(row => {
            const text = row.textContent.toLowerCase()
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>

</body>
</html>