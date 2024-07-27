<?php
declare(strict_types=1);
// Set the appropriate headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the type and id parameters are present in the request
    if (isset($_POST['type']) && isset($_POST['id'])) {
        // Get the type and id values from the request
        $type = $_POST['type'];
        $id = $_POST['id'];

        // Perform the increment operation based on the type
        if ($type === 'downloads') {
            $newNumber = incrementDownloads($id);
        } elseif ($type === 'plays') {
            $newNumber = incrementPlays($id);
        } else {
            // Create an error response object
            $response = [
                'status' => 'error',
                'message' => 'Invalid type parameter'
            ];

            // Send the response as JSON
            echo json_encode($response);
            exit;
        }

        // Create a success response object
        $response = [
            'status' => 'success',
            'message' => ucfirst($type) . ' incremented',
            'newNumber' => $newNumber
        ];

        // Send the response as JSON
        echo json_encode($response);

    } else {
        // Create an error response object
        $response = [
            'status' => 'error',
            'message' => 'Missing parameters'
        ];

        // Send the response as JSON
        echo json_encode($response);
    }
} else {
    // Create an error response object
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method'
    ];

    // Send the response as JSON
    echo json_encode($response);
}



// Function to increment the downloads count and return the new value
function incrementDownloads($id)
{
    // Get the database connection object
    $conn = include 'config/dbh.conf.php';

    try {
        // Update the downloads count in the database
        $query = "UPDATE music SET downloads = downloads + 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        // Get the new downloads count
        $query = "SELECT downloads FROM music WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $newNumber = $stmt->fetchColumn();

        return $newNumber;
    } catch (PDOException $e) {
        // Create an error response object
        $response = [
            'status' => 'error',
            'message' => 'Failed to increment downloads'
        ];

        // Send the response as JSON
        echo json_encode($response);
        exit;
    }
}



// Function to increment the plays count and return the new value
function incrementPlays($id)
{
    // Get the database connection object
    $conn = include 'config/dbh.conf.php';

    try {
        // Update the plays count in the database
        $query = "UPDATE music SET plays = plays + 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        // Get the new plays count
        $query = "SELECT plays FROM music WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $newNumber = $stmt->fetchColumn();
        return $newNumber;
    } catch (PDOException $e) {
        // Create an error response object
        $response = [
            'status' => 'error',
            'message' => 'Failed to increment plays'
        ];

        // Send the response as JSON
        echo json_encode($response);
        exit;
    }
}
