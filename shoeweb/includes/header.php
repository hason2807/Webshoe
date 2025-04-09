<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once 'config.php';

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ShopEric' : 'ShopEric'; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <style>
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        .dropdown-item {
            padding: 12px 16px;
            display: block;
            text-align: left;
            color: #333;
            text-decoration: none;
        }
        
        .dropdown-item:hover {
            background-color: #f1f1f1;
        }
        
        .selected {
            background-color: #1a56db;
            color: white !important;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #6B7280;
            background-image: url('public/images/user-avatar.png');
            background-size: cover;
            background-position: center;
        }
        
        /* Login Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            text-align: center;
            animation: modalFadeIn 0.3s;
        }
        
        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close-modal:hover {
            color: #555;
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .modal-content {
                background-color: #2a2a2a;
                color: #e0e0e0;
            }
            
            .close-modal {
                color: #ccc;
            }
            
            .close-modal:hover {
                color: #fff;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 py-3 flex items-center">
            <!-- Logo -->
            <a href="index.php" class="text-2xl font-bold text-orange-500 mr-8">ShopEric</a>
            
            <!-- Search Bar with Dropdown -->
            <div class="relative flex-grow max-w-2xl mr-4">
                <div class="flex">
                    <div class="dropdown relative">
                        <button class="bg-white text-gray-700 px-4 py-2 rounded-l-md border-r border-gray-300 flex items-center">
                            <span>All</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="dropdown-content">
                        <a href="#" class="dropdown-item selected">All</a>
                        <a href="#" class="dropdown-item">Shoes</a>
                        <a href="#" class="dropdown-item">Accessories</a>
                        </div>
                    </div>
                    <input type="text" placeholder="Search for products..." class="px-4 py-2 flex-grow focus:outline-none">
                    <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-r-md transition-colors">
                        Search
                    </button>
                </div>
            </div>
            
            <!-- Right Menu Items -->
            <div class="flex items-center space-x-6">
                <!-- Categories -->
                <div class="dropdown">
                    <button class="hover:text-orange-300 transition-colors flex items-center">
                        <span>Category</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="dropdown-content">
                    <a href="#" class="dropdown-item">Men's Shoes</a>
                    <a href="#" class="dropdown-item">Accessories</a>
                    </div>
                </div>
                
                <!-- User Account -->
                <div class="flex items-center">
                    <!-- User Avatar -->
                    <div class="w-9 h-9 rounded-full mr-2 bg-gray-200 overflow-hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" fill="#8696AC" class="bg-blue-200 w-full h-full">
                            <circle cx="18" cy="11" r="7" />
                            <path d="M18,2A16,16,0,1,0,34,18,16,16,0,0,0,18,2Zm0,5a6,6,0,1,1-6,6A6,6,0,0,1,18,7ZM18,30a12,12,0,0,1-9.42-4.64,18,18,0,0,1,18.83,0A12,12,0,0,1,18,30Z" />
                        </svg>
                    </div>
                    <?php if (isLoggedIn()): ?>
                        <div class="dropdown">
                            <button class="hover:text-orange-300 transition-colors">
                                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                            </button>
                            <div class="dropdown-content">
                            <a href="profile.php" class="dropdown-item">Profile</a>
                            <a href="orders.php" class="dropdown-item">Orders</a>
                            <a href="logout.php" class="dropdown-item">Log Out</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center">
                        <a href="login.php" class="hover:text-orange-300 transition-colors">Login</a>
                        <span class="mx-2">|</span>
                        <a href="register.php" class="hover:text-orange-300 transition-colors">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Cart - Check if user is logged in -->
                <?php if (isLoggedIn()): ?>
                    <a href="cart.php" class="relative hover:text-orange-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                        </span>
                    </a>
                <?php else: ?>
                    <!-- Show cart button that triggers login prompt for non-logged in users -->
                    <button id="cartLoginBtn" class="relative hover:text-orange-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            0
                        </span>
                    </button>
                <?php endif; ?>
                
                <!-- Chat -->
                <a href="chat.php" class="hover:text-orange-300 transition-colors">
                    Chat
                </a>
            </div>
        </div>
    </header>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-orange-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h2 class="text-xl font-semibold mb-4">Login Required</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300">You need to login to access your shopping cart</p>
                <div class="flex justify-center space-x-4">
                    <a href="login.php" class="px-6 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors">Login</a>
                    <a href="register.php" class="px-6 py-2 bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Register</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add JavaScript for cart functionality and login modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the modal
            const modal = document.getElementById("loginModal");
            
            // Get the button that opens the modal
            const cartBtn = document.getElementById("cartLoginBtn");
            
            // Get the <span> element that closes the modal
            const closeBtn = document.querySelector(".close-modal");
            
            // Open modal when cart button is clicked
            if (cartBtn) {
                cartBtn.addEventListener('click', function() {
                    modal.style.display = "block";
                });
            }
            
            // Close modal when close button is clicked
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.style.display = "none";
                });
            }
            
            // Close modal when clicking outside of it
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            });
            
            // Add event listeners to "Add to Cart" buttons on product pages
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    <?php if (!isLoggedIn()): ?>
                    // If not logged in, prevent default action and show login modal
                    e.preventDefault();
                    modal.style.display = "block";
                    <?php endif; ?>
                });
            });
        });
        
        // Function to add event listeners to dynamically added "Add to Cart" buttons
        function setupCartButtons() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            
            addToCartButtons.forEach(button => {
                // Remove old event listeners if any
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                newButton.addEventListener('click', function(e) {
                    <?php if (!isLoggedIn()): ?>
                    // If not logged in, prevent default action and show login modal
                    e.preventDefault();
                    document.getElementById("loginModal").style.display = "block";
                    <?php endif; ?>
                });
            });
        }
    </script>

    <!-- Script to ensure login requirement is applied to dynamically loaded content -->
    <script>
        // Use MutationObserver to detect when new "Add to Cart" buttons are added to the page
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    setupCartButtons();
                }
            });
        });
        
        // Start observing the document with the configured parameters
        observer.observe(document.body, { childList: true, subtree: true });
        
        // Initial setup
        document.addEventListener('DOMContentLoaded', setupCartButtons);
    </script>