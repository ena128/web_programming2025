$(document).ready(function () {
    var app = $.spapp({
        defaultView: "home",
        templateDir: "frontend/views/"
    });
    UserService.generateMenuItems();
    app.run();
});