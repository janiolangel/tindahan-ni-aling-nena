<?php
// -- starts and gets session data
session_start();

// -- unsets all of the session variables
$_SESSION = array();

// -- destroy the session RAGHHHHHHH
session_destroy();

// -- redirect to login page
header("Location: index.php");
exit();
?>