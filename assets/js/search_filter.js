// ===== SEARCH FILTER (Live/AJAX Search) =====
// Optional: Kung gusto mo ng real-time search na hindi nagre-refresh ng page

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-filter-bar input[name="search"]');
    const categorySelect = document.querySelector('.search-filter-bar select[name="category"]');
    const itemsGrid = document.getElementById('itemsGrid');

    // Kung meron ng search at category sa page
    if (!searchInput || !itemsGrid) return;

    let searchTimeout;

    function performLiveSearch() {
        const search = searchInput.value;
        const category = categorySelect ? categorySelect.value : '';

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (category) params.append('category', category);

        fetch('../api/items/search_items.php?' + params.toString())
            .then(response => response.json())
            .then(items => {
                if (items.length === 0) {
                    itemsGrid.innerHTML = `
                        <div class="no-items">
                            <h3>No items found</h3>
                            <p>Try a different search term.</p>
                        </div>
                    `;
                    return;
                }

                itemsGrid.innerHTML = items.map(item => {
                    const imageHTML = item.image_path 
                        ? `<img src="../${item.image_path}" alt="${item.title}">` 
                        : '📦';
                    
                    const date = new Date(item.date_reported).toLocaleDateString('en-US', {
                        month: 'short', day: 'numeric', year: 'numeric'
                    });

                    return `
                        <a href="item_details.php?id=${item.id}" class="item-card">
                            <div class="item-card-image">${imageHTML}</div>
                            <div class="item-card-body">
                                <h3>${item.title}</h3>
                                <p>📍 ${item.location}</p>
                                <p>📅 ${date}</p>
                                <p>👤 ${item.username}</p>
                                <span class="item-badge badge-${item.category}">
                                    ${item.category.charAt(0).toUpperCase() + item.category.slice(1)}
                                </span>
                            </div>
                        </a>
                    `;
                }).join('');
            })
            .catch(error => console.error('Search error:', error));
    }

    // Live search with debounce (300ms delay)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performLiveSearch, 300);
    });

    if (categorySelect) {
        categorySelect.addEventListener('change', performLiveSearch);
    }
});