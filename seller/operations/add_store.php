<?php
// -- includes 
include '../../includes/db_connector.php';
include '../../includes/functions.php';

// -- starts and gets session data
session_start();

// -- add store form handling
$name = $address = "";
$errors = [];
do {
    // --- if wrong request or new start
    if (!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_store']))) {
        break;
    }

    // --- name user input handling
    if (empty($_POST["name"])) {
        $errors["name"] = "Name is required";
        break;
    } else {
        $name = sanitize($_POST['name']);
    } 
    $address = sanitize($_POST['address']);

    // --- user data handling
    $name_exists = $conn->query(
        "SELECT *
        FROM user_tindahan ut
        JOIN tindahan t ON ut.tindahan_id = t.id
        WHERE ut.email = '".$_SESSION['email']."' AND t.name = '$name'");
    var_dump($name_exists);
    if ($name_exists->num_rows > 0) {
        $errors["name"] = "Name is already taken";
    }   
    if (strlen($name) < 5) {
        $errors["name"] = "Name must be at least 5 characters";
    }
    if (!empty($errors)) {
        break;
    }

    // --- perform add operation
    $conn->query(
        "INSERT INTO tindahan (id, name, address, expense, revenue) 
        VALUES (NULL, '$name', '$address', 0, 0)"
    );
    $last_id = $conn->insert_id;
    $email = $_SESSION["email"]; 
    $conn->query(
        "INSERT INTO user_tindahan(email, tindahan_id)
        VALUES ('$email', $last_id)"
    );
    $_SESSION["tindahan_id"] = $last_id; 
    header("Location: ../dashboard.php?view=store");
} while (0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Store - Tindahan ni Aling Nena</title>
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
            max-width: 500px;
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

        .success-message {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
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

        @media (max-width: 640px) {
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

        .form-hint {
            color: #6B7280;
            font-size: 0.875rem;
            margin-top: 6px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <div class="logo-icon">🏪</div>
            <h1 class="form-title">Add New Store</h1>
            <p class="form-subtitle">Create a new store for your business</p>
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
            <div class="form-group">
                <label for="name" class="form-label">Store Name</label>
                <div class="input-group">
                    <span class="input-icon">🏪</span>
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        class="form-input with-icon <?php echo isset($errors["name"]) ? 'error' : ''; ?>"
                        value="<?php echo htmlspecialchars($name);?>"
                        placeholder="Enter store name"
                        maxlength="100"
                    >
                </div>
                <?php if (isset($errors["name"])): ?>
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        <?php echo $errors["name"]; ?>
                    </div>
                <?php endif; ?>
                <div class="form-hint">Store name must be at least 5 characters long</div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <div class="input-group">
                    <span class="input-icon">📍</span>
                    <input 
                        type="text" 
                        id="address"
                        name="address" 
                        class="form-input with-icon"
                        value="<?php echo htmlspecialchars($address);?>"
                        placeholder="Enter store address (optional)"
                        maxlength="255"
                    >
                </div>
                <div class="form-hint">Physical location of your store</div>
            </div>

            <div class="form-actions">
                <button type="submit" name="add_store" class="btn btn-primary" id="submitBtn">
                    <span>➕ Add Store</span>
                </button>
                <a href="../dashboard.php?view=store" class="btn btn-secondary">
                    <span>← Back</span>
                </a>
            </div>
        </form>
    </div>

    
</body>
</html>