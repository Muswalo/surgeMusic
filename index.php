<?php

declare(strict_types=1);

session_start();
require_once 'config/dbh.conf.php';
require_once 'templates/nav-bar.php';
require_once 'templates/music-template.php';
require_once 'templates/news-template.php';
require_once 'templates/checkdb.php';
require_once 'templates/footer.php';
require_once 'templates/countvisits.php';
countPageVisit($conn);
// Number of records per page
$perPage = 10;

// Get the current page from the query parameter
$page = isset($_GET['page']) ? $_GET['page'] : 1;

try {

    // Query to fetch music records with pagination
    $musicQuery = "SELECT COUNT(*) as total FROM music";
    $stmt = $conn->prepare($musicQuery);
    $stmt->execute();
    $totalMusicRecords = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $musicPages = ceil($totalMusicRecords / $perPage);

    $musicOffset = ($page - 1) * $perPage;

    $musicSql = "SELECT * FROM music ORDER BY date DESC LIMIT :offset, :limit";
    $musicStmt = $conn->prepare($musicSql);
    $musicStmt->bindValue(':offset', $musicOffset, PDO::PARAM_INT);
    $musicStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $musicStmt->execute();

    // Array to hold music records with popularity scores
    $musicRecords = array();

    // Calculate popularity score for each record
    while ($row = $musicStmt->fetch(PDO::FETCH_ASSOC)) {
        // Code to calculate popularity score omitted for brevity

        // Add record with popularity score to the array
        $musicRecords[] = $row;
    }
} catch (PDOException $e) {
    die('Something went wrong');
}

try {
    // Query to fetch news records with pagination
    $newsQuery = "SELECT COUNT(*) as total FROM news";
    $stmt = $conn->prepare($newsQuery);
    $stmt->execute();
    $totalNewsRecords = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $newsPages = ceil($totalNewsRecords / $perPage);

    $newsOffset = ($page - 1) * $perPage;

    $newsSql = "SELECT id, headline, news, date, posted_by, link, image
                FROM news
                ORDER BY date DESC
                LIMIT :offset, :limit";
    $newsStmt = $conn->prepare($newsSql);
    $newsStmt->bindValue(':offset', $newsOffset, PDO::PARAM_INT);
    $newsStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $newsStmt->execute();

    // Fetch the latest news
    $latestNews = $newsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Something went wrong');
}

?>

<!DOCTYPE html>
<html>

<head>
    <?php
    $title = 'Surge Music';
    $desc = 'Welcome to Surge Music! We provide a platform to discover and enjoy a wide variety 
    of African music and news. Stay up-to-date with the latest updates and trends in the African
     music industry.';

    #####################################################################################################
    ####################################################################################################
    ####################################################################################################
    ######################################################################################################
    //  Compatibilty for Legacy link handler. This part of the code will be rolled out in the second iteratio
    //  In favour of using the view.php script which is more favourable for search engine crawlers

    $domain = $_SERVER['HTTP_HOST'];
    $imageUrl = 'https://' . $domain;
    if (isset($_GET['req']) && isset($_GET['t'])) {
        $rec = checkDb($conn, $_GET['req'], $_GET['t']);
        if (!empty($rec)) {
            if ($rec['type'] == 'music') {
                $title = $rec['title'];
                $desc = $rec['title'] . ' by ' . $rec['artist_name'];
                $imageUrl = 'https://' . $domain . '/img/' . $rec['song_art_work'];
            } elseif ($rec['type'] == 'news') {
                $title = $rec['headline'];
                $desc = $rec['headline'] . ' by ' . $rec['posted_by'];
                $imageUrl = 'https://' . $domain . '/img/' . $rec['image'];
            }
        }
    }
    ?>
    <title>
        <?php
        echo !isset($_GET['req']) || $rec['type'] == 'news' ? $title : $desc;
        ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Emmanuel Muswalo">
    <meta name="robots" content="index, follow">
    <meta name="description" content="<?php echo $desc ?>">
    <meta property="og:title" content="<?php echo $title ?>">
    <meta property="og:description" content="<?php echo $desc ?>">
    <meta property="og:image" content="<?php echo $imageUrl ?>">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js" defer></script>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/nav-bar.css">
    <link rel="stylesheet" href="css/music-template.css">
    <link rel="stylesheet" href="css/news.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="canonical" href="https://www.surgemusic.site" />
    <script src="js/audio-control.js" defer></script>
    <script src="js/news.js" defer></script>
    <script src="js/share.js" defer></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1490207580728898"
        crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>

<body>
    <!-- the navigation bar goes here -->
    <nav>
        <?php
        navBar();
        ?>
    </nav>

    <main>
        <?php
        #####################################################################################################
        ####################################################################################################
        ####################################################################################################
        ######################################################################################################
        //  Compatibilty for Legacy link handler. This part of the code will be rolled out in the second iteratio
        //  In favour of using the view.php script which is more favourable for search engine crawlers
        ?>
        <section>
            <?php
            if (isset($_GET['req']) && isset($_GET['t'])) {

                $record = checkDb($conn, $_GET['req'], $_GET['t']);

                if (empty($record)) {
                    echo '<p class="no-records">Invalid link</p>';
                } else {
                    if ($record['type'] == 'music') {
                        musicTemplate($record['title'], $record['artist_name'], $record['plays'], $record['downloads'], $record['song'], $record['link'], $record['song_art_work'], $record['id']);
                    } else {
                        newTemplate($record['headline'], $record['news'], $record['date'], $record['posted_by'], $record['link'], $record['image']);
                    }
                }
            }
            ?>
        </section>
        <section>
            <h3 class="mu">Music</h3>
            <?php
            if (empty($musicRecords)) {
                echo '<p class="no-records">No music records found.</p>';
            } else {
                foreach ($musicRecords as $record) {
                    musicTemplate($record['title'], $record['artist_name'], $record['plays'], $record['downloads'], $record['song'], $record['link'], $record['song_art_work'], $record['id']);
                }
            }
            ?>
        </section>

        <section>
            <h3 class="mu">News</h3>
            <?php
            if (empty($latestNews)) {
                echo '<p class="no-records">No news articles found.</p>';
            } else {
                foreach ($latestNews as $article) {
                    newTemplate($article['headline'], $article['news'], $article['date'], $article['posted_by'], $article['link'], $article['image']);
                }
            }
            ?>
        </section>

        <section class="pagination">
            <?php
            if ($page > 1) {
                $prevPage = $page - 1;
                echo '<a href="https://surgemusic.site/?page=' . $prevPage . '"><i class="fas fa-chevron-left"></i> Previous</a>';
            }

            if ($page < $musicPages || $page < $newsPages) {
                $nextPage = $page + 1;
                echo '<a href="https://surgemusic.site/?page=' . $nextPage . '">Next <i class="fas fa-chevron-right"></i></a>';
            }
            ?>
        </section>
        <!-- embeded music player  -->

    </main>
    <?php
    footer();
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<?php
$conn = null;
?>

</html>