document.addEventListener('DOMContentLoaded', function() {
    // Handle book deletion with confirmation
    document.querySelectorAll('.delete').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this book?')) {
                e.preventDefault();
            }
        });
    });
    
    // Toggle book details
    document.querySelectorAll('.book-card h3').forEach(title => {
        title.addEventListener('click', function() {
            this.nextElementSibling.classList.toggle('show-details');
        });
    });
    
    // Rating system
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('click', function() {
            const bookId = this.dataset.bookId;
            const rating = this.dataset.value;
            
            fetch(`../server/book_operations.php?action=rate&bookId=${bookId}&rating=${rating}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStarDisplay(bookId, rating);
                }
            });
        });
    });
    
    function updateStarDisplay(bookId, rating) {
        const stars = document.querySelectorAll(`.rating-star[data-book-id="${bookId}"]`);
        stars.forEach(star => {
            if (star.dataset.value <= rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }
});