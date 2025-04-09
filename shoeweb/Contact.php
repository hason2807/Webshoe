<?php require_once 'includes/header.php'; ?>

<div class="container mx-auto p-6 bg-gray-100 rounded-lg shadow-md max-w-6xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Contact Form Section -->
        <div class="bg-white p-6 shadow-lg rounded-lg flex flex-col h-full">
            <h2 class="text-center text-3xl font-bold text-blue-600 mb-4">Contact Us</h2>
            <div class="space-y-2 text-gray-700 text-lg">
                <p><i class="fas fa-map-marker-alt text-red-500"></i> <span class="font-semibold">Address:</span> 123 Nguyen Van Linh, Hai Chau, Da Nang</p>
                <p><i class="fas fa-envelope text-blue-500"></i> <span class="font-semibold">Email:</span> hason200702004@gmail.com</p>
                <p><i class="fas fa-phone text-green-500"></i> <span class="font-semibold">Hotline:</span> 0865340954</p>
            </div>
            <form method="POST" action="" class="mt-4 flex-grow">
                <div class="mb-4">
                    <label class="block font-semibold text-gray-800" for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter your full name" required>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold text-gray-800" for="email">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter your email" required>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold text-gray-800" for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter your phone number">
                </div>
                <div class="mb-4">
                    <label class="block font-semibold text-gray-800" for="message">Message</label>
                    <textarea id="message" name="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter your message"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 transition shadow-md hover:shadow-lg">
                    Send Message
                </button>
            </form>
        </div>

        <!-- Map Section -->
        <div class="bg-white p-6 shadow-lg rounded-lg flex flex-col h-full">
            <h2 class="text-center text-3xl font-bold text-blue-600 mb-4">Map</h2>
            <div class="rounded-lg overflow-hidden flex-grow">
                <iframe class="w-full h-full aspect-[16/9] rounded-lg shadow-md" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.6753805208367!2d106.67881731474385!3d10.76893649235474!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f5897c1a1b7%3A0xf6e63c3ead00acd4!2zVHLGsOG7nW5nIENhbyDEkOG6rW5nIFRp4buDdSBIb8Mz!5e0!3m2!1svi!2s!4v1680756229532!5m2!1svi!2s" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
