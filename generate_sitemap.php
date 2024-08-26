<?php
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$pages = [
    ['loc' => 'https://www.surgemusic.site/', 'priority' => '1.0'],
    ['loc' => 'https://www.surgemusic.site/music.php', 'priority' => '0.8'],
    ['loc' => 'https://www.surgemusic.site/news.php', 'priority' => '0.7'],
];

foreach ($pages as $page) {
    echo '<url>';
    echo '<loc>' . $page['loc'] . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>' . $page['priority'] . '</priority>';
    echo '</url>';
}

echo '</urlset>';
?>
