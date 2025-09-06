document.addEventListener('DOMContentLoaded', function() {
    // Register form validation
    if (document.getElementById('registerForm')) {
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Username validation
            const username = document.getElementById('username').value;
            if (username.length < 4) {
                document.getElementById('usernameError').textContent = 'Username must be at least 4 characters';
                isValid = false;
            } else {
                document.getElementById('usernameError').textContent = '';
            }
            
            // Email validation
            const email = document.getElementById('email').value;
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('emailError').textContent = 'Please enter a valid email';
                isValid = false;
            } else {
                document.getElementById('emailError').textContent = '';
            }
            
            // Password validation
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                document.getElementById('passwordError').textContent = 'Password must be at least 6 characters';
                isValid = false;
            } else {
                document.getElementById('passwordError').textContent = '';
            }
            
            // Confirm password
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                isValid = false;
            } else {
                document.getElementById('confirmPasswordError').textContent = '';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Login form validation
    if (document.getElementById('loginForm')) {
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            if (document.getElementById('username').value.trim() === '') {
                document.getElementById('usernameError').textContent = 'Username is required';
                isValid = false;
            } else {
                document.getElementById('usernameError').textContent = '';
            }
            
            if (document.getElementById('password').value.trim() === '') {
                document.getElementById('passwordError').textContent = 'Password is required';
                isValid = false;
            } else {
                document.getElementById('passwordError').textContent = '';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Add book form validation
    if (document.getElementById('addBookForm')) {
        document.getElementById('addBookForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            if (document.getElementById('title').value.trim() === '') {
                document.getElementById('titleError').textContent = 'Title is required';
                isValid = false;
            } else {
                document.getElementById('titleError').textContent = '';
            }
            
            if (document.getElementById('author').value.trim() === '') {
                document.getElementById('authorError').textContent = 'Author is required';
                isValid = false;
            } else {
                document.getElementById('authorError').textContent = '';
            }
            
            const isbn = document.getElementById('isbn').value;
            if (isbn && !/^(?:\d{10}|\d{13})$/.test(isbn)) {
                document.getElementById('isbnError').textContent = 'ISBN must be 10 or 13 digits';
                isValid = false;
            } else {
                document.getElementById('isbnError').textContent = '';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});