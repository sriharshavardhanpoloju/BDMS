<?php
// Database credentials
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password is empty
$dbname = "blood_donation"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form data
$blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
$city = mysqli_real_escape_string($conn, $_POST['city']);

// SQL query to search for matching donors
$sql = "SELECT name, email, phone, age, blood_group, gender, address, city, state, country FROM donors 
        WHERE blood_group = '$blood_group' AND city LIKE '%$city%'";

// Execute the query
$result = $conn->query($sql);

// Check if any donors are found
if ($result->num_rows > 0) {
    echo "<h2>Available Donors:</h2>";
    echo "<table border='1'>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Age</th>
                <th>Blood Group</th>
                <th>Gender</th>
                <th>Address</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
            </tr>";

    // Output the data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['name'] . "</td>
                <td>" . $row['email'] . "</td>
                <td>" . $row['phone'] . "</td>
                <td>" . $row['age'] . "</td>
                <td>" . $row['blood_group'] . "</td>
                <td>" . $row['gender'] . "</td>
                <td>" . $row['address'] . "</td>
                <td>" . $row['city'] . "</td>
                <td>" . $row['state'] . "</td>
                <td>" . $row['country'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No matching donors found.";
}

// Close the connection
$conn->close();
?>
