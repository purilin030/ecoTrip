document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. Image Upload Preview Logic (Edit Mode) ---
    const photoInput = document.getElementById('photo_upload');
    const dropZone = document.getElementById('dropZone');
    const uploadContent = document.getElementById('uploadContent');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = imagePreview.querySelector('img');
    const removeBtn = document.getElementById('removeImageBtn');
    
    // Get the hidden input that holds the existing database image path
    const existingPhotoInput = document.querySelector('input[name="existing_photo"]');

    // Make drop zone clickable
    dropZone.addEventListener('click', function(e) {
        if (e.target !== removeBtn && !removeBtn.contains(e.target)) {
            photoInput.click();
        }
    });

    // Helper: Show Preview
    function showPreview(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                uploadContent.classList.add('hidden');
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            alert('Please select a valid image file (PNG, JPG, etc.)');
            photoInput.value = '';
        }
    }

    // Helper: Hide Preview
    function hidePreview() {
        previewImg.src = '';
        imagePreview.classList.add('hidden');
        uploadContent.classList.remove('hidden');
    }

    // Event: Handle File Selection via Button
    photoInput.addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            showPreview(file);
        }
    });

    // Event: Handle Remove Button (Edit Mode Specific)
    removeBtn.addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent triggering dropZone click
        
        // 1. Clear the file input (new uploads)
        photoInput.value = ''; 
        
        // 2. Clear the hidden input (existing database photo)
        // This effectively tells the validator "We have no image anymore"
        if (existingPhotoInput) {
            existingPhotoInput.value = '';
        }

        // 3. Hide the visual preview
        hidePreview();
    });

    // Event: Drag and Drop Effects
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            const file = e.dataTransfer.files[0];
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            photoInput.files = dataTransfer.files;
            showPreview(file);
        }
    });


    // --- 2. Date Validation Logic ---
    const form = document.getElementById('challengeForm');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    // Set minimum date to today for start date
    const today = new Date().toISOString().split('T')[0];
    startDateInput.setAttribute('min', today);

    // Update end date minimum when start date changes
    startDateInput.addEventListener('change', function() {
        endDateInput.setAttribute('min', this.value);
        endDateInput.classList.remove('border-red-500', 'focus:ring-red-500');
    });

    // --- 3. Form Submission Validation (Edit Mode Specific) ---
    form.addEventListener('submit', function(event) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        // Validate dates
        if (startDate && endDate && startDate > endDate) {
            event.preventDefault();
            alert("Error: Start Date cannot be later than End Date.");
            endDateInput.focus();
            endDateInput.classList.add('border-red-500', 'focus:ring-red-500');
            return false;
        }

        // Validate Image (Edit Mode Logic)
        // Pass if: A NEW file is selected OR an EXISTING file is present
        const hasNewFile = photoInput.files && photoInput.files.length > 0;
        const hasExistingFile = existingPhotoInput && existingPhotoInput.value.trim() !== '';

        if (!hasNewFile && !hasExistingFile) {
            event.preventDefault(); // Stop submission
            alert("Please ensure the challenge has a photo.");
            dropZone.scrollIntoView({ behavior: 'smooth', block: 'center' });
            dropZone.style.borderColor = '#ef4444';
            setTimeout(() => {
                dropZone.style.borderColor = '';
            }, 2000);
            return false;
        }
        
        // If validation passes, the form submits to process_challenge.php
    });
    
    // Remove error styling on input change
    endDateInput.addEventListener('input', function() {
        this.classList.remove('border-red-500', 'focus:ring-red-500');
    });

    // --- 4. Form Reset ---
    form.addEventListener('reset', function() {
        // In edit mode, resetting might mean reverting to the original DB image.
        // For simplicity, we just reload the page to get fresh DB data.
        setTimeout(() => {
            window.location.reload();
        }, 10);
    });
});