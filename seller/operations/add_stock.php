<?php
// -- includes 
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- add stock form handling
$name = $quantity = $unit = $price = $source = "";
$errors = [];
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_stock']))) {
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
    
    if (empty($_POST["unit"])) {
        $errors["unit"] = "Unit is required";
    } else {
        $unit = sanitize($_POST['unit']);
    } 

    if (empty($_POST["price"])) {
        $errors["price"] = "Price is required";
    } else {
        $price = sanitize($_POST['price']);
    } 
    
    if (empty($_POST["source"])) {
        $errors["source"] = "Source is required";
    } else {
        $source = sanitize($_POST['source']);
    } 

    if (!empty($errors)) {
        break;
    }

    // --- user data handling
    $name_exists = $conn->query(
        "SELECT *
        FROM user_stock us
        JOIN stock s ON us.stock_id = s.id
        WHERE us.email = '".$_SESSION['email']."' AND s.name = '$name'");
    if ($name_exists->num_rows > 0) {
        $errors["name"] = "Name is already in stock";
    }   

    if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
        $errors["quantity"] = "Quantity value is not valid";
    } else {
       $quantity = intval($quantity); 
    }

    if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
        $errors["price"] = "Price value is not valid";
    } else {
        $price = floatval($price); 
    }

    if (!empty($errors)) {
        break;
    }

    // --- perform add operation
    $conn->query(
        "INSERT INTO stock (id, name, quantity, unit, price, source) 
        VALUES (NULL, '$name', $quantity, '$unit', $price, '$source')"
    );
    $last_id = $conn->insert_id;
    $conn->query(
        "INSERT INTO user_stock (email, stock_id)
        VALUES ('".$_SESSION['email']."', $last_id)"
    );
    header("Location: ../dashboard.php?view=stock");
} while (0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Stock - Tindahan ni Aling Nena</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 64px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            font-weight: bold;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }

        .form-title {
            font-family: "Poppins", sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #6B7280;
            font-size: 1rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            font-family: "Inter", sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .form-input.error {
            border-color: #EF4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .error-message {
            color: #EF4444;
            font-size: 0.875rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .error-icon {
            font-size: 0.8rem;
        }

        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 32px;
        }

        .btn {
            flex: 1;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: "Inter", sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            border: 2px solid #E5E7EB;
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(102, 126, 234, 0.1);
            border-color: #667eea;
            transform: translateY(-1px);
        }

        .btn-secondary:active {
            transform: translateY(0);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 1.1rem;
            z-index: 2;
        }

        .form-input.with-icon {
            padding-left: 48px;
        }

        .form-hint {
            color: #6B7280;
            font-size: 0.875rem;
            margin-top: 6px;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .form-container {
                padding: 32px 24px;
                margin: 20px;
                border-radius: 20px;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                padding: 14px 20px;
            }
        }

        /* Loading animation for submit button */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <div class="logo-icon">📦</div>
            <h1 class="form-title">Add New Stock</h1>
            <p class="form-subtitle">Add inventory items to your store</p>
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="name" class="form-label">Stock Name</label>
                <div class="input-group">
                    <span class="input-icon">📦</span>
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        class="form-input with-icon <?php echo isset($errors["name"]) ? 'error' : ''; ?>"
                        value="<?php echo htmlspecialchars($name);?>"
                        placeholder="Enter stock name"
                        maxlength="100"
                    >
                </div>
                <?php if (isset($errors["name"])): ?>
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        <?php echo $errors["name"]; ?>
                    </div>
                <?php endif; ?>
                <div class="form-hint">Name of the product or item</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity</label>
                    <div class="input-group">
                        <span class="input-icon">🔢</span>
                        <input 
                            type="number" 
                            id="quantity"
                            name="quantity" 
                            class="form-input with-icon <?php echo isset($errors["quantity"]) ? 'error' : ''; ?>"
                            value="<?php echo htmlspecialchars($quantity);?>"
                            placeholder="Enter quantity"
                            min="0"
                        >
                    </div>
                    <?php if (isset($errors["quantity"])): ?>
                        <div class="error-message">
                            <span class="error-icon">⚠️</span>
                            <?php echo $errors["quantity"]; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-hint">Number of items in stock</div>
                </div>

                <div class="form-group">
                    <label for="unit" class="form-label">Unit</label>
                    <div class="input-group">
                        <span class="input-icon">📏</span>
                        <input 
                            type="text" 
                            id="unit"
                            name="unit" 
                            class="form-input with-icon <?php echo isset($errors["unit"]) ? 'error' : ''; ?>"
                            value="<?php echo htmlspecialchars($unit);?>"
                            placeholder="e.g., pcs, kg, liters"
                            maxlength="20"
                        >
                    </div>
                    <?php if (isset($errors["unit"])): ?>
                        <div class="error-message">
                            <span class="error-icon">⚠️</span>
                            <?php echo $errors["unit"]; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-hint">Unit of measurement</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price" class="form-label">Price</label>
                    <div class="input-group">
                        <span class="input-icon">💰</span>
                        <input 
                            type="number" 
                            id="price"
                            name="price" 
                            class="form-input with-icon <?php echo isset($errors["price"]) ? 'error' : ''; ?>"
                            value="<?php echo htmlspecialchars($price);?>"
                            placeholder="Enter price"
                            step="0.01"
                            min="0"
                        >
                    </div>
                    <?php if (isset($errors["price"])): ?>
                        <div class="error-message">
                            <span class="error-icon">⚠️</span>
                            <?php echo $errors["price"]; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-hint">Price per unit</div>
                </div>

                <div class="form-group">
                    <label for="source" class="form-label">Source</label>
                    <div class="input-group">
                        <span class="input-icon">🏪</span>
                        <input 
                            type="text" 
                            id="source"
                            name="source" 
                            class="form-input with-icon <?php echo isset($errors["source"]) ? 'error' : ''; ?>"
                            value="<?php echo htmlspecialchars($source);?>"
                            placeholder="Enter supplier/source"
                            maxlength="100"
                        >
                    </div>
                    <?php if (isset($errors["source"])): ?>
                        <div class="error-message">
                            <span class="error-icon">⚠️</span>
                            <?php echo $errors["source"]; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-hint">Supplier or source name</div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="add_stock" class="btn btn-primary" id="submitBtn">
                    <span>📦 Add Stock</span>
                </button>
                <a href="../dashboard.php?view=stock" class="btn btn-secondary">
                    <span>← Back</span>
                </a>
            </div>
        </form>
    </div>

</body>
</html>