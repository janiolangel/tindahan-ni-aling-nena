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
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_store']))) {
        $store =  $store = $conn->query(
            "SELECT * 
            FROM tindahan
            WHERE id = ".$_GET['store_id'].""
        )->fetch_assoc();
        $name = $store['name'];
        $address = $store['address'];
        break;
    }   
     
    // --- perform delete operation
    $conn->query(
        "DELETE FROM tindahan 
        WHERE id = ".$_GET['store_id'].";"
    );
    header("Location: ../dashboard.php");
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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?store_id=".$_GET['store_id'];?>">
        <div>
            <label>Delete <?php echo htmlspecialchars($name)?>?</label>
            <button type="submit" name="delete_store">Delete</button>
        </div>
    </form>
   <a href="../dashboard.php?view=store">
        <button>Back</button>
    </a>
</body>
</html>
