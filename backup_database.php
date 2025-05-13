<?php
session_start();

// Allow backup only for logged-in users
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php?access=denied");
    exit();
}

// Database credentials
$servername = "localhost";
$username = "root";
$password = "giocimenI2291928";
$dbname = "videokeman";

// Backup directory
$backupDir = "C:\\xampp\\htdocs\\Videokeman\\backupdb";

// Ensure backup directory exists
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Timestamp and file name
$timestamp = date("Y-m-d_H-i-s");
$backupFile = $backupDir . DIRECTORY_SEPARATOR . $dbname . "_" . $timestamp . ".sql";

// Path to mysqldump
$mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump.exe";

// Command to execute
$command = "\"$mysqldumpPath\" -u $username -p$password $dbname > \"$backupFile\"";

// Run the command
exec($command . " 2>&1", $output, $return_var);

// Show result
if ($return_var === 0) {
    echo "<h3>✅ Backup Successful!</h3>";
    echo "<p>File saved as: <code>$backupFile</code></p>";
    echo "<a href='profile.php'>← Go Back to Profile</a>";
} else {
    echo "<h3>❌ Backup Failed!</h3>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
    echo "<a href='profile.php'>← Go Back to Profile</a>";
}
?>
