<?php

$page_title = "Chat Support";

require_once 'includes/header.php';

?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-blue-600 text-white">
                <h2 class="text-xl font-bold">Chat with Our AI Assistant</h2>
                <p class="text-sm">Ask me anything about our shoes!</p>
            </div>

            <!-- Chat Messages Container -->
            <div id="chat-messages" class="p-4 h-[500px] overflow-y-auto space-y-4 bg-gray-50 dark:bg-gray-900">
                <!-- Messages will be inserted here -->
            </div>

            <!-- Chat Input -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <form id="chat-form" class="flex gap-2">
                    <input type="text" id="user-input"
                        class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                        placeholder="Type your message here...">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-300">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');
    let conversationHistory = [];

    // Add initial bot message
    addMessage("Hi! I'm your AI shopping assistant. How can I help you today?", 'bot');

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = userInput.value.trim();
        if (!message) return;

        // Add user message to chat
        addMessage(message, 'user');
        userInput.value = '';

        // Save to conversation history
        conversationHistory.push({
            role: 'user',
            content: message
        });

        // Show typing indicator
        const typingIndicator = addTypingIndicator();

        try {
            // Send message to bot
            const response = await fetch('chat_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    history: conversationHistory
                })
            });

            const data = await response.json();

            // Remove typing indicator
            typingIndicator.remove();

            // Add bot response to chat
            if (data.success) {
                // Process the response to ensure it renders correctly
                addMessage(data.response, 'bot');

                // Strip HTML for conversation history to avoid double rendering
                const plainTextResponse = data.response.replace(/<[^>]*>/g, '');
                conversationHistory.push({
                    role: 'assistant',
                    content: plainTextResponse
                });
            } else {
                addMessage("Sorry, I'm having trouble understanding. Could you rephrase that?", 'bot');
            }
        } catch (error) {
            console.error('Error:', error);
            typingIndicator.remove();
            addMessage("Sorry, I encountered an error. Please try again.", 'bot');
        }
    });

    function addMessage(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

        const messageBubble = document.createElement('div');

        // Adjust width based on content type and sender
        const hasProductCard = message.includes('product-card');

        messageBubble.className = `${
            hasProductCard && sender === 'bot' ? 'w-[85%] md:w-[75%]' : 'max-w-[75%]'
        } rounded-lg ${
            sender === 'user'
            ? 'bg-blue-600 text-white'
            : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700'
        } ${
            hasProductCard && sender === 'bot' ? 'p-2 overflow-hidden' : 'p-3'
        } message-bubble`;

        // Set innerHTML for rendering the content with HTML
        messageBubble.innerHTML = message;

        // Find all product cards and fix their styles for chat context
        if (hasProductCard) {
            setTimeout(() => {
                const productCards = messageBubble.querySelectorAll('.product-card');
                productCards.forEach(card => {
                    // Make sure product cards adapt well to the chat bubble
                    card.style.margin = '8px 0';
                    card.style.width = '100%';
                });

                // Re-scroll after images load to ensure everything is visible
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 100);
        }

        messageDiv.appendChild(messageBubble);
        chatMessages.appendChild(messageDiv);
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Add an additional scroll after images load
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 500);
    }

    function addTypingIndicator() {
        const indicatorDiv = document.createElement('div');
        indicatorDiv.className = 'flex justify-start';
        
        const indicator = document.createElement('div');
        indicator.className = 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg p-3';
        indicator.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
        
        indicatorDiv.appendChild(indicator);
        chatMessages.appendChild(indicatorDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        return indicatorDiv;
    }
});
</script>

<style>
/* Base styles for typing indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    background-color: #90909090;
    border-radius: 50%;
    animation: bounce 1.4s infinite ease-in-out;
}

.typing-indicator span:nth-child(1) {
    animation-delay: -0.32s;
}

.typing-indicator span:nth-child(2) {
    animation-delay: -0.16s;
}

@keyframes bounce {
    0%, 80%, 100% {
        transform: scale(0);
    }
    40% {
        transform: scale(1);
    }
}

/* Product card styling fixes for chat context */
.message-bubble .product-card {
    width: 100%;
    margin: 8px 0;
    border-radius: 6px;
}

/* Make sure images don't overflow */
.message-bubble img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

/* Fix spacing issues in messages */
.message-bubble > div {
    margin-bottom: 6px;
}

/* Improve link appearance in chat */
.message-bubble a {
    color: #4a6cf7;
    text-decoration: none;
}

.message-bubble a:hover {
    text-decoration: underline;
}

/* Ensure proper emoji spacing in chat */
.message-bubble .emoji {
    display: inline-block;
    margin-right: 4px;
}

/* Fix spacing between product sections */
#chat-messages .message-bubble {
    overflow-wrap: break-word;
}

/* Dark mode tweaks for product cards */
@media (prefers-color-scheme: dark) {
    .message-bubble .product-card {
        background-color: #2d3748 !important;
        color: #e2e8f0 !important;
    }

    .message-bubble .product-detail,
    .message-bubble .product-name,
    .message-bubble .product-text {
        color: #e2e8f0 !important;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>

<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="vi">
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
    </style>
</head>
<body>
