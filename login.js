    document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const emailInput = document.getElementById("logemail");
    const passwordInput = document.getElementById("logpass");

    // Helper function to display error
    function showError(input, message) {
        clearError(input); // Remove any existing error message first

        let error = input.nextElementSibling;
        if (error && !error.classList.contains('login-error-message')) {
            error = null;
        }

        if (!error) {
            error = document.createElement('div');
            error.className = 'login-error-message'; // Specific class for the error message on login page
            input.parentNode.insertBefore(error, input.nextSibling);
        }

        error.textContent = message;
        input.classList.add('login-error'); // Specific class to highlight the input with error on login page
    }

    // Helper function to clear error
    function clearError(input) {
        let error = input.nextElementSibling;
        if (error && error.classList.contains('login-error-message')) {
            error.remove();
        }
        input.classList.remove('login-error');
    }

    // Email validation
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@"]+\.)+[^<>()[\]\\.,;:\s@"]{2,})$/i;
        return re.test(email.toLowerCase());
    }

    // Password validation
    function validatePassword(password) {
        const re = /^[^\s]{6,}$/; // 6 or more characters, no spaces
        return re.test(password);
    }

    // Individual field validation functions
    function validateEmailField() {
        if (emailInput.value && !validateEmail(emailInput.value)) {
            showError(emailInput, "Please enter a valid email address.");
        } else {
            clearError(emailInput);
        }
    }

    function validatePasswordField() {
        if (passwordInput.value && !validatePassword(passwordInput.value)) {
            showError(passwordInput, "Password must be 6 characters or longer and contain no spaces.");
        } else {
            clearError(passwordInput);
        }
    }

    // Event listeners for real-time validation
    emailInput.addEventListener("blur", validateEmailField);
    passwordInput.addEventListener("blur", validatePasswordField);

    // Validate all fields on form submit
    form.addEventListener("submit", function(event) {
        validateEmailField();
        validatePasswordField();

        // Check if there are any errors
        if (form.querySelectorAll('.login-error-message').length > 0) {
            event.preventDefault();
        }
    });
});
