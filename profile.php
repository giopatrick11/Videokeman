<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $user_id = $_SESSION['user_id'];

    include 'db.php';
    $query = "SELECT username, email FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    if (!$user) {
        echo "User  not found.";
        exit;
    }
} else {
    echo "You need to log in to view your profile.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Videokeman - Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container {
            width: 100%;
            padding: 20px;
            background-color: #fdf0cc;
            border-radius: 6px;
        }

        .profile-container h2 {
            margin-bottom: 20px;
            color: #aa4413;
            border-bottom: 2px solid #e0c27c;
            padding-bottom: 10px;
        }

        .profile-info {
            background-color: white;
            padding: 15px 20px;
            margin-bottom: 30px;
            border-radius: 6px;
            border: 1px solid #e0c27c;
        }

        .profile-info p {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .profile-songs h3 {
            color: #aa4413;
            margin-bottom: 15px;
        }

        .song-item {
            background-color: white;
            padding: 15px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .song-item strong {
            display: inline-block;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .song-item a {
            color: #c4511c;
            margin-right: 10px;
            text-decoration: none;
            font-size: 14px;
        }

        .song-item a:hover {
            text-decoration: underline;
        }

        .backup-btn {
            padding: 6px 12px;
            background-color: #246b45;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px; /* Add some space above the button */
        }

        .backup-btn:hover {
            background-color: #1d5636;
        }
    </style>
</head>
<body>
    <div class="background">
        <div class="main-container">
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
                    <a href="logout.php"><button class="login-btn">Logout</button></a>
                </div>
            </div>

            <div class="nav-bar">
                <a href="index.php">Home &gt;&gt;</a>
                <a href="#">#</a>
            </div>

            <div class="main-content">
                <div class="profile-container">
                    <h2>Your Profile</h2>

                    <div class="profile-info">
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <div class="profile-songs">
                        <h3>Your Submitted Songs</h3>
                        <?php
                        $query = "SELECT song_title, author, id FROM lyrics WHERE user_id = '$user_id' ORDER BY created_at DESC";
                        $result = mysqli_query($conn, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $song_title = htmlspecialchars($row['song_title']);
                                $author = htmlspecialchars($row['author']);
                                $song_id = $row['id'];
                                echo "<div class='song-item'>
                                        <strong>$song_title</strong> by $author<br>
                                        <a href='edit_song.php?song_id=$song_id'>Edit</a> | 
                                        <a href='remove.php?song_id=$song_id' onclick='return confirm(\"Are you sure you want to remove this song?\");'>Remove</a>
                                      </div>";
                            }
                        } else {
                            echo "<p>You haven't submitted any songs yet.</p>";
                        }
                        ?>
                    </div>

                    <!-- Backup Database Button for Admin -->
                    <?php if ($user['username'] === 'admin'): ?>
                        <form action="backup_database.php" method="post">
                            <button type="submit" class="backup-btn">Backup Database</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
