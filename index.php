<?php
session_start();
include 'db.php';
// Fetch 5 random songs from the database
$randomSongsQuery = "SELECT id, song_title, author FROM lyrics ORDER BY RAND() LIMIT 5";
$randomSongsResult = mysqli_query($conn, $randomSongsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Videokeman</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin: auto; /* Center the modal content */
        }

        .modal-content h2 {
            color: #2e8b57;
            margin-bottom: 20px;
            text-align: center;
        }

        .modal-content label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .modal-content input[type="text"],
        .modal-content input[type="password"],
        .modal-content input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .modal-content input[type="text"]:focus,
        .modal-content input[type="password"]:focus,
        .modal-content input[type="email"]:focus {
            border-color: #2e8b57;
            outline: none;
        }

        .modal-content button {
            width: 100%;
            padding: 10px;
            background-color: #2e8b57;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-content button:hover {
            background-color: #1d5636;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .success {
            color: green;
            text-align: center;
            margin-top: 10px;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pagination a {
            padding: 6px 12px;
            background-color: #246b45;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .pagination a:hover,
        .pagination a.active {
            background-color: #1d5636;
        }

        .pagination a.disabled {
            pointer-events: none;
            opacity: 0.5;
        }

        .footer {
            background-color: #2f7c52;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            margin-top: 40px;
        }

        .footer a {
            color: #d4f8e8;
            text-decoration: underline;
        }

        .footer a:hover {
            color: #ffffff;
        }

        .music-section {
            min-height: 300px; /* Adjust this value as needed */
        }
    </style>
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
            <?php foreach (range('A', 'Z') as $char): ?>
                <a href="index.php?filter=<?php echo $char; ?>"><?php echo $char; ?></a>
            <?php endforeach; ?>
            <a href="index.php">#</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dynamic Music Section -->
            <div class="music-section">
                <h2>Videokeman mp3 music</h2>
                <ul>
                    <?php
                    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
                    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                    $limit = 25;
                    $offset = ($page - 1) * $limit;

                    if (!empty($filter) && preg_match('/^[A-Z]$/', $filter)) {
                        $query = "SELECT id, song_title, author FROM lyrics WHERE song_title LIKE '$filter%' ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
                        $countQuery = "SELECT COUNT(*) AS total FROM lyrics WHERE song_title LIKE '$filter%'";
                    } else {
                        $query = "SELECT id, song_title, author FROM lyrics ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
                        $countQuery = "SELECT COUNT(*) AS total FROM lyrics";
                    }

                    $result = mysqli_query($conn, $query);
                    $countResult = mysqli_query($conn, $countQuery);
                    $totalRows = mysqli_fetch_assoc($countResult)['total'];
                    $totalPages = ceil($totalRows / $limit);

                    $songs = [];
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = (int)$row['id'];
                            $title = htmlspecialchars($row['song_title']);
                            $author = htmlspecialchars($row['author']);
                            $songs[] = "<li><a href='lyrics.php?song_id=$id'>{$title} – {$author}</a></li>";
                        }
                    } else {
                        $songs[] = "<li>No lyrics found.</li>";
                    }

                    // Fill the remaining space with empty list items if there are less than 25 songs
                    $remainingSongs = 25 - count($songs);
                    for ($i = 0; $i < $remainingSongs; $i++) {
                        $songs[] = "<li>&nbsp;</li>"; // Empty list item
                    }

                    // Output all songs
                    echo implode("", $songs);
                    ?>
                </ul>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?php echo http_build_query(['filter' => $filter, 'page' => $page - 1]); ?>">Previous</a>
                        <?php else: ?>
                            <a class="disabled">Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?<?php echo http_build_query(['filter' => $filter, 'page' => $i]); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?<?php echo http_build_query(['filter' => $filter, 'page' => $page + 1]); ?>">Next</a>
                        <?php else: ?>
                            <a class="disabled">Next</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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
                        <li><a href="#" id="submitLyricsLink">Submit Lyrics</a></li>
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <li><a href="profile.php">Profile</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    &copy; <?php echo date("Y"); ?> Videokeman. All rights reserved. | Contact us at <a href="mailto:giopatrick11@gmail.com">giopatrick11@gmail.com</a>
</div>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" id="loginClose">&times;</span>
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="loginUsername">Username:</label>
            <input type="text" id="loginUsername" name="username" required>
            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" required>
            <button type="submit">Login</button>
        </form>
        <?php
        if (isset($_GET['login'])) {
            if ($_GET['login'] == 'nouser') echo "<p class='error'>Username does not exist.</p>";
            else if ($_GET['login'] == 'wrongpassword') echo "<p class='error'>Incorrect password.</p>";
            else if ($_GET['login'] == 'success') echo "<p class='success'>Login successful!</p>";
        }
        ?>
    </div>
</div>

<!-- Signup Modal -->
<div id="signupModal" class="modal">
    <div class="modal-content">
        <span class="close" id="signupClose">&times;</span>
        <h2>Sign Up</h2>
        <form action="signup.php" method="post">
            <label for="signupUsername">Username:</label>
            <input type="text" id="signupUsername" name="username" required>
            <label for="signupPassword">Password:</label>
            <input type="password" id="signupPassword" name="password" required>
            <label for="signupEmail">Email:</label>
            <input type="email" id="signupEmail" name="email" required>
            <button type="submit">Sign Up</button>
        </form>
        <?php
        if (isset($_GET['signup'])) {
            if ($_GET['signup'] == 'userexists') echo "<p class='error'>Username already exists.</p>";
            else if ($_GET['signup'] == 'success') echo "<p class='success'>Registration successful! You can now log in.</p>";
            else if ($_GET['signup'] == 'error') echo "<p class='error'>There was an error processing your registration.</p>";
        }
        ?>
    </div>
</div>

<!-- Scripts -->
<script>
    const loginModal = document.getElementById("loginModal");
    const signupModal = document.getElementById("signupModal");
    const loginBtn = document.getElementById("loginBtn");
    const signupBtn = document.getElementById("signupBtn");
    const loginClose = document.getElementById("loginClose");
    const signupClose = document.getElementById("signupClose");

    loginBtn && (loginBtn.onclick = () => loginModal.style.display = "block");
    signupBtn && (signupBtn.onclick = () => signupModal.style.display = "block");
    loginClose.onclick = () => loginModal.style.display = "none";
    signupClose.onclick = () => signupModal.style.display = "none";

    window.onclick = function (event) {
        if (event.target == loginModal) loginModal.style.display = "none";
        if (event.target == signupModal) signupModal.style.display = "none";
    };

    window.onload = function () {
        const params = new URLSearchParams(window.location.search);
        if (params.get('login') === 'nouser' || params.get('login') === 'wrongpassword') {
            loginModal.style.display = "block";
        }
        if (params.get('signup') === 'userexists') {
            signupModal.style.display = "block";
        }
    };

    const submitLink = document.getElementById("submitLyricsLink");
    submitLink.addEventListener("click", function (e) {
        e.preventDefault();
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            window.location.href = 'submit_lyrics.php';
        <?php else: ?>
            alert("Please log in or sign up to submit lyrics.");
        <?php endif; ?>
    });
</script>
</body>
</html>