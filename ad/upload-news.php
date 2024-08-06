<?php

require_once 'check-loging.php';
checkAdminLogin();
// Function to compress an image
function compressImage($sourcePath, $targetPath, $quality)
{
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

    return true; // Return the compressed image file name
}

// Function to generate a unique ID
function generateUniqueID()
{
    $id = bin2hex(random_bytes(8)); // Generate a random ID
    return $id;
}

// Function to create a news record in the database
function createNewsRecord($id, $headline, $newsContent, $postedBy, $link, $image)
{
    // Database connection code
    require_once '../config/dbh.conf.php';

    try {
        // Prepare the SQL statement
        $sql = "INSERT INTO news (id, headline, news, posted_by, link, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $headline);
        $stmt->bindParam(3, $newsContent);
        $stmt->bindParam(4, $postedBy);
        $stmt->bindParam(5, $link);
        $stmt->bindParam(6, $image);

        // Execute the statement
        $stmt->execute();

        // Close the database connection
        $conn = null;

        return true; // News record created successfully
    } catch (PDOException $e) {
        // Handle the exception or display an error message
        die('Failed to create news record');
    }
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Generate a unique ID
    $id = generateUniqueID();

    // Retrieve the form data
    $headline = $_POST['news-headline'];
    $newsContent = $_POST['news-content'];
    $postedBy = $_POST['author-name'];
    $link = "?req={$id}&t=news";

    // Upload the image file
    $image = $_FILES['news-image'];


    // Specify the target directory and generate a unique file name
    $targetDirectory = dirname(__DIR__) . '/img';
    $fileName = $id . '_' . $image['name'];
    $targetPath = $targetDirectory . '/' . $fileName;

    // Compress the image
    $compressionQuality = 60; // Adjust the desired compression quality as needed
    if (compressImage($image['tmp_name'], $targetPath, $compressionQuality)) {
        // Create the news record in the database
        createNewsRecord($id, $headline, $newsContent, $postedBy, $link, $fileName);

        // Redirect to a success page or perform any other desired action
        header('Location: dashboard.php');
        exit();
    } else {
        die('Failed to compress image');
    }
}
