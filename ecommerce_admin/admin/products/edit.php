<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <?php
    // Start session, but no login required
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
    $product = [];

    // Check product ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: index.php');
        exit;
    }

    $product_id = (int)$_GET['id'];

    // Get product information
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            // If product not found
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $name = sanitize($_POST['name']);
        $category = sanitize($_POST['category']);
        $color = sanitize($_POST['color']);
        $price = (float)sanitize($_POST['price']);
        
        // Validate data
        if (empty($name)) {
            $errors[] = "Product name cannot be empty";
        }
        
        if (empty($category)) {
            $errors[] = "Product category cannot be empty";
        }
        
        if (empty($color)) {
            $errors[] = "Color cannot be empty";
        }
        
        if ($price <= 0) {
            $errors[] = "Price must be greater than 0";
        }
        
        // Handle new image upload (if any)
        $image_url = isset($product['image_url']) ? $product['image_url'] : ''; // Keep the same if no new image
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../uploads/products/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Check file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($image_file_type, $allowed_types)) {
                $errors[] = "Only JPG, JPEG, PNG, GIF or WEBP files are allowed";
            } else {
                // Move temporary file to server
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_url = 'uploads/products/' . $file_name;
                    
                    // Delete old image if it exists and isn't the default image
                    if (!empty($product['image_url']) && file_exists('../../' . $product['image_url'])) {
                        @unlink('../../' . $product['image_url']);
                    }
                } else {
                    $errors[] = "Failed to upload image";
                }
            }
        }
        
        // Update product in database if no errors
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, color = ?, price = ?, image_url = ? WHERE id = ?");
                $result = $stmt->execute([$name, $category, $color, $price, $image_url, $product_id]);
                
                if ($result) {
                    $success = true;
                    // Refresh product information after update
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch();
                } else {
                    $errors[] = "Failed to update product. Please try again.";
                }
            } catch (PDOException $e) {
                $errors[] = "Error: " . $e->getMessage();
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
                <h1>Product Management</h1>
                <div class="user-info">
                    <span>Welcome, Admin</span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </header>
            
            <div class="content-header">
                <h2>Edit Product</h2>
                <a href="index.php" class="btn btn-secondary">Back to list</a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">Product updated successfully!</div>
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
            
            <div class="form-container">
                <form action="edit.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Product Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo isset($product['name']) ? htmlspecialchars($product['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Product Category <span class="required">*</span></label>
                        <input type="text" id="category" name="category" value="<?php echo isset($product['category']) ? htmlspecialchars($product['category']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="color">Color <span class="required">*</span></label>
                        <input type="text" id="color" name="color" value="<?php echo isset($product['color']) ? htmlspecialchars($product['color']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price (VND) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" min="0" step="1000" value="<?php echo isset($product['price']) ? htmlspecialchars($product['price']) : 0; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <p class="form-hint">Leave blank if you don't want to change the image</p>
                        
                        <?php if (isset($product['image_url']) && !empty($product['image_url'])): ?>
                        <div class="current-image">
                            <p>Current Image:</p>
                            <img src="../../<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo isset($product['name']) ? htmlspecialchars($product['name']) : 'Product'; ?>" class="product-image">
                        </div>
                        <?php endif; ?>
                        
                        <div class="image-preview" id="imagePreview"></div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Preview image before upload
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('preview-img');
                    preview.appendChild(img);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>