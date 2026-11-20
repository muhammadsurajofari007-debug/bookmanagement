
<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'config/database.php';

$search = trim($_GET['q'] ?? '');

if ($search === '') {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT id, title, author, genre, publication_year
            FROM books
            WHERE title LIKE ?
               OR author LIKE ?
               OR genre LIKE ?
            ORDER BY title ASC
            LIMIT 20";

    $stmt = $pdo->prepare($sql);

    $term = '%' . $search . '%';

    $stmt->execute([
        $term,
        $term,
        $term
    ]);

    $books = $stmt->fetchAll();

    echo json_encode($books);

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo json_encode([
        'error' => 'Search failed. Please try again.'
    ]);
}
