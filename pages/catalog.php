<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
require_once '../server/db_connect.php';
require_once '../server/auth.php';

checkAuthentication();

// Initialize variables
$searchQuery = $_GET['search'] ?? '';
$selectedGenre = $_GET['genre'] ?? '';
$books = [];

try {
    // Base query
    $query = "SELECT * FROM books WHERE 1=1";
    $params = [];
    
    // Add search filter
    if (!empty($searchQuery)) {
        $query .= " AND (title LIKE ? OR author LIKE ?)";
        $searchTerm = "%$searchQuery%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    // Add genre filter
    if (!empty($selectedGenre)) {
        $query .= " AND genre = ?";
        $params[] = $selectedGenre;
    }
    
    // Prepare and execute
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get available genres for filter
    $genreStmt = $pdo->query("SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL ORDER BY genre");
    $availableGenres = $genreStmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Catalog</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="catalog-container">
        <h1>Book Catalog</h1>
        
        <div class="search-filter-container">
            <h2>Find Your Next Read</h2>
            <form method="get" action="catalog.php" class="search-form">
                <div class="search-input-group">
                    <input type="text" name="search" placeholder="Search by title or author..." 
                        value="<?= htmlspecialchars($searchQuery) ?>" class="search-input">
                    <button type="submit" class="search-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Search
                    </button>
                </div>
                
                <div class="filter-group">
                    <select name="genre" class="genre-select">
                        <option value="">All Genres</option>
                        <?php foreach ($availableGenres as $genre): ?>
                            <option value="<?= htmlspecialchars($genre) ?>" 
                                <?= ($selectedGenre === $genre) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($genre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="button" class="reset-filters" onclick="location.href='catalog.php'">
                        Clear Filters
                    </button>
                </div>
            </form>
        </div>
        
        <div class="book-list">
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p>Author: <?= htmlspecialchars($book['author']) ?></p>
                    <?php if (!empty($book['genre'])): ?>
                        <p>Genre: <?= htmlspecialchars($book['genre']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($book['publication_year'])): ?>
                        <p>Year: <?= htmlspecialchars($book['publication_year']) ?></p>
                    <?php endif; ?>
                    <div class="book-actions">
                        <a href="view_book.php?id=<?= $book['book_id'] ?>" class="view-btn">View</a>
                        <a href="edit_book.php?id=<?= $book['book_id'] ?>" class="edit-btn">Edit</a>
                        <form action="../server/delete_book.php" method="POST" class="delete-form">
                            <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this book?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <a href="add_book.php" class="add-book-button">Add New Book</a>
    </div>
    <script src="../scripts/search.js"></script>
    <script src="../scripts/dynamic.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>