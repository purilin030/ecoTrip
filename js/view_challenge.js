// Challenge Page JavaScript

// Toggle Admin Dropdown
function toggleDropdown() {
    const dropdown = document.getElementById('adminDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('adminDropdown');
    const button = document.querySelector('.nav-dropdown-btn');

    if (!button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

// Filter Challenges Function
function filterChallenges() {
    // 1. Get all filter values
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const selectedCategory = document.getElementById('categoryFilter').value;
    const selectedCity = document.getElementById('cityFilter').value;
    const selectedPointsRange = document.getElementById('pointsFilter').value;
    const selectedDifficulty = document.getElementById('difficultyFilter').value;

    const cards = document.querySelectorAll('.challenge-card');

    cards.forEach(card => {
        // 2. Get card data attributes
        const title = card.getAttribute('data-title').toLowerCase();
        const category = card.getAttribute('data-category');
        const city = card.getAttribute('data-city');
        const points = parseInt(card.getAttribute('data-points'));
        const difficulty = card.getAttribute('data-difficulty');

        // 3. Check matches
        const matchesSearch = title.includes(searchTerm);
        const matchesCategory = !selectedCategory || category === selectedCategory;
        const matchesCity = !selectedCity || city === selectedCity;

        // Difficulty Match
        const matchesDifficulty = !selectedDifficulty || difficulty === selectedDifficulty;

        let matchesPoints = true;
        if (selectedPointsRange) {
            if (selectedPointsRange === '0-200') {
                matchesPoints = points >= 0 && points <= 200;
            } else if (selectedPointsRange === '201-500') {
                matchesPoints = points >= 201 && points <= 500;
            } else if (selectedPointsRange === '501-1000') {
                matchesPoints = points >= 501 && points <= 1000;
            } else if (selectedPointsRange === '1000+') {
                matchesPoints = points > 1000;
            }
        }

        // 4. Show or Hide based on all criteria
        if (matchesSearch && matchesCategory && matchesCity && matchesPoints && matchesDifficulty) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Add Event Listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('searchInput').addEventListener('input', filterChallenges);
    document.getElementById('categoryFilter').addEventListener('change', filterChallenges);
    document.getElementById('cityFilter').addEventListener('change', filterChallenges);
    document.getElementById('pointsFilter').addEventListener('change', filterChallenges);
    document.getElementById('difficultyFilter').addEventListener('change', filterChallenges);
});