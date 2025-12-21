var UserService = {
    /**
     * Inicijalizacija listenera za Login i Register forme
     */
    init: function () {
        // Login logika
        $(document).off("submit", "#login-form").on("submit", "#login-form", function (e) {
            e.preventDefault();
            UserService.login({
                email: $("#email").val(),
                password: $("#password").val()
            });
        });

        // Register logika
        $(document).off("submit", "#register-form").on("submit", "#register-form", function (e) {
            e.preventDefault();
            UserService.register({
                fullname: $("#fullName").val(),
                email: $("#email").val(),
                password: $("#password").val()
            });
        });
    },

    /**
     * Ključna funkcija: Dekodira token da izvučemo podatke (Ime, Role, ID)
     */
    parseJwt: function (token) {
        if (!token || token === "undefined") return null;
        try {
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            return JSON.parse(jsonPayload);
        } catch (e) {
            return null;
        }
    },

    /**
     * OVO JE DIO KOJI TI TREBA: Inicijalizacija Account stranice
     */
    initAccountPage: function () {
        const token = localStorage.getItem("user_token");
        
        // 1. Ako nema tokena, vrati na login
        if (!token) {
            window.location.hash = "#login";
            return;
        }

        // 2. Dekodiraj token
        const payload = UserService.parseJwt(token);
        
        // Provjera da li je token validan
        if (!payload || !payload.user) {
            toastr.error("Invalid token. Please login again.");
            UserService.logout();
            return;
        }

        const user = payload.user;

        // 3. OVDJE SE POSTAVLJA IME KORISNIKA
        // Tražimo element sa id="username" i upisujemo user.name
        $("#username").text(user.name); 

        // 4. Logika za prikazivanje panela (Admin vs User)
        if (user.role && user.role.toUpperCase() === 'ADMIN') {
            // Ako je Admin
            $("#adminPanel").show();      // Prikaži admin panel
            UserAdminService.loadAllUsers(); // Učitaj tabelu korisnika
        } else {
            // Ako je običan User
            $("#adminPanel").hide();      // Sakrij admin panel
        }

        // 5. Učitaj taskove (ovo vide svi)
        // Ako imaš CategoryService, možeš ga ovdje pozvati da napuniš dropdown menije
        if (typeof TaskService !== 'undefined') {
            TaskService.loadUserTasks();
        }
    },

    login: function (entity) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/auth/login",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(entity),
            success: function (res) {
                // Čuvanje tokena
                localStorage.setItem("user_token", res.token || res.data.token);
                toastr.success("Login successful!");
                window.location.hash = "#account";
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON ? xhr.responseJSON.error : "Login failed");
            }
        });
    },

    register: function (entity) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/auth/register",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(entity),
            success: function (res) {
                toastr.success("Registration successful!");
                window.location.hash = "#login";
            },
            error: function (xhr) {
                toastr.error("Registration failed.");
            }
        });
    },

    logout: function () {
        localStorage.removeItem("user_token");
        window.location.hash = "#home";
        location.reload();
    }
};

// Inicijalizacija na document ready
$(document).ready(function () {
    UserService.init();
});