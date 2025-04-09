<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C2Shop Store</title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    // You can add PHP variables here if needed
    $currentYear = date("Y");
    $companyName = "Ericshop store";
    $adminName = "HUA HA SON";
    $email = "hason28072004@gmail.com";
    $phone = "0865340954";
    $location = "Da Nang";
    ?>

    <!-- Modal Structure -->
    <div id="loginModal" class="hidden fixed z-50 inset-0">
        <!-- Modal Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        
        <!-- Modal Container -->
        <div class="relative w-11/12 max-w-md mx-auto mt-20 bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Close Button -->
            <span class="absolute top-3 right-3 text-2xl cursor-pointer hover:text-red-600" onclick="closeModal()">&times;</span>
            
            <!-- Modal Content -->
            <div class="px-6 py-8">
                <h3 class="text-xl font-bold text-center mb-6">Login to Your Account</h3>
                
                <form action="" method="post">
                    <input type="text" name="username" placeholder="Username or Email" required
                           class="w-full mb-4 px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none transition-colors">
                    
                    <input type="password" name="password" placeholder="Password" required
                           class="w-full mb-4 px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none transition-colors">
                    
                    <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white rounded transition-colors">
                        Login
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <a href="#" class="text-blue-600 hover:text-blue-800 hover:underline text-sm transition-colors">
                        Forgot your password?
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->  
    <footer class="mt-20 pt-16 pb-8 bg-gray-800 text-white">  
        <div class="container mx-auto px-4">  
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">   
                <!-- Customer Support Column -->
                <div>
                    <h5 class="uppercase mb-6 font-bold text-lg">Customer Support</h5>  
                    <ul class="space-y-2">
                        <?php
                        $supportLinks = [
                            'Complaints', 'Product Advice', 'Shopping Guide', 
                            'Payment Instructions', 'Shipping', 'Warranty Policy'
                        ];
                        
                        foreach ($supportLinks as $link) {
                            echo '<li><a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-300">' . $link . '</a></li>';
                        }
                        ?>
                    </ul>  
                </div>
                
                <!-- Store Info Column -->
                <div>
                    <h5 class="uppercase mb-6 font-bold text-lg">EricShop Store Info</h5>  
                    <ul class="space-y-2">
                        <?php
                        $storeLinks = [
                            'About Us', 'Authentic Products', 'Secure Payment', 
                            'Partners', 'Careers'
                        ];
                        
                        foreach ($storeLinks as $link) {
                            echo '<li><a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-300">' . $link . '</a></li>';
                        }
                        ?>
                    </ul>  
                </div>
                
                <!-- Follow Us Column -->
                <div>
                    <h5 class="uppercase mb-6 font-bold text-lg">Follow Us</h5>  
                    <ul class="space-y-3">
                        <?php
                        $socialLinks = [
                            ['fab fa-facebook', 'Facebook'],
                            ['fab fa-instagram', 'Instagram'],
                            ['fab fa-youtube', 'YouTube']
                        ];
                        
                        foreach ($socialLinks as $social) {
                            echo '
                            <li>
                                <a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-300 flex items-center">
                                    <i class="' . $social[0] . ' text-lg mr-3 w-6 text-center"></i>' . $social[1] . '
                                </a>
                            </li>';
                        }
                        ?>
                    </ul>  
                </div>  

                <!-- Contact Column -->
                <div>
                <h5 class="text-uppercase mb-4 font-weight-bold">
                    <a href="/Contact.php" class="text-light">CONTACT</a>
                </h5>  
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-300 flex items-center">
                                <i class="fas fa-map-marker-alt text-lg mr-3 w-6 text-center"></i><?php echo $location; ?>
                            </a>
                        </li>
                        <li>
                            <a href="tel:<?php echo $phone; ?>" class="text-gray-300 hover:text-yellow-400 transition-colors duration-300 flex items-center">
                                <i class="fas fa-phone text-lg mr-3 w-6 text-center"></i><?php echo $phone; ?>
                            </a>
                        </li>
                        <li>
                            <a href="mailto:<?php echo $email; ?>" class="text-gray-300 hover:text-yellow-400 transition-colors duration-300 flex items-center">
                                <i class="fas fa-envelope text-lg mr-3 w-6 text-center"></i><?php echo $email; ?>
                            </a>
                        </li>
                    </ul>  
                </div>  
            </div>  

            <hr class="my-8 border-gray-700">  

            <!-- Copyright Section -->
            <div class="flex flex-col md:flex-row justify-between items-center">  
                <div class="md:w-7/12">  
                    <p class="text-sm">  
                        &copy; <?php echo $currentYear; ?> All rights reserved by 
                        <a href="index.html" class="text-yellow-400 hover:text-yellow-300 no-underline">
                            <?php echo $companyName; ?>
                        </a>  
                    </p>  
                </div>  
                <div class="md:w-5/12 text-center md:text-right mt-4 md:mt-0">  
                    <p class="text-sm">  
                        Admin <i class="fas fa-heart text-red-500"></i> 
                        <a href="#" class="text-yellow-400 hover:text-yellow-300 no-underline">
                            <?php echo $adminName; ?>
                        </a>  
                    </p>  
                </div>  
            </div>  
        </div>  
    </footer>

    <!-- JavaScript for Modal -->
    <script>
        function openModal() {
            document.getElementById('loginModal').classList.remove('hidden');
            document.getElementById('loginModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('loginModal').classList.remove('flex');
            document.getElementById('loginModal').classList.add('hidden');
        }
    </script>
</body>
</html>