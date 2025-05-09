<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:/xampp/htdocs/blood_donation_form/PHPMailer/src/PHPMailer.php';
require 'C:/xampp/htdocs/blood_donation_form/PHPMailer/src/SMTP.php';
require 'C:/xampp/htdocs/blood_donation_form/PHPMailer/src/Exception.php';
require 'db_connection.php'; // Database connection

if (isset($_GET['donor_id']) && isset($_GET['user_name']) && isset($_GET['user_email']) && isset($_GET['user_phone']) && isset($_GET['user_blood_group']) && isset($_GET['user_city'])) {
    $donor_id = $_GET['donor_id'];
    $user_name = $_GET['user_name'];
    $user_email = $_GET['user_email'];
    $user_phone = $_GET['user_phone'];
    $user_blood_group = $_GET['user_blood_group'];
    $user_city = $_GET['user_city'];

    try {
        // Fetch donor details
        $donorQuery = "SELECT * FROM donors WHERE id = :donor_id";
        $donorStmt = $pdo->prepare($donorQuery);
        $donorStmt->bindParam(':donor_id', $donor_id, PDO::PARAM_INT);
        $donorStmt->execute();
        $donor = $donorStmt->fetch(PDO::FETCH_ASSOC);

        if ($donor) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'sriharshapoloju@gmail.com';
                $mail->Password = 'yuji heex ynpo rxme';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('your_email@gmail.com', 'Blood Donation System');
                $mail->addAddress($donor['email']); 
                $mail->Subject = 'Blood Donation Request';
                $mail->isHTML(true);
                $mail->Body = "
                    <p>Dear {$donor['name']},</p>
                    <p>You have received a blood donation request from the following user:</p>
                    <p>
                        <strong>Name:</strong> {$user_name}<br>
                        <strong>Email:</strong> {$user_email}<br>
                        <strong>Phone:</strong> {$user_phone}<br>
                        <strong>Blood Group:</strong> {$user_blood_group}<br>
                        <strong>City:</strong> {$user_city}<br>
                    </p>
                    <p>Please contact above receiver for further details</p>
                    <p>Thank you for your generosity in saving lives!</p>
                ";

                // Send the email
                if ($mail->send()) {
                    echo "<p>Email sent successfully to {$donor['name']} ({$donor['email']}).</p>";
                } else {
                    echo "<p>There was an issue sending the email. Please try again later.</p>";
                }
            } catch (Exception $e) {
                echo "<p>Mailer Error: {$mail->ErrorInfo}</p>";
            }
        } else {
            echo "<p>Donor details not found.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Database error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Invalid request. Required user or donor details are missing.</p>";
}
?>
