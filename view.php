<?php

declare(strict_types=1);

session_start();
require_once 'config/dbh.conf.php';
require_once 'templates/music-template.php';
require_once 'templates/news-template.php';
require_once 'templates/checkdb.php';
require_once 'templates/nav-bar.php';
require_once 'templates/footer.php';

// Ensure the required parameters are present
if (!isset($_GET['id']) || !isset($_GET['type'])) {
    die('Invalid request.');
}

$id = $_GET['id'];
$type = $_GET['type'];

// Fetch the record from the database
$record = checkDb($conn, $id, $type);

if (empty($record)) {
    die('Record not found.');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo htmlspecialchars($record['type'] == 'music' ? $record['title'] : $record['headline']); ?> - Surge Music</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo $record['type'] == 'news' ? htmlspecialchars($record['posted_by']) : 'Surge Music'; ?>">
    <meta name="robots" content="index, follow">
    <meta name="description" content="<?php echo htmlspecialchars($record['type'] == 'music' ? $record['title'] . ' by ' . $record['artist_name'] : $record['headline']); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($record['type'] == 'music' ? $record['title'] : $record['headline']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($record['type'] == 'music' ? $record['title'] . ' by ' . $record['artist_name'] : $record['headline']); ?>">
    <meta property="og:image" content="https://<?php echo $_SERVER['HTTP_HOST'] . '/img/' . ($record['type'] == 'music' ? $record['song_art_work'] : $record['image']); ?>">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/nav-bar.css">
    <link rel="stylesheet" href="css/music-template.css">
    <link rel="stylesheet" href="css/news.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
    <script src="js/audio-control.js" defer></script>
    <script src="js/news.js" defer></script>
    <script src="js/share.js" defer></script>

</head>

<body>
    <nav>
        <?php navBar(); ?>
    </nav>

    <main>
        <section style="margin-top: 50px;">
            <?php
            if ($record['type'] == 'music') {
                musicTemplate($record['title'], $record['artist_name'], $record['plays'], $record['downloads'], $record['song'], $record['link'], $record['song_art_work'], $record['id']);
                echo '<div class="buttons"><a href="index.php" class="btn btn-primary">Back to Home</a></div>';
            } elseif ($record['type'] == 'news') {
                newTemplate($record['headline'], $record['news'], $record['date'], $record['posted_by'], $record['link'], $record['image']);
                echo '<div class="buttons"><a href="index.php" class="btn btn-primary">Back to Home</a></div>';

            }
            ?>
        </section>
    </main>

    <?php footer(); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$conn = null;
?>
