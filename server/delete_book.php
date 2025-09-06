<?php
require_once __DIR__ . '/db_connect.php';
require_once 'auth.php';

session_start();

// Verify request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'You must be logged in to delete books';
    header('Location: ../pages/catalog.php');
    exit();
}

// Validate book ID
$bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
if (!$bookId) {
    $_SESSION['error'] = 'Invalid book ID';
    header('Location: ../pages/catalog.php');
    exit();
}

try {
    // Verify book ownership
    $stmt = $pdo->prepare("SELECT added_by FROM books WHERE book_id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        $_SESSION['error'] = 'Book not found';
        header('Location: ../pages/catalog.php');
        exit();
    }
    
    // Check if user owns the book or is admin
    if ($book['added_by'] != $_SESSION['user_id']) {
        $_SESSION['error'] = 'You can only delete your own books';
        header('Location: ../pages/catalog.php');
        exit();
    }
    
    // Perform deletion
    $deleteStmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
    $deleteStmt->execute([$bookId]);
    
    $_SESSION['success'] = 'Book deleted successfully';
    
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $_SESSION['error'] = 'Error deleting book';
}

header('Location: ../pages/catalog.php');
exit();
?>