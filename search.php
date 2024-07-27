<?php
declare(strict_types=1);
session_start();
require_once 'config/dbh.conf.php';
require_once 'templates/nav-bar.php';
require_once 'templates/music-template.php';
require_once 'templates/news-template.php';
require_once 'templates/footer.php';
require_once 'templates/countvisits.php';
countPageVisit($conn);

// Pagination settings
$recordsPerPage = 10;

// Get the search query from the URL parameter
if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
} else {
    $searchQuery = '';
}

// Escape the search query
$searchQuery = htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8');
 
try {
    // Fetch music and news records matching the search query
    $sql = "SELECT DISTINCT music.id, music.title, music.artist_name, music.song, music.link, music.plays, music.downloads, music.date, music.song_art_work, 'music' AS type 
            FROM music 
            WHERE music.title OR music.artist_name  LIKE :query 
            -- UNION
            -- SELECT DISTINCT news.id, news.headline, news.news, news.date, news.posted_by, news.link, news.image, 'news' AS type 
            -- FROM news 
            -- WHERE news.headline LIKE :query 
            ORDER BY type ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':query', "%{$searchQuery}%", PDO::PARAM_STR);
    $stmt->execute();
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalRecords = count($searchResults);
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Get current page from the query parameter
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $currentPage = $_GET['page'];
    } else {
        $currentPage = 1;
    }

    // Calculate the offset
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Fetch paginated search results
    $sql = "SELECT DISTINCT music.id, music.title, music.artist_name, music.song, music.link, music.plays, music.downloads, music.date, music.song_art_work, 'music' AS type 
            FROM music 
            WHERE music.title OR music.artist_name LIKE :query 
            -- UNION
            -- SELECT DISTINCT news.id, news.headline, news.news, news.date, news.posted_by, news.link, news.image, 'news' AS type 
            -- FROM news 
            -- WHERE news.headline LIKE :query 
            ORDER BY type ASC LIMIT :offset, :limit";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':query', "%{$searchQuery}%", PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // echo $e->getMessage();
    die('Something went wrong');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>African Music Promoters - Search: <?php echo $searchQuery; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Emmanuel Muswalo">
    <meta name="robots" content="index, follow">
    <meta name="description" content="Search and find African music tracks, news articles, and more on African Music Promoters. Our search page allows you to explore our database and discover the African music content you're looking for.">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
    <script src="js/share.js" defer></script>
    <script src="js/audio-control.js" defer></script>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/nav-bar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/music-template.css">
    <link rel="stylesheet" href="css/news-template.css">
</head>

<body>
    <!-- The navigation bar goes here -->
    <nav>
        <?php
        navBar();
        ?>
    </nav>

    <main>
        <section role="search-results">
            <h3 class="mu">Search: <i style="font-weight: lighter; font-size:20px;"><?php echo $searchQuery; ?></i></h3>
            <?php if (empty($searchResults)) : ?>
                <p class="no-records">No results found for "<?php echo $searchQuery; ?>".</p>
            <?php else : ?>
                <?php foreach ($searchResults as $result) : ?>
                    <?php if ($result['type'] == 'music') : ?>
                        <?php
                        musicTemplate($result['title'], $result['artist_name'], $result['plays'], $result['downloads'], $result['song'], $result['link'], $result['song_art_work'], $result['id']);
                        ?>
                    <?php elseif ($result['type'] == 'news') : ?>
                        <?php
                        newTemplate($result['headline'], $result['news'], $result['date'], $result['posted_by'], $result['link'], $result['image']);
                        ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($totalPages > 1) : ?>
                <?php if ($currentPage > 1) : ?>
                    <a href="?query=<?php echo urlencode($searchQuery); ?>&page=<?php echo $currentPage - 1; ?>" class="page-link" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?query=<?php echo urlencode($searchQuery); ?>&page=<?php echo $i; ?>" class="page-link <?php echo ($i == $currentPage) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages) : ?>
                    <a href="?query=<?php echo urlencode($searchQuery); ?>&page=<?php echo $currentPage + 1; ?>" class="page-link" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Related results -->
        <section role="related-results">
            <h3 class="mu">Related Results</h3>
            <!-- Display related results based on your logic here -->
        </section>
    </main>
    <?php
    footer();
    ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
