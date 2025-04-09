<?php
$page_title = "Shopping Cart";
require_once 'includes/header.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculate cart totals
$cart_total = 0;
$item_count = 0;

foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $item_count += $item['quantity'];
}
?>

<!-- Page Header -->
<div class="bg-orange-500 py-3">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Your Shopping Cart</h1>
        <p class="text-gray-300 mt-2"><?php echo $item_count; ?> items in your cart</p>
    </div>
</div>
<!-- Cart Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <?php if (count($_SESSION['cart']) > 0): ?>
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4 border-b">
                            <h2 class="text-xl font-bold">Cart Items</h2>
                        </div>

                        <div class="divide-y">
                            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                <div class="p-4 flex flex-col sm:flex-row items-center gap-4"
                                    data-index="<?php echo $index; ?>">
                                    <div class="w-24 h-24 flex-shrink-0">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                                            class="w-full h-full object-cover rounded">
                                    </div>

                                    <div class="flex-grow">
                                        <h3 class="text-lg font-bold text-gray-800">
                                            <?php echo htmlspecialchars($item['name']); ?></h3>
                                        <p class="text-gray-600 text-sm">Product ID: <?php echo $item['id']; ?></p>
                                        
                                        <!-- Shoe Size Selection -->
                                        <div class="mt-2">
                                            <label class="text-sm text-gray-600 mr-2">Size:</label>
                                            <select class="shoe-size border rounded-md px-2 py-1 text-sm" data-index="<?php echo $index; ?>">
                                                <?php for ($size = 36; $size <= 42; $size++): ?>
                                                    <option value="<?php echo $size; ?>" <?php echo (isset($item['size']) && $item['size'] == $size) ? 'selected' : ''; ?>>
                                                        <?php echo $size; ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="quantity-btn decrease bg-gray-200 hover:bg-gray-300 rounded-full w-8 h-8 flex items-center justify-center"
                                            data-action="decrease">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4" />
                                            </svg>
                                        </button>

                                        <input type="number" class="quantity-input border rounded-md w-12 h-8 text-center"
                                            value="<?php echo $item['quantity']; ?>" min="1" max="99">

                                        <button
                                            class="quantity-btn increase bg-gray-200 hover:bg-gray-300 rounded-full w-8 h-8 flex items-center justify-center"
                                            data-action="increase">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-lg font-bold text-red-600">
                                            <?php echo number_format($item['price'] * $item['quantity'], 0); ?>đ</p>
                                        <p class="text-sm text-gray-500"><?php echo number_format($item['price'], 0); ?>đ each
                                        </p>
                                    </div>

                                    <button class="remove-item text-gray-500 hover:text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between">
                        <a href="products.php"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded transition duration-300 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Continue Shopping
                        </a>

                        <button id="update-cart"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-300">
                            Update Cart
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-20">
                        <div class="p-4 border-b">
                            <h2 class="text-xl font-bold">Order Summary</h2>
                        </div>

                        <div class="p-4 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal (<?php echo $item_count; ?> items)</span>
                                <span class="font-medium"><?php echo number_format($cart_total, 0); ?>đ</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium"><?php echo number_format(10000, 0); ?>đ</span>
                            </div>

                            <div class="border-t pt-4 flex justify-between">
                                <span class="text-lg font-bold">Total</span>
                                <span
                                    class="text-lg font-bold text-red-600"><?php echo number_format($cart_total + 10000, 0); ?>đ</span>
                            </div>

                            <a href="checkout.php"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 mt-4 block text-center">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h2 class="text-2xl font-bold mb-2">Your cart is empty</h2>
                <p class="text-gray-600 mb-6">Looks like you haven't added any products to your cart yet.</p>
                <a href="products.php"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300 inline-block">
                    Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity buttons functionality
        const quantityBtns = document.querySelectorAll('.quantity-btn');

        quantityBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const input = this.parentElement.querySelector('.quantity-input');
                let value = parseInt(input.value);

                if (action === 'increase') {
                    if (value < 99) input.value = value + 1;
                } else if (action === 'decrease') {
                    if (value > 1) input.value = value - 1;
                }

                // Update item price display
                updateItemPrice(this.closest('[data-index]'));

                // Update order summary in real-time
                updateCartTotals();
            });
        });

        // Quantity input change
        const quantityInputs = document.querySelectorAll('.quantity-input');

        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                let value = parseInt(this.value);

                // Validate input
                if (isNaN(value) || value < 1) this.value = 1;
                if (value > 99) this.value = 99;

                // Update item price display
                updateItemPrice(this.closest('[data-index]'));

                // Update order summary in real-time
                updateCartTotals();
            });
        });

        // Size selection change
        const sizeSelects = document.querySelectorAll('.shoe-size');
        
        sizeSelects.forEach(select => {
            select.addEventListener('change', function() {
                // We'll update the size when the cart is updated
                // No need to do anything else here
            });
        });

        // Remove item buttons
        const removeButtons = document.querySelectorAll('.remove-item');

        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const item = this.closest('[data-index]');
                const index = item.getAttribute('data-index');

                // Send AJAX request to remove item
                fetch('includes/update_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=remove&index=${index}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove item from DOM
                            item.remove();

                            // Update cart count in header
                            updateCartCount(data.cartCount);

                            // Update order summary
                            updateOrderSummary(data.cartTotal, data.itemCount);

                            // If cart is empty, reload page to show empty cart message
                            if (data.cartCount === 0) {
                                window.location.reload();
                            }

                            // Show notification
                            showNotification('Item removed from cart');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error removing item', 'error');
                    });
            });
        });

        // Update cart button
        const updateCartButton = document.getElementById('update-cart');

        if (updateCartButton) {
            updateCartButton.addEventListener('click', function() {
                const items = document.querySelectorAll('[data-index]');
                const updates = [];

                items.forEach(item => {
                    const index = item.getAttribute('data-index');
                    const quantity = item.querySelector('.quantity-input').value;
                    const size = item.querySelector('.shoe-size').value;

                    updates.push({
                        index: index,
                        quantity: quantity,
                        size: size
                    });
                });

                // Send AJAX request to update cart
                fetch('includes/update_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'update',
                            updates: updates
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart count in header
                            updateCartCount(data.cartCount);

                            // Update order summary
                            updateOrderSummary(data.cartTotal, data.itemCount);

                            // Show notification
                            showNotification('Cart updated successfully');

                            // Reload page to reflect changes
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error updating cart', 'error');
                    });
            });
        }

        // Helper function to update item price display
        function updateItemPrice(item) {
            const priceElement = item.querySelector('.text-red-600');
            const unitPriceText = item.querySelector('.text-gray-500').textContent;
            const unitPrice = parseFloat(unitPriceText.replace('đ each', '').replace(/,/g, ''));
            const quantity = parseInt(item.querySelector('.quantity-input').value);

            priceElement.textContent = number_format(unitPrice * quantity, 0) + 'đ';
        }

        // Helper function to update cart count in header
        function updateCartCount(count) {
            const cartCount = document.querySelector('header .absolute');
            if (cartCount) {
                if (count > 0) {
                    cartCount.textContent = count;
                    cartCount.classList.remove('hidden');
                } else {
                    cartCount.classList.add('hidden');
                }
            }
        }

        // Helper function to update order summary
        function updateOrderSummary(total, count) {
            const subtotalElement = document.querySelector('.order-summary .subtotal');
            const totalElement = document.querySelector('.order-summary .total');
            const itemCountElement = document.querySelector('.order-summary .item-count');

            if (subtotalElement && totalElement && itemCountElement) {
                subtotalElement.textContent = number_format(total, 0) + 'đ';
                totalElement.textContent = number_format(total + 10000, 0) + 'đ';
                itemCountElement.textContent = count;
            } else {
                // If the elements with specific classes aren't found, try to find them by their position in the DOM
                const subtotalValueElement = document.querySelector(
                    '.p-4.space-y-4 .flex.justify-between:nth-child(1) span:nth-child(2)');
                const totalValueElement = document.querySelector(
                    '.border-t.pt-4.flex.justify-between span:nth-child(2)');
                const subtotalTextElement = document.querySelector(
                    '.p-4.space-y-4 .flex.justify-between:nth-child(1) span:nth-child(1)');

                if (subtotalValueElement && totalValueElement && subtotalTextElement) {
                    subtotalValueElement.textContent = number_format(total, 0) + 'đ';
                    totalValueElement.textContent = number_format(total + 10000, 0) + 'đ';
                    subtotalTextElement.textContent = `Subtotal (${count} items)`;
                }
            }
        }

        // Function to calculate and update cart totals in real-time
        function updateCartTotals() {
            const items = document.querySelectorAll('[data-index]');
            let cartTotal = 0;
            let itemCount = 0;

            items.forEach(item => {
                const unitPriceText = item.querySelector('.text-gray-500').textContent;
                const unitPrice = parseFloat(unitPriceText.replace('đ each', '').replace(/,/g, ''));
                const quantity = parseInt(item.querySelector('.quantity-input').value);

                cartTotal += unitPrice * quantity;
                itemCount += quantity;
            });

            // Update order summary with new totals
            updateOrderSummary(cartTotal, itemCount);

            // Update cart count in header
            updateCartCount(itemCount);
        }

        // Helper function to format numbers with commas
        function number_format(number, decimals) {
            return number.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Helper function to show notifications
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg animate-fadeIn ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>