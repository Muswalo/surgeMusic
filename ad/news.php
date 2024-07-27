<?php
declare(strict_types=1);

require_once 'check-loging.php';
checkAdminLogin();
require_once '../config/dbh.conf.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin News Management</title>
    <meta name="author" content="Emmanuel Muswalo">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h2>News Management</h2>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Posted</th>
                    <th>Headline</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Pagination variables
                $limit = 10; // Number of records per page
                $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page

                // Calculate the offset
                $offset = ($page - 1) * $limit;

                try {
                    // Fetch news records with pagination
                    $stmt = $conn->prepare("SELECT * FROM news ORDER BY date DESC LIMIT :limit OFFSET :offset");
                    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $newsId = $row['id'];
                        $headline = $row['headline'];
                        $datePosted = date('F j, Y', strtotime($row['date']));

                        echo "<tr>";
                        echo "<td>$datePosted</td>";
                        echo "<td>$headline</td>";
                        echo "<td><a class='btn btn-danger' href='delete-news.php?id=$newsId'>Delete</a></td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='3'>Database error: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                // Fetch total count for pagination
                $totalRecords = $conn->query("SELECT COUNT(*) FROM news")->fetchColumn();
                $totalPages = ceil($totalRecords / $limit);

                // Display pagination links
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo "<li class='page-item" . ($page == $i ? " active" : "") . "'><a class='page-link' href='news.php?page=$i'>$i</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
