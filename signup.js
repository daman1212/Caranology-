document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const emailInput = document.getElementById("mail1");
    const usernameInput = document.getElementById("usernme1");
    const avatarInput = document.getElementById("avtar1");
    const passwordInput = document.getElementById("psswd1");
    const confirmPasswordInput = document.getElementById("conpsswd1");

    // Helper function to display error
    function showError(input, message) {
        clearError(input); // Remove any existing error message first

        let error = input.nextElementSibling;
        if (error && !error.classList.contains('error-message')) {
            error = null;
        }

        if (!error) {
            error = document.createElement('div');
            error.className = 'error-message'; // Class for the error message
            input.parentNode.insertBefore(error, input.nextSibling);
        }

        error.textContent = message;
        input.classList.add('error'); // Class to highlight the input with error
    }

    // Helper function to clear error
    function clearError(input) {
        let error = input.nextElementSibling;
        if (error && error.classList.contains('error-message')) {
            error.remove();
        }
        input.classList.remove('error');
    }

    // Email validation
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@"]+\.)+[^<>()[\]\\.,;:\s@"]{2,})$/i;
        return re.test(email.toLowerCase());
    }

    // Username validation
    function validateUsername(username) {
        const re = /^\w+$/;
        return re.test(username);
    }

    // Password validation
    function validatePassword(password) {
        const re = /^(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,}$/;
        return re.test(password);
    }

    // Avatar validation
    function validateAvatar(avatar) {
        return avatar.files.length > 0;
    }

    // Password match validation
    function validatePasswordMatch(password, confirmPassword) {
        return password === confirmPassword;
    }

    // Individual field validation functions
    function validateEmailField() {
        if (emailInput.value && !validateEmail(emailInput.value)) {
            showError(emailInput, "Please enter a valid email address.");
        } else {
            clearError(emailInput);
        }
    }

    function validateUsernameField() {
        if (usernameInput.value && !validateUsername(usernameInput.value)) {
            showError(usernameInput, "Username must not contain spaces or special characters.");
        } else {
            clearError(usernameInput);
        }
    }

    function validatePasswordField() {
        if (passwordInput.value && !validatePassword(passwordInput.value)) {
            showError(passwordInput, "Password must be 6+ characters with 1+ non-letter.");
        } else {
            clearError(passwordInput);
        }
    }

    function validateAvatarField() {
        if (!validateAvatar(avatarInput)) {
            showError(avatarInput, "Please choose an avatar image.");
        } else {
            clearError(avatarInput);
        }
    }

    function validateConfirmPasswordField() {
        if (!validatePasswordMatch(passwordInput.value, confirmPasswordInput.value)) {
            showError(confirmPasswordInput, "Passwords do not match.");
        } else {
            clearError(confirmPasswordInput);
        }
    }

    // Event listeners for real-time validation
    emailInput.addEventListener("blur", validateEmailField);
    usernameInput.addEventListener("blur", validateUsernameField);
    avatarInput.addEventListener("change", validateAvatarField);
    passwordInput.addEventListener("blur", validatePasswordField);
    confirmPasswordInput.addEventListener("blur", validateConfirmPasswordField);

    // Validate all fields on form submit
    form.addEventListener("submit", function(event) {
        validateEmailField();
        validateUsernameField();
        validatePasswordField();
        validateAvatarField();
        validateConfirmPasswordField();

        // Check if there are any errors
        if (form.querySelectorAll('.error-message').length > 0) {
            event.preventDefault(); // Prevent form submission if there are errors
        }
    });
});


//I will be creating the get elemnt by id of a element named fname again 
let fname = document.getElementByID("fname");
name.addEventListner("blur",fnameevent);
fnameevent(event)
{
fname = event.target;
if(!fnameconfirm(fname.value))
{
    fname.classList.add("red-box")
}
else
{
    fname.classList.remove("red-box")
}
}

fnameconfirm(name)
{
    let indexname = /^\w+$/;

    if(indexname.test(name))
    {
        return true;

    }
    else
    {
        return false;
    }
}


