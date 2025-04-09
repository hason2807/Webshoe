<?php
require_once '../../config/database.php';

// Handle pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of products per page
$offset = ($page - 1) * $limit;

// Define sanitize function if not already defined
if (!function_exists('sanitize')) {
    function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

// Handle search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$search_condition = '';
$params = [];

if (!empty($search)) {
    $search_condition = "WHERE name LIKE ? OR category LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get total number of products
try {
    $count_sql = "SELECT COUNT(*) as total FROM products $search_condition";
    $stmt = $pdo->prepare($count_sql);
    if (!empty($search)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    $total_products = $stmt->fetch()['total'];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Calculate total pages
$total_pages = ceil($total_products / $limit);

// Get paginated product list
try {
    $sql = "SELECT * FROM products $search_condition ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    if (!empty($search)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Set default username
$admin_username = "Administrator";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Product Management</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo $admin_username; ?></span>
                    <a href="../index.php" class="btn btn-sm btn-primary">Control Panel</a>
                </div>
            </header>
            
            <div class="content-header">
                <h2>Product List</h2>
                <a href="add.php" class="btn btn-primary">Add New Product</a>
            </div>
            
            <div class="filter-container">
                <form action="" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Color</th>
                            <th>Price</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="product-thumbnail">
                                </td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category']; ?></td>
                                <td><?php echo $product['color']; ?></td>
                                <td><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</td>
                                <td><?php echo date('m/d/Y', strtotime($product['created_at'])); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>