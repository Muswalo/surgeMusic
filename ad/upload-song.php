<?php
require_once 'check-loging.php';
checkAdminLogin();
require_once '../config/dbh.conf.php';
// Function to generate a unique ID
function generateUniqueId() {
    return bin2hex(random_bytes(8));
}


// Function to compress an image
function compressImage($sourcePath, $targetPath, $quality) {
    $imageInfo = getimagesize($sourcePath);
    $mimeType = $imageInfo['mime'];

    // Create an image resource based on the image type
    switch ($mimeType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        default:
            return false; // Unsupported image type
    }

    // Compress and save the image
    switch ($mimeType) {
        case 'image/jpeg':
            imagejpeg($image, $targetPath, $quality);
            break;
        case 'image/png':
            imagepng($image, $targetPath, floor($quality / 10));
            break;
        case 'image/gif':
            imagegif($image, $targetPath);
            break;
    }

    // Free up memory by destroying the image resource
    imagedestroy($image);

    return basename($targetPath); // Return the compressed image file name
}


// Function to upload a song
function uploadSong($file, $targetDirectory) {
    // Check if the file was uploaded without errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $file['tmp_name'];

        // Generate a unique file name
        $fileName = uniqid() . '_' . $file['name'];

        // Set the target path to the specified directory with the generated file name
        $targetPath = $targetDirectory . '/' . $fileName;

        // Move the temporary file to the target path
        if (move_uploaded_file($tempFilePath, $targetPath)) {
            return $fileName; // Return the generated file name as the upload was successful
        }
    }

    return false; // Upload failed
}

// Function to create a music record in the database
function createMusicRecord($title, $artistName, $songArtwork, $songFile, $conn,$targetDirectory) {
    $id = generateUniqueId(); // Generate a unique ID
    $songFileName = $songFile; // Get the original song file name

    // Compress and store the song artwork
    // $songImageName = uniqid('image_').microtime(true).$songArtwork['name'];
    $compressedArtwork = compressImage($songArtwork['tmp_name'], $targetDirectory . '/' . $id . '.jpg', 60);
    $link = "?req={$id}&t=music";
    $query = "INSERT INTO music (id, title, artist_name, song, link, song_art_work) VALUES (:id, :title, :artistName, :song, :link, :songArtwork)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':artistName', $artistName);
    $stmt->bindParam(':song', $songFileName);
    $stmt->bindParam(':link', $link);
    $stmt->bindParam(':songArtwork', $compressedArtwork);

    if ($stmt->execute()) {
        return true; // Record created successfully
    } else {
        return false; // Record creation failed
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['song-title'], $_POST['artist-name'], $_FILES['song-file'], $_FILES['song-artwork'])) {
    $title = $_POST['song-title'];
    $artistName = $_POST['artist-name'];
    $songFile = $_FILES['song-file'];
    $songArtwork = $_FILES['song-artwork'];

    $targetDirectory = dirname(__DIR__).'/audio';

    //Upload the song file
    $fileName = uploadSong($songFile, $targetDirectory);

    if ($fileName) {
        // Create the music record in the database
        $result = createMusicRecord($title, $artistName, $songArtwork, $fileName, $conn, dirname(__DIR__).'/img');

        if ($result) {
            echo "Music record created successfully.";
            sleep(2);
            header('Location: dashboard.php');
        } else {
            echo "Failed to create music record.";
        }
    } else {
        echo "Song upload failed.";
    }
}
