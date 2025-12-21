var app = $.spapp({
    defaultView: "home",
    templateDir: "frontend/views/",
    pageNotFound: "error_404"
});

// HOME je javna ruta (GUEST MODE DOZVOLJEN)
app.route({
    view: "home",
    load: "home.html",
    onCreate: function() {},
    onReady: function() {}
});

// LOGIN je javna ruta
app.route({
    view: "login",
    load: "login.html",
    onCreate: function() {},
    onReady: function() {}
});

// REGISTER je javna ruta
app.route({
    view: "register",
    load: "register.html",
    onCreate: function() {},
    onReady: function() {}
});

// ACCOUNT je ZAŠTIĆENA ruta (Samo logged in)
app.route({
    view: "account",
    load: "account.html",
    onCreate: function() {},
    onReady: function() {
        // Samo ovdje provjeravamo token i ulogu
        UserService.initAccountPage();
    }
});

// Pokretanje
$(document).ready(function() {
    app.run();
    
    // OVO JE KLJUČNO ZA GUEST MODE:
    // Generiši meni odmah pri učitavanju da se prikažu Login/Register dugmad
    if(typeof UserService !== 'undefined'){
        UserService.generateMenuItems();
    }
});