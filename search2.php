<?php
// Include PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:/xampp/htdocs/blood_donation_form/PHPMailer/src/PHPMailer.php';
require 'C:/xampp/htdocs/blood_donation_form/PHPMailer/src/SMTP.php';
require 'C:/xampp/htdocs/blood_donation_form/PHPMailer/src/Exception.php';

// Include database connection
require 'db_connection.php'; // Replace with your actual database connection

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $name = $_POST['name'];
    $email = $_POST['email'];
    $blood_group = $_POST['blood_group'];
    $city = $_POST['city'];

    // Prepare the SQL query to search for matching donors
    $query = "SELECT * FROM donors WHERE blood_group = :blood_group AND city = :city";
    $stmt = $pdo->prepare($query);
    
    // Bind parameters to prevent SQL injection
    $stmt->bindParam(':blood_group', $blood_group);
    $stmt->bindParam(':city', $city);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $donors = $stmt->fetchAll();

    // Check if any donors match the search criteria
    if (empty($donors)) {
        echo "<p>No donors found for the selected blood group and city.</p>";
    } else {
        // Display the matching donors and update the user details in the database
        echo "<h3>Matching Donors:</h3>";
        echo "<ul>";
        
        foreach ($donors as $donor) {
            echo "<li>";
            echo "Name: " . $donor['name'] . "<br>";
            echo "Email: " . $donor['email'] . "<br>";

            // Update user details in the database (mark the request as sent or add a request entry)
            $updateQuery = "INSERT INTO user_requests (user_name, user_email, donor_id, donor_name, donor_email, blood_group, city) 
                            VALUES (:user_name, :user_email, :donor_id, :donor_name, :donor_email, :blood_group, :city)";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':user_name', $name);
            $updateStmt->bindParam(':user_email', $email);
            $updateStmt->bindParam(':donor_id', $donor['id']);
            $updateStmt->bindParam(':donor_name', $donor['name']);
            $updateStmt->bindParam(':donor_email', $donor['email']);
            $updateStmt->bindParam(':blood_group', $blood_group);
            $updateStmt->bindParam(':city', $city);
            $updateStmt->execute();
            
            // Generate the contact donor link
            echo "<a href='contact_donor.php?donor_id=" . $donor['id'] . "&user_email=$email'>Contact Donor</a>";
            echo "</li><br>";
        }
        echo "</ul>";
    }
}

// Code to handle "Contact Donor" when clicked
if (isset($_GET['donor_id']) && isset($_GET['user_email'])) {
    $donor_id = $_GET['donor_id'];
    $user_email = $_GET['user_email'];

    // Get donor details from the database
    $query = "SELECT * FROM donors WHERE id = :donor_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':donor_id', $donor_id, PDO::PARAM_INT);
    $stmt->execute();
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get user details from the database
    $userQuery = "SELECT * FROM users WHERE email = :user_email";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($donor && $user) {
        // Create an instance of PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'sriharshapoloju@gmail.com'; // Your email
            $mail->Password = 'iver tonw vuhb lokm'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set email details
            $acceptLink = "http://yourdomain.com/response.php?action=accept&donor_id={$donor['id']}&user_email={$user_email}";
            $rejectLink = "http://yourdomain.com/response.php?action=reject&donor_id={$donor['id']}&user_email={$user_email}";

            $mail->setFrom('your_email@example.com', 'Blood Donation System');
            $mail->addAddress($donor['email']); // Send email to donor
            $mail->Subject = 'Blood Donation Request';
            $mail->isHTML(true);
            $mail->Body = "
                <p>Dear {$donor['name']},</p>
                <p>You have received a new blood donation request from the following user:</p>
                <p>
                    <strong>Name:</strong> {$user['name']}<br>
                    <strong>Email:</strong> {$user['email']}<br>
                    <strong>Phone:</strong> {$user['phone']}<br>
                </p>
                <p>Please choose your response:</p>
                <p>
                    <a href='{$acceptLink}'>Accept</a> | <a href='{$rejectLink}'>Reject</a>
                </p>
                <p>Thank you for being a valuable part of our blood donation system!</p>";

            // Send the email
            if ($mail->send()) {
                echo 'Request email sent to donor successfully.';
            }
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'Donor or user details not found.';
    }
}
?>
