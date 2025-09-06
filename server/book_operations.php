<?php
require_once 'db_connect.php';

function getAllBooks() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM books");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBookById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addBook($title, $author, $isbn, $year, $genre, $description, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn, publication_year, genre, description, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $author, $isbn, $year, $genre, $description, $userId]);
}

function updateBook($id, $title, $author, $isbn, $year, $genre, $description) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, publication_year = ?, genre = ?, description = ? WHERE book_id = ?");
    return $stmt->execute([$title, $author, $isbn, $year, $genre, $description, $id]);
}

function deleteBook($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
    return $stmt->execute([$id]);
}

function searchBooks($query, $genre = null) {
    global $pdo;
    
    $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ?";
    $params = ["%$query%", "%$query%"];
    
    if ($genre) {
        $sql .= " AND genre = ?";
        $params[] = $genre;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>