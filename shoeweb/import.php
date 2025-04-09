<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Shoe Data</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-lg mx-auto">
            <h1 class="text-2xl font-bold text-center mb-6">Import Shoe Data</h1>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">About This Tool</h2>
                <p class="text-gray-700 mb-4">This tool will import the shoe data from ShoeMen.csv into your database for easier management and display on your website.</p>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <p class="text-blue-700">The import will:</p>
                    <ul class="list-disc ml-5 mt-2 text-blue-700">
                        <li>Create a database table if it doesn't exist</li>
                        <li>Import all products from the CSV file</li>
                        <li>Clean and format price values</li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center">
                <a href="includes/import_shoes.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 inline-block">Start Import</a>
            </div>
        </div>
    </div>
</body>
</html>