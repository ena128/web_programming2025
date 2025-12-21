var UserService = {
    /**
     * Initializes event listeners for Login and Register forms.
     * Uses event delegation to handle forms loaded dynamically via SPApp.
     */
    init: function () {
        // Handle Login form submission
        $(document).off("submit", "#login-form").on("submit", "#login-form", function (e) {
            e.preventDefault();
            
            const entity = {
                email: $("#email").val(),
                password: $("#password").val()
            };
            
            UserService.login(entity);
        });

        // Handle Register form submission
        $(document).off("submit", "#register-form").on("submit", "#register-form", function (e) {
            e.preventDefault();
            
            const entity = {
                name: $("#name").val(),
                email: $("#email").val(),
                password: $("#password").val()
            };
            
            UserService.register(entity);
        });
    },

    /**
     * Sends login credentials to the backend.
     */
    login: function (entity) {
    $.ajax({
        url: Constants.PROJECT_BASE_URL + "/auth/login",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            email: entity.email.trim(), // Remove accidental spaces
            password: entity.password.trim()
        }),
        success: function (res) {
            localStorage.setItem("user_token", res.data.token);
            toastr.success("Login successful!");
            window.location.hash = "#home";
            location.reload(); 
        },
        error: function (xhr) {
            // This will now show the EXACT error from PHP
            const errorMsg = xhr.responseJSON ? xhr.responseJSON.error : "Unknown error";
            console.error("Backend says:", errorMsg);
            toastr.error(errorMsg);
        }
    });
},

    /**
     * Sends registration data to the backend.
     */
    register: function (entity) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/auth/register",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(entity),
            success: function (res) {
                console.log("RESPONSE OD SERVERA:", res); // Gledaj u konzolu (F12)

                // Ako server vrati samo ID (broj), pretvori ga u uspjeh
                // Ako server vrati JSON objekat, to je isto super.
                
                toastr.success("Registration successful! Please login.");
                
                // Forsiramo preusmjeravanje nakon kratke pauze da vidiš poruku
                setTimeout(function() {
                    console.log("Preusmjeravam na login...");
                    window.location.hash = "#login";
                }, 1000);
            },
            error: function (xhr, status, error) {
                console.error("Greška detalji:", xhr);
                console.log("Response text:", xhr.responseText);
                
                // Čak i ako je 200 OK, ako JSON nije validan, jQuery baci "parsererror"
                if (xhr.status === 200) {
                    toastr.success("Registracija uspjela (iako je format čudan)!");
                    window.location.hash = "#login";
                } else {
                    toastr.error("Greška: " + xhr.responseText);
                }
            }
        });
    },

    /**
     * Generates navbar items dynamically based on the user's authentication state.
     */
    generateMenuItems: function () {
        const token = localStorage.getItem("user_token");
        let nav = `<li class="nav-item"><a class="nav-link" href="#home">Home</a></li>`;

        if (token && token !== "undefined") {
            // Links for logged-in users
            nav += `<li class="nav-item"><a class="nav-link" href="#account">Account</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" id="logoutButton">Logout</a></li>`;
        } else {
            // Links for guests
            nav += `<li class="nav-item"><a class="nav-link" href="#register">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="#login">Login</a></li>`;
        }
        
        // Target the navbar ul in index.html
        $("#navbarSupportedContent ul").html(nav);
    },

    /**
     * Logs the user out by clearing the token and redirecting.
     */
    logout: function () {
        localStorage.removeItem("user_token");
        toastr.info("Logged out successfully.");
        window.location.hash = "#home";
        location.reload();
    }
};

/**
 * Run initialization when the document is ready.
 */
$(document).ready(function () {
    UserService.init();
    
    // Handle Logout button click (delegated)
    $(document).on("click", "#logoutButton", function (e) {
        e.preventDefault();
        UserService.logout();
    });
});