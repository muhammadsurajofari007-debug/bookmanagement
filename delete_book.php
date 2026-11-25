
<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Create a CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get and validate the book ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('Invalid book ID.');
}

// Find the book
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    exit('Book not found.');
}

// Delete only after confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token'] ?? ''
    )) {
        exit('Invalid request. Please refresh the page.');
    }

    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: books.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Book</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <h1>Book Management System</h1>
    <p>Delete Book</p>
</header>

<main>
    <h2>Confirm Deletion</h2>

    <p>
        Are you sure you want to delete
        <strong><?= htmlspecialchars($book['title']) ?></strong>?
    </p>

    <form method="POST">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >

        <button type="submit">Yes, Delete Book</button>
    </form>

    <p><a href="books.php">Cancel</a></p>
</main>

</body>
</html>
