<?php
$page_title = "Products";
require_once 'includes/header.php';

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 1000;
$color = isset($_GET['color']) ? $_GET['color'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build SQL query with filters
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($color)) {
    $sql .= " AND color = ?";
    $params[] = $color;
    $types .= "s";
}

if ($min_price > 0) {
    $sql .= " AND price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price < 1000) {
    $sql .= " AND price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR category LIKE ? OR color LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Get distinct categories and colors for filter options
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$colors_query = "SELECT DISTINCT color FROM products ORDER BY color";
$categories_result = mysqli_query($conn, $categories_query);
$colors_result = mysqli_query($conn, $colors_query);

// Get price range
$price_query = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products";
$price_result = mysqli_query($conn, $price_query);
$price_range = mysqli_fetch_assoc($price_result);
$db_min_price = $price_range ? floor($price_range['min_price']) : 0;
$db_max_price = $price_range ? ceil($price_range['max_price']) : 1000;

// If no filters are set, use the database min/max
if (!isset($_GET['min_price'])) $min_price = $db_min_price;
if (!isset($_GET['max_price'])) $max_price = $db_max_price;

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!-- Page Header -->
<div class="bg-orange-500 py-3">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Our Products</h1>
        <p class="text-gray-300 mt-2">Find your perfect pair of shoes</p>
    </div>
</div>
<!-- Products Section with Filters -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="w-full md:w-1/4 bg-white p-6 rounded-lg shadow-md h-fit">
                <h2 class="text-xl font-bold mb-4">Filters</h2>

                <form id="filter-form" action="products.php" method="get" class="space-y-6">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-gray-700 font-medium mb-2">Search</label>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-gray-700 font-medium mb-2">Category</label>
                        <select id="category" name="category"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                    <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Color Filter -->
                    <div>
                        <label for="color" class="block text-gray-700 font-medium mb-2">Color</label>
                        <select id="color" name="color"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Colors</option>
                            <?php while ($col = mysqli_fetch_assoc($colors_result)): ?>
                                <option value="<?php echo htmlspecialchars($col['color']); ?>"
                                    <?php echo $color === $col['color'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($col['color']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Price Range</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" id="min_price" name="min_price" value="<?php echo $min_price; ?>"
                                min="<?php echo $db_min_price; ?>" max="<?php echo $db_max_price; ?>"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span>to</span>
                            <input type="number" id="max_price" name="max_price" value="<?php echo $max_price; ?>"
                                min="<?php echo $db_min_price; ?>" max="<?php echo $db_max_price; ?>"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mt-2">
                            <input type="range" id="price_slider" min="<?php echo $db_min_price; ?>"
                                max="<?php echo $db_max_price; ?>" class="w-full" value="<?php echo $min_price; ?>">
                            <div class="flex justify-between text-sm text-gray-500">
                                <span><?php echo $db_min_price; ?>đ</span>
                                <span><?php echo $db_max_price; ?>đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex space-x-2">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-300">
                            Apply Filters
                        </button>
                        <a href="products.php"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded transition duration-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            <div class="w-full md:w-3/4">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-600"><?php echo count($products); ?> products found</p>
                    <div class="flex items-center space-x-2">
                        <label for="sort" class="text-gray-600">Sort by:</label>
                        <select id="sort"
                            class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="name_asc">Name: A to Z</option>
                            <option value="name_desc">Name: Z to A</option>
                        </select>
                    </div>
                </div>

                <?php if (count($products) > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="products-container">
    <?php foreach ($products as $product): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 product-card">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>" 
                class="w-[315px] h-[315px] object-cover mx-auto">
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-lg font-bold text-gray-800">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                        <?php echo htmlspecialchars($product['category']); ?>
                    </span>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xl font-bold text-red-600">
                        <?php echo number_format($product['price'], 0); ?>đ
                    </p>
                    <span class="text-sm text-gray-500">
                        <?php echo htmlspecialchars($product['color']); ?>
                    </span>
                </div>
                <button class="add-to-cart w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-300"
                    data-id="<?php echo $product['id']; ?>"
                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                    data-price="<?php echo $product['price']; ?>"
                    data-image="<?php echo htmlspecialchars($product['image_url']); ?>">
                    Add to Cart
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

                <?php else: ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-yellow-700">No products found matching your criteria. Try adjusting your filters.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<script>
    // JavaScript for product sorting and filtering
    document.addEventListener('DOMContentLoaded', function() {
        // Sort functionality
        const sortSelect = document.getElementById('sort');
        const productsContainer = document.getElementById('products-container');

        if (sortSelect && productsContainer) {
            sortSelect.addEventListener('change', function() {
                const products = Array.from(productsContainer.querySelectorAll('.product-card'));

                products.sort(function(a, b) {
                    const aPrice = parseFloat(a.querySelector('.text-red-600').textContent.replace(
                        'đ', '').replace(/,/g, ''));
                    const bPrice = parseFloat(b.querySelector('.text-red-600').textContent.replace(
                        'đ', '').replace(/,/g, ''));
                    const aName = a.querySelector('h3').textContent;
                    const bName = b.querySelector('h3').textContent;

                    switch (sortSelect.value) {
                        case 'price_asc':
                            return aPrice - bPrice;
                        case 'price_desc':
                            return bPrice - aPrice;
                        case 'name_asc':
                            return aName.localeCompare(bName);
                        case 'name_desc':
                            return bName.localeCompare(aName);
                        default:
                            return 0;
                    }
                });

                // Clear and re-append sorted products
                productsContainer.innerHTML = '';
                products.forEach(function(product) {
                    productsContainer.appendChild(product);
                });
            });
        }

        // Price slider functionality
        const priceSlider = document.getElementById('price_slider');
        const minPriceInput = document.getElementById('min_price');
        const maxPriceInput = document.getElementById('max_price');

        if (priceSlider && minPriceInput && maxPriceInput) {
            priceSlider.addEventListener('input', function() {
                minPriceInput.value = this.value;
            });
        }

        // Add to cart functionality
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                const productPrice = this.getAttribute('data-price');
                const productImage = this.getAttribute('data-image');

                // Send AJAX request to add item to cart
                fetch('includes/add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${productId}&name=${encodeURIComponent(productName)}&price=${productPrice}&image=${encodeURIComponent(productImage)}&quantity=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success notification
                            showNotification(`${productName} added to cart!`);

                            // Update cart count in header if it exists
                            const cartCount = document.querySelector('header .absolute');
                            if (cartCount) {
                                cartCount.textContent = data.cartCount;
                                cartCount.classList.remove('hidden');
                            }
                        } else {
                            // Show error notification
                            showNotification('Error adding product to cart', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error adding product to cart', 'error');
                    });
            });
        });

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