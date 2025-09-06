<?php
session_start();
require_once __DIR__ . '/../server/db_connect.php';
require_once __DIR__ . '/../server/auth.php';

checkAuthentication();

if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Get book ID from URL
$bookId = $_GET['id'] ?? null;

if (!$bookId) {
    header("Location: catalog.php");
    exit();
}

try {
    // Fetch the specific book
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$book) {
        header("Location: catalog.php");
        exit();
    }
    
    // Fetch related books (optional)
    $relatedStmt = $pdo->prepare("
        SELECT * FROM books 
        WHERE genre = ? AND book_id != ?
        ORDER BY RAND() LIMIT 3
    ");
    $relatedStmt->execute([$book['genre'], $bookId]);
    $relatedBooks = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include __DIR__ . '/../includes/header.php';
?>

<div class="book-detail-container">
    <a href="catalog.php" class="back-link">← Back to Catalog</a>
    <?php if (isset($successMessage)): ?>
        <div class="success-message">
            <?= $successMessage ?>
            <span class="close-message">&times;</span>
        </div>
    <?php endif; ?>
    <div class="book-main">
        <div class="book-cover">
            <!-- Placeholder for book cover image -->
            <div class="cover-placeholder">
                <?= substr($book['title'], 0, 1) ?>
            </div>
        </div>
        
        <div class="book-info">
            <h1><?= htmlspecialchars($book['title']) ?></h1>
            <p class="book-author">by <?= htmlspecialchars($book['author']) ?></p>
            
            <div class="book-meta">
                <?php if (!empty($book['genre'])): ?>
                    <span class="genre-badge"><?= htmlspecialchars($book['genre']) ?></span>
                <?php endif; ?>
                
                <?php if (!empty($book['publication_year'])): ?>
                    <span class="year">Published: <?= htmlspecialchars($book['publication_year']) ?></span>
                <?php endif; ?>
                
                <?php if (!empty($book['isbn'])): ?>
                    <span class="isbn">ISBN: <?= htmlspecialchars($book['isbn']) ?></span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($book['description'])): ?>
                <div class="book-description">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($relatedBooks)): ?>
        <div class="related-books">
            <h2>More <?= htmlspecialchars($book['genre']) ?> Books</h2>
            <div class="related-list">
                <?php foreach ($relatedBooks as $related): ?>
                    <div class="related-book">
                        <a href="view_book.php?id=<?= $related['book_id'] ?>">
                            <div class="related-cover">
                                <?= substr($related['title'], 0, 1) ?>
                            </div>
                            <h3><?= htmlspecialchars($related['title']) ?></h3>
                            <p><?= htmlspecialchars($related['author']) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>