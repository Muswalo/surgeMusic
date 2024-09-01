<?php
require_once 'config/dbh.conf.php';

header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$canonical = 'https://surgemusic.site';

// Array of static pages
$pages = [
    ['loc' => '/news.php', 'priority' => '0.8'],
];

// Generate static page URLs
foreach ($pages as $page) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($canonical . $page['loc']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>' . $page['priority'] . '</priority>';
    echo '</url>';
}

try {
    // Fetch news data from the database
    $sql = "SELECT `link`, `date` FROM news";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($songs as $song) {
        echo '<url>';
        echo '<loc>' . htmlspecialchars($canonical . '/' . $song['link']) . '</loc>';
        echo '<lastmod>' . date('Y-m-d', strtotime($song['date'])) . '</lastmod>';
        echo '<changefreq>daily</changefreq>';
        echo '<priority>0.6</priority>';
        echo '</url>';
    }
} catch (PDOException $e) {
    die('Database query failed');
}

echo '</urlset>';
?>
