<?php
declare(strict_types=1);
require_once 'check-loging.php';
checkAdminLogin();
require_once '../config/dbh.conf.php';

// Check if the song ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to the music page or display an error message
    header("Location: music.php");
    exit();
}

// Get the song ID from the URL parameter
$songId = $_GET['id'];

// Check if the song exists in the database
$stmt = $conn->prepare("SELECT COUNT(*) FROM music WHERE id = :songId");
$stmt->bindParam(":songId", $songId, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->fetchColumn() == 0) {
    // Song does not exist, display an error message or redirect to an error page
    die("Song does not exist");
}

try {
    // Prepare the deletion query
    $stmt = $conn->prepare("DELETE FROM music WHERE id = :songId");
    $stmt->bindParam(":songId", $songId, PDO::PARAM_INT);

    // Execute the deletion query
   if ($stmt->execute()) {
    echo 'song deleted';
    header("Location: music.php");
    exit();

   }else {
    die('something went wrong');
   }
    } catch (PDOException $e) {
    // Display an error message or redirect to an error page
    echo "Database error: " . $e->getMessage();
}
