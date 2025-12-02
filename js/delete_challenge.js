// Delete Challenge Function
// This script handles the deletion confirmation and redirection to the correct PHP file.

function confirmDelete(id, title) {
    // 1. Show confirmation dialog
    if (confirm(`Are you sure you want to delete the challenge "${title}"?\n\nThis action cannot be undone.`)) {
        
        // 2. Redirect to the processing page
        // IMPORTANT: Ensure the filename matches your actual PHP file exactly (case-sensitive)
        window.location.href = `manage_challenge.php?delete=${id}`;
    }
}