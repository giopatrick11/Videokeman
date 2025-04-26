<?php
session_start();
include 'db.php';

// Check if the search query is set
if (isset($_GET['query'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['query']);

    // Query to search for songs containing the search query
    $query = "SELECT id, song_title, author FROM lyrics WHERE song_title LIKE '%$search_query%' OR author LIKE '%$search_query%'";
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
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

        <div class="nav-bar">
            <a href="index.php">Home &gt;&gt;</a>
        </div>

        <div class="main-content">
            <div class="music-section">
                <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
                <ul>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = (int)$row['id'];
                            $title = htmlspecialchars($row['song_title']);
                            $author = htmlspecialchars($row['author']);
                            echo "<li><a href='lyrics.php?song_id=$id'>{$title} – {$author}</a></li>";
                        }
                    } else {
                        echo "<li>No results found.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
</body>
</html>