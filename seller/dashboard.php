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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tindahan ni Aling Nena - Dashboard</title>
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

        .sidebar .btn {
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
    <div class="container">
        <header class="header">
            <div class="logo">
                <div class="logo-icon"></div>
                <div class="logo-text">TINDAHAN NI<br>ALING NENA</div>
            </div>
            <div class="user-info">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION["username"], 0, 2)); ?></div>
                <span>Current User: <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            </div>
        </header>

        <nav class="nav-tabs">
            <a href='?view=store' class="nav-tab <?php echo ($_SESSION['dashboard_view'] == 'store')? 'active':''; ?>">
                📍 Stores
            </a>
            <a href='?view=stock' class="nav-tab <?php echo ($_SESSION['dashboard_view'] == 'stock')? 'active':''; ?>">
                📦 Stocks
            </a>
        </nav>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="🔍 Search <?php echo ($_SESSION['dashboard_view'] == 'store') ? 'stores' : 'stocks'; ?>...">
        </div>

        <div class="content-area">
            <?php if ($_SESSION['dashboard_view'] == "store"): ?>
                <?php if (empty($stores)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🏪</div>
                        <h3>No stores found</h3>
                        <p>Get started by adding your first store!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($stores as $store): ?>
                        <div class="store-item item-card">
                            <div class="item-info">
                                <div class="item-title"><?php echo htmlspecialchars($store['name']); ?></div>
                                <div class="item-subtitle">📍 <?php echo htmlspecialchars($store['address']); ?></div>
                            </div>
                            <div class="item-actions">
                                <a href="store.php?store_id=<?php echo $store['id']; ?>" class="btn btn-primary">
                                    👁️ View
                                </a>
                                <a href="operations/delete_store.php?store_id=<?php echo $store['id'];?>" class="btn btn-delete">
                                    🗑️ Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php elseif ($_SESSION['dashboard_view'] == "stock"): ?>
                <form action='operations/delete_stock.php' method='post'>
                    <?php if (empty($stocks)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📦</div>
                            <h3>No stocks found</h3>
                            <p>Add your first stock item to get started!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($stocks as $stock): ?>
                            <div class="stock-item item-card <?php echo $delete_stock_enabled ? 'delete-mode' : ''; ?>">
                                <div class="item-info">
                                    <div class="item-title"><?php echo htmlspecialchars($stock['name']); ?></div>
                                    <div class="stock-details">
                                        <div class="stock-detail">
                                            <div class="stock-detail-label">Price</div>
                                            <div class="stock-detail-value">₱<?php echo number_format($stock['price'], 2); ?></div>
                                        </div>
                                        <div class="stock-detail">
                                            <div class="stock-detail-label">Source</div>
                                            <div class="stock-detail-value"><?php echo htmlspecialchars($stock['source']); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <a href="operations/edit_stock.php?stock_id=<?php echo $stock['id'];?>" class="btn btn-edit">
                                        ✏️ Edit
                                    </a>
                                    <?php if ($delete_stock_enabled): ?>
                                        <input type="checkbox" name="deleted_stocks[]" value="<?php echo $stock['id'];?>" class="checkbox-item">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>

        <aside class="sidebar">
            <?php if ($_SESSION['dashboard_view'] == "store"): ?>
                <a href='operations/add_store.php' class="btn">
                    ➕ Add Store
                </a>
            <?php else: ?>
                <a href='operations/add_stock.php' class="btn">
                    ➕ Add Stock
                </a>
                <?php if ($delete_stock_enabled): ?>
                    <input type='submit' value='✅ Confirm Delete' class="btn btn-delete" form="delete-form">
                    <a href='?view=stock' class="btn">
                        ❌ Cancel
                    </a>
                <?php else: ?>
                    <a href='?view=stock&operation=delete_stock' class="btn">
                        🗑️ Delete Stocks
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="../logout.php" class="btn">
                🚪 Log Out
            </a>
        </aside>
    </div>

</body>
</html>