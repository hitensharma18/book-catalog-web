<?php
session_start();
require_once '../server/db_connect.php';
require_once '../server/auth.php';
require_once '../server/book_operations.php';

checkAuthentication();

$errorMessages = [];
$successMessage = '';
$formValues = [
    'title' => '',
    'author' => '',
    'isbn' => '',
    'publication_year' => '',
    'genre' => '',
    'description' => ''
];

// Get genres from database
$genres = [];
try {
    // This query gets all unique genres from books table AND any hardcoded genres
    $stmt = $pdo->query("
        SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre != ''
        UNION
        SELECT 'Fiction' AS genre
        UNION SELECT 'Non-Fiction'
        UNION SELECT 'Science Fiction'
        UNION SELECT 'Fantasy'
        UNION SELECT 'Mystery'
        UNION SELECT 'Romance'
        UNION SELECT 'Biography'
        UNION SELECT 'History'
        ORDER BY genre
    ");
    $genres = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
} catch (PDOException $e) {
    $errorMessages['general'] = 'Error loading genres: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $formValues['title'] = trim($_POST['title']);
    $formValues['author'] = trim($_POST['author']);
    $formValues['isbn'] = trim($_POST['isbn']);
    $formValues['publication_year'] = trim($_POST['publication_year']);
    $formValues['genre'] = trim($_POST['genre']);
    $formValues['description'] = trim($_POST['description']);

    // Validation
    if (empty($formValues['title'])) {
        $errorMessages['title'] = 'Title is required';
    }

    if (empty($formValues['author'])) {
        $errorMessages['author'] = 'Author is required';
    }

    if (!empty($formValues['isbn']) && !preg_match('/^\d{10}(\d{3})?$/', $formValues['isbn'])) {
        $errorMessages['isbn'] = 'ISBN must be 10 or 13 digits';
    }

    if (!empty($formValues['publication_year']) && 
        ($formValues['publication_year'] < 1000 || $formValues['publication_year'] > date('Y'))) {
        $errorMessages['publication_year'] = 'Publication year must be between 1000 and ' . date('Y');
    }

    if (empty($errorMessages)) {
        try {
            $result = addBook(
                $formValues['title'],
                $formValues['author'],
                $formValues['isbn'],
                $formValues['publication_year'],
                $formValues['genre'],
                $formValues['description'],
                $_SESSION['user_id']
            );

            if ($result) {
                $successMessage = 'Book added successfully!';
                // Clear form on success
                $formValues = [
                    'title' => '',
                    'author' => '',
                    'isbn' => '',
                    'publication_year' => '',
                    'genre' => '',
                    'description' => ''
                ];
            } else {
                $errorMessages['general'] = 'Failed to add book. Please try again.';
            }
        } catch (PDOException $e) {
            $errorMessages['general'] = 'Database error: ' . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book - Book Catalog</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <main>
        <div class="form-container">
            <h1>Add New Book</h1>
            
            <?php if (!empty($successMessage)): ?>
                <div class="success-message"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            
            <?php if (isset($errorMessages['general'])): ?>
                <div class="error-message"><?= htmlspecialchars($errorMessages['general']) ?></div>
            <?php endif; ?>
            
            <form id="addBookForm" action="add_book.php" method="POST">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" 
                           value="<?= htmlspecialchars($formValues['title']) ?>" required>
                    <?php if (isset($errorMessages['title'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errorMessages['title']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" 
                           value="<?= htmlspecialchars($formValues['author']) ?>" required>
                    <?php if (isset($errorMessages['author'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errorMessages['author']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="isbn">ISBN (optional):</label>
                    <input type="text" id="isbn" name="isbn" 
                           value="<?= htmlspecialchars($formValues['isbn']) ?>">
                    <?php if (isset($errorMessages['isbn'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errorMessages['isbn']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="publication_year">Publication Year (optional):</label>
                    <input type="number" id="publication_year" name="publication_year" 
                           value="<?= htmlspecialchars($formValues['publication_year']) ?>"
                           min="1000" max="<?= date('Y') ?>">
                    <?php if (isset($errorMessages['publication_year'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errorMessages['publication_year']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="genre">Genre (optional):</label>
                    <select id="genre" name="genre">
                        <option value="">Select Genre</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= htmlspecialchars($genre) ?>" 
                                <?= $formValues['genre'] === $genre ? 'selected' : '' ?>>
                                <?= htmlspecialchars($genre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description (optional):</label>
                    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($formValues['description']) ?></textarea>
                </div>
                
                <button type="submit">Add Book</button>
                <a href="catalog.php" class="cancel-button">Cancel</a>
            </form>
        </div>
    </main>
    <script src="../scripts/validation.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>