$(document).ready(function () {
    // Load header and footer dynamically
    $("#header").load("index.html #navbar", function () {
        // Dodajemo profilnu samo na account page
        if (window.location.hash === "#account") {
            $("#navbar").append('<img id="profile-icon" src="profile.jpg" alt="Profile">');
            $("#navbar").append('<div id="dropdown-menu" class="hidden">Opcije</div>');
        }
    });
    $("#footer").load("index.html footer");

    // Initialize SPApp
    var app = $.spapp({
        defaultView: "#home",
        templateDir: "frontend/views/"
    });

    app.run();

    // Event delegation for dynamically added profile icon
    $(document).on("click", "#profile-icon", function () {
        $("#dropdown-menu").toggleClass("hidden");
    });

    // Close dropdown when clicking outside
    $(document).on("click", function (event) {
        if (!$(event.target).closest("#profile-icon, #dropdown-menu").length) {
            $("#dropdown-menu").addClass("hidden");
        }
    });
});
