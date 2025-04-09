<?php
// Include database configuration
require_once 'config.php';

// Create products table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    color VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $sql)) {
    die("Error creating products table: " . mysqli_error($conn));
}

// Function to clean price values (remove commas and convert to decimal)
function cleanPrice($price)
{
    // Remove commas and convert to float
    return (float) str_replace(',', '', $price);
}

// Check if file exists
$csvFile = '../ShoeMen.csv';
if (!file_exists($csvFile)) {
    die("CSV file not found: $csvFile");
}

// Read CSV file
$file = fopen($csvFile, 'r');
if (!$file) {
    die("Could not open the CSV file");
}

// Skip header row
$header = fgetcsv($file);

// Prepare SQL statement for inserting data
$stmt = $conn->prepare("INSERT INTO products (category, name, color, price, image_url) VALUES (?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("sssds", $category, $name, $color, $price, $image_url);

// Counter for imported records
$importCount = 0;

// Process each row in the CSV file
while (($row = fgetcsv($file)) !== FALSE) {
    // Skip rows with insufficient data
    if (count($row) < 5) {
        continue;
    }

    // Assign values from CSV
    $category = !empty($row[0]) ? $row[0] : 'Uncategorized';
    $name = $row[1];
    $color = $row[2];
    $price = cleanPrice($row[3]);
    $image_url = $row[4];

    // Execute the statement
    if ($stmt->execute()) {
        $importCount++;
    } else {
        echo "Error importing row: " . $stmt->error . "<br>";
    }
}

// Close statement and file
$stmt->close();
fclose($file);

echo "<h2>Import Complete</h2>";
echo "<p>Successfully imported $importCount products into the database.</p>";
echo "<p><a href='../index.php'>Return to homepage</a></p>";
