<?php

require_once '../../config/database.php';

// Check user ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$user_id = (int)$_GET['id'];

// Delete user
try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $result = $stmt->execute([$user_id]);
    
    if ($result) {
        // Redirect with success message
        header('Location: index.php?success=deleted');
    } else {
        // Redirect with error message
        header('Location: index.php?error=failed');
    }
} catch (PDOException $e) {
    // Redirect with error message
    header('Location: index.php?error=' . urlencode($e->getMessage()));
}
exit;
?>
