// ManageChallenge JavaScript

// Toggle Admin Dropdown
function toggleDropdown() {
    const dropdown = document.getElementById('adminDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('adminDropdown');
    const button = event.target.closest('button');
    
    if (!button || !button.onclick || button.onclick.toString().indexOf('toggleDropdown') === -1) {
        if (!dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    }
});

// Filter Challenges Function
function filterChallenges() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const selectedCategory = document.getElementById('categoryFilter').value;
    const selectedCity = document.getElementById('cityFilter').value;
    const selectedStatus = document.getElementById('statusFilter').value;
    const selectedDifficulty = document.getElementById('difficultyFilter').value;
    
    const rows = document.querySelectorAll('.challenge-row');
    let visibleCount = 0;
    const totalChallenges = rows.length;

    rows.forEach(row => {
        const title = row.getAttribute('data-title');
        const category = row.getAttribute('data-category');
        const city = row.getAttribute('data-city');
        const status = row.getAttribute('data-status');
        const difficulty = row.getAttribute('data-difficulty');
        
        const matchesSearch = title.includes(searchTerm);
        const matchesCategory = !selectedCategory || category === selectedCategory;
        const matchesCity = !selectedCity || city === selectedCity;
        const matchesStatus = !selectedStatus || status === selectedStatus;
        const matchesDifficulty = !selectedDifficulty || difficulty === selectedDifficulty;

        if (matchesSearch && matchesCategory && matchesCity && matchesStatus && matchesDifficulty) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update entry count
    document.getElementById('entryCount').textContent = 
        `Showing ${visibleCount} of ${totalChallenges} entries`;
}

// Add event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if elements exist before adding listeners to avoid errors on other pages
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterChallenges);
        document.getElementById('categoryFilter').addEventListener('change', filterChallenges);
        document.getElementById('cityFilter').addEventListener('change', filterChallenges);
        document.getElementById('statusFilter').addEventListener('change', filterChallenges);
        document.getElementById('difficultyFilter').addEventListener('change', filterChallenges);
    }
});