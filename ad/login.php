<?php
declare(strict_types=1);
session_start();
require_once '../config/dbh.conf.php';

function login($username, $password, $conn){
    try {
        // Prepare the query
        $stmt = $conn->prepare("SELECT id FROM admin WHERE user = :username AND `password` = :p");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':p', $password);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if user exists
        if ($stmt->rowCount() > 0) {
            // Fetch the user ID
            $userId = $row['id'];

            // Store the user ID in the session
            $_SESSION['user_id'] = $userId;

            // Redirect to the admin dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid credentials
            return "Invalid username or password.";
        }
    } catch (PDOException $e) {
        return 'coudnt login. contact sys admin';
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $loginResult = login($username,hash('sha256', $password), $conn);

    if ($loginResult !== true) {
        echo $loginResult;
    }
}else {
    header('Loaction: index.php');
    exit();
}

