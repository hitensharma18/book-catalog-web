document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const genreFilter = document.getElementById('genreFilter');
    const searchButton = document.getElementById('searchButton');
    const bookList = document.querySelector('.book-list');
    
    function performSearch() {
        const query = searchInput.value.trim();
        const genre = genreFilter.value;
        
        fetch(`../server/book_operations.php?action=search&query=${encodeURIComponent(query)}&genre=${encodeURIComponent(genre)}`)
            .then(response => response.json())
            .then(books => {
                bookList.innerHTML = '';
                books.forEach(book => {
                    const bookCard = document.createElement('div');
                    bookCard.className = 'book-card';
                    bookCard.innerHTML = `
                        <h3>${escapeHtml(book.title)}</h3>
                        <p>Author: ${escapeHtml(book.author)}</p>
                        <p>Genre: ${escapeHtml(book.genre)}</p>
                        <p>Year: ${escapeHtml(book.publication_year)}</p>
                        <div class="book-actions">
                            <a href="view_book.php?id=${book.book_id}">View Details</a>
                        </div>
                    `;
                    bookList.appendChild(bookCard);
                });
            })
            .catch(error => console.error('Error:', error));
    }
    
    if (searchButton) {
        searchButton.addEventListener('click', performSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    if (genreFilter) {
        genreFilter.addEventListener('change', performSearch);
    }
    
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});