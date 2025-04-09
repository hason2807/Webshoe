<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php';

$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = mysqli_query($conn, $categories_query);
$categories = [];
while ($category = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $category['category'];
}

$sliderImages = [
    [
        'src' => 'https://tyhisneaker.com/wp-content/uploads/2024/03/giay-nike-air-force-1-valentines-2024-best-quality.jpg',
        'alt' => 'Fashion Shoes 1'
    ],
    [
        'src' => 'https://down-vn.img.susercontent.com/file/vn-11134201-7r98o-lv8cftc78x6zb0.webp',
        'alt' => 'Fashion Shoes 2'
    ],
    [
        'src' => 'https://images.meesho.com/images/products/386730295/1rbkm_512.webp',
        'alt' => 'Fashion Shoes 3'
    ]
];

require_once 'includes/header.php';
?>

<section class="py-24 relative overflow-hidden rounded-b-[50px] shadow-lg bg-gradient-to-br from-[#fff5e6] via-[#ffeccc] to-[#fff9f0] opacity-0 translate-y-12 transition-all duration-800 ease-in-out" id="heroSection">
    <div class="absolute top-[-50px] right-[-50px] w-[200px] h-[200px] rounded-full bg-amber-100/10 z-0"></div>
    <div class="absolute bottom-[-100px] left-[-100px] w-[300px] h-[300px] rounded-full bg-amber-100/5 z-0"></div>
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex flex-wrap items-center justify-between">
            <div class="w-full lg:w-1/2 mb-8 lg:mb-0 lg:pr-8 text-left relative z-2 md:animate-fadeInLeft translate-x-[50px]">
                <span class="inline-block px-5 py-2.5 bg-amber-400 text-gray-800 rounded-full text-base font-bold mb-6 shadow-md shadow-amber-400/30 border-none relative z-2">Opening Sale Discount 50%</span>
                <h1 class="text-4xl md:text-5xl lg:text-[52px] font-extrabold mb-6 leading-tight tracking-wider text-gray-800 uppercase relative z-2">
                    <span class="relative inline-block pr-2">
                        FASHION
                        <span class="absolute bottom-0 left-0 w-full h-2 bg-amber-400/30 -z-10"></span>
                    </span> 
                    SHOE STORE
                </h1>
                <p class="text-lg text-gray-600 leading-relaxed mb-5 max-w-[500px] relative z-2">
                    Introducing new premium products for online shoe shopping with convenient home delivery and exclusive designs for every occasion.
                </p>
                <a href="#" class="inline-flex items-center px-8 py-4 bg-red-400 hover:bg-red-500 text-white font-semibold text-lg rounded-full transition duration-300 shadow-lg shadow-red-400/30 hover:-translate-y-1 gap-3 mt-6 border-none relative z-2 group">
                    Shop Now 
                    <i class="fas fa-arrow-right transition-transform duration-300 group-hover:translate-x-1"></i>
                </a>
            </div>
            <div class="w-full lg:w-1/2 relative md:animate-fadeInRight -translate-x-[50px]">
                <div class="hero-slider relative w-full max-w-[500px] h-[500px] mx-auto lg:mx-0 lg:ml-auto overflow-visible">
                    <div class="slider-decoration decoration-1 absolute top-[-20px] right-[-30px] w-[80px] h-[80px] bg-red-400/10 rounded-full -z-10"></div>
                    <div class="slider-decoration decoration-2 absolute bottom-[-40px] left-[-20px] w-[100px] h-[100px] bg-amber-400/10 rounded-full -z-10"></div>
                    <div class="slider-decoration decoration-3 absolute top-[40%] right-[-50px] w-[60px] h-[60px] bg-blue-400/10 rounded-full -z-10"></div>
                    <div class="slider-frame relative w-full h-full rounded-[30px] bg-white overflow-hidden shadow-2xl transition-all duration-500 ease-in-out border-[15px] border-white hover:shadow-[0_25px_60px_rgba(0,0,0,0.3)]">
                        <div class="slides-container relative w-full h-full rounded-[15px] overflow-hidden transition-fade">
                            <?php foreach ($sliderImages as $index => $image): ?>
                                <div class="slide absolute w-full h-full rounded-[15px] overflow-hidden opacity-0 z-[1] transition-opacity duration-800 ease-in-out <?php echo $index === 0 ? 'active opacity-100 z-[2]' : ''; ?>" data-index="<?php echo $index; ?>">
                                    <img src="<?php echo $image['src']; ?>" alt="<?php echo $image['alt']; ?>" class="w-full h-full object-cover object-center transition-transform duration-800 ease-in-out rounded-[15px] filter contrast-105 brightness-102 <?php echo $index === 0 ? 'animate-smoothZoom' : ''; ?>">
                                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(255,255,255,0)_70%,rgba(255,255,255,0.5)_100%)] pointer-events-none rounded-[15px]"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider-arrow prev absolute top-1/2 left-[-25px] -translate-y-1/2 w-[50px] h-[50px] bg-white/90 rounded-full flex items-center justify-center text-lg text-red-400 cursor-pointer z-10 transition-all duration-300 shadow-md opacity-70 hover:bg-white hover:text-red-500 hover:shadow-lg hover:shadow-red-400/40 hover:opacity-100 hover:scale-110">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div class="slider-arrow next absolute top-1/2 right-[-25px] -translate-y-1/2 w-[50px] h-[50px] bg-white/90 rounded-full flex items-center justify-center text-lg text-red-400 cursor-pointer z-10 transition-all duration-300 shadow-md opacity-70 hover:bg-white hover:text-red-500 hover:shadow-lg hover:shadow-red-400/40 hover:opacity-100 hover:scale-110">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                        <div class="slider-controls absolute bottom-[-30px] left-0 right-0 flex justify-center gap-2.5 z-10">
                            <?php for ($i = 0; $i < count($sliderImages); $i++): ?>
                                <div class="slider-dot w-3 h-3 rounded-full bg-red-400/30 cursor-pointer transition-all duration-300 border-2 border-transparent <?php echo $i === 0 ? 'active bg-red-400 scale-130 shadow-md shadow-red-400/50' : ''; ?>" data-index="<?php echo $i; ?>"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(-50px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeInRight {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes smoothZoom {
    0% { transform: scale(1) translate(0, 0); }
    100% { transform: scale(1.05) translate(-5px, -5px); }
}
@layer utilities {
    .animate-fadeInDown { animation: fadeInDown 1s ease-in-out; }
    .animate-fadeInLeft { animation: fadeInLeft 1s ease-in-out; }
    .animate-fadeInRight { animation: fadeInRight 1s ease-in-out; }
    .animate-fadeInUp { animation: fadeInUp 1s ease-in-out; }
    .animate-smoothZoom { animation: smoothZoom 10s infinite alternate; }
    .scale-130 { transform: scale(1.3); }
}
#heroSection.visible {
    opacity: 1 !important;
    transform: translateY(0) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.getElementById('heroSection').classList.add('visible');
    }, 100);

    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-arrow.prev');
    const nextBtn = document.querySelector('.slider-arrow.next');
    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('opacity-0', i !== index);
            slide.classList.toggle('opacity-100', i === index);
            slide.classList.toggle('active', i === index);
            slide.classList.toggle('z-[1]', i !== index);
            slide.classList.toggle('z-[2]', i === index);

            const img = slide.querySelector('img');
            if (i === index) {
                img.classList.add('animate-smoothZoom');
            } else {
                img.classList.remove('animate-smoothZoom');
            }
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-red-400/30', i !== index);
            dot.classList.toggle('bg-red-400', i === index);
            dot.classList.toggle('scale-130', i === index);
            dot.classList.toggle('shadow-md', i === index);
            dot.classList.toggle('shadow-red-400/50', i === index);
        });

        currentSlide = index;
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => showSlide(index));
    });

    prevBtn.addEventListener('click', () => {
        let newIndex = currentSlide - 1;
        if (newIndex < 0) newIndex = slides.length - 1;
        showSlide(newIndex);
    });

    nextBtn.addEventListener('click', () => {
        let newIndex = currentSlide + 1;
        if (newIndex >= slides.length) newIndex = 0;
        showSlide(newIndex);
    });

    setInterval(() => {
        let newIndex = currentSlide + 1;
        if (newIndex >= slides.length) newIndex = 0;
        showSlide(newIndex);
    }, 5000);
});
</script>

