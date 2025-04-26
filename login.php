<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Username exists
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Correct password
        $_SESSION['logged_in'] = true; // Set session variable
        $_SESSION['user_id'] = $row['id']; // Store user ID in session
        header("Location: index.php?login=success");
        exit();
    } else {
        // Wrong password
        header("Location: index.php?login=wrongpassword");
        exit();
    }
} else {
    // Username not found
    header("Location: index.php?login=nouser");
    exit();
}
?>
