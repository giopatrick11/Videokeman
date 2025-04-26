<?php
session_start();
include 'db.php';

// Check if song_id is set
if (isset($_GET['song_id'])) {
    $song_id = intval($_GET['song_id']);

    $query = "SELECT song_title, author, lyrics, mp3_path, user_id FROM lyrics WHERE id = '$song_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $song = mysqli_fetch_assoc($result);
    } else {
        echo "Song not found.";
        exit;
    }
} else {
    echo "No song ID provided.";
    exit;
}

// Get uploader name
$uploader_query = "SELECT username FROM users WHERE id = '{$song['user_id']}'";
$uploader_result = mysqli_query($conn, $uploader_query);
$uploader = mysqli_fetch_assoc($uploader_result);

// Fetch 5 random songs from the database
$randomSongsQuery = "SELECT id, song_title, author FROM lyrics ORDER BY RAND() LIMIT 5";
$randomSongsResult = mysqli_query($conn, $randomSongsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($song['song_title']); ?> - Lyrics</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="background">
    <div class="main-container">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="logo">
                <img src="images/vklogo-removebg-preview.png" alt="Videokeman Logo">
            </div>
            <div class="search-login-container">
                <div class="search-bar">
                    <form action="search.php" method="GET">
                        <input type="text" name="query" placeholder="Search" required>
                        <button type="submit">Search</button>
                    </form>
                </div>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <a href="logout.php"><button class="login-btn">Logout</button></a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation -->
        <div class="nav-bar">
            <a href="index.php">Home &gt;&gt;</a>
            <?php
                foreach (range('A', 'Z') as $char) {
                    echo "<a href='index.php?filter={$char}'>{$char}</a>";
                }
            ?>
            <a href="index.php">#</a>
        </div>

        <!-- Main Content Wrapper -->
        <div class="main-content">
            <!-- Lyrics Content -->
            <div class="music-section">
                <h2><?php echo htmlspecialchars($song['song_title']); ?></h2>
                <p><strong>Artist:</strong> <?php echo htmlspecialchars($song['author']); ?></p>
                <p><strong>Uploaded by:</strong> <?php echo htmlspecialchars($uploader['username']); ?></p>
                <h3>Lyrics:</h3>
                <p style="white-space: pre-line;"><?php echo nl2br(htmlspecialchars($song['lyrics'])); ?></p>
                <audio controls style="margin-top: 10px;">
                    <source src="<?php echo htmlspecialchars($song['mp3_path']); ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <div class="box">
                    <div class="box-header">Random Songs</div>
                    <ul>
                        <?php
                        if ($randomSongsResult && mysqli_num_rows($randomSongsResult) > 0) {
                            while ($randomSong = mysqli_fetch_assoc($randomSongsResult)) {
                                $random_song_id = (int)$randomSong['id'];
                                $random_title = htmlspecialchars($randomSong['song_title']);
                                $random_author = htmlspecialchars($randomSong['author']);
                                echo "<li><a href='lyrics.php?song_id=$random_song_id'>{$random_title} – {$random_author}</a></li>";
                            }
                        } else {
                            echo "<li>No random songs found.</li>";
                        }
                        ?>
                    </ul>
                </div>

                <!-- Videokeman Music -->
                <div class="box">
                    <div class="box-header">Videokeman Music</div>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="submit_lyrics.php">Submit Lyrics</a></li>
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <li><a href="profile.php">Profile</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
