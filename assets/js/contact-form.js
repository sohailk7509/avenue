/**
 * Contact Form JS
 * Handles the submission of the contact form with AJAX
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get the contact form element
    const contactForm = document.querySelector('form[action="forms/contact.php"]');
    
    if (contactForm) {
        // Flag to prevent duplicate submissions
        let isSubmitting = false;
        
        // Handle form submission
        contactForm.addEventListener('submit', function(e) {
            // Always prevent default form submission
            e.preventDefault();
            e.stopPropagation();
            
            // If already submitting, don't submit again
            if (isSubmitting) {
                console.log('Form is already being submitted');
                return false;
            }
            
            // Set submitting flag
            isSubmitting = true;
            
            // Show loading indicator
            const loadingElement = contactForm.querySelector('.loading');
            const errorElement = contactForm.querySelector('.error-message');
            const sentElement = contactForm.querySelector('.sent-message');
            const submitButton = contactForm.querySelector('button[type="submit"]');
            
            // Reset messages
            errorElement.innerHTML = '';
            errorElement.style.display = 'none';
            sentElement.style.display = 'none';
            sentElement.innerHTML = 'Your message has been sent. Thank you!';
            
            // Show loading
            loadingElement.style.display = 'block';
            submitButton.disabled = true;
            
            // Get form data
            const formData = new FormData(contactForm);
            
            // Add timestamp to prevent caching
            formData.append('timestamp', new Date().getTime());
            
            // Send AJAX request
            fetch(contactForm.getAttribute('action') + '?t=' + new Date().getTime(), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // First check if the response is valid JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                throw new Error('Server returned non-JSON response');
            })
            .then(data => {
                // Hide loading
                loadingElement.style.display = 'none';
                
                if (data.success) {
                    // Show success message
                    sentElement.innerHTML = data.message || 'Your message has been sent. Thank you!';
                    sentElement.style.display = 'block';
                    contactForm.reset();
                } else {
                    // Show error message
                    errorElement.innerHTML = data.message || 'An error occurred. Please try again.';
                    errorElement.style.display = 'block';
                }
                
                // Re-enable submit button and reset flag
                submitButton.disabled = false;
                isSubmitting = false;
            })
            .catch(error => {
                // Hide loading and show error
                loadingElement.style.display = 'none';
                errorElement.innerHTML = 'An error occurred. Please try again later.';
                errorElement.style.display = 'block';
                submitButton.disabled = false;
                isSubmitting = false;
                
                console.error('Error:', error);
            });
            
            return false;
        });
    }
}); 