<?php
session_start();

require_once 'config/auth.php';
require_once 'config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

$stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('books.twig', [
    'books' => $books
]);
