<?php
session_start();
require_once 'config/auth.php';
require_once 'config/database.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        exit('Invalid request. Please refresh the page.');
    }

    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $year = trim($_POST['publication_year'] ?? '');

    if ($title === '' || $author === '') {
        $message = 'Title and author are required.';
    } elseif ($year !== '' && (!ctype_digit($year) || (int)$year < 1901 || (int)$year > 2155)) {
        $message = 'Please enter a valid publication year.';
    } else {
        $sql = "INSERT INTO books (title, author, genre, publication_year)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $title,
            $author,
            $genre ?: null,
            $year !== '' ? (int)$year : null
        ]);

        header('Location: books.php');
        exit;
    }
}
?>

<?php
require_once __DIR__ . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('add_book.twig', [
    'message' => $message,
    'csrf_token' => $_SESSION['csrf_token']
]);