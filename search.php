
<?php
require_once 'config/database.php';

$search = trim($_GET['q'] ?? '');
$books = [];

if ($search !== '') {
    $stmt = $pdo->prepare(
        "SELECT * FROM books
         WHERE title LIKE ?
         OR author LIKE ?
         OR genre LIKE ?
         ORDER BY title ASC"
    );

    $term = "%$search%";
    $stmt->execute([$term, $term, $term]);
    $books = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <h1>Book Management System</h1>
    <p>Search Our Library</p>
</header>

<main>
   <h2>Find a Book</h2>

<label for="searchInput">
    Enter title, author or genre:
</label>

<input
    type="text"
    id="searchInput"
    placeholder="Search books..."
>

<div id="searchResults"></div>

<p><a href="books.php">Back to Books</a></p>
<p><a href="index.php">Back to Home</a></p>

<script>
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

searchInput.addEventListener('input', function () {
    const query = this.value.trim();

    if (query === '') {
        searchResults.innerHTML = '';
        return;
    }

    fetch('ajax_search.php?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(books => {
            searchResults.innerHTML = '';

            if (books.length === 0) {
                searchResults.textContent = 'No books found.';
                return;
            }

            const table = document.createElement('table');
            const headings = ['Title', 'Author', 'Genre', 'Year'];
            const headerRow = document.createElement('tr');

            headings.forEach(heading => {
                const th = document.createElement('th');
                th.textContent = heading;
                headerRow.appendChild(th);
            });

            table.appendChild(headerRow);

            books.forEach(book => {
                const row = document.createElement('tr');

                ['title', 'author', 'genre', 'publication_year'].forEach(key => {
                    const td = document.createElement('td');
                    td.textContent = book[key] ?? '';
                    row.appendChild(td);
                });

                table.appendChild(row);
            });

            searchResults.appendChild(table);
        })
        .catch(() => {
            searchResults.textContent = 'Search failed. Please try again.';
        });
});
</script>
</main>

</body>
</html>
