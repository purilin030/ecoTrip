/**
 * Trigger the Smart Clone process
 * @param {number} challengeId - The ID of the challenge to clone
 */
function initiateSmartClone(challengeId) {
    // 1. Confirm the action with the user
    if (!confirm('Smart Clone: Create a copy of this challenge for NEXT MONTH?\n\nThis will automatically shift the start and end dates forward by one month.')) {
        return;
    }

    // 2. Show loading state (change cursor to wait)
    const originalBtnText = document.body.style.cursor;
    document.body.style.cursor = 'wait';

    // 3. Prepare data to send
    const formData = new FormData();
    formData.append('challenge_id', challengeId);

    // 4. Send Request to PHP
    fetch('smart_clone.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Get raw text first to debug potential HTML errors
    .then(text => {
        try {
            return JSON.parse(text); // Try parsing the text as JSON
        } catch (e) {
            // If parsing fails, it means the server returned an error page or warning text
            throw new Error("Server Response was not JSON. Raw Output: " + text.substring(0, 100) + "...");
        }
    })
    .then(data => {
        // Reset cursor
        document.body.style.cursor = originalBtnText;

        if (data.status === 'success') {
            // Success: Alert user and reload the page to see the new item
            alert(`Success! Cloned to dates: ${data.new_dates}`);
            window.location.reload(); 
        } else {
            // Logic Error (e.g., ID not found)
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        // System/Network Error
        document.body.style.cursor = originalBtnText;
        console.error(error);
        alert('System Error: ' + error.message);
    });
}