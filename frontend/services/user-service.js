var UserService = {
    init: function () {
        // LOGIN LISTENER
        $(document).off("submit", "#login-form").on("submit", "#login-form", function (e) {
            e.preventDefault();
            const entity = {
                email: $("#email").val(),
                password: $("#password").val()
            };
            UserService.login(entity);
        });

        // REGISTER LISTENER
        $(document).off("submit", "#register-form").on("submit", "#register-form", function (e) {
            e.preventDefault();
            const entity = {
                name: $("#name").val(), 
                email: $("#email").val(),
                password: $("#password").val()
            };
            UserService.register(entity);
        });

        // LOGOUT LISTENER
        $(document).on("click", "#logoutBtn", function() {
            UserService.logout();
        });
    },

    parseJwt: function (token) {
        if (!token || token === "undefined") return null;
        try {
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            return JSON.parse(decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join('')));
        } catch (e) { return null; }
    },

    // OVO JE FALILO: Funkcija koja pravi meni zavisno od toga da li si Guest ili User
    generateMenuItems: function() {
        const token = localStorage.getItem("user_token");
        const ul = $("#navbarSupportedContent ul"); // Selektujemo listu u navbaru
        
        let html = `
            <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#about-us">About Us</a></li>
        `;

        if (token) {
            // Ako je ulogovan -> Prikazi Account i Logout
            html += `
                <li class="nav-item"><a class="nav-link" href="#account">My Account</a></li>
                <li class="nav-item"><button class="btn btn-link nav-link" id="logoutBtn">Logout</button></li>
            `;
        } else {
            // Ako NIJE ulogovan (Guest) -> Prikazi Login i Register
            html += `
                <li class="nav-item"><a class="nav-link" href="#login">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="#register">Register</a></li>
            `;
        }

        ul.html(html);
    },

    initAccountPage: function () {
        const token = localStorage.getItem("user_token");

        // Ako neko pokuša ući na Account a nije ulogovan -> Login
        if (!token) {
            window.location.hash = "#login";
            return;
        }

        const payload = UserService.parseJwt(token);

        if (!payload || !payload.user) {
            toastr.error("Invalid token");
            UserService.logout();
            return;
        }

        const user = payload.user;
        $("#username").text(user.name || user.email);

        // Role-Based Display
        if (user.role === 'ADMIN') {
            $("#adminPanel").show();
            $("#userPanel").show();
            if(typeof UserAdminService !== 'undefined') {
                UserAdminService.loadAllUsers();
            }
        } else {
            $("#adminPanel").hide();
            $("#userPanel").show();
        }

        if(typeof TaskService !== 'undefined') {
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
                const token = res.token || (res.data && res.data.token);
                if (token) {
                    localStorage.setItem("user_token", token);
                    toastr.success("Login successful!");
                    window.location.hash = "#account";
                    UserService.generateMenuItems(); // Osvježi meni nakon logina
                } else {
                    toastr.error("Token error");
                }
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
                toastr.success("Success! Redirecting to login...");
                setTimeout(function() {
                    window.location.hash = "#login";
                }, 1000);
            },
            error: function (xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.error : "Error";
                toastr.error(msg);
            }
        });
    },

    logout: function () {
        localStorage.removeItem("user_token");
        window.location.hash = "#login";
        UserService.generateMenuItems(); // Osvježi meni nakon logouta
    }
};

$(document).ready(function () {
    UserService.init();
});