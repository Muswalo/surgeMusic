<?php

function countPageVisit($conn)
{
    // Check if a session is active
    if (isset($_SESSION['visited']) && $_SESSION['visited'] == true) {
        // Session is active, do not count visit
        return;
    }

    // Increment the site visits count in the database
    try {
        // Fetch the current site visits count
        $stmt = $conn->query("SELECT site_visits FROM admin");
        $siteVisits = $stmt->fetchColumn();

        // Increment the site visits count
        $siteVisits++;

        // Update the site visits count in the database
        $stmt = $conn->prepare("UPDATE admin SET site_visits = :siteVisits");
        $stmt->bindValue(':siteVisits', $siteVisits, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['visited'] = true;
    } catch (PDOException $e) {
        // Handle the database error
        echo "Database error: " . $e->getMessage();
    }
}


