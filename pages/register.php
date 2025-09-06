<?php
session_start();
require_once '../server/db_connect.php';
require_once '../server/auth.php';

$errorMessages = [];
$successMessage = '';
$formValues = [
    'username' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $formValues['username'] = trim($_POST['username']);
    $formValues['email'] = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Basic validation
    if (empty($formValues['username'])) {
        $errorMessages['username'] = 'Username is required';
    } elseif (strlen($formValues['username']) < 4) {
        $errorMessages['username'] = 'Username must be at least 4 characters';
    }

    if (empty($formValues['email'])) {
        $errorMessages['email'] = 'Email is required';
    } elseif (!filter_var($formValues['email'], FILTER_VALIDATE_EMAIL)) {
        $errorMessages['email'] = 'Please enter a valid email';
    }

    if (empty($password)) {
        $errorMessages['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errorMessages['password'] = 'Password must be at least 6 characters';
    } elseif ($password !== $confirmPassword) {
        $errorMessages['confirmPassword'] = 'Passwords do not match';
    }

    // Only proceed if no validation errors
    if (empty($errorMessages)) {
        $registrationResult = registerUser($formValues['username'], $formValues['email'], $password);
        
        if ($registrationResult['success']) {
            $successMessage = 'Registration successful! You can now log in.';
            // Clear form values on success
            $formValues = [
                'username' => '',
                'email' => ''
            ];
        } else {
            $errorMessages['general'] = $registrationResult['error'];
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
    <title>Register - Book Catalog</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?= $successMessage ?></div>
        <?php endif; ?>
        
        <?php if (isset($errorMessages['general'])): ?>
            <div class="error-message"><?= $errorMessages['general'] ?></div>
        <?php endif; ?>
        
        <form id="registerForm" action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" 
                       value="<?= htmlspecialchars($formValues['username']) ?>" required>
                <?php if (isset($errorMessages['username'])): ?>
                    <span class="field-error" id="usernameError"><?= $errorMessages['username'] ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($formValues['email']) ?>" required>
                <?php if (isset($errorMessages['email'])): ?>
                    <span class="field-error" id="emailError"><?= $errorMessages['email'] ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <?php if (isset($errorMessages['password'])): ?>
                    <span class="field-error" id="passwordError"><?= $errorMessages['password'] ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <?php if (isset($errorMessages['confirmPassword'])): ?>
                    <span class="field-error" id="confirmPasswordError"><?= $errorMessages['confirmPassword'] ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit">Register</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
    <script src="../scripts/validation.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>