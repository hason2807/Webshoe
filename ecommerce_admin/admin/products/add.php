<?php
session_start();
// Check login
require_once '../../config/database.php';

// Define the sanitize function if not already defined
if (!function_exists('sanitize')) {
    function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

$errors = [];
$success = false;

// Handle form submission
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
    
    // Handle image from URL
    $image_url = '';
    $image_link = isset($_POST['image_link']) ? sanitize($_POST['image_link']) : '';
    
    // Process image URL
    if (!empty($image_link)) {
        if (filter_var($image_link, FILTER_VALIDATE_URL)) {
            // Check if the URL is an image
            $headers = @get_headers($image_link, 1);
            if ($headers !== false && isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'image/') !== false) {
                // Use the direct URL
                $image_url = $image_link;
            } else {
                $errors[] = "The URL is not a valid image or cannot be accessed";
            }
        } else {
            $errors[] = "Invalid image URL";
        }
    } else {
        $errors[] = "Please enter an image URL";
    }
    
    // Insert product into database if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, category, color, price, image_url) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$name, $category, $color, $price, $image_url]);
            
            if ($result) {
                $success = true;
                // Redirect after successful addition
                header("Location: index.php?success=1");
                exit;
            } else {
                $errors[] = "Unable to add product. Please try again.";
            }
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Product Management</h1>
                <div class="user-info">
                    <span>Hello, <?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin'; ?></span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </header>
            
            <div class="content-header">
                <h2>Add New Product</h2>
                <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">Product added successfully!</div>
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
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="name">Product Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Product Category <span class="required">*</span></label>
                        <input type="text" id="category" name="category" value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="color">Color <span class="required">*</span></label>
                        <input type="text" id="color" name="color" value="<?php echo isset($_POST['color']) ? htmlspecialchars($_POST['color']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price (VND) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" min="0" step="1000" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_link">Product Image (URL) <span class="required">*</span></label>
                        <input type="url" id="image_link" name="image_link" placeholder="Enter image URL" value="<?php echo isset($_POST['image_link']) ? htmlspecialchars($_POST['image_link']) : ''; ?>" required>
                        <div class="image-preview" id="imagePreview"></div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Preview image from URL
        document.getElementById('image_link').addEventListener('input', function() {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (this.value) {
                const img = document.createElement('img');
                img.src = this.value;
                img.classList.add('preview-img');
                
                // Show an error if the link is invalid
                img.onerror = function() {
                    preview.innerHTML = '<p style="color: red;">Cannot load image from this URL</p>';
                };
                
                preview.appendChild(img);
            }
        });
        
        // Automatically preview if a value exists on page load
        window.addEventListener('DOMContentLoaded', function() {
            const imageLinkInput = document.getElementById('image_link');
            if (imageLinkInput.value) {
                const event = new Event('input');
                imageLinkInput.dispatchEvent(event);
            }
        });
    </script>
</body>
</html>