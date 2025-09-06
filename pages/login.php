<?php
session_start();
require_once '../server/db_connect.php';
require_once '../server/auth.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $errorMessage = 'Please enter both username and password';
    } else {
        if (loginUser($username, $password)) {
            header('Location: catalog.php');
            exit();
        } else {
            $errorMessage = 'Invalid username or password';
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
    <title>Login - Book Catalog</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <main>
        <div class="login-container">
            <h1>Login</h1>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="error-message"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            
            <form id="loginForm" action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <span class="field-error" id="usernameError"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="field-error" id="passwordError"></span>
                </div>
                
                <button type="submit">Login</button>
            </form>
            
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </main>
    <script src="../scripts/validation.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>