<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php?access=denied");
    exit();
}

include 'db.php';

// Check if song_id is set in the URL
if (isset($_GET['song_id'])) {
    $song_id = intval($_GET['song_id']); // Sanitize the input

    // Fetch the song details to get the mp3 path
    $query = "SELECT mp3_path FROM lyrics WHERE id = '$song_id' AND user_id = '{$_SESSION['user_id']}'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $song = mysqli_fetch_assoc($result);
        $mp3_path = $song['mp3_path'];

        // Delete the song from the database
        $delete_query = "DELETE FROM lyrics WHERE id = '$song_id' AND user_id = '{$_SESSION['user_id']}'";
        if (mysqli_query($conn, $delete_query)) {
            // Remove the MP3 file from the server
            if (file_exists($mp3_path)) {
                unlink($mp3_path);
            }
            header("Location: profile.php?remove=success");
            exit();
        } else {
            echo "Error deleting song: " . mysqli_error($conn);
        }
    } else {
        echo "Song not found or you do not have permission to delete this song.";
    }
} else {
    echo "No song ID provided.";
}
?>