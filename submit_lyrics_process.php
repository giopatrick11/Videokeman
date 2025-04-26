<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: index.php?access=denied");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize form inputs
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $lyrics = mysqli_real_escape_string($conn, $_POST['lyrics']);
    
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Handle the MP3 file upload
    $mp3 = $_FILES['mp3'];
    $mp3Name = basename($mp3['name']);
    $mp3Tmp = $mp3['tmp_name'];
    $uploadDir = "uploads/";
    $mp3Path = $uploadDir . time() . "_" . $mp3Name;

    // Create the uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Check file extension
    $fileExt = strtolower(pathinfo($mp3Path, PATHINFO_EXTENSION));
    if ($fileExt !== 'mp3') {
        echo "Only MP3 files are allowed.";
        exit();
    }

    // Move the uploaded file
    if (move_uploaded_file($mp3Tmp, $mp3Path)) {
        // Insert data into the database including user_id
        $sql = "INSERT INTO lyrics (song_title, author, lyrics, mp3_path, user_id) 
                VALUES ('$title', '$author', '$lyrics', '$mp3Path', '$user_id')";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?success=true");
            exit();
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    } else {
        echo "Error uploading the MP3 file.";
    }
}
?>
