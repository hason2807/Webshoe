<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Items Management - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <?php
    // Start session at the beginning
    session_start();
    require_once '../../config/database.php';

    // Pagination handling
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 15; // Items per page
    $offset = ($page - 1) * $limit;

    // Order ID filter
    $order_id_filter = isset($_GET['order_id']) ? sanitize($_GET['order_id']) : '';
    $where_condition = '';
    $params = [];

    if (!empty($order_id_filter)) {
        $where_condition = "WHERE oi.order_id = ?";
        $params[] = $order_id_filter;
    }

    // Get total count of order items
    try {
        $count_sql = "SELECT COUNT(*) as total FROM order_items oi $where_condition";
        $stmt = $pdo->prepare($count_sql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $total_items = $stmt->fetch()['total'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Calculate total pages
    $total_pages = ceil($total_items / $limit);

    // Get order items list
    try {
        $sql = "SELECT oi.*, o.full_name, o.order_date, p.image_url 
                FROM order_items oi
                LEFT JOIN orders o ON oi.order_id = o.order_id
                LEFT JOIN products p ON oi.product_id = p.id
                $where_condition
                ORDER BY o.order_date DESC
                LIMIT $limit OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $order_items = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Order Items Management</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin'; ?></span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </header>
            
            <div class="content-header">
                <h2>Order Items List</h2>
            </div>
            
            <div class="filter-container">
                <form action="" method="GET" class="search-form">
                    <input type="text" name="order_id" placeholder="Search by order ID..." value="<?php echo htmlspecialchars($order_id_filter); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if (!empty($order_id_filter)): ?>
                        <a href="index.php" class="btn btn-outline">Clear filter</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($order_items) > 0): ?>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td>
                                    <a href="../orders/edit.php?id=<?php echo $item['order_id']; ?>" class="order-id-link">
                                        <?php echo $item['order_id']; ?>
                                    </a>
                                </td>
                                <td><?php echo $item['full_name']; ?></td>
                                <td>
                                    <div class="product-info">
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="../../<?php echo $item['image_url']; ?>" alt="<?php echo $item['product_name']; ?>" class="product-thumbnail">
                                        <?php endif; ?>
                                        <span><?php echo $item['product_name']; ?></span>
                                    </div>
                                </td>
                                <td><?php echo number_format($item['price'], 0, ',', '.'); ?>₫</td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>₫</td>
                                <td><?php echo date('m/d/Y', strtotime($item['order_date'])); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="delete.php?id=<?php echo $item['id']; ?>&order_id=<?php echo $item['order_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No order items found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($order_id_filter) ? '&order_id=' . urlencode($order_id_filter) : ''; ?>" class="page-link">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($order_id_filter) ? '&order_id=' . urlencode($order_id_filter) : ''; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($order_id_filter) ? '&order_id=' . urlencode($order_id_filter) : ''; ?>" class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>