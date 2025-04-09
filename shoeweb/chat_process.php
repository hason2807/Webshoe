<?php

require_once 'includes/config.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'] ?? '';
$history = $data['history'] ?? [];

// Initialize response array
$response = [
'success' => false,
'response' => ''
];
// Check if message is empty
if (empty($message)) {
$response['response'] = "Xin lỗi, tôi không nhận được tin nhắn của bạn.";
echo json_encode($response);
exit;
}
// Get product information from database (thêm image_url vào truy vấn)
$sql = "SELECT id, category, name, color, price, image_url FROM products ORDER BY category, price";
$result = mysqli_query($conn, $sql);
$products_by_category = [];
// Organize products by category
while ($row = mysqli_fetch_assoc($result)) {
if (!isset($products_by_category[$row['category']])) {
$products_by_category[$row['category']] = [];
}
$products_by_category[$row['category']][] = $row;
}

// Format product data for better context
$productContext = "Danh sách sản phẩm theo danh mục:\n\n";
foreach ($products_by_category as $category => $products) {
$productContext .= "$category:\n";
foreach ($products as $product) {
$productContext .= "• {$product['name']}\n";
$productContext .= " Giá: " . number_format($product['price'], 0) . "đ\n";
$productContext .= " Màu: {$product['color']}\n";
$productContext .= " ID: {$product['id']}\n";
// Convert image URL to img tag
$imageUrl = $product['image_url'] ?? '';
if (!empty($imageUrl)) {
$productContext .= " Ảnh: <img src='" . $imageUrl . "' alt='{$product['name']}' style='max-width:200px; max-height:150px; display:block; margin:8px 0;'>\n\n";
} else {
$productContext .= " Ảnh: Không có ảnh\n\n";
}
}
}

// Define the assistant's initial message with proper formatting
$assistantMessage = "Hello!\n\n" .
"I'm happy to help you find the perfect pair of shoes at our store. To provide the best advice, please share some details:\n\n" .
"🔹 Budget: How much are you planning to spend on the shoes?\n\n" .
"🔹 Purpose: Are you buying shoes for work, casual wear, sports, or another purpose?\n\n" .
"🔹 Favorite color: Do you have any preferred colors?\n\n" .
"🔹 Favorite brand: Do you have a favorite shoe brand?";

// Build conversation context
$conversationContext = "You are an intelligent shoe consultation assistant for a shoe store. Follow these guidelines:

Respond naturally, in a friendly and professional manner.
Format messages clearly:
Use blank lines to separate sections.
Use the emoji 🔹 to highlight key points.
Each question or important information should be on a separate line.
Always add a blank line after each question or key detail.
Each product introduction should have a blank line above it.
When introducing products:
Use the emoji ✨ before the product name.
Display the full name on a separate line.
Show the price on a separate line with the emoji 💰.
List key features, one per line, using the emoji 👉.
Available colors should be listed separately with the emoji 🎨.
Always provide the product ID so users can view details.
Give recommendations based on:
The customer's budget.
The intended purpose of use.
Color preferences.
Preferred brands.
Always ask for more information to give better advice.
Maintain a smooth and context-aware conversation.
Product information:\n\n" . $productContext;

// Add system message at the start of conversation
if (empty($history)) {
$formattedHistory[] = [
"role" => "assistant",
"parts" => [["text" => $assistantMessage]]
];
}

// Format conversation history
$formattedHistory = [];
foreach ($history as $entry) {
$formattedHistory[] = [
"role" => $entry['role'],
"parts" => [["text" => $entry['content']]]
];
}

// Add current message with context
$formattedHistory[] = [
"role" => "user",
"parts" => [["text" => $message . "\n\nContext: " . $conversationContext]]
];

// Prepare Gemini API request
$apiKey = getenv('GEMINI_API_KEY');
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

