const UserService = {
    init: function () {
        const path = window.location.pathname;
        const token = localStorage.getItem("user_token");

        // Validator custom methods
        if ($.validator && !$.validator.methods.lettersandspacesonly) {
            $.validator.addMethod("lettersandspacesonly", function (value, element) {
                return this.optional(element) || /^[a-zA-ZčćžšđČĆŽŠĐ\s]+$/.test(value);
            }, "Please enter only letters and spaces.");
        }
        if ($.validator && !$.validator.methods.pwcheck) {
            $.validator.addMethod("pwcheck", function (value) {
                return /^(?=.*[A-Za-z])(?=.*\d).{6,}$/.test(value);
            }, "Password must be at least 6 characters and include at least one letter and one number.");
        }

        // LOGIN PAGE
        if (path.endsWith("login.html")) {
            if (token && token !== "undefined") {
                window.location.replace("index.html");
                return;
            }
            if ($("#login-form").length) {
                $("#login-form").validate({
                    rules: {
                        email: { required: true, email: true },
                        password: { required: true, minlength: 6 }
                    },
                    messages: {
                        email: { required: "Email is required.", email: "Please enter a valid email address." },
                        password: { required: "Password is required.", minlength: "Password must be at least 6 characters." }
                    },
                    submitHandler: function (form) {
                        var entity = {
                            email: $("#email").val(),
                            password: $("#password").val()
                        };
                         console.log("Login data:", entity);
        UserService.login(entity, form);
                       
                    }
                });
            }
        }
        // REGISTER PAGE
        else if (path.endsWith("register.html")) {
            if (token && token !== "undefined") {
                window.location.replace("index.html");
                return;
            }
            if ($("#register-form").length) {
                $("#register-form").validate({
                    rules: {
                        fullname: { required: true, minlength: 3, lettersandspacesonly: true },
                        email: { required: true, email: true },
                        username: { required: true, minlength: 3 },
                        password: { required: true, minlength: 6, pwcheck: true }
                    },
                    messages: {
                        fullname: { required: "Full name is required.", minlength: "Full name must be at least 3 characters.", lettersandspacesonly: "Full name must contain only letters and spaces." },
                        email: { required: "Email is required.", email: "Please enter a valid email address." },
                        username: { required: "Username is required.", minlength: "Username must be at least 3 characters." },
                        password: { required: "Password is required.", minlength: "Password must be at least 6 characters.", pwcheck: "Password must include at least one letter and one number." }
                    },
                    submitHandler: function (form) {
                        var entity = Object.fromEntries(new FormData(form).entries());
                        UserService.register(entity, form);
                    }
                });
            }
        }
        // INDEX OR OTHER PAGES
        else {
            if (typeof UserService.generateMenuItems === 'function') {
                UserService.generateMenuItems();
            }
        }
    },

    login: function (entity, formEl) {
        console.log("UserService.login called with:", entity);
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/auth/login",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(entity),
            success: function (res) {
                localStorage.setItem("user_token", res.data.token);
                toastr.success("Login successful!");
                if (formEl) formEl.reset();
                window.location.href = "./account.html";
            },
            error: function (xhr) {
                console.error("Login failed:", xhr.responseText || "Unknown error");
                let msg = "Login failed. Please check your credentials.";
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.error) msg = resp.error;
                } catch (e) { }
                toastr.error(msg);
            }
        });
    },

    register: function (entity, formEl) {
        console.log("UserService.register called with entity:", entity);
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/auth/register",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(entity),
            dataType: "json",
            success: function (res) {
                toastr.success("Registration successful! Please login.");
                if (formEl) formEl.reset();
                window.location.replace("login.html");
            },
            error: function (xhr) {
                console.error("Registration failed:", xhr.responseText || "Unknown error");
                let msg = "Registration failed. Please try again.";
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.error) msg = resp.error;
                } catch (e) { }
                toastr.error(msg);
            }
        });
    },
    getCurrentUser: function(callback) {
    const token = localStorage.getItem("user_token");
    if (!token) {
        callback(null); // nema tokena
        return;
    }

    $.ajax({
        url: Constants.PROJECT_BASE_URL + "/auth/me",
        method: "GET",
        headers: {
            "Authorization": "Bearer " + token
        },
        success: function(res) {
            callback(res.user); // vraćamo usera kroz callback
        },
        error: function(err) {
            console.error("Failed to load current user:", err);
            localStorage.removeItem("user_token"); // token invalid
            callback(null);
        }
    });
},


    logout: function () {
        localStorage.removeItem("user_token");
        toastr.info("You have been logged out.");
        window.location.replace("index.html");
    },

    generateMenuItems: function () {
        const token = localStorage.getItem("user_token");
        let nav = "";

        if (token && token !== "undefined") {
            let user = null;
            try {
                user = Utils.parseJwt(token).user;
                if (!user || !user.role) throw new Error("Invalid token");
            } catch (e) {
                console.error("Token parsing error:", e);
                localStorage.removeItem("user_token");
                nav = `<li class="nav-item"><a class="nav-link fs-5" href="index.html">Home</a></li>
                       <li class="nav-item"><a class="nav-link fs-5" href="login.html">Login</a></li>`;
                $("#menuTabs").html(nav);
                return;
            }

            nav += `<li class="nav-item"><a class="nav-link fs-5" href="index.html">Home</a></li>`;
            if (user.role === Constants.USER_ROLE) {
                nav += `<li class="nav-item"><a class="nav-link fs-5" href="account.html">Profile</a></li>`;
            } else if (user.role === Constants.ADMIN_ROLE) {
                nav += `<li class="nav-item"><a class="nav-link fs-5" href="account.html">Profile</a></li>
                        <li class="nav-item"><a class="nav-link fs-5" href="admin-dashboard.html">Dashboard</a></li>`;
            }
            nav += `<li class="nav-item"><a class="nav-link fs-5" href="#" id="logoutButton">Logout</a></li>`;
        } else {
            nav = `<li class="nav-item"><a class="nav-link fs-5" href="index.html">Home</a></li>
                   <li class="nav-item"><a class="nav-link fs-5" href="login.html">Login</a></li>`;
        }
        $("#menuTabs").html(nav);
    }
};

$(document).ready(function () {
    UserService.init();
    $(document).on("click", "#logoutButton", function (e) {
        e.preventDefault();
        UserService.logout();
    });
});
