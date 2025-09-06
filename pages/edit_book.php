<?php
require_once __DIR__ . '/../server/db_connect.php';
require_once __DIR__ . '/../server/auth.php';

session_start();
checkAuthentication();

// Get book ID
$bookId = $_GET['id'] ?? null;
if (!$bookId || !is_numeric($bookId)) {
    header("Location: catalog.php");
    exit();
}

// Fetch book data
try {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ? AND added_by = ?");
    $stmt->execute([$bookId, $_SESSION['user_id']]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$book) {
        $_SESSION['error'] = "Book not found or you don't have permission to edit it";
        header("Location: catalog.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $year = trim($_POST['publication_year']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);

    // Validate inputs
    $errors = [];
    if (empty($title)) $errors['title'] = 'Title is required';
    if (empty($author)) $errors['author'] = 'Author is required';
    if (!empty($isbn) && !preg_match('/^\d{10}(\d{3})?$/', $isbn)) $errors['isbn'] = 'Invalid ISBN format';
    if (!empty($year) && ($year < 1000 || $year > date('Y'))) $errors['publication_year'] = 'Invalid year';

    if (empty($errors)) {
        try {
            $updateStmt = $pdo->prepare("
                UPDATE books SET 
                title = ?, author = ?, isbn = ?, 
                publication_year = ?, genre = ?, description = ?
                WHERE book_id = ? AND added_by = ?
            ");
            $updateStmt->execute([
                $title, $author, $isbn, 
                $year, $genre, $description,
                $bookId, $_SESSION['user_id']
            ]);
            
            $_SESSION['success'] = "Book updated successfully";
            header("Location: view_book.php?id=$bookId");
            exit();
        } catch (PDOException $e) {
            $errors['general'] = "Database error: " . $e->getMessage();
        }
    }
}

// Get available genres
$genres = [];
try {
    $genreStmt = $pdo->query("SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL ORDER BY genre");
    $genres = $genreStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $errors['general'] = "Error loading genres: " . $e->getMessage();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-container">
    <h1>Edit Book</h1>
    
    <?php if (isset($errors['general'])): ?>
        <div class="error-message"><?= $errors['general'] ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="title">Title*</label>
            <input type="text" id="title" name="title" 
                   value="<?= htmlspecialchars($_POST['title'] ?? $book['title']) ?>" required>
            <?php if (isset($errors['title'])): ?>
                <span class="field-error"><?= $errors['title'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="author">Author*</label>
            <input type="text" id="author" name="author" 
                   value="<?= htmlspecialchars($_POST['author'] ?? $book['author']) ?>" required>
            <?php if (isset($errors['author'])): ?>
                <span class="field-error"><?= $errors['author'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" 
                   value="<?= htmlspecialchars($_POST['isbn'] ?? $book['isbn']) ?>">
            <?php if (isset($errors['isbn'])): ?>
                <span class="field-error"><?= $errors['isbn'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="publication_year">Publication Year</label>
            <input type="number" id="publication_year" name="publication_year" 
                   value="<?= htmlspecialchars($_POST['publication_year'] ?? $book['publication_year']) ?>"
                   min="1000" max="<?= date('Y') ?>">
            <?php if (isset($errors['publication_year'])): ?>
                <span class="field-error"><?= $errors['publication_year'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="genre">Genre</label>
            <select id="genre" name="genre">
                <option value="">Select Genre</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>"
                        <?= (($_POST['genre'] ?? $book['genre']) === $g) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?= 
                htmlspecialchars($_POST['description'] ?? $book['description']) 
            ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="save-btn">Save Changes</button>
            <a href="view_book.php?id=<?= $bookId ?>" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>