<section class="py-16 bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- Enhanced Featured Products title with elegant styling -->
        <div class="text-center mb-16">
            <h2 class="font-sans text-5xl font-light text-gray-800 inline-block relative">
                <span class="text-orange-500 font-normal">Featured</span> Products
                <span class="block h-1 w-24 bg-orange-500 mx-auto mt-4 rounded-full"></span>
                <span class="block h-1 w-12 bg-gray-300 mx-auto mt-2 rounded-full"></span>
            </h2>
            <p class="text-gray-600 mt-4 font-light italic max-w-2xl mx-auto">Discover our curated selection of premium items</p>
        </div>
        
        <?php
        foreach ($categories as $category) {
            $sql = "SELECT * FROM products WHERE category = ? ORDER BY created_at DESC LIMIT 4";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = [];
            
            while ($product = $result->fetch_assoc()) {
                $products[] = $product;
            }
            echo '<div class="mb-16">';
            // Changed the border-b to orange color
            echo '<div class="flex justify-between items-center mb-8 border-b border-orange-500 pb-4">';
            echo '<h3 class="text-2xl font-light text-gray-800">' . htmlspecialchars($category) . '</h3>';
            echo '<a href="products.php?category=' . urlencode($category) . '" class="group text-orange-500 hover:text-orange-700 font-light flex items-center transition-colors duration-300">';
            echo 'View All <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 20 20" fill="currentColor">';
            echo '<path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />';
            echo '</svg></a>';
            echo '</div>';
            
            echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">';
            
            foreach ($products as $product) {
                echo '<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition duration-300 group">';
                echo '<div class="relative w-full h-[315px] overflow-hidden bg-gray-100">';
                echo '<img src="' . htmlspecialchars($product['image_url']) . '" alt="' . htmlspecialchars($product['name']) . '" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">';
                
                // Elegant overlay with light, non-bold text
echo '<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">';
                echo '<div class="p-4 w-full">';
                echo '<p class="text-white font-light text-sm tracking-wide">' . htmlspecialchars($product['name']) . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="p-6">';
                echo '<h3 class="text-lg font-light text-gray-800 mb-3 line-clamp-2 h-14 tracking-wide">' . htmlspecialchars($product['name']) . '</h3>';
                echo '<div class="flex justify-between items-center mb-4">';
                echo '<p class="text-xl font-light text-orange-500">' . number_format($product['price'], 0) . 'đ</p>';
                echo '<span class="px-3 py-1 text-sm font-light text-gray-700 bg-gray-100 rounded-full">' . htmlspecialchars($product['color']) . '</span>';
                echo '</div>';
                echo '<button class="add-to-cart w-full bg-orange-500 hover:bg-orange-600 text-white font-light py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center space-x-2 transform hover:scale-[1.02] tracking-wide"';
                echo ' data-id="' . $product['id'] . '" data-name="' . htmlspecialchars($product['name']) . '"';
                echo ' data-price="' . $product['price'] . '" data-image="' . htmlspecialchars($product['image_url']) . '">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
                echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />';
                echo '</svg>';
                echo '<span>Add to Cart</span></button>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            
            echo '</div>';
            
            $stmt->close();
        }
        ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-arrow.prev');
    const nextBtn = document.querySelector('.slider-arrow.next');
    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('opacity-0', i !== index);
            slide.classList.toggle('opacity-100', i === index);
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-gray-300', i !== index);
            dot.classList.toggle('bg-blue-500', i === index);
        });

        currentSlide = index;
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => showSlide(index));
    });

    prevBtn.addEventListener('click', () => {
        let newIndex = currentSlide - 1;
        if (newIndex < 0) newIndex = slides.length - 1;
        showSlide(newIndex);
    });

    nextBtn.addEventListener('click', () => {
        let newIndex = currentSlide + 1;
        if (newIndex >= slides.length) newIndex = 0;
        showSlide(newIndex);
    });

    setInterval(() => {
        let newIndex = currentSlide + 1;
        if (newIndex >= slides.length) newIndex = 0;
        showSlide(newIndex);
    }, 5000);

    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = this.getAttribute('data-price');
            const productImage = this.getAttribute('data-image');

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
                    alert(`${productName} added to cart!`);
                    const cartCount = document.querySelector('header .absolute');
                    if (cartCount) {
                        cartCount.textContent = data.cartCount;
                        cartCount.classList.remove('hidden');
                    }
                } else {
                    alert('Error adding product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding product to cart');
            });
        });
    });
});
</script>