<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once 'includes/config.php';

$page_title = "Checkout";

// Redirect to cart if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: cart.php");
    exit;
}

// Calculate cart totals
$cart_total = 0;
$item_count = 0;
$shipping_fee = 10000; // Fixed shipping fee

foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $item_count += $item['quantity'];
}

$order_total = $cart_total + $shipping_fee;

// Process checkout form submission
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form fields
    $required_fields = [
        'full_name' => 'Full Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'address' => 'Address',
        'city' => 'City',
        'payment_method' => 'Payment Method'
    ];

    foreach ($required_fields as $field => $label) {
        if (empty($_POST[$field])) {
            $errors[$field] = $label . ' is required';
        }
    }

    // Validate email format
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    // If no errors, process the order
    if (empty($errors)) {
        // Generate order ID
        $order_id = 'ORD-' . time();

        // Store order details in session for confirmation page
        $_SESSION['order_details'] = [
            'order_id' => $order_id,
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'payment_method' => $_POST['payment_method'],
            'items' => $_SESSION['cart'],
            'subtotal' => $cart_total,
            'shipping' => $shipping_fee,
            'total' => $order_total,
            'order_date' => date('Y-m-d H:i:s')
        ];

        // Save order to database
        $sql = "INSERT INTO orders (order_id, full_name, email, phone, address, city, payment_method, subtotal, shipping, total) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            'sssssssddd',
            $order_id,
            $_POST['full_name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['city'],
            $_POST['payment_method'],
            $cart_total,
            $shipping_fee,
            $order_total
        );

        $order_saved = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Save order items
        if ($order_saved) {
            foreach ($_SESSION['cart'] as $item) {
                $sql = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) 
                        VALUES (?, ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param(
                    $stmt,
                    'sisdi',
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity']
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // Clear the cart
            $_SESSION['cart'] = [];

            // Redirect to order confirmation page
            header("Location: order_confirmation.php");
            exit;
        } else {
            // If order couldn't be saved, show error
            $errors['db'] = "Could not process your order. Please try again.";
        }
    }
}

// Now include the header after all potential redirects
require_once 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-gray-800 py-6">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Checkout</h1>
        <p class="text-gray-300 mt-2">Complete your purchase</p>
    </div>
</div>

<!-- Checkout Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Checkout Form -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b">
                        <h2 class="text-xl font-bold">Shipping Information</h2>
                    </div>

                    <form method="POST" action="" class="p-6 space-y-4">
                        <!-- Personal Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="full_name" class="block text-gray-700 font-medium mb-2">Full Name *</label>
                                <input type="text" id="full_name" name="full_name"
                                    value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($errors['full_name']) ? 'border-red-500' : ''; ?>">
                                <?php if (isset($errors['full_name'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?php echo $errors['full_name']; ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email *</label>
                                <input type="email" id="email" name="email"
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($errors['email']) ? 'border-red-500' : ''; ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?php echo $errors['email']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number *</label>
                            <input type="tel" id="phone" name="phone"
                                value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($errors['phone']) ? 'border-red-500' : ''; ?>">
                            <?php if (isset($errors['phone'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo $errors['phone']; ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Address Information -->
                        <div>
                            <label for="address" class="block text-gray-700 font-medium mb-2">Address *</label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($errors['address']) ? 'border-red-500' : ''; ?>"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo $errors['address']; ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="city" class="block text-gray-700 font-medium mb-2">City *</label>
                            <input type="text" id="city" name="city"
                                value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($errors['city']) ? 'border-red-500' : ''; ?>">
                            <?php if (isset($errors['city'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo $errors['city']; ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Payment Method -->
                        <div class="mt-6">
                            <h3 class="text-lg font-bold mb-4">Payment Method</h3>

                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input type="radio" id="cod" name="payment_method" value="cod"
                                        <?php echo (!isset($_POST['payment_method']) || $_POST['payment_method'] == 'cod') ? 'checked' : ''; ?>
                                        class="mr-2">
                                    <label for="cod" class="flex items-center">
                                        <span class="ml-2">Cash on Delivery (COD)</span>
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer"
                                        <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank_transfer') ? 'checked' : ''; ?>
                                        class="mr-2">
                                    <label for="bank_transfer" class="flex items-center">
                                        <span class="ml-2">Bank Transfer</span>
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="radio" id="credit_card" name="payment_method" value="credit_card"
                                        <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card') ? 'checked' : ''; ?>
                                        class="mr-2">
                                    <label for="credit_card" class="flex items-center">
                                        <span class="ml-2">Credit Card</span>
                                    </label>
                                </div>
                            </div>

                            <?php if (isset($errors['payment_method'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo $errors['payment_method']; ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                                Complete Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-20">
                    <div class="p-4 border-b">
                        <h2 class="text-xl font-bold">Order Summary</h2>
                    </div>

                    <div class="p-4">
                        <div class="max-h-64 overflow-y-auto mb-4">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <div class="flex items-center gap-3 mb-3 pb-3 border-b">
                                    <div class="w-16 h-16 flex-shrink-0">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                                            class="w-full h-full object-cover rounded">
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="text-sm font-medium"><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <p class="text-xs text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold">
                                            <?php echo number_format($item['price'] * $item['quantity'], 0); ?>đ</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="space-y-3 pt-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal (<?php echo $item_count; ?> items)</span>
                                <span class="font-medium"><?php echo number_format($cart_total, 0); ?>đ</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium"><?php echo number_format($shipping_fee, 0); ?>đ</span>
                            </div>

                            <div class="border-t pt-3 mt-3 flex justify-between">
                                <span class="text-lg font-bold">Total</span>
                                <span
                                    class="text-lg font-bold text-red-600"><?php echo number_format($order_total, 0); ?>đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <h3 class="font-bold text-blue-800 mb-2">Secure Checkout</h3>
                    <p class="text-sm text-blue-700">Your information is protected using secure encryption technology.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>