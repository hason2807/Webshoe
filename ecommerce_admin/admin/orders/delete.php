<?php

require_once '../../config/database.php';

// Check for order ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = sanitize($_GET['id']);

// Delete order and its items
try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Delete order items
    $delete_items_stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
    $delete_items_stmt->execute([$order_id]);
    
    // Delete the order
    $delete_order_stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
    $result = $delete_order_stmt->execute([$order_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Redirect with success message
    header('Location: index.php?success=deleted');
} catch (PDOException $e) {
    // Rollback transaction if an error occurs
    $pdo->rollBack();
    
    // Redirect with error message
    header('Location: index.php?error=' . urlencode($e->getMessage()));
}
exit;
?>