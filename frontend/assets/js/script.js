window.addEventListener('DOMContentLoaded', event => {

   
    var navbarShrink = function () {
        const navbarCollapsible = document.body.querySelector('#mainNav');
        if (!navbarCollapsible) {
            return;
        }
        if (window.scrollY === 0) {
            navbarCollapsible.classList.remove('navbar-shrink');
        } else {
            navbarCollapsible.classList.add('navbar-shrink');
        }
    };

    // Shrink the navbar on page load
    navbarShrink();

    // Shrink the navbar when page is scrolled
    document.addEventListener('scroll', navbarShrink);

    /**
     * Activate Bootstrap scrollspy on the main nav element.
     * This highlights the active link in the navbar based on scroll position.
     */
    const mainNav = document.body.querySelector('#mainNav');
    if (mainNav) {
        new bootstrap.ScrollSpy(document.body, {
            target: '#mainNav',
            rootMargin: '0px 0px -40%',
        });
    }

    /**
     * Responsive Navbar Management
     * Automatically collapses the responsive navbar when a link is clicked
     * (only applicable on mobile view/small screens).
     */
    const navbarToggler = document.body.querySelector('.navbar-toggler');
    // Using event delegation to catch clicks on dynamically generated nav-links
    $(document).on('click', '#navbarSupportedContent .nav-link', function() {
        if (window.getComputedStyle(navbarToggler).display !== 'none') {
            navbarToggler.click();
        }
    });

    /**
     * Account Dropdown Logic
     * Handles the visual toggle for the profile dropdown menu.
     */
    $(document).on("click", "#profile-icon", function () {
        $("#dropdown-menu").toggleClass("hidden");
    });

    // Close dropdown when clicking anywhere outside of the icon or menu
    $(document).on("click", function (event) {
        if (!$(event.target).closest("#profile-icon, #dropdown-menu").length) {
            $("#dropdown-menu").addClass("hidden");
        }
    });

});