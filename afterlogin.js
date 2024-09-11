
    // Get the dropdown button
    var dropbtn1 = document.querySelector('.dropbtn1');

    // Toggle the display of the dropdown content
    dropbtn1.onclick = function() {
        var dropdownContent = document.querySelector('.dropdown-content1');
        if (dropdownContent.style.display === 'block') {
            dropdownContent.style.display = 'none';
        } else {
            dropdownContent.style.display = 'block';
        }
    };

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn1')) {
            var dropdowns = document.getElementsByClassName('dropdown-content1');
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === 'block') {
                    openDropdown.style.display = 'none';
                }
            }
        }
    };

