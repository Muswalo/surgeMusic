<?php
function checkAdminLogin() {
    // Start a session if one is not already running
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user ID is set in the session and is not empty
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        // Redirect to the login page
        header("Location: index.php");
        exit();
    }
}

