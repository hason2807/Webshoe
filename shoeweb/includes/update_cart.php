<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'cartCount' => 0,
    'cartTotal' => 0,
    'itemCount' => 0
];

// Check if cart exists in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle JSON request for batch updates
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'update' && isset($data['updates'])) {
        foreach ($data['updates'] as $update) {
            $index = isset($update['index']) ? intval($update['index']) : -1;
            $quantity = isset($update['quantity']) ? intval($update['quantity']) : 0;

            if ($index >= 0 && $index < count($_SESSION['cart']) && $quantity > 0) {
                $_SESSION['cart'][$index]['quantity'] = min(99, $quantity);
            }
        }

        $response['success'] = true;
        $response['message'] = 'Cart updated successfully';
    }
}
// Handle form-encoded request for single item actions
else {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'remove' && isset($_POST['index'])) {
        $index = intval($_POST['index']);

        if ($index >= 0 && $index < count($_SESSION['cart'])) {
            // Remove item from cart
            array_splice($_SESSION['cart'], $index, 1);

            $response['success'] = true;
            $response['message'] = 'Item removed from cart';
        }
    }
}

// Calculate updated cart totals
$cart_total = 0;
$item_count = 0;

foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $item_count += $item['quantity'];
}

// Update response with cart data
$response['cartCount'] = count($_SESSION['cart']);
$response['cartTotal'] = $cart_total;
$response['itemCount'] = $item_count;

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
