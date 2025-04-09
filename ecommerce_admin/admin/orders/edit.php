<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <?php
    session_start();

    require_once '../../config/database.php';

    // Define the sanitize function if not already defined
    if (!function_exists('sanitize')) {
        function sanitize($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
    }

    $errors = [];
    $success = false;
    $order = [];
    $order_items = [];

    // Check for order ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: index.php');
        exit;
    }

    $order_id = sanitize($_GET['id']);

    // Fetch order details
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            header('Location: index.php');
            exit;
        }
        
        // Fetch order items
        $stmt = $pdo->prepare("SELECT oi.*, p.image_url FROM order_items oi 
                            LEFT JOIN products p ON oi.product_id = p.id
                            WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = sanitize($_POST['status']);
        
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $result = $stmt->execute([$status, $order_id]);
            
            if ($result) {
                $success = true;
                // Refresh order details after update
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
                $stmt->execute([$order_id]);
                $order = $stmt->fetch();
            } else {
                $errors[] = "Unable to update the order. Please try again.";
            }
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
    ?>

    <div class="admin-container">
        <?php 
        $sidebar_path = '../includes/sidebar.php';
        if (file_exists($sidebar_path)) {
            include $sidebar_path;
        } else {
            echo '<div class="sidebar">Menu not available</div>';
        }
        ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Order Management</h1>
                <div class="user-info">
                    <span>Hello, <?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin'; ?></span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </header>
            
            <div class="content-header">
                <h2>Order Details #<?php echo htmlspecialchars($order_id); ?></h2>
                <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">Order updated successfully!</div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="order-details">
                <div class="order-info-container">
                    <div class="order-card">
                        <h3>Order Information</h3>
                        <div class="info-group">
                            <span class="label">Order ID:</span>
                            <span class="value"><?php echo htmlspecialchars($order['order_id']); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Order Date:</span>
                            <span class="value"><?php echo isset($order['order_date']) ? date('d/m/Y H:i', strtotime($order['order_date'])) : 'N/A'; ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Subtotal:</span>
                            <span class="value"><?php echo isset($order['subtotal']) ? number_format($order['subtotal'], 0, ',', '.') : 0; ?>đ</span>
                        </div>
                        <div class="info-group">
                            <span class="label">Shipping Fee:</span>
                            <span class="value"><?php echo isset($order['shipping']) ? number_format($order['shipping'], 0, ',', '.') : 0; ?>đ</span>
                        </div>
                        <div class="info-group">
                            <span class="label">Total Payment:</span>
                            <span class="value total-amount"><?php echo isset($order['total']) ? number_format($order['total'], 0, ',', '.') : 0; ?>đ</span>
                        </div>
                        <div class="info-group">
                            <span class="label">Payment Method:</span>
                            <span class="value"><?php echo isset($order['payment_method']) ? htmlspecialchars($order['payment_method']) : 'N/A'; ?></span>
                        </div>
                    </div>
                    
                    <div class="order-card">
                        <h3>Customer Information</h3>
                        <div class="info-group">
                            <span class="label">Full Name:</span>
                            <span class="value"><?php echo isset($order['full_name']) ? htmlspecialchars($order['full_name']) : 'N/A'; ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Email:</span>
                            <span class="value"><?php echo isset($order['email']) ? htmlspecialchars($order['email']) : 'N/A'; ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Phone:</span>
                            <span class="value"><?php echo isset($order['phone']) ? htmlspecialchars($order['phone']) : 'N/A'; ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Address:</span>
                            <span class="value"><?php echo isset($order['address']) ? htmlspecialchars($order['address']) : 'N/A'; ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">City:</span>
                            <span class="value"><?php echo isset($order['city']) ? htmlspecialchars($order['city']) : 'N/A'; ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="order-status-update">
                    <h3>Update Order Status</h3>
                    <form action="edit.php?id=<?php echo htmlspecialchars($order_id); ?>" method="POST">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="pending" <?php echo (isset($order['status']) && $order['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo (isset($order['status']) && $order['status'] === 'processing') ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo (isset($order['status']) && $order['status'] === 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo (isset($order['status']) && $order['status'] === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo (isset($order['status']) && $order['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
                
                <div class="order-items">
                    <h3>Product Details</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($order_items) > 0): ?>
                                    <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="../../<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-thumbnail">
                                            <?php else: ?>
                                                <div class="no-image">No Image</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo isset($item['product_name']) ? htmlspecialchars($item['product_name']) : 'N/A'; ?></td>
                                        <td><?php echo isset($item['price']) ? number_format($item['price'], 0, ',', '.') : 0; ?>đ</td>
                                        <td><?php echo isset($item['quantity']) ? (int)$item['quantity'] : 0; ?></td>
                                        <td><?php echo isset($item['price'], $item['quantity']) ? number_format($item['price'] * $item['quantity'], 0, ',', '.') : 0; ?>đ</td>
                                        <td>
                                            <a href="../order_items/edit.php?id=<?php echo isset($item['id']) ? (int)$item['id'] : ''; ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="../order_items/delete.php?id=<?php echo isset($item['id']) ? (int)$item['id'] : ''; ?>&order_id=<?php echo htmlspecialchars($order_id); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product from the order?')">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No products in the order.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>