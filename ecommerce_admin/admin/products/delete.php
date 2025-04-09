<?php
require_once '../../config/database.php';

// Check for product ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product_id = (int)$_GET['id'];

// Delete product
try {
    // Check if the product is being used in any order
    $check_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM order_items WHERE product_id = ?");
    $check_stmt->execute([$product_id]);
    $is_used = $check_stmt->fetch()['count'] > 0;
    
    if ($is_used) {
        // Redirect with an error message
        header('Location: index.php?error=used');
        exit;
    }
    
    // Fetch product image information to delete the file
    $img_stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $img_stmt->execute([$product_id]);
    $product = $img_stmt->fetch();
    
    // Delete product from the database
    $delete_stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $result = $delete_stmt->execute([$product_id]);
    
    if ($result) {
        // Delete the image file if it exists
        if (!empty($product['image_url']) && file_exists('../../' . $product['image_url'])) {
            unlink('../../' . $product['image_url']);
        }
        
        // Redirect with a success message
        header('Location: index.php?success=deleted');
    } else {
        // Redirect with an error message
        header('Location: index.php?error=failed');
    }
} catch (PDOException $e) {
    // Redirect with an error message
    header('Location: index.php?error=' . urlencode($e->getMessage()));
}
exit;
?>