$requestData = [
"contents" => $formattedHistory,
"safetySettings" => [
[
"category" => "HARM_CATEGORY_HARASSMENT",
"threshold" => "BLOCK_MEDIUM_AND_ABOVE"
]
],
"generationConfig" => [
"temperature" => 0.9,
"topK" => 40,
"topP" => 0.95,
"maxOutputTokens" => 1024,
]
];

// Make API request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
'Content-Type: application/json'
]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
$apiResponse = json_decode($result, true);
if (isset($apiResponse['candidates'][0]['content']['parts'][0]['text'])) {
$response['success'] = true;
$botResponse = $apiResponse['candidates'][0]['content']['parts'][0]['text'];

    // Xóa bỏ các ký tự Markdown không cần thiết
    $botResponse = str_replace(['**', '***', '.**', '!**', '?**'], '', $botResponse);
    
    // 1. Loại bỏ các emoji trùng lặp trước khi xử lý
    $botResponse = preg_replace('/([✨💰🎨👉🔹])\s*\1+/', '$1', $botResponse);
    
    // 2. Thay thế dấu • hoặc - đầu dòng bằng emoji 🔹 nếu chưa có emoji
    $botResponse = preg_replace('/^(•|\-)\s+/m', '🔹 ', $botResponse);
    
    // 3. Đảm bảo có dòng trống sau mỗi dòng có emoji 🔹 (nhưng không quá nhiều)
    $botResponse = preg_replace('/(🔹[^\n]+)(\n)(?![\n🔹])/', "$1\n", $botResponse);
    
    // 4. Định dạng tên sản phẩm với emoji ✨ nếu không phải là câu hỏi
    $lines = explode("\n", $botResponse);
    $formattedLines = [];
    
    // Array để lưu tên sản phẩm và ID để tạo link
    $productInfo = [];
    $currentProduct = null;
    $currentImageUrl = null;
    
    foreach ($lines as $line) {
        // Bỏ qua dòng trống
        if (trim($line) === '') {
            $formattedLines[] = '';
            continue;
        }
        
        // Trích xuất ID sản phẩm từ dòng nếu có
        if (preg_match('/ID:\s*(\d+)/i', $line, $matches)) {
            if ($currentProduct) {
                $productInfo[$currentProduct]['id'] = $matches[1];
            }
            continue; // Bỏ qua dòng ID, sẽ thêm link sau
        }
        
        // Trích xuất URL ảnh nếu có
        if (preg_match('/Ảnh:\s*(https?:\/\/[^\s]+)/i', $line, $matches)) {
            if ($currentProduct) {
                $productInfo[$currentProduct]['image'] = $matches[1];
            }
            continue; // Bỏ qua dòng URL ảnh, sẽ hiển thị ảnh sau
        }
        
        // Nếu là dòng chứa tên sản phẩm (không có emoji và không phải câu hỏi)
        if (!preg_match('/^[🔹✨💰👉🎨]/', $line) && 
            !strpos($line, '?') && 
            (strpos(strtolower($line), 'giày') !== false || 
             strpos(strtolower($line), 'dép') !== false ||
             strpos(strtolower($line), 'sandal') !== false ||
             preg_match('/[A-Z][a-z]+ [A-Z][a-z]+/', $line))
           ) {
            // Chỉ áp dụng cho dòng có vẻ như là tên sản phẩm
            if (!preg_match('/^(giá|màu|chất liệu|đặc điểm|kích cỡ|mục đích|phong cách)/i', trim($line))) {
                $currentProduct = trim($line);
                $productInfo[$currentProduct] = []; // Khởi tạo mảng thông tin sản phẩm
                $formattedLines[] = '✨ ' . $currentProduct;
                continue;
            }
        }
        
        // Định dạng dòng giá với emoji 💰 (nhưng chỉ thêm nếu chưa có)
        if (preg_match('/giá:?\s*(\d{1,3}(,\d{3})*đ)/i', $line, $matches)) {
            if (!preg_match('/^💰/', $line)) {
                $formattedLines[] = '💰 ' . trim($line);
                
                // Lưu giá sản phẩm cho việc thêm vào giỏ hàng
                if ($currentProduct) {
                    $productInfo[$currentProduct]['price'] = $matches[1];
                }
            } else {
                $formattedLines[] = $line;
                
                // Trích xuất giá từ dòng đã có emoji
                if (preg_match('/💰\s+Giá:?\s*(\d{1,3}(,\d{3})*đ)/i', $line, $matches) && $currentProduct) {
                    $productInfo[$currentProduct]['price'] = $matches[1];
                }
            }
            continue;
        }
        
        // Định dạng dòng màu sắc với emoji 🎨 (nhưng chỉ thêm nếu chưa có)
        if (preg_match('/màu:?\s+([^:]+)$/i', $line, $matches)) {
            if (!preg_match('/^🎨/', $line)) {
                $formattedLines[] = '🎨 ' . trim($line);
                
                // Lưu màu sản phẩm
                if ($currentProduct) {
                    $productInfo[$currentProduct]['color'] = trim($matches[1]);
                }
            } else {
                $formattedLines[] = $line;
                
                // Trích xuất màu từ dòng đã có emoji
                if (preg_match('/🎨\s+Màu:?\s+([^:]+)$/i', $line, $matches) && $currentProduct) {
                    $productInfo[$currentProduct]['color'] = trim($matches[1]);
                }
            }
            continue;
        }
        
        // Định dạng các đặc điểm với emoji 👉 (nhưng chỉ thêm nếu chưa có)
        if (preg_match('/^(đặc điểm|phù hợp|thích hợp|chất liệu|thiết kế|kiểu dáng|thoải mái|thích hợp|giảm sốc)/i', trim($line)) && !preg_match('/^👉/', $line)) {
            $formattedLines[] = '👉 ' . trim($line);
            continue;
        }
        
        // Giữ nguyên các dòng khác
        $formattedLines[] = $line;
    }
    
    // Tạo product cards cho mỗi sản phẩm - với khoảng cách hợp lý và nút thêm vào giỏ hàng
    $finalLines = [];
    $currentProductCard = null;
    $inProductCard = false;
    
    foreach ($formattedLines as $index => $line) {
        // Bắt đầu product card mới nếu đây là dòng tên sản phẩm
        if (strpos($line, '✨ ') === 0) {
            // Đóng product card trước đó nếu có
            if ($inProductCard) {
                // Thêm ảnh sản phẩm nếu có
                $productName = substr($currentProductCard, 2); // Bỏ emoji ✨ ở đầu
                if (isset($productInfo[$productName]['image'])) {
                    $finalLines[] = '<div class="product-image"><img src="' . $productInfo[$productName]['image'] . '" alt="' . $productName . '" style="max-width:200px; max-height:150px; display:block; margin:5px 0;"></div>';
                }
                
                // Tạo container để chứa các nút (Xem chi tiết và Thêm vào giỏ hàng)
                $finalLines[] = '<div class="product-actions">';
                
                // Thêm link sản phẩm nếu có ID
                if (isset($productInfo[$productName]['id'])) {
                    $finalLines[] = '<a href="product.php?id=' . $productInfo[$productName]['id'] . '" class="view-details-btn" target="_blank">🔍 Xem chi tiết</a>';
                    
                    // Thêm nút "Thêm vào giỏ hàng"
                    // Tạo dữ liệu sản phẩm để truyền vào hàm addToCart
                    $productData = [
                        'id' => $productInfo[$productName]['id'],
                        'name' => $productName,
                        'price' => $productInfo[$productName]['price'] ?? '',
                        'color' => $productInfo[$productName]['color'] ?? '',
                        'image' => $productInfo[$productName]['image'] ?? ''
                    ];
                    $productDataJson = htmlspecialchars(json_encode($productData), ENT_QUOTES, 'UTF-8');
                    
                    $finalLines[] = '<button class="add-to-cart-btn" data-product=\'' . $productDataJson . '\'>🛒 Thêm vào giỏ</button>';
                }
                
                $finalLines[] = '</div>'; // Đóng product-actions
                $finalLines[] = '</div>'; // Đóng product-card
            }
            
            // Bắt đầu product card mới
            $finalLines[] = '<div class="product-card">';
            $finalLines[] = '<div class="product-name">' . $line . '</div>';
            $currentProductCard = $line;
            $inProductCard = true;
            continue;
        }
        
        // Nếu đang trong product card, thêm dòng vào card
        if ($inProductCard) {
            // Đặc biệt xử lý cho các chi tiết sản phẩm
            if (strpos($line, '💰 ') === 0 || strpos($line, '🎨 ') === 0 || strpos($line, '👉 ') === 0) {
                $finalLines[] = '<div class="product-detail">' . $line . '</div>';
            } else if (trim($line) !== '') {
                $finalLines[] = '<div class="product-text">' . $line . '</div>';
            } else {
                // Dòng trống, chỉ thêm vào nếu cần thiết để phân cách
                // (giảm thiểu các dòng trống không cần thiết)
                continue;
            }
            continue;
        }
        
        // Các dòng khác không thuộc product card
        if (trim($line) !== '') {
            $finalLines[] = $line;
        }
    }
    
    // Đóng product card cuối cùng nếu có
    if ($inProductCard) {
        // Thêm ảnh sản phẩm nếu có
        $productName = substr($currentProductCard, 2); // Bỏ emoji ✨ ở đầu
        if (isset($productInfo[$productName]['image'])) {
            $finalLines[] = '<div class="product-image"><img src="' . $productInfo[$productName]['image'] . '" alt="' . $productName . '" style="max-width:200px; max-height:150px; display:block; margin:5px 0;"></div>';
        }
        
        // Tạo container để chứa các nút (Xem chi tiết và Thêm vào giỏ hàng)
        $finalLines[] = '<div class="product-actions">';
        
        // Thêm link sản phẩm nếu có ID
        if (isset($productInfo[$productName]['id'])) {
            $finalLines[] = '<a href="product.php?id=' . $productInfo[$productName]['id'] . '" class="view-details-btn" target="_blank">🔍 Xem chi tiết</a>';
            
            // Thêm nút "Thêm vào giỏ hàng"
            // Tạo dữ liệu sản phẩm để truyền vào hàm addToCart
            $productData = [
                'id' => $productInfo[$productName]['id'],
                'name' => $productName,
                'price' => $productInfo[$productName]['price'] ?? '',
                'color' => $productInfo[$productName]['color'] ?? '',
                'image' => $productInfo[$productName]['image'] ?? ''
            ];
            $productDataJson = htmlspecialchars(json_encode($productData), ENT_QUOTES, 'UTF-8');
            
            $finalLines[] = '<button class="add-to-cart-btn" data-product=\'' . $productDataJson . '\'>🛒 Thêm vào giỏ</button>';
        }
        
        $finalLines[] = '</div>'; // Đóng product-actions
        $finalLines[] = '</div>'; // Đóng product-card
    }
    
    $botResponse = implode("\n", $finalLines);
    
    // 5. Làm sạch định dạng (loại bỏ nhiều dòng trống liên tiếp)
    $botResponse = preg_replace('/\n{3,}/', "\n", $botResponse);
    $botResponse = trim($botResponse);
    
    // 6. Định dạng dấu chấm câu để đảm bảo luôn có khoảng cách (nhưng không quá nhiều)
    $botResponse = preg_replace('/\.([A-ZÀ-Ỹ])/', ".\n$1", $botResponse);
    $botResponse = preg_replace('/\?([A-ZÀ-Ỹ])/', "?\n$1", $botResponse);
    $botResponse = preg_replace('/\!([A-ZÀ-Ỹ])/', "!\n$1", $botResponse);

    // 7. Chuyển đổi image URL còn lại trong text sang thẻ <img> trong kết quả trả về
    $botResponse = preg_replace('/Ảnh:\s+(https?:\/\/[^\s]+)/', '<div class="product-image"><img src="$1" alt="Hình ảnh sản phẩm" style="max-width:200px; max-height:150px; display:block; margin:5px 0;"></div>', $botResponse);

    // 8. Quan trọng: Chuyển đổi các ký tự xuống dòng \n thành thẻ HTML <br> 
    // Nhưng bỏ qua trong các thẻ div
    $botResponse = preg_replace('/\n(?!<div|<\/div>)/', '<br>', $botResponse);
    
    // 9. Thêm CSS và JavaScript cho product cards và chức năng giỏ hàng
    $css_js = '<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
        }
        
        .shoe-assistant-response {
            max-width: 100%;
        }
        
        .product-card {
            background-color: #f9f9f9;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        
        .product-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
            padding-left: 0;
        }
        
        .product-detail {
            margin: 3px 0;
            color: #555;
            font-size: 14px;
        }
        
        .product-image {
            margin: 6px 0;
        }
        
        .product-image img {
            border-radius: 4px;
        }
        
        .product-actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
            flex-wrap: wrap;
        }
        
        .view-details-btn, .add-to-cart-btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .view-details-btn {
            background-color: #5D5CDE;
            color: white;
            border: none;
        }
        
        .add-to-cart-btn {
            background-color: #4caf50;
            color: white;
            border: none;
        }
        
        .view-details-btn:hover {
            background-color: #4A49B8;
        }
        
        .add-to-cart-btn:hover {
            background-color: #3d8b40;
        }
        
        /* Emoji styling */
        span.emoji {
            display: inline-block;
            margin-right: 4px;
            font-size: 14px;
        }
        
        /* Text spacing */
        .product-text, .product-detail {
            padding-left: 0;
            display: flex;
            align-items: flex-start;
        }
        
        /* Line spacing */
        br {
            line-height: 1;
            content: "";
            margin: 1px 0;
            display: block;
        }
        
        /* Regular text spacing */
        div:not(.product-card):not(.product-name):not(.product-detail):not(.product-image):not(.product-link):not(.product-actions) {
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        /* Adjust spacing for point items */
        span.emoji-point {
            margin-bottom: 2px;
        }
        
        /* Remove duplicate emoji */
        .product-name span.emoji + span.emoji,
        .product-detail span.emoji + span.emoji {
            display: none;
        }
        
        /* Toast notification */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .toast {
            background-color: #4caf50;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            animation: slideIn 0.3s, fadeOut 0.5s 2.5s forwards;
            max-width: 300px;
        }
        
        .toast-icon {
            margin-right: 10px;
            font-size: 20px;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
                visibility: hidden;
            }
        }
        
        /* Loading spinner for Ajax */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            to {transform: rotate(360deg);}
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .product-card {
                background-color: #2d3748;
                color: #e2e8f0;
            }
            
            .product-name {
                color: #e2e8f0;
            }
            
            .product-detail {
                color: #cbd5e0;
            }
        }
    </style>
    
    <script>
    // Tạo container cho toast notifications nếu chưa có
    if (!document.querySelector(".toast-container")) {
        const toastContainer = document.createElement("div");
        toastContainer.className = "toast-container";
        document.body.appendChild(toastContainer);
    }
    
    // Thêm event listener cho tất cả các nút "Thêm vào giỏ hàng"
    document.addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("add-to-cart-btn")) {
            // Ngăn chặn hành vi mặc định (như submit form)
            e.preventDefault();
            
            // Hiển thị spinner trong nút 
            const originalButtonText = e.target.innerHTML;
            e.target.innerHTML = `<span class="spinner"></span>Đang thêm...`;
            e.target.disabled = true;
            
            // Lấy dữ liệu sản phẩm
            const productData = JSON.parse(e.target.getAttribute("data-product"));
            
            // Gửi dữ liệu sản phẩm đến server qua AJAX
            const formData = new FormData();
            formData.append("id", productData.id);
            formData.append("name", productData.name);
            formData.append("price", productData.price.replace(/[^\d]/g, "")); // Loại bỏ ký tự không phải số
            formData.append("image", productData.image);
            formData.append("quantity", 1);
            
            fetch("add_to_cart.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Khôi phục nút về trạng thái ban đầu
                e.target.innerHTML = originalButtonText;
                e.target.disabled = false;
                
                if (data.success) {
                    // Hiển thị thông báo thành công
                    showToast(`Đã thêm "${productData.name}" vào giỏ hàng!`);
                    
                    // Gửi thông báo cập nhật giỏ hàng đến trang cha
                    sendCartUpdateToParent(data.cartCount);
                } else {
                    // Hiển thị thông báo lỗi
                    showToast("Không thể thêm vào giỏ hàng: " + data.message, "error");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                // Khôi phục nút về trạng thái ban đầu
                e.target.innerHTML = originalButtonText;
                e.target.disabled = false;
                // Hiển thị thông báo lỗi
                showToast("Đã xảy ra lỗi khi thêm vào giỏ hàng.", "error");
            });
        }
    });
    
    // Hàm gửi thông báo cập nhật giỏ hàng đến trang cha
    function sendCartUpdateToParent(cartCount) {
        // Gửi thông báo đến tất cả các cửa sổ cha có thể chứa iframe này
        if (window.parent) {
            try {
                window.parent.postMessage({
                    type: "UPDATE_CART_COUNT",
                    cartCount: cartCount
                }, "*");
            } catch (e) {
                console.log("Could not send message to parent window:", e);
            }
        }
    }
    
    // Hàm hiển thị toast notification
    function showToast(message, type = "success") {
        const toastContainer = document.querySelector(".toast-container");
        const toast = document.createElement("div");
        toast.className = "toast";
        
        // Đổi màu nền dựa trên loại thông báo
        if (type === "error") {
            toast.style.backgroundColor = "#f44336";
        }
        
        toast.innerHTML = `
            <span class="toast-icon">${type === "success" ? "✅" : "❌"}</span>
            <span>${message}</span>
        `;
        
        toastContainer.appendChild(toast);
        
        // Tự động xóa toast sau 3 giây
        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.visibility = "hidden";
            
            // Xóa phần tử sau khi animation kết thúc
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 3000);
    }
    </script>';
    
    // 10. Thay thế các emoji bằng spans để styling tốt hơn, đảm bảo không bị trùng lặp
    $botResponse = preg_replace('/✨(?!\s*<\/span>)/', '<span class="emoji emoji-product">✨</span>', $botResponse);
    $botResponse = preg_replace('/🔹(?!\s*<\/span>)/', '<span class="emoji emoji-point">🔹</span>', $botResponse);
    $botResponse = preg_replace('/💰(?!\s*<\/span>)/', '<span class="emoji emoji-price">💰</span>', $botResponse);
    $botResponse = preg_replace('/👉(?!\s*<\/span>)/', '<span class="emoji emoji-feature">👉</span>', $botResponse);
    $botResponse = preg_replace('/🎨(?!\s*<\/span>)/', '<span class="emoji emoji-color">🎨</span>', $botResponse);
    $botResponse = preg_replace('/🔗(?!\s*<\/span>)/', '<span class="emoji emoji-link">🔗</span>', $botResponse);
    
    // 11. Loại bỏ emoji trùng lặp
    $botResponse = preg_replace('/<span class="emoji[^"]*">([✨💰🎨👉🔹🔗])<\/span>\s*<span class="emoji[^"]*">\1<\/span>/', '<span class="emoji">$1</span>', $botResponse);
    
    // Bọc response trong div chính
    $botResponse = '<div class="shoe-assistant-response">' . $botResponse . '</div>';
    
    // Thêm CSS và JavaScript vào response
    $response['response'] = $css_js . $botResponse;
} else {
    $response['response'] = "Xin lỗi, bạn có thể diễn đạt lại yêu cầu của mình được không?";
}
} else {
$response['response'] = "Xin lỗi, hiện tại tôi đang gặp một chút trục trặc. Bạn vui lòng thử lại sau nhé!";
}

echo json_encode($response);