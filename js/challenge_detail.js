function joinChallenge(id) {
    // 1. Confirm action
    if(confirm("Are you sure you want to join this challenge?")) {
        
        // 2. Here you would typically send an AJAX request to PHP
        // For now, we simulate a success message
        
        // Example Fetch (Uncomment to use with backend logic):
        /*
        fetch('join_challenge_process.php', {
            method: 'POST',
            body: JSON.stringify({ challenge_id: id }),
            headers: { 'Content-Type': 'application/json' }
        }).then(response => ...);
        */

        alert("Congratulations! You have successfully joined the challenge.");
        
        // Optional: Reload page or change button text
        const btn = document.querySelector('.join-btn');
        btn.textContent = "Joined";
        btn.style.backgroundColor = "#9ca3af"; // Gray out button
        btn.disabled = true;
    }
}