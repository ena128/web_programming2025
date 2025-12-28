var app = $.spapp({
    defaultView: "home",
    templateDir: "views/",
    pageNotFound: "error_404"
});


app.route({
    view: "home",
    load: "home.html",
    onCreate: function() {},
    onReady: function() {}
});


app.route({
    view: "login",
    load: "login.html",
    onCreate: function() {},
    onReady: function() {}
});


app.route({
    view: "register",
    load: "register.html",
    onCreate: function() {},
    onReady: function() {}
});


app.route({
    view: "account",
    load: "account.html",
    onCreate: function() {},
    onReady: function() {
        
        UserService.initAccountPage();
    }
});

// Pokretanje
$(document).ready(function() {
    app.run();
    
    
    if(typeof UserService !== 'undefined'){
        UserService.generateMenuItems();
    }
});