<?php
$page_title = "Order Confirmation";
require_once 'includes/header.php';

// Redirect to home if no order details exist
if (!isset($_SESSION['order_details'])) {
    header("Location: index.php");
    exit;
}

// Get order details from session
$order = $_SESSION['order_details'];
?>

<!-- Page Header -->
<div class="bg-gray-800 py-6">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Order Confirmation</h1>
        <p class="text-gray-300 mt-2">Thank you for your purchase!</p>
    </div>
</div>

<!-- Confirmation Section -->
<section class="py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Success Message -->
            <div class="bg-green-50 p-6 border-b border-green-100">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-2 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-green-800">Order Placed Successfully!</h2>
                        <p class="text-green-700 mt-1">Your order has been received and is being processed.</p>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between mb-6 pb-6 border-b">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-gray-500 text-sm font-medium">ORDER NUMBER</h3>
                        <p class="text-lg font-bold"><?php echo htmlspecialchars($order['order_id']); ?></p>
                    </div>
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-gray-500 text-sm font-medium">DATE</h3>
                        <p class="text-lg font-bold"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                    </div>
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-gray-500 text-sm font-medium">TOTAL</h3>
                        <p class="text-lg font-bold text-red-600"><?php echo number_format($order['total'], 0); ?>đ</p>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium">PAYMENT METHOD</h3>
                        <p class="text-lg font-bold">
                            <?php
                            $payment_methods = [
                                'cod' => 'Cash on Delivery',
                                'bank_transfer' => 'Bank Transfer',
                                'credit_card' => 'Credit Card'
                            ];
                            echo isset($payment_methods[$order['payment_method']]) ?
                                $payment_methods[$order['payment_method']] :
                                ucfirst($order['payment_method']);
                            ?>
                        </p>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b">
                    <div>
                        <h3 class="font-bold text-lg mb-3">Shipping Information</h3>
                        <p class="mb-1"><?php echo htmlspecialchars($order['full_name']); ?></p>
                        <p class="mb-1"><?php echo htmlspecialchars($order['address']); ?></p>
                        <p class="mb-1"><?php echo htmlspecialchars($order['city']); ?></p>
                        <p class="mb-1">Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                        <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
                    </div>

                    <div>
                        <h3 class="font-bold text-lg mb-3">Order Summary</h3>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Subtotal</span>
                            <span><?php echo number_format($order['subtotal'], 0); ?>đ</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Shipping</span>
                            <span><?php echo number_format($order['shipping'], 0); ?>đ</span>
                        </div>
                        <div class="flex justify-between font-bold pt-2 border-t mt-2">
                            <span>Total</span>
                            <span class="text-red-600"><?php echo number_format($order['total'], 0); ?>đ</span>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <h3 class="font-bold text-lg mb-4">Order Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Product</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Price</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantity</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-md object-cover"
                                                    src="<?php echo htmlspecialchars($item['image']); ?>"
                                                    alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($item['name']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo number_format($item['price'], 0); ?>đ
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo $item['quantity']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo number_format($item['price'] * $item['quantity'], 0); ?>đ</div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Next Steps -->
                <div class="mt-8 bg-blue-50 rounded-lg p-6 border border-blue-100">
                    <h3 class="font-bold text-blue-800 text-lg mb-3">What's Next?</h3>
                    <ul class="list-disc pl-5 space-y-2 text-blue-700">
                        <li>You will receive an order confirmation email shortly.</li>
                        <li>We will notify you when your order ships.</li>
                        <li>For any questions about your order, please contact our customer service.</li>
                    </ul>
                </div>

                <div class="mt-6 text-center">
                    <a href="index.php"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>