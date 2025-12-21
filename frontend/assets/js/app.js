$(document).ready(function () {
    var app = $.spapp({
        defaultView: "home",
        templateDir: "frontend/views/"
    });

    // Konfiguracija rute za account
    app.route({
        view: 'account',
        onSectionShow: function() {
            // Pozivamo logiku koja odlučuje da li prikazati Admin ili User panel
            UserService.initAccountView();
        }
    });

    // Inicijalizacija menija (Home, Login, Register ili Account, Logout)
    UserService.generateMenuItems();

    // Pokretanje SPApp-a
    app.run();
});