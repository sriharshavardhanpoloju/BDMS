<?php
// Database credentials
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password is empty
$dbname = "blood_donation"; // Your database name

try {
    // Create a PDO instance and set error mode to exception
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If there is an error in the connection, it will show a message
    echo "Connection failed: " . $e->getMessage();
}
?>
