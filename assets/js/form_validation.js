// assets/js/form_validation.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportForm');
    const imageInput = document.getElementById('itemImage');
    const imagePreview = document.getElementById('imagePreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const imageUploadArea = document.getElementById('imageUploadArea');
    
    // Image Preview
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                showError('itemImage', 'Only JPG, JPEG, and PNG files are allowed');
                imageInput.value = '';
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showError('itemImage', 'Image size must be less than 5MB');
                imageInput.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                uploadPlaceholder.style.display = 'none';
                imageUploadArea.style.borderStyle = 'solid';
                imageUploadArea.style.borderColor = '#2ecc71';
            };
            reader.readAsDataURL(file);
            
            clearError('itemImage');
        }
    });
    
    // Form Validation on Submit
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Clear all errors
        clearAllErrors();
        
        // Validate item type
        const itemType = document.getElementById('itemType').value;
        if (!itemType) {
            showError('itemType', 'Please select an item type');
            isValid = false;
        }
        
        // Validate title
        const title = document.getElementById('title').value.trim();
        if (!title) {
            showError('title', 'Title is required');
            isValid = false;
        } else if (title.length > 100) {
            showError('title', 'Title must be less than 100 characters');
            isValid = false;
        }
        
        // Validate location
        const location = document.getElementById('location').value.trim();
        if (!location) {
            showError('location', 'Location is required');
            isValid = false;
        } else if (location.length > 200) {
            showError('location', 'Location must be less than 200 characters');
            isValid = false;
        }
        
        // Validate description
        const description = document.getElementById('description').value.trim();
        if (!description) {
            showError('description', 'Description is required');
            isValid = false;
        } else if (description.length > 1000) {
            showError('description', 'Description must be less than 1000 characters');
            isValid = false;
        }
        
        // Validate image
        if (!imageInput.files || imageInput.files.length === 0) {
            showError('itemImage', 'Image is required');
            isValid = false;
        }
        
        // If validation fails, prevent form submission
        if (!isValid) {
            e.preventDefault();
            
            // Scroll to first error
            const firstError = document.querySelector('.error-message:not(:empty)');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
    
    // Real-time validation
    const inputs = ['itemType', 'title', 'location', 'description'];
    inputs.forEach(function(inputId) {
        const input = document.getElementById(inputId);
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                clearError(inputId);
            }
        });
    });
    
    // Helper functions
    function showError(fieldId, message) {
        const errorElement = document.getElementById('error-' + fieldId);
        if (errorElement) {
            errorElement.textContent = message;
        }
    }
    
    function clearError(fieldId) {
        const errorElement = document.getElementById('error-' + fieldId);
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
    
    function clearAllErrors() {
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(function(element) {
            element.textContent = '';
        });
    }
});