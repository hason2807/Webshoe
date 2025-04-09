<?php
require_once '../../config/database.php';

// Check order item ID
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$item_id = (int)$_GET['id'];
$order_id = sanitize($_GET['order_id']);

// Delete order item
try {
    // Get order item details before deletion to update order total
    $stmt = $pdo->prepare("SELECT price, quantity FROM order_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();
    
    if (!$item) {
        // If order item not found
        header('Location: ../orders/edit.php?id=' . urlencode($order_id));
        exit;
    }
    
    // Calculate amount to subtract
    $amount_to_subtract = $item['price'] * $item['quantity'];
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Delete order item
    $delete_stmt = $pdo->prepare("DELETE FROM order_items WHERE id = ?");
    $delete_stmt->execute([$item_id]);
    
    // Update order totals
    $update_stmt = $pdo->prepare("UPDATE orders SET 
                                subtotal = subtotal - ?, 
                                total = total - ? 
                                WHERE order_id = ?");
    $update_stmt->execute([$amount_to_subtract, $amount_to_subtract, $order_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Redirect with success message
    header('Location: ../orders/edit.php?id=' . urlencode($order_id) . '&success=item_deleted');
} catch (PDOException $e) {
    // Rollback transaction if error occurs
    $pdo->rollBack();
    
    // Redirect with error message
    header('Location: ../orders/edit.php?id=' . urlencode($order_id) . '&error=' . urlencode($e->getMessage()));
}
exit;
?>