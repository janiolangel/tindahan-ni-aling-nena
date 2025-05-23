<?php
session_start();
// Database connection configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "tindahan_system";

// Mock user data (in a real app, this would come from database)
$current_user = $_SESSION['username'];
$current_date = date("F j, Y");
$stores = [
    [
        "id" => 1,
        "name" => "Store 1",
        "location" => "Iloilo City, Iloilo, Philippines",
        "products" => [
            ["name" => "Product 1", "price" => 150, "stock" => 25, "producer" => "Producer A"],
            ["name" => "Product 2", "price" => 200, "stock" => 15, "producer" => "Producer B"],
            ["name" => "Product 3", "price" => 100, "stock" => 30, "producer" => "Producer A"],
            ["name" => "Product 4", "price" => 250, "stock" => 10, "producer" => "Producer C"]
        ]
    ]
];

// Get current store (default to first store if none selected)
$current_store_id = isset($_GET['store_id']) ? intval($_GET['store_id']) : 1;
$current_store = null;

foreach ($stores as $store) {
    if ($store['id'] == $current_store_id) {
        $current_store = $store;
        break;
    }
}

// If store not found, redirect to first store
if ($current_store === null && !empty($stores)) {
    $current_store = $stores[0];
}

// Handle different views (products, invoices, ledger, customers, producers)
$view = isset($_GET['view']) ? $_GET['view'] : 'products';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lindahan ni <?php echo htmlspecialchars($current_user); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        header {
            background-color: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            height: 40px;
            margin-right: 15px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-name {
            font-size: 18px;
            color: #5e1a12;
            font-weight: bold;
        }
        
        .button {
            padding: 8px 15px;
            background-color: white;
            border: 1px solid #5e1a12;
            color: #5e1a12;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .main-content {
            display: flex;
            min-height: calc(100vh - 70px);
        }
        
        .sidebar {
            width: 200px;
            border-right: 1px solid #ddd;
            padding: 20px;
        }
        
        .store-list {
            margin-bottom: 30px;
        }
        
        .store-item {
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #ddd;
            text-align: center;
            background-color: <?php echo ($current_store_id == 1) ? '#f8f8f8' : 'white'; ?>;
        }
        
        .store-item.active {
            border: 1px solid #5e1a12;
            background-color: #f8f8f8;
        }
        
        .add-store {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            cursor: pointer;
        }
        
        .content {
            flex: 1;
            padding: 20px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .store-title {
            font-size: 24px;
            color: #5e1a12;
            margin: 0;
        }
        
        .store-location {
            color: #666;
            margin-top: 5px;
        }
        
        .nav-tabs {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }
        
        .nav-item {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }
        
        .nav-item.active {
            border: 1px solid #5e1a12;
            background-color: #f8f8f8;
        }
        
        .search-container {
            margin-bottom: 20px;
        }
        
        .search-input {
            padding: 8px;
            width: 250px;
            border: 1px solid #ddd;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #f8f8f8;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            color: #5e1a12;
            cursor: pointer;
        }
        
        th:after {
            content: "▼";
            font-size: 12px;
            margin-left: 5px;
            opacity: 0.5;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .add-product {
            display: block;
            text-align: center;
            padding: 10px;
            background-color: #f8f8f8;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            text-decoration: none;
            color: #333;
        }
        
        .date-display {
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-container">
                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIGZpbGw9IiM1ZTFhMTIiLz48dGV4dCB4PSIyMCIgeT0iMjUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0id2hpdGUiPkxOPC90ZXh0Pjwvc3ZnPg==" alt="Logo" class="logo">
                <div>LINDAHAN NI<br>ALING NENA</div>
            </div>
            <div class="user-info">
                <div>Current User: <span class="user-name"><?php echo htmlspecialchars($current_user); ?></span></div>
                <a href="#" class="button">Edit Mode</a>
                <a href="logout.php" class="button">Log Out</a>
            </div>
        </header>
        
        <div class="main-content">
            <div class="sidebar">
                <h2>Stores</h2>
                <div class="store-list">
                    <?php foreach ($stores as $store): ?>
                    <div class="store-item <?php echo ($store['id'] == $current_store_id) ? 'active' : ''; ?>">
                        <a href="?store_id=<?php echo $store['id']; ?>" style="text-decoration: none; color: inherit;">
                            <?php echo htmlspecialchars($store['name']); ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <div class="add-store">
                        Add Store +
                    </div>
                </div>
            </div>
            
            <div class="content">
                <div class="content-header">
                    <div>
                        <h2 class="store-title"><?php echo htmlspecialchars($current_store['name']); ?></h2>
                        <div class="store-location"><?php echo htmlspecialchars($current_store['location']); ?></div>
                    </div>
                    <div class="date-display">
                        Date: <?php echo $current_date; ?>
                    </div>
                </div>
                
                <div class="nav-tabs">
                    <a href="?store_id=<?php echo $current_store_id; ?>&view=products" class="nav-item <?php echo ($view == 'products') ? 'active' : ''; ?>">Products</a>
                    <a href="?store_id=<?php echo $current_store_id; ?>&view=invoices" class="nav-item <?php echo ($view == 'invoices') ? 'active' : ''; ?>">Invoices</a>
                    <a href="?store_id=<?php echo $current_store_id; ?>&view=ledger" class="nav-item <?php echo ($view == 'ledger') ? 'active' : ''; ?>">Ledger</a>
                    <a href="?store_id=<?php echo $current_store_id; ?>&view=customers" class="nav-item <?php echo ($view == 'customers') ? 'active' : ''; ?>">Customers</a>
                    <a href="?store_id=<?php echo $current_store_id; ?>&view=producers" class="nav-item <?php echo ($view == 'producers') ? 'active' : ''; ?>">Producers</a>
                </div>
            </div>
            
            <div class="content" style="border-left: 1px solid #ddd;">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search">
                </div>
                
                <?php if ($view == 'products'): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Producer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($current_store['products'] as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>₱<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td><?php echo htmlspecialchars($product['producer']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="#" class="add-product">Add Product +</a>
                <?php elseif ($view == 'invoices'): ?>
                <div>Invoices content would go here</div>
                <?php elseif ($view == 'ledger'): ?>
                <div>Ledger content would go here</div>
                <?php elseif ($view == 'customers'): ?>
                <div>Customers content would go here</div>
                <?php elseif ($view == 'producers'): ?>
                <div>Producers content would go here</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Simple JavaScript to handle sorting
        document.addEventListener('DOMContentLoaded', function() {
            const tableHeaders = document.querySelectorAll('th');
            
            tableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const table = this.closest('table');
                    const index = Array.from(this.parentNode.children).indexOf(this);
                    const rows = Array.from(table.querySelectorAll('tbody tr'));
                    const direction = this.classList.contains('asc') ? -1 : 1;
                    
                    // Remove classes from all headers
                    tableHeaders.forEach(h => h.classList.remove('asc', 'desc'));
                    
                    // Add class to current header
                    this.classList.add(direction === 1 ? 'asc' : 'desc');
                    
                    // Sort rows
                    rows.sort((a, b) => {
                        const aValue = a.children[index].textContent;
                        const bValue = b.children[index].textContent;
                        
                        // Check if values are numbers
                        const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
                        const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
                        
                        if (!isNaN(aNum) && !isNaN(bNum)) {
                            return (aNum - bNum) * direction;
                        }
                        
                        return aValue.localeCompare(bValue) * direction;
                    });
                    
                    // Append sorted rows
                    const tbody = table.querySelector('tbody');
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }
        });
    </script>
</body>
</html>