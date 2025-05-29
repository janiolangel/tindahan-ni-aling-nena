<?php
// -- includes
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- delete stock handling
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_stock']))) {
        $display_stocks = [];
        foreach ($_POST["deleted_stocks"] as $stock_id) {
            $stock_id = (int)$stock_id;
            $stock_temp = $conn->query("SELECT * FROM stock WHERE id = $stock_id")->fetch_assoc();
            if ($stock_temp) {
                $display_stocks[] = $stock_temp;
            }
        }
        $_SESSION['display_stocks'] = $display_stocks;    
        break;
    }

    // -- perform delete operation
    foreach ($_SESSION['display_stocks'] as $stock) {
        $conn->query("DELETE FROM stock WHERE id = " . (int)$stock['id']);
    }
    unset($_SESSION['display_stocks']);
    header("Location: ../dashboard.php?view=stock");
    exit();
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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?store_id=".$_SESSION['store_id'];?>">
        <div>
            <label>Delete</label><br>
            <?php foreach ($display_stocks as $stock): ?>
                <label><?php echo $stock['name']?></label><br>
            <?php endforeach;?>
            <button type="submit" name="delete_stock">Delete</button>
        </div>
    </form>
   <a href="../dashboard.php?view=store">
        <button>Back</button>
    </a>
</body>
</html>





