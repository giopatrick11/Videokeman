<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "giocimenI2291928"; // Default XAMPP password is empty
$dbname = "videokeman"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
