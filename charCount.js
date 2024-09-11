// JavaScript Function to Count Characters
function setupCharacterCount(textAreaId, messageId, maxChars) {
    const textArea = document.getElementById(textAreaId);
    const message = document.getElementById(messageId);

    // Update the character count message
    function updateMessage() {
        const charsTyped = textArea.value.length;
        const charsLeft = maxChars - charsTyped;
        
        // Show the message if it's hidden
        message.style.display = 'block'; 
        
        // Update the message content based on the number of characters left
        if (charsLeft < 0) {
            // If the character limit is exceeded
            message.textContent = `Character limit exceeded by ${Math.abs(charsLeft)}.`;
            message.style.color = 'red'; // Highlight in red
        } else if (charsLeft <= 50) {
            // If 50 or fewer characters are left
            message.textContent = `Characters left: ${charsLeft}`;
            message.style.color = 'blue'; // Change to yellow
        } else {
            // Default case, more than 50 characters left
            message.textContent = `Characters left: ${charsLeft}`;
            message.style.color = 'black'; // Default color
        }
    }

    // Listen for input event to update dynamically
    textArea.addEventListener('input', updateMessage);

    // Initialize message at start
    updateMessage();
}

// Function to validate that textarea is not empty on submission
function setupFormValidation(textAreaId, submitButtonId, submitErrorId) {
    const textArea = document.getElementById(textAreaId);
    const submitButton = document.getElementById(submitButtonId);
    const submitError = document.getElementById(submitErrorId);

    submitButton.addEventListener('click', function(event) {
        if (textArea.value.trim() === "") {
            submitError.textContent = "The box can't be empty.";
            submitError.style.display = "block";
            event.preventDefault(); // Prevent form submission
        } else {
            submitError.style.display = "none";
            // Proceed with form submission or further processing
        }
    });
}

// Example initialization for a Question Creation Page
if (document.getElementById("inputBoxqc")) {
    setupCharacterCount("inputBoxqc", "errorMessage", 1500);
    setupFormValidation("inputBoxqc", "submitqc", "submitError");
}

// Example initialization for a Question Detail Page
if (document.getElementById("inputBoxqd")) {
    setupCharacterCount("inputBoxqd", "errorMessageqd", 1500);
    setupFormValidation("inputBoxqd", "giveans", "submitErrorqd");
}