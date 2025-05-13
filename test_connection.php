<?php

$db_host = "localhost"; // Change if your MySQL server is on a different host
$db_user = "root";      // Change to your MySQL username
$db_pass = "";          // Change to your MySQL password
$db_name = "tindahan_system";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo "<h2>Database connection successful!</h2>";
echo "<p>Connected to: " . $db_name . "</p>";


$conn->close();
?>