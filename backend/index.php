<?php

header('Access-Control-Allow-Origin: https://sea-lion-app-z5bja.ondigitalocean.app/'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}
// Autoload Composer dependencies
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
// ===== Roles =====
require_once __DIR__ . '/data/roles.php';

// ===== Register services with Flight =====
Flight::register('authService', 'AuthService');
Flight::register('userService', 'UserService');
Flight::register('taskService', 'TaskService');
Flight::register('categoryService', 'CategoryService');
Flight::register('priorityService', 'PriorityService');
Flight::register('activityLogsService', 'ActivityLogsService');


// ===== Services =====
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/TaskService.php';
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/PriorityService.php';
require_once __DIR__ . '/rest/services/ActivityLogsService.php';
require_once __DIR__ . '/rest/services/AuthService.php';


// ===== Middleware =====
require_once __DIR__ . '/middleware/AuthMiddleware.php';



// ===== DAOs (if needed directly) =====
require_once __DIR__ . '/rest/DAO/AuthDAO.php';
require_once __DIR__ . '/rest/DAO/UserDAO.php';
require_once __DIR__ . '/rest/DAO/TaskDAO.php';
require_once __DIR__ . '/rest/DAO/CategoryDAO.php';
require_once __DIR__ . '/rest/DAO/PriorityDAO.php';
require_once __DIR__ . '/rest/DAO/ActivityLogsDAO.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// ===== Register middleware with Flight =====
Flight::map('auth_middleware', function() {
    return new AuthMiddleware();
});

// ===== Before hook: check JWT on all routes except login/register =====
Flight::before('start', function(&$params, &$output) {
    $url = Flight::request()->url;

    // POPRAVKA: Koristimo strpos umjesto str_starts_with radi kompatibilnosti
    if (
        strpos($url, '/auth/login') === 0 ||
        strpos($url, '/auth/register') === 0
    ) {
        return TRUE;
    }

    try {
        $authHeader = Flight::request()->getHeader("Authorization");
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Flight::halt(401, json_encode(['error' => 'Missing or invalid Authorization header']));
        }

        $token = $matches[1];
        Flight::auth_middleware()->verifyToken($token);

    } catch (\Exception $e) {
        Flight::halt(401, json_encode(['error' => "Invalid or expired token: " . $e->getMessage()]));
    }
});

Flight::route('/', function() {
    echo 'Hello from Flight!';
});

require_once __DIR__ . '/rest/routes/AuthRoutes.php';
require_once __DIR__ . '/rest/routes/users.php';
require_once __DIR__ . '/rest/routes/tasks.php';
require_once __DIR__ . '/rest/routes/categories.php';
require_once __DIR__ . '/rest/routes/priorities.php';
require_once __DIR__ . '/rest/routes/activitylogs.php';

Flight::start();
?>