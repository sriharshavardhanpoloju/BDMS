<?php
// Include database connection
require 'path/to/db_connection.php';  // Adjust the path

if (isset($_GET['action']) && $_GET['action'] == 'accept') {
    $donor_id = $_GET['donor_id'];  // Get donor ID from the URL
    $user_email = $_GET['user_email'];  // Get the recipient's email from the URL
    
    // Assuming that recipient details are stored in a table 'users' (or any table where recipient data is stored)
    try {
        // Fetch donor details
        $stmt = $pdo->prepare("SELECT * FROM donors WHERE id = :donor_id");
        $stmt->bindParam(':donor_id', $donor_id);
        $stmt->execute();
        $donor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Fetch recipient details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :user_email");
        $stmt->bindParam(':user_email', $user_email);
        $stmt->execute();
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if donor and recipient exist
        if ($donor && $recipient) {
            // Get current date for donation
            $donation_date = date('Y-m-d');
            $request_date = date('Y-m-d', strtotime($donor['request_date']));  // Assuming the donor's request date is in their record

            // Insert into accepted_donations table
            $insertQuery = "INSERT INTO accepted_donations (donor_id, donor_name, donor_email, recipient_id, recipient_name, recipient_email, donation_date, request_date) 
                            VALUES (:donor_id, :donor_name, :donor_email, :recipient_id, :recipient_name, :recipient_email, :donation_date, :request_date)";
            
            $stmt = $pdo->prepare($insertQuery);
            $stmt->bindParam(':donor_id', $donor['id']);
            $stmt->bindParam(':donor_name', $donor['name']);
            $stmt->bindParam(':donor_email', $donor['email']);
            $stmt->bindParam(':recipient_id', $recipient['id']);
            $stmt->bindParam(':recipient_name', $recipient['name']);
            $stmt->bindParam(':recipient_email', $recipient['email']);
            $stmt->bindParam(':donation_date', $donation_date);
            $stmt->bindParam(':request_date', $request_date);
            $stmt->execute();

            echo "Donation accepted successfully! Thank you for your generosity.";
        } else {
            echo "Donor or recipient not found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
