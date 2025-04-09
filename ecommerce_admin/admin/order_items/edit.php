<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order Item - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <?php
    // Start session
    session_start();

    require_once '../../config/database.php';

    // Define sanitize function if not already defined
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
    $item = [];
    $products = [];

    // Check order item ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: index.php');
        exit;
    }

    $item_id = (int)$_GET['id'];

    // Get order item details
    try {
        $stmt = $pdo->prepare("SELECT oi.*, o.full_name, o.total as order_total 
                              FROM order_items oi
                              JOIN orders o ON oi.order_id = o.order_id
                              WHERE oi.id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();
        
        if (!$item) {
            // If order item not found
            header('Location: index.php');
            exit;
        }
        
        // Get product list for selection
        $stmt = $pdo->prepare("SELECT id, name, price FROM products ORDER BY name ASC");
        $stmt->execute();
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        $errors[] = "Error: " . $e->getMessage();
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $product_id = isset($_POST['product_id']) ? (int)sanitize($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? (int)sanitize($_POST['quantity']) : 0;
        
        // Validate data
        if ($product_id <= 0) {
            $errors[] = "Please select a product";
        }
        
        if ($quantity <= 0) {
            $errors[] = "Quantity must be greater than 0";
        }
        
        // Get new product information
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();
                
                if (!$product) {
                    $errors[] = "Product does not exist";
                } else {
                    $product_name = $product['name'];
                    $price = $product['price'];
                    
                    // Calculate price difference
                    $old_total = isset($item['price'], $item['quantity']) ? $item['price'] * $item['quantity'] : 0;
                    $new_total = $price * $quantity;
                    $price_difference = $new_total - $old_total;
                    
                    // Begin transaction
                    $pdo->beginTransaction();
                    
                    try {
                        // Update order item
                        $update_item_stmt = $pdo->prepare("UPDATE order_items SET 
                                                        product_id = ?, 
                                                        product_name = ?, 
                                                        price = ?, 
                                                        quantity = ? 
                                                        WHERE id = ?");
                        $update_item_stmt->execute([$product_id, $product_name, $price, $quantity, $item_id]);
                        
                        // Update order totals
                        $update_order_stmt = $pdo->prepare("UPDATE orders SET 
                                                        subtotal = subtotal + ?, 
                                                        total = total + ? 
                                                        WHERE order_id = ?");
                        $update_order_stmt->execute([$price_difference, $price_difference, $item['order_id']]);
                        
                        // Commit transaction
                        $pdo->commit();
                        
                        $success = true;
                        
                        // Refresh order item details after update
                        $stmt = $pdo->prepare("SELECT oi.*, o.full_name, o.total as order_total 
                                            FROM order_items oi
                                            JOIN orders o ON oi.order_id = o.order_id
                                            WHERE oi.id = ?");
                        $stmt->execute([$item_id]);
                        $item = $stmt->fetch();
                    } catch (PDOException $e) {
                        // Rollback transaction if error occurs
                        $pdo->rollBack();
                        $errors[] = "Update error: " . $e->getMessage();
                    }
                }
            } catch (PDOException $e) {
                $errors[] = "Product query error: " . $e->getMessage();
            }
        }
    }
    ?>

    <div class="admin-container">
        <?php 
        // Check if sidebar file exists before including
        $sidebar_path = '../includes/sidebar.php';
        if (file_exists($sidebar_path)) {
            include $sidebar_path;
        } else {
            echo '<div class="sidebar">Menu not available</div>';
        }
        ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Order Item Management</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin'; ?></span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </header>
            
            <div class="content-header">
                <h2>Edit Order Item</h2>
                <div>
                    <a href="index.php" class="btn btn-secondary">Back to list</a>
                    <?php if (isset($item['order_id'])): ?>
                    <a href="../orders/edit.php?id=<?php echo htmlspecialchars($item['order_id']); ?>" class="btn btn-primary">View Order</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">Order item updated successfully!</div>
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
            
            <?php if (isset($item) && !empty($item)): ?>
            <div class="order-item-details">
                <div class="order-info-card">
                    <h3>Order Information</h3>
                    <div class="info-group">
                        <span class="label">Order ID:</span>
                        <span class="value"><?php echo isset($item['order_id']) ? htmlspecialchars($item['order_id']) : 'N/A'; ?></span>
                    </div>
                    <div class="info-group">
                        <span class="label">Customer:</span>
                        <span class="value"><?php echo isset($item['full_name']) ? htmlspecialchars($item['full_name']) : 'N/A'; ?></span>
                    </div>
                    <div class="info-group">
                        <span class="label">Order Total:</span>
                        <span class="value"><?php echo isset($item['order_total']) ? number_format($item['order_total'], 0, ',', '.') : 0; ?>₫</span>
                    </div>
                </div>
                
                <div class="form-container">
                    <form action="edit.php?id=<?php echo $item_id; ?>" method="POST">
                        <div class="form-group">
                            <label for="product_id">Product <span class="required">*</span></label>
                            <select id="product_id" name="product_id" class="form-control" required>
                                <option value="">-- Select product --</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo htmlspecialchars($product['id']); ?>" <?php echo (isset($item['product_id']) && $product['id'] == $item['product_id']) ? 'selected' : ''; ?> data-price="<?php echo htmlspecialchars($product['price']); ?>">
                                        <?php echo htmlspecialchars($product['name']); ?> - <?php echo number_format($product['price'], 0, ',', '.'); ?>₫
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity">Quantity <span class="required">*</span></label>
                            <input type="number" id="quantity" name="quantity" min="1" value="<?php echo isset($item['quantity']) ? (int)$item['quantity'] : 1; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Product Price:</label>
                            <div id="productPrice" class="input-readonly"><?php echo isset($item['price']) ? number_format($item['price'], 0, ',', '.') : 0; ?>₫</div>
                        </div>
                        
                        <div class="form-group">
                            <label>Subtotal:</label>
                            <div id="totalPrice" class="input-readonly total-amount"><?php echo isset($item['price'], $item['quantity']) ? number_format($item['price'] * $item['quantity'], 0, ',', '.') : 0; ?>₫</div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-error">
                <p>Order item not found.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Update price and subtotal when product or quantity changes
        const productSelect = document.getElementById('product_id');
        const quantityInput = document.getElementById('quantity');
        const productPriceEl = document.getElementById('productPrice');
        const totalPriceEl = document.getElementById('totalPrice');
        
        function updatePrices() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            let price = 0;
            
            if (selectedOption && selectedOption.value) {
                price = parseFloat(selectedOption.dataset.price) || 0;
            }
            
            const quantity = parseInt(quantityInput.value) || 0;
            const totalPrice = price * quantity;
            
            // Format numbers with Vietnamese locale (keeping ₫ as currency)
            productPriceEl.textContent = new Intl.NumberFormat('vi-VN').format(price) + '₫';
            totalPriceEl.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + '₫';
        }
        
        productSelect.addEventListener('change', updatePrices);
        quantityInput.addEventListener('input', updatePrices);
        
        // Initialize prices on page load
        document.addEventListener('DOMContentLoaded', updatePrices);
    </script>
</body>
</html>