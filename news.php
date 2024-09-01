<?php
declare(strict_types=1);
session_start();
require_once 'config/dbh.conf.php';
require_once 'templates/nav-bar.php';
require_once 'templates/news-template.php';
require_once 'templates/footer.php';
require_once 'templates/countvisits.php';
countPageVisit($conn);
// Pagination settings
$recordsPerPage = 10;

try {
    // Fetch total number of records
    $sql = "SELECT COUNT(*) as total FROM news";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalRecords = $result['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Get current page from the query parameter
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $currentPage = $_GET['page'];
    } else {
        $currentPage = 1;
    }

    // Calculate the offset
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Query to fetch news records with pagination
    $sql = "SELECT * FROM news ORDER BY date DESC LIMIT :offset, :limit";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $newsRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Something went wrong');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>African Music Promoters</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Emmanuel Muswalo">
    <meta name="robots" content="index, follow">
    <meta name="description" content="Stay informed with the latest news in the African music industry on African Music Promoters. Our news page covers trending stories, artist interviews, album releases, and more, focusing on the rich and diverse African music scene.">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
    <link rel="stylesheet" href="css/nav-bar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/news.css">
    <link rel="canonical" href="https://surgemusic.site/news.php" />
    <script src="js/news.js" defer></script>
    <script src="js/share.js" defer></script>
</head>

<body>
    <!-- The navigation bar goes here -->
    <nav>
        <?php
        navBar();
        ?>
    </nav>

    <main>
        <section role="news">
            <h3 class="mu">News</h3>
            <?php
            if (empty($newsRecords)) {
                echo '<p class="no-records">No news records found.</p>';
            } else {
                foreach ($newsRecords as $record) {
                    newTemplate($record['headline'], $record['news'], $record['date'], $record['posted_by'], $record['link'], $record['image']);
                }
            }
            ?>
        </section>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($totalPages > 1) : ?>
                <?php if ($currentPage > 1) : ?>
                    <a href="?page=<?php echo $currentPage - 1; ?>" class="page-link" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="https://surgemusic.site/?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $currentPage) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages) : ?>
                    <a href="?page=<?php echo $currentPage + 1; ?>" class="page-link" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php
    footer();
    ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
