<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <?php
    session_start();
    require_once '../../config/database.php';

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
    $where_condition = '';
    $params = [];

    if (!empty($status_filter)) {
        $where_condition = "WHERE status = ?";
        $params[] = $status_filter;
    }

    $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

    if (!empty($search)) {
        if (empty($where_condition)) {
            $where_condition = "WHERE order_id LIKE ? OR full_name LIKE ? OR email LIKE ? OR phone LIKE ?";
        } else {
            $where_condition .= " AND (order_id LIKE ? OR full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
        }
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    }

    try {
        $count_sql = "SELECT COUNT(*) as total FROM orders $where_condition";
        $stmt = $pdo->prepare($count_sql);
        $stmt->execute($params);
        $total_orders = $stmt->fetch()['total'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $total_pages = ceil($total_orders / $limit);

    try {
        $sql = "SELECT * FROM orders $where_condition ORDER BY order_date DESC LIMIT $limit OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Order Management</h1>
                <div class="user-info">
                    <span>Hello, <?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin'; ?></span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </header>
            
            <div class="content-header">
                <h2>Order List</h2>
            </div>
            
            <div class="filter-container">
                <div class="status-filter">
                    <a href="index.php" class="btn btn-sm <?php echo empty($status_filter) ? 'btn-primary' : 'btn-outline'; ?>">All</a>
                    <a href="index.php?status=pending" class="btn btn-sm <?php echo $status_filter === 'pending' ? 'btn-primary' : 'btn-outline'; ?>">Pending</a>
                    <a href="index.php?status=processing" class="btn btn-sm <?php echo $status_filter === 'processing' ? 'btn-primary' : 'btn-outline'; ?>">Processing</a>
                    <a href="index.php?status=shipped" class="btn btn-sm <?php echo $status_filter === 'shipped' ? 'btn-primary' : 'btn-outline'; ?>">Shipped</a>
                    <a href="index.php?status=delivered" class="btn btn-sm <?php echo $status_filter === 'delivered' ? 'btn-primary' : 'btn-outline'; ?>">Delivered</a>
                    <a href="index.php?status=cancelled" class="btn btn-sm <?php echo $status_filter === 'cancelled' ? 'btn-primary' : 'btn-outline'; ?>">Cancelled</a>
                </div>
                
                <form action="" method="GET" class="search-form">
                    <?php if (!empty($status_filter)): ?>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                    <?php endif; ?>
                    <input type="text" name="search" placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo $order['full_name']; ?></td>
                                <td>
                                    <div><?php echo $order['email']; ?></div>
                                    <div><?php echo $order['phone']; ?></div>
                                </td>
                                <td><?php echo number_format($order['total'], 0, ',', '.'); ?>đ</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php 
                                        switch($order['status']) {
                                            case 'pending': echo 'Pending'; break;
                                            case 'processing': echo 'Processing'; break;
                                            case 'shipped': echo 'Shipped'; break;
                                            case 'delivered': echo 'Delivered'; break;
                                            case 'cancelled': echo 'Cancelled'; break;
                                            default: echo $order['status'];
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">Details</a>
                                    <a href="delete.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
