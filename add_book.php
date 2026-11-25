<?php
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <h1>Book Management System</h1>
    <p>Add a New Book</p>
</header>

<main>
    <h2>Book Details</h2>

    <p><?= htmlspecialchars($message) ?></p>

    <form method="POST">
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <label>Book Title:</label>
        <input type="text" name="title" required maxlength="255">

        <label>Author:</label>
        <input type="text" name="author" required maxlength="255">

        <label>Genre:</label>
        <input type="text" name="genre" maxlength="100">

        <label>Publication Year:</label>
        <input type="number" name="publication_year"
               min="1901" max="2155">

        <button type="submit">Add Book</button>
    </form>

    <p><a href="books.php">Back to Books</a></p>
</main>

</body>
</html>