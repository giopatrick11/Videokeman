<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php?access=denied");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['song_id'])) {
    echo "No song ID provided.";
    exit;
}

$song_id = (int)$_GET['song_id'];

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = mysqli_real_escape_string($conn, $_POST['song_title']);
    $new_author = mysqli_real_escape_string($conn, $_POST['author']);
    $new_lyrics = mysqli_real_escape_string($conn, $_POST['lyrics']);

    $update = "UPDATE lyrics SET song_title = '$new_title', author = '$new_author', lyrics = '$new_lyrics' 
               WHERE id = $song_id AND user_id = $user_id";

    if (mysqli_query($conn, $update)) {
        header("Location: profile.php");
        exit;
    } else {
        echo "Failed to update song.";
    }
}

// Fetch the current song
$query = "SELECT song_title, author, lyrics FROM lyrics WHERE id = $song_id AND user_id = $user_id";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) != 1) {
    echo "Song not found or you're not allowed to edit it.";
    exit;
}

$song = mysqli_fetch_assoc($result);

// Fetch 5 random songs from the database
$randomSongsQuery = "SELECT id, song_title, author FROM lyrics ORDER BY RAND() LIMIT 5";
$randomSongsResult = mysqli_query($conn, $randomSongsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Song - Videokeman</title>
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
                        echo "<a href='#'>{$char}</a>";
                    }
                ?>
                <a href="#">#</a>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="music-section">
                    <h2>Edit Your Song</h2>
                    <form action="" method="post">
                        <label for="song_title">Song Title:</label><br>
                        <input type="text" name="song_title" id="song_title" value="<?php echo htmlspecialchars($song['song_title']); ?>" required><br><br>

                        <label for="author">Author:</label><br>
                        <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($song['author']); ?>" required><br><br>

                        <label for="lyrics">Lyrics:</label><br>
                        <textarea name="lyrics" id="lyrics" rows="15" cols="50" required><?php echo htmlspecialchars($song['lyrics']); ?></textarea><br><br>

                        <button type="submit">Update Song</button>
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
    </div>
</body>
</html>