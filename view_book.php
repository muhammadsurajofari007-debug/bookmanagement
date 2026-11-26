
<?php
require_once 'config/auth.php';
require_once 'config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid book ID.');
}

$stmt = $pdo->prepare(
    "SELECT id, title, author, genre, publication_year
     FROM books WHERE id = ?"
);

$stmt->execute([$id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    http_response_code(404);
    exit('Book not found.');
}

$loader = new \Twig\Loader\FilesystemLoader(
    __DIR__ . '/templates'
);

$twig = new \Twig\Environment($loader);

echo $twig->render('view_book.twig', [
    'book' => $book
]);
