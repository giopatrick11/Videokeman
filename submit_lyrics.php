<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php?access=denied");
    exit();
}

include 'db.php'; // Include your database connection

// Fetch 5 random songs from the database
$randomSongsQuery = "SELECT id, song_title, author FROM lyrics ORDER BY RAND() LIMIT 5";
$randomSongsResult = mysqli_query($conn, $randomSongsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Lyrics - Videokeman</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="background">
        <div class="main-container">
            <!-- Header -->
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
                    <?php else: ?>
                        <button class="login-btn" id="loginBtn">Login</button>
                        <button class="signup-btn" id="signupBtn">Sign Up</button>
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

            <!-- Main Content -->
            <div class="main-content">
                <div class="music-section">
                    <h2>Submit Lyrics</h2>
                    <form action="submit_lyrics_process.php" method="post" enctype="multipart/form-data">
                        <label for="title">Song Title:</label><br>
                        <input type="text" id="title" name="title" required><br><br>

                        <label for="author">Author:</label><br>
                        <input type="text" id="author" name="author" required><br><br>

                        <label for="lyrics">Lyrics:</label><br>
                        <textarea id="lyrics" name="lyrics" rows="6" cols="50" required></textarea><br><br>

                        <label for="mp3">Upload MP3:</label><br>
                        <input type="file" id="mp3" name="mp3" accept=".mp3" required><br><br>

                        <button type="submit">Submit</button>
                    </form>
                </div>

                <!-- Sidebar -->
                <div class="sidebar">
                    <div class="box">
                        <div class="box-header">Random Songs</div>
                        <ul>
                            <?php
                            if ($randomSongsResult && mysqli_num_rows($randomSongsResult) > 0) {
                                while ($song = mysqli_fetch_assoc($randomSongsResult)) {
                                    $song_id = (int)$song['id'];
                                    $title = htmlspecialchars($song['song_title']);
                                    $author = htmlspecialchars($song['author']);
                                    echo "<li><a href='lyrics.php?song_id=$song_id'>{$title} – {$author}</a></li>";
                                }
                            } else {
                                echo "<li>No random songs found.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="box">
                        <div class="box-header">Videokeman Music</div>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="submit_lyrics.php">Submit Lyrics</a></li>
                            <li><a href="profile.php">Profile</a></li>
                        </ul>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</body>
</html>
