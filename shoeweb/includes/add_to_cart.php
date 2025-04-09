<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get product data from POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = isset($_POST['name']) ? $_POST['name'] : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$image = isset($_POST['image']) ? $_POST['image'] : '';
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Validate data
if ($id <= 0 || empty($name) || $price <= 0 || empty($image) || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product data']);
    exit;
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already exists in cart
$product_exists = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $id) {
        // Update quantity
        $item['quantity'] += $quantity;
        $product_exists = true;
        break;
    }
}

// If product doesn't exist in cart, add it
if (!$product_exists) {
    $_SESSION['cart'][] = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'image' => $image,
        'quantity' => $quantity
    ];
}

// Return success response with cart count
echo json_encode([
    'success' => true,
    'message' => 'Product added to cart',
    'cartCount' => count($_SESSION['cart'])
]);
