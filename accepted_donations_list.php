<?php
// Include your database connection
require 'path/to/db_connection.php';  // Adjust the path

try {
    // Fetch all accepted donations
    $query = "SELECT ad.donor_name, ad.donor_email, ad.recipient_name, ad.recipient_email, ad.donation_date, ad.request_date
              FROM accepted_donations ad
              ORDER BY ad.donation_date DESC";  // Order by donation date (latest first)

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $accepted_donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display donations
    if ($accepted_donations) {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Accepted Donations</title>
            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid black;
                }
                th, td {
                    padding: 10px;
                    text-align: left;
                }
            </style>
        </head>
        <body>
            <h1>Accepted Donations</h1>
            <table>
                <thead>
                    <tr>
                        <th>Donor Name</th>
                        <th>Donor Email</th>
                        <th>Recipient Name</th>
                        <th>Recipient Email</th>
                        <th>Donation Date</th>
                        <th>Request Date</th>
                    </tr>
                </thead>
                <tbody>";

        // Loop through each donation record and display it
        foreach ($accepted_donations as $donation) {
            echo "<tr>
                    <td>{$donation['donor_name']}</td>
                    <td>{$donation['donor_email']}</td>
                    <td>{$donation['recipient_name']}</td>
                    <td>{$donation['recipient_email']}</td>
                    <td>{$donation['donation_date']}</td>
                    <td>{$donation['request_date']}</td>
                  </tr>";
        }

        echo "</tbody>
            </table>
        </body>
        </html>";
    } else {
        echo "No accepted donations yet.";
    }
} catch (PDOException $e) {
    echo "Error fetching donations: " . $e->getMessage();
}
?>
