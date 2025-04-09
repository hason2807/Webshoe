// Main JavaScript file for ShoesStore

document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Add to cart functionality
    // Use a more specific selector to target only product add-to-cart buttons
    // and avoid affecting login/register form buttons
    const addToCartButtons = document.querySelectorAll('.product-card button[class*="bg-blue-600"], .add-to-cart-btn');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const product = this.closest('div.bg-white');
            let productName = 'Product';
            
            if (product && product.querySelector('h3')) {
                productName = product.querySelector('h3').textContent;
            }

            // Show a simple notification
            showNotification(`${productName} added to cart!`);

            // In a real application, you would send this to a cart API
            console.log(`Added ${productName} to cart`);
        });
    });

    // Newsletter form submission
    const newsletterForm = document.querySelector('section.bg-blue-600 form');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');

            if (emailInput && emailInput.value) {
                // Show success notification
                showNotification('Thank you for subscribing to our newsletter!');
                emailInput.value = '';

                // In a real application, you would send this to a newsletter API
                console.log(`Subscribed email: ${emailInput.value}`);
            }
        });
    }

    // Helper function to show notifications
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg animate-fadeIn';
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

    // Add active class to current navigation link
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('nav a');

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('nav-active');
        }
    });
});