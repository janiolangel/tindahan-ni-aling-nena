<?php
// -- includes
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();
date_default_timezone_set('Asia/Manila');
$invoice_items = $conn->query("
    SELECT ii.id, p.name, ii.quantity, ii.subtotal, ii.status
    FROM invoice i
    JOIN invoiceitem_invoice i2i ON i.id = i2i.invoice_id
    JOIN invoice_item ii ON i2i.invoice_item_id = ii.id
    JOIN product_invoiceitem pii ON ii.id = pii.invoice_item_id
    JOIN product p ON pii.product_id = p.product_id
    WHERE i.id = ".$_GET['invoice_id']."
")->fetch_all(MYSQLI_ASSOC);

do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_invoice']))) {
        break;
    }

    // --- perform edit operation
    $payed_items = isset($_POST['payed_items']) ? $_POST['payed_items'] : [];
    
    $is_cleared = 1;
    foreach ($invoice_items as $item) {
        $item_id = $item['id'];
        $old_status = $item['status'];
        $new_status = in_array($item_id, $payed_items) ? 1 : 0;
        if ($new_status == 0) {
            $is_cleared = 0;
        }
        $conn->query("UPDATE invoice_item SET status = $new_status WHERE id = $item_id");

        if ($old_status != $new_status) {
            if ($new_status == 1) {
                $conn->query("
                    UPDATE tindahan 
                    SET revenue = revenue + ".$item['subtotal']."
                    WHERE id = ".$_SESSION['store_id']."");
            } else {
                $conn->query("
                    UPDATE tindahan 
                    SET revenue = revenue - ".$item['subtotal']."
                    WHERE id = ".$_SESSION['store_id']."");
            }
        }
    }
    $conn->query("
        UPDATE invoice 
        SET status = $is_cleared
        WHERE id = ".$_GET['invoice_id']."");
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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?invoice_id=".$_GET['invoice_id'];?>">
        <?php foreach ($invoice_items as $invoice_item): ?>
            <div>
                <span>
                    <?php echo htmlspecialchars($invoice_item['name']); ?>
                    <?php echo htmlspecialchars($invoice_item['quantity']); ?>
                    <?php echo htmlspecialchars($invoice_item['subtotal']); ?>
                </span>
                <input type="checkbox" name="payed_items[]" value="<?php echo $invoice_item['id']?>" <?php echo ($invoice_item['status'] == 1) ? 'checked' : ''; ?>>            </div>
            </div>
        <?php endforeach;?>
        <button type="submit" name="edit_invoice" class="btn">Done</button>
    </form>     
</body>