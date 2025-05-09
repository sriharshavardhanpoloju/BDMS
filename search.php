<?php
// Include database connection
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $blood_group = htmlspecialchars(trim($_POST['blood_group']));
    $city = htmlspecialchars(trim($_POST['city']));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p>Invalid email format.</p>";
        exit;
    }

    // Validate phone number (example: only digits allowed)
    if (!preg_match('/^\d+$/', $phone)) {
        echo "<p>Invalid phone number. Only digits are allowed.</p>";
        exit;
    }

    try {
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

        if (empty($donors)) {
            echo "<p>No donors found for the selected blood group and city.</p>";
        } else {
            echo "<h3>Matching Donors:</h3>";
            echo "<ul>";
            foreach ($donors as $donor) {
                // Correct URL with all parameters encoded properly
                $url = "contact_donor.php?donor_id=" . urlencode($donor['id']) .
                       "&user_name=" . urlencode($name) .
                       "&user_email=" . urlencode($email) .
                       "&user_phone=" . urlencode($phone) .
                       "&user_blood_group=" . urlencode($blood_group) .
                       "&user_city=" . urlencode($city);

                // Display donor information
                echo "<li>";
                echo "Name: " . htmlspecialchars($donor['name']) . "<br>";
                echo "Email: " . htmlspecialchars($donor['email']) . "<br>";
                echo "Phone: " . htmlspecialchars($donor['phone']) . "<br>"; // Added mobile number
                echo "<a href='$url'>Contact Donor</a>";
                echo "</li><br>";
            }
            echo "</ul>";
        }
    } catch (PDOException $e) {
        // Log error instead of displaying it
        error_log("Database error: " . $e->getMessage());
        echo "<p>There was an error processing your request. Please try again later.</p>";
    }
} else {
    echo "<p>Invalid request. Please submit the form.</p>";
}
?>
