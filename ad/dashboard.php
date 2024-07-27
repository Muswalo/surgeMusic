<?php
declare(strict_types=1);
require_once 'check-loging.php';
checkAdminLogin();
require_once '../config/dbh.conf.php';

function getTotalPlays()
{
    global $conn;

    $sql = "SELECT SUM(plays) AS total_plays FROM music";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total_plays'];
}

function getSiteVisits()
{
    global $conn;

    try {
        // Fetch the site visits count
        $stmt = $conn->query("SELECT site_visits FROM admin");
        $siteVisits = $stmt->fetchColumn();

        return $siteVisits;
    } catch (PDOException $e) {
        // Handle the database error
        echo "Database error: " . $e->getMessage();
    }
}

function getTotalSongs()
{
    global $conn;

    $sql = "SELECT COUNT(*) AS total_songs FROM music";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total_songs'];
}

function getTotalNews()
{
    global $conn;

    $sql = "SELECT COUNT(*) AS total_news FROM news";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total_news'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Emmanuel Muswalo">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .header {
            background-color: #333;
            padding: 20px;
            color: #fff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .dashboard-section {
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .dashboard-section h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .dashboard-stat {
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .dashboard-stat h5 {
            margin-top: 0;
            font-size: 18px;
        }

        .dashboard-stat p {
            margin-bottom: 0;
        }

        .dashboard-actions {
            margin-top: 20px;
        }

        .dashboard-actions a {
            color: #333;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <div class="dashboard-actions">
            <a href="music.php" style = "color:white"><i class="fas fa-music"></i> Music Management</a>
            <br>
            <a href="news.php" style = "color:white"><i class="far fa-newspaper"></i> News Management</a>
        </div>
    </div>
    <br>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="dashboard-section">
                    <div class="dashboard-stat">
                        <h5>Site Visits</h5>
                        <p>Total site visits:</p>
                        <p class="display-4"><?php echo getSiteVisits(); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-section">
                    <div class="dashboard-stat">
                        <h5>Total News</h5>
                        <p>Total news articles:</p>
                        <p class="display-4"><?php echo getTotalNews(); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-section">
                    <div class="dashboard-stat">
                        <h5>Total Plays</h5>
                        <p>Total number of plays:</p>
                        <p class="display-4"><?php echo getTotalPlays(); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-section">
                    <div class="dashboard-stat">
                        <h5>Total Songs</h5>
                        <p>Total number of songs:</p>
                        <p class="display-4"><?php echo getTotalSongs(); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-section">
            <h2>Upload Song</h2>
            <form action="upload-song.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="song-title">Title</label>
                    <input type="text" class="form-control" id="song-title" name="song-title" required>
                </div>
                <div class="form-group">
                    <label for="artist-name">Artist Name</label>
                    <input type="text" class="form-control" id="artist-name" name="artist-name" required>
                </div>
                <div class="form-group">
                    <label for="song-file">Song File</label>
                    <input type="file" class="form-control-file" id="song-file" name="song-file" required>
                    <br>
                    <label for="song-file">Song Artwork</label>
                    <input type="file" name="song-artwork" id="song-file" class="form-control-file" required>

                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>

        <div class="dashboard-section">
            <h2>Upload News</h2>
            <form action="upload-news.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="news-headline">Headline</label>
                    <input type="text" class="form-control" id="news-headline" name="news-headline" required>
                </div>
                <div class="form-group">
                    <label for="news-content">Content</label>
                    <textarea class="form-control" id="news-content" name="news-content" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="news-image">Image</label>
                    <input type="file" class="form-control-file" id="news-image" name="news-image" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
