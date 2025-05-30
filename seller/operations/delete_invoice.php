<?php
// -- includes
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- delete stock handling
$name = "";
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_invoice']))) {
         $invoice = $conn->query(
            "SELECT * 
            FROM invoice
            WHERE id = ".$_GET['invoice_id'].""
        )->fetch_assoc();
        $name = $invoice['buyer'];
        break;
    }   
     
    // --- perform delete operation
    $conn->query(
        "DELETE FROM invoice 
        WHERE id = ".$_GET['invoice_id'].";"
    );
    header("Location: ../store.php?store_id=".$_SESSION['store_id']."&view=invoice");
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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?store_id=".$_SESSION['store_id']."&view=invoice&invoice_id=".$_GET['invoice_id']."";?>">
        <div>
            <label>Delete <?php echo htmlspecialchars($name)?>?</label>
            <button type="submit" name="delete_invoice">Delete</button>
        </div>
    </form>
    <a href="../store.php?store_id=<?php echo $_SESSION['store_id']?>&view=invoice">
        <button>Back</button>
    </a>
</body>
</html>





