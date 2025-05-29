<?php
// -- database connection configs
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "tindahan_system";

// -- connect to database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// -- check